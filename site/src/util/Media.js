import { store } from '../store/Store';
import * as MediaAction from '../store/action/MediaAction';
import { randomId } from './Tool';

//const clear = () => store.dispatch(MediaAction.clear());
export const get = () => store.getState().media;
export const set = (data) => store.dispatch(MediaAction.set(data));
const update = (data) => store.dispatch(MediaAction.merge(data));

export const add = (data) => {
    const id = randomId();
    let item = {
        name: '',
        type: '',
        url: '',
        ...data,
        id,
    };

    if (item.name !== '' && item.type !== '' && item.url !== '') {
        update({ items: { [id]: item } });
        return item;
    }

    return false;
};

export const getItem = (id) => {
    const items = get().items;
    if (typeof items[id] !== 'undefined') {
        return items[id];
    }
    return false;
};

export const remove = (id) => {
    let items = get().items;
    if (typeof items[id] !== 'undefined') {
        delete items[id];
        set({ items });
    }
};

export const setItem = (id, data = {}) => {
    return update({ items: { [id]: { ...data } } });
};
