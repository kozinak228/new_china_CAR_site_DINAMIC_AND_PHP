<?php

// Функция для безопасной загрузки и обрезки изображения (Cover-эффект)
function uploadAndCropImage($fileInfo, $destinationFolder, $targetWidth = 800, $targetHeight = 600)
{
    if (!isset($fileInfo['error']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $tmpName = $fileInfo['tmp_name'];

    // Проверяем реальный MIME-тип
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        return false;
    }

    $ext = '';
    switch ($mimeType) {
        case 'image/jpeg':
            $ext = '.jpg';
            break;
        case 'image/png':
            $ext = '.png';
            break;
        case 'image/webp':
            $ext = '.webp';
            break;
    }

    // Если GD нет - просто безопасный перенос
    if (!function_exists('imagecreatetruecolor')) {
        $newName = time() . "_" . bin2hex(random_bytes(4)) . $ext;
        $targetPath = rtrim($destinationFolder, '/') . '/' . $newName;
        if (move_uploaded_file($tmpName, $targetPath)) {
            return $newName;
        }
        return false;
    }

    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($tmpName);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($tmpName);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($tmpName);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    $srcWidth = imagesx($sourceImage);
    $srcHeight = imagesy($sourceImage);

    // Вычисляем размеры для обрезки
    $ratio = max($targetWidth / $srcWidth, $targetHeight / $srcHeight);
    $scaledWidth = (int) ($srcWidth * $ratio);
    $scaledHeight = (int) ($srcHeight * $ratio);

    $offsetX = (int) (($scaledWidth - $targetWidth) / 2);
    $offsetY = (int) (($scaledHeight - $targetHeight) / 2);

    $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

    // Сохраняем прозрачность
    if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
    }

    imagecopyresampled($targetImage, $sourceImage, -$offsetX, -$offsetY, 0, 0, $scaledWidth, $scaledHeight, $srcWidth, $srcHeight);

    $newName = time() . "_" . bin2hex(random_bytes(4)) . $ext;
    $targetPath = rtrim($destinationFolder, '/') . '/' . $newName;

    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($targetImage, $targetPath, 65);
            break;
        case 'image/png':
            $success = imagepng($targetImage, $targetPath, 8);
            break;
        case 'image/webp':
            $success = imagewebp($targetImage, $targetPath, 65);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($targetImage);

    return $success ? $newName : false;
}
