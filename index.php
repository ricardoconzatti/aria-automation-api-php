<html>
<head>
    <title>Aria Automation</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
require_once('config.php');
session_start();

if ($serverUrl == null) {
	$color = "red";
	$button = '<p>Aria Automation URL is empty! Check the <b>config.php</b> file and reload this page!</p>';
}
else {
	$button = '<input type="submit" value="CONNECT">';
}

if ($_SESSION['bearer_token'] == 0) { ?>
	<form method="POST" action="token/get-refresh-token.php">
		<div class="minimal-table-auth">
			<div class="table-header-main">
				ARIA AUTOMATION AUTHENTICATION (TOKEN)
				<p class="table-header-sub">Access tokens are valid for 8 hours<br>(times out after 25 minutes of inactivity)</p>
			</div>
			<div class="table-row">
				<div class="table-cell-title">
					<label for="url">URL</label>
				</div>
				<div class="table-cell" style="background-color: <?php echo $color; ?>;"><?php echo $serverUrl;?><i style="font-size: 10px;"><?php if ($serverUrl != null) { echo "<br>data from config.php file";}?></i></div>
			</div>
			<div class="table-row">
				<div class="table-cell-title">
					<label for="user">USERNAME</label>
				</div>
				<div class="table-cell"><input type="text" name="user" value="<?php echo $serverUser;?>" id="user" required><i style="font-size: 10px;"><?php if ($serverUser != null) { echo "<br>data from config.php file"; } ?></i></div>
			</div>
			<div class="table-row">
				<div class="table-cell-title">
					<label for="password">PASSWORD</label>
				</div>
				<div class="table-cell"><input type="password" name="pass" value="<?php echo $serverPass;?>" id="pass" required><i style="font-size: 10px;"><?php if ($serverPass != null) { echo "<br>data from config.php file"; } ?></i></div>
			</div>
			<div class="table-row">
				<div class="table-cell" colspan="2" style="text-align: center;">
					<?php echo $button; ?>
				</div>
			</div>
		</div>
	</form>

<?php
}
else {
	//include 'menu.php';
	include 'counter.php';
	include 'footer.php';
}
?>
</body>
</html>