<?php

class Horloges extends BaseController
{
    private $horlogesModel;

    public function __construct()
    {
         $this->horlogesModel = $this->model('HorlogesModel');
    }

    public function index()
    {
       /**
        * Hier halen we alle smartphones op uit de database
        */
       $result = $this->horlogesModel->getAllHorloges();
       
       /**
        * Het $data-array geeft informatie mee aan de view-pagina
        */
       $data = [
            'title' => 'Mooiste horloges',
            'horloges' => $result
       ];

         /**
          * Met de view-method uit de BaseController-class wordt de view
          * aangeroepen met de informatie uit het $data-array
          */
       $this->view('horloges/index', $data); 
    }

}