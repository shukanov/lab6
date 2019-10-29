<?php
  session_start();
  require "connection.php";
  $requery = 'SELECT event.title, event.description, event.img, event.id, event.date, clients.first_name, 
  clients.last_name FROM event, clients WHERE event.clients_id = clients.id'; 
  foreach($dbh->query($requery) as $row)
  {
    $title = $row['title'];
    $desc = $row['description'];
    $img = $row['img'];
    $id = $row['id'];
    $date = $row['date'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    require "_event.php";
  }     
