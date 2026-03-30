<?php

/** @var common\models\User $user */
?>

<h2>Password Changed</h2>

<p>Hello <?= htmlspecialchars($user->username) ?>,</p>

<p>
    Your account password was successfully changed.
</p>

<p>
    If this was not you, please contact support immediately or reset your password.
</p>

<br>
<p>
    — <?= Yii::$app->name ?> Security Team
</p>