<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Project $model */

$this->title = $model->project_name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4 project-view">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><?= Html::encode($this->title) ?></h3>
            <small class="text-muted">Project Details</small>
        </div>

        <div class="d-flex gap-2">
            <?= Html::a('Edit Project', ['update', 'id' => $model->id], [
                'class' => 'btn btn-primary'
            ]) ?>
            <?= Html::a('Back to Projects', ['index'], [
                'class' => 'btn btn-outline-secondary'
            ]) ?>
        </div>
    </div>

    <div class="row g-4">

        <!-- ================= PROJECT INFO ================= -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold">
                    Project Information
                </div>
                <div class="card-body">

                    <p class="mb-2">
                        <strong>Description</strong><br>
                        <?= nl2br(Html::encode($model->description)) ?>
                    </p>

                    <p><strong>Deadline:</strong> <?= Yii::$app->formatter->asDate($model->end_date) ?></p>

                    <p>
                        <strong>Status:</strong>
                        <span class="badge bg-secondary">
                            <?= ucfirst($model->status) ?>
                        </span>
                    </p>

                    <p>
                        <strong>Created:</strong>
                        <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                    </p>

                </div>
            </div>
        </div>

        <!-- ================= ASSIGNED USER ================= -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold">
                    Assigned User
                </div>
                <div class="card-body">

                    <?php if ($model->projectManager): ?>
                        <p><strong>Name:</strong> <?= Html::encode($model->projectManager->fullName) ?></p>
                        <p><strong>Email:</strong> <?= Html::encode($model->projectManager->email) ?></p>
                        <p><strong>Phone:</strong> <?= Html::encode($model->projectManager->phone ?? '-') ?></p>
                    <?php else: ?>
                        <p class="text-muted">No user assigned</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- ================= CLIENT INFO ================= -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold">
                    Client Information
                </div>
                <div class="card-body">

                    <?php if ($model->client): ?>
                        <p><strong>Company:</strong> <?= Html::encode($model->client->company_name) ?></p>
                        <p><strong>Contact Person:</strong> <?= Html::encode($model->client->contactPerson) ?></p>
                        <p><strong>Email:</strong> <?= Html::encode($model->client->email) ?></p>
                        <p><strong>Phone:</strong> <?= Html::encode($model->client->phone) ?></p>
                    <?php else: ?>
                        <p class="text-muted">Client not linked</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- ================= TASK SUMMARY ================= -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold">
                    Tasks Information
                </div>
                <div class="card-body">

                    <p>
                        <strong>Total Tasks:</strong>
                        <?= count($model->tasks) ?>
                    </p>

                    <ul class="list-unstyled">
                        <?php foreach ($model->tasks as $task): ?>
                            <li class="mb-2">
                                <?= Html::encode($task->title) ?>
                                <span class="badge bg-light text-dark float-end">
                                    <?= ucfirst($task->status) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        </div>

        <!-- ================= COMPANY ADDRESS ================= -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    Company Address
                </div>

                <div class="card-body">
                    <?php if ($model->client && $model->client->officeAddress): ?>

                        <p class="mb-1">
                            <strong>Address Line:</strong><br>
                            <?= Html::encode($model->client->officeAddress->address) ?>
                        </p>

                        <p class="mb-1">
                            <strong>City:</strong>
                            <?= Html::encode($model->client->officeAddress->city ?? '-') ?>
                        </p>

                        <p class="mb-1">
                            <strong>State:</strong>
                            <?= Html::encode($model->client->officeAddress->state ?? '-') ?>
                        </p>

                        <p class="mb-1">
                            <strong>Country:</strong>
                            <?= Html::encode($model->client->officeAddress->country ?? '-') ?>
                        </p>

                        <p class="mb-0">
                            <strong>Pincode:</strong>
                            <?= Html::encode($model->client->officeAddress->pincode ?? '-') ?>
                        </p>

                    <?php else: ?>
                        <span class="text-muted">Office address not available</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!-- ================= PROJECT TASKS TABLE ================= -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    Project Tasks
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($model->tasks as $task): ?>
                                <tr>
                                    <td><?= Html::encode($task->title) ?></td>
                                    <td><?= Html::encode($task->assignedUser->full_name ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucfirst($task->status) ?>
                                        </span>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDate($task->due_date) ?></td>
                                    <td>
                                        <?= Html::a('View', ['/task/view', 'id' => $task->id], [
                                            'class' => 'btn btn-sm btn-link'
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>