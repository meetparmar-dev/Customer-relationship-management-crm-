<?php

use common\models\ClientAddress;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ClientAddressSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-address-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Address', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'client_id',
            'address_type',
            'address:ntext',
            'city',
            //'state',
            //'country',
            //'pincode',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ClientAddress $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
