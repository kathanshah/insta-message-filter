<?php

require_once 'param.php';

use \PDO as PDO;

class Connection {
  private static $instance = NULL;

  private function __construct() {}

  private function __clone() {}

  public static function getInstance() {
    if (!isset(self::$instance)) {
      $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
      self::$instance = new PDO('mysql:host='.DB_HOST.':'.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS, $pdo_options);
      self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return self::$instance;
  }
}