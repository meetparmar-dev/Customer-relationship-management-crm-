<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Project;
use common\models\User;
use backend\assets\AppAsset;
use common\models\Task;

AppAsset::register($this);

/** @var yii\web\View $this */
/** @var common\models\Task $model */

$selectedProject = $model->project;
$selectedUser    = $model->assignee;
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

            <!-- ================= PROJECT SEARCH ================= -->
            <div class="col-md-6 mb-3 position-relative">
                <label class="form-label">Project</label>

                <input type="text"
                    id="projectSearch"
                    class="form-control"
                    placeholder="Search project..."
                    autocomplete="off"
                    value="<?= $selectedProject ? Html::encode($selectedProject->project_name) : '' ?>">

                <input type="hidden"
                    name="Task[project_id]"
                    id="project_id"
                    value="<?= $model->project_id ?>">

                <div id="projectList"
                    class="border rounded mt-1 search-dropdown"
                    style="max-height:200px;overflow:auto;display:none;">
                    <?php foreach (Project::find()->orderBy('project_name')->all() as $project): ?>
                        <div class="p-2 search-item project-item"
                            data-id="<?= $project->id ?>">
                            <?= Html::encode($project->project_name) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?= Html::error($model, 'project_id', ['class' => 'text-danger small']) ?>
            </div>

            <!-- ================= ASSIGNED TO SEARCH ================= -->
            <div class="col-md-6 mb-3 position-relative">
                <label class="form-label">Assign To</label>

                <input type="text"
                    id="userSearch"
                    class="form-control"
                    placeholder="Search user..."
                    autocomplete="off"
                    value="<?= $selectedUser ? Html::encode($selectedUser->first_name . ' ' . $selectedUser->last_name) : '' ?>">

                <input type="hidden"
                    name="Task[assigned_to]"
                    id="assigned_to"
                    value="<?= $model->assigned_to ?>">

                <div id="userList"
                    class="border rounded mt-1 search-dropdown"
                    style="max-height:200px;overflow:auto;display:none;">
                    <?php foreach (User::find()->orderBy('first_name')->all() as $user): ?>
                        <div class="p-2 search-item user-item"
                            data-id="<?= $user->id ?>">
                            <?= Html::encode($user->first_name . ' ' . $user->last_name) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?= Html::error($model, 'assigned_to', ['class' => 'text-danger small']) ?>
            </div>

            <!-- ================= TITLE ================= -->
            <div class="col-md-12 mb-3">
                <?= $form->field($model, 'title')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Enter task title'
                ]) ?>
            </div>

            <!-- ================= DESCRIPTION ================= -->
            <div class="col-md-12 mb-3">
                <?= $form->field($model, 'status')
                    ->dropDownList(Task::statusList(), ['prompt' => 'Select Status']) ?>
            </div>

            <!-- ================= STATUS ================= -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'priority')
                    ->dropDownList(Task::priorityList(), ['prompt' => 'Select Priority']) ?>
            </div>

            <!-- ================= PRIORITY ================= -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'priority')->dropDownList([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ], ['prompt' => 'Select Priority']) ?>
            </div>

            <!-- ================= ESTIMATED HOURS ================= -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'estimated_hours')->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'step' => 0.5,
                ]) ?>
            </div>

            <!-- ================= DATES ================= -->
            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'start_date')->input('date') ?>
            </div>

            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'due_date')->input('date') ?>
            </div>

            <div class="col-md-4 mb-3">
                <?= $form->field($model, 'completed_at')->input('date') ?>
            </div>

        </div>

        <!-- ================= ACTION BUTTONS ================= -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-light']) ?>
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create Task' : 'Update Task',
                ['class' => 'btn btn-success']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>