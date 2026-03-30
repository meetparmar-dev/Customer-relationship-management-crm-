<?php

namespace common\components;

use Yii;
use yii\base\Component;

class AvatarComponent extends Component
{
    /**
     * Get avatar URL
     */
    public function get($user): string
    {
        // If user has uploaded avatar
        if (!empty($user->avatar)) {
            return Yii::$app->request->hostInfo
                . Yii::$app->request->baseUrl
                . '/uploads/avatars/' . $user->avatar;
        }

        // Default avatar (DiceBear)
        $seed = $user->id ?? $user->username ?? 'user';

        return 'https://api.dicebear.com/9.x/avataaars/svg?seed='
            . urlencode($seed);
    }
}
