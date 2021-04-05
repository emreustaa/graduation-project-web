<?php

try{
    $db = new PDO('mysql:host=localhost;dbname=bitirmeprojesi','root','root');
}catch(PDOException $e){
    $e->getMessage();
}
?>