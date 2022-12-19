<?php

namespace App\Http;

use App\Exceptions\InvalidClientException;
use App\Exceptions\InvalidParameterException;
use App\models\AuthClient;
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

    /**
     * @throws InvalidParameterException
     */
    public function compareParameterArray(array $storedRequestParameters, array $requestParameters): bool

    {
        foreach ($storedRequestParameters as $storedRequestParameterKey => $storedRequestParameterValue) {
            if (!in_array($storedRequestParameterValue, $requestParameters) || $storedRequestParameterValue !== $requestParameters[$storedRequestParameterKey]) {
                session()->flush();
                throw new InvalidParameterException("Previously provided parameter $storedRequestParameterKey did not match provided parameter $requestParameters[$storedRequestParameterKey]!");
            }
        }

        return true;
    }


    /**
     * @throws InvalidParameterException
     */
    public function verifyRequestParameters(array $requestParameters): bool
    {
        $requiredParameters = config('parameters.authorization_request');
        foreach ($requiredParameters as $requiredParameter) {
            if (!$requestParameters[$requiredParameter]) {
                throw new InvalidParameterException("Please provide parameter $requiredParameter!");
            }
        }
        return true;
    }


    /**
     * @throws InvalidClientException
     */
    public function getParametersForConfirm(array $requestParameters): array
    {
        $client = AuthClient::getClient($requestParameters['client_id']);
        if (!$client) {
            throw new InvalidClientException("Client not found");
        }
        $confirmDialogueParameters = config('authorization_request.confirm_dialogue_parameters');
        $parameters = [];
        foreach ($confirmDialogueParameters as $confirmDialogueParameter) {
            try {
                $parameters[$confirmDialogueParameter] = $requestParameters[$confirmDialogueParameter];
            } catch (\Exception $exception) {

            }
        }
        $parameters['client_id'] = $client->client_id;
        return $parameters;
    }


}
