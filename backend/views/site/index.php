<?php

use yii\helpers\Html;

$this->title = 'Dashboard';
?>

<div class="container-fluid px-4 mt-4">

    <!-- HEADER -->
    <div class="mb-4">
        <h4 class="fw-semibold mb-1">Dashboard</h4>
        <small class="text-muted">Clean overview of your system</small>
    </div>

    <!-- DROPDOWN -->
    <div class="d-flex justify-content-end mb-3">
        <select class="form-select form-select-sm w-auto" id="periodSelect">
            <option>Last 1 Month</option>
            <option>Last 3 Months</option>
            <option selected>Last 6 Months</option>
            <option>Last 12 Months</option>
        </select>
    </div>

    <!-- STATS -->
    <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">

        <?php
        $cards = [
            ['Clients', 'people', 'primary', 'card-clients', $clientCount],
            ['Projects', 'kanban', 'success', 'card-projects', $projectCount],
            ['Total Tasks', 'list-task', 'info', 'card-tasks', $totalTaskCount],
            ['Pending Tasks', 'hourglass-split', 'warning', 'card-pending', $pendingTaskCount],
            ['Completed', 'check-circle', 'success', 'card-completed', $completedTaskCount],
        ];
        ?>

        <?php foreach ($cards as [$label, $icon, $color, $id, $value]): ?>
            <div class="col">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <small class="text-muted"><?= $label ?></small>
                            <h4 class="fw-semibold mb-0" id="<?= $id ?>"><?= $value ?></h4>
                        </div>
                        <i class="bi bi-<?= $icon ?> text-<?= $color ?> fs-4"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- CHARTS -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Projects Growth</div>
                    <canvas id="projectChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Task Status</div>
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-3">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Recent Clients</div>

                    <?php foreach ($recentClients as $client): ?>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span><?= Html::encode($client->first_name . ' ' . $client->last_name) ?></span>
                            <small class="text-muted">
                                <?= Yii::$app->formatter->asRelativeTime($client->created_at) ?>
                            </small>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Upcoming Tasks</div>

                    <?php foreach ($upcomingTasks as $task): ?>
                        <?php
                        $badge = match ($task->status) {
                            'pending' => 'warning',
                            'in_progress' => 'primary',
                            'completed' => 'success',
                            default => 'secondary',
                        };
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span><?= Html::encode($task->title) ?></span>
                            <span class="badge bg-<?= $badge ?>-subtle text-<?= $badge ?>">
                                <?= ucfirst(str_replace('_', ' ', $task->status)) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.DASHBOARD = {
        projectLabels: <?= json_encode($projectLabels) ?>,
        projectData: <?= json_encode($projectData) ?>,
        taskData: <?= json_encode($taskData) ?>,
        statsUrl: "<?= Yii::$app->urlManager->createUrl(['site/get-dashboard-stats']) ?>"
    };
</script>