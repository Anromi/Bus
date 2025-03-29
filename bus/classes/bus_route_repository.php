<?php

require_once '../database/bd_connection.php';


class BusRouteRepository extends DB {

    /**
     * Получаем последнюю остановку маршрута
     */
    function getFinalStop($route_id) {
        $arr = [];
        $res = '';
        if (!empty($route_id)) {
            $SQL = "SELECT route_link.stop_sequence, stops.name FROM route_link";
            $SQL = $SQL . " INNER JOIN stops ON(route_link.stop_id=stops.id AND route_id=" . $route_id . ")";
            //$SQL = "SELECT * FROM route_link WHERE stop_sequence = (SELECT MAX(stop_sequence) FROM route_link) AND route_id=".$route_id;
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
        }
        if (count($arr)>0) {
            $keys = array_keys($arr);
            $key_max = max($keys);

            $res = $arr[$key_max]['name'];
        }
        //$this->log($res,'getStops_arr');
        return $res;
    }

    /**
     * Получаем маршруты, в которых есть наши заданные остановки
     */
    function getRouteLink($from, $to) {
        $arr = [];
        if (!empty($from) && !empty($to)) {
            $SQL = "SELECT ";
            $SQL = $SQL . "route_link.stop_sequence, route_link.id as main_id,
            routes.name as route_name, routes.id as routes_id,
        
            stops.name as stop_name, stops.id as stops_id"; //directions.direction, directions.id as directions_id,
            $SQL = $SQL . " FROM " . "route_link";

            $SQL = $SQL . " INNER JOIN routes ON(route_link.route_id=routes.id)";
            //$SQL = $SQL . " INNER JOIN directions ON(route_link.direction_id=directions.id)";
            $SQL = $SQL . " INNER JOIN stops ON(route_link.stop_id=stops.id AND (route_link.stop_id=" . $from . " OR route_link.stop_id=" . $to . "))";
            $this->query($SQL);
            //$this->log($SQL,'getRouteLink_$arr');
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
        }
        //$this->log($arr,'getRouteLink_$arr');
        return $arr;
    }

    /**
     * Получаем время относящееся к остановке
     */
    function getSchedules($route_link_id) {
        $arr = [];
        if (!empty($route_link_id)) {
            $SQL = "SELECT * FROM schedules WHERE route_link_id=" . $route_link_id;
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
        }
        //$this->log($arr,'getSchedules_arr');
        return $arr;
    }

    /**
     * Поиск автобусов (маршрутов) с болижайшим временем
     */
    function findBus($from, $to, $currentTime) {

        $routes = $this->getRouteLink($from, $to);

        $list_routes_link = []; // id связей, содержат исх. и конеч. остановку в заданном направлении
        for ($i = 0; $i < count($routes); ++$i) {
            $id = $routes[$i]['main_id'];
            $route_id = $routes[$i]['routes_id'];
            $stop_id = $routes[$i]['stops_id'];
            $stop_sequence = $routes[$i]['stop_sequence'];
            if ($from === $stop_id) {
                for ($j = 0; $j < count($routes); ++$j) {
                    if ($route_id === $routes[$j]['routes_id'] && $id !==  $routes[$j]['main_id']) { // если общий маршурут
                        if ($stop_sequence < $routes[$j]['stop_sequence']) { // если порядок соблюдается
                            $list_routes_link[] = [
                                'main_id' => $id,
                                'stop_name_from' => $routes[$i]['stop_name'],
                                'stop_name_to' => $routes[$j]['stop_name'],
                                'route_name' => $routes[$i]['route_name'],
                                'route_id' => $routes[$i]['routes_id'],
                            ];
                        }
                    }
                }
            }
        }
        //$this->log($list_routes_link,'getSchedules_$list_routes_link_id');

        // Сбор ближайших к текущему времени
        $list_routes_time = [];
        $final_stop = '';
        for ($i = 0; $i < count($list_routes_link); ++$i) {
            $list_routes_time[] = [
                'list_time' => $this->getSchedules($list_routes_link[$i]['main_id']),
                'list_routes_link' => $list_routes_link[$i],
            ];
            $final_stop = $this->getFinalStop($list_routes_link[$i]['route_id']);
        }
        //$this->log($list_routes_time,'getSchedules_$list_routes_time');

        $res_route = [];
        $from_name = "";
        $to_name = "";
        foreach ($list_routes_time as $routes) {
            $next_departure_time = [];
            foreach ($routes['list_time'] as $schedule) {
                // отсеивание лишнех
                if (strtotime($schedule['departure_time']) >= strtotime($currentTime)) {
                    $next_departure_time[] = $schedule['departure_time'];
                    if (count($next_departure_time) == 3) break;
                }
            }

            $from_name = $routes['list_routes_link']['stop_name_from'];
            $to_name = $routes['list_routes_link']['stop_name_to'];
            $route = "Маршрут " . $routes['list_routes_link']['route_name'] . " в сторону " .  $final_stop;
            $next_arrivals = 'Автобусов уже больше не будет';

            // если есть время прибытия на остановку
            if (!empty($next_departure_time)) {
                $next_arrivals = $next_departure_time;
            }
            $res_route[] = [
                'route' => $route,
                'next_arrivals' => $next_arrivals,
            ];
        }
        $res = [
            'from' => $from_name,
            'to' => $to_name,
            'buses' => $res_route,
        ];
        $this->log($res,'bus-route-res');
        return [
            'from' => $from_name,
            'to' => $to_name,
            'buses' => $res_route,
        ];
    }
}

