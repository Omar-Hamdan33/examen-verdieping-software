<?php

class Vulkaan extends BaseController
{
    private $VulkaanModel;

    public function __construct()
    {
         $this->VulkaanModel = $this->model('VulkaanModel');
    }

    public function index()
    {
       /**
        * Hier halen we alle Vulkaan op uit de database
        */
       $result = $this->VulkaanModel->getAllVulkaan();
       
       /**
        * Het $data-array geeft informatie mee aan de view-pagina
        */
       $data = [
            'title' => 'Overzicht Vulkaan',
            'Vulkaan' => $result
       ];

         /**
          * Met de view-method uit de BaseController-class wordt de view
          * aangeroepen met de informatie uit het $data-array
          */
       $this->view('Vulkaan/index', $data); 
    }

}