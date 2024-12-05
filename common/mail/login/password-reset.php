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
                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>

                        <?php echo Yii::$app->mailer->render('common/_dear', ['user' => $user]); ?>
                        <p>To continue with resetting your password, please enter this one time password into the password reset form on our websites.</p>

                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <?php echo Yii::$app->mailer->render('common/_pin', ['pin' => $hash->pin]); ?>

                        <?php echo Yii::$app->mailer->render('common/_spacer'); ?>
                        <p>If you did not request this email then please review the strength of your password as someone may have attempted to gain access to your account. For further information please see the knowledge base on our webiste.</p>


                        <?php //echo Yii::$app->mailer->render('common/_button', ['url' => $hash->url, 'label' => 'Reset Password']); ?>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>