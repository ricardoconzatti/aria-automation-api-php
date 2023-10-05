<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/cloud-accounts?$orderby=name%20asc'; // API URL
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

$vsphereAccount = 0;
$nsxAccount = 0;
$otherAccount = 0;
for ($ii = 0; $ii < $total; $ii++) {
	switch ($data['content'][$ii]['cloudAccountType']) {
		case "vsphere":
			$vsphereAccount = $vsphereAccount + 1;
			break;
		case "nsxt":
			$nsxAccount = $nsxAccount + 1;
			break;
		default:
			$otherAccount = $otherAccount + 1;
			break;
	}
}
curl_close($ch);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Cloud Accounts</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			CLOUD ACCOUNTS: <?php echo $total?>
			<p class="table-header-sub">vSphere based: <?php echo $vsphereAccount?> | NSX based: <?php echo $nsxAccount?>  | Other: <?php echo $otherAccount?></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">TYPE</div>
			<div class="table-cell-title">HOSTNAME</div>
			<div class="table-cell-title">REGIONS</div>
			<div class="table-cell-title">CREATED</div>
			<div class="table-cell-title">STATUS</div>
			<div class="table-cell-title">HEALTHY</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$regions = "";
			if ($data['content'][$i]['enabledRegions'] != null) {
				$totalNet = count($data['content'][$i]['enabledRegions']);
				for ($ii = 0; $ii < $totalNet; $ii++) {
					$regions .= $data['content'][$i]['enabledRegions'][$ii]['name'];
					if ($ii != $totalNet -1) {
						$regions .= " | ";
					}
				}
			}
			else {
				$regions = "No regions";
			}
			if ($data['content'][$i]['healthy'] == true) {
				$healthy = "YES";
				$color = "color-text-success";
			}
			else {
				$healthy = "NO";
				$color = "color-text-failure";
			}
			
			if ($data['content'][$i]['customProperties']['endpointHealthCheckState'] == "AVAILABLE") {
				$color2 = "color-text-success";
			}
			else {
				$color2 = "color-text-failure";
			}
			?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['cloudAccountType'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['cloudAccountProperties']['hostName'];?></div>
			<div class="table-cell"><?php echo $regions;?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['createdAt'];?></div>
			<div class="table-cell <?php echo $color;?>"><?php echo $data['content'][$i]['customProperties']['endpointHealthCheckState'];?></div>
			<div class="table-cell <?php echo $color;?>"><?php echo $healthy;?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>