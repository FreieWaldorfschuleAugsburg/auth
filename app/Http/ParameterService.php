<?php

namespace App\Http;

use App\models\RequestParameters;
use Illuminate\Http\Request;

class ParameterService
{
    public function getRequestParameters(Request $request)
    {
        return [
            'client_id' => $request->input('client_id'),
            'redirect_uri' => $request->input('redirect_uri'),
            'scope' => $request->input('scope'),
            'response_type' => $request->input('response_type'),
            'state' => $request->input('state')
        ];
    }

}
