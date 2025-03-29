<?php
require_once 'edit_route_repository.php';

// Входные параметры
$route_id = (int)$_GET['route_id']; // id маршрута в котором остановка
$action = $_GET['action']; // действие: add, delete
// если action = add - маркер нужно вводить, он указывает на место куда добавить
// если action=delete - маркер не нужно вводить
$stop_name = $_GET['stop_name']; // name остановки который будем изменять
$marker = (int)$_GET['marker'];
$params = [
    'route_id'=>$route_id,
    'stop_name'=>$stop_name,
    'action'=>$action,
    'marker'=>$marker,
];

$BRR = new EditRouteRepository();
$res_editRoute = $BRR->editRoute($params);

var_dump($res_editRoute);
