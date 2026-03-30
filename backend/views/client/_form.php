<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Client;

/** @var Client $model */
?>

<div class="card shadow-sm">
    <div class="card-body">

        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'text-danger small'],
            ],
        ]); ?>

        <div class="row">

            <!-- CLIENT CODE -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'client_code')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'CLT001'
                ]) ?>
            </div>

            <!-- CLIENT TYPE -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'type')->dropDownList(
                    Client::typeList(),
                    ['prompt' => 'Select Client Type']
                ) ?>

            </div>

            <!-- STATUS -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    Client::statusList(),
                    ['prompt' => 'Select Status']
                ) ?>
            </div>

            <!-- COMPANY NAME (ONLY COMPANY) -->
            <div class="col-md-12 mb-3 field-company">
                <?= $form->field($model, 'company_name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Company Name'
                ]) ?>
            </div>

            <!-- FIRST NAME -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'first_name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'First Name'
                ]) ?>
            </div>

            <!-- LAST NAME -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'last_name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Last Name'
                ]) ?>
            </div>

            <!-- EMAIL -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'email')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Email Address'
                ]) ?>
            </div>

            <!-- PHONE -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'phone')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Phone Number',
                    'type' => 'tel'
                ]) ?>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-light']) ?>
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create Client' : 'Update Client',
                ['class' => 'btn btn-success']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>