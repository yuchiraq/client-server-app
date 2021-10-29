<?php

$database = $_GET["database"];

if (!$database) {
	echo "{'error':'Empty parameter', 'code':0}";
} else {    
	if (!strcmp($database, "users_main") || !strcmp($database, "users_university") || !strcmp($database, "group_main") || !strcmp($database, "ms_3")) {
        $select_db = "SELECT * FROM " . $database . ";";
        $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');
        $data = $dbh->prepare($select_db);
        $data->execute();
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
        print_r($data);
        return json_encode($data);
	} else {
		echo "{'error':'The table is not exist', 'code':1}";
	}
}
?>
