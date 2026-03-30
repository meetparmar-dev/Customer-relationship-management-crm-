<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\ClientContact $model */
?>

<div class="client-contact-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'text-danger small'],
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput([
        'maxlength' => true,
        'placeholder' => 'Contact Name'
    ]) ?>

    <?= $form->field($model, 'designation')->textInput([
        'maxlength' => true,
        'placeholder' => 'Designation (Manager, HR, Owner...)'
    ]) ?>

    <?= $form->field($model, 'email')->textInput([
        'maxlength' => true,
        'placeholder' => 'Email'
    ]) ?>

    <?= $form->field($model, 'phone')->textInput([
        'maxlength' => true,
        'placeholder' => 'Phone'
    ]) ?>

    <?= $form->field($model, 'is_primary')->checkbox() ?>

    <div class="mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Save Contact' : 'Update Contact',
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>