<?php
require_once("helpers.php");
require_once("init.php");

if(!$con) {
    $error = mysqli_connect_error();
}

function getProjects($con, int $author_id) {
    $sql = "SELECT * FROM projects WHERE author_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $author_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $projects = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $projects;
}

function getTasks($con, int $author_id) {
    $sql = "SELECT * FROM tasks WHERE author_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $author_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $tasks;
}

$author_id = 11;
$project_id = null;
$show_complete_tasks = rand(0, 1);
$projects = getProjects($con, $author_id);
$tasks = getTasks($con, $author_id);

$page_content = include_template("main.php", [
    "show_complete_tasks" => $show_complete_tasks,
    "projects" => $projects,
    "tasks" => $tasks]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => "Дела в порядке"]);

print($layout_content);
