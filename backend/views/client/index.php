<?php

use common\models\Client;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\widgets\Alert;

/** @var yii\web\View $this */
/** @var backend\models\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4">

    <!-- ================= PAGE HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>

        <!-- Create Client Button -->
        <?= Html::a(
            '<i class="bi bi-plus-circle"></i> Create Client',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <!-- ================= CARD ================= -->
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- ================= PJAX WRAPPER ================= -->
            <?php Pjax::begin([
                'id' => 'client-grid-pjax',
                'timeout' => 5000,
                'enablePushState' => false,
            ]); ?>


            <!-- ================= FLASH ALERTS ================= -->
            <div class="mb-3">
                <?= Alert::widget() ?>
            </div>

            <!-- ================= GRID VIEW ================= -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,

                /* Table styling */
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover align-middle',
                ],

                /* Summary text */
                'summary' => '
                    <div class="text-muted mb-2">
                        Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> clients
                    </div>
                ',

                /* Pagination (Bootstrap 5) */
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

                    /* ================= SERIAL ================= */
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'header' => '#',
                        'headerOptions' => ['style' => 'width:60px'],
                    ],

                    /* ================= CLIENT CODE ================= */
                    [
                        'attribute' => 'client_code',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'client_code',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search client code',
                            ]
                        ),
                    ],


                    /* ================= CLIENT NAME ================= */
                    [
                        'attribute' => 'full_name', // ✅ MUST
                        'label' => 'Name',
                        'value' => fn($model) =>
                        trim($model->first_name . ' ' . $model->last_name),
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'full_name',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search name',
                            ]
                        ),
                    ],



                    /* ================= CLIENT TYPE ================= */
                    [
                        'attribute' => 'type',
                        'format' => 'raw',
                        'value' => fn($model) =>
                        $model->type === Client::TYPE_COMPANY
                            ? '<span class="badge bg-info">Company</span>'
                            : '<span class="badge bg-secondary">Individual</span>',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'type',
                            [
                                Client::TYPE_INDIVIDUAL => 'Individual',
                                Client::TYPE_COMPANY    => 'Company',
                            ],
                            [
                                'class' => 'form-select',
                                'prompt' => 'Select type',
                            ]
                        ),
                    ],


                    /* ================= COMPANY NAME ================= */
                    [
                        'attribute' => 'company_name',
                        'value' => fn($model) => $model->company_name ?: '-',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'company_name',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search company',
                            ]
                        ),
                    ],

                    /* ================= STATUS (AJAX DROPDOWN) ================= */
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::dropDownList(
                                'status',
                                $model->status,
                                Client::statusList(),
                                [
                                    'class'   => 'form-select form-select-sm client-status',
                                    'data-id' => $model->id,
                                    'style'   => 'min-width:140px',
                                ]
                            );
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'status',
                            Client::statusList(),
                            [
                                'class' => 'form-select',
                                'prompt' => 'Select status',
                            ]
                        ),
                    ],


                    /* ================= ACTIONS (3 DOT MENU) ================= */
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
                                            'confirm' => 'Are you sure you want to delete this client?',
                                            'method'  => 'post',
                                        ],
                                        'data-pjax' => 0,
                                    ]
                                ) . '</li>
                                        </ul>
                                    </div>
                                ';
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
            <!-- ================= /PJAX ================= -->

        </div>
    </div>
</div>

<!-- /* ================= STATUS CHANGE AJAX ================= */ -->

<script>
    window.CLIENT_STATUS_URL = "<?= Url::to(['client/change-status']) ?>";
</script>