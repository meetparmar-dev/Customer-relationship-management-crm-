<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Create Account';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-signup container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <!-- Title -->
                    <h3 class="text-center mb-3 fw-bold">
                        <?= Html::encode($this->title) ?>
                    </h3>

                    <p class="text-center text-muted mb-4">
                        Fill in the details below to create your account
                    </p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'form-signup',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                        ],
                    ]); ?>

                    <!-- First Name -->
                    <?= $form->field($model, 'first_name')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Enter first name'
                    ]) ?>

                    <!-- Last Name -->
                    <?= $form->field($model, 'last_name')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Enter last name'
                    ]) ?>

                    <!-- Username -->
                    <?= $form->field($model, 'username')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Choose a username'
                    ]) ?>

                    <!-- Email -->
                    <?= $form->field($model, 'email')->input('email', [
                        'class' => 'form-control',
                        'placeholder' => 'Enter your email'
                    ]) ?>

                    <!-- Password -->
                    <?= $form->field($model, 'password')->passwordInput([
                        'class' => 'form-control',
                        'placeholder' => 'Create password'
                    ]) ?>

                    <!-- Confirm Password -->
                    <?= $form->field($model, 'confirm_password')->passwordInput([
                        'class' => 'form-control',
                        'placeholder' => 'Confirm password'
                    ]) ?>

                    <!-- Submit -->
                    <div class="d-grid mt-4">
                        <?= Html::submitButton(
                            'Create Account',
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-3">
                <span class="text-muted">Already have an account?</span>
                <?= Html::a('Login', ['/site/login'], ['class' => 'fw-semibold']) ?>
            </div>

        </div>
    </div>
</div>