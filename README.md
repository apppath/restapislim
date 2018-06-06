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
