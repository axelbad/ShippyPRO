<?php 
require 'data.php';
require 'function.php';

// Many other validaton and check could be done here
// ie: departure and arrival from same location
$id_departure = filter_var($_POST['id_departure'], FILTER_SANITIZE_NUMBER_INT);
$id_arrival = filter_var($_POST['id_arrival'], FILTER_SANITIZE_NUMBER_INT);

// Call main function inside function.php
$paths = main($airports, $flights, $id_departure, $id_arrival);

$results['status'] = 'ok';
$results['final_path'] = $paths;

echo json_encode($results);