<?php require_once("../inc/init.inc.php");

//------------------------- VERIFICATION ADMIN ----------------------------//
if(!internauteEstConnecteEtEstAdmin()) {
	header("location:../gestion_membres.php");
	exit();
}
//------------------------- VERIFICATION ADMIN ---------------------------//

//---------------------------- LIENS MEMBRES -----------------------------//
$content .= '<a href="?page=gestion_membres&action=affichage">Affichage les membres <br> </a>';
$content .= '<a href="?page=gestion_membres&action=ajout">Ajout des membres </a><br><br><hr><br>'; // lien ajout
//---------------------------- LIENS MEMBRES -----------------------------//

//------------------------AFFICHAGE DES MEMBRES ----------------------------//
if(isset($_GET['action']) && $_GET['action'] == "affichage"){
    $resultat = $pdo->query('SELECT * FROM membre');
    $content .= '<h2>Affichage des membres</h2>';
    $content .= 'Nombre de membre(s) du site : ' . $resultat->rowCount();
    $content .= '<table class="table"><tr>';
    for($i = 0; $i < $resultat->columnCount(); $i++) { // boucle sur les colonnes
        $colonne = $resultat->getColumnMeta($i); // getColumnMeta récupère les informations sur les colonnes
        $content .= "<th>$colonne[name]</th>";
        }
    $content .= '<th colspan="2">Actions</th>';
    $content .= '</tr>';

    while($membre = $resultat->fetch(PDO::FETCH_ASSOC)) 
    {
        $content .= '<tr>';
        foreach($membre as $indice => $valeur) 
        {
			$content .= "<td>$valeur</td>";
	    }

        $content .= '<td><a href="?page=gestion_membres&action=modification&id_membre=' . $membre['id_membre'] . '"><span class="glyphicon glyphicon-pencil"></span></a></td>'; // lien modification
        $content .= '<td><a href="?page=gestion_membres&action=suppression&id_membre=' . $membre['id_membre'] . '" onClick="return(confirm(\'En êtes vous certain ?\'))"><span class="glyphicon glyphicon-trash"></span></a></td></tr>'; // lien suppression
    }
     $content .= '</table><br><hr><br>';
}
//------------------------------AFFICHAGE DES MEMBRES ---------------------------//

//------------------------------SUPPRIMER UN MEMBRE------------------------------//

if(isset($_GET['action']) && $_GET['action'] == 'suppression') {
		$pdo->exec("DELETE FROM membre WHERE id_membre = $_GET[id_membre]");
}
//------------------------------SUPPRIMER UN MEMBRE------------------------------//

//------------------ MODIFICATION DES PRODUITS -------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'modification') {
	$r = $pdo->query("SELECT * FROM membre WHERE id_membre = $_GET[id_membre]"); // récupération des informations d'1 produit
	$membre = $r->fetch(PDO::FETCH_ASSOC); // accès aux données
}

	// si nous sommes dans le cas d'une modification, nous souhaitons pré-remplir le formulaire avec les informations actuelles (sinon, en cas d'ajout, les variables seront vides) :
	$id_membre = (isset($membre['id_membre'])) ? $membre['id_membre'] : '';
	$pseudo = (isset($membre['pseudo'])) ? $membre['pseudo'] : '';
	$mdp = (isset($membre['mdp'])) ? $membre['mdp'] : '';
	$nom = (isset($membre['nom'])) ? $membre['nom'] : '';
	$prenom = (isset($membre['prenom'])) ? $membre['prenom'] : '';
	$email = (isset($membre['email'])) ? $membre['email'] : '';
	$civilite = (isset($membre['civilite'])) ? $membre['civilite'] : '';
	$ville = (isset($membre['ville'])) ? $membre['ville'] : '';
	$code_postal = (isset($membre['cp'])) ? $membre['cp'] : '';
	$adresse = (isset($membre['adresse'])) ? $membre['adresse'] : '';
	$statut = (isset($membre['statut'])) ? $membre['statut'] : '';
//------------------ MODIFICATION DES PRODUITS -------------------------//

//------------------- ENREGISTREMENT D'UN PRODUIT ----------------------//
	
	if($_POST) {
		foreach($_POST as $indice => $valeur) {
			$_POST[$indice] = addslashes($valeur);
		}
		$_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
		$id_membre = (isset($_GET['id_membre'])) ? $_GET['id_membre'] : 'NULL';
		$pdo->exec("REPLACE INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, ville,code_postal, adresse) VALUES ('$id_membre', '$_POST[pseudo]', '$_POST[mdp]', '$_POST[nom]', '$_POST[prenom]', '$_POST[email]', '$_POST[civilite]', '$_POST[ville]', '$_POST[cp]', '$_POST[adresse]')");
		$content .= '<div class="alert alert-success" role="alert">Inscription validée !</div>';
	}


//------------------- ENREGISTREMENT D'UN PRODUIT ----------------------//


//----------------- FORMULAIRE D'AJOUT D'UN PRODUIT --------------------//

require_once("../inc/haut.inc.php");

if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) {
	if(isset($_GET['id_membre'])) {
		$resultat = $pdo->query("SELECT * FROM membre WHERE id_membre = $_GET[id_membre]");
		$membre_actuel = $resultat->fetch(PDO::FETCH_ASSOC); 
	}

$content .= '
<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" id="id_membre" name="id_membre" value="';
			if(isset($membre_actuel['id_membre'])) $content .= $membre_actuel['id_membre'];
			$content .= '">
		<div class="form-group">
			<label for="pseudo">Pseudo : </label><br>
			<input type="text" class="form-control" placeholder="Votre pseudo" name="pseudo" id="pseudo" maxlength="20" pattern="[a-zA-Z0-9-_.]{3, 20}" title="caractères acceptés : a-z A-Z 0-9 .-_" value="' . $pseudo . '" required><br>
		</div>
		<div class="form-group">
			<label for="mdp">Mot de passe : </label><br>
			<input type="password" class="form-control" placeholder="Votre mot de passe" name="mdp" id="mdp" required value="' . $mdp . '"><br>
		</div>
		<div class="form-group">
			<label for="nom">Nom : </label><br>
			<input type="text" class="form-control" placeholder="Votre nom" name="nom" id="nom" value="' . $nom . '"><br>
		</div>
		<div class="form-group">
			<label for="prenom">Prenom : </label><br>
			<input type="text" class="form-control" placeholder="votre prénom" name="prenom" id="prenom" value="' . $prenom . '"><br>
		</div>
		<div class="form-group">
			<label for="email">Email : </label><br>
			<input type="email" class="form-control" placeholder="votre email" name="email" id="email" required value="' . $email . '"><br>

		<div>
			<label for="civilite">Civilité : </label>
			<input type="radio" name="civilite" id="civilite" value="m"';
		 		if($civilite == 'm') $content .= 'checked';
		 		$content .= '>
			Homme -- Femme
			<input type="radio" name="civilite" id="civilite" value="f"';
			 if($civilite == 'f') $content .= 'checked';
		 		$content .= '><br>
		</div>

		</div>
		<div class="form-group">
			<label for="ville">Ville : </label><br>
			<input type="text" class="form-control" placeholder="Votre ville" name="ville" id="ville" pattern="[a-zA-Z0-9-_.]{2, 25}" title="caractères acceptés : a-z A-Z 0-9 .-_" value="' . $ville . '"><br>
		</div>
		<div class="form-group">
			<label for="cp">Code postal : </label>
			<input type="text" class="form-control" placeholder="votre cp" name="cp" id="cp" pattern="[0-9]{5}" title="5 chiffres requis : 0-9" value="' . $code_postal . '"><br>
		</div>
		<div class="form-group">
			<label for="adresse">Adresse : </label><br>
			<input class="form-control" placeholder="Votre adresse" name="adresse" id="adresse" pattern="[a-zA-Z0-9-_.]{5, 50}" title="caractères acceptés : a-z A-Z 0-9 .-_" value="' . $adresse . '"><br>
		</div>
		<div class="form-group">
			<label for="statut">Statut : </label><br>
			<input type="text"  class="form-control" id="statut" name="statut" placeholder="Statut" value="' . $statut . '"><br>
		</div>
		
		<div class="form-group">
			<input type="submit" class="btn btn-default" value="'; $content .= ucfirst($_GET['action']) . ' d\'un membre';
			$content .= '">
		</div>
		</form>';
}

echo $content;

require_once("../inc/bas.inc.php");