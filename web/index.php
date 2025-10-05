<?php


defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require './../vendor/autoload.php';
require './../vendor/yiisoft/yii2/Yii.php';

$config = require './../config/web.php';

(new yii\web\Application($config))->run();
