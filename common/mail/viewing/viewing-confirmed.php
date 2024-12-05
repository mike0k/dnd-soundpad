<table class="row viewing">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <h1 class="text-center">Viewing Confirmation</h1>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <h2 class="text-center">
                            <?php echo date('h:i a', $diary->startTime); ?>
                            <br />
                            <?php echo date('l', $diary->startTime); ?>
                        </h2>
                        <h4 class="text-center"><?php echo date('jS F Y', $diary->startTime); ?></h4>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>

<table class="row viewing">
    <tbody>
    <tr>
        <th class="small-12 large-6 columns first agent">
            <table>
                <tr>
                    <th>
                        <h3>Guide</h3>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <img class="avatar" src="<?= $agent->avatarUrl ?>" alt="avatar" />
                        <p><?php echo $agent->name; ?></p>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
        <th class="small-12 large-6 columns last buyer">
            <table>
                <tr>
                    <th>
                        <h3>Buyer</h3>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <img class="avatar" src="<?= $buyer->avatarUrl ?>" alt="avatar" />
                        <p><?php echo $buyer->name; ?></p>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>

<table class="row viewing">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <h3><?php echo $property->addressFull; ?></h3>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <a href="http://www.google.com/maps/place/<?= $property->lat.','.$property->lng ?>"><img class="map" src="<?= $property->map->url ?>" alt="Map" /></a>
                    </th>
                    <th class="expander"></th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>

