<?php

// how to...


// import needed classes:
use hocine\protun\core\db;
use hocine\protun\core\User;

require_once __DIR__."/vendor/autoload.php";

// connect to the database
    db::addConnection(
        [
            'host'    => 'localhost',
            'user'    => 'root',
            'pass'    => '',
            'db'      => 'db',
            'charset' => 'utf8'
        ]
    );


// database object:
$db = new db();

// User object with dependency injection:
$user = new User( $db );

// the user table name:
$user->setUserTable("users");

// the id column
$user->setIdColumn("id");

// the unique column (( يستخدم في منع المستخدم من أن يسجل مرتين بنفس الاسم أو الايميل  ))
$user->setUniqueColumn("username");

// results order by column (( for example: id, username, created_at ...etc ))
$user->setOrderByColumn('id');


// user login: return --> true/false
$is_logged_in = $user->login("fermax", "user_password");



// delete a user by id:
$user->remove(1);
/*
 * you can delete a user by email; flow this two steps :
 *                                 -1 $user->setIdColumn("email")
 *                                 -2 $user->remove("user@example.com");
 * */



/*
 * create a user:
 *
 */
$user->create([
                            'username'    => 'Alhocine',
                            'password'    => '123456',
                            'email'       => 'user@gmail.com',
                            'fullName'    => 'Hocine Ferradj',
                            'secret'      => 'What is my favorite number?',
                            'answer'      => '4'
]);


// edit a user:
$query = $user->edit(7, ["email" => "user@example.com"]);


// error reporting:
if( count( $db->getErrors())  )
{
    echo "<pre style='background-color: red; color: white; font-size: 20px;'>";
    print_r( $db->getErrors() );
    echo "</pre>";
}
