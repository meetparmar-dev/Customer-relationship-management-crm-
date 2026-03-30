<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
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

            <!-- FIRST NAME -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'first_name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Enter first name'
                ]) ?>
            </div>

            <!-- LAST NAME -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'last_name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Enter last name'
                ]) ?>
            </div>

            <!-- USERNAME -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'username')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Enter username'
                ]) ?>
            </div>

            <!-- EMAIL -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'email')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Enter email'
                ]) ?>
            </div>

            <!-- PASSWORD -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'password', [
                    'template' => '<div class="input-group">{input}<button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(\'user-password\')"><i class="bi bi-eye"></i></button></div>{error}'
                ])->passwordInput([
                    'placeholder' => 'Enter password',
                    'id' => 'user-password'
                ]) ?>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'confirm_password', [
                    'template' => '<div class="input-group">{input}<button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(\'user-confirm_password\')"><i class="bi bi-eye"></i></button></div>{error}'
                ])->passwordInput([
                    'placeholder' => 'Confirm password',
                    'id' => 'user-confirm_password'
                ]) ?>
            </div>

            <!-- ROLE -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'role')->dropDownList([
                    User::ROLE_ADMIN => 'Admin',
                    User::ROLE_USER  => 'User',
                ], ['prompt' => 'Select Role']) ?>
            </div>

            <!-- STATUS -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'status')->dropDownList([
                    User::STATUS_ACTIVE   => 'Active',
                    User::STATUS_INACTIVE => 'Inactive',
                ], ['prompt' => 'Select Status']) ?>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-light']) ?>
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create User' : 'Update User',
                ['class' => 'btn btn-success']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.parentNode.querySelector('button');
        const icon = button.querySelector('.bi');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>