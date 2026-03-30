<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\ClientAddress $model */
?>

<div class="client-address-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'text-danger small'],
        ],
    ]); ?>

    <!-- client_id hidden (view se aayega) -->
    <?= $form->field($model, 'client_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'address_type')->dropDownList([
        'billing' => 'Billing',
        'shipping' => 'Shipping',
        'office' => 'Office',
    ])
    ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'city')->textInput() ?>
    <?= $form->field($model, 'state')->textInput() ?>
    <?= $form->field($model, 'pincode')->textInput() ?>

    <div class="mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Save Address' : 'Update Address',
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>