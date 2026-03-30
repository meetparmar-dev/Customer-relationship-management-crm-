<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container mt-5" style="max-width:400px">
    <h4 class="mb-3 text-center">Two Factor Authentication</h4>
    <p class="text-muted text-center">
        OTP sent to your email
    </p>

    <?php $form = ActiveForm::begin(['action' => ['site/verify-2fa-submit']]); ?>

    <?= Html::input('text', 'otp', '', [
        'class' => 'form-control',
        'placeholder' => 'Enter 6 digit OTP',
        'required' => true
    ]) ?>

    <button class="btn btn-primary w-100 mt-3">
        Verify & Login
    </button>

    <?php ActiveForm::end(); ?>
</div>