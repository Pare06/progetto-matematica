<?php
    session_start();

    require_once "./common/imports.php";

    fetch_error();

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

            if (email_already_exists($email)) {
                set_error_and_refresh("Un account con questa email esiste già!");
            } else {
                $stmt1 = $conn->prepare("INSERT INTO $table (nome, cognome, email, password) VALUES (?, ?, ?, ?)");
                $stmt1->bind_param("ssss", $nome, $cognome, $email, $password);
                $stmt1->execute();

                $id = get_id_from_email($email, $table);
            
                $_SESSION["id"] = $id;
                $_SESSION["tipo"] = $tipoAccount;
                header("Location: ./" .($tipoAccount == 1 ? "professore" : "studente"));
                exit;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scapegoat | Registrati</title>
    <link rel="stylesheet" href="./stylesheets/auth.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="QuanSlim-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="QuanSlim-ExtraBold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="QuanSlim-Italic.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="QuanSlim-Bold.woff2" as="font" type="font/woff2" crossorigin>
    
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="./js/show_hide_psw.js"></script>
    <script src="./js/theme_switcher.js"></script>
</head>
<body>
    <!-- $error -->
    <form action="./signup" method="POST">
        <div class="container">
            <div class="container_vertical">
                <div class="container_upper">
                    <div class="container_sections">
                        <div class="container_sections_signup">
                            <h1>Registrati</h1>
                            <input type="hidden" name="tipo" value="1" /> <!-- (leva quando c'è lo slider) -->
                            <input type="text" name="nome" placeholder="Nome *" required />
                            <input type="text" name="cognome" placeholder="Cognome *" required />
                            <input type="text" name="email" placeholder="Indirizzo email *" required />
                            <div class="password-container">
                                <input type="password" name="password" id="password" placeholder="Password *" required />
                                <span class="toggle-password" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="password-container">
                                <input type="password" name="confermapass" id="conferma" placeholder="Conferma password *" required />
                                <span class="toggle-password" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                            </div>
                            <p class="error"><?= $error ?></p>
                            <input class="submit" type="submit" value="Registrati" />
                            <span class="line-with-text">ㅤOppureㅤ</span>
                            <a href="<?= $login_url ?? '#' ?>"><button class="submit" type="button"><i class="fa-brands fa-google"></i>ㅤAccedi con Google</button></a>
                        </div>
                        <div class="container_sections_login">
                            <dotlottie-player src="https://lottie.host/b297b8f7-58c5-4aeb-aa03-a287c257bf17/nagvWrpn7y.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
                            <span class="title">Benvenuto in <span class="highlight">Scapegoat</span>!</span>
                            <span class="line-with-text">ㅤHai già un account?ㅤ</span>
                            <a href="./login"><button class="submit" type="button">Accedi</button></a>
                        </div>
                    </div>
                </div>
                <div class="container_lower">
                    <span><i id="theme" class="fa-solid fa-sun"></i></span>
                </div>
            </div>
        </>
    </form>
</body>
</html>