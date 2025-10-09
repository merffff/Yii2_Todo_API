<?php

namespace app\components;

use Yii;

trait ResponseHelper
{
    protected function jsonResponse(
        bool $success,
        string $message = '',
        array $data = [],
        int $statusCode = 200
    ): array {
        Yii::$app->response->setStatusCode($statusCode);
        return compact('success', 'message', 'data');
    }
}
