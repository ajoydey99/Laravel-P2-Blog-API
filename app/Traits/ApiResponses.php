<?php
namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponses
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200,
        ?string $token = null
    ) {
        $response = [
            'status'  => true,
            'message' => $message,
        ];

        if ($token) {
            $response['token'] = $token;
        }

        // fetch all data collection with pagination
        if ($data && ($data instanceof ResourceCollection)) {
            return $data->additional($response)
                ->response()
                ->setStatusCode($statusCode);
        }

        // get single data
        if (! is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(
        mixed $errors = null,
        string $message = 'Something went wrong',
        int $statusCode = 422
    ) {
        $response = [
            'status'  => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function permissionResponse(
        string $message = 'forbidden',
        int $statusCode = 403
    ) {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], $statusCode);
    }
}
