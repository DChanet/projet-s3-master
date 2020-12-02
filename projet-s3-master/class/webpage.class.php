<?php
require_once("user.class.php");
/**
 * Classe de gestion d'une page Web permettant de s'affranchir de l'écriture de la structure de base du code HTML
 */
class WebPage {
    /**
     * Texte compris entre <head> et </head>
     * @var string
     */
    private $head  = null ;
    /**
     * Texte compris entre <title> et </title>
     * @var string
     */
    private $title = null ;
    /**
     * Texte compris entre <body> et </body>
     * @var string
     */
    private $body  = null ;

    /**
     * Constructeur
     * @param string $title Titre de la page
     */
    public function __construct(string $title=null) {
        if (!is_null($title)) {
            $this->setTitle($title) ;
        }
    }

    /**
     * Protéger les caractères spéciaux pouvant dégrader la page Web
     * @param string $string La chaîne à protéger
     *
     * @return string La chaîne protégée
     */
    public function escapeString(string $string) {
        return htmlentities($string, ENT_QUOTES|ENT_HTML5, "utf-8") ;
    }

    /**
     * Affecter le titre de la page
     * @param string $title Le titre
     */
    public function setTitle(string $title) {
        $this->title = $title ;
    }

    /**
     * Ajouter un contenu dans head
     * @param string $content Le contenu à ajouter
     *
     * @return void
     */
    public function appendToHead(string $content) {
        $this->head .= $content ;
    }

    /**
     * Ajouter un contenu CSS dans head
     * @param string $css Le contenu CSS à ajouter
     *
     * @return void
     */
    public function appendCss(string $css) {
        $this->appendToHead(<<<HTML
    <style type='text/css'>
    {$css}
    </style>

HTML
);
    }

    /**
     * Ajouter l'URL d'un script CSS dans head
     * @param string $url L'URL du script CSS
     *
     * @return void
     */
    public function appendCssUrl(string $url) {
        $this->appendToHead(<<<HTML
    <link rel="stylesheet" type="text/css" href="{$url}">

HTML
);
    }

    /**
     * Ajouter un contenu JavaScript dans head
     * @param string $js Le contenu JavaScript à ajouter
     *
     * @return void
     */
    public function appendJs(string $js) {
        $this->appendToHead(<<<HTML
    <script type='text/javascript'>
    $js
    </script>

HTML
) ;
    }

    /**
     * Ajouter l'URL d'un script JavaScript dans head
     * @param string $url L'URL du script JavaScript
     *
     * @return void
     */
    public function appendJsUrl(string $url) {
        $this->appendToHead(<<<HTML
    <script type='text/javascript' src='{$url}'></script>

HTML
) ;
    }

    /**
     * Ajouter un contenu dans body
     * @param string $content Le contenu à ajouter
     *
     * @return void
     */
    public function appendContent(string $content) {
        $this->body .= $content ;
    }

    /**
     * Produire la page Web complète
     * @throws Exception si title n'est pas défini
     *
     * @return string
     */
    public function toHTML() {
        if (is_null($this->title)) {
            throw new Exception(__CLASS__ . ": title not set") ;
        }

        $lastmod = strftime("Dernière modification de cette page le %d/%m/%Y à %Hh%M", getlastmod()) ;
        return <<<HTML
<!doctype html>
<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{$this->title}</title>
{$this->head}
    </head>
    <body>
        <div id='page'>
{$this->body}
        </div>
    </body>
</html>
HTML;
    }

    /**
     * fonction appendBootstrap
     *
     * implemante Bootstrap à la page web
     *
     * @return void
     */

    public function appendBootstrap(){
        $this->head .=<<<HTML
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <link rel="icon" type="image/png" href="https://romeo.univ-reims.fr/medias/images/URCAorbrun.png" />

            <link rel='canonical' href='https://getbootstrap.com/docs/4.3/examples/blog/'>

            <!-- Bootstrap core CSS -->
                <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>

            <!-- Custom styles for this template -->
                <link href='https://fonts.googleapis.com/css?family=Playfair+Display:700,900' rel='stylesheet'>
            <!-- Custom styles for this template -->
                <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous" />
                <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
HTML
        ;
        $this->body .=<<<HTML
            <!-- /.row -->
            <!-- Optional JavaScript -->
            <!-- jQuery first, then Popper.js, then Bootstrap JS -->
            <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
            <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
            <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
HTML
        ;
    }

    /**
     * fonction appendBootstrap
     *
     * implemante Bootstrap à la page web
     *
     * @return void
     */

    public function appendNavbar(){

        if(isset($_SESSION['__user__']['id'])){
            $stmt = MyPDO::getInstance()->prepare(<<<SQL
                SELECT COUNT(id)
                FROM alerts
                WHERE id_users = :id
SQL
            );

            $stmt->execute(array('id'=>$_SESSION['__user__']['id']));
            $sql = $stmt->fetch();
            $res = $sql['COUNT(id)'];
            $_SESSION['__user__']['nbNotif'] = $res;
        }


        $this->head .= <<<HTML
            <style type='text/css'>
                .separation{
                    height: 30px;
                    margin-left: 20px;
                    margin-right: 5px;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    width : 1px;
                    background: grey;
                }
                .separationlist{
                    height: 30px;
                    margin-left: 10px;
                    margin-right: 10px;
                    margin-top: 5px;
                    margin-bottom: 0px;
                    width : 1px;
                    background: grey;
                }
                @media screen and (max-width: 768px){
                    .separationlist{
                        height: 1px;
                        width : 93%;
                        background: grey;
                    }
                    .nav-link{
                        text-align:center;
                    }
                    .liste{
                        margin-left: 0px;
                    }

                }
            </style>

            <nav class='navbar navbar-expand-md navbar-dark fixed-top bg-dark'>
                <a class='navbar-brand mr-4' href='accueil.php'>Master IA</a>
                    <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarCollapse' aria-controls='navbarCollapse' aria-expanded='false' aria-label='Toggle navigation'>
                        <span class='navbar-toggler-icon'></span>
                    </button>
                <div class='collapse navbar-collapse liste' id='navbarCollapse'>
                    <ul class='navbar-nav mr-auto liste'>
HTML
            ;
            if($_SESSION['__user__']['page'] == 'stages'){
                $this->head .= <<<HTML
                        <li class='nav-item active'>
                            <a class='nav-link' href='stages.php'>Stages</a>
                        </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                        <li class='nav-item'>
                            <a class='nav-link' href='stages.php'>Stages</a>
                        </li>
HTML
                ;
            }
        if($_SESSION['__user__']['role'] == 'student'){
            $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
HTML
            ;
            if($_SESSION['__user__']['page'] == 'bulletins'){
                $this->head .= <<<HTML
                    <li class='nav-item active'>
                        <a class='nav-link' href='bulletins.php'>Bulletins</a>
                    </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                    <li class='nav-item'>
                        <a class='nav-link' href='bulletins.php'>Bulletins</a>
                    </li>
HTML
                ;
            }
            $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
HTML
            ;
            if($_SESSION['__user__']['page'] == 'monStage'){
                $this->head .= <<<HTML
                    <li class='nav-item active'>
                        <a class='nav-link' href='monStage.php'>Mon Stage</a>
                    </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                    <li class='nav-item'>
                        <a class='nav-link' href='monStage.php'>Mon Stage</a>
                    </li>
HTML
                ;
            }
        }
        elseif ($_SESSION['__user__']['role'] == 'teacher'){
            $this->head .= <<<HTML

                <div>
                    <hr class="separationlist">
                </div>
HTML
                ;

            if($_SESSION['__user__']['page'] == 'bulletins'){

            $this->head .= <<<HTML

                    <li class='nav-item active'>
                        <a class='nav-link' href='bulletins.php'>Bulletins</a>
                    </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                    <li class='nav-item'>
                        <a class='nav-link' href='bulletins.php'>Bulletins</a>
                    </li>
HTML
                ;
            }
            if($_SESSION['__user__']['page'] == 'mesStages'){
                $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
                    <li class='nav-item active'>
                        <a class='nav-link' href='mesStages.php'>Mes Stages</a>
                    </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
                    <li class='nav-item'>
                        <a class='nav-link' href='mesStages.php'>Mes Stages</a>
                    </li>
HTML
                ;
            }
        }
        else{

            if($_SESSION['__user__']['page'] == 'mesStages'){
                $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
                    <li class='nav-item active'>
                        <a class='nav-link' href='mesStages.php'>Mes Stages</a>
                    </li>
HTML
                ;
            }
            else{
                $this->head .= <<<HTML
                <div>
                    <hr class="separationlist">
                </div>
                    <li class='nav-item'>
                        <a class='nav-link' href='mesStages.php'>Mes Stages</a>
                    </li>
HTML
                ;
            }
        }
        
        $this->head .= <<<HTML
                    </ul>
                    <ul class='navbar-nav mr-2'>
HTML
        ;
        if($res != 0){
            $this->head .= <<<HTML
                <li class='nav-item'>
                    <div class="btn-group btn-block ">
                        <button type="button" class="btn btn-light" onclick="document.location.href='profil.php'">Profil <span class="badge badge-danger pl-1">$res</span><span class="sr-only">unread messages</span></button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" id="dropdownMenuReference" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="parent">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuReference">
                            <div class="dropdown-item">{$_SESSION['__user__']['boutonLogout']}</div>
                        </div>
                    </div>
                </li>
HTML
            ;
        }
        else{
            $this->head .= <<<HTML
                <li class='nav-item p-auto'>
                    <div class="btn-group btn-block">
                        <button type="button" class="btn btn-light" onclick="document.location.href='profil.php'">Profil</button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" id="dropdownMenuReference" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="parent">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuReference">
                            <div class="dropdown-item">{$_SESSION['__user__']['boutonLogout']}</div>
                        </div>
                    </div>
                </li>
HTML
        ;
        }

        $this->head .= <<<HTML
            </ul>
        </div>
    </nav>
HTML
        ;

    }

}
