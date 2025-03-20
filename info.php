<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Таблицы</title>
    <style>  
    /* Для наиболее красивого вывода результатов добавила в код css оформления */

    /*Для всей страницы, её элементов*/
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: #f4f4f4;
        }

        /* Стили таблиц */
        table {
            border-collapse: collapse;
            width: 200px;
            margin: 10px;
            background-color: #fff;
        }

        /*Стили ячеек таблиц */
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        /*Стили заголовков таблиц */
        th {
            background-color:rgb(179, 179, 179);
            height:50px;
        }

        /*Стили заголовков элементов, для обозначения названий таблиц */
        h2 {
            text-align: center;
        }

    </style>
</head>
<body>




<?php
/*Для решения данной задачи решила применить ООП */
class P {    
    public $domain;
    public $user;
    public $password;
    public $DBH;

    public function __construct($domain, $user, $password) {   //для проверки подключения БД к файлу 
        $this->domain = $domain;
        $this->user = $user;
        $this->password = $password;
        try {
            $this->DBH = new PDO($domain, $user, $password);
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Проблемы с подключением к БД infoproject.";
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    public function sel() { // SELECT всей таблицы info
        $STH = $this->DBH->query('SELECT * FROM info');//sql запрос для вывода результатов
        $STH->setFetchMode(PDO::FETCH_ASSOC);

        echo '<div>';
        echo '<h2>Исходная таблица</h2>';
        echo '<table>';
        echo '<tr><th>Date</th><th>Count</th></tr>';

        while ($row = $STH->fetch()) {
            echo '<tr><td>' . $row['date'] . '</td><td>' . $row['count'] . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function sel2() { // SELECT скорректированной таблицы info
        //sql запрос для вывода результатов
        $STH = $this->DBH->query('
            SELECT 
                DATE_FORMAT(date, "%m.%Y") AS month,
                SUM(count) AS total_count
            FROM 
                info
            WHERE 
                DAY(date) != 1
            GROUP BY 
                DATE_FORMAT(date, "%m.%Y")

            UNION ALL

            SELECT 
                DATE_FORMAT(date, "%d.%m.%Y") AS month,
                count
            FROM 
                info
            WHERE 
                DAY(date) = 1
        ');

        $STH->setFetchMode(PDO::FETCH_ASSOC);

        echo '<div>';
        echo '<h2>Скорректированная таблица</h2>';
        echo '<table>';
        echo '<tr>
        <th>Date</th>
        <th>Count</th>
        </tr>';

        while ($row = $STH->fetch()) {
            echo '<tr><td>' . $row['month'] . '</td><td>' . $row['total_count'] . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }
}

$P_ON = new P('mysql:host=localhost;dbname=infoproject', 'root', '');//подключение к БД, передача параметров для этого в класс 
//ниже перечислен вызов функций, которые будут выводить соответствующие результаты
$P_ON->sel();
$P_ON->sel2();
?>

</body>
</html>
