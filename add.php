<?php
require_once("helpers.php");
require_once("init.php");
require_once("queries.php");

$author_id = 1;
$projects = get_all_projects($con, $author_id);
$projects_id = array_column($projects, "id");
$all_tasks = get_all_tasks($con, $author_id);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors['name'] = is_filled('name');
    //$errors['project'] = is_project_exist($projects, 'project');
    $errors['date'] = is_correct_date('date');

    $deadline = 'NULL';
    if ($_POST['date']) {
        $deadline = $_POST['date'];
    }

    $file_link = 'NULL';
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        $file_name = 'file-' . uniqid() . '_' . $_FILES['file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
        $file_link = '/uploads/' . $file_name;
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        add_task($con, $_POST['name'], $file_link, $deadline, $_POST['project'], $author_id);
        header('Location: index.php');
        exit();
    }
}

$page_content = include_template(
    "new_task.php", [
    "projects" => $projects,
    "all_tasks" => $all_tasks,
    "errors" => $errors
]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => "Добавить задачу"]);

print($layout_content);