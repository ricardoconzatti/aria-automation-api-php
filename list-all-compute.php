<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/fabric-computes?$orderby=name%20asc'; // API URL
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

$hostCompute = 0;
$resourceCompute = 0;
$clusterCompute = 0;
$azOther = 0;
for ($ii = 0; $ii < $total; $ii++) {
	switch ($data['content'][$ii]['type']) {
		case "Host":
			$hostCompute = $hostCompute + 1;
			break;
		case "ResourcePool":
			$resourceCompute = $resourceCompute + 1;
			break;
		case "Cluster":
			$clusterCompute = $clusterCompute + 1;
			break;
		default:
			$azOther = $azOther + 1;
			break;
	}
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aria Automation - Compute</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			COMPUTE: <?php echo $total?>
			<p class="table-header-sub">Host based: <?php echo $hostCompute?> | Resource pool based: <?php echo $resourceCompute?> | Cluster based: <?php echo $clusterCompute?> | Other: <?php echo $azOther?></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">TYPE</div>
			<div class="table-cell-title">TAGS</div>
			<div class="table-cell-title">STATE</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$info = "";
			$tags = "";
			if ($data['content'][$i]['tags'] != null) {
				$totalTags = count($data['content'][$i]['tags']);
				for ($ii = 0; $ii < $totalTags; $ii++) {
					$tags .= '<span class="minimal-highlight-tags">' . $data['content'][$i]['tags'][$ii]['key'] . ':' . $data['content'][$i]['tags'][$ii]['value'] . '</span>';
					if ($ii != $totalTags -1) {
						$tags .= " ";
					}
				}
			}
			else {
				$tags = "No tags";
			}
			if ($data['content'][$i]['lifecycleState'] == "READY") {
				$healthy = "READY";
				$color = "color-text-success";
			}
			else {
				$healthy = "NOT READY";
				$color = "color-text-failure";
			}
			if ($data['content'][$i]['type'] == "Host") { // HOST
				$memoryGb = $data['content'][$i]['customProperties']['memoryGB'] / 1024;
				$hostInfo = $data['content'][$i]['customProperties']['manufacturer'] . ' ' . $data['content'][$i]['customProperties']['modelName'] . ' | Logical Processors: ' . $data['content'][$i]['customProperties']['cpuCoreCount'] . ', Memory GB: ' . $memoryGb . ', NICs: ' . $data['content'][$i]['customProperties']['HOST__PNIC_COUNT'];
				$info = '<img src="img/icons/info.png" border="0" width="16" height="16" title="' . $hostInfo . '">';
			}
			if ($data['content'][$i]['type'] == "Cluster") { // CLUSTER
				$memoryGb = $data['content'][$i]['customProperties']['memoryGB'] / 1024;
				$clusterInfo = 'ESXi hosts: ' . $data['content'][$i]['customProperties']['hostCount'] . ', Processors: ' . $data['content'][$i]['customProperties']['cpuCoreCount'] . ', vSAN: ' . $data['content'][$i]['customProperties']['isVsanEnabled'];
				$info = '<img src="img/icons/info.png" border="0" width="16" height="16" title="' . $clusterInfo . '">';
			}
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['type'] . ' ' . $info;?></div>
			<div class="table-cell"><?php echo $tags;?></div>
			<div class="table-cell <?php echo $color;?>"><?php echo $healthy;?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';
	curl_close($ch);
	?>
</body>
</html>