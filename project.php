<?php
require_once("helpers.php");
require_once("init.php");
require_once("queries.php");

$projects = get_all_projects($con, $user_id);
$projects_id = array_column($projects, "id");
$all_tasks = get_all_tasks($con, $user_id);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors['name'] = is_filled('name');



    $errors = array_filter($errors);

    if (empty($errors)) {
        add_project($con, $_POST['name'], $user_id);
        header('Location: index.php');
        exit();
    }
}

$page_content = include_template(
    "new_project.php", [
    "projects" => $projects,
    "all_tasks" => $all_tasks,
    "errors" => $errors
]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => "Добавить задачу"]);

print($layout_content);