<?php
require_once("helpers.php");

function get_all_projects($con, int $user_id) {
    $sql = "SELECT * FROM projects WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $projects = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $projects;
}

function get_all_tasks($con, int $user_id) {
    $sql = "SELECT * FROM tasks WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $tasks;
}

function get_tasks_by_project($con, int $project_id) {
    $sql = "SELECT * FROM tasks WHERE project_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $project_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $tasks;
}

function get_users($con) {
    $sql = "SELECT * FROM users";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $users;
}

/**
 * Добавляет новую задачу в базу данных
 *
 * @param bool $connect состояние подключения к БД
 * @param string $title - заголовок задачи
 */
function add_project($con, string $title, int $user_id) {
    $title = mysqli_real_escape_string($con, $title);
    $filepath = mysqli_real_escape_string($con, $user_id);

    $sql = "INSERT INTO projects SET title = '$title', user_id='$user_id'";

    $sql = $sql . ";";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        $error = mysqli_error($con);
        print ("Ошибка подключения к БД: " . $error);
    }
}

/**
 * Добавляет новую задачу в базу данных
 *
 * @param bool $connect состояние подключения к БД
 * @param sting $title - заголовок задачи
 * @param sting $filepath - путь к прикрепленному файлу
 * @param sting $deadline - дата окончания задачи
 * @param int $project_id - id проекта, к которому относится задача
 * @param int $user_id - id пользователя, который создал задачу
 *
 */
function add_task($con, string $title, string $filepath, string $deadline, int $project_id, int $user_id) {
    $title = mysqli_real_escape_string($con, $title);
    $filepath = mysqli_real_escape_string($con, $filepath);
    $deadline = mysqli_real_escape_string($con, $deadline);

    $sql = "INSERT INTO tasks SET title = '$title', project_id = '$project_id', user_id='$user_id'";
    if ($deadline !== 'NULL') {
        $sql = $sql . ", deadline = '$deadline'";
    }
    if ($filepath !== 'NULL') {
        $sql = $sql . ", filepath = '$filepath'";
    }
    $sql = $sql . ";";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        $error = mysqli_error($con);
        print ("Ошибка подключения к БД: " . $error);
    }
}

/**
 * Добавляет нового пользователя в базу данных
 *
 * @param bool $connect состояние подключения к БД
 * @param sting $email - электронная почта пользователя
 * @param sting $password - пароль пользователя
 * @param sting $name - имя пользователя
 *
 */
function add_user($con, string $email, string $password, string $name) {
    $email = mysqli_real_escape_string($con, $email);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($con, $name);
    $sql = "INSERT INTO users SET email = '$email', password = '$password', name='$name';";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        $error = mysqli_error($con);
        print ("Ошибка подключения к БД: " . $error);
        exit();
    }
}

/**
 * Сравнивает полученный от пользователя пароль с хэшем из БД
 *
 * @param bool $con состояние подключения к БД
 * @param int $email email пользователя
 * @param int $password пароль пользователя
 *
 * @return bool true если пароль совпадает, false если нет
 */
function check_password($con, string $email, string $password) {
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT email FROM users WHERE email = '$email';";
    $result = mysqli_query($con, $sql);
    $email_in_base = mysqli_fetch_assoc($result);
    if ($email_in_base) {
        $sql = "SELECT password FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $password_hash = mysqli_fetch_assoc($result);
        } else {
            $error = mysqli_error($con);
            print ("Ошибка подключения к БД: " . $error);
        }
        return password_verify($password, $password_hash['password']);
    }
}

/**
 * Получает из базы данных id пользователя по его email
 *
 * @param bool $con состояние подключения к БД
 * @param int $email email пользователя
 *
 * @return int id пользователя
 */
function get_user($con, string $email) {
    $sql = 'SELECT * FROM users WHERE email = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $user = null;

    foreach ($users as $us) {
        $user = $us;
    }

    return $user;
}

/**
 * Получает из базы данных id пользователя по его email
 *
 * @param bool $con состояние подключения к БД
 * @param int $email email пользователя
 *
 * @return int id пользователя
 */
function get_user_id($con, string $email) {
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = mysqli_error($con);
        print("Ошибка подключения к БД: " . $error);
    }
    return $user['id'];
}

/*функция, возвращающая массив найденных задач*/
function get_search_tasks($con, $search)
{
    $sql = 'SELECT * FROM tasks WHERE MATCH(title) AGAINST (? IN BOOLEAN MODE)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $search);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $tasks;
}

/**
 * функция, возвращающая имя пользователя по идентификатору
 * @param resource $con ресурс соединения
 * @param integer $user_id идентификатор пользователя
 * @return string имя пользователя по идентификатору
 */
function get_user_name($con, $user_id)
{
    $sql = 'SELECT name FROM users WHERE id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $user_name = null;

    foreach ($users as $user) {
        $user_name = $user['name'];
    }

    return $user_name;
}

/**
 * функция, возвращающая задачу по идентификатору
 * @param resource $con ресурс соединения
 * @param integer $task_id идентификатор задачи
 *
 * @return array массив задач по идентификатору
 */
function get_task_where_id($con, int $task_id)
{
    $sql = 'SELECT * FROM tasks WHERE id = ?';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $task_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $task = null;

    foreach ($tasks as $value) {
        $task = $value;
    }

    return $task;
}

/**
 * функция, инвертирующая статус задачи
 * @param resource $con ресурс соединения
 * @param array $task массив задач
 */
function change_status($con, $task)
{
    $status = 1 - get_value($task, 'status');

    $parameters = [$status, get_value($task, 'id')];
    $sql = 'UPDATE tasks SET status = ? WHERE id = ?';

    $stmt = db_get_prepare_stmt($con, $sql, $parameters);
    mysqli_stmt_execute($stmt);
}