<?php require_once("../inc/init.inc.php");

if(!internauteEstConnecteEtEstAdmin()) {
	header("location:../connexion.php");
	exit();
}

//-------------------- SUPPRESSION D'UN PRODUIT ------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'suppression') {
		$pdo->exec("DELETE FROM details_commande WHERE id_commande = $_GET[id_commande]");
}

$content .= '<a href="?page=gestion-des-produits&action=affichage">Affichage des produits</a><br>'; // lien affichage

//--------------------- Gestion DES PRODUITS -------------------------//

if(isset($_GET['action']) && $_GET['action'] == "affichage") {
$resultat = $pdo->query('SELECT c.id_commande, c.date_enregistrement, c.montant, c.etat, c.id_membre, p.id_produit, p.titre, p.photo, d.quantite FROM commande c, produit p, details_commande d WHERE c.id_commande = d.id_commande AND d.id_produit = p.id_produit');
// $content .= debug($resultat);
$content .= '<h2>Gestion commande</h2>';
$content .= 'Nombre de commande : ' . $resultat->rowCount();
$content .= '<table class="table"><tr>';
for($i = 0; $i < $resultat->columnCount(); $i++) { // boucle sur les colonnes
	$colonne = $resultat->getColumnMeta($i); // getColumnMeta récupère les informations sur les colonnes
	$content .= "<th>$colonne[name]</th>";
}
$content .= '<th colspan="2">Actions</th>';
$content .= '</tr>';
while($produits = $resultat->fetch(PDO::FETCH_ASSOC)) { // boucle sur les données
// $content .= debug($produits);
	$content .= '<tr>';
	foreach($produits as $indice => $valeur) {
		if($indice == 'photo')
			$content .= "<td><img src=\"$valeur\"></td>";
		else
			$content .= "<td>$valeur</td>";
	}
	$content .= '<td><a href="?page=gestion-des-produits&action=modification&id_commande=' . $produits['id_commande'] . '"><span class="glyphicon glyphicon-pencil"></span></a></td>'; // lien modification
	$content .= '<td><a href="?page=gestion-des-produits&action=suppression&id_commande=' . $produits['id_commande'] . '" onClick="return(confirm(\'En êtes vous certain ?\'))"><span class="glyphicon glyphicon-trash"></span></a></td></tr>'; // lien suppression
}
$content .= '</table><br><hr><br>';
}

//------------------ MODIFICATION DES PRODUITS -------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'modification') {
	$r = $pdo->query("SELECT etat FROM commande WHERE id_commande = $_GET[id_commande]"); // récupération des informations d'1 produit
	$produit = $r->fetch(PDO::FETCH_ASSOC); // accès aux données
}
	$etat = (isset($produit['etat'])) ? $produit['etat'] : '';



require_once("../inc/haut.inc.php");

$content .= 'Bonjour <br>Gestion des commandes.<hr>';
$content .= '

	<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" id="id_produit" name="id_produit" value="';
			if(isset($produit_actuel['id_commande'])) $content .= $produit_actuel['id_commande'];
			$content .= '">

		<input type="hidden" id="id_produit" name="id_produit" value="';
			if(isset($produit_actuel['id_article'])) $content .= $produit_actuel['id_article'];
			$content .= '">

		<input type="hidden" id="id_produit" name="id_produit" value="';
			if(isset($produit_actuel['id_membre'])) $content .= $produit_actuel['id_membre'];
			$content .= '">

	</form>';

	echo $content;
require_once("../inc/bas.inc.php");