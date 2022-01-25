<?php

/**
 * Генерирурет уникальное имя загруженному файлу и
 * переносит его из временной папки в папку проекта
 * @return string|null путь загруженного файла или null
 */
function saveFile(): ?string
{
    $path = $_FILES["file"]["tmp_name"];
    $filename = uniqid() . "__" . $_FILES["file"]["name"];

    $isMoved = move_uploaded_file($path, "uploads/" . $filename);

    if ($isMoved === false) {
        return null;
    }

    return "uploads/" . $filename;
}
