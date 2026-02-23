<?php
$mysqli = new mysqli("localhost", "root", "", "ventas_2026");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

echo "=== ROLES ===\n";
$result = $mysqli->query("SELECT * FROM roles");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error querying roles: " . $mysqli->error . "\n";
}

echo "\n=== USERS ===\n";
$result = $mysqli->query("SELECT id, username, role_id FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error querying users: " . $mysqli->error . "\n";
}
