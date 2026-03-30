<?php

use yii\helpers\Url;

$link = Url::to([
    '/site/verify-new-email',
    'token' => $user->verification_token
], true);
?>

Hello <?= $user->username ?>,

You requested to change your email.

Click the link below to confirm your new email address:

<?= $link ?>

If you didn’t request this, you can ignore this email.