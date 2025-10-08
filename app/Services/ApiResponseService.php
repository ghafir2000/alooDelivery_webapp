<?php


namespace Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{

    public static function successResponse($data = [], $msg = null, $code = 200): JsonResponse
    {
        return response()->json([
            'sucess' =>true,
            'message' => $msg ?? trans('response.success'),
            'data'    => $data,
        ],
            $code);
    }


    public static function errorResponse($msg = null, $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'message' => trans('response.wrong'),
            'errors'  => $errors ?? [$msg],
        ],
            $code);
    }


    /*****************************************************************************************/

    public static function validateResponse($errors, $code = 422): JsonResponse
    {
        return static::errorResponse(
            msg: $msg ?? trans('response.validation_error'),
            code: $code,
            errors: $errors,
        );
    }

    public static function successMsgResponse($msg = null, $code = 200)
    {
        return static::successResponse(
            msg: $msg ?? trans('response.success'),
            code: $code
        );
    }

    public static function errorMsgResponse($msg = null, $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return static::errorResponse(
            msg: $msg,
            code: $code
        );
    }

    public static function deletedResponse($msg = null, $code = 200): JsonResponse
    {
        return static::successResponse(
            msg: $msg ?? trans('response.deleted'),
            code: $code
        );
    }

    public static function createdResponse($data = [], $msg = null, $code = Response::HTTP_CREATED): JsonResponse
    {
        return static::successResponse(
            data: $data,
            msg: $msg ?? trans('response.created'),
            code: $code
        );
    }

    public static function successWithPagination($data = [], LengthAwarePaginator $paginator = null, $msg = null, $code = Response::HTTP_OK): JsonResponse
    {
        $calculatedPaginator = new LengthAwarePaginator(
            $data,
            $paginator->total(), // Total number of items
            $paginator->perPage(),
            $paginator->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()] // Path and query for pagination links
        );
        return static::successResponse(
            data: $calculatedPaginator,
            msg: $msg ?? trans('response.created'),
            code: $code
        );
    }

    public
    static function updatedResponse($data = [], $msg = null, $code = Response::HTTP_CREATED): JsonResponse
    {
        return static::successResponse(
            data: $data,
            msg: $msg ?? trans('response.updated'),
            code: $code
        );
    }

    public
    static function notFoundResponse($msg = null, $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return static::errorResponse(
            msg: trans('response.not_found'),
            code: $code,
            errors: !is_array($msg) ? [$msg] : $msg,
        );
    }

    public
    static function unauthorizedResponse($msg = null, $code = Response::HTTP_UNAUTHORIZED): JsonResponse
    {
        return static::errorResponse(
            msg: $msg ?? trans('response.unauthorized'),
            code: $code
        );
    }


}
