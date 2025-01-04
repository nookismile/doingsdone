<?php
require_once("init.php");
require_once("helpers.php");
require_once("queries.php");

$project_id = filter_input(INPUT_GET, 'id');

$projects = get_all_projects($con, $user_id);
$all_tasks = get_all_tasks($con, $user_id);
$tasks_filter = ['Все задачи', 'Повестка дня', 'Завтра', 'Просроченные'];

/**
 * функция для добавления параметра к строке запроса
 * @param string $name_params имя параметра из массива $_GET
 * @param string $value_params значение параметра
 *
 * @return string url с новым параметром
 */
function get_new_url($name_params, $value_params)
{
    $params = $_GET;
    $params[$name_params] = $value_params;

    return pathinfo(__FILE__, PATHINFO_BASENAME) . '?' . http_build_query($params);
}

if (isset($project_id)) {
    $tasks = get_tasks_by_project($con, $project_id);
} else {
    $tasks = get_all_tasks($con, $user_id);
}

/*проверка наличия запроса на инвертирование статуса задачи*/
if (isset($_GET['task_completed'])) {
    $task = get_task_where_id($con, $_GET['task_completed']);
    change_status($con, $task);
    header('Location: index.php');
}

/*проверка выбора фильтра задач*/
if (isset($_GET['filter'])) {
    switch ($_GET['filter']) {
        case 1:
            $tasks = get_tasks_today($tasks);
            break;
        case 2:
            $tasks = get_task_tomorrow($tasks);
            break;
        case 3:
            $tasks = get_task_overdue($tasks);
            break;
        default:
            break;
    }
}

if (isset($_GET['search'])) {
    $search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS));
    if (!empty($search)) {
        $tasks = get_search_tasks($con, $search);
    }
}

if (!isset($_SESSION['user_id'])) {
    $page_content = include_template('guest.php');
} else {
    $user_name = get_user_name($con, $user_id);
    $page_content = include_template(
        "main.php",
        [
            "projects" => $projects,
            "all_tasks" => $all_tasks,
            "tasks" => $tasks,
            'tasks_filter' => $tasks_filter]
    );
}

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    'user_name' => $user_name,
    "title" => "Дела в порядке"]);

print($layout_content);
