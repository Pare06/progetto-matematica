<?php
    session_start();

    require_once 'google-api-php-client/vendor/autoload.php';

    $client = new Google_Client();
    $client->setClientId('286996823248-cqpbhaebapn9ompaefpnjl4n05732h8u');
    $client->setClientSecret('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');
    $client->setRedirectUri('https://1015-151-43-158-18.ngrok-free.app/PROGETTO%20MATTO/test');
    $client->addScope('email');
    $client->addScope('profile');

    $oauth = new Google_Service_Oauth2($client);

    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
        header('Location: index.php');
        exit;
    }

    if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
    }

    if ($client->getAccessToken()) {
        $user = $oauth->userinfo->get();
        $google_user = ['name' => $user['name'], 'email' => $user['email'], 'picture' => $user['picture']];
    } else {
        $login_url = $client->createAuthUrl();
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    if (isset($_SESSION['google_user'])) {
        $stmt = "SELECT "
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <?php if (isset($_SESSION['google_user'])): ?>
        <h2>Welcome, <?= htmlspecialchars($google_user['name']) ?></h2>
        <img src="<?= $google_user['picture'] ?>" width="100"><br>
        <a href="?logout">Logout</a>
    <?php else: ?>
        <a href="<?= $login_url ?? '#' ?>">Login with Google</a>
    <?php endif; ?>
</body>
</html>