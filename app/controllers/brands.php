<?php
include_once SITE_ROOT . "/app/database/db.php";

$errMsg = '';
$id = '';
$name = '';
$logo = '';
$country = '';

$brands = selectAll('brands');

// РЎРѕР·РґР°РЅРёРµ Р±СЂРµРЅРґР°
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand-create'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : 'РљРёС‚Р°Р№';

    // Р—Р°РіСЂСѓР·РєР° Р»РѕРіРѕС‚РёРїР°
    $logoName = '';
    if (!empty($_FILES['logo']['name'])) {
        $imgName = time() . "_" . $_FILES['logo']['name'];
        $fileTmpName = $_FILES['logo']['tmp_name'];
        $fileType = $_FILES['logo']['type'];
        $destination = ROOT_PATH . "/assets/images/brands/" . $imgName;

        if (strpos($fileType, 'image') !== false) {
            if (!is_dir(ROOT_PATH . "/assets/images/brands")) {
                mkdir(ROOT_PATH . "/assets/images/brands", 0777, true);
            }
            move_uploaded_file($fileTmpName, $destination);
            $logoName = $imgName;
        }
    }

    if ($name === '') {
        $errMsg = "РќР°Р·РІР°РЅРёРµ Р±СЂРµРЅРґР° РЅРµ РјРѕР¶РµС‚ Р±С‹С‚СЊ РїСѓСЃС‚С‹Рј!";
    } elseif (mb_strlen($name, 'UTF8') < 2) {
        $errMsg = "РќР°Р·РІР°РЅРёРµ Р±СЂРµРЅРґР° РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ";
    } else {
        $existence = selectOne('brands', ['name' => $name]);
        if ($existence && $existence['name'] === $name) {
            $errMsg = "РўР°РєРѕР№ Р±СЂРµРЅРґ СѓР¶Рµ РµСЃС‚СЊ РІ Р±Р°Р·Рµ";
        } else {
            $brand = [
                'name' => $name,
                'logo' => $logoName,
                'country' => $country
            ];
            $id = insert('brands', $brand);
            header('location: ' . BASE_URL . 'admin/brands/index.php');
        }
    }
} else {
    $name = '';
    $country = '';
}

// РђРїРґРµР№С‚ Р±СЂРµРЅРґР°
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $brand = selectOne('brands', ['id' => $id]);
    $id = $brand['id'];
    $name = $brand['name'];
    $logo = $brand['logo'];
    $country = $brand['country'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand-edit'])) {
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);

    // Р—Р°РіСЂСѓР·РєР° РЅРѕРІРѕРіРѕ Р»РѕРіРѕС‚РёРїР°
    $logoName = $_POST['current_logo'] ?? '';
    if (!empty($_FILES['logo']['name'])) {
        $imgName = time() . "_" . $_FILES['logo']['name'];
        $fileTmpName = $_FILES['logo']['tmp_name'];
        $fileType = $_FILES['logo']['type'];
        $destination = ROOT_PATH . "/assets/images/brands/" . $imgName;

        if (strpos($fileType, 'image') !== false) {
            if (!is_dir(ROOT_PATH . "/assets/images/brands")) {
                mkdir(ROOT_PATH . "/assets/images/brands", 0777, true);
            }
            move_uploaded_file($fileTmpName, $destination);
            $logoName = $imgName;
        }
    }

    if ($name === '') {
        $errMsg = "РќР°Р·РІР°РЅРёРµ Р±СЂРµРЅРґР° РЅРµ РјРѕР¶РµС‚ Р±С‹С‚СЊ РїСѓСЃС‚С‹Рј!";
    } elseif (mb_strlen($name, 'UTF8') < 2) {
        $errMsg = "РќР°Р·РІР°РЅРёРµ Р±СЂРµРЅРґР° РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ Р±РѕР»РµРµ 2-С… СЃРёРјРІРѕР»РѕРІ";
    } else {
        $brand = [
            'name' => $name,
            'logo' => $logoName,
            'country' => $country
        ];
        $id = $_POST['id'];
        update('brands', $id, $brand);
        header('location: ' . BASE_URL . 'admin/brands/index.php');
    }
}

// РЈРґР°Р»РµРЅРёРµ Р±СЂРµРЅРґР°
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    delete('brands', $id);
    header('location: ' . BASE_URL . 'admin/brands/index.php');
}
