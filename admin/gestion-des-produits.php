<?php require_once("../inc/init.inc.php");
//--------------------------- TRAITEMENTS PHP --------------------------//

//-------------------------- VERIFICATION ADMIN ------------------------//
if(!internauteEstConnecteEtEstAdmin()) {
	header("location:../connexion.php");
	exit();
}
//-------------------------- VERIFICATION ADMIN ------------------------//

//------------------- ENREGISTREMENT D'UN PRODUIT ----------------------//
if(!empty($_POST)) {
	// debug($_POST);
	$photo_bdd = '';
	if(isset($_GET['action']) && $_GET['action'] == 'modification') {
		$photo_bdd = $_POST['photo_actuelle']; // en cas de modification, on récupère la photo actuelle.
	}

	if(!empty($_FILES['photo']['name'])) { // s'il y a une photo qui a été ajoutée
		$photo_bdd = URL . "photo/$_POST[reference]_" . $_FILES['photo']['name']; // cette variable nous permettera de sauvegarder le chemin dans la base
		$photo_dossier = RACINE_SITE . "photo/$_POST[reference]_" . $_FILES['photo']['name']; // cette variable nous permettera de sauvegarder la photo dans le dossier
		copy($_FILES['photo']['tmp_name'], $photo_dossier); // copy permet de sauvegarder un fichier sur le serveur
	}

	$id_produit = (isset($_GET['id_produit'])) ? $_GET['id_produit'] : 'NULL'; // s'il y a un id_produit dans l'url c'est que nous sommes dans le cas d'une modification
	$pdo->exec("REPLACE INTO produit (id_produit, reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) VALUES ('$id_produit', '$_POST[reference]', '$_POST[categorie]', '$_POST[titre]', '$_POST[description]', '$_POST[couleur]', '$_POST[taille]', '$_POST[sexe]', '$photo_bdd', '$_POST[prix]', '$_POST[stock]')");
	$content .= '<div class="alert alert-success">Le produit a bien été ajouté ;-) !</div>';
}
//------------------- ENREGISTREMENT D'UN PRODUIT ----------------------//

//-------------------- SUPPRESSION D'UN PRODUIT ------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'suppression') {
		$pdo->exec("DELETE FROM produit WHERE id_produit = $_GET[id_produit]");
}
//-------------------- SUPPRESSION D'UN PRODUIT ------------------------//

//-------------------- LIENS PRODUITS ------------------------//
$content .= '<a href="?page=gestion-des-produits&action=affichage">Affichage des produits</a><br>'; // lien affichage
$content .= '<a href="?page=gestion-des-produits&action=ajout">Ajout d\'un produit</a><br><br><hr><br>'; // lien ajout
//-------------------- LIENS PRODUITS ------------------------//

//--------------------- AFFICHAGE DES PRODUITS -------------------------//
if(isset($_GET['action']) && $_GET['action'] == "affichage") {
$resultat = $pdo->query('SELECT * FROM produit');
$content .= '<h2>Affichage des produits</h2>';
$content .= 'Nombre de produit(s) dans la boutique : ' . $resultat->rowCount();
$content .= '<table class="table"><tr>';
for($i = 0; $i < $resultat->columnCount(); $i++) { // boucle sur les colonnes
	$colonne = $resultat->getColumnMeta($i); // getColumnMeta récupère les informations sur les colonnes
	$content .= "<th>$colonne[name]</th>";
}
$content .= '<th colspan="2">Actions</th>';
$content .= '</tr>';
while($produits = $resultat->fetch(PDO::FETCH_ASSOC)) { // boucle sur les données
	$content .= '<tr>';
	foreach($produits as $indice => $valeur) {
		if($indice == 'photo')
			$content .= "<td><img src=\"$valeur\"></td>";
		else
			$content .= "<td>$valeur</td>";
	}
	$content .= '<td><a href="?page=gestion-des-produits&action=modification&id_produit=' . $produits['id_produit'] . '"><span class="glyphicon glyphicon-pencil"></span></a></td>'; // lien modification
	$content .= '<td><a href="?page=gestion-des-produits&action=suppression&id_produit=' . $produits['id_produit'] . '" onClick="return(confirm(\'En êtes vous certain ?\'))"><span class="glyphicon glyphicon-trash"></span></a></td></tr>'; // lien suppression
}
$content .= '</table><br><hr><br>';
}
//--------------------- AFFICHAGE DES PRODUITS -------------------------//

//------------------ MODIFICATION DES PRODUITS -------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'modification') {
	$r = $pdo->query("SELECT * FROM produit WHERE id_produit=$_GET[id_produit]"); // récupération des informations d'1 produit
	$produit = $r->fetch(PDO::FETCH_ASSOC); // accès aux données
}

	// si nous sommes dans le cas d'une modification, nous souhaitons pré-remplir le formulaire avec les informations actuelles (sinon, en cas d'ajout, les variables seront vides) :
	$id_produit = (isset($produit['id_produit'])) ? $produit['id_produit'] : '';
	$reference = (isset($produit['reference'])) ? $produit['reference'] : '';
	$categorie = (isset($produit['categorie'])) ? $produit['categorie'] : '';
	$titre = (isset($produit['titre'])) ? $produit['titre'] : '';
	$description = (isset($produit['description'])) ? $produit['description'] : '';
	$couleur = (isset($produit['couleur'])) ? $produit['couleur'] : '';
	$taille = (isset($produit['taille'])) ? $produit['taille'] : '';
	$sexe = (isset($produit['sexe'])) ? $produit['sexe'] : '';
	$photo = (isset($produit['photo'])) ? $produit['photo'] : '';
	$prix = (isset($produit['prix'])) ? $produit['prix'] : '';
	$stock = (isset($produit['stock'])) ? $produit['stock'] : '';
//------------------ MODIFICATION DES PRODUITS -------------------------//

//----------------- FORMULAIRE D'AJOUT D'UN PRODUIT --------------------//
require_once("../inc/haut.inc.php");
if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) {
	if(isset($_GET['id_produit'])) {
		$resultat = $pdo->query("SELECT * FROM produit WHERE id_produit = $_GET[id_produit]");
		$produit_actuel = $resultat->fetch(PDO::FETCH_ASSOC); 
	}
$content .= 'Bonjour <br> voici la gestion des produits.<hr>';
$content .= '
	<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" id="id_produit" name="id_produit" value="';
			if(isset($produit_actuel['id_produit'])) $content .= $produit_actuel['id_produit'];
			$content .= '">
		<div class="form-group">
			<label for="reference">Réference : </label><br>
			<input type="text" class="form-control" id="reference" name="reference" placeholder="Réference du produit" value="' . $reference . '"><br>
		</div>
		<div class="form-group">
			<label for="categorie">Catégorie : </label><br>
			<input type="text"  class="form-control" id="categorie" name="categorie" placeholder="Catégorie du produit" value="' . $categorie . '"><br>
		</div>
		<div class="form-group">
			<label for="titre">Titre : </label><br>
			<input type="text" id="titre" name="titre" class="form-control" placeholder="Titre du produit" value="' . $titre . '"><br>
		</div>
		<div class="form-group">
			<label for="description">Description : </label><br>
			<textarea name="description" class="form-control" placeholder="Description du produit">' . $description . '</textarea><br>
		</div>
		<div class="form-group">
			<label for="couleur">Couleur : </label><br>
			<input type="text" id="couleur"  class="form-control"name="couleur" placeholder="Couleur du produit" value="' . $couleur . '"><br>
		</div>
		<div class="form-group">
		<label for="taille">Taille : </label><br>
			<select name="taille" id="taille" class="form-control">
				<option value="S"';
					if($taille == 'S') $content .= ' selected';
				$content .= '>S</option>';
				$content .= '<option value="M"';
					if($taille == 'M') $content .= ' selected';
				$content .= '>M</option>';
				$content .= '<option value="L"';
					if($taille == 'L') $content .= ' selected';
				$content .= '>L</option>';
				$content .= '<option value="XL"';
					if($taille == 'XL') $content .= ' selected';
				$content .= '>XL</option>';
			$content .= '
			</select><br>
		</div>
		<div class="form-group">
			<label for="sexe">Sexe : </label><br>
			<select name="sexe" id="sexe" class="form-control">
				<option value="f" ';
					if($sexe == 'f') $content .= ' selected';
				$content .= '>Femme</option>';
				$content .= '<option value="m"';
					if($sexe == 'm') $content .= ' selected';
				$content .= '>Homme</option>';
				$content .= '<option value="mixte"';
					if($sexe == 'mixte') $content .= ' selected';
				$content .= '>Mixte</option>';
			$content .= '
			</select><br>
		</div>
		<div class="form-group">
			<label for="photo">Photo : </label>
			<input type="file"  class="form-control" name="photo" placeholder="Photo du produit">';
				if(!empty($photo))
				{
					$content .= 'photo actuelle : <img src="' . $photo . '" width="50">';
					$content .= '<input type="hidden" name="photo_actuelle" value="' . $photo . '">';
				}
		$content .= '<br>
		</div>
		<div class="form-group">
			<label for="prix">Prix : </label><br>
			<input type="text" class="form-control" name="prix" placeholder="Prix du produit" value="' . $prix . '"><br>
		</div>
		<div class="form-group">
			<label for="stock">Stock : </label><br>
			<input type="text" class="form-control" name="stock" placeholder="Stock du produit" value="' . $stock . '"><br>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-default" value="'; $content .= ucfirst($_GET['action']) . ' du produit';
			$content .= '">
		</div>
		</form>';
}
//----------------- FORMULAIRE D'AJOUT D'UN PRODUIT --------------------//
echo $content;
require_once("../inc/bas.inc.php");