<?php
namespace App\Controller;

use App\Model\AdministrateurModel;
use App\View\AdministrateurView;
use App\Database;


class AdministrateurController 
{
    private AdministratuerModel $administrateurModel;
    private AdministrateurView $administrateurView;

    public function __construct()
    {
        $this->administrateurModel = new AdministrateurModel();
        $this->administrateurView = new AdministrateurView();
    }
    
}