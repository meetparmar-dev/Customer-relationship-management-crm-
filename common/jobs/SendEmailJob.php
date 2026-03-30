<?php

namespace common\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\User;
use Yii;

class SendEmailJob extends BaseObject implements JobInterface
{
    public $from;
    public $to;
    public $subject;
    public $htmlBody;
    public $textBody;
    public $userId;

    public function execute($queue)
    {
        Yii::info("SendEmailJob started for userId={$this->userId}", __METHOD__);

        // IMPORTANT: bypass User::find() status filter
        $user = User::find()
            ->where(['id' => $this->userId])
            ->one();

        if (!$user) {
            Yii::error("User not found in DB. userId={$this->userId}", __METHOD__);
            return false;
        }

        try {
            $message = Yii::$app->mailer->compose(
                ['html' => $this->htmlBody, 'text' => $this->textBody],
                ['user' => $user]
            )
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setSubject($this->subject);

            if (!$message->send()) {
                Yii::error("Email send failed for userId={$this->userId}", __METHOD__);
                return false;
            }

            Yii::info("Email successfully sent to {$this->to}", __METHOD__);
            return true;
        } catch (\Throwable $e) {
            Yii::error(
                "Exception while sending email: " . $e->getMessage(),
                __METHOD__
            );
            return false;
        }
    }

    public function getTtr()
    {
        return 300;
    }

    public function canRetry($attempt, $error)
    {
        return $attempt < 3;
    }
}
