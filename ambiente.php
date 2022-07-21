<?php
spl_autoload_extensions('.php');
spl_autoload_register('load');

function load($className) {
  $extension = spl_autoload_extensions();
  require_once($_SERVER["DOCUMENT_ROOT"] . str_replace('\\', '/', $className . $extension));
}

$_SERVER["DOCUMENT_ROOT"] = "/home/pablo/phpapi/";
