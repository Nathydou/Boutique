<?php require_once("inc/init.inc.php");







?>
<?php require_once("inc/haut.inc.php"); ?>

<h1>Recuperation du mot de passe</h1>
<form method="post" action="">
	<label for="email">Email : </label>
	
	<input type="text" name="email" placeholder="Votre email" id="email" class="form-control"><br>

	<input type="submit" class="btn btn-default" value="Envoyer">
</form>

<?php require_once("inc/bas.inc.php"); ?>