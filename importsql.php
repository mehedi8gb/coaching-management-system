<?php

// Name of the file
$filename = 'database/infixv4_5_upgraded.sql';

$conn = new mysqli('localhost', 'root', 'root', 'v4.4');
$conn->set_charset("utf8");

$query = '';
$sqlScript = file('database/infixv4_5_upgraded.sql');
foreach ($sqlScript as $line) {

    $startWith = substr(trim($line), 0, 2);
    $endWith = substr(trim($line), -1, 1);

    if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
        continue;
    }

    $query = $query . $line;
    if ($endWith == ';') {
        try {
            mysqli_query($conn, $query);
        } catch (Exception $e) {
            echo ("Error description: " . $mysqli->error);
            echo 'Message: ' . $e->getMessage();
            exit();
        }
        $query = '';
    }
}
echo '<div class="success-response sql-import-response">SQL file imported successfully</div>';
