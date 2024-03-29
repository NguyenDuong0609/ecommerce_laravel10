<?php

namespace App\Http\Controllers;

use App\Support\Transform;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Manager;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $reponse;

    public function __contruct() {
        $manager = new Manager();

        $this->reponse = new \App\Support\Response(response(), new Transform($manager));
    }
}
