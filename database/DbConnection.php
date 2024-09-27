<?php

namespace app\database;

use mysqli;

class DbConnection
{
  private $server;
  private $username;
  private $password;
  private $db;
  private $port;

  private static $dbConnection = null;
  private $conn;

  private function __construct($config)
  {
    $this->server = $config['SERVER'];
    $this->username = $config['USER_NAME'];
    $this->password = $config['PASSWORD'];
    $this->db = $config['DB_NAME'];
    $this->port = $config['PORT'];

    $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db, $this->port);
  }

  // -> object
  public static function getDbConnectionInstance($config = []): DbConnection
  {
    if (self::$dbConnection === null) {
      self::$dbConnection = new DbConnection($config);
    }

    return self::$dbConnection;
  }

  public function getDbConnection()
  {
    return $this->conn;
  }
}