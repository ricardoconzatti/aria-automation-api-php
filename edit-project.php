<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once('config.php');
$apiUrl = '/iaas/api/projects/' . $_GET['id']; // API URL

session_start();
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

?>

<html>
<head>
    <title>Aria Automation - Edit Project</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="list-all-projects.php">back</a></p>
	<?php include 'menu.php'; ?>
    <div class="minimal-table-auth">
    <form action="update-project.php?id=<?php echo $data['id'];?>" name="salva" method="POST">
        <div class="table-row">
            <div class="table-cell-title">NAME</div>
            <div class="table-cell"><input type="text" name="name" value="<?php echo $data['name'];?>"/></div>
        </div>
        <div class="table-row">
            <div class="table-cell-title">DESCRIPTION</div>
            <div class="table-cell"><input type="text" name="description" value="<?php echo $data['description'];?>"/></div>
        </div>
        <div class="table-row">
            <div class="table-cell" style="text-align: center;">
                <input type="submit" name="salva" value="SAVE" />
            </div>
        </div>
    </form>
</div>

	<?php include 'footer.php';?>
</body>
</html>