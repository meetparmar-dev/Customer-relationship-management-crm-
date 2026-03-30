<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use common\widgets\Alert;

$this->title = 'Login';
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

                    <?= $form->field($model, 'rememberMe')->checkbox() ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton(
                            'Login',
                            ['class' => 'btn btn-primary', 'name' => 'login-button']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <!-- Signup & Forgot Password -->
                    <div class="text-center mt-4">
                        <p class="mb-1">
                            <?= Html::a(
                                'Forgot Password?',
                                ['site/request-password-reset'],
                                ['class' => 'text-decoration-none']
                            ) ?>
                        </p>

                        <p class="mb-0">
                            Don’t have an account?
                            <?= Html::a(
                                'Sign Up',
                                ['site/signup'],
                                ['class' => 'fw-semibold text-decoration-none']
                            ) ?>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>