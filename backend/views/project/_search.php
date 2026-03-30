<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProjectSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'project_code') ?>

    <?= $form->field($model, 'project_name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'client_id') ?>

    <?php // echo $form->field($model, 'project_manager_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'end_date') ?>

    <?php // echo $form->field($model, 'completed_at') ?>

    <?php // echo $form->field($model, 'budget') ?>

    <?php // echo $form->field($model, 'billing_type') ?>

    <?php // echo $form->field($model, 'estimated_hours') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
