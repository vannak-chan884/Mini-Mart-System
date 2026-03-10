<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    // ── Success response ─────────────────────────────────────────────
    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    // ── Success with pagination meta ─────────────────────────────────
    protected function paginated($paginator, string $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ], 200);
    }

    // ── Created response (201) ───────────────────────────────────────
    protected function created($data = null, string $message = 'Created successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], 201);
    }

    // ── Error response ───────────────────────────────────────────────
    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    // ── Not found response (404) ─────────────────────────────────────
    protected function notFound(string $message = 'Resource not found')
    {
        return $this->error($message, 404);
    }

    // ── Forbidden response (403) ─────────────────────────────────────
    protected function forbidden(string $message = 'Permission denied')
    {
        return $this->error($message, 403);
    }

    // ── Unauthorized response (401) ──────────────────────────────────
    protected function unauthorized(string $message = 'Unauthenticated')
    {
        return $this->error($message, 401);
    }

    // ── Validation error response (422) ─────────────────────────────
    protected function validationError($errors, string $message = 'Validation failed')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}