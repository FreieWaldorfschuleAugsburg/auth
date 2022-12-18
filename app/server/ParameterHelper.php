<?php

namespace App\server;

use App\Exceptions\InvalidParameterException;
use Illuminate\Support\Facades\Log;

class ParameterHelper
{
    /**
     * @throws InvalidParameterException
     */
    public static function validateParameters(array $previouslyProvidedParameters, array $providedParameters): bool

    {
        Log::debug(print_r($providedParameters, true));
        Log::debug(print_r($previouslyProvidedParameters, true));
        foreach ($previouslyProvidedParameters as $previouslyProvidedParameterKey => $previouslyProvidedParameterValue) {
            if (!in_array($previouslyProvidedParameterValue, $providedParameters) || $previouslyProvidedParameterValue !== $providedParameters[$previouslyProvidedParameterKey]) {
                throw new InvalidParameterException("Previously provided parameter $previouslyProvidedParameterKey did not match provided parameter $providedParameters[$previouslyProvidedParameterKey]!");
            }
        }

        return true;
    }


}
