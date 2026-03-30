<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use common\widgets\Alert;

$this->title = 'Two-Factor Authentication';
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
                        Enter the 6-digit OTP sent to your email
                    </p>

                    <?= Alert::widget() ?>

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'otp')->textInput([
                        'maxlength' => 6,
                        'placeholder' => 'Enter OTP',
                        'class' => 'form-control text-center fs-5 letter-spacing',
                        'autofocus' => true
                    ]) ?>

                    <div class="d-flex justify-content-between mb-3">
                        <small class="text-muted">Didn’t receive the OTP?</small>
                        <a href="<?= Url::to(['site/resend-otp']) ?>" class="fw-semibold text-decoration-none">
                            Resend OTP
                        </a>
                    </div>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton(
                            'Verify',
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>
</div>