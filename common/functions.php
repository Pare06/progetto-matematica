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
        $stmt2 = $conn->prepare("SELECT id FROM $table WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        return $stmt2->get_result()->fetch_assoc()["id"];
    }