<?php

namespace App\server;

use App\Exceptions\InvalidParameterException;

class ParameterHelper
{
    /**
     * @throws InvalidParameterException
     */
    public static function validateParameters(array $previouslyProvidedParameters, array $providedParameters): bool

    {
        foreach ($providedParameters as $providedParameterKey => $providedParameterValue) {
            if (!in_array($providedParameterValue, $previouslyProvidedParameters) || $providedParameterValue !== $previouslyProvidedParameters[$providedParameterKey]) {
                throw new InvalidParameterException("Parameter $providedParameterValue did not match previously provided parameter $previouslyProvidedParameters[$providedParameterKey]!");
            }
        }

        return true;
    }


}
