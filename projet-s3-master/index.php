<?php
require_once('autoload.php') ;

User::logoutIfRequested();

$Home = new WebPage('Home');

//evite de devoir accepter le réenvoie du post

if (!empty($_POST)) {
    $_SESSION["formulaire_envoye"] = $_POST;
    header("Location: index.php");
}
if (isset($_SESSION["formulaire_envoye"])) {
    $_POST = $_SESSION["formulaire_envoye"];
}

$Home->appendCss('
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
            font-size: 3.5rem;
            }
        }'); 

$Home->appendBootstrap();

$Home->appendContent(<<<HTML
    <div class="container">
    <header class="blog-header py-4">
                <div class="row flex-nowrap justify-content-between ">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <p><FONT size="10" class="blog-header-logo text-dark">Master IA URCA</FONT></p>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modalLoginForm" >Connexion</button>
                        <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header text-center">
                                    <h4 class="modal-title w-100 font-weight-bold">Authentification</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
HTML
);
try{
    $user = User::createFromSession();
    $Home->appendContent(User::logoutForm('index.php','Se déconnecter'));
}catch(Exception $e){
    $form = User::loginFormSHA512('page/accueil.php') ;
    $Home->appendContent(<<<HTML
    {$form}
HTML
);
}

$Home->appendContent(<<<HTML
                                    <h6>Compte étudiant:</h6>
                                    <div>login: zizou98</div>
                                    <div>mdp: zizou</div>
                                    <h6>Compte entreprise:</h6>
                                    <div>login: entr0001</div>
                                    <div>mdp: entreprise</div>
                                </div>
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </header>


            <div class="nav-scroller py-4 mb-2">
                <nav class="nav d-flex justify-content-between">
                <a class="p-2 text-muted" href="index.php">Accueil</a>
                <a class="p-2 text-muted" href="http://ebureau.univ-reims.fr/uPortal/render.userLayoutRootNode.uP?uP_root=root&uP_sparam=activeTab&activeTab=3">Annuaire</a>
                <a class="p-2 text-muted" href="http://ebureau.univ-reims.fr/uPortal/render.userLayoutRootNode.uP?uP_root=root&uP_sparam=activeTab&activeTab=2">Assistance</a>
                </nav>
            </div>

            <div class="accordion" id="accordionExample">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                      <button class="btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Présentation du master IA
                    </button>
                    </h2>
                  </div>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <h1>PRESENTATION :</h1>
                        <div class="py-4 ml-4">
                            <p><strong>Niveau requis en entrée :</strong> BAC+4 ou équivalent</p>
                            <p><strong>Niveau validé à la sortie :</strong> BAC+5 ou équivalent</p>
                            <p><strong>Niveau validé à la sortie :</strong> BAC+5 ou équivalent</p>
                            <p><strong>Durée de la formation :</strong> 2 ans</p>
                            <p><strong>Forme de l'enseignement :</strong>  Enseignement en présentiel<p>
                            <p>Le master est diplôme national.</p>
                            <p>Il est organisé en 5 Unités d'Enseignement :</p>
                            <ul>
                                <li>UE1 : Intelligence Artificielle et Management des Connaissances.</li>
                                <li>UE2 : Données et Décision.</li>
                                <li>UE3 : Modélisation et Maîtrise de la Complexité.</li>
                                <li>UE4 : Informatique et Interaction.</li>
                                <li>UE5 : Projets et Entreprise), les différents contenus étant déployés sur 4 semestres.</li>
                            </ul>
                            <p>Les enseignements sont dispensés sous forme de cours magistraux, de travaux dirigés et de travaux pratiques, l’accent étant mis sur les projets individuels et collectifs, les stages, ainsi que sur le contrôle continu.</p>                         
                            <p>En M1, un projet tutoré et un stage obligatoire de deux mois minimum sont prévus au second semestre (S2). En M2, un projet tutoré et un stage obligatoire de quatre mois minimum sont prévus au second semestre (S4).</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Admission
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <h3>Modalités d'admission :</h3>
                            <div id="Modalités d'admission" class="py-4 ml-4">
                                <dl style="list-style-type: none">
                                    <dt>&rarr; <i>Pour entrer en M1 :</i></dt>
                                    <dd class="ml-4">Les modalités relatives aux inscriptions en Master 1 sont disponibles sur le lien suivant :</dd>
                                    <dd class="ml-4"><a href="http://www.univ-reims.fr/portail-master">http://www.univ-reims.fr/portail-master</a></dd>
                                    <dt>&rarr; <i>Pour entrer en M2 :</i></dt>
                                    <dd class="ml-4">Pour tout renseignement, merci de contacter la scolarité (voir dans la rubrique contact).</dd>
                                    <dt>&rarr; <i>Vous êtes de nationalité étrangère :</i></dt>
                                    <dd class="ml-4">Les modalités relatives à l’admission des étudiants étrangers sont disponibles sur le lien suivant : <a href="http://www.univ-reims.fr/etudiants-internationaux">http://www.univ-reims.fr/etudiants-internationaux</a></dd>
                                    <dd class="ml-4">Pour plus d’informations, vous pouvez également envoyer un e-mail : <a href="mailto:etudiants.etrangers@univ-reims.fr">etudiants.etrangers@univ-reims.fr</a></dd>
                                </dl>
                            </div>
                            <h3>Prérequis obligatoires :</h3>
                            <div id="Prérequis obligatoires" class="py-4 ml-4">
                                <p>Pour le M1 : être titulaire d’une L3 ou équivalent (180 crédits ECTS).</p>
                                <p>Pour le M2 : être titulaire d’un M1 (240 crédits ECTS).</p>
                            </div>
                            <h3>Mentions de Licence recommandées :</h3>
                            <div id="Mentions de Licence recommandées" class="py-4 ml-4">
                                <ul style="list-style-type: none;">
                                    <li>&rarr; Licence Informatique</li>
                                </ul>
                            </div>
                            <h3>Prérequis recommandés :</h3>
                            <div id="Prérequis recommandés" class="py-4 ml-4">
                                <p>Pour suivre cette formation dans de bonnes conditions, il est recommandé de connaître et pratiquer un ou plusieurs langages de programmation, maîtriser les fondements de l’informatique théorique (algorithmique, calcul, programmation, logique, traitement et stockage de données, systèmes d’information, interfaces personnes-machines, modélisation).</p>
                                <p>Intérêt prononcé pour l’intelligence artificielle et les humanités numériques, la fouille/visualisation de données, les applications Big Data et Machine Learning, ainsi que l’informatique participative en réseaux.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                  <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                      <button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Les poursuites d'études
                     </button>
                    </h2>
                  </div>
                  <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        <h3>Poursuites d’études envisageables :</h3>
                        <div id="Poursuites d’études envisageables" class=" py-4 ml-4">
                            <p>Les étudiants motivés pourront compléter leur formation par un doctorat.</p>
                        </div>
                        <h3>Débouchés :</h3>
                        <div id="Débouchés" class="py-4 ml-4">
                            <ul style="list-style-type: none">
                                <li>Ingénieur en Systèmes d’information et de télécommunication</li>
                                <li>Management et ingénierie études, recherche et développement industriel</li>
                                <li>Data scientist / Études et prospectives socio-économiques</li>
                                <li>Conseil en organisation et management d’entreprise</li>
                                <li>Direction de petite ou moyenne entreprise</li>
                                <li>Réalisateur de contenus multimédias</li>
                            </ul>
                        </div>
                        <h3>Insertion professionnelle</h3>
                        <div id="Insertion professionnelle" class="py-4 ml-4">
                            <ul style="list-style-type: none">
                                <li>&rarr; Devenir à 6 mois</li>
                            </ul>
                            <center>
                                <p><a href="https://www.univ-reims.fr/orientation-et-insertion/observatoire-du-suivi-de-l-insertion-professionnelle-et-de-l-evaluation-osipe/devenir-et-insertion-professionnelle-des-diplomes/diplomes-de-master-devenir-a-6-mois-resultats-par-diplome,8605,37610.html">Résultats par diplôme</a></p>
                            </center>
                            <ul style="list-style-type: none">
                                <li>&rarr; Insertion professionnelle à 30 mois</li>
                            </ul>
                            <center>
                                <p><a href="https://www.univ-reims.fr/orientation-et-insertion/observatoire-du-suivi-de-l-insertion-professionnelle-et-de-l-evaluation-osipe/devenir-et-insertion-professionnelle-des-diplomes/diplomes-de-master-insertion-professionnelle-a-30-mois-resultats-par-diplome,8605,37611.html">Résultats par diplôme</a></p>
                            </center>
                        </div>
                    </div>
                  </div>
                </div>
              </div>       
            </div>
              
HTML
);

echo $Home->toHTML();