<?php
    session_start();

    require_once "./common/imports.php";

    $login_url = $googleClient->createAuthUrl();

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
            $nome .= " $cognome";

            $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Un account con questa email esiste già!";
            } else {
                $stmt1 = $conn->prepare("INSERT INTO $table (nome, email, password) VALUES (?, ?, ?)");
                $stmt1->bind_param("sss", $nome, $email, $password);
                $stmt1->execute();

                $id = get_id_from_email($email, $table);
            
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
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
                    <input type="password" name="password" id="password" placeholder="Password *" required />
                    <input type="password" name="confermapass" id="conferma" placeholder="Conferma password *" required />
                    <input class="submit" type="submit" value="Registrati" />
                    <span class="line-with-text">ㅤOppureㅤ</span>
                    <a href="<?= $login_url ?? '#' ?>"><button class="submit" type="button"><i class="fa-brands fa-google"></i>ㅤAccedi con Google</button></a>
                </div>
                <div class="container_sections_login">
                <dotlottie-player src="https://lottie.host/b297b8f7-58c5-4aeb-aa03-a287c257bf17/nagvWrpn7y.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
                </div>
            </div>
            <!---->
        </div>
    </form>
</body>
</html>