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
        $select_db = "SELECT * FROM users_university WHERE user_id = '" . $login . "';";              // берем остальные данные
        $data_university = $dbh->prepare($select_db);
        $data_university->execute();
        $data_university = $data_university->fetchAll(PDO::FETCH_ASSOC);
        $data_university = print_r($data_university, true); 
        $data_university_massiv = explode(" ", $data_university);
        $group = $data_university_massiv[56];
        $group = substr_replace($group,"",-1); 
        
        $select_db = "SELECT * FROM group_main WHERE group_name = '" . $group . "';";              // берем остальные данные
        $data_group = $dbh->prepare($select_db);
        $data_group->execute();
        $data_group = $data_group->fetchAll(PDO::FETCH_ASSOC);
        $data_group = print_r($data_group, true); 
        $data_group_massiv = explode(" ", $data_group);

        $i = 112;                                                                                   //текст группы
        $text = "";
        while($data_group_massiv[$i] != ""){
            $text = $text . " " . $data_group_massiv[$i];
            $i++;
        }

        $select_db = "SELECT subgroup FROM users_university WHERE user_id = " . $login . ";";              // берем подгруппу
        $subgroup = $dbh->prepare($select_db);
        $subgroup->execute();
        $subgroup = $subgroup->fetchAll(PDO::FETCH_ASSOC);
        $subgroup = print_r($subgroup, true);
        $subgroup = explode(" ", $subgroup);
        $subgroup = $subgroup[28];

        $select_db = "SELECT user_rank FROM users_university WHERE user_id = " . $login . ";";              // берем rank
        $rank = $dbh->prepare($select_db);
        $rank->execute();
        $rank = $rank->fetchAll(PDO::FETCH_ASSOC);
        $rank = print_r($rank, true);
        $rank = explode(" ", $rank);
        $rank = $rank[28];

        $data = array(
            'user_id' => $data_user_massiv[42],
            'name' => $data_user_massiv[56],
            'lname' => $data_user_massiv[70],
            'email' => $data_user_massiv[84],
            'phone' => $data_user_massiv[98],
            'group' => $group,
            'subgroup' => $subgroup,
            'faculty' => $data_group_massiv[70],
            'specialization' => $data_group_massiv[84],
            'course' => $data_group_massiv[98],
            'group_lider' => $data_group_massiv[56],
            'user_rank' => $rank,
            'group_text' => $text 
        );
        echo json_encode($data);
    }else{
        print_r("incorrect_password");
    }
    

?>