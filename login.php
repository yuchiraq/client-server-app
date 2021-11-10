<?php

    $login = $_GET["login"];
    $password = $_GET["password"];

    if(!$login){
        print_r("error: Empty parameter");
        return 0;
    }

    $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');
    $select_db = "SELECT * FROM users_main WHERE user_id = '" . $login . "';";
    $data_user = $dbh->prepare($select_db);
    $data_user->execute();
    $data_user = $data_user->fetchAll(PDO::FETCH_ASSOC);
    
    if(!$data_user){
        print_r("new_user");
        return 1;
    }else if(!$password){
        print_r("password");
        return 1;
    }
                                                                    //получение хеша
    $data_user = print_r($data_user, true);  
    $data_user_massiv = explode(" ", $data_user);
    $hash = $data_user_massiv[112];
    $hash = substr_replace($hash,"",-1); 

    if(password_verify($password, $hash)){
        print_r($data_user);
    }else{
        print_r("ПОШОЛ НАХУЙ");
    }
    

?>