<?php
    session_start();
    if (isset($_SESSION["id"])) {
        $tipo = $_SESSION["tipo"] == 1 ? "professore" : "studente";
        header("Location: $tipo/");
    } else {
        header("Location: signup.php");
    }