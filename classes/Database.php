<?php
class Database
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
?>