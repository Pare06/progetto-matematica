<?php
    $conn_config = [
        "host" => "localhost",
        "user" => "root",
        "pass" => null,
        "db" => "piattaforma_matematica"
    ];

    mysqli_report(MYSQLI_REPORT_OFF);

    $conn = new mysqli($conn_config["host"], $conn_config["user"], $conn_config["pass"], $conn_config["db"]);
    if ($conn->connect_errno) {
        $conn = null;
    }
