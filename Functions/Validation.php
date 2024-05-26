<?php

function ValidateEmail($email){
    $emailRegex =  "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
       return preg_match($emailRegex, $email);
}

function ValidatePassword($password){
    $passwordRegex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
    return preg_match($passwordRegex, $password);
    }
?>