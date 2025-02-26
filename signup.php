<?php
    session_start();

    require_once "./common/imports.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST["nome"] ?? null;
        $cognome = $_POST["cognome"] ?? null;
        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;
        $tipoAccount = $_POST["tipo"] ?? null; // 0 studente, 1 prof

        $error = check_signup($nome, $cognome, $email, $password, $tipoAccount);

        if ($error == "") {
            $table = $tipoAccount == 1 ? "professori" : "studenti";
            $password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Un account con questa email esiste già!";
            } else {
                $stmt1 = $conn->prepare("INSERT INTO $table (nome, cognome, email, password) VALUES (?, ?, ?, ?)");
                $stmt1->bind_param("ssss", $nome, $cognome, $email, $password);
                $stmt1->execute();

                $stmt2 = $conn->prepare("SELECT id FROM $table WHERE email = ?");
                $stmt2->bind_param("s", $email);
                $stmt2->execute();
                $id = $stmt2->get_result()->fetch_assoc()["id"];
            
                $_SESSION["id"] = $id;
                $_SESSION["tipo"] = $tipoAccount;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati</title>
    <link rel="stylesheet" href="./stylesheets/auth.css" />
</head>
<body>
    <form action="./signup" method="POST">
        <!-- slider studente/prof che farà il signorino (chi è il signorino?) -->

        <div class="container">
            <div class="container_sections">
                <div class="container_sections_signup">
                    <h1>Registrati</h1>
                    <input type="hidden" name="tipo" value="1" />
                    <input type="text" name="nome" placeholder="Nome *" required />
                    <input type="text" name="cognome" placeholder="Cognome *" required />
                    <input type="text" name="email" placeholder="Indirizzo email *" required />
                    <input type="password" name="password" id="password *" placeholder="Password *" required />
                    <input type="password" name="confermapass" id="conferma" placeholder="Conferma password *" required />
                    <input class="submit" type="submit" value="Registrati" />
                </div>
                <div class="container_sections_login">

                </div>
            </div>
            <!---->
        </div>
    </form>
</body>
</html>