<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
if ($_SESSION['bearer_token'] == 0) {
	header('Location: index.php');
}

require_once('config.php');
$apiUrl = '/iaas/api/projects?$orderby=name%20asc'; // API URL
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
	<title>Aria Automation - Projects</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="index.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table">
		<div class="table-header-main">
			PROJECT: <?php echo $total?> | <a href="new-project.php"><img src="img/icons/add.png" border="0" alt="New project" title="New project"></a>
			<p class="table-header-sub"><img src="img/icons/atencao.png" border="0"> THIS IS THE ONLY PAGE THAT YOU CAN CREATE, EDIT AND DELETE ITEMS <img src="img/icons/atencao.png" border="0"></p>
        </div>
        <div class="table-row-title">
            <div class="table-cell-title">NAME</div>
			<div class="table-cell-title">DESCRIPTION</div>
			<div class="table-cell-title">ACTIONS</div>
        </div>
        <?php
		for ($i = 0; $i < $total; $i++) { ?>
		<div class="table-row">
			<div class="table-cell"><?php echo $data['content'][$i]['name'];?></div>
			<div class="table-cell"><?php echo $data['content'][$i]['description'];?></div>
			<div class="table-cell"><a href="edit-project.php?id=<?php echo $data['content'][$i]['id'];?>"><img src="img/icons/edit.gif" border="0" alt="Edit <?php echo $data['content'][$i]['name'];?> project" title="Edit <?php echo $data['content'][$i]['name'];?> project"></a> <a href="remove-project.php?id=<?php echo $data['content'][$i]['id'];?>"><img src="img/icons/delete.png" border="0" alt="Delete <?php echo $data['content'][$i]['name'];?> project" title="Delete <?php echo $data['content'][$i]['name'];?> project"></a></div>
		</div>
		<?php } ?>
    </div>
	<?php include 'footer.php';?>
</body>
</html>