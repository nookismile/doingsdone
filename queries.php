<?php

function get_all_projects($con, int $author_id) {
    $sql = "SELECT * FROM projects WHERE author_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $author_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $projects = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $projects;
}

function get_all_tasks($con, int $author_id) {
    $sql = "SELECT * FROM tasks WHERE author_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $author_id);
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
 * @param sting $title - заголовок задачи
 * @param sting $filepath - путь к прикрепленному файлу
 * @param sting $deadline - дата окончания задачи
 * @param int $project_id - id проекта, к которому относится задача
 * @param int $user_id - id пользователя, который создал задачу
 *
 */
function add_task($con, string $title, string $filepath, string $deadline, int $project_id, int $author_id) {
    $title = mysqli_real_escape_string($con, $title);
    $filepath = mysqli_real_escape_string($con, $filepath);
    $deadline = mysqli_real_escape_string($con, $deadline);

    $sql = "INSERT INTO tasks SET title = '$title', project_id = '$project_id', author_id='$author_id'";
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
