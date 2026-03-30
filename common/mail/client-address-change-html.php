<?php

/** @var $address common\models\ClientAddress */
/** @var $action string */
/** @var $user common\models\User */
?>

<h2>Client Address Update</h2>

<p><strong>Action:</strong> <?= str_replace('_', ' ', $action) ?></p>

<p><strong>Changed By:</strong>
    <?= $user ? $user->username . ' (' . $user->email . ')' : 'System' ?>
</p>

<hr>

<p><strong>Client ID:</strong> <?= $address->client_id ?></p>
<p><strong>Address Type:</strong> <?= $address->address_type ?></p>
<p><strong>Address:</strong></p>

<pre><?= htmlspecialchars($address->address_line_1 ?? '') ?></pre>

<p><small>Time: <?= date('d M Y H:i:s') ?></small></p>