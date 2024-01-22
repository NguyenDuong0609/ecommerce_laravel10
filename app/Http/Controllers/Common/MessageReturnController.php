<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageReturnController extends Controller
{
    public static function convertArrayMessage($arrayMessage)
    {
        $errors = [];
        foreach ($arrayMessage as $k => $val) {
            foreach ($val as $value) {
                $errors[] = $value;
            }
        }
        return array_unique($errors);
    }
}
