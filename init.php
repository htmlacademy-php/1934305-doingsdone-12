<?php

$data = [];

if (($handle = fopen("info.csv", "r")) !== FALSE) {
    $data = fgetcsv($handle, 1000, ",");
    fclose($handle);
}

$db = ["host" => $data[0], "user" => $data[1], "password" => $data[2], "database" => $data[3]];
?>
