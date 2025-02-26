<?php
    require_once "./google-api-php-client/vendor/autoload.php";

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

    const GOOGLE_CLIENTID = "286996823248-cqpbhaebapn9ompaefpnjl4n05732h8u";
    const GOOGLE_SECRET = "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";
    const GOOGLE_REDIRECTURI = "https://e559-151-43-171-76.ngrok-free.app/PROGETTO%20MATTO/set_account_type";

    $googleClient = new Google_Client();
    $googleClient->setClientId(GOOGLE_CLIENTID);
    $googleClient->setClientSecret(GOOGLE_SECRET);
    $googleClient->setRedirectUri(GOOGLE_REDIRECTURI);
    $googleClient->addScope('email');
    $googleClient->addScope('profile');
    $googleOauth = new Google_Service_Oauth2($googleClient);