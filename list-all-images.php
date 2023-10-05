<?php
//error_reporting(E_ALL); 
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/images?$orderby=name%20asc'; // API URL
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
$total = count($data['content']); // total regions

$images = 0;
for ($iii = 0; $iii < $total; $iii++) {
	$totalMapping = count($data['content'][$iii]['mapping']);
	for ($iiii = 0; $iiii < $totalMapping; $iiii++) {
		$images++;
	}
}

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
			IMAGES: <?php echo $images?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">TEMPLATE NAME</div>
			<div class="table-cell-title">OS FAMILY</div>
			<div class="table-cell-title">CLOUD ACCOUNT / REGION</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {			
			// get region info
			$apiUrl = $data['content'][$i]['_links']['region']['href']; // API URL
			curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
			$response2 = curl_exec($ch);
			//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$data2 = json_decode($response2, true);
			
			// get cloud account info
			$apiUrl = '/iaas/api/cloud-accounts/' . $data2['cloudAccountId']; // API URL
			curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
			$response3 = curl_exec($ch);
			//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$data3 = json_decode($response3, true);

			$cloudAcRegion = $data3['name'] . ' / ' . $data2['name'];
			
			// inside MAPPING
			$totalMapping = count($data['content'][$i]['mapping']);
			$imageData = array_keys($data['content'][$i]['mapping']);
			for ($ii = 0; $ii < $totalMapping; $ii++) {
				$imageName = $imageData[$ii];
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $imageName?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['mapping'][$imageName]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['mapping'][$imageName]['osFamily'];?></div>
			<div class="table-cell"><?php echo $cloudAcRegion; ?></div>
		</div>
		<?php }}?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>