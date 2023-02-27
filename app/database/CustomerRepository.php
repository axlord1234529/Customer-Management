<?php

namespace App\database;

class CustomerRepository
{
    private $db;
    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getCustomers() {

    }

}