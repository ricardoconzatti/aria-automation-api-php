<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/zones?$orderby=name%20asc'; // API URL
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
	<title>Aria Automation - Cloud Zones</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			CLOUD ZONE: <?php echo $total?>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">TAGS</div>
			<div class="table-cell-title">PLACEMENT POLICY</div>
			<div class="table-cell-title">PROJECTS</div>
			<div class="table-cell-title">CLOUD ACCOUNT / REGION</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) { 
			if ($data['content'][$i]['_links']['projects']['hrefs'] != null) {
				$totalProjects = count($data['content'][$i]['_links']['projects']['hrefs']);
				$projectName = "";
				for ($iiii = 0; $iiii < $totalProjects; $iiii++) { // get projects name by ID
					$apiUrl = $data['content'][$i]['_links']['projects']['hrefs'][$iiii]; // API URL
					curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
					$response3 = curl_exec($ch);
					$data3 = json_decode($response3, true);
					$projectName .= $data3['name'];
					if ($iiii != $totalProjects -1) {
						$projectName .= " / ";
					}
				}
				$projectInfo = '<img src="img/icons/info.png" border="0" width="16" height="16" title="' . $projectName . '">';
			}
			else {
				$totalProjects = 0;
				$projectName = "";
				$projectInfo = "";
			}
			$tags = "";
			if ($data['content'][$i]['tags'] != null) {
				$totalZones = count($data['content'][$i]['tags']);
				for ($ii = 0; $ii < $totalZones; $ii++) {
					$tags .= '<span class="minimal-highlight-tags">' . $data['content'][$i]['tags'][$ii]['key'] . ":" . $data['content'][$i]['tags'][$ii]['value'] . '</span>';
					if ($ii != $totalZones -1) {
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
			curl_close($ch);
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
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $tags;?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['placementPolicy'];?></div>
			<div class="table-cell"><?php echo $totalProjects . ' ' . $projectInfo;?></div>
			<div class="table-cell"><?php echo $data2['name'] . ' / ' . $region;?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>