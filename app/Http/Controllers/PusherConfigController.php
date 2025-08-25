<?php

namespace App\Http\Controllers;

use App\Http\Resources\PusherConfigResource;
use Illuminate\Http\Request;

class PusherConfigController extends Controller
{
    public function __invoke(Request $request)
    {
        return new PusherConfigResource(config('broadcasting.connections.pusher'));
    }
}
