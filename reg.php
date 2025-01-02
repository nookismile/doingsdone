<?php
require_once("helpers.php");
require_once("init.php");
require_once("queries.php");

$users = get_users($con);
$required_fields = ['email', 'password', 'name'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        };
    };

    if (!empty($_POST['email'])) {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный Email';
        };

        foreach ($users as $user) {
            if ($user['email'] == $_POST['email']) {
                $errors['email'] = 'Пользователь с этим Email уже зарегестрирован';
            };
        };
    };

    if (!empty($_POST['password'])) {
        $hashPassword = password_hash($_POST['password'] ,PASSWORD_DEFAULT);
    };

    if (empty($errors)) {
        add_user($con, $_POST['email'], $hashPassword, $_POST['name']);
        header ('Location: index.php');
        exit;
    };
}

$page_content = include_template(
    "reg.php",
    [
        "errors" => $errors
    ]
);

$layout_content = include_template("layout_auth.php", [
    "content" => $page_content,
    "title" => "Авторизация"]);

print($layout_content);