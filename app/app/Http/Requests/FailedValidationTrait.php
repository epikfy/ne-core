<?php


namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

trait FailedValidationTrait
{
    /**
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = implode(" ",$validator->messages()->all());
        throw new HttpResponseException(response()->json(['success' => false, 'message' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
