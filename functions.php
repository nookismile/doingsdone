<?php
function get_tasks_count_by_project(array $tasks, $project) {
    $sum = 0;
    foreach ($tasks as $task) {
        if ($task["project"] == $project){
            $sum++;
        }
    }
    return $sum;
}