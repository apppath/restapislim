<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
require_once '../include/operation.php';

//config to show errors
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);


//create  new users
$app->post('/register', function (Request $request, Response $response) {
    if (ParametersAvailable(array('full_name' , 'email_address', 'password' , 'address'))) {
        $requestData = $request->getParsedBody();
        $full_name = $requestData['full_name'];
        $email_address = $requestData['email_address'];
        $password = $requestData['password'];
        $address = $requestData['address'];
        $db = new Operation();
        $responseData = array();
        $result = $db->createUser($full_name , $email_address , $password , $address);

        if ($result == CREATED_CODE) {
            $responseData['error'] = false;
            $responseData['message'] = 'craete new user successfully';
        }
        elseif ($result == FAILED_CODE) {
            $responseData['error'] = true;
            $responseData['message'] = 'error occurred';
        }
         elseif ($result == EXIST_CODE) {
            $responseData['error'] = true;
            $responseData['message'] = 'email already exist, login';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});


  //getting all users
  $app->get('/users', function (Request $request, Response $response) {
      $db = new Operation();
      $users = $db->getUsers();
      $response->getBody()->write(json_encode(array("users" => $users)));
  });


//function to check parameters
function ParametersAvailable($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echo json_encode($response);
        return false;
    }
    return true;
}

// Run app
$app->run();
