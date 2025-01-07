<?php

function fetchPagedMessages()
{
    $db = db();

    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;
    $count = 5;
    $start = ($page * $count) - $count;

    $res = mysqli_query($db, "SELECT COUNT(*) FROM `messages`");
    $row = mysqli_fetch_row($res);
    $total = $row[0];

    $totalPages = ceil($total / $count);

    for ($i = 1; $i <= $totalPages; $i++){
        echo "<a href=index.php?page=".$i."> Страница ".$i." </a>";
    }

    return mysqli_query($db,
    "SELECT username, email, text, m.create_date AS 'created', filePath
            FROM `messages` m JOIN `user` u ON m.user_id = u.id
            ORDER BY 4 DESC
            LIMIT $start, $count;");
}


function createMessage(string $message, string $savedfilePath = NULl): bool
{
    $db = db();
    $savedfilePath = mysqli_escape_string($db, $savedfilePath);
    $message = mysqli_escape_string($db, $message);
    return  mysqli_query($db, "INSERT INTO messages (text, user_id, filePath) VALUES ('$message', 1, '$savedfilePath');");
}

function addFile(&$error, &$savedfilePath): bool
{
    $allowedTextExtensions = ['txt'];
    $allowedImageExtensions = ['jpg', 'jpeg', 'gif', 'png'];
    $maxFileSize = 100 * 1024;
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/userfiles/';

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    if (!in_array($fileExtension, array_merge($allowedTextExtensions, $allowedImageExtensions))) {
        $error .= 'Unexpected file type! Allowed: TXT, JPG, GIF, PNG.';
        return false;
    } elseif ($fileSize > $maxFileSize) {
        $error .= 'File size exceeds limit of 100KB!';
        return false;
    }

    if (in_array($fileExtension, $allowedImageExtensions)) {
        list($width, $height) = getimagesize($fileTmpPath);
        if ($width > 320 || $height > 240) {
            $error .= "Image exceeds allowed resolution. Allowed resolution: 320x240!";
            return false;
        }
    }

    $storageFileName = time() . " - " . $fileName;
    $storageFilePath = $uploadPath . $storageFileName;
    if (!move_uploaded_file($fileTmpPath, $storageFilePath)) {
        $error .= "Error saving the file!";
        return false;
    } else {
        $savedfilePath = $storageFileName;
    }

    return true;
}
