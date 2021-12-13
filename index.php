<?php

$database = $_GET["database"];

if (!$database) {
	print_r("error: Empty parameter");
} else {    
	$select_db = "SELECT * FROM " . $database . ";";
        $dbh = new PDO('pgsql:host=localhost port=5432 user=chiraq dbname=chiraq password=0812');
        $data = $dbh->prepare($select_db);
        $data->execute();
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
        print_r($data);
        //return json_encode($data);
}
?>
