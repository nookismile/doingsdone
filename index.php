<?php
require_once("init.php");
require_once("helpers.php");
require_once("queries.php");

$project_id = filter_input(INPUT_GET, 'id');

$show_complete_tasks = rand(0, 1);
$projects = get_all_projects($con, $user_id);
$all_tasks = get_all_tasks($con, $user_id);

if (isset($project_id)) {
    $tasks = get_tasks_by_project($con, $project_id);
} else {
    $tasks = get_all_tasks($con, $user_id);
}

if (!isset($_SESSION['user_id'])) {
    $page_content = include_template('guest.php');
} else {
    $page_content = include_template(
        "main.php",
        [
            "show_complete_tasks" => $show_complete_tasks,
            "projects" => $projects,
            "all_tasks" => $all_tasks,
            "tasks" => $tasks]
    );
}

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => "Дела в порядке"]);

print($layout_content);
