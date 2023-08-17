<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Traits\Responsable;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, Responsable;
}
