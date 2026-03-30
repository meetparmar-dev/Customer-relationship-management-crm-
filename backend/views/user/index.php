<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>

        <?= Html::a(
            '<i class="bi bi-plus-circle"></i> Create User',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <?php Pjax::begin([
                'id' => 'user-grid-pjax',
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
                    Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> users
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

                    // 'id',
                    [
                        'attribute' => 'full_name',
                        'label' => 'Full Name',
                        'value' => function ($model) {
                            return trim($model->first_name . ' ' . $model->last_name);
                        },
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'full_name',
                            ['class' => 'form-control', 'placeholder' => 'Search name']
                        ),
                    ],

                    [
                        'attribute' => 'username',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'username',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search username'
                            ]
                        ),
                    ],

                    [
                        'attribute' => 'email',
                        'filter' => Html::activeTextInput(
                            $searchModel,
                            'email',
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Search email'
                            ]
                        ),
                    ],


                    [
                        'attribute' => 'role',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->role == User::ROLE_ADMIN) {
                                return '<span class="badge bg-danger">Admin</span>';
                            }
                            return '<span class="badge bg-primary">User</span>';
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'role',
                            [
                                User::ROLE_ADMIN => 'Admin',
                                User::ROLE_USER  => 'User',
                            ],
                            [
                                'class' => 'form-select',
                                'prompt' => 'All roles'
                            ]
                        ),
                    ],




                    [
                        'attribute' => 'created_at',
                        'label' => 'Created Date',
                        'format' => ['date', 'php:d M Y'],
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'created_at_range',
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'Select date range',
                            ],
                            'pluginOptions' => [
                                'locale' => [
                                    'format' => 'd M Y',
                                ],
                                'autoUpdateInput' => false,
                                'maxDate' => date('Y-m-d'),
                            ],
                        ]),
                    ],



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
                                            'confirm' => 'Are you sure you want to delete this user?',
                                            'method' => 'post',
                                        ],
                                        'data-pjax' => 0
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

<?php

$changeRoleUrl = Url::to(['user/change-role']);

$js = <<<JS
$(document).on('change', '.user-role', function () {

    let role = $(this).val();
    let id   = $(this).data('id');

    $.post('$changeRoleUrl', {
        id: id,
        role: role,
        _csrf: yii.getCsrfToken()
    }, function (response) {
        alert(response.message);

        if (!response.success) {
            // $(this).val($(this).data('current')); // optional
        }

    }).fail(function () {
        alert('Server error occurred');
    });

});
JS;

$this->registerJs($js);
?>