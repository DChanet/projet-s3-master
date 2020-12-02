<?php

class AuthenticationException extends Exception{
    public function __construct(string $message){
        Parent::__construct($message);
    }
}
