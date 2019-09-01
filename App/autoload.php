<?php
/**
 * 这里注册了一个自动加载函数，用来按照命名空间加载对应目录的php文件
 *
 * @author az13js
 */
spl_autoload_register(function($class) {
    require __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class . '.php');
});
