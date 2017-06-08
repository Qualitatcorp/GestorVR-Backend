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
            'enableSession' => false,
            'loginUrl' =>'',
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
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST authentication/<action:\w+>' => 'authentication/<action>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/comuna',
                        'v1/dispositivo',
                        'v1/dispositivotipo',
                        'v1/empresadispositivo',
                        'v1/empresauser',
                        'v1/licencia',
                        'v1/licenciaregistro',
                        'v1/licenciatipo',
                        'v1/pais',
                        'v1/rvalternativa',
                        'v1/rvevaluacion',
                        'v1/rvficha',
                        'v1/rvitem',
                        'v1/rvpregunta',
                        'v1/rvproyecto',
                        'v1/rvrespuesta',
                        'v1/trabajador',
                        'v1/userauthentication',
                        'v1/userauthorization',
                        'v1/userclient',
                        'v1/userresource',
                        'v1/userresourcechildren',
                        'v1/rvintalternativa',
                        'v1/rvintevaluacion',
                        'v1/rvintpregunta',
                        'v1/rvinttipo'
                    ],
                    'extraPatterns' => [
                        'GET search' => 'search',
                    ],
                    'pluralize' => false,
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user',
                    ],
                    'extraPatterns' => [
                        'GET search' => 'search',
                        'GET identity'=>'getidentity',
                        'PUT identity'=>'updateidentity'
                        // 'POST changepassword'=>'changepassword'
                    ],
                    'pluralize' => false,
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/empresa',
                    ],
                    'extraPatterns' => [
                        'GET search' => 'search',
                        'GET ficha'=>'getfichas',
                        'GET ficha/<id:\d+>'=>'getficha',
                        'GET trabajador'=>'gettrabajadores',
                        'GET trabajador/<id:\d+>'=>'gettrabajador',
                        'GET trabajador/<id:\d+>/fichas'=>'gettrabajadorfichas',
                        // 'POST changepassword'=>'changepassword'
                    ],
                    'pluralize' => false,
                ],

                'GET <ns:\w+>/<controller:\w+>/<action:\w+>'=>'<ns>/<controller>/<action>',
                'GET <ns:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<ns>/<controller>/<action>',
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
