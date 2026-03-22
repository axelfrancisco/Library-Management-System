<?php
header("Content-Type: application/javascript");
require_once __DIR__ . "/google.php";
echo "window.NEU_GOOGLE_CLIENT_ID = " . json_encode($GOOGLE_CLIENT_ID) . ";";
?>
