<?php
$username = 'root';
$password = '';
$pdo_con = new PDO('mysql:host=localhost;dbname=gis;', $username, $password);
$pdo_con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );