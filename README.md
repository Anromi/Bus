# Тестовое задание
____
Расписание рейсовых автобусов.

Подготовка:
-----------
Я использовал OSPanel вместе с Adminer.

**1.** В Adminer нужно импортировать БД, это файл:

* [BUSp.sql](https://github.com/Anromi/Bus/blob/main/BUSp.sql)

Если вдруг не получится из за ошибок, попробуйте сделать следующее:
В Adminer создайте БД с названием `BUSp`. Нажмите на строчку `SQL-запрос` и вставьте код из файла `BUSp.sql`.

**2.** В OSPanel выбираем модули: PHP-8.0 и PostgreSql-10. В директиву `OSPanel\home` закидываем папку `bus` из проекта. Перезагружаем OSPanel. Теперь наш проект можно открыть в браузере.

Как пользоваться:
-----------
**1.** `bus/classes/find-bus.php?` и следом входные параметры:

{from} - id исходной остановки;

{to} - id конечной остановки.

Текущее время записано как константа `9:20`

Примеры входной строки: 

Пример №1

`bus/classes/find-bus.php?from=1&to=3` 

Результат

```
Array
(
    [from] => Пионерстроя
    [to] => Лётчика Пилютова
    [buses] => Array
        (
            [0] => Array
                (
                    [route] => Маршрут Автобус №0 в сторону Партизана Германа
                    [next_arrivals] => Array
                        (
                            [0] => 10:00
                            [1] => 11:00
                        )

                )

            [1] => Array
                (
                    [route] => Маршрут Автобус №1 в сторону Партизана Германа
                    [next_arrivals] => Array
                        (
                            [0] => 10:15
                        )

                )

        )

)
```
Пример №2

`bus/classes/find-bus.php?from=1&to=2` 

Результат

```
Array
(
    [from] => 
    [to] => 
    [buses] => Array
        (
        )

)

Array
(
    [from] => Пионерстроя
    [to] => проспект Ветеранов д.149
    [buses] => Array
        (
            [0] => Array
                (
                    [route] => Маршрут Автобус №0 в сторону Партизана Германа
                    [next_arrivals] => Array
                        (
                            [0] => 10:00
                            [1] => 11:00
                        )

                )

        )

)
```

Пример №3

`bus/classes/find-bus.php?from=7&to=3` 

Результат

Если ближайшего времени нет то `next_arrivals` получит такое сообщение
```
Array
(
    [from] => Партизана Германа
    [to] => Лётчика Пилютова
    [buses] => Array
        (
            [0] => Array
                (
                    [route] => Маршрут Автобус №2 в сторону Партизана Германа
                    [next_arrivals] => Автобусов уже больше не будет
                )

        )

)
```

**2.** `bus/classes/edit-route.php?` и следом входные параметры:

{route_1} - id маршрута в котором остановка;

{action} - действие: add, delete. Eсли action = add - маркер нужно вводить, он указывает на место куда добавить. Если action = delete - маркер не нужно вводить;

{stop_name} - название остановки;

{marker} - порядковый номер

Примеры входной строки: 

Пример №1

`bus/classes/edit-route.php?route_id=2&action=add&stop_name=Тамбасова&marker=4` 

Что мы хотим тут сделать: В маршруте под id=2 добавить остановку под порядковым номером 4.

Результат

Тут обращаем внимание на массивы `start` - как было изначально и `end` - как стало после запроса. У остановки Тамбасова `stop_id=5`, можно увидеть как его `stop_sequence` меняется порядковый номер с 3 на 4, а остановка которая занимала 4, опустилась вниз на 3.
```
Array
(
    [request] => id маршрута: 2. Остановка: Тамбасова. Действие: add. Порядковый номер: 4
    [result] => Array
        (
            [start] => Array
                (
                    [0] => Array
                        (
                            [id] => 8
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 9
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 2
                        )

                    [2] => Array
                        (
                            [id] => 10
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 5
                            [stop_sequence] => 3
                        )

                    [3] => Array
                        (
                            [id] => 11
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 4
                        )

                )

            [end] => Array
                (
                    [0] => Array
                        (
                            [id] => 8
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 9
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 2
                        )

                    [2] => Array
                        (
                            [id] => 11
                            [route_id] => 2
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 3
                        )

                    [3] => Array
                        (
                            [id] => 19
                            [route_id] => 2
                            [direction_id] => 0
                            [stop_id] => 5
                            [stop_sequence] => 4
                        )

                )

        )

    [error] => Array
        (
            [0] => stop_name сущ. и его связка с маршрутом тоже сущ.
        )

)
```

Пример №2

`bus/classes/edit-route.php?route_id=1&action=add&stop_name=Авангардная&marker=2` 

Что мы хотим тут сделать: В маршруте под id=1 добавить остановку под порядковым номером 2.

Результат

```
Array
(
    [request] => id маршрута: 1. Остановка: Авангардная. Действие: add. Порядковый номер: 2
    [result] => Array
        (
            [start] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 2
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 2
                            [stop_sequence] => 2
                        )

                    [2] => Array
                        (
                            [id] => 3
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 3
                        )

                    [3] => Array
                        (
                            [id] => 4
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 4
                            [stop_sequence] => 4
                        )

                    [4] => Array
                        (
                            [id] => 5
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 5
                            [stop_sequence] => 5
                        )

                    [5] => Array
                        (
                            [id] => 6
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 6
                            [stop_sequence] => 6
                        )

                    [6] => Array
                        (
                            [id] => 7
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 7
                        )

                )

            [end] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 2
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 2
                            [stop_sequence] => 3
                        )

                    [2] => Array
                        (
                            [id] => 3
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 4
                        )

                    [3] => Array
                        (
                            [id] => 4
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 4
                            [stop_sequence] => 5
                        )

                    [4] => Array
                        (
                            [id] => 5
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 5
                            [stop_sequence] => 6
                        )
                    [5] => Array
                        (
                            [id] => 6
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 6
                            [stop_sequence] => 7
                        )

                    [6] => Array
                        (
                            [id] => 7
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 8
                        )

                    [7] => Array
                        (
                            [id] => 20
                            [route_id] => 1
                            [direction_id] => 0
                            [stop_id] => 8
                            [stop_sequence] => 2
                        )

                )

        )

    [error] => Array
        (
        )

)
```

Пример №3

`bus/classes/edit-route.php?route_id=1&action=delete&stop_name=Авангардная` 

Что мы хотим тут сделать: В маршруте под id=1 удалить остановку. В пример №2 мы данную остановку добавали, сейчас её же и уберем.

Результат

Массив `end` соотвествует массиву `start` из примера №2.
```
Array
(
    [request] => id маршрута: 1. Остановка: Авангардная. Действие: delete. Порядковый номер: 0
    [result] => Array
        (
            [start] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 2
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 2
                            [stop_sequence] => 3
                        )

                    [2] => Array
                        (
                            [id] => 3
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 4
                        )

                    [3] => Array
                        (
                            [id] => 4
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 4
                            [stop_sequence] => 5
                        )

                    [4] => Array
                        (
                            [id] => 5
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 5
                            [stop_sequence] => 6
                        )

                    [5] => Array
                        (
                            [id] => 6
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 6
                            [stop_sequence] => 7
                        )

                    [6] => Array
                        (
                            [id] => 7
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 8
                        )

                    [7] => Array
                        (
                            [id] => 20
                            [route_id] => 1
                            [direction_id] => 0
                            [stop_id] => 8
                            [stop_sequence] => 2
                        )

                )

            [end] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 1
                            [stop_sequence] => 1
                        )

                    [1] => Array
                        (
                            [id] => 2
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 2
                            [stop_sequence] => 2
                        )

                    [2] => Array
                        (
                            [id] => 3
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 3
                            [stop_sequence] => 3
                        )

                    [3] => Array
                        (
                            [id] => 4
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 4
                            [stop_sequence] => 4
                        )
                    [4] => Array
                        (
                            [id] => 5
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 5
                            [stop_sequence] => 5
                        )

                    [5] => Array
                        (
                            [id] => 6
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 6
                            [stop_sequence] => 6
                        )

                    [6] => Array
                        (
                            [id] => 7
                            [route_id] => 1
                            [direction_id] => 1
                            [stop_id] => 7
                            [stop_sequence] => 7
                        )

                )

        )

    [error] => Array
        (
        )

)
```