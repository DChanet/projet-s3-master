<?php

class SessionException extend Exception{

    public function __construct(string $message){
        Parent::__construct($message);
    }
}
