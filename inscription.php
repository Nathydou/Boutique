<?php require_once("inc/init.inc.php"); ?>
<?php
	if($_POST) {
		// debug($_POST); //debug($pdo);
		$erreur = '';
		if(strlen($_POST['pseudo']) <= 3 || strlen($_POST['pseudo']) > 20) {
			$erreur .= '<div class="alert alert-danger" role="alert">Erreur taille pseudo</div>';
		}
		if(!preg_match('#^[a-zA-Z0-9.-_]+$#', $_POST['pseudo'])) {
			$erreur .= '<div class="alert alert-danger" role="alert">Erreur format pseudo</div>';
		}
		$r = $pdo->query("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]'");
		if($r->rowCount() >= 1) {
			$erreur .= '<div class="alert alert-danger" role="alert">Pseudo indisponible !</div>';
		}
		foreach($_POST as $indice => $valeur) {
			$_POST[$indice] = addslashes($valeur);
		}
		$_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
		if(empty($erreur)) {
			$pdo->exec("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville,code_postal, adresse) VALUES ('$_POST[pseudo]', '$_POST[mdp]', '$_POST[nom]', '$_POST[prenom]', '$_POST[email]', '$_POST[civilite]', '$_POST[ville]', '$_POST[cp]', '$_POST[adresse]')");
			$content .= '<div class="alert alert-success" role="alert">Inscription validée !</div>';
		}
		$content .= $erreur;
	}
?>
<?php require_once("inc/haut.inc.php"); ?>
<?= $content; ?>
<form method="post" action="">
	<label for="pseudo">Pseudo : </label>
	<input type="text" class="form-control" placeholder="Votre pseudo" name="pseudo" id="pseudo" maxlength="20" pattern="[a-zA-Z0-9-_.]{3, 20}" title="caractères acceptés : a-z A-Z 0-9 .-_" required><br>
	
	<label for="mdp">Mot de passe : </label>
	<input type="password" class="form-control" placeholder="Votre mot de passe" name="mdp" id="mdp" required><br>
	
	<label for="nom">Nom : </label>
	<input type="text" class="form-control" placeholder="Votre nom" name="nom" id="nom"><br>
	
	<label for="prenom">Prénom : </label>
	<input type="text" class="form-control" placeholder="votre prénom" name="prenom" id="prenom"><br>
	
	<label for="email">Email : </label>
	<input type="email" class="form-control" placeholder="votre email" name="email" id="email" required><br>
	
	<label for="civilite">Civilité : </label>
	<input type="radio" name="civilite" id="civilite" value="m" checked>
	Homme -- Femme
	<input type="radio" name="civilite" id="civilite" value="f"><br>
	
	<label for="ville">Ville : </label>
	<input type="text" class="form-control" placeholder="Votre ville" name="ville" id="ville" pattern="[a-zA-Z0-9-_.]{2, 25}" title="caractères acceptés : a-z A-Z 0-9 .-_"><br>
	
	<label for="cp">Code postal : </label>
	<input type="text" class="form-control" placeholder="votre cp" name="cp" id="cp" pattern="[0-9]{5}" title="5 chiffres requis : 0-9"><br>
	
	<label for="adresse">Adresse : </label>
	<textarea class="form-control" placeholder="Votre adresse" name="adresse" id="adresse" pattern="[a-zA-Z0-9-_.]{5, 50}" title="caractères acceptés : a-z A-Z 0-9 .-_"></textarea><br>

	<input type="submit" name="inscription" value="S'inscrire" class="btn btn-default"><br>
</form>

<?php require_once("inc/bas.inc.php"); ?>