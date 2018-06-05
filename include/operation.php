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
