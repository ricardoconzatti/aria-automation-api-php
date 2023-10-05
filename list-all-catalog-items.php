<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/catalog/api/items?$orderby=name%20asc'; // API URL
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
curl_close($ch);

$total = count($data['content']);
$vraItem = 0;
$vroItem = 0;
for ($ii = 0; $ii < $total; $ii++) {
	switch ($data['content'][$ii]['type']['name']) {
		case "VMware Aria Automation Templates":
			$vraItem = $vraItem + 1;
			break;
		case "Automation Orchestrator Workflow":
			$vroItem = $vroItem + 1;
			break;
	}
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Catalog Items</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			CATALOG ITEMS: <?php echo $total?>
			<p class="table-header-sub">Blueprint based: <?php echo $vraItem?> | Orchestrator based: <?php echo $vroItem?></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">DESCRIPTION</div>
			<div class="table-cell-title">TYPE</div>
			<div class="table-cell-title">CREATED</div>
			<div class="table-cell-title">CREATED BY</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$dateTime = new DateTime($data['content'][$i]['createdAt']);
			$created = $dateTime->format('Y-m-d H:i:s');
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['description'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['type']['name'];?></div>
			<div class="table-cell"><?php echo $created;?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['createdBy'];?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';	?>
</body>
</html>