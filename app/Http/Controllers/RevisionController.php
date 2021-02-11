<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function alive(): JsonResponse
    {
        return response()
            ->json('It\'s alive !');
    }

    public function iAmAuthorized(): JsonResponse
    {
        return response()
            ->json('It\'s good, you can pass bro !');
    }
}
