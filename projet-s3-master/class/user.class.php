<?php

Class User{
    /*Atttributs*/
    const session_key = '__user__'; 
    private $id; //int 
	private $lastname; //string 
	private $firstname; //string 
    private $login; //string
	/*Methodes*/
	
	private function __construct()
	{
	}

    public function profil():array{
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
        select lastname, firstname, DATE_FORMAT(birthDate, "%d-%m-%Y"), address, mail, phone, login, schoolClass, photo, promotion, role, id
        from users
        where id = :id
SQL
);
        $stmt->execute(array('id'=> $this->id));
        $rep = $stmt->fetch();
        $tab =[];
        foreach($rep as $elmnt){
            array_push($tab,$elmnt);
        }
        return $tab;
    }

    public static function isConnected():bool{
        Session::start();
        return isset($_SESSION[self::session_key]['connected']) && $_SESSION[self::session_key]['connected'];
    }

    public static function logoutIfRequested():void{
        Session::start();
        if (isset($_POST['logout'])){
            session_destroy();
        }
    }

    public static function logoutForm($action, $text):string{
        $res = <<<HTML
            <form method='POST' action='{$action}'class="m-0">
                <button class='btn m-0' name='logout' value='logout'>{$text}</button>
            </form>
HTML
;
		return $res;
    }

        
    

    public function saveIntoSession():void{
        Session::start();
        $_SESSION[self::session_key]['User'] = $this;
    }

    public static function createFromSession():self{
        Session::start();
        if (isset($_SESSION[self::session_key]['User'])){
            return $_SESSION[self::session_key]['User'];
        }
        else{
            throw new NotInSessionException('Non présent dans la session');
        }
    }

    public static function randomString($size): string
    {
        $chaine = '';
        for ($i= 0; $i < $size; $i++){
            switch(rand(1,3)){
                case 1:
                    $chaine .= chr(rand(ord('a'), ord('z')));
                break;
                case 2:
                    $chaine .= chr(rand(ord('A'), ord('Z')));
                break;
                case 3:
                    $chaine .= chr(rand(ord('0'), ord('9')));
                break;
            }
        }
        return $chaine;
    }

    public static function loginFormSHA512($action, $submitText='Valider')
    {   
        Session::start();
        $challenge = User::randomString(10);
        $_SESSION[self::session_key]['challenge'] = $challenge;
        $form = <<<HTML
			<form onSubmit='cryptage(this)' action='{$action}' method='POST'>
				<input required name='login' placeholder='login'>
				<input required type='password' name='password' placeholder='password'>
                <input name='challenge' type='hidden' value='{$challenge}'>
                <input type='submit' class="mt-2 btn btn-dark btn btn-block" name="Valider"/>
			</form>
            <script src='script/sha512.js'>
            </script>
            <script>
                function cryptage(formu){
                    var login = formu.login.value;
                    var mdp = formu.password.value;
                    var challenge = formu.challenge.value;
                    console.log(login, mdp, challenge)
                    var code = CryptoJS.SHA512( CryptoJS.SHA512( mdp ) + challenge + CryptoJS.SHA512( login ) );
                    formu.login.value = '';
                    formu.password.value = '';
                    formu.challenge.value = code;
                }
            </script>
HTML;
		return $form;
    }

    public static function createFromAuthSHA512(array $data):self{
        Session::start();
        $chal =  $_SESSION[self::session_key]['challenge'];
        if(isset($data['challenge'])){
	    	$stmt = MyPDO::getInstance()->prepare(<<<SQL
                SELECT  id, lastname, firstname, login
                FROM users
                WHERE :code = SHA2(CONCAT(password, :chal, SHA2(login,512)),512)
SQL
);
            $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
            $stmt->execute(array('chal'=> $chal,'code' => $data['challenge']));
            if (($object = $stmt->fetch()) !== false) {
                $_SESSION[self::session_key]['connected'] = true;
                return $object;
    	    }
            else{        
                throw new AuthenticationException('User not found');
            }
        }
        else
        {
            throw new Exception('Champ vide');
        }  
    }
}


    
