<?php

include_once SITE_ROOT . "/app/database/db.php";
if (!$_SESSION){
    header('location: ' . BASE_URL . 'log.php');
}

$errMsg = [];
$id = '';
$title = '';
$content = '';
$img = '';
$topic = '';

$topics = selectAll('topics');
$posts = selectAll('posts');
$postsAdm = selectAllFromPostsWithUsers('posts', 'users');

// РљРѕРґ РґР»СЏ С„РѕСЂРјС‹ СЃРѕР·РґР°РЅРёСЏ Р·Р°РїРёСЃРё
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])){

    if (!empty($_FILES['img']['name'])){
        $imgName = time() . "_" . $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileType = $_FILES['img']['type'];
        $destination = ROOT_PATH . "\assets\images\posts\\" . $imgName;


        if (strpos($fileType, 'image') === false) {
            array_push($errMsg, "РџРѕРґРіСЂСѓР¶Р°РµРјС‹Р№ С„Р°Р№Р» РЅРµ СЏРІР»СЏРµС‚СЃСЏ РёР·РѕР±СЂР°Р¶РµРЅРёРµРј!");
        }else{
            $result = move_uploaded_file($fileTmpName, $destination);

            if ($result){
                $_POST['img'] = $imgName;
            }else{
                array_push($errMsg, "РћС€РёР±РєР° Р·Р°РіСЂСѓР·РєРё РёР·РѕР±СЂР°Р¶РµРЅРёСЏ РЅР° СЃРµСЂРІРµСЂ");
            }
        }
    }else{
        array_push($errMsg, "РћС€РёР±РєР° РїРѕР»СѓС‡РµРЅРёСЏ РєР°СЂС‚РёРЅРєРё");
    }

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $topic = trim($_POST['topic']);
    $publish = isset($_POST['publish']) ? 1 : 0;


    if($title === '' || $content === '' || $topic === ''){
        array_push($errMsg, "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!");
    }elseif (mb_strlen($title, 'UTF8') < 7){
        array_push($errMsg, "РќР°Р·РІР°РЅРёРµ СЃС‚Р°С‚СЊРё РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ Р±РѕР»РµРµ 7-РјРё СЃРёРјРІРѕР»РѕРІ");
    }else{
        $post = [
            'id_user' => $_SESSION['id'],
            'title' => $title,
            'content' => $content,
            'img' => $_POST['img'],
            'status' => $publish,
            'id_topic' => $topic
        ];

        $post = insert('posts', $post);
        $post = selectOne('posts', ['id' => $id] );
        header('location: ' . BASE_URL . 'admin/posts/index.php');
    }
}else{
    $id = '';
    $title = '';
    $content = '';
    $publish = '';
    $topic = '';
}


// РђРџР”Р•Р™Рў РЎРўРђРўР¬Р
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    $post = selectOne('posts', ['id' => $_GET['id']]);

    $id =  $post['id'];
    $title =  $post['title'];
    $content = $post['content'];
    $topic = $post['id_topic'];
    $publish = $post['status'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])){
    $id =  $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $topic = trim($_POST['topic']);
    $publish = isset($_POST['publish']) ? 1 : 0;

    if (!empty($_FILES['img']['name'])){
        $imgName = time() . "_" . $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileType = $_FILES['img']['type'];
        $destination = ROOT_PATH . "\assets\images\posts\\" . $imgName;


        if (strpos($fileType, 'image') === false) {
            array_push($errMsg, "РџРѕРґРіСЂСѓР¶Р°РµРјС‹Р№ С„Р°Р№Р» РЅРµ СЏРІР»СЏРµС‚СЃСЏ РёР·РѕР±СЂР°Р¶РµРЅРёРµРј!");
        }else{
            $result = move_uploaded_file($fileTmpName, $destination);

            if ($result){
                $_POST['img'] = $imgName;
            }else{
                array_push($errMsg, "РћС€РёР±РєР° Р·Р°РіСЂСѓР·РєРё РёР·РѕР±СЂР°Р¶РµРЅРёСЏ РЅР° СЃРµСЂРІРµСЂ");
            }
        }
    }else{
        array_push($errMsg, "РћС€РёР±РєР° РїРѕР»СѓС‡РµРЅРёСЏ РєР°СЂС‚РёРЅРєРё");
    }


    if($title === '' || $content === '' || $topic === ''){
        array_push($errMsg, "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!");
    }elseif (mb_strlen($title, 'UTF8') < 7){
        array_push($errMsg, "РќР°Р·РІР°РЅРёРµ СЃС‚Р°С‚СЊРё РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ Р±РѕР»РµРµ 7-РјРё СЃРёРјРІРѕР»РѕРІ");
    }else{
        $post = [
            'id_user' => $_SESSION['id'],
            'title' => $title,
            'content' => $content,
            'img' => $_POST['img'],
            'status' => $publish,
            'id_topic' => $topic
        ];

        $post = update('posts', $id, $post);
        header('location: ' . BASE_URL . 'admin/posts/index.php');
    }
}else{
    $title = $_POST['title'];
    $content = $_POST['content'];
    $publish = isset($_POST['publish']) ? 1 : 0;
    $topic = $_POST['id_topic'];
}

// РЎС‚Р°С‚СѓСЃ РѕРїСѓР±Р»РёРєРѕРІР°С‚СЊ РёР»Рё СЃРЅСЏС‚СЊ СЃ РїСѓР±Р»РёРєР°С†РёРё
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])){
    $id = $_GET['pub_id'];
    $publish = $_GET['publish'];

    $postId = update('posts', $id, ['status' => $publish]);

    header('location: ' . BASE_URL . 'admin/posts/index.php');
    exit();
}

// РЈРґР°Р»РµРЅРёРµ СЃС‚Р°С‚СЊРё
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    delete('posts', $id);
    header('location: ' . BASE_URL . 'admin/posts/index.php');
}