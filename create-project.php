<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once('config.php');
$apiUrl = '/iaas/api/projects'; // API URL

$data = array(
    'name' => $_POST['name'],
    'description' => $_POST['description']
);
$jsonData = json_encode($data);

session_start();
$bearerToken = $_SESSION['bearer_token'];

$ch = curl_init($serverUrl . $apiUrl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
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
header('Location: list-all-projects.php');
exit;
?>