<?php
require_once('config.php');
$bearerToken = $_SESSION['bearer_token'];

// CLOUD ACCOUNT
$apiUrl = '/iaas/api/cloud-accounts'; // API URL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $bearerToken, 'Content-Type: application/json']);
$response = curl_exec($ch);
//$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//echo "---> STATUS CODE: " . $statusCode;
$data = json_decode($response, true);
$totalCloudAccount = count($data['content']);

// CLOUD ZONE
$apiUrl = '/iaas/api/zones'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalCloudZone = count($data['content']);

// PROJECTS
$apiUrl = '/iaas/api/projects'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalProjects = count($data['content']);

// NETWORK PROFILE
$apiUrl = '/iaas/api/network-profiles'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalNetProfile = count($data['content']);

// NETWORK
$apiUrl = '/iaas/api/fabric-networks'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalNet = count($data['content']);

// STORAGE PROFILE
$apiUrl = '/iaas/api/storage-profiles'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalStorageProfile = count($data['content']);

// STORAGE
$apiUrl = '/iaas/api/fabric-vsphere-datastores'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalStorage = count($data['content']);

// COMPUTE
$apiUrl = '/iaas/api/fabric-computes'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalCompute = count($data['content']);

// DEPLOYMENTS
$apiUrl = '/deployment/api/deployments'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalDeployments = count($data['content']);

// CATALOG ITEMS
$apiUrl = '/catalog/api/items'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalCatalogItems = count($data['content']);

// TAGS
$apiUrl = '/iaas/api/tags'; // API URL
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalTags = count($data['content']);

// WORKFLOW RUNS
$apiUrl = '/vro/runs?$top=500'; // API URL (LIMITED TO 500 RESULTS)
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalWfRuns = count($data['content']);

// WORKFLOWS
$apiUrl = '/vro/workflows?$top=1000'; // API URL (LIMITED TO 1000 RESULTS)
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalWorkflows = count($data['content']);

// BLUEPRINTS
$apiUrl = '/blueprint/api/blueprints?$top=1000'; // API URL (LIMITED TO 1000 RESULTS)
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$totalBlueprints = count($data['content']);

// IMAGES
$apiUrl = '/iaas/api/images'; // API URL (LIMITED TO 1000 RESULTS)
curl_setopt($ch, CURLOPT_URL, $serverUrl . $apiUrl);
$response = curl_exec($ch);
//$statusCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
$total = count($data['content']); // total regions
$totalImages = 0;
for ($i = 0; $i < $total; $i++) {
	$totalMapping = count($data['content'][$i]['mapping']);
	for ($ii = 0; $ii < $totalMapping; $ii++) {
		$totalImages++;
	}
}

curl_close($ch);
?>

<div class="dashboard">
	<h1>ASSEMBLER</h1>
	
	<a href="list-all-cloud-accounts.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalCloudAccount; ?></div>
			<div class="dashboard-box-label">CLOUD ACCOUNTS</div>
		</div>
	</a>
	<a href="list-all-cloud-zones.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalCloudZone; ?></div>
			<div class="dashboard-box-label">CLOUD ZONES</div>
		</div>
	</a>
	<a href="list-all-projects.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalProjects; ?></div>
			<div class="dashboard-box-label">PROJECTS</div>
		</div>
	</a>
	<a href="list-all-network-profiles.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalNetProfile; ?></div>
			<div class="dashboard-box-label">NETWORK PROFILES</div>
		</div>
	</a>
	<a href="list-all-storage-profiles.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalStorageProfile; ?></div>
			<div class="dashboard-box-label">STORAGE PROFILES</div>
		</div>
	</a>
	<a href="list-all-blueprints.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalBlueprints; ?></div>
			<div class="dashboard-box-label">BLUEPRINTS</div>
			
		</div>
	</a>
	<a href="list-all-deployments.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalDeployments; ?></div>
			<div class="dashboard-box-label">DEPLOYMENTS</div>
		</div>
	</a>
	<a href="list-all-tags.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalTags; ?></div>
			<div class="dashboard-box-label">TAGS</div>
		</div>
	</a>
	<a href="list-all-images.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalImages; ?></div>
			<div class="dashboard-box-label">IMAGES</div>
		</div>
	</a>
</div>

<div class="dashboard">
	<h1>RESOURCE</h1>
	<a href="list-all-compute.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalCompute;?></div>
			<div class="dashboard-box-label">COMPUTE</div>
		</div>
	</a>
	<a href="list-all-networks.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalNet;?></div>
			<div class="dashboard-box-label">NETWORKS</div>
		</div>
	</a>
	<a href="list-all-storages.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalStorage;?></div>
			<div class="dashboard-box-label">STORAGE</div>
		</div>
	</a>
</div>

<div class="dashboard">
	<h1>SERVICE BROKER</h1>
	<a href="list-all-catalog-items.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalCatalogItems;?></div>
			<div class="dashboard-box-label">CATALOG ITEMS</div>
		</div>
	</a>
</div>

<div class="dashboard">
	<h1>ORCHESTRATOR</h1>
	<a href="list-all-workflows.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalWorkflows;?></div>
			<div class="dashboard-box-label">WORKFLOWS</div>
		</div>
	</a>
	<a href="list-all-workflow-runs.php">
		<div class="dashboard-box">
			<div class="dashboard-box-value"><?php echo $totalWfRuns;?></div>
			<div class="dashboard-box-label">WORKFLOW RUNS</div>
		</div>
	</a>
</div>