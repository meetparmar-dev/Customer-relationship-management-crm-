<?php

namespace backend\listeners;

use common\models\Project;
use Yii;

class ProjectListener
{
    public static function onCreated($event)
    {
        /** @var Project $project */
        $project = $event->sender;

        Yii::info("New project created: {$project->name}", 'project');
    }

    public static function onUpdated($event)
    {
        $project = $event->sender;

        Yii::info("Project updated: {$project->name}", 'project');
    }

    public static function onStatusChanged($event)
    {
        $project = $event->sender;

        Yii::info(
            "Project status changed: {$project->name} → {$project->status}",
            'project'
        );
    }
}
