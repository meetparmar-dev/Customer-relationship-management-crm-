<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Client $model */

$this->title = $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="client-view">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><?= Html::encode($this->title) ?></h3>

        <div class="d-flex gap-2">
            <?= Html::a('Edit Client', ['update', 'id' => $model->id], [
                'class' => 'btn btn-primary'
            ]) ?>

            <?= Html::a('Delete Client', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this client?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">

        <!-- ================= CLIENT INFO ================= -->
        <div class="col-md-6">
            <div class="card mb-3 h-100">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    <span>Client Information</span>
                    <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>

                <div class="card-body">
                    <p><b>Client Code:</b> <?= $model->client_code ?></p>
                    <p><b>Type:</b> <?= ucfirst($model->type) ?></p>
                    <p><b>Company:</b> <?= $model->company_name ?: '-' ?></p>
                    <p><b>Status:</b> <?= ucfirst($model->status) ?></p>
                    <p><b>Email:</b> <?= $model->email ?></p>
                    <p><b>Phone:</b> <?= $model->phone ?></p>
                    <p><b>Created:</b> <?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
                </div>
            </div>
        </div>

        <?php
        $primary       = $model->primaryContact;
        $contactCount  = count($model->contacts);
        ?>

        <!-- ================= CONTACT INFO ================= -->
        <div class="col-md-6">
            <div class="card mb-3 h-100">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    <span>Contact Information</span>

                    <?php if ($primary): ?>
                        <div class="d-flex gap-1">
                            <?= Html::a('Edit', ['client-contact/update', 'id' => $primary->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            <?= Html::a('Delete', ['client-contact/delete', 'id' => $primary->id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => ['confirm' => 'Delete this contact?', 'method' => 'post'],
                            ]) ?>
                        </div>
                    <?php else: ?>
                        <?= Html::a('+ Add', ['client-contact/create', 'client_id' => $model->id], ['class' => 'btn btn-sm btn-outline-success']) ?>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <?php $primary = $model->primaryContact; ?>

                    <?php if ($primary): ?>
                        <p><b>Name:</b> <?= $primary->name ?></p>
                        <p><b>Designation:</b> <?= $primary->designation ?: '-' ?></p>
                        <p><b>Email:</b> <?= $primary->email ?: '-' ?></p>
                        <p><b>Phone:</b> <?= $primary->phone ?: '-' ?></p>

                        <?php if (count($model->contacts) > 1): ?>
                            <?= Html::a(
                                '+ ' . (count($model->contacts) - 1) . ' more contacts',
                                ['client-contact/index', 'client_id' => $model->id],
                                ['class' => 'btn btn-sm btn-outline-secondary mt-2']
                            ) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">No contact added</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ================= ADDRESSES ================= -->
        <div class="col-md-12 mt-2">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    <span>Address Information</span>

                    <?= Html::a('+ Add', ['client-address/create', 'client_id' => $model->id], [
                        'class' => 'btn btn-sm btn-outline-success'
                    ]) ?>
                </div>

                <div class="card-body">
                    <?php if (!empty($model->addresses)): ?>
                        <div class="row">
                            <?php foreach ($model->addresses as $address): ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 mb-3">

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong><?= ucfirst($address->address_type) ?> Address</strong>

                                            <div class="d-flex gap-1">
                                                <?= Html::a('Edit', ['client-address/update', 'id' => $address->id], [
                                                    'class' => 'btn btn-sm btn-outline-primary'
                                                ]) ?>

                                                <?= Html::a('Delete', ['client-address/delete', 'id' => $address->id], [
                                                    'class' => 'btn btn-sm btn-outline-danger',
                                                    'data' => [
                                                        'confirm' => 'Delete this address?',
                                                        'method' => 'post',
                                                    ],
                                                ]) ?>
                                            </div>
                                        </div>

                                        <p class="mb-1"><b>Address:</b> <?= Html::encode($address->address) ?></p>
                                        <p class="mb-1"><b>City:</b> <?= Html::encode($address->city) ?></p>
                                        <p class="mb-1"><b>State:</b> <?= Html::encode($address->state) ?></p>
                                        <p class="mb-0"><b>Pincode:</b> <?= Html::encode($address->pincode) ?></p>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No address added</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ================= PROJECTS ================= -->
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    <span>Projects Overview</span>

                    <?= Html::a('View All', [
                        '/project/index',
                        'ProjectSearch[client_id]' => $model->id
                    ], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>

                <div class="card-body p-0">
                    <?php if (!empty($model->projects)): ?>
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Timeline</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($model->projects as $project): ?>
                                    <tr>
                                        <td><?= $project->project_code ?></td>

                                        <td>
                                            <?= Html::a(
                                                $project->project_name,
                                                ['/project/view', 'id' => $project->id],
                                                ['class' => 'text-decoration-none']
                                            ) ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= ucfirst(str_replace('_', ' ', $project->status)) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= $project->start_date ?: '-' ?> → <?= $project->end_date ?: '-' ?>
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <?= Html::a(
                                                    'Edit',
                                                    ['/project/update', 'id' => $project->id],
                                                    ['class' => 'btn btn-sm btn-outline-primary']
                                                ) ?>

                                                <?= Html::a(
                                                    'Delete',
                                                    ['/project/delete', 'id' => $project->id],
                                                    [
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                        'data' => [
                                                            'confirm' => 'Are you sure you want to delete this project?',
                                                            'method' => 'post',
                                                        ],
                                                    ]
                                                ) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="p-3 text-muted mb-0">No projects found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>