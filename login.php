<?php
    session_start();

    require_once "./common/imports.php";

    $login_url = $googleClient->createAuthUrl();

    fetch_error();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;

        if ($email == null || $password == null) {
            set_error_and_refresh("Credenziali non inserite!");
        }
        
        if (!isset($error)) {
            $idStudente = get_id_from_email($email, "studenti");
            $idProfessore = get_id_from_email($email, "professori");
            
            if ($idStudente == null && $idProfessore == null) {
                set_error_and_refresh("Credenziali errate o non esistenti!");
            } else {
                $table = $idStudente == null ? "professori" : "studenti";
                $id = $idStudente ?? $idProfessore;
                
                $isGoogle = is_logged_with_google($email);
                if ($isGoogle) {
                    set_error_and_refresh("Questo account è stato registrato con Google!");
                } else {
                    $stmtCheck = $conn->prepare("SELECT password FROM $table WHERE email = ?");
                    $stmtCheck->bind_param("s", $email);
                    $stmtCheck->execute();
                    $storedPass = $stmtCheck->get_result()->fetch_assoc()["password"];
                    if (password_verify($password, $storedPass)) {
                        $_SESSION["id"] = $id;
                        $_SESSION["tipo"] = $idStudente == null ? 1 : 0;
                        header("Location: ./" .($table == "professori" ? "professore" : "studente"));
                        exit;
                    } else {
                        $error = "Credenziali errate o non esistenti!";
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scapegoat | Accedi</title>
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
    <form action="./login" method="POST">
        <div class="container">
            <div class="container_vertical">
                <div class="container_upper">
                    <div class="container_sections">
                        <div class="container_sections_signup">
                            <h1>Accedi</h1>
                            <input type="text" name="email" placeholder="Indirizzo email" required />
                            <div class="password-container">
                                <input type="password" name="password" id="password" placeholder="Password" required />
                                <span class="toggle-password" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                            </div>
                            <p class="error"><?= $error ?></p>
                            <input class="submit" type="submit" value="Accedi" />
                            <span class="line-with-text">ㅤOppureㅤ</span>
                            <a href="<?= $login_url ?? '#' ?>"><button class="submit" type="button"><i class="fa-brands fa-google"></i>ㅤAccedi con Google</button></a>
                        </div>
                        <div class="container_sections_login">
                        <dotlottie-player src="https://lottie.host/b297b8f7-58c5-4aeb-aa03-a287c257bf17/nagvWrpn7y.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
                            <span class="title">Bentornato in <span class="highlight">Scapegoat</span>!</span>
                            <span class="line-with-text">ㅤNon hai un account?ㅤ</span>
                            <a href="./signup"><button class="submit" type="button">Registrati</button></a>
                        </div>
                    </div>
                </div>
            <div class="container_lower">
                    <span><i id="theme" class="fa-solid fa-sun"></i></span>
            </div>
            <!---->
        </div>
    </form>
</body>
</html>