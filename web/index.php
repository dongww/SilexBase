<?php
$silexBasePath = __DIR__ . '/../SilexBase/vendor/autoload.php';
$rootPath = __DIR__ . '/..';

$loader = require_once $silexBasePath;
$loader->add('', $rootPath . '/app/src');

$app = new SilexBase\Core\Application(array(
    'root_path' => $rootPath,
    'locale' => 'zh',
));
$app['http_cache']->run();