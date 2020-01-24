<?php
session_start();

	//echo $_SERVER["REQUEST_URI"] . "<br />";

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/bdd.php"; 

	$data["connecte"] = valider("connecte","SESSION");
	$data["action"] = valider("action");

	if (!$data["action"])
	{
		// On ne doit rentrer dans le switch que si on y est autorisé
		$data["feedback"] = "Entrez connexion(login,passe) (eg 'tom','web2')";
	}
	else 
	{
		// si on a une action, on devrait avoir un message classique
		$data["feedback"] = "entrez action: logout, setUser(login,passe,initiales), getUsers,setNotification(description), getNotifications, delNotification(idNotification), setBoard(label),getBoards,majBoard(idBoard,label), getColonnes(idBoard), majColonne(idBoard,numColonne,label), setPostIt(idBoard,[label],[avancement],[numColonne]), getPostIts(idBoard,[numColonne]),  majPostIt(idPostIt,[label],[avancement],[numColonne]), delPostIt(idPostIt),setMarqueur(idPostIt,type,valeur), getMarqueurs(idPostIt),delMarqueur(idMarqueur),setCommentaire(idPostIt,contenu),getCommentaires(idPostIt),delCommentaire(idPostIt)";
				
		// si pas connecte et action n'est pas connexion, on refuse
		if ( (!valider("idUser","SESSION")) && ($data["action"] != "connexion" ) ) {
			$data["feedback"] = "Entrez connexion(login,passe) (eg 'user','user')";
		}
		else {
			 
	
			switch($data["action"])
			{
		

				// Connexion //////////////////////////////////////////////////

				case 'connexion' :
					// On verifie la presence des champs login et passe
			

					if 	(
							!($login = valider("login")) 
						|| 	!($passe = valider("passe"))
						||	!($data["connecte"] = verifUser($login,$passe))
					)
					{
						// On verifie l'utilisateur, et on crée des variables de session si tout est OK
						$data["feedback"] = "Entrez login,passe (eg 'user','user')";
					}
					else {
						$data["initiales"] = $_SESSION["initiales"];
						$data["pseudo"] = $_SESSION["pseudo"];
					}
				break;

				case 'logout' :
					// On supprime juste la session 
					session_destroy();
					$data["feedback"] = "Entrez login,passe (eg 'user','user')";
					$data["connecte"] = false;
				break;	

				// Utilisateurs //////////////////////////////////////////////////

				case 'setUser' :
				if ($login = valider("login"))
				if ($passe = valider("passe"))
				if ($initiales = valider("initiales"))
				{	
					$data["idUser"] = mkUser($login, $passe, $initiales); 
					mkNotification(valider("idUser","SESSION"),"Creation de l\'utilisateur $login"); 
				}
				break;


				case 'getUsers' : 
					$data["users"] = listerUsers();
				break;

				// Notifications //////////////////////////////////////////////////

				// plutôt un message interne qu'une action possible...
				case 'setNotification' :
				if ($idAuteur = valider("idUser","SESSION"))
				if ($description = valider("description"))
				{	
					$data["idNotification"] = mkNotification($idAuteur,$description); 
				}
				break;


				case 'getNotifications' : 
					$data["notifications"] = listerNotifications();
				break;

				case 'delNotification' : 
					if ($idNotification = valider("idNotification"))
					delNotification($idNotification); 
				break;

				// Boards //////////////////////////////////////////////////

				case 'setBoard' :
				if ($label = valider("label"))
				{	
					$data["idBoard"] = mkBoard($label);
					// On définit aussi ses colonnes 
					setColonnes($data["idBoard"]); 

					mkNotification(valider("idUser","SESSION"),"Creation du Board \'$label\'"); 
				}
				break;


				case 'getBoards' : 
					$data["boards"] = listerBoards();
				break;

				case 'majBoard' : 
					if ($idBoard = valider("idBoard"))
					if ($label = valider("label"))
					majBoard($idBoard,$label); 
				break;

				// Colonnes //////////////////////////////////////////////////


				case 'getColonnes' : 
					if ($idBoard = valider("idBoard"))
					$data["colonnes"] = listerColonnes($idBoard);
				break;

				case 'majColonne' : 
					if ($idBoard = valider("idBoard"))
					if ($numColonne = valider("numColonne"))
					if ($label = valider("label"))
					majColonne($idBoard,$numColonne,$label); 
				break;

				// post its //////////////////////////////////////////////////


				case 'setPostIt' :
				if ($idBoard = valider("idBoard"))
				{	
					$label = valider("label"); 
					$avancement = valider("avancement"); 
					$numColonne = valider("numColonne"); 
					$data["idPostIt"] = mkPostIt($idBoard, $label,$avancement, $numColonne);

					mkNotification(valider("idUser","SESSION"),"Creation du PostIt \'$label\'"); 
					
				}
				break;


				case 'getPostIts' : 
					if ($idBoard = valider("idBoard")) {
						$numColonne = valider("numColonne"); 
						$data["postIts"] = listerPostIts($idBoard,$numColonne); 
					}
				break;

				case 'majPostIt' : 
					if ($idPostIt = valider("idPostIt"))
					{
						$label = valider("label"); 
						$avancement = valider("avancement"); 
						$numColonne = valider("numColonne");
						majPostIt($idPostIt, $label,$avancement, $numColonne);  

						mkNotification(valider("idUser","SESSION"),"Mise a jour du PostIt \'$label\'"); 
					}
				break;

				case 'delPostIt' : 
					if ($idPostIt = valider("idPostIt"))
					delPostIt($idPostIt);
				break;


				// marqueurs //////////////////////////////////////////////////
				case 'setMarqueur' :
				if ($idPostIt = valider("idPostIt"))
				if ($type = valider("type"))
				if ($valeur = valider("valeur"))
				{	
					$data["idMarqueur"] = mkMarqueur($idPostIt, $type, $valeur); 
					mkNotification(valider("idUser","SESSION"),"Creation d\'un marqueur dans le post-it \'$idPostIt\'"); 
				}
				break;


				case 'getMarqueurs' : 
					if ($idPostIt = valider("idPostIt")) {
						$data["marqueurs"] = listerMarqueurs($idPostIt); 
					}
				break;

				case 'delMarqueur' : 
					if ($idMarqueur = valider("idMarqueur"))
					delMarqueur($idMarqueur); 

					mkNotification(valider("idUser","SESSION"),"Suppression du marqueur \'$idMarqueur\'"); 
				break;

				// commentaires //////////////////////////////////////////////////
				case 'setCommentaire' :
				if ($idPostIt = valider("idPostIt"))
				if ($contenu = valider("contenu"))
				if ($idAuteur = valider("idUser","SESSION"))
				{	
					$data["idCommentaire"] = mkCommentaire( $contenu,$idAuteur,$idPostIt); 
					mkNotification(valider("idUser","SESSION"),"Commentaire du post-it \'$idPostIt\' : \'contenu\' ");
				}
				break;


				case 'getCommentaires' : 
					if ($idPostIt = valider("idPostIt")) {
						$data["commentaires"] = listerCommentaires($idPostIt); 
					}
				break;

				case 'delCommentaire' : 
					if ($idCommentaire = valider("idCommentaire"))
					delCommentaire($idCommentaire) ;
					mkNotification(valider("idUser","SESSION"),"Suppression du commentaire \'$idCommentaire\' ");
				break;


				// Defaut //////////////////////////////////////////////////

				default : 				
					$data["action"] = "default";


			}

		}
	}

		
	 
	echo json_encode($data);

	// todo : notifications
?>










