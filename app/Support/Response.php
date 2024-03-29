<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class Response
 * @package App\Support
 */
class Response
{
    /**
     * HTTP Response.
     *
     * @var ResponseFactory
     */
    private $response;

    /**
     * API transformer helper.
     *
     * @var Transform
     */
    public $transform;

    /**
     * HTTP status code.
     *
     * @var int
     */
    private $statusCode = HttpResponse::HTTP_OK;

    /**
     * Create a new class instance.
     *
     * @param $response
     * @param $transform
     */
    public function __construct(ResponseFactory $response, Transform $transform)
    {
        $this->response = $response;
        $this->transform = $transform;
    }

    /**
     * Return a 201 response with the given created resource.
     *
     * @param null $resource
     * @param TransformerAbstract|null $transformer
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function withCreated($resource = null, TransformerAbstract $transformer = null)
    {
        $this->statusCode = HttpResponse::HTTP_CREATED;

        if (is_null($resource)) {
            return $this->json();
        }

        return $this->item($resource, $transformer);
    }

    /**
     * Make a 200 with data response.
     *
     * @param $data
     * @return JsonResponse
     */
    public function withOk($data)
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_OK
        )->json([
            "success" => true,
            "data" => $data
        ]);
    }

    /**
     * Make a 200 with message response.
     *
     * @param $message
     * @return JsonResponse
     */
    public function withMessage($message)
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_OK
        )->json([
            'message' => $message
        ]);
    }

    /**
     * Make a 204 no content response.
     *
     * @return JsonResponse
     */
    public function withNoContent()
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NO_CONTENT
        )->json();
    }

    /**
     * Make a 400 'Bad Request' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withBadRequest($message = 'Bad Request')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_BAD_REQUEST
        )->withError($message);
    }

    /**
     * Make a 401 'Unauthorized' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNAUTHORIZED
        )->withError($message);
    }

    /**
     * Make a 402 'Payment required' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withPaymentRequired($message = 'Payment required')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_PAYMENT_REQUIRED
        )->json($message);
    }

    /**
     * Make a 403 'Forbidden' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_FORBIDDEN
        )->withError($message);
    }

    /**
     * Make a 404 'Not Found' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NOT_FOUND
        )->withError($message);
    }

    /**
     * Make a 429 'Too Many Requests' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withTooManyRequests($message = 'Too Many Requests')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_TOO_MANY_REQUESTS
        )->withError($message);
    }

    /**
     * Make a 500 'Internal Server Error' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withInternalServer($message = 'Internal Server Error')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        )->withError($message);
    }

    /**
     * Make an error response.
     *
     * @param $message
     *
     * @return JsonResponse
     */
    public function withError($message)
    {
        return $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    public function withSucess($message)
    {
        return $this->setStatusCode(HttpResponse::HTTP_OK)->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Make a 422 'UNPROCESSABLE ENTITY' response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function withUnprocessableEntity($message = 'UNPROCESSABLE_ENTITY')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNPROCESSABLE_ENTITY
        )->withError($message);
    }

    /**
     * Make a JSON response with the transformed items.
     *
     * @param $item
     * @param TransformerAbstract|null $transformer
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function item($item, TransformerAbstract $transformer = null)
    {
        return $this->json(
            $this->transform->item($item, $transformer)
        );
    }

    /**
     * Make a JSON response.
     *
     * @param $items
     * @param TransformerAbstract|null $transformer
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function collection($items, TransformerAbstract $transformer = null)
    {
        return $this->json(
            $this->transform->collection($items, $transformer)
        );
    }

    /**
     * Make a JSON response.
     *
     * @param $items
     * @param TransformerAbstract|null $transformer
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function collection_sale($items, TransformerAbstract $transformer = null, $total_profit = 0)
    {
        return $this->json(
            $this->transform->collection_sale($items, $transformer, $total_profit)
        );
    }

    /**
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function json($data = [], array $headers = [])
    {
        return $this->response->json($data, $this->statusCode, $headers);
    }

    /**
     * Set HTTP status code.
     *
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Gets the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
