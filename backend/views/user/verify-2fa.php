<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use common\models\TwofaForm;

$this->title = 'Verify 2FA Code';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height:100vh;">
        <div class="col-md-5 col-lg-4">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="text-center mb-3">Two-Factor Verification</h4>
                    <p class="text-center text-muted">
                        We have sent a 6-digit OTP to your email. Enter it below to continue.
                    </p>

                    <?php $form = ActiveForm::begin([
                        'action' => ['verify-2fa'],
                        'method' => 'post',
                    ]); ?>

                    <?= $form->field($model, 'twofa_verification_code')->textInput([
                        'placeholder' => 'Enter 6-digit code',
                        'maxlength' => 6,
                        'class' => 'form-control text-center fs-5 letter-spacing'
                    ])->label(false) ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Verify', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <div class="text-center mt-3">
                        <?= Html::a('Cancel & go back', ['profile', 'tab' => 'security'], ['class' => 'text-decoration-none']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>
</div>