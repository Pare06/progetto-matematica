<?php
    require_once "imports.php";
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" value="" placeholder="Inserisci email" required><br>
        <label for="pwd">Password</label>
        <input type="password" value="" placeholder="Inserisci password" required><br>
        <label for="remember">Ricordami</label>
        <input type="checkbox" name="remember"><br><br>
        <input type="submit" value="Accedi">
    
    </form>
</body>
</html>