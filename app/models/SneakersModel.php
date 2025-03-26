<?php

class SneakersModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function getAllSneakers()
    {
        $sql = 'SELECT  SNKS.Merk
                       ,SNKS.Model
                       ,SNKS.Type

                FROM   Sneakers as SNKS
                
                ORDER BY SNKS.Merk DESC';

        $this->db->query($sql);

        return $this->db->resultSet();
    }

}