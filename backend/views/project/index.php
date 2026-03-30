<?php

use common\models\Project;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/** @var yii\web\View $this */
/** @var backend\models\ProjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>

        <?= Html::a(
            '<i class="bi bi-plus-circle"></i> Create Project',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <?php Pjax::begin([
                'id' => 'project-grid-pjax',
                'timeout' => 5000,
                'enablePushState' => false,
            ]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'tableOptions' => [
                    'class' => 'table table-bordered table-hover align-middle'
                ],

                'summary' => '<div class="text-muted mb-2">
                    Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> projects
                </div>',

                'pager' => [
                    'class' => 'yii\bootstrap5\LinkPager',
                    'options' => ['class' => 'pagination justify-content-center mt-3'],
                    'linkOptions' => ['class' => 'page-link'],
                    'pageCssClass' => 'page-item',
                    'prevPageCssClass' => 'page-item',
                    'nextPageCssClass' => 'page-item',
                    'activePageCssClass' => 'active',
                    'disabledPageCssClass' => 'disabled',
                ],

                'columns' => [

                    [
                        'class' => 'yii\grid\SerialColumn',
                        'header' => '#',
                        'headerOptions' => ['style' => 'width:60px']
                    ],

                    [
                        'attribute' => 'project_code',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'project_code',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search project code',
                            ]
                        ),
                    ],

                    [
                        'attribute' => 'project_name',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'project_name',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search project name',
                            ]
                        ),
                    ],

                    [
                        'attribute' => 'client_id',
                        'label' => 'Client',
                        'value' => function ($model) {
                            return $model->client
                                ? ($model->client->company_name ?: $model->client->email)
                                : '-';
                        },
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'client_id',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search client',
                            ]
                        ),
                    ],


                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::dropDownList(
                                'status',
                                $model->status,
                                [
                                    'pending'     => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed'   => 'Completed',
                                    'on_hold'     => 'On Hold',
                                ],
                                [
                                    'class' => 'form-select form-select-sm project-status',
                                    'data-id' => $model->id,
                                    'style' => 'min-width:150px'
                                ]
                            );
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'status',
                            [
                                'pending'     => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed'   => 'Completed',
                                'on_hold'     => 'On Hold',
                            ],
                            [
                                'class' => 'form-select',
                                'prompt' => 'Select status',
                            ]
                        ),
                    ],


                    [
                        'attribute' => 'priority',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return match ($model->priority) {
                                'high'   => '<span class="badge bg-danger">High</span>',
                                'medium' => '<span class="badge bg-warning text-dark">Medium</span>',
                                default  => '<span class="badge bg-secondary">Low</span>',
                            };
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'priority',
                            [
                                'low'    => 'Low',
                                'medium' => 'Medium',
                                'high'   => 'High',
                            ],
                            [
                                'class' => 'form-select',
                                'prompt' => 'Select priority',
                            ]
                        ),
                    ],


                    [
                        'attribute' => 'start_date',
                        'format' => ['date', 'php:d M Y'],
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'start_date_range',
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'Select start date range',
                            ],
                            'pluginOptions' => [
                                'locale' => ['format' => 'd M Y'],
                                'autoUpdateInput' => false,
                                'maxDate' => date('Y-m-d'),
                            ],
                        ]),
                    ],

                    [
                        'attribute' => 'end_date',
                        'format' => ['date', 'php:d M Y'],
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'end_date_range',
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'Select end date range',
                            ],
                            'pluginOptions' => [
                                'locale' => ['format' => 'd M Y'],
                                'autoUpdateInput' => false,
                                'maxDate' => date('Y-m-d'),
                            ],
                        ]),
                    ],


                    // ACTIONS (3 DOT)
                    [
                        'class' => ActionColumn::class,
                        'header' => '',
                        'headerOptions' => ['style' => 'width:60px'],
                        'contentOptions' => ['class' => 'text-center'],
                        'template' => '{actions}',
                        'buttons' => [
                            'actions' => function ($url, $model) {
                                return '
                                <div class="dropdown">
                                    <a href="#" class="text-muted" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>' . Html::a(
                                    '<i class="bi bi-eye me-2"></i> View',
                                    ['view', 'id' => $model->id],
                                    ['class' => 'dropdown-item', 'data-pjax' => 0]
                                ) . '</li>
                                        <li>' . Html::a(
                                    '<i class="bi bi-pencil me-2"></i> Update',
                                    ['update', 'id' => $model->id],
                                    ['class' => 'dropdown-item', 'data-pjax' => 0]
                                ) . '</li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>' . Html::a(
                                    '<i class="bi bi-trash me-2"></i> Delete',
                                    ['delete', 'id' => $model->id],
                                    [
                                        'class' => 'dropdown-item text-danger',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this project?',
                                            'method' => 'post',
                                        ],
                                        'data-pjax' => 0,
                                    ]
                                ) . '</li>
                                    </ul>
                                </div>';
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>

<script>
    window.PROJECT_STATUS_URL = "<?= Url::to(['project/change-status']) ?>";
</script>