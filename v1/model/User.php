<?php

class UserException extends Exception{ }

class User{

    private $id;
    private $firstName;
    private $lastName;
    private $username;
    private $dateCreated;
    private $darkMode;

    public function __construct($id, string $firstName, string $lastName, string $username, int $darkMode){
        $this->setId($id);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setUsername($username);
        $this->setDarkMode($darkMode);
    }

    // get id...
    public function getId(){
        return $this->id;
    }

    // get firstname...
    public function getFirstName() : string{
        return $this->firstName;
    }

    // getlastname...
    public function getLastName() : string{
        return $this->lastName;
    }

    // get username...
    public function getUsername() : string{
        return $this->username;
    }

    // get date created...
    public function getDateCreated(){
        return $this->dateCreated;
    }

    // get darkMode...
    public function getDarkMode() : int{
        return $this->darkMode;
    }

    // validate and set id value...
    public function setId($id){
        if (($id !== null) && (!is_numeric($id) || $id <= 0 || $this->id !== null)){
            throw new UserException("User ID error");
        }
        $this->id = $id;
    }

    // validate and set first name value...
    public function setFirstName(string $firstName){
        if(strlen($firstName) < 1 || strlen($firstName) > 50){
            throw new UserException("User First name error");
        }
        $this->firstName = $firstName;
    }

    // validate and set last name value...
    public function setLastName(string $lastName){
        if(strlen($lastName) < 1 || strlen($lastName) > 50){
            throw new UserException("User Last name error");
        }
        $this->lastName = $lastName;
    }

    // validate and set username value...
    public function setUsername(string $username){
        if(strlen($username) < 6 || strlen($username) > 20){
            throw new UserException("User Username error");
        }
        $this->username = $username;
    }

    // validate and set date created value...
    public function setDateCreated($dateCreated){
        if (($dateCreated !== null) && date_format(DateTime::createFromFormat('Y/m/d H:i:s', $dateCreated), 'Y/m/d H:i:s') != $dateCreated) {
            throw new UserException("User date created error");
        }
        $this->dateCreated = $dateCreated;
    }

    // validate and set dark mode value...
    public function setDarkMode(string $darkMode){
        if (!is_numeric($darkMode)) {
            throw new UserException("User Dark mode error:" . var_dump($darkMode));
        }
        $this->darkMode = $darkMode;
    }

    // return properties as array...
    public function returnUserAsArray() : array{
        $user = array();
        $user['id'] = $this->getId();
        $user['firstName'] = $this->getFirstName();
        $user['lastName'] = $this->getLastName();
        $user['Username'] = $this->getUsername();
        $user['darkMode'] = $this->getDarkMode();
        return $user;
    }

    // toggle 1 or 0 for dark mode...
    public function toggleDarkMode(int $currentMode) : int{
        if ($currentMode === 0) {
            return 1;
        } else if($currentMode === 1){
            return 0;
        } else {
            return 0;
        }
    }

}