<?php

class VulkaanModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function getAllVulkaan()
    {
        $sql = 'SELECT  VUL.Naam
                        ,VUL.Hoogte
                        ,VUL.Land
                        ,VUL.JaarLaatsteUitbarsting
                        ,VUL.AantalSlachtoffers

                FROM   Vulkaan as VUL

                ORDER BY VUL.AantalSlachtoffers ASC';

        $this->db->query($sql);

        return $this->db->resultSet();
    }

}