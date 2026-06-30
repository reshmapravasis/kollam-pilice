<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'pravasis_cms');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$result = $conn->query("SELECT * FROM pages");
echo "Pages in pravasis_cms: " . $result->num_rows . "\n";
