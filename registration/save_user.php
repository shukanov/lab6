<?php
require "../connection.php";

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$login = $_POST['login'];
$pass1 = $_POST['password1'];
$pass2 = $_POST['password2'];
$email = $_POST['email'];

if(isset($_POST['submit']))
{
    if($first_name == NULL || $last_name == NULL || $login == NULL || $pass1 == NULL || $pass2 == NULL || $email == NULL)
    {
      $text = 'Вы не ввели все данные';	
      require 'errors.php';
      return;
    }
    else if($pass1 != $pass2)
    {
        $text = 'Пароли не совпадают';
        require 'errors.php';
        return;
    }
    else if(strlen($login) < 3 || strlen($login) > 15)
    {
        $text = "Логин должен состоять не менее чем из 3 символов и не более 15";
        require "errors.php";
        return;
    }
    else if(strlen($pass1) < 3 || strlen($pass1) > 15)
    {
        $text = "Пароль должен состоять не менее чем из 3 символов и не более 15";
        require "errors.php";
        return;
    }
    else
    {
        $first_name = stripcslashes($first_name);  //обработка логина
        $first_name = htmlspecialchars($first_name, ENT_QUOTES);
        $first_name = trim($first_name); // удаление пробелов

        $last_name = stripcslashes($last_name);  //обработка логина
        $last_name = htmlspecialchars($last_name, ENT_QUOTES);
        $last_name = trim($last_name); // удаление пробелов

        $login = stripcslashes($login);  //обработка логина
        $login = htmlspecialchars($login, ENT_QUOTES);
        $login = trim($login); // удаление пробелов

        $pass1 = stripcslashes($pass1);  //обработка логина
        $pass1 = htmlspecialchars($pass1, ENT_QUOTES);
        $pass1 = trim($pass1); // удаление пробелов

        $pass1 = password_hash($pass1, PASSWORD_DEFAULT);

        $requery = 'SELECT * FROM clients WHERE login=:login';  
        $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array (':login' => $login));
        $row = $stmt->fetchAll();
        if(count($row) != 0)
        {
            $text = 'Логин уже существует';
            require 'errors.php';
            return;
        }
        else
        {
            $requery = 'INSERT INTO clients (first_name, last_name, login, password, email, date) VALUES (:first_name, :last_name, :login, :password, :email, NOW())';
            $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array (':first_name' => $first_name, ':last_name' => $last_name, ':login' => $login, ':password' => $pass1, ':email' => $email));

            $requery = 'SELECT * FROM clients WHERE login=:login';  
            $stmt = $dbh->prepare($requery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute(array (':login' => $login));
            $row = $stmt->fetchAll();

            $sha256_login = hash('sha256', $row[0]['login']);
            $sha256_id = hash('sha256', $row[0]['id']);
 
            
            $charset = "utf-8";
            $headerss ="Content-type: text/html; charset=$charset\r\n";
            $headerss.="MIME-Version: 1.0\r\n";
            $headerss.="Date: ".date('D, d M Y h:i:s O')."\r\n";
            
            $activation = $sha256_login . $sha256_id; //код активации аккаунта. Зашифруем через хеш sha256 идентификатор и логин.
            $subject    = "Подтверждение регистрации";
            $message    = "Здравствуйте ".$first_name."! Спасибо за регистрацию на test.ru\nВаш логин:    ".$login."\n
            Перейдите    по ссылке, чтобы активировать ваш    аккаунт:\nhttp://lab6/registration/activation.php?login=".$login."&code=".$activation."\nС    уважением,\n
            Администрация    test.ru";//содержание сообщение
            mail($email, $subject, $message, $headerss);//отправляем сообщение                    
            

            header("Location: ../index.php");
        }
    }
}
    

?>   