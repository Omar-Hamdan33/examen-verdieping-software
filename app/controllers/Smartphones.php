<?php

class Smartphones extends BaseController
{
    private $smartphonesModel;

    public function __construct()
    {
         $this->smartphonesModel = $this->model('SmartphonesModel');
    }

    public function index()
    {
       /**
        * Hier halen we alle smartphones op uit de database
        */
       $result = $this->smartphonesModel->getAllSmartphones();
       
       /**
        * Het $data-array geeft informatie mee aan de view-pagina
        */
       $data = [
            'title' => 'Overzicht smartphones',
            'smartphones' => $result
       ];

         /**
          * Met de view-method uit de BaseController-class wordt de view
          * aangeroepen met de informatie uit het $data-array
          */
       $this->view('smartphones/index', $data); 
    }

}