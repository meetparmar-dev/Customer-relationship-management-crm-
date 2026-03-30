<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class CountryController extends ActiveController
{
    public $modelClass = 'common\models\Country';

    public function actionFoo()
    {
        return [
            'status' => true,
            'messsage' => 'API controller'
        ];
    }
}

