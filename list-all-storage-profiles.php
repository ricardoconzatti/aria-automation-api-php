<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/storage-profiles?$orderby=name%20asc'; // API URL
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
    <title>Aria Automation - Storage Profile</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			STORAGE PROFILE: <?php echo $total?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">TAGS</div>
			<div class="table-cell-title">DEFAULT</div>
			<div class="table-cell-title">CREATED</div>
			<div class="table-cell-title">CLOUD ACCOUNT / REGION</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) { 
			$tags = "";
			if ($data['content'][$i]['tags'] != null) {
				$totalNet = count($data['content'][$i]['tags']);
				for ($ii = 0; $ii < $totalNet; $ii++) {
					$tags .= '<span class="minimal-highlight-tags">' . $data['content'][$i]['tags'][$ii]['key'] . ":" . $data['content'][$i]['tags'][$ii]['value'] . '</span>';
					if ($ii != $totalNet -1) {
						$tags .= " ";
					}
				}
			}
			else {
				$tags = "No tags";
			}
			// get cloud account info
			$apiUrl = '/iaas/api/cloud-accounts/' . $data['content'][$i]['cloudAccountId']; // API URL
			curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
			$response2 = curl_exec($ch);
			//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$data2 = json_decode($response2, true);
			//echo $response2;
			
			// get region info
			$totalRegion = count($data2['enabledRegions']);
			for ($iii = 0; $iii < $totalRegion; $iii++) {
				if ($data['content'][$i]['externalRegionId'] == $data2['enabledRegions'][$iii]['externalRegionId']) {
					$region = $data2['enabledRegions'][$iii]['name'];
				}
			}
			
			if ($data['content'][$i]['defaultItem'] === true) {
				$defaultItem = "YES";
			}
			else {
				$defaultItem = "NO";
			}
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $tags;?></div>
			<div class="table-cell"><?php echo $defaultItem;?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['createdAt'];?></div>
			<div class="table-cell"><?php echo $data2['name'];?> / <?php echo $region;?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>