<?php

use common\models\ClientContact;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ClientContactSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-contact-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'client_id',
            'name',
            'designation',
            'email:email',
            //'phone',
            //'is_primary',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ClientContact $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
