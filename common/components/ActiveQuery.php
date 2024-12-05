<?php

namespace common\components;

use Yii;
use common\models\DbVar;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the extended ActiveQuery class.
 *
 * @property ActiveRecord $model
 */
class ActiveQuery extends \yii\db\ActiveQuery {

    public $search;
    public $searchSort;
    protected $model;

    protected $searchTerms;

    public function init () {
        parent::init();
        if (empty($this->model)) {
            $this->model($this->modelClass);
        }
    }

    public function getSql () {
        return $this->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;
    }

    ///////////////////////////////////////
    // SCOPES
    ///////////////////////////////////////

    public function active () {
        if (!empty($this->model) && $this->model->getClassName() == 'DbProperty') {
            return $this->andWhere(['status' => ['publish', 'offer']]);

        } else if (!empty($this->model) && $this->model->getClassName() == 'DbDiary') {
            return $this->andWhere(['status' => ['pending', 'confirmed']])
                ->andWhere([['>=', 'endTime', time()]]);

        } else {
            return $this->andWhere(['status' => 'active']);
        }
    }

    public function inactive () {
        if (!empty($this->model) && $this->model->getClassName() == 'DbProperty') {
            return $this->andWhere(['status' => ['draft', 'withdraw', 'sold']]);

        } else if (!empty($this->model) && $this->model->getClassName() == 'DbDiary') {
            return $this->andWhere(['status' => 'cancelled'])
                ->orWhere([['<', 'endTime', time()]]);

        } else {
            return $this->andWhere(['status' => 'inactive']);
        }
    }

    public function property ($refId, $relation = '') {
        return $this->relation('DbProperty', 'propId', $refId, $relation);
    }

    public function ref ($refId, $refType = null) {
        $refType = (!empty($refType) ? $refType : $this->model->className);

        return $this->andWhere($this->sqlAttrs(['refId' => $refId, 'refType' => $refType]));
    }

    private function relation ($class, $attr, $refId, $relation = '') {
        $relation = (!empty($relation) ? $relation . '.' : $relation);
        if (!empty($refId)) {
            if ($this->model->hasAttribute($attr)) {
                return $this->andWhere($this->sqlAttrs([$relation . $attr => $refId]));
            } else {
                return $this->andWhere($this->sqlAttrs([$relation . 'refId' => $refId, $relation . 'refType' => $class]));
            }
        }

        return $this;
    }

    public function user ($refId, $relation = '') {
        return $this->relation('DbUser', 'userId', $refId, $relation);
    }

    ///////////////////////////////////////
    // WHERE
    ///////////////////////////////////////

    public function whereTime ($attr, $time, $operator = 'AND') {
        $time = (empty($time) ? time() : $time);
        $attr = $this->sqlAttr($attr);
        $query = new ActiveQuery(new DbVar());
        if (is_array($time)) {
            $query->andWhere(['>=', $attr, $time[0]]);
            $query->andWhere(['<=', $attr, $time[1]]);
        } else {
            $query->andWhere(['>=', $attr, $time]);
            $query->andWhere(['<=', $attr, $time]);
        }
        $this->merge($query, $operator);

        return $this;
    }

    public function whereTimeRange ($startTime, $endTime = null, $operator = 'AND') {
        $endTime = (empty($endTime) ? time() : $endTime);

        $query = new ActiveQuery(new DbVar());
        $query->andWhere(['>', 'startTime', 0]);
        $query->andWhere(['>', 'endTime', 0]);

        $query1 = new ActiveQuery(new DbVar());
        $query1->andWhere(['>=', 'startTime', $startTime]);
        $query1->andWhere(['<=', 'startTime', $endTime]);

        $query2 = new ActiveQuery(new DbVar());
        $query2->andWhere(['>=', 'endTime', $startTime]);
        $query2->andWhere(['<=', 'endTime', $endTime]);

        $query3 = new ActiveQuery(new DbVar());
        $query3->andWhere(['<=', 'startTime', $startTime]);
        $query3->andWhere(['>=', 'endTime', $endTime]);

        $query2->merge($query3, 'OR');
        $query1->merge($query2, 'OR');
        $query->merge($query1, 'AND');
        $this->merge($query, $operator);

        return $this;
    }

    ///////////////////////////////////////
    // SEARCH
    ///////////////////////////////////////

    /*
     * @param Array $attrs - 1 dimension array of models attributes
     * @return ActiveQuery
     */
    public function addSearchTerms ($attrs, $operator = 'AND') {
        //format search terms
        if (empty($this->searchTerms) && !empty($this->search)) {
            $this->searchTerms = $this->explodeTerms($this->search);
        }

        //create the query, find if an attr contains all of the terms
        if (!empty($this->searchTerms)) {
            $query = new ActiveQuery($this->modelClass);
            if (!empty($this->searchTerms) && !empty($attrs)) {
                $query2 = new ActiveQuery($this->modelClass);
                foreach ($attrs as $attr) {
                    $query3 = new ActiveQuery($this->modelClass);
                    if (is_array($attr)) {
                        foreach ($this->searchTerms as $term) {
                            $query4 = new ActiveQuery($this->modelClass);
                            foreach ($attr as $subAttr) {
                                $query3->orWhere(['like', $this->sqlAttr($subAttr), $term]);
                            }
                            $query3->merge($query4, 'AND');
                        }
                    } else {
                        foreach ($this->searchTerms as $term) {
                            $query3->andWhere(['like', $this->sqlAttr($attr), $term]);
                        }
                    }
                    $query2->merge($query3, 'OR');
                }
                $query->merge($query2);
            }
            $this->merge($query, $operator);
        }

        //order the search
        if (!empty($this->searchSort)) {
            $sort = $this->model->getListLabel('listSearchSort', $this->searchSort);
            $this->orderBy($this->sqlAttr($sort));
        }

        return $this;
    }

    public function filters ($type) {
        $html = '';
        if ($this->model->hasAttribute($type)) {
            $html = Html::textInput($this->model->getClassName() . '[' . $type . ']', $this->$type, array('class' => 'form-control'));
        }

        return $html;
    }

    /**
     * Return db results in different formats. typically used with $model->search()
     * @param ActiveQuery $query
     * @param array       $options
     * @return mixed
     */
    public function search ($options = null) {
        $default = array(
            'type' => 'dp', //dataProvider, criteria, array, single, criteria
            'pageSize' => 20,
            'searchAttr' => $this->model->getSearchAttrs(),
            'order' => $this->model->getSearchOrder(),
            'debug' => false,
            'debugDie' => false,
        );
        $options = ArrayHelper::merge($default, $options);

        if (!empty($options['order'])) {
            if (is_array($options['order'])) {
                $orders = array();
                foreach ($options['order'] as $order) {
                    $parts = explode(' ', $order);
                    if (!empty($parts)) {
                        $orders[] = str_replace($parts[0], $this->sqlAttr($parts[0]), $order);
                    }
                }
                $options['order'] = implode(', ', $orders);
            }

            $this->orderBy($options['order']);
        }
        if (!empty($options['limit'])) {
            $this->limit($options['limit']);
        }
        if (!empty($options['query'])) {
            $this->andWhere($options['query']->where);
            $this->addParams($options['query']->params);
        }
        if (!empty($options['searchAttr'])) {
            $this->addSearchTerms($options['searchAttr']);
        }
        if (!empty($this->model->attributes)) {
            $this->andFilterWhere($this->sqlAttrs($this->model->attributes));
        }

        if ($options['pageSize'] == 'all') {
            $options['pageSize'] = $this->count();
            if ($options['pageSize'] < 20) {
                $options['pageSize'] = 50;
            }
        }

        //var_dump($this);exit;

        switch ($options['type']) {
            case 'all':
                $return = $this->all();
                break;
            case 'count':
                $return = $this->count();
                break;
            case 'query':
                $return = $this;
                break;
            case 'one':
                $return = $this->one();
                break;
            case 'dp':
            default:
                $return = new ActiveDataProvider([
                    'query' => $this,
                    'pagination' => [
                        'pageSize' => $options['pageSize'],
                    ],
                ]);
                break;
        }

        if ($options['debug'] || $options['debugDie']) {
            echo '<pre>' . var_dump($this) . '</pre>';
            echo '<pre>' . var_dump($return) . '</pre>';
        }
        if ($options['debugDie']) {
            exit;
        }

        return $return;
    }

    ///////////////////////////////////////
    // UTILITIES
    ///////////////////////////////////////

    public function debug ($die = true) {
        print_r($this->createCommand()->rawSql);
        if ($die) {
            exit;
        }

        return $this;
    }

    //explode public search terms. use different function if exploding only by specific character e.g. commas
    public function explodeTerms ($input) {
        $terms = array();
        if (!empty($input)) {
            //remove symbols
            $input = str_replace(array(
                ',',
                '.',
                '&'
            ), ' ', $input);

            //remove double spacing
            $input = preg_replace('!\s+!', ' ', $input);

            $parts = explode(' ', $input);
            if (!empty($parts)) {
                $terms = $parts;
            }
        }

        return $terms;
    }

    public function merge ($query, $operator = 'AND') {
        if (!empty($query->where)) {
            if (strtoupper($operator) == 'AND') {
                $this->andWhere($query->where);
            } else {
                $this->orWhere($query->where);
            }
        }

        if (!empty($query->joinWith)) {
            $this->joinWith($query->joinWith);
        }

        if (!empty($query->params)) {
            $this->addParams($query->params);
        }

        return $this;
    }

    public function model ($model) {
        if (!is_object($this->model)) {
            $this->model = new $model;
            $this->modelClass = $model;
        } else {
            $this->model = $model;
            $this->modelClass = get_class($model);
        }

        $this->search = $this->model->search;
        $this->searchSort = $this->model->searchSort;

        return $this;
    }

    public function sqlAttr ($attr, $joinRelation = true) {

        if (strpos($attr, '.')) {
            $relations = explode('.', $attr);
            if (!empty($relations)) {
                $method = 'get' . ucfirst($relations[0]);
                $query = $this->model->$method();
                $attr = $query->model->tableName() . '.' . $relations[1];
                if ($joinRelation) {
                    $this->joinWith($relations[0]);
                }
            }
        } else {
            $attr = $this->model->tableName() . '.' . $attr;
        }

        return $attr;
    }

    public function sqlAttrs ($attrs) {
        $return = array();
        if (!empty($attrs)) {
            foreach ($attrs as $key => $val) {
                if (is_numeric($key)) {
                    $return[] = $this->sqlAttr($val);
                } else {
                    $return[$this->sqlAttr($key)] = $val;
                }
            }
        }

        return $return;
    }
}
