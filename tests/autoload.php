<?php
use Composer\Autoload\ClassLoader;

include_once dirname(__FILE__).'/../vendor/autoload.php';
$classLoader = new ClassLoader();
$classLoader->addPsr4("app\\models\\",dirname(__FILE__).'/app/models',true);
$classLoader->addPsr4("database\\migrations\\",dirname(__FILE__).'/laravault/migrations',true);
$classLoader->register();