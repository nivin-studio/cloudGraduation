<?php

$loader = new \Phalcon\Loader();

/**
 * 需要自动注册的目录
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->servicesDir,
        $config->application->pluginsDir,
        $config->application->libraryDir,
    ]
);

// 注册自动加载器
$loader->register();
