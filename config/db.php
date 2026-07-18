<?php
$db_host = "sql301.infinityfree.com";
$db_user = "if0_42425847";
$db_pass = "eyrivlrpogi50";
$db_name = "if0_42425847_ctrlt_shop";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
