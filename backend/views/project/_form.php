<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Client;
use common\models\User;

/** @var yii\web\View $this */
/** @var common\models\Project $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="project-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'text-danger small'],
        ],
    ]); ?>

    <!-- Project Name -->
    <?= $form->field($model, 'project_name')->textInput([
        'maxlength' => true,
        'placeholder' => 'Enter project name'
    ]) ?>

    <!-- Description -->
    <?= $form->field($model, 'description')->textarea([
        'rows' => 4,
        'placeholder' => 'Project description'
    ]) ?>

    <!-- Client Dropdown -->
    <?php $clients = Client::find()->orderBy('first_name')->all(); ?>

    <label class="form-label">Client</label>

    <input type="hidden" name="Project[client_id]" id="client-id" value="<?= $model->client_id ?>">

    <input type="text"
        id="client-search"
        class="form-control"
        placeholder="Search client..."
        autocomplete="off"
        value="<?= $model->client ? $model->client->first_name . ' ' . $model->client->last_name : '' ?>">

    <div class="dropdown-menu w-100" id="client-dropdown" style="max-height:220px; overflow-y:auto;">
        <?php foreach ($clients as $client): ?>
            <button type="button"
                class="dropdown-item search-item"
                data-id="<?= $client->id ?>"
                data-name="<?= $client->first_name . ' ' . $client->last_name ?>">
                <?= Html::encode($client->first_name . ' ' . $client->last_name) ?>
            </button>
        <?php endforeach; ?>
    </div>




    <!-- Project Manager Dropdown -->
    <?php $managers = User::find()->where(['status' => User::STATUS_ACTIVE])->all(); ?>

    <label class="form-label mt-3">Project Manager</label>

    <input type="hidden" name="Project[project_manager_id]" id="manager-id" value="<?= $model->project_manager_id ?>">

    <input type="text"
        id="manager-search"
        class="form-control"
        placeholder="Search project manager..."
        autocomplete="off"
        value="<?= $model->projectManager ? $model->projectManager->first_name . ' ' . $model->projectManager->last_name : '' ?>">

    <div class="dropdown-menu w-100" id="manager-dropdown" style="max-height:220px; overflow-y:auto;">
        <?php foreach ($managers as $user): ?>
            <button type="button"
                class="dropdown-item search-item"
                data-id="<?= $user->id ?>"
                data-name="<?= $user->first_name . ' ' . $user->last_name ?>">
                <?= Html::encode($user->first_name . ' ' . $user->last_name) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Status -->
            <?= $form->field($model, 'status')->dropDownList([
                'planned'   => 'Planned',
                'active'    => 'Active',
                'on_hold'   => 'On Hold',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ], ['prompt' => 'Select Status']) ?>
        </div>

        <div class="col-md-6">
            <!-- Priority -->
            <?= $form->field($model, 'priority')->dropDownList([
                'low'    => 'Low',
                'medium' => 'Medium',
                'high'   => 'High',
            ], ['prompt' => 'Select Priority']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'start_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date')->input('date') ?>
        </div>
    </div>

    <!-- Completed At -->
    <?= $form->field($model, 'completed_at')->input('datetime-local') ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'budget')->textInput([
                'placeholder' => '₹ Budget'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'billing_type')->dropDownList([
                'fixed'  => 'Fixed',
                'hourly' => 'Hourly',
            ], ['prompt' => 'Select Billing Type']) ?>
        </div>
    </div>

    <?= $form->field($model, 'estimated_hours')->textInput([
        'placeholder' => 'Estimated hours'
    ]) ?>

    <?= $form->field($model, 'notes')->textarea([
        'rows' => 3,
        'placeholder' => 'Internal notes'
    ]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Create Project' : 'Update Project',
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>