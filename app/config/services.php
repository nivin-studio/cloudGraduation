<?php
use Phalcon\Crypt;
use Phalcon\Http\Request;
use Phalcon\Http\Response\Cookies;
use Phalcon\Logger\Factory as Logger;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Predis\Client as Redis;

/**
 * 配置文件
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * request
 */
$di->setShared('request', function () {
    return new Request();
});

/**
 * url
 */
$di->setShared('url', function () {
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
});

/**
 * 设置视图组件
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath'      => $config->application->cacheDir,
                'compiledSeparator' => '_',
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class,

    ]);

    return $view;
});

/**
 * mysql数据库
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class  = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset,
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});

/**
 * mongo数据库
 */
$di->setShared('mongo', function () {
    $config = $this->getConfig();

    if (!$config->mongo->username || !$config->mongo->password) {
        $dsn = sprintf(
            'mongodb://%s:%s',
            $config->mongo->host,
            $config->mongo->port
        );
    } else {
        $dsn = sprintf(
            'mongodb://%s:%s@%s:%s',
            $config->mongo->username,
            $config->mongo->password,
            $config->mongo->host,
            $config->mongo->port
        );
    }

    $mongo = new MongoDB\Driver\Manager($dsn);

    return $mongo;
});

/**
 * redis缓存
 */
$di->setShared('redis', function () {
    $config = $this->getConfig();

    $redis = new Redis([
        'scheme' => 'tcp',
        'host'   => $config->redis->host,
        'port'   => $config->redis->port,
    ]);

    return $redis;
});

/**
 * redis集群缓存
 */
$di->setShared('redisGroup', function () {
    $config = $this->getConfig();

    $redis = new Redis((array) $config->redisGroup, ['cluster' => 'redis']);

    return $redis;
});

/**
 * session
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * cookies
 */
$di->setShared('cookies', function () {
    $cookies = new Cookies();

    $cookies->useEncryption(true);

    return $cookies;
});

/**
 * 加密
 */
$di->setShared('crypt', function () {
    $crypt = new Crypt();
    $key   = 'T5\xb3\x6d\xa9\x78\x520t7w!z%C*F-Ck\x99\x52\x5c';

    $crypt->setCipher('aes-256-ctr');
    $crypt->setKey($key);

    return $crypt;
});

/**
 * 日志
 */
$di->setShared('log', function () {
    $options = [
        'name'    => '../logs/log.txt',
        'adapter' => 'file',
    ];

    $logger = Logger::load($options);

    return $logger;
});
