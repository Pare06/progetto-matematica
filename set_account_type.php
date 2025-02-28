<?php
    session_start();

    require_once "./common/imports.php";
    require_once "./google-api-php-client/vendor/autoload.php";

    $oauthCode = $_GET["code"] ?? null;
    
    if ($oauthCode == null) {
        header("Location: index.php");
        exit;
    }

    $token = $googleClient->fetchAccessTokenWithAuthCode($oauthCode);
    
    if (isset($token["error"])) {
        $token = $_SESSION['access_token'];
    } else {
        $_SESSION['access_token'] = $token;
    }
    
    $googleClient->setAccessToken($token);
    $user = $googleOauth->userinfo->get();
    
    $name = $user->getGivenName();
    $surname = $user->getFamilyName();
    $email = $user->getEmail();
    $photo = $user->getPicture();
    
    if (isset($email) && email_already_exists($email)) {
        if (is_logged_with_google($email)) {
            $tabella = get_table_from_email($email);
            $_SESSION["id"] = get_id_from_email($email, $tabella);
            $_SESSION["tipo"] = acctype_from_table($tabella);
            
            header("Location: /PROGETTO MATTO/");
            die();
        } else {
            $_SESSION["error"] = "Un account con questa email esiste già!";
            header("Location: signup");
            die();
        }
    }
    
    if (isset($_GET["studente"])) {
        $tabella = "studenti";
    } else if (isset($_GET["professore"])) {
        $tabella = "professori";
    }
    
    if (isset($tabella)) {
        $stmt = $conn->prepare("INSERT INTO $tabella (nome, cognome, foto, email) VALUES (?, ?, ?, ?)");
        echo $name, $surname, $photo, $email;

        $stmt->bind_param("ssss", $name, $surname, $photo, $email);
        $stmt->execute();
    
        $stmt2 = $conn->prepare("SELECT id FROM $tabella WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $id = $stmt2->get_result()->fetch_assoc()["id"];
    
        $_SESSION["id"] = $id;
        $_SESSION["tipo"] = $tabella == "studenti" ? 0 : 1;
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scegli tipo account</title>
</head>
<body>
    <h1>Sei uno studente o un professore?</h1>
    <br>
    <a href="?studente=1&code=<?=$oauthCode?>">Studente</a>
    <br>
    <a href="?professore=1&code=<?=$oauthCode?>">Professore</a>
</body>
</html>