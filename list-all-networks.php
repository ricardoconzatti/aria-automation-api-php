<?php
//error_reporting(E_ALL); // Ativar relatório de erros
//ini_set('display_errors', 1); // Definir a exibição de erros no navegador (opcional)

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/fabric-networks?$orderby=name%20asc'; // API URL
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
$vsphereNet = 0;
$nsxNet = 0;
$otherNet = 0;
for ($ii = 0; $ii < $total; $ii++) {
	// get cloud account info
	$apiUrl = '/iaas/api/cloud-accounts/' . $data['content'][$ii]['cloudAccountIds'][0]; // API URL
	curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
	$response3 = curl_exec($ch);
	//echo $response3;
	//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$data3 = json_decode($response3, true);
	switch ($data3['cloudAccountType']) {
		case "vsphere":
			$vsphereNet = $vsphereNet + 1;
			break;
		case "nsxt":
			$nsxNet = $nsxNet + 1;
			break;
		default:
			$otherNet = $otherNet + 1;
			break;
	}
}
curl_close($ch);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Networks</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			NETWORKS: <?php echo $total?>
			<p class="table-header-sub">vSphere based: <?php echo $vsphereNet?> | NSX based: <?php echo $nsxNet?>  | Other: <?php echo $otherNet?></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">CIDR</div>
			<div class="table-cell-title">TAGS</div>
			<div class="table-cell-title">TYPE</div>
			<div class="table-cell-title">SOURCE</div>
			<div class="table-cell-title">CLOUD ACCOUNT / REGION</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$tags = "";
			if ($data['content'][$i]['tags'] != null) {
				$totalNet = count($data['content'][$i]['tags']);
				for ($ii = 0; $ii < $totalNet; $ii++) {
					$tags .= '<span class="minimal-highlight-tags">' . $data['content'][$i]['tags'][$ii]['key'] . ':' . $data['content'][$i]['tags'][$ii]['value'] . '</span>';
					if ($ii != $totalNet -1) {
						$tags .= " ";
					}
				}
			}
			else {
				$tags = "No tags";
			}
			
			// get cloud account info
			$apiUrl = '/iaas/api/cloud-accounts/' . $data['content'][$i]['cloudAccountIds'][0]; // API URL
			curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
			$response2 = curl_exec($ch);
			//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$data2 = json_decode($response2, true);

			// get region info
			if ($data2['cloudAccountType'] != "nsxt") {
				$totalRegion = count($data2['enabledRegions']);
				for ($iii = 0; $iii < $totalRegion; $iii++) {
					if ($data['content'][$i]['externalRegionId'] == $data2['enabledRegions'][$iii]['externalRegionId']) {
						$region = " / " . $data2['enabledRegions'][$iii]['name'];
					}
				}
			}
			else {
				$region = "";
			}
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['cidr'];?></div>
			<div class="table-cell"><?php echo $tags;?></div>
			<div class="table-cell"><?php echo $data2['cloudAccountType'];?></div>
			<div class="table-cell"><?php echo $data2['cloudAccountProperties']['hostName'];?></div>
			<div class="table-cell"><?php echo $data2['name'] . $region;?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>