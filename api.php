<?php

/**
 * fonction de connexion
 * Le fait d'en faire une fonction permet d'éviter les erreurs et rend le code plus propre
 * @return mysqli $conn instance de connexion  mysqli
 */
function connect() {
	$db_host = 'localhost';
	$db_user = 'root';
	$db_password = 'root';
	$db_db = 'produits';
	$db_port = 8889;

	$conn = new mysqli(
	$db_host,
	$db_user,
	$db_password,
	$db_db
	);

	if ($conn->connect_error) {
		echo 'Errno: '.$conn->connect_errno;
		echo '<br>';
		echo 'Error: '.$conn->connect_error;
		exit();
	}
	return $conn;
}

/**
 * désormais nous séparons les requêtes à la base de données du traitement des données pour les envoyer. Cette fonction sert à analyser (parse) les réusltats (results) provenant de la bdd et à les envoyer en tant que contenu JSON
 * 
 * cette fonction va donc envoyer une réponse au client web (javascript la prendra alors dans la variable "response" suite au await fetch())
 * 
 * @param $result objet de résultat de la bdd
 */
function parseResults($result) {
	// indication d'une entête à la réponse HTTP. Cette entête indique le type de contenu : ici du contenu JSON
	header("Content-Type: application/json");
	// on vérifie d'abord si les résultats ne sont pas vides
	if ($result->num_rows > 0) {
		// puis on créer un tableau vide si ce n'est aps le cas
		$res = array();
		// notez cette approche différente qui suit une logique de tableau plus "classique". Je passe par l'indice du tableau pour insérer des valeurs, comme en algorithmique. Vous pouvez aussi remplacer cela par array_push() qui prend moins de lignes de code 😜
		$idx = 0;
		// on sort les données de chaque rangée (row) en forme de tableau associatif
		while($row = $result->fetch_assoc()) {
			// on ajoute ces éléments
			$res[$idx] = $row;
			// on incrémente l'indice (pas nécessaire si on utilise array_push)
			$idx = $idx + 1;
		}
		// on affiche le tableau de tableaux associatifs, transformé en JSON par la fonction json_encode(). Cela signifie que la sortie sera du JSON pur. Si vous y mettez de l'HTML cela ne correspondra plus à l'entête application/json et retournera une erreur côté javascript qui sera complètement déboussolé le pauvre 🥲
		echo json_encode($res);
		// on utilise exit() pour bien s'assurer qu'on quitte le script PHP (il s'agit juste d'une précaution). Effectivement, l'API a renvoyé le contenu en formaty JSON nous n'avons plus besoin de rien faire d'autre. Sa tâche est effectué.
		exit();
	} else {
		echo "0 results";
	}
}

/**
 * cette focntion findAll() est comme en TD8 mais elle possède moins de lignes car elle ne s'occupe désormais que de trouver les éléments. 
 * Le traitement du réusltat obtenu est exporté vers parseResults() qui sera alors utilisé dans plusieurs fonctions.
 * 
 * 
 * @param mysqli $conn objet de connexion mysqli
 */
function findAll($conn) {
	$sql = "SELECT * FROM vetements";
	$result = $conn->query($sql);
	// le résultat a simplement besoin d'être analysé et envoyé au format JSON.
	// Oh quelle coincidence ! Nous avons justement une fonction prévue pour cela 🤓
	parseResults($result);
}

/**
 * fonction findBy() dédiée ) trouver (find) dans la base de données par (by) un filtre. bien que nous utilisons un seule filtre sur la colonne "gender", cette fonction est assez abstraite pour permettre de s'adapter à toutes les colonnes :) C'est pouruqoi nous considérons les arguments du nom de colonne et de la valeur à chercher
 * @param mysqli $conn objet de connexion mysqli
 * @param string $filterColumn nom de la colonne sur laquelle filtrer
 * @param string $filterValue valeur servant à filtrer
 */
function findBy($conn, $filterColumn, $filterValue) {
	// on utilise la méthode prepare() pour préparer notre requête SQL avec un paramètre anonyme
	$statement = $conn->prepare("SELECT * FROM vetements WHERE $filterColumn = ? ;");
	// mainetnant qu'elle est préparée on utilise la méthode bind_param() pour associer une valeur provenant d'une variable $filterValue
	// cette valeur est une string donc nous indiquons la lettre "s"
	$statement->bind_param("s", $filterValue);
	// nous exécutons cette requête préparée
	$statement->execute();
	// nous récupérons les résultats de cette exécution à l'aide de la méthode get_result()
	$result = $statement->get_result();
	// encore une fois, nous n'avons besoin que d'utiliser cette super focntion. 
	// C'est le but de toute fonction : simplifier le code par une logique et permettre de répéter cette logique sans avoir à tout recoder
	// les fonctions = l'outil du flemmard malin ! 😆
	parseResults($result);
}


// jusqu'ici notre code ne fait rien. Car nous avons défini plusieurs fonctions sans les appeler. Ce n'est aps grave, voyez cela comme des LEGOs : nous avons défini les briques et pouvons maintenant les utiliser ultra facilement :)

// commencons par la connexion. Hop : il suffit d'utiliser la fonction connect() et de récupérer sa sortie
$conn = connect();

// ici nous contrôler l'arriver de requêtes GET uniquement. Si la super variable (variable toujours présente qui informe du contexte comme $_GET $_POST $_SERVER $_COOKIES) n'est pas vide
if (count($_GET)){
	// ALORS nous vérifions si elle possède la clé filterby
	if (isset($_GET["filterby"])){
		// si c'est le cas nous utilisons la fonction findBy pour filtrer à l'aide des clés filterby et filterval qui proviennent des arguments de requête GET. concrètement, ils provient par exemple de :
		// http://localhost:8888/td9/api.php?filterby=gender&filterval=women vous voyez bien que filterby est associé à "gender" et filterval associé à women. Les clés ont donc une valeur, d'où notre récupération sous forme de tableau associatif
		findBy($conn, $_GET["filterby"], $_GET["filterval"]);
	}
} else {
	// si la requête GEt n'a pas de clé alors on rend quand même tout le contenu de la bdd
	findAll($conn);
}

// bien entendu il manque quelques vérifications (la présence de la clé filterval par exemple)

// n'oubliez pas de fermer la connexion !
$conn->close();

?>

