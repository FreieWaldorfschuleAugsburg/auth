<?php

namespace App\helpers\authentication;


use App\Exceptions\InvalidAuthorizationRequest;
use App\Exceptions\InvalidParameterException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormHelper
{

    /**
     * @throws InvalidParameterException
     */
    public static function parseParameters(Request $request)
    {
        $params = [];
        $requiredParams = config('requiredParams.oauth2');
        foreach ($requiredParams as $requiredParam) {
            $queries = $request->query();
            if (!key_exists($requiredParam, $queries)) {
                throw new InvalidParameterException($requiredParam, 403);
            }
        }
        foreach ($request->query() as $key => $value) {
            if (in_array($key, $requiredParams)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidAuthorizationRequest
     */
    public static function verifyClientData(Request $request): array
    {
        $verifiedParams = [];
        $requiredParams = config('requiredParams.oauth2');
        foreach ($requiredParams as $key => $requiredParam) {
            if (Crypt::decryptString($request->input($requiredParam)) === session()->get($requiredParam)) {
                $verifiedParams[$requiredParam] = Crypt::decryptString($request->input($requiredParam));
            } else throw new InvalidAuthorizationRequest('The delivered params did not match');
        }
        return $verifiedParams;
    }

    /**
     * @throws InvalidParameterException
     */
    public static function parseFormData(Request $request)
    {
        $requiredFields = config('requiredParams.getAccessToken');
        foreach ($requiredFields as $requiredField) {
            if (!$request->input($requiredField)) {
                throw new InvalidParameterException('invalid form data');
            }
        }



    }


}
