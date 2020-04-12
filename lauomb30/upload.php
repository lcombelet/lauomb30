<?php
// SOURCE + DESTINATION
$source = $_FILES["file-upload"]["tmp_name"];
$destination = "uploads/" . $_FILES["file-upload"]["name"];
$error = "";

// CHECK IF FILE ALREADY EXIST
if (file_exists($destination)) {
  $error = $destination." already exist.";
}

// ALLOWED FILE EXTENSIONS
if ($error == "") {
  $allowed = ["7z", "gif", "jpeg", "jpg", "png", "rar", "zip"];
  $ext = strtolower(pathinfo($_FILES["file-upload"]["name"], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed)) {
    $error = "$ext file type not allowed - " . $_FILES["file-upload"]["name"];
  }
}

// LEGIT IMAGE FILE CHECK
if ($error == "") {
  if (getimagesize($_FILES["file-upload"]["tmp_name"]) == false) {
    $error = $_FILES["file-upload"]["name"] . " is not a valid image file.";
  }
}

// FILE SIZE CHECK
if ($error == "") {
  // 1,000,000 = 1MB
  $allowedMB = 50;
  $allowedBytes = $allowedMB * (1024 ** 2);
  if ($_FILES["file-upload"]["size"] > $allowedBytes) {
    $error = $_FILES["file-upload"]["name"] . " - file size too big!";
  }
}

// ALL CHECKS OK - MOVE FILE
if ($error == "") {
  if (!move_uploaded_file($source, $destination)) {
    $error = "Error moving $source to $destination";
  }
}

// ERROR OCCURED OR OK?
if ($error == "") {
  echo $_FILES["file-upload"]["name"] . " upload OK";
} else {
  echo $error;
}
?>
