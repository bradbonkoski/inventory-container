<?php

namespace SimpleRoles;


class DB extends \PDO
{
    protected $db;

    private function buildDSN($dbname, $host, $port)
    {
        $dsn = "mysql:dbname=$dbname;host=$host;port=$port";
        return $dsn;
    }

    public function __construct()
    {
        $config = Config::getConfigSection('db');

        $dsn = $this->buildDSN(
            $config['dbname'],
            $config['host'],
            $config['port']
        );

        $this->db = new \PDO($dsn, $config['user'], $config['pass']);
        $this->db->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_ASSOC
        );
    }

    public function getConn()
    {
        return $this->db;
    }
}
