<?php
    session_start();

    require_once __DIR__ ."/../common/imports.php";

    $accData = load_teacher();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php require_once "common/layout.php" ?>
    chi si chiama <?=$accData["nome"] ." ". $accData["cognome"]?> è tipo super gay
    <br>
    e se hai questa foto:
    <br>
    <img src="<?=$accData["foto"]?>" />
    <br>
    sei ancora più gay
</body>
</html>