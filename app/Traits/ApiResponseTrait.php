<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

trait ApiResponseTrait
{
    protected function success(mixed $data = null, string $message = 'Success', int $code = HttpResponse::HTTP_OK): JsonResponse
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $code);
    }

    protected function error(string $message, int $code = HttpResponse::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        $payload = ['status' => 'error', 'message' => $message];
        if ($errors) $payload['errors'] = $errors;
        return response()->json($payload, $code);
    }

    protected function created(mixed $data, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, HttpResponse::HTTP_CREATED);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, HttpResponse::HTTP_NO_CONTENT);
    }
}
