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
