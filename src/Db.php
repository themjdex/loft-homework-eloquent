<?php

namespace Base;

class Db
{
    /** @var \PDO */
    private $pdo;
    private $log = [];
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function getConnection()
    {
        $host = DB_HOST;
        $dbName = DB_NAME;
        $dbUser = DB_USER;
        $dbPassword = DB_PASSWORD;

        if (!$this->pdo) {
            $this->pdo = new \PDO(
                "mysql:host=$host;dbname=$dbName",
                $dbUser,
                $dbPassword,
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
                ]
            );
        }

        return $this->pdo;
    }

    public function fetchAll(string $query, $_method, array $params = [])
    {
        $t = microtime(true);
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affectedRows = $prepared->rowCount();
        $this->log[] = [$query, microtime(true) - $t, $_method, $affectedRows];

        return $data;
    }

    public function fetchOne(string $query, $_method, array $params = [])
    {
        $t = microtime(true);
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affectedRows = $prepared->rowCount();


        $this->log[] = [$query, microtime(true) - $t, $_method, $affectedRows];
        if (!$data) {
            return false;
        }
        return reset($data);
    }

    public function exec(string $query, $_method, array $params = []): int
    {
        $t = microtime(1);
        $pdo = $this->getConnection();
        $prepared = $pdo->prepare($query);

        $ret = $prepared->execute($params);


        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return -1;
        }
        $affectedRows = $prepared->rowCount();

        $this->log[] = [$query, microtime(1) - $t, $_method, $affectedRows];

        return $affectedRows;
    }

    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    public function getLogHTML()
    {
        if (!$this->log) {
            return '';
        }
        $res = '';
        foreach ($this->log as $elem) {
            $res = $elem[1] . ': ' . $elem[0] . ' (' . $elem[2] . ') [' . $elem[3] . ']' . "\n";
        }
        return '<pre>' . $res . '</pre>';
    }


}