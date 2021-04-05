<?php


class User
{

    private $db;
    private $name;
    private $surname;
    private $username;
    private $mail;
    private $password;
    private $task;
    private $role;


    public function __construct($name, $surname, $username, $mail, $password, $task, $role)
    {

        $this->name = $name;
        $this->surname = $surname;
        $this->username = $username;
        $this->mail = $mail;
        $this->password = $password;
        $this->task = $task;
        $this->role = $role;


        //
    }

    public function __destruct()
    {
    }
}
