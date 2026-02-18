<?php
include_once SITE_ROOT . "/app/database/db.php";

$errMsg = '';
$id = '';
$name = '';
$description = '';

$topics = selectAll('topics');


// РљРѕРґ РґР»СЏ С„РѕСЂРјС‹ СЃРѕР·РґР°РЅРёСЏ РєР°С‚РµРіРѕСЂРёРё
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-create'])){
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if($name === '' || $description === ''){
        $errMsg = "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!";
    }elseif (mb_strlen($name, 'UTF8') < 2){
        $errMsg = "РљР°С‚РµРіРѕСЂРёСЏ РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ";
    }else{
        $existence = selectOne('topics', ['name' => $name]);
        if($existence['name'] === $name){
            $errMsg = "РўР°РєР°СЏ РєР°С‚РµРіРѕСЂРёСЏ СѓР¶Рµ РµСЃС‚СЊ РІ Р±Р°Р·Рµ";
        }else{
            $topic = [
                'name' => $name,
                'description' => $description
            ];
            $id = insert('topics', $topic);
            $topic = selectOne('topics', ['id' => $id] );
            header('location: ' . BASE_URL . 'admin/topics/index.php');
        }
    }
}else{
    $name = '';
    $description = '';
}


// РђРїРґРµР№С‚ РєР°С‚РµРіРѕСЂРёРё
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    $id = $_GET['id'];
    $topic = selectOne('topics', ['id' => $id]);
    $id = $topic['id'];
    $name = $topic['name'];
    $description = $topic['description'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-edit'])){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if($name === '' || $description === ''){
        $errMsg = "РќРµ РІСЃРµ РїРѕР»СЏ Р·Р°РїРѕР»РЅРµРЅС‹!";
    }elseif (mb_strlen($name, 'UTF8') < 2){
        $errMsg = "РљР°С‚РµРіРѕСЂРёСЏ РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ";
    }else{
        $topic = [
            'name' => $name,
            'description' => $description
        ];
        $id = $_POST['id'];
        $topic_id = update('topics', $id, $topic);
        header('location: ' . BASE_URL . 'admin/topics/index.php');
    }
}

// РЈРґР°Р»РµРЅРёРµ РєР°С‚РµРіРѕСЂРёРё
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del_id'])){
    $id = $_GET['del_id'];
    delete('topics', $id);
    header('location: ' . BASE_URL . 'admin/topics/index.php');
}