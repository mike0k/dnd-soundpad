<table class="row">
    <tbody>
    <tr>
        <th class="small-12 large-12 columns first last">
            <table>
                <tr>
                    <th>
                        <h1 class="text-center">
                            Password Reset OTP
                        </h1>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?><h1 class="text-center">Account Verification</h1>
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>

                        <?php echo Yii::$app->mailer->render('common/_dear', ['user' => $user]); ?>
                        <p>To continue with creating your account, please verify your email address by entering this one time password into the signup form on our websites.</p>


                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <?php echo Yii::$app->mailer->render('common/_pin', ['pin' => $hash->pin]); ?>
                        <?php //echo Yii::$app->mailer->render('common/_button', ['url' => $hash->url, 'label' => 'Verify Your Email Address']); ?>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>