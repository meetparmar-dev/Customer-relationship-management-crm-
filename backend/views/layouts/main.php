<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use common\widgets\Alert;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ?? 'CRM Admin') ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="main-wrapper d-flex">

        <!-- SIDEBAR -->
        <?= $this->render('sidebar') ?>

        <!-- CONTENT -->
        <div class="content bg-white w-100">
            <?= $this->render('topbar') ?>

            <div class="p-4">
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>

    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>