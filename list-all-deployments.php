<?php
//error_reporting(E_ALL); 
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/deployment/api/deployments?$orderby=createdAt%20desc'; // API URL
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

curl_close($ch);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Deployments</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			DEPLOYMENTS: <?php echo $total?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">DESCRIPTION</div>
			<div class="table-cell-title">CREATED BY</div>
			<div class="table-cell-title">OWNER</div>
			<div class="table-cell-title">LAST UPDATE</div>
			<div class="table-cell-title">STATUS</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$color = "";
			if ($data['content'][$i]['status'] == "CREATE_FAILED") {
				$color = "color-text-failure";
			}
			if ($data['content'][$i]['status'] == "CREATE_SUCCESSFUL") {
				$color = "color-text-success";
			}
			
			$dateTime = new DateTime($data['content'][$i]['lastUpdatedAt']);
			$lastUpdate = $dateTime->format('Y-m-d H:i:s');
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['description'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['createdBy'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['ownedBy'];?></div>
			<div class="table-cell"><?php echo $lastUpdate;?></div>
			<div class="table-cell <?php echo $color; ?>"><?php echo $data['content'][$i]['status'];?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>