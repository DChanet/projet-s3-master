<?php

class Session
{
    
    public static function start():void
    {
        switch (session_status()) {
            case PHP_SESSION_DISABLED:
                throw new Exception('Sessions désactivées');
                break;
            case PHP_SESSION_NONE:
                if (!headers_sent()){
                    session_start(); 
                }
                else {
                    throw new SessionException('Impossible de démarer les sessions');
                }                    
                break;
            case PHP_SESSION_ACTIVE:
                break;
            default:
                throw new SessionException('Etat incohérent des sessions');
        }
    }
}


