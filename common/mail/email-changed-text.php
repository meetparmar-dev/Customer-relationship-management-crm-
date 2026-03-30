Hello <?= $user->first_name ?>,

Your email address was changed.

Old Email: <?= $oldEmail ?>
New Email: <?= $newEmail ?>

Date: <?= date('Y-m-d H:i:s') ?>
IP: <?= $ip ?? 'Unknown' ?>

If you did not make this change, contact support immediately.

<?= Yii::$app->name ?>