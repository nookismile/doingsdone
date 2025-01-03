<?php
require_once("helpers.php");
require_once("init.php");
require_once("queries.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['email', 'password'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        };
    };

    if (!empty($_POST['email'])) {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail введён некорректно';
        };
    };

    $user_id = get_user_id($con, $_POST['email']);

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($con, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) && !empty($user)) {
   //     if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user_id;
//        }
//        else {
//            $errors['password'] = 'Неверный пароль';
//        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors)) {
        $page_content = include_template('auth.php', ['errors' => $errors]);
    }
    else {
        $_SESSION['user_id'] = $user_id;
        header("Location: /index.php");
        exit();
    }

} else {
    $page_content = include_template(
        "auth.php",
        []
    );

    if(isset($_SESSION['user_id'])) {
        header ('Location: index.php');
        exit();
    }
}

$layout_content = include_template("layout_auth.php", [
    "content" => $page_content,
    "title" => "Вход на сайт"]);

print($layout_content);