<?php

use yii\helpers\Url;

$controller = Yii::$app->controller->id;
?>

<div class="sidebar">

    <div class="p-3 fw-bold text-primary fs-5 sidebar-title">
        <span>CRM Admin panel</span>
    </div>

    <a href="<?= Url::to(['/site/index']) ?>"
        class="<?= $controller === 'site' ? 'active' : '' ?>">
        <i class="bi bi-house me-2"></i>
        <span>Dashboard</span>
    </a>

    <a href="<?= Url::to(['/user/index']) ?>"
        class="<?= $controller === 'user' ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i>
        <span>Users</span>
    </a>

    <a href="<?= Url::to(['/client/index']) ?>"
        class="<?= $controller === 'client' ? 'active' : '' ?>">
        <i class="bi bi-briefcase me-2"></i>
        <span>Clients</span>
    </a>

    <a href="<?= Url::to(['/task/index']) ?>"
        class="<?= $controller === 'task' ? 'active' : '' ?>">
        <i class="bi bi-list-task me-2"></i>
        <span>Tasks</span>
    </a>

    <a href="<?= Url::to(['/project/index']) ?>"
        class="<?= $controller === 'project' ? 'active' : '' ?>">
        <i class="bi bi-kanban me-2"></i>
        <span>Projects</span>
    </a>

</div>