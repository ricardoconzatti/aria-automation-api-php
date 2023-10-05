<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/vro/runs?$orderby=createdOn%20desc&$top=500'; // API URL (LIMITED TO 500 RESULTS)
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

$runCompleted = 0;
$runFailed = 0;
for ($ii = 0; $ii < $total; $ii++) {
	switch ($data['content'][$ii]['runStatus']) {
		case "COMPLETED":
			$runCompleted = $runCompleted + 1;
			break;
		case "FAILED":
			$runFailed = $runFailed + 1;
			break;
	}
}
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aria Automation - Workflow Runs</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			WORKFLOW RUNS: <?php echo $total?>
			<p class="table-header-sub">Completed: <?php echo $runCompleted?> | Failed: <?php echo $runFailed?></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">STARTED BY</div>
			<div class="table-cell-title">CREATED ON</div>
			<div class="table-cell-title">TOTAL TIME</div>
			<div class="table-cell-title">STATUS</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) {
			$time = ($data['content'][$i]['completedOn'] - $data['content'][$i]['startedOn']) / 1000;
			switch (true) {
				case $time < 60:
					$mTime = "s";
					$totalTime = round($time, 1);
					break;
				case $time >= 60:
					$mTime = "m";
					$totalTime = round($time / 60, 1);
					break;
			}
			
			$created = $data['content'][$i]['createdOn'] / 1000;
			$date = date("Y-m-d H:i:s", $created);
			
			$color = "";
			if ($data['content'][$i]['runStatus'] == "FAILED") {
				$color = "color-text-failure";
			}
			if ($data['content'][$i]['runStatus'] == "COMPLETED") {
				$color = "color-text-success";
			}
		?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['startedBy'];?></div>
			<div class="table-cell"><?php echo $date;?></div>
			<div class="table-cell"><?php echo $totalTime . $mTime;?></div>
			<div class="table-cell <?php echo $color; ?>"><?php echo $data['content'][$i]['runStatus'];?></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';
	curl_close($ch);
	?>
</body>
</html>