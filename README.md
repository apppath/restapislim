# Slim Framework 3 Skeleton Application

Use this skeleton application to quickly setup and start working on a new Slim Framework 3 application. This application uses the latest Slim 3 with the PHP-View template renderer. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

```php
php composer.phar create-project slim/slim-skeleton [my-app-name]
```

Replace [my-app-name] with the desired directory name for your new application. You'll want to:
. Point your virtual host document root to your new application's public/ directory.
. Ensure logs/ is web writeable.

To run the application in development, you can run these commands

```php

cd [my-app-name]
php composer.phar start

```
Run this command in the application directory to run the test suite

```php
php composer.phar test
```

### Api Files include folder
* constant.php
* connection.php
* operation.php

#### constant.php file
* constant database variable define
* constant api code error or success define 

```php

<?php

define('DATABASE_USERNAME', 'root');
define('DATABASE_PASSWORD', '');
define('DATABASE_HOST', 'localhost');
define('DATABASE_NAME', 'slimrestapis');

define('CREATED_CODE', 101);
define('EXIST_CODE', 102);
define('FAILED_CODE', 103);
?>

```
#### connection.php  file
* connect into database
* include constant file and connect to database

```php

<?php

class Connection
{
    //variable of database link
    private $connection;

    //constructor
    function __construct()
    {

    }

    //This method will connect to the database
    function connect()
    {
        //constants.php file include
        include_once dirname(__FILE__) . '/constant.php';

        //connecting to mysql database
        $this->connection = new mysqli(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

        //error occured while connecting
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            return null;
        }

        //returning the connection
        return $this->connection;
    }

}


```

#### operation.php file

* create new users opertation
* list of all user operation 

```php

<?php

class Operation
{
    private $connection;

    function __construct()
    {
        require_once dirname(__FILE__) . '/connection.php';
        $dbconn = new Connection();
        $this->connection = $dbconn->connect();
    }

    //create new users
    function createUser($full_name , $email_address , $pass , $address)
    {
        if (!$this->isUserExist($email_address)) {
            $password = md5($pass);
            $stmt = $this->connection->prepare("INSERT INTO register(full_name , email_address, password , address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name , $email_address , $password , $address);
            if ($stmt->execute())
                return CREATED_CODE;
            return FAILED_CODE;
        }
        return EXIST_CODE;
    }


    //get all users
    function getUsers(){
        $stmt = $this->connection->prepare("SELECT user_id , full_name , email_address , password , address FROM register");
        $stmt->execute();
        $stmt->bind_result($user_id, $full_name , $email_address , $password , $address);
        $users = array();
        while($stmt->fetch()){
            $temp = array();
            $temp['user_id'] = $user_id;
            $temp['full_name'] = $full_name;
            $temp['email_address'] = $email_address;
            $temp['address'] = $address;
            array_push($users, $temp);
        }
        return $users;
    }


    //check if email exist or not
    function isUserExist($email_address)
    {
        $stmt = $this->connection->prepare("SELECT user_id FROM register WHERE email_address = ?");
        $stmt->bind_param("s", $email_address);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows >0;
    }

  } //end of php class


```

### v3 folder file 

* index.php 

#### index.php file
* create new user
* list of all user

```php

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


```

# Next android app use volley library 
