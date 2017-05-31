<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'GestorVR',
    'language' => 'es',
    'sourceLanguage' => 'es_CL',
    'timeZone' => 'America/Santiago',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'yC8juG2gpIhV7Rmlx14cPgy3WdB6u4kX',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            // 'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST authentication/<action:\w+>' => 'authentication/<action>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/test'
                    ],
                    'extraPatterns' => [
                        'GET search' => 'search'
                    ],
                    'pluralize' => false,
                ],
            ],
        ],
        
    ],
    'params' => $params,
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],        
        'report' => [
            'class' => 'app\modules\report\Module',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
