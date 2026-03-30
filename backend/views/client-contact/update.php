<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ClientContact $model */

$this->title = 'Update Client Contact: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Client Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-contact-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
