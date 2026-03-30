<?php

/** @var \common\models\User $user */
?>

<h2>Welcome <?= htmlspecialchars($user->first_name) ?> 🎉</h2>

<p>Your email has been successfully verified.</p>

<p>
    You can now login and start using
    <strong><?= Yii::$app->name ?></strong>.
</p>

<p>We’re happy to have you with us!</p>

<br>

<p>
    Regards,<br>
    <?= Yii::$app->name ?> Team
</p>