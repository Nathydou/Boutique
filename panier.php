<?php require_once('inc/init.inc.php');
//----------------------- AJOUT DANS PANIER ----------------------------//
if(isset($_POST['ajout_panier'])) {
	$r = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_POST[id_produit]'");
	$produit = $r->fetch(PDO::FETCH_ASSOC);
	ajoutProduitDansPanier($produit['id_produit'], $produit['titre'], $produit['reference'], $_POST['quantite'], $produit['prix']);
}

//------------------------- VIDER LE PANIER ----------------------------//
if(isset($_GET['action']) && $_GET['action'] == "vider") {
	unset($_SESSION['panier']);
}

//-------------------- SUPPRESSION D'UN PRODUIT ------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'suppression') {
	retireProduitPanier($_GET['id_produit']);
}

//---------------------------- PAIEMENT --------------------------------//
if(isset($_POST['payer'])) {
	for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
		$resultat = $pdo->query("SELECT * FROM produit WHERE id_produit = '" . $_SESSION['panier']['id_produit'][$i] . "'");
		$produit = $resultat->fetch(PDO::FETCH_ASSOC);
		if($produit['stock'] < $_SESSION['panier']['quantite'][$i]) {
			if($produit['stock'] > 0) { // encore un peu de stock
				$_SESSION['panier']['quantite'][$i] = $produit['stock'];
				$content .= '<div class="alert alert-warning" role="alert">La quantité du produit n° ' . $_SESSION['panier']['id_produit'][$i] . ' a été réduite car notre stock était insuffisant, veuillez vérifier vos achats.</div>';
			} else { // plus de stock
				$content .= '<div class="alert alert-warning" role="alert">Le produit n° ' . $_SESSION['panier']['id_produit'][$i] . ' a été retiré de votre panier car nous sommes en rupture de stock, veuillez vérifier vos achats.</div>';
				retireProduitPanier($_SESSION['panier']['id_produit'][$i]);
				$i--;
			}
			$erreur = true;
		}
	}
	if(!isset($erreur)) {
		$pdo->exec("INSERT INTO commande (id_membre, montant, date_enregistrement) VALUES ('" . $_SESSION['membre']['id_membre'] . "', '" . montantTotal() . "', NOW())");
		$id_commande = $pdo->lastInsertId();
		for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
			$pdo->exec("INSERT INTO details_commande (id_commande, id_produit, quantite, prix) VALUES ('" . $id_commande . "', '" . $_SESSION['panier']['id_produit'][$i] . "', '" . $_SESSION['panier']['quantite'][$i] . "', '" . $_SESSION['panier']['prix'][$i] . "')");
			$pdo->exec('UPDATE produit SET stock = stock - "' . $_SESSION['panier']['quantite'][$i] . '" WHERE id_produit = "' . $_SESSION['panier']['id_produit'][$i] . '"');
		}
		unset($_SESSION['panier']);
		$content .= '<div class="alert alert-success" role="alert">Merci pour votre commande, votre n° de suivi est le ' . $id_commande . '.</div>';
	}
}

//----------------------- AFFICHAGE DU PANIER --------------------------//
$content .= '<table class="table">';
$content .= '<tr><th>id_produit</th><th>titre</th><th>reference</th><th>quantité</th><th>prix</th>';
$content .= '<th colspan="1">supprimer</th></tr>';
if(empty($_SESSION['panier']['id_produit'])) { // panier vide
	$content .= '<tr><td colspan="6">Votre panier est vide</td></tr>';
}
else {
	for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
		$content .= '<tr>';
		$content .= '<td>' . $_SESSION['panier']['id_produit'][$i] . '</td>';
		$content .= '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
		$content .= '<td>' . $_SESSION['panier']['reference'][$i] . '</td>';
		$content .= '<td>' . $_SESSION['panier']['quantite'][$i] . '</td>';
		$content .= '<td>' . $_SESSION['panier']['prix'][$i] . ' €</td>';
		$content .= '<td><a href="?page=panier&action=suppression&id_produit=' . $_SESSION['panier']['id_produit'][$i] . '" onClick="return(confirm(\'En êtes vous certain ?\'))"><span class="glyphicon glyphicon-trash"></span></a></td>'; // lien suppression
		$content .= '</tr>';
	}
	$content .= '<tr><th colspan="6">Montant total de vos achats&nbsp;:&nbsp;' . montantTotal() . ' €</th></tr>';
	if(internauteEstConnecte()) {
		$content .= '<form method="post" action="">';
		$content .= '<tr><td colspan="6"><input type="submit" name="payer" value="Valider le paiement" class="btn btn-default"></td></tr>';
		$content .= '</form>';
	} else {
		$content .= '<tr><td colspan="6">Veuillez vous <a href="inscription.php">inscrire</a> ou vous <a href="connexion.php">connecter</a> afin de pouvoir payer</td></tr>';
	}
	$content .= "<tr><td colspan='6'><a href='?action=vider'>Vider mon panier</a></td></tr>";
}
$content .= '</table>';
$content .= "<i>Réglement par CHÈQUE uniquement à l'adresse suivante&nbsp;:&nbsp;74 Avenue Denfert-Rochereau, 75014 Paris</i><br>";
?>
<?php require_once('inc/haut.inc.php'); ?>
<h1>Panier</h1>
<?= $content; ?>
<?php require_once('inc/bas.inc.php'); ?>