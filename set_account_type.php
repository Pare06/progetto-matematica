<?php
    session_start();

    require_once "./common/imports.php";
    require_once "./google-api-php-client/vendor/autoload.php";

    $oauthCode = $_GET["code"] ?? null;
    $studente = $_GET["studente"] ?? null;
    $professore = $_GET["professore"] ?? null;

    if ($oauthCode == null) {
        header("Location: index.php");
        exit;
    }

    if ($studente == 1) {
        $tabella = "studenti";
    } else if ($professore == 1) {
        $tabella = "professori";
    }

    if (isset($tabella)) {
        if (isset($_GET['code'])) {
            $googleClient->authenticate($_GET['code']);
            $user = $googleOauth->userinfo->get();
            
            $name = $user->getName();
            $email = $user->getEmail();
            if (email_already_exists($email)) { // NON VA DIO CANE
                $_SESSION["error"] = "Un account con questa email esiste già!";
                header("Location: signup");
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO $tabella (nome, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email); 
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