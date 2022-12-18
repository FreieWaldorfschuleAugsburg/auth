<?php

namespace App\models;

class RequestParameters
{
    public string $clientId;
    public string $redirectUri;
    public string $scope;
    public string $responseType;
    public string $state;

    /**
     * @param string $clientId
     * @param string $redirectUri
     * @param string $scope
     * @param string $responseType
     * @param string $state
     */
    public function __construct(string $clientId, string $redirectUri, string $scope, string $responseType, string $state)
    {
        $this->clientId = $clientId;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
        $this->responseType = $responseType;
        $this->state = $state;
    }


}
