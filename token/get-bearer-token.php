<html>
<head>
    <title>Aria Automation - Authentication</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<p><a href="../index.php">back</a></p>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once('../config.php');
$apiUrl = '/iaas/api/login'; // API URL

$data = array(
	"refreshToken" => $_GET['refresh_token']
);
$jsonData = json_encode($data);

$ch = curl_init($serverUrl . $apiUrl);

curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($statusCode != 200) { ?>
    <h3 align="center"><?php echo "Failed to get the Aria Automation bearer token <br><br>";?></h3>
	<p align="center"><?php echo "Details: " . $response; ?></p>
<?php } 
else {
	$data = json_decode($response, true);
	$bearerToken = $data['tokenType'] . " " . $data['token'];
	session_start();
	$_SESSION['bearer_token'] = $bearerToken;
	header('Location: ../index.php');
}

curl_close($ch);
?>
</body>
</html>