<?php require_once("inc/init.inc.php");
//--------------------------- TRAITEMENTS PHP---------------------------//
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
	unset($_SESSION['membre']);
}
if(internauteEstConnecte()) {
	header('location:profil.php');
	exit();
}
if($_POST) {
	$resultat = $pdo->query("SELECT * FROM membre WHERE pseudo='$_POST[pseudo]'");
	if($resultat->rowCount() >= 1) {
		$membre = $resultat->fetch(PDO::FETCH_ASSOC);
		if(password_verify($_POST['mdp'], $membre['mdp'])) {
			$_SESSION['membre']['id_membre'] = $membre['id_membre'];
			$_SESSION['membre']['pseudo'] = $membre['pseudo'];
			$_SESSION['membre']['nom'] = $membre['nom'];
			$_SESSION['membre']['prenom'] = $membre['prenom'];
			$_SESSION['membre']['email'] = $membre['email'];
			$_SESSION['membre']['civilite'] = $membre['civilite'];
			$_SESSION['membre']['ville'] = $membre['ville'];
			$_SESSION['membre']['code_postal'] = $membre['code_postal'];
			$_SESSION['membre']['adresse'] = $membre['adresse'];
			$_SESSION['membre']['statut'] = $membre['statut'];
			
			header("location:profil.php");
		} else {
			$content .= '<div class="alert alert-danger" role="alert">Erreur de mot de passe</div>';
		}
	} else {
		$content .= '<div class="alert alert-danger" role="alert">Erreur de pseudo</div>';
	}
}
?>
<!----------------------------------------------------------------------->
<?php require_once("inc/haut.inc.php"); ?>
<?= $content; ?>
<form method="post" action="">
	<label for="pseudo">Pseudo : </label>
	<input type="text" name="pseudo" placeholder="Votre pseudo" id="pseudo" class="form-control"><br>
	
	<label for="mdp">Mot de passe : </label>
	<input type="password" name="mdp" placeholder="Votre mot de passe" id="mdp" class="form-control"><br>

	<label ><a href="mdp-oublie.php" >Mot de passe</a></label></br>

	<input type="submit" class="btn btn-default" value="Se connecter">
</form>
<?php require_once("inc/bas.inc.php"); ?>
