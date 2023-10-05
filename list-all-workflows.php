<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/vro/workflows?$orderby=name%20asc&$top=1000'; // API URL (LIMITED TO 1000)
$bearerToken = $_SESSION['bearer_token'];

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $bearerToken,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//echo "---> STATUS CODE: " . $statusCode;

$data = json_decode($response, true);
//echo $response;
$total = count($data['content']);
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Workflows</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			WORKFLOWS: <?php echo $total?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">DESCRIPTION</div>
			<div class="table-cell-title">VERSION</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['description'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['version'];?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';
	curl_close($ch);
	?>
</body>
</html>