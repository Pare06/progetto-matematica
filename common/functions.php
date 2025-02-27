<?php
    require_once "config.php";

    function check_signup($nome, $cognome, $email, $password, $tipoAccount) {
        if ($nome == null || empty($nome)) {
            return "Inserisci il nome!";
        }
        if ($cognome == null || empty($cognome)) {
            return "Inserisci il cognome!";
        }
        if ($email == null || empty($email)) {
            return "Inserisci l'indirizzo email!";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Indirizzo email non valido!";
        }
        if ($password == null || empty($password)) {
            return "Inserisci la password!";
        }
        if ($tipoAccount == null || ($tipoAccount != 1 && $tipoAccount != 0)) {
            return "Tipo account non valido!";
        }

        return "";
    }

    function get_id_from_email($email, $table) {
        global $conn;
        $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()["id"] ?? null;
    }

    function is_logged_with_google($email, $table) {
        global $conn;
        $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ? AND password IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function email_already_exists($email) {
        global $conn;
        $stmt = $conn->prepare("SELECT 1 FROM studenti WHERE email = ? UNION SELECT 1 FROM professori WHERE email = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function fetch_error() {
        global $error;
        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            unset($_SESSION["error"]);
        }
    }

    function set_error_and_refresh($msg) {
        if (!empty($msg)) {
            $_SESSION["error"] = $msg;
            header("Location: " .$_SERVER["HTTP_REFERER"]);
            exit;
        }
    }

    function load_teacher() {
        global $conn;
        $stmt = $conn->prepare("SELECT nome, cognome, foto, email FROM professori WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["id"]);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows == 0) { // acc eliminato
            session_unset();
            session_destroy();
            header("Location: /PROGETTO MATTO/signup");
            exit;
        }

        return $res->fetch_assoc();
    }