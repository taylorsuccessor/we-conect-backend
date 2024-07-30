<?php

namespace App\Http\Controllers\Swagger;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Response as ResponseFacade;
use L5Swagger\Http\Controllers\SwaggerController;

class ConfigerSwaggerController extends SwaggerController
{
    /**
     * Display Swagger API page.
     *
     * @param  Request  $request
     * @return Response
     */
    public function api(Request $request)
    {
        request()->merge([
            'documentation' => 'default',
            'config' => config('l5-swagger.defaults')
        ]);

        return parent::api($request);
    }
}
