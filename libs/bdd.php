<?php


include_once("maLibSQL.pdo.php"); 
// définit les fonctions SQLSelect, SQLUpdate... 


function mkUser($pseudo, $passe, $initiales)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	$SQL = "INSERT INTO users(pseudo,passe,initiales) VALUES('$pseudo','$passe','$initiales')";
	return SQLInsert($SQL);
}

function listerUsers()
{
	// liste tous les boards disponibles, triés par valeur du champ 'ordre' croissant
	$SQL = "SELECT * FROM users ORDER BY id ASC"; 
	return parcoursRs(SQLSelect($SQL));
}

//////////////////////////////////////////////////////////////////////////////

function mkNotification($idAuteur,$description) {
	$SQL = "INSERT INTO notifications(idAuteur,description) VALUES('$idAuteur','$description')";
	return SQLInsert($SQL);
}

function listerNotifications()
{
	// liste tous les boards disponibles, triés par valeur du champ 'ordre' croissant
	$SQL = "SELECT n.id, u.pseudo, u.initiales, n.description, n.dateAction FROM notifications n INNER JOIN users u ON n.idAuteur=u.id ORDER BY n.id ASC"; 
	return parcoursRs(SQLSelect($SQL));
}

function delNotification($idNotification)
{	
	// supprimer un post-it nécessite de supprimer aussi ses marqueurs ! 
	$SQL = "DELETE FROM notifications WHERE id='$idNotification'"; 
	SQLDelete($SQL);
}

//////////////////////////////////////////////////////////////////////////////


function mkBoard($label)
{
	// Cette fonction crée un nouveau board à la fin des boards existants et renvoie son identifiant
	$SQL = "INSERT INTO boards(label) VALUES('$label')";

	// On peut définir une valeur d'ordre du board en argument supplémentaire
	// La faire calculer automatiquement en spécifiant que ce champ est auto-incrémenté dans la bdd
	// ou ajouter une fonction de mise à jour d'un board... 
	return SQLInsert($SQL);
}

function listerBoards()
{
	// liste tous les boards disponibles, triés par valeur du champ 'ordre' croissant
	$SQL = "SELECT * FROM boards ORDER BY id ASC"; 
	return parcoursRs(SQLSelect($SQL));
}

// fonction ajoutee
function majBoard($idBoard,$label)
{
	$SQL = "UPDATE boards SET label='$label' WHERE id='$idBoard'";
	return SQLUpdate($SQL);
}

//////////////////////////////////////////////////////////////////////////////

function listerColonnes($idBoard) {
	$SQL = "SELECT * FROM colonnes WHERE idBoard='$idBoard'"; 
	return parcoursRs(SQLSelect($SQL));
}

function majColonne($idBoard,$numColonne,$label) {
	$SQL = "UPDATE colonnes SET nomCol" . $numColonne . "='$label' WHERE idBoard='$idBoard'";
	return SQLUpdate($SQL);
}

function setColonnes($idBoard) {
	$SQL = "INSERT INTO colonnes(idBoard,nomCol1,nomCol2,nomCol3) VALUES ('$idBoard','A Faire', 'En cours', 'Fait')";
	return SQLUpdate($SQL);
}

//////////////////////////////////////////////////////////////////////////////

// fonction modifiée : il parait intéressant de filtrer les postits par colonne, on rajoute un argument 
function listerPostIts($idBoard,$numColonne=false)
{
	// Liste les post-it associés à un board dont l'identifiant est fourni
	$SQL = "SELECT * FROM postits WHERE idBoard='$idBoard'"; 
	if ($numColonne) $SQL .= " AND numColonne='$numColonne'";
	return parcoursRs(SQLSelect($SQL));
}

function delPostIt($idPostIt)
{	
	// supprimer un post-it nécessite de supprimer aussi ses marqueurs ! 
	$SQL = "DELETE FROM postits WHERE id='$idPostIt'"; 
	SQLDelete($SQL);
	
	$SQL = "DELETE FROM marqueurs WHERE idPostit='$idPostIt'"; 
	SQLDelete($SQL);

	$SQL = "DELETE FROM commentaires WHERE idPostit='$idPostIt'"; 
	SQLDelete($SQL);
}

function mkPostIt($idBoard, $label="Nouveau Post-It",$avancement=0, $numColonne=1)
{
	if ($label === false) $label="Nouveau Post-It"; 
	if ($avancement === false) $avancement=0; 
	if ($numColonne === false) $numColonne=1; 
	
	// Cette fonction crée un nouveau post-it dans un board passé en paramètre et renvoie son identifiant
	// le post-it créé est initialement vide (ses champs numColonne et avancement sont à 0, son label est vide)
	$SQL = "INSERT INTO postits(idBoard, label, avancement, numColonne) VALUES('$idBoard', '$label', '$avancement', '$numColonne')"; 
	return SQLInsert($SQL);
}

function majPostIt($idPostIt, $label,$avancement, $numColonne)
{
	// Cette fonction met à jour un postIt dont l'identifiant est passé en paramètre
	// ON doit pouvoir vérifier la présence des différents critères
		$SQL = "UPDATE postits SET  "; 
		if ($label !== false) $SQL .= "label='$label',";
		if ($avancement !== false) $SQL .= "avancement='$avancement',";
		if ($numColonne !== false) $SQL .= "numColonne='$numColonne',";
		$SQL = rtrim($SQL,',');
		$SQL .= " WHERE id='$idPostIt'"; 
	return SQLUpdate($SQL);
}


//////////////////////////////////////////////////////////////////////////////

function mkMarqueur($idPostIt, $type, $valeur) {
	$SQL = "INSERT INTO marqueurs(idPostIt, type, valeur) VALUES('$idPostIt', '$type', '$valeur')"; 
	return SQLInsert($SQL);
}


function delMarqueur($idMarqueur) {
	$SQL = "DELETE FROM marqueurs WHERE id='$idMarqueur'"; 
	SQLDelete($SQL);
}

function listerMarqueurs($idPostIt) {
	$SQL = "SELECT * FROM marqueurs WHERE idPostIt='$idPostIt'"; 
echo $SQL;
	return parcoursRs(SQLSelect($SQL));
}

//////////////////////////////////////////////////////////////////////////////


function listerCommentaires($idPostIt)
{
	// Liste les post-it associés à un board dont l'identifiant est fourni
	$SQL = "SELECT * FROM commentaires WHERE idPostIt='$idPostIt' ORDER BY id ASC"; 
	return parcoursRs(SQLSelect($SQL));
}

function delCommentaire($idCommentaire)
{	
	// supprimer un post-it nécessite de supprimer aussi ses marqueurs ! 
	$SQL = "DELETE FROM commentaires WHERE id='$idCommentaire'"; 
	SQLDelete($SQL);
}

function mkCommentaire( $contenu,$idAuteur,$idPostIt)
{
	// Cette fonction crée un nouveau post-it dans un board passé en paramètre et renvoie son identifiant
	// le post-it créé est initialement vide (ses champs numColonne et avancement sont à 0, son label est vide)
	$SQL = "INSERT INTO commentaires(contenu, idAuteur,idPostIt ) VALUES('$contenu', '$idAuteur', '$idPostIt')"; 
	return SQLInsert($SQL);
}






















?>
