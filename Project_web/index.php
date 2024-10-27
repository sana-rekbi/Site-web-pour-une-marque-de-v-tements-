<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>TempleDuSWAG</title>
		<link rel="icon" href="img/logo.png" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	</head>
	<body>

		<nav class="navbar navbar-expand-sm bg-light navbar-light fixed-top">
			<div class="container-fluid">
				<ul class="navbar-nav">
					<!-- remplacement du lien vers cette véritable page. Cela rechargera la page.
					évidemment, modifiez cette valeur vers celle qui correspond à votre installation -->
					<li class="nav-link" href="http://localhost:8888/td9/index.php">
						<!-- remplacement du lien de l'icône vers cette véritable page. Cela rechargera la page.
					évidemment, modifiez cette valeur vers celle qui correspond à votre installation -->
					<a class="navbar-brand" href="http://localhost:8888/td9/index.php"><img src="img/logo.png" height="24"></a>
					</li>
					<li class="nav-item">
						<!-- remplacement du lien vers cette véritable page. Cela rechargera la page.
						évidemment, modifiez cette valeur vers celle qui correspond à votre installation -->
						<a class="nav-link active" href="http://localhost:8888/td9/index.php"><i class="bi bi-house-fill"> Home</i></a>
					</li>
				</ul>
			</div>
		</nav>

		<div class="container-fluid text-center" style="margin-top:80px">
			<!-- remplacement du lien de l'icône vers cette véritable page. Cela rechargera la page.
			évidemment, modifiez cette valeur vers celle qui correspond à votre installation -->
			<a class="navbar-brand" href="http://localhost:8888/td9/index.php"><img src="img/logo.png" height="200"></a>
			<p>Bienvenue dans le super site des étudiants en L2 MIASHS 😊</p> 
		</div>


		<?php 
			$db_host = 'localhost';
			$db_user = 'root';
			$db_password = 'root';
			$db_db = 'produits';
			$db_port = 8889;

			// création de la connexion
			$conn = new mysqli($db_host, $db_user, $db_password, $db_db);

			// vérification de la connexion
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			/**
			 * Fonction dédiée à trouver tous les éléments
			 * trouver = find ; tous = all
			 * cette nomenclature suit la logique de Spring Boot en Java. Mais vous pouvez en utiliser une autre.
			 * findAll, findBy, findByID, findByName, etc.
			 * @param mysqli $conn l'objet de connexion mysqli
			 * @return array[] un tableau de tableaux associatifs
			 */
			function findAll($conn) {
				$sql = "SELECT * FROM vetements";
				$result = $conn->query($sql);
				$data = [];
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($data, $row);
					}
				} else {
					echo "Aucun résultat 😐";
				}
				return $data;
			}

			$data = findAll($conn);

			$conn->close();
		?>

		<div class="container mt-5">
			<div class="row">
				<?php
					$nbres = "<div class='col-sm-4'>
						<h5>". count($data) ." produits trouvés</h5>
					</div></div>";
					echo $nbres;
					echo '<div id="produitsAffichage" class="row gx-5 gy-5">';

					for ($i = 0; $i < count($data); $i++) {
						$genderIcon = "";
						if ($data[$i]['gender'] == "Women"){ $genderIcon = "<i class='bi bi-gender-female'></i>"; }
						else{ $genderIcon = '<i class="bi bi-gender-male"></i>';  } 

						// bien que la logique soit la même que dans le TD08, nous ajoutons ici le button (bouton) à la place d'un <span> pour l'image du genre de destination du produit
						$card = "
						<div class='col-sm-4'>
							<div class='card text-bg-light'>
								<img class='card-img-top' src='img/vetements/{$data[$i]['id']}.jpg' alt='Card image cap'>
								<div class='card-body'>
									<h4 class='card-title'>{$data[$i]['productDisplayName']}</h4>
									<p class='card-text'>
										<span class='badge bg-secondary'>{$data[$i]['articleType']}</span>
										<span class='badge bg-secondary'>{$data[$i]['baseColour']}</span>
										<span class='badge bg-dark'>{$data[$i]['year']}</span>
										<button class='btn btn-secondary btn-sm' onclick='filterByGender(\"" . $data[$i]["gender"] . "\")' >$genderIcon</button>
									</p>
								</div>
							</div>
						</div>";
						echo $card;
					}
				?>
			</div>
		</div>
	
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
		<script type="text/javascript" src="swag.js"></script>
  </body>
</html>
