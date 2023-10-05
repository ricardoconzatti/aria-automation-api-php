<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/blueprint/api/blueprints?$orderby=name%20asc&$top=1000'; // API URL (LIMITED TO 1000 RESULTS)
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
	<title>Aria Automation - Blueprints</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			BLUEPRINTS: <?php echo $total?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">DESCRIPTION</div>
			<div class="table-cell-title">PROJECT</div>
			<div class="table-cell-title">CREATED BY</div>
			<div class="table-cell-title">CREATED</div>
			<div class="table-cell-title">SOURCE</div>
			<div class="table-cell-title">VERSION</div>
			<div class="table-cell-title">RELEASED</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$dateTime = new DateTime($data['content'][$i]['createdAt']);
			$created = $dateTime->format('Y-m-d H:i:s');
			
			$info = "";
			if ($data['content'][$i]['contentSourceType'] == "com.github") {
				$contentSource = "GitHub";
				$contentSourceMsg = $data['content'][$i]['contentSourceSyncStatus'] . ' - ';
				
				$totalContentSourceMsg = count($data['content'][$i]['contentSourceSyncMessages']);
				for ($ii = 0; $ii < $totalContentSourceMsg; $ii++) {
					$contentSourceMsg .= $data['content'][$i]['contentSourceSyncMessages'][$ii];
					if ($ii != $totalContentSourceMsg -1) {
						$contentSourceMsg .= ' / ';
					}
				}
				$info = '<img src="img/icons/info.png" border="0" width="16" height="16" title="' . $contentSourceMsg . '">';
			}
			if ($data['content'][$i]['contentSourceType'] == null) {
				$contentSource = "Local";
			}
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['description'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['projectName'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['createdBy'];?></div>
			<div class="table-cell"><?php echo $created;?></div>
			<div class="table-cell"><?php echo $contentSource . ' ' . $info;?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['totalVersions'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['totalReleasedVersions'];?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';
	curl_close($ch);
	?>
</body>
</html>