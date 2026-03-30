<p>Hello <?= $user->getFullName() ?>,</p>

<p>Your account status has been changed by an administrator.</p>

<p>Status: <b><?= $user->status == 10 ? 'Active' : 'Inactive / Blocked' ?></b></p>

<p>If this was unexpected, contact support.</p>

<p>– <?= Yii::$app->name ?></p>