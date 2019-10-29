<?php
header('Content-Type: text/plain; charset=utf-8');
session_start();
require "../connection.php";

$id = $_GET['uid'];
$first_name = $_GET['first_name'];
$last_name = $_GET['last_name'];
$hash = $_GET['hash'];

$requery = 'SELECT * FROM clients WHERE login=:login';  
$stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$stmt->execute(array (':login' => $id));
$row = $stmt->fetchAll();
if(count($row) != 0)
{
    $_SESSION['login'] = $row[0]['first_name'];
    $_SESSION['id'] = $row[0]['id'];
    header("Location: ../index.php");
}
else
{
    $requery = 'INSERT INTO clients (first_name, last_name, login, date) VALUES (:first_name, :last_name, :login, NOW())';
    $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt->execute(array (':first_name' => $first_name, ':last_name' => $last_name, ':login' => $id));

    $requery = 'UPDATE clients SET activation=1 WHERE login=:login';
    $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt->execute(array (':login' => $id));

    $requery = 'SELECT * FROM clients WHERE login=:login';  
    $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt->execute(array (':login' => $id));
    $row = $stmt->fetchAll();

    $_SESSION['login'] = $row[0]['first_name'];
    $_SESSION['id'] = $row[0]['id'];
    header("Location: ../index.php");
}