<?php

namespace App\Database;

use Dotenv\Dotenv;
use mysqli;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

class DbConnection
{
  private $server;
  private $username;
  private $password;
  private $db;
  private $port;

  private static $dbConnection = null;
  private $conn;

  private function __construct()
  {
    $this->server = $_ENV['SERVER'];
    $this->username = $_ENV['USER_NAME'];
    $this->password = $_ENV['PASSWORD'];
    $this->db = $_ENV['DB_NAME'];
    $this->port = $_ENV['PORT'];

    $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db, $this->port);
  }

  // -> object
  public static function getDbConnectionInstance(){
    if (self::$dbConnection === null) {
      self::$dbConnection = new DbConnection();
    }

    return self::$dbConnection;
  }

  public function getDbConnection(){
    return $this->conn;
  }
}