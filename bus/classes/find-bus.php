<?php
require_once 'bus_route_repository.php';

// Входные параметры
$from = (int)$_GET['from'];
$to = (int)$_GET['to'];
$currentTime = '9:20'; // Текущее время

$BRR = new BusRouteRepository();
$res_findBus = $BRR->findBus($from, $to, $currentTime);

var_dump($res_findBus);
