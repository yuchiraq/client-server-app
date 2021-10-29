<?php
    $group = $_GET["group"];
    if (!$group) {
        echo "{'error':'Empty parameter', 'code':0}";
    } else {        
        $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');     // подключение к БД
        $database_group = "SELECT * FROM users_university WHERE group_name LIKE '" . $group . "' ;";   // Выбираем user_id тех, кто в группе
        $data_user = $dbh->prepare($database_group);
        $data_user->execute();
        $data_user = $data_user->fetchAll(PDO::FETCH_ASSOC);     
        $data_user = print_r($data_user, true);                     // В data_user выборка челиков
        $data_user_massiv = explode(" ", $data_user);
        for($i = 42, $j = 0; $i <= count($data_user_massiv) ; $i = $i + 78, $j++){
            $user_id = $data_user_massiv[$i];
            $database_final = "SELECT * FROM users_main WHERE user_id = " . $user_id . " ;";
            $data = $dbh->prepare($database_final);
            $data->execute();
            $data = $data->fetchAll(PDO::FETCH_ASSOC);
            $data_exit[$j] = print_r($data, true);
        }
        /*print_r($data_user_massiv[42]);
        print_r($data_user_massiv[120]);           // 42 + 78      user_id
        print_r($data_user_massiv[198]);*/
        print_r(json_encode($data_exit));
        return json_encode($data_exit);  
    }
?>