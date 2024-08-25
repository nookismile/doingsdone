<?php
require_once("helpers.php");
require_once("init.php");

if(!$con) {
    $error = mysqli_connect_error();
}

$sql_get_projects = "SELECT * FROM projects WHERE author_id = ?";

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

$project_id = filter_input(INPUT_GET, 'id');

$author_id = 12;
$show_complete_tasks = rand(0, 1);
$projects = get_all_projects($con, $author_id);
$all_tasks = get_all_tasks($con, $author_id);

if (isset($project_id)) {
    $tasks = get_tasks_by_project($con, $project_id);
} else {
    $tasks = get_all_tasks($con, $author_id);
}

$page_content = include_template(
    "main.php",
    [
        "show_complete_tasks" => $show_complete_tasks,
        "projects" => $projects,
        "all_tasks" => $all_tasks,
        "tasks" => $tasks]
);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => "Дела в порядке"]);

print($layout_content);












