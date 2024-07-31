<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Connecter
{
  private $server;
  private $username;
  private $password;
  private $db;
  private $port;

  private static $connecter = null;
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
  public static function getConneterInstance(){
    if (self::$connecter === null) {
      self::$connecter = new Connecter();
    }

    return self::$connecter;
  }

  public function getDbConnection(){
    return $this->conn;
  }
}