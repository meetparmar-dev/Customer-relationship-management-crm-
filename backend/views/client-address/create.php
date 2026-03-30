<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ClientAddress $model */

$this->title = 'Create Client Address';
$this->params['breadcrumbs'][] = ['label' => 'Client Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-address-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
