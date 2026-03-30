<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ClientSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_code') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'company_name') ?>

    <?= $form->field($model, 'first_name') ?>

    <?php // echo $form->field($model, 'last_name') 
    ?>

    <?php // echo $form->field($model, 'email') 
    ?>

    <?php // echo $form->field($model, 'phone') 
    ?>

    <?php // echo $form->field($model, 'status') 
    ?>

    <?php // echo $form->field($model, 'created_at') 
    ?>

    <?php // echo $form->field($model, 'updated_at') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>