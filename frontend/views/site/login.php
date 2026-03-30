<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use common\widgets\Alert;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h3 class="text-center mb-3">
                        <?= Html::encode($this->title) ?>
                    </h3>

                    <p class="text-center text-muted mb-4">
                        Login to your account
                    </p>

                    <?= Alert::widget() ?>

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Username or Email'
                    ]) ?>

                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Password'
                    ]) ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'class' => 'form-check-input'
                    ]) ?>

                    <div class="small text-muted mb-3">
                        Forgot password?
                        <?= Html::a('Reset it', ['site/request-password-reset']) ?>
                        <br>
                        Need new verification email?
                        <?= Html::a('Resend', ['site/resend-verification-email']) ?>
                    </div>

                    <div class="d-grid">
                        <?= Html::submitButton(
                            'Login',
                            ['class' => 'btn btn-primary', 'name' => 'login-button']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>
</div>