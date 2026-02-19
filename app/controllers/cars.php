<?php

include_once SITE_ROOT . "/app/database/db.php";
if (!$_SESSION) {
    header('location: ' . BASE_URL . 'log.php');
}

$errMsg = [];
$id = '';
$title = '';
$description = '';
$img = '';
$brand = '';

$brands = selectAll('brands');
$carsAdm = selectAllCarsWithBrands();

// РЎРѕР·РґР°РЅРёРµ Р°РІС‚Рѕ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {

    // Р“Р»Р°РІРЅРѕРµ С„РѕС‚Рѕ
    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileType = $_FILES['img']['type'];
        $destination = ROOT_PATH . "/assets/images/cars/" . $imgName;

        if (strpos($fileType, 'image') === false) {
            array_push($errMsg, "РџРѕРґРіСЂСѓР¶Р°РµРјС‹Р№ С„Р°Р№Р» РЅРµ СЏРІР»СЏРµС‚СЃСЏ РёР·РѕР±СЂР°Р¶РµРЅРёРµРј!");
        } else {
            if (!is_dir(ROOT_PATH . "/assets/images/cars")) {
                mkdir(ROOT_PATH . "/assets/images/cars", 0777, true);
            }
            $result = move_uploaded_file($fileTmpName, $destination);
            if ($result) {
                $_POST['img'] = $imgName;
            } else {
                array_push($errMsg, "РћС€РёР±РєР° Р·Р°РіСЂСѓР·РєРё РёР·РѕР±СЂР°Р¶РµРЅРёСЏ РЅР° СЃРµСЂРІРµСЂ");
            }
        }
    } else {
        $_POST['img'] = '';
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $brand = trim($_POST['brand']);
    $publish = isset($_POST['publish']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;

    if ($title === '' || $brand === '') {
        array_push($errMsg, "Не все обязательные поля заполнены!");
    } elseif (mb_strlen($title, 'UTF8') < 3) {
        array_push($errMsg, "Название авто должно быть более 3-х символов");
    } else {
        $car = [
            'id_user' => $_SESSION['id'],
            'id_brand' => $brand,
            'title' => $title,
            'price' => $_POST['price'] ?? 0,
            'year' => $_POST['year'] ?? date('Y'),
            'mileage' => $_POST['mileage'] ?? 0,
            'engine_type' => $_POST['engine_type'] ?? '',
            'engine_volume' => $_POST['engine_volume'] ?? 0,
            'horsepower' => $_POST['horsepower'] ?? 0,
            'transmission' => $_POST['transmission'] ?? '',
            'drive_type' => $_POST['drive_type'] ?? '',
            'body_type' => $_POST['body_type'] ?? '',
            'color' => $_POST['color'] ?? '',
            'description' => $description,
            'img' => $_POST['img'],
            'status' => $publish,
            'featured' => $featured
        ];

        $carId = insert('cars', $car);

        // Загрузка дополнительных фото (галерея)
        if (!empty($_FILES['gallery']['name'][0])) {
            if (!is_dir(ROOT_PATH . "/assets/images/cars")) {
                mkdir(ROOT_PATH . "/assets/images/cars", 0777, true);
            }
            foreach ($_FILES['gallery']['name'] as $key => $galleryName) {
                if (!empty($galleryName)) {
                    $galleryImgName = time() . "_" . $key . "_" . $galleryName;
                    $galleryTmpName = $_FILES['gallery']['tmp_name'][$key];
                    $galleryType = $_FILES['gallery']['type'][$key];
                    $galleryDest = ROOT_PATH . "/assets/images/cars/" . $galleryImgName;

                    if (strpos($galleryType, 'image') !== false) {
                        move_uploaded_file($galleryTmpName, $galleryDest);
                        insertCarImage($carId, $galleryImgName, $key);
                    }
                }
            }
        }

        header('location: ' . BASE_URL . 'admin/cars/index.php');
    }
} else {
    $id = '';
    $title = '';
    $description = '';
    $publish = '';
    $brand = '';
}

// Загрузка данных авто для редактирования
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $car = selectOne('cars', ['id' => $_GET['id']]);
    $id = $car['id'];
    $title = $car['title'];
    $description = $car['description'];
    $brand = $car['id_brand'];
    $publish = $car['status'];
}

// Сохранение редактирования авто
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_car'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $brand = trim($_POST['brand']);
    $publish = isset($_POST['publish']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Новое главное фото
    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileType = $_FILES['img']['type'];
        $destination = ROOT_PATH . "/assets/images/cars/" . $imgName;

        if (strpos($fileType, 'image') !== false) {
            if (!is_dir(ROOT_PATH . "/assets/images/cars")) {
                mkdir(ROOT_PATH . "/assets/images/cars", 0777, true);
            }
            move_uploaded_file($fileTmpName, $destination);
            $_POST['img'] = $imgName;
        }
    } else {
        $_POST['img'] = $_POST['current_img'] ?? '';
    }

    if ($title === '' || $brand === '') {
        array_push($errMsg, "Не все обязательные поля заполнены!");
    } elseif (mb_strlen($title, 'UTF8') < 3) {
        array_push($errMsg, "Название авто должно быть более 3-х символов");
    } else {
        $car = [
            'id_user' => $_SESSION['id'],
            'id_brand' => $brand,
            'title' => $title,
            'price' => $_POST['price'] ?? 0,
            'year' => $_POST['year'] ?? date('Y'),
            'mileage' => $_POST['mileage'] ?? 0,
            'engine_type' => $_POST['engine_type'] ?? '',
            'engine_volume' => $_POST['engine_volume'] ?? 0,
            'horsepower' => $_POST['horsepower'] ?? 0,
            'transmission' => $_POST['transmission'] ?? '',
            'drive_type' => $_POST['drive_type'] ?? '',
            'body_type' => $_POST['body_type'] ?? '',
            'color' => $_POST['color'] ?? '',
            'description' => $description,
            'img' => $_POST['img'],
            'status' => $publish,
            'featured' => $featured
        ];

        update('cars', $id, $car);

        // Загрузка новых фото в галерею
        if (!empty($_FILES['gallery']['name'][0])) {
            if (!is_dir(ROOT_PATH . "/assets/images/cars")) {
                mkdir(ROOT_PATH . "/assets/images/cars", 0777, true);
            }
            foreach ($_FILES['gallery']['name'] as $key => $galleryName) {
                if (!empty($galleryName)) {
                    $galleryImgName = time() . "_" . $key . "_" . $galleryName;
                    $galleryTmpName = $_FILES['gallery']['tmp_name'][$key];
                    $galleryType = $_FILES['gallery']['type'][$key];
                    $galleryDest = ROOT_PATH . "/assets/images/cars/" . $galleryImgName;

                    if (strpos($galleryType, 'image') !== false) {
                        move_uploaded_file($galleryTmpName, $galleryDest);
                        insertCarImage($id, $galleryImgName, $key);
                    }
                }
            }
        }

        header('location: ' . BASE_URL . 'admin/cars/index.php');
    }
} else {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $publish = isset($_POST['publish']) ? 1 : 0;
        $brand = $_POST['brand'];
    }
}

// РџСѓР±Р»РёРєР°С†РёСЏ / СЃРЅСЏС‚РёРµ СЃ РїСѓР±Р»РёРєР°С†РёРё
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
    $id = $_GET['pub_id'];
    $publish = $_GET['publish'];
    update('cars', $id, ['status' => $publish]);
    header('location: ' . BASE_URL . 'admin/cars/index.php');
    exit();
}

// РЈРґР°Р»РµРЅРёРµ Р°РІС‚Рѕ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    delete('cars', $id);
    header('location: ' . BASE_URL . 'admin/cars/index.php');
}

// РЈРґР°Р»РµРЅРёРµ С„РѕС‚Рѕ РёР· РіР°Р»РµСЂРµРё
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del_img_id'])) {
    $imgId = $_GET['del_img_id'];
    $carId = $_GET['car_id'] ?? '';
    deleteCarImage($imgId);
    header('location: ' . BASE_URL . 'admin/cars/edit.php?id=' . $carId);
}
