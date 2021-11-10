<?php

    $login = $_GET["login"];
    $password = $_GET["password"];
    $name = $_GET["name"];
    $lname = $_GET["lname"];
    $email = $_GET["email"];
    $phone = $_GET["phone"];

    if(!$login || !$password || !$name || !$lname || !$email || !$phone){
        print_r("empty parameter");
        return 0;
    }
    
    $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');

    $select_db = "SELECT * FROM users_main WHERE user_id = '" . $login . "';";

    $data_user = $dbh->prepare($select_db);
    $data_user->execute();
    $data_user = $data_user->fetchAll(PDO::FETCH_ASSOC);

    if($data_user){
        print_r("exists");
        return 0;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $insert_db = "INSERT INTO users_main (user_id, fname, lname, email, phone, password) VALUES('" . $login . "', '" . $name . "', '" . $lname . "', '" . $email . "', '" . $phone . "', '" . $hash . "');";
    $answer = $dbh->prepare($insert_db);
    $answer->execute();
    
    print_r("registration_done");
    return 1;
?>