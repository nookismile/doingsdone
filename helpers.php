<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Подсчет количества задач в категориях проектов
 * @param array $tasks массив задач
 * @param string $project название проекта
 * @return int Сумма задач в категорияз проектов
 */
function get_tasks_count_by_project(array $tasks, $project) {
    $sum = 0;
    foreach ($tasks as $task) {
        if ($task["project_id"] == $project["id"]){
            $sum++;
        }
    }
    return $sum;
}

/**
 * Подсчет времени, оставшегося до дедлайна задачи
 * @param string $date дата дедлайна задачи
 * @return int Число, показывающее сколько часов осталось до дедлайна
 */
function compare_dates($date) {
    $current_date = time();
    $deadline = strtotime($date);
    $diff = $deadline - $current_date;
    return $diff;
}


/**
 * Валидирует поле проекта, если такого проекта нет в списке
 * возвращает сообщение об этом
 * @param int $id проект, который ввел пользователь в форму
 * @param array $allowed_list Список существующих проектов
 * @return string Текст сообщения об ошибке
 */
function validate_project($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return "Указан несуществующий проект";
    }
}

function validate_value($field) {
    if (empty($_POST[$field])) {
        return "Это поле должно быть заполнено";
    }
}

/**
 * Проверяет что дата окончания торгов не меньше одного дня
 * @param string $date дата которую ввел пользователь в форму
 * @return string Текст сообщения об ошибке
 */
function validate_date($date) {
    if (is_date_valid($date)) {
        $cur_date = time();
        $task_date = strtotime($date);
        if (floor(($cur_date - $task_date) / 3600) >= 24) {
            return 'Дата должна быть больше или равна текущей';
        }
    } else {
        return "Дата должна быть в формате ГГГГ-ММ-ДД";
    }
};

/**
 * Провряет заполнено ли поле в форме
 *
 * @param string $input_name имя поля
 *
 * @return string текст ошибки
 */
function is_filled($input_name) {
    if (empty($_POST[$input_name])) {
        return "Это поле не может быть пустым! ";
    }
}

/**
 * Провряет существование проекта по его id, полученному из поля формы
 *
 * @param array $projects массив с id всех проектов пользователя
 * @param string $input_name имя поля
 *
 * @return string текст ошибки
 */
function is_project_exist($projects, $input_name) {
    $project_exists = false;
    foreach($projects as $project) {
        if ($project['id'] === $_POST[$input_name]) {
            $project_exists = true;
        }
    }
    if (!$project_exists) {
        return "Проект должен быть существующим! ";
    }
}

/**
 * Провряет поле даты в форме на соответствие формату и актуальность
 *
 * @param string $input_name имя поля
 *
 * @return string текст ошибки
 */
function is_correct_date($input_name) {
    if (!empty($_POST[$input_name])) {

        if (!is_date_valid($_POST[$input_name])) {
            return "Введите дату в формате ГГГГ-ММ-ДД!";
        }

        $task_date = $_POST[$input_name];
        $actual_date = date('Y-m-d');
        if ((strtotime($actual_date) - strtotime($task_date)) / 86400 > 0) {
            return "Дата не может быть в прошлом!";
        }
    }
}


/**
 * Возвращает значение поля из отправленной формы
 * @param string $input_name имя поля
 * @return string значение поля
 */
function get_post_value($input_name) {
    return $_POST[$input_name] ?? "";
}
