<?php
//Script Author: phccoder @PHCC0D3r
// It handles config, session, https, and themes.
include 'init.php';

// 1. CHECK FOR A LOGIN ATTEMPT (form submission)
if ($forceAuth && isset($_POST['auth'])) {
    $submitted_pass = $_POST['authpass'];
    if (password_verify($submitted_pass, $AuthPass_Hashed)) {
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
<html lang="en">
<head>
	<!-- BASIC DATA -->
	<meta charset="utf-8">
	<title><?php echo $site_name;?></title>
	<meta name="author" content="<?php echo $owner ?>">
	<link rel="icon" href="<?php echo $site_icon; ?>" type="image/png">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php echo $owner ?> CC Checker">
	<!-- BOOTSTRAP -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Gruppo&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="./assets/css/jquery.ambiance.css"/>
	<style type="text/css">
		.ambiance-custom {
			background: <?php echo $theme_background_opp;?>;
			color: <?php echo $theme_text_opp;?>;
			padding: 10px;
			border-radius: 5px;
			-moz-border-radius: 5px; /* Firefox 3.6 and earlier. */
			margin: 10px;
		}
		.ambiance:hover {
			border: 3px solid <?php echo $theme_background;?>;
		}
		.ambiance-close:hover {
			color: <?php echo $theme_background;?>;
			cursor: pointer;
		}
		.ambiance-close {
			display: block;
			position: relative;
			top: -2px;
			right: 0px;
			color: <?php echo $theme_text_opp;?>;
			float: right;
			font-size: 18px;
			font-weight: bold;
			filter: alpha(opacity=20);
			text-decoration: none;
			position: relative;
			line-height: 14px;
			margin-left: 5px;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
		input[type=text]:focus,select[type=text]:focus,input[type=number]:focus,textarea[type=text]:focus{
			border: 1px solid #dc3545;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
		}
	</style>
</head>
<body style="background: <?php echo $theme_background ?>;">
	<div class="container" id="container">
		<!-- START OF IMAGE HEADER -->
		<div class="row justify-content-md-center">
			<div class="col-md">
				<center>
					<img class="rounded-circle" src="<?php echo $site_icon; ?>" width="200" height="200" style="margin-top: 40px;">
				</center>
			</div>
		</div>
		<!-- END OF IMAGE HEADER -->
		<!-- START OF FORMS -->
		<div class="row justify-content-md-center" style="margin-top: 40px;">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<form method="POST">
					<div class="form-group">
						<div class="input-group mb-3 ">
							<input type="password" class="form-control" style="border-color: #dc3545;background: transparent;color: <?php echo $theme_text ?>;" id="authpass" name="authpass" placeholder="Auth Pass">
						</div>
					</div>
					<button style="margin-top: 20px" type="submit" name="auth" class="btn btn-outline-danger btn-block">ACCESS</button>
				</form>
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
	
	<!-- BOOTSTRAP PLUGIN SCRIPTS-->
	<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- CHECKER PLUGIN SCRIPTS-->
	<script src="./assets/js/jquery.ambiance.js"></script>
	<script type="text/javascript">
		function lightmode(){
			setCookie('checker_theme', 'light', '30');
			location.reload();
		}
		function darkmode(){
			setCookie('checker_theme', 'dark', '30');
			location.reload();
		}
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}
	</script>
</body>
</html>
