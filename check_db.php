<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

$cars = selectAll('cars');
foreach ($cars as $c) {
    echo $c['title'] . ': body=' . $c['body_type'] . ' drive=' . $c['drive_type'] . ' color=' . $c['color'] . "\n";
}
