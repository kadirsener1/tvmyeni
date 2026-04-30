<?php
$data = file_get_contents("php://input");
file_put_contents("channels.json", $data);
echo json_encode(["success" => true]);
?>
