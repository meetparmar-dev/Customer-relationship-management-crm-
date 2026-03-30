<p>Hello <?= $user->getFullName() ?>,</p>

<p>Your account role has been updated.</p>

<p>Current Role: <b><?= $user->role == 1 ? 'Admin' : 'User' ?></b></p>

<p>– <?= Yii::$app->name ?></p>