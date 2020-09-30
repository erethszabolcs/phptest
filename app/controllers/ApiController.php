<?php
declare(strict_types=1);

use Models\Users;
use Models\Products;
use Models\Ratings;
use Phalcon\Paginator\Adapter\NativeArray;
use Phalcon\Http\Response;

class ApiController extends \Phalcon\Mvc\Controller
{

    protected $user;
    protected $response;

    public function initialize()
    {
        // Setting $this->user according to our pseudo-authentication
        $this->user = Users::findFirst((int) $this->request->getHeader('userID'));
        $this->response = new Response();
    }

    public function productListAction()
    {
        $parameters = null;

        // Get 'code' parameter if there is one, else $productCode = null
        $productCode = $this->request->get(
            $name = 'code',
            $filters = 'string',
            $defaultValue = null,
            $notAllowEmpty = false,
            $noRecursive = false
        );

        // If there was no 'code' parameter there will be no filtering parameters,
        // meaning that we will get all the products
        if($productCode) {
            $parameters = [
                'code = "' . $productCode . '"'
            ];
        }

        // $currentPage is for pagination
        $currentPage = $this->request->getQuery('page', 'int');

        // Set up paginator
        $paginator   = new NativeArray(
            [
                "data"  => Products::find($parameters)->toArray(),
                "limit" => 2,
                "page"  => $currentPage,
            ]
        );

        $paginate = $paginator->paginate();
        
        $this->response->setStatusCode(200);
        $this->response->setJsonContent(['message' => 'Products list', 'data' => $paginate]);

        return $this->response;
    }

    public function productDetailAction($product)
    {
        // We get the product details with it's ratings if there was a product according to productID
        if($product) {
            $this->setResponse(200, 'Product Detail', ['product' => $product, 'ratings' => $product->ratings]);
        }
        else {
            $this->setResponse(404, 'Product not found.');
        }

        return $this->response;
    }

    public function productUpdateAction($product)
    {   
        if(!$product) {

            // If there's no product according to productID, we send response with status code 404
            $this->setResponse(404, 'Product not found.');
        }
        elseif(!$this->user->ratedThisProduct($product)) {

            // If the user hasn't rated the product yet, he/she is not allowed to update it
            // as the product doesn't belong to him/her in any way
            $this->setResponse(403, 'You are not allowed to update this product.');
        }
        else {
            $rawBody = $this->request->getJsonRawBody(true);
            if($rawBody) {
                $product->assign($rawBody);
                $result = $product->save();

                // If there was a validation error,
                // we send response with status code 400 along with the messages
                if(!$result && !empty($product->getMessages())) {
                    $messageArray = [];
                    foreach($product->getMessages() as $messageObject) {
                        $messageArray []= $messageObject->getMessage();
                    }
                    $this->setResponse(400, $messageArray);
                }
                else {
                    
                    // If there was no error during product update,
                    // we send response with status code 200 along with the product data
                    $this->setResponse(200, 'Product updated successfully', ['product' => $product]);
                }
            }
            else {
                
                // If the request body was empty, we inform the user about it
                $this->setResponse(200, 'There were no updated attributes');
            }
        }

        return $this->response;
    }

    public function rateAction($product)
    {
        if(!$product) {

            // If there's no product according to productID, we send response with status code 404
            $this->setResponse(404, 'Product not found.');
        }
        elseif($this->user->ratedThisProduct($product)) {

            // If the user has already rated the product,
            // he/she is not allowed to rate it again
            $this->setResponse(200, 'You have already rated this product');
        }
        else {
            
            $rawBody = $this->request->getJsonRawBody(true);
            if($rawBody) {
                $rating = new Ratings();

                $rating->assign(
                    [
                        'user_id' => (int) $this->user->id,
                        'product_id' => (int) $product->id,
                        'value' => isset($rawBody['value']) ? $rawBody['value'] : null,
                    ]
                );

                $result = $rating->save();

                // If there was a validation error,
                // we send response with status code 400 along with the messages
                if(!$result && !empty($rating->getMessages())) {
                    $messageArray = [];
                    foreach($rating->getMessages() as $messageObject) {
                        $messageArray []= $messageObject->getMessage();
                    }
                    $this->setResponse(400, $messageArray);
                }
                else {

                    // If there was no error during product rating,
                    // we send response with status code 201 along with the rating data
                    $this->setResponse(201, 'Rating was successful', ['rating' => $rating]);
                }
            }
            else {

                // If the request body was empty, we inform the user about it
                $this->setResponse(200, 'There were no rating attributes');
            }
        }

        return $this->response;
    }

    // Function the fill the general response with the specific details
    protected function setResponse($statusCode, $message, $data = null)
    {
        $this->response->setStatusCode($statusCode);
        $this->response->setJsonContent(
            [
                'message' => $message,
                'data' => $data,
            ]
        );
    }
}

