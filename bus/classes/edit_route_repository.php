<?php

require_once '../database/bd_connection.php';


class EditRouteRepository extends DB {

    function editRoute($params){
        $request = "id маршрута: ".$params['route_id'].". Остановка: ".$params['stop_name'].'. Действие: '.$params['action'].". Порядковый номер: ". $params['marker'];
        $result_start=[];
        $result_end=[];
        //$this->log($params,'editRoute_$params');
        $err = [];
        // проверяем остановку
        $stop_id = $this->ckeckStop($params['stop_name']);

        if (!empty($params['route_id']) && !empty($params['stop_name'])) {
            $result_start = $this->getListRoutStop($params['route_id']);
            // узнаем поряд. номер
            $list = $this->getListRoutStop($params['route_id']);
            $key = array_search($stop_id, array_column($list, 'stop_id'));
            $marker = $list[$key]['stop_sequence'];

            // имеется ли такой маршрут c остановкой
            $list_check = $this->ckeckRouteAndStop($params['route_id'], $stop_id);
            switch ($params['action']) {
                case 'add':
                    if (!$list_check) { // если такой связки нет
                        $max_marker = count($this->getListRoutStop($params['route_id']));
                        if (empty($stop_id)) {
                            $stop_id = $this->addStop($params['stop_name']); // добавить остановку
                        }
                        if (empty($params['marker']) || $params['marker'] < 1 || $params['marker'] > $max_marker) { // добавить остановку в заданное место в машруте
                            $params['marker'] = $max_marker+1;// добавить остановку в конец маршрута
                            $err[] = "В КОНЕЦ add";
                        }
                        $this->actionRouteAndStop($params['route_id'], $stop_id, $params['marker'], $params['action']);
                    } else {
                        if (!empty($stop_id) && $params['marker'] !== $marker) {
                            // случай когда указывается add, который уже есть в маршруте
                            $err[] = 'stop_name сущ. и его связка с маршрутом тоже сущ.';
                            $params['action'] = 'delete';
                            $this->editRoute($params); // чтобы изменить порядковый номер точно такойже остановки
                            $params['action'] = 'add';
                            $this->editRoute($params);
                        } else {
                            $err[] = "stop_id пуст или marker точно такой же";
                        }
                    }
                    break;
                case 'delete':
                    if ($list_check) { // если такая связь есть
                        if (!empty($stop_id)) {
                            // удалить связь и сдвинуть на -1 те что выше
                            $this->actionRouteAndStop($params['route_id'], $stop_id, $marker, $params['action']);
                            // удалить остановку
                            //$this->deleteStop($params['stop_name']);
                        } else {
                            $err[] = 'Такой остановки нет.';
                        }
                    } else {
                        $err[] = 'Такой связи нет.';
                    }
                    break;
                case 'remove':
                    break;
                default:
                    $err[] = 'Параметр action отсутствует.';
                    break;
            }
            $result_end = $this->getListRoutStop($params['route_id']);
        } else {
            $err[] = 'Проверьте не пусты ли параметры: route_id, stop_name.';
        }
        $res = [
            'start'=>$result_start,
            'end'=>$result_end,
        ];
        $result = [
            'request'=>$request,
            'result'=>$res,
            'error'=>$err,
        ];
        $this->log($result, 'edit-route-res');
        return $result;
    }

    /**
     * Добавление в таблицу stop новую остановку
     * Возможно не нужен метод т.к. требовалось редактирование набора остановок в маршруте
     */
    function addStop($stop_name) {
        $SQL = "INSERT INTO stops (name) VALUES ('".$stop_name."')";
        $this->query($SQL);
        $id = $this->lastInsertId();
        //$this->log($id,'addStop_id');

        return $id;
    }

    /**
     * Удаление в таблице stop остановку
     * Возможно не нужен метод т.к. требовалось редактирование набора остановок в маршруте
     */
    function deleteStop($stop_name) {
        $SQL = "DELETE FROM stops name='".$stop_name."')";
        $this->query($SQL);
    }

    /**
     * Проверка нахождения записи в таблице stop и выдача id
     */
    function ckeckStop($stop_name) {
        $arr = [];
        if (!empty($stop_name)) {
            $SQL = "SELECT * FROM stops WHERE stops.name='" . $stop_name . "'";
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
            //$this->log($arr[0]['id'], 'ckeckStop_arr');
            $arr = $arr[0]['id'];
        }
        return $arr;
    }

    /**
     * Проверка нахождения записи в таблице route_link
     */
    function ckeckRouteAndStop($route_id, $stop_id) {
        $arr = [];
        if (!empty($route_id) && !empty($stop_id)) {
            $SQL = "SELECT * FROM route_link WHERE route_id=" . $route_id . " AND stop_id=" . $stop_id;
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
            //$this->log($arr, 'ckeckRouteAndStop_arr');
        }
        return !empty($arr) ? true : false;
    }

    /**
     * Редактирование таблицы route_link
     * Изменение расположения остановки в маршруте
     * А также удаление из маршрута остановки
     */
    function actionRouteAndStop($route_id, $stop_id, $marker, $action) {
        $arr = [];
        $SQL_sign = '+';
        $SQL_sign_comparison = '>=';

        if ($action === 'delete'){
            $SQL_sign = '-';
            $SQL_sign_comparison = '>';
        }

        // остановки которые нужно сдвинуть
        $SQL = "SELECT * FROM route_link WHERE route_id = ".$route_id." AND stop_sequence >=".$marker;
        $this->query($SQL);

        // обновляем порядковые номера, выбранные прошлым запросом
        $SQL = "UPDATE route_link SET stop_sequence = stop_sequence ".$SQL_sign." 1 WHERE route_id = ".$route_id." AND stop_sequence ".$SQL_sign_comparison.$marker;
        $this->query($SQL);

        if ($action === 'delete') {
            // удаляем запись
            $SQL = "DELETE FROM route_link WHERE route_id=".$route_id." AND stop_id=".$stop_id." AND stop_sequence=".$marker;
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
            //$this->log($arr,'deleteRouteAndStop_arr');
        }
        if ($action === 'add') {
            // добавляем новую запись
            $SQL = "INSERT INTO route_link (route_id, stop_id, stop_sequence) VALUES (" . $route_id . ", " . $stop_id . ", " . $marker . ")";
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
            //$this->log($arr,'addRouteAndStop_arr');
        }

        return $arr;
    }

    /**
     * Получение списка из таблице route_link
     */
    function getListRoutStop($route_id) {
        $arr = [];
        if (!empty($route_id)) {
            $SQL = "SELECT * FROM route_link WHERE route_id=" . $route_id;
            $this->query($SQL);
            while ($row = $this->rows()) {
                $arr[] = $row;
            }
            //$this->log($arr,'getListRoutStop_arr');
        }
        return $arr;
    }
}

