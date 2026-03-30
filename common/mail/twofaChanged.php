<?php

/** @var common\models\User $user */
/** @var string $status */
?>

<h2>Two-Factor Authentication <?= ucfirst($status) ?></h2>

<p>Hello <?= htmlspecialchars($user->username) ?>,</p>

<p>
    Two-Factor Authentication has been <b><?= $status ?></b> on your account.
</p>

<p>
    If this was not you, please secure your account immediately.
</p>

<br>
<p>— <?= Yii::$app->name ?> Security Team</p>