<?php
//Script Author: phccoder @PHCC0D3r
// It handles config, session, https, and themes.
include 'init.php';

// 1. CHECK FOR A LOGIN ATTEMPT (form submission)
if ($forceAuth && isset($_POST['auth'])) {
    $submitted_pass = $_POST['authpass'];
    if ($submitted_pass !== "" && password_verify($submitted_pass, $AuthPass_Hashed)) {
        session_regenerate_id(true);
        $_SESSION['Auth'] = 'allowed';
        header("location: ./");
        exit();
    }
}

// 2. CHECK IF USER IS ALREADY LOGGED IN
if ($forceAuth && isset($_SESSION['Auth']) && $_SESSION['Auth'] === 'allowed') {
    header("location: ./");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
	<meta charset="utf-8">
	<title><?php echo $site_name;?> | Access</title>
	<meta name="author" content="<?php echo $owner ?>">
	<link rel="icon" href="<?php echo $site_icon; ?>" type="image/png">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php echo $owner ?> CC Checker">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="./assets/css/style.css"/>
</head>
<body>
	<div class="bg-gradient"></div>

	<div class="auth-container">
		<div class="glass auth-card">
			<img class="avatar" src="<?php echo $site_icon; ?>" alt="logo">
			<h2><?php echo $site_name; ?></h2>
			<p>Please enter your access pass to continue.</p>
			<form method="POST">
				<input type="password" class="glass-input" id="authpass" name="authpass" placeholder="Auth Pass" autocomplete="off">
				<button style="margin-top: 4px;" type="submit" name="auth" class="btn btn-accent btn-block">ACCESS</button>
			</form>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
	<script src="./assets/js/main.js"></script>
</body>
</html>
