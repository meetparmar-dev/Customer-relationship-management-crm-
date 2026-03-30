<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/crm.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css',


    ];
    public $js = [
        'js/task-search.js',
        'js/task-index.js',
        'js/searchable-dropdown.js',
        'js/project-dropdown.js',
        'js/client-form.js',
        'js/client-index.js',
        'js/project-index.js',
        'js/dashboard.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
