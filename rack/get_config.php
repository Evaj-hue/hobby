<?php
include "config.php";
$sql = "SELECT item_weight, tolerance FROM config ORDER BY id DESC LIMIT 1";
$res = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($res)) {
    echo json_encode($row);
} else {
    // Default values - now using percentage-based tolerance (2%)
    echo json_encode(['item_weight' => 0.5, 'tolerance' => 2.0]);
}
mysqli_close($conn);
?>