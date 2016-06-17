<?php

namespace App\Http\Controllers\app;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;

class RedirectController extends Controller
{
    public function betacode($betacode) {
        // TODO make this work better in chrome
        // Test in Safari
        return Redirect::away("Kidgifting://kidgifting/betacode/$betacode");
    }
}
