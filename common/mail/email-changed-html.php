<?php

/** @var \common\models\User $user */
/** @var string $oldEmail */
/** @var string $newEmail */
/** @var string $ip */
?>

<h2>Email Changed Notification</h2>

<p>Hello <?= htmlspecialchars($user->first_name) ?>,</p>

<p>This is to notify you that your email address was recently changed.</p>

<p>
    <strong>Old Email:</strong> <?= htmlspecialchars($oldEmail) ?><br>
    <strong>New Email:</strong> <?= htmlspecialchars($newEmail) ?>
</p>

<p>
    <strong>Date:</strong> <?= date('F j, Y \a\t g:i A') ?><br>
    <strong>IP Address:</strong> <?= htmlspecialchars($ip ?? 'Unknown') ?>
</p>

<p>
    If you did not make this change, please contact our support team immediately as your account may have been compromised.
</p>

<br>

<p>
    Regards,<br>
    <?= Yii::$app->name ?> Team
</p>