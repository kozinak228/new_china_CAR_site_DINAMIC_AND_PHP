<?php
include_once SITE_ROOT . "/app/database/db.php";

$errMsg = [];

function userAuth($user)
{
    $_SESSION['id'] = $user['id'];
    $_SESSION['login'] = $user['username'];
    $_SESSION['admin'] = $user['admin'];
    if ($_SESSION['admin']) {
        header('location: ' . BASE_URL . "admin/cars/index.php");
    } else {
        header('location: ' . BASE_URL);
    }
}

$users = selectAll('users');

// РљРѕРґ РґР»СЏ С„РѕСЂРјС‹ СЂРµРіРёСЃС‚СЂР°С†РёРё
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-reg'])) {

    $admin = 0;
    $login = trim($_POST['login']);
    $email = trim($_POST['mail']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);

    if ($login === '' || $email === '' || $passF === '') {
        array_push($errMsg, "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!");
    } elseif (mb_strlen($login, 'UTF8') < 2) {
        array_push($errMsg, "Р›РѕРіРёРЅ РґРѕР»Р¶РµРЅ Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ");
    } elseif ($passF !== $passS) {
        array_push($errMsg, "РџР°СЂРѕР»Рё РІ РѕР±РµРёС… РїРѕР»СЏС… РґРѕР»Р¶РЅС‹ СЃРѕРѕС‚РІРµС‚СЃС‚РІРѕРІР°С‚СЊ!");
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if ($existence['email'] === $email) {
            array_push($errMsg, "РџРѕР»СЊР·РѕРІР°С‚РµР»СЊ СЃ С‚Р°РєРѕР№ РїРѕС‡С‚РѕР№ СѓР¶Рµ Р·Р°СЂРµРіРёСЃС‚СЂРёСЂРѕРІР°РЅ!");
        } else {
            $pass = password_hash($passF, PASSWORD_DEFAULT);
            $post = [
                'admin' => $admin,
                'username' => $login,
                'email' => $email,
                'password' => $pass
            ];
            $id = insert('users', $post);
            $user = selectOne('users', ['id' => $id]);
            userAuth($user);
        }
    }
} else {
    $login = '';
    $email = '';
}

// РљРѕРґ РґР»СЏ С„РѕСЂРјС‹ Р°РІС‚РѕСЂРёР·Р°С†РёРё
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])) {

    $email = trim($_POST['mail']);
    $pass = trim($_POST['password']);

    if ($email === '' || $pass === '') {
        array_push($errMsg, "Не все поля заполнены!");
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if (!$existence) {
            $existence = selectOne('users', ['username' => $email]);
        }
        if ($existence && password_verify($pass, $existence['password'])) {
            userAuth($existence);
        } else {
            array_push($errMsg, "Email либо пароль введены неверно!");
        }
    }
} else {
    $email = '';
}

// РљРѕРґ РґРѕР±Р°РІР»РµРЅРёСЏ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ РІ Р°РґРјРёРЅРєРµ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-user'])) {


    $admin = 0;
    $login = trim($_POST['login']);
    $email = trim($_POST['mail']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);

    if ($login === '' || $email === '' || $passF === '') {
        array_push($errMsg, "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!");
    } elseif (mb_strlen($login, 'UTF8') < 2) {
        array_push($errMsg, "Р›РѕРіРёРЅ РґРѕР»Р¶РµРЅ Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ");
    } elseif ($passF !== $passS) {
        array_push($errMsg, "РџР°СЂРѕР»Рё РІ РѕР±РµРёС… РїРѕР»СЏС… РґРѕР»Р¶РЅС‹ СЃРѕРѕС‚РІРµС‚СЃС‚РІРѕРІР°С‚СЊ!");
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if ($existence['email'] === $email) {
            array_push($errMsg, "РџРѕР»СЊР·РѕРІР°С‚РµР»СЊ СЃ С‚Р°РєРѕР№ РїРѕС‡С‚РѕР№ СѓР¶Рµ Р·Р°СЂРµРіРёСЃС‚СЂРёСЂРѕРІР°РЅ!");
        } else {
            $pass = password_hash($passF, PASSWORD_DEFAULT);
            if (isset($_POST['admin']))
                $admin = 1;
            $user = [
                'admin' => $admin,
                'username' => $login,
                'email' => $email,
                'password' => $pass
            ];
            $id = insert('users', $user);
            $user = selectOne('users', ['id' => $id]);
            userAuth($user);
        }
    }
} else {
    $login = '';
    $email = '';
}

// РљРѕРґ СѓРґР°Р»РµРЅРёСЏ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ РІ Р°РґРјРёРЅРєРµ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    delete('users', $id);
    header('location: ' . BASE_URL . 'admin/users/index.php');
}

// Р Р•Р”РђРљРўРР РћР’РђРќРР• РџРћР›Р¬Р—РћР’РђРўР•Р›РЇ Р§Р•Р Р•Р— РђР”РњРРќРљРЈ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])) {
    $user = selectOne('users', ['id' => $_GET['edit_id']]);

    $id = $user['id'];
    $admin = $user['admin'];
    $username = $user['username'];
    $email = $user['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user'])) {

    $id = $_POST['id'];
    $mail = trim($_POST['mail']);
    $login = trim($_POST['login']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);
    $admin = isset($_POST['admin']) ? 1 : 0;

    if ($login === '') {
        array_push($errMsg, "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!");
    } elseif (mb_strlen($login, 'UTF8') < 2) {
        array_push($errMsg, "Р›РѕРіРёРЅ РґРѕР»Р¶РµРЅ Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ");
    } elseif ($passF !== $passS) {
        array_push($errMsg, "РџР°СЂРѕР»Рё РІ РѕР±РµРёС… РїРѕР»СЏС… РґРѕР»Р¶РЅС‹ СЃРѕРѕС‚РІРµС‚СЃС‚РІРѕРІР°С‚СЊ!");
    } else {
        $pass = password_hash($passF, PASSWORD_DEFAULT);
        if (isset($_POST['admin']))
            $admin = 1;
        $user = [
            'admin' => $admin,
            'username' => $login,
            //            'email' => $mail,
            'password' => $pass
        ];

        $user = update('users', $id, $user);
        header('location: ' . BASE_URL . 'admin/users/index.php');
    }
} else {
    $id = isset($user) ? $user['id'] : '';
    $admin = isset($user) ? $user['admin'] : 0;
    $username = isset($user) ? $user['username'] : '';
    $email = isset($user) ? ($user['email'] ?? '') : '';
}

//if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])){
//    $id = $_GET['pub_id'];
//    $publish = $_GET['publish'];
//
//    $postId = update('posts', $id, ['status' => $publish]);
//
//    header('location: ' . BASE_URL . 'admin/posts/index.php');
//    exit();
//}