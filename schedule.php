<?php

    $user_id = $_GET["user"];

    $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');
    
    $select_db = "SELECT group_name FROM users_university WHERE user_id = '" . $user_id . "';";
    $group = $dbh->prepare($select_db);
    $group->execute();
    $group = $group->fetchAll(PDO::FETCH_ASSOC);
    $group = print_r($group, true);                   
    $group = explode(" ", $group);
    $group = $group[28];
    $group = substr_replace($group,"",-1); 

    $select_db = "SELECT spec, course FROM group_main WHERE group_name = '" . $group . "';";
    $data_group = $dbh->prepare($select_db);
    $data_group->execute();
    $data_group = $data_group->fetchAll(PDO::FETCH_ASSOC);
    $data_group = print_r($data_group, true);                   
    $data_group = explode(" ", $data_group);
    $spec = $data_group[28];
    $spec = substr_replace($spec,"",-1); 
    $course = $data_group[42];
    $course = substr_replace($course,"",-1); 
    
    readfile($spec . $course . ".json");
?>

