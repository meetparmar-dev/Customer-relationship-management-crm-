<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use common\widgets\Alert;
use backend\assets\AppAsset;

AppAsset::register($this);

$user = Yii::$app->user->identity;
$this->title = 'My Profile';
$tab = Yii::$app->request->get('tab', 'profile');
?>

<?= Alert::widget() ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- PAGE TITLE -->
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Account /</span> My Profile
    </h4>

    <!-- ================= TABS ================= -->
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'profile' ? 'active' : '' ?>"
                href="<?= Url::to(['user/profile', 'tab' => 'profile']) ?>">
                <i class="bi bi-person"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'security' ? 'active' : '' ?>"
                href="<?= Url::to(['user/profile', 'tab' => 'security']) ?>">
                <i class="bi bi-shield-lock"></i> Security
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'reset' ? 'active' : '' ?>"
                href="<?= Url::to(['user/profile', 'tab' => 'reset']) ?>">
                <i class="bi bi-key"></i> Reset Password
            </a>
        </li>
    </ul>

    <div class="row">

        <!-- ================= LEFT COLUMN ================= -->
        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header">Profile Picture</h5>

                <div class="card-body text-center">

                    <img src="<?= Yii::$app->avatar->get($user) ?>"
                        class="rounded-circle shadow-sm mb-3"
                        width="140" height="140"
                        style="object-fit:cover;">

                    <small class="text-muted d-block mb-3">
                        Allowed: JPG, PNG, WEBP (Max 2MB)
                    </small>

                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/user/upload-avatar']),
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>

                    <?= $form->field($user, 'avatarFile')
                        ->fileInput(['class' => 'form-control'])
                        ->label(false) ?>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <?= Html::submitButton(
                            '<i class="bi bi-cloud-arrow-up"></i> Upload',
                            ['class' => 'btn btn-primary']
                        ) ?>

                        <?php if (!empty($user->avatar)): ?>
                            <?= Html::a(
                                '<i class="bi bi-trash"></i> Delete',
                                ['/user/delete-avatar'],
                                [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete profile picture?',
                                        'method' => 'post',
                                    ],
                                ]
                            ) ?>
                        <?php endif; ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN ================= -->
        <div class="col-md-8">

            <?php if ($tab === 'profile'): ?>

                <!-- PROFILE DETAILS -->
                <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <div class="card-body">

                        <?php $form = ActiveForm::begin(); ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'first_name') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'last_name') ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'username') ?>
                        <?= $form->field($model, 'email') ?>

                        <?php if (!empty($user->pending_email)): ?>
                            <div class="alert alert-warning p-2 mt-2">
                                Email verification pending for
                                <strong><?= Html::encode($user->pending_email) ?></strong><br>
                                Please check your inbox.
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <?= Html::submitButton(
                                '<i class="bi bi-check-circle me-1"></i> Save Changes',
                                ['class' => 'btn btn-primary']
                            ) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

            <?php elseif ($tab === 'security'): ?>

                <!-- 2FA -->
                <div class="card mb-4">
                    <h5 class="card-header">Two Factor Authentication</h5>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">
                            <?= $user->twofa_enabled ? 'Enabled' : 'Disabled' ?>
                        </span>

                        <a href="<?= $user->twofa_enabled
                                        ? Url::to(['/user/disable-2fa'])
                                        : Url::to(['/user/enable-2fa']) ?>"
                            class="btn <?= $user->twofa_enabled ? 'btn-danger' : 'btn-success' ?>">
                            <?= $user->twofa_enabled ? 'Disable' : 'Enable' ?>
                        </a>
                    </div>
                </div>

            <?php elseif ($tab === 'reset'): ?>

                <!-- CHANGE PASSWORD -->
                <div class="card mb-4 shadow-sm">
                    <h5 class="card-header">
                        <i class="bi bi-key me-2"></i> Change Password
                    </h5>

                    <div class="card-body">

                        <?php $passwordForm = ActiveForm::begin([
                            'action' => Url::to(['/user/change-password']),
                        ]); ?>

                        <?= $passwordForm->field($model, 'current_password')->passwordInput() ?>
                        <?= $passwordForm->field($model, 'new_password')->passwordInput() ?>
                        <?= $passwordForm->field($model, 'confirm_password')->passwordInput() ?>

                        <?= Html::submitButton(
                            'Update Password',
                            ['class' => 'btn btn-warning']
                        ) ?>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- RESET PASSWORD EMAIL -->
                <div class="card">
                    <h5 class="card-header">Reset Password</h5>
                    <div class="card-body">
                        <p>We will send a reset password link to your email.</p>
                        <a href="<?= Url::to(['/site/request-password-reset']) ?>"
                            class="btn btn-danger">
                            Send Reset Password Email
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>