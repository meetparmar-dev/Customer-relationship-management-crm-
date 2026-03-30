<?php

/** @var $user common\models\User */
?>
<h2>Security Verification</h2>

<p>Hello <?= htmlspecialchars($user->username) ?>,</p>

<p>Your One-Time Password (OTP) is:</p>

<h1 style="letter-spacing:4px;"><?= $user->twofa_secret ?></h1>

<p>This code will expire in 5 minutes.</p>

<p>If you didn’t request this, please ignore this email.</p>

<hr>
<small>CRM System</small>