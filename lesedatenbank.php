<?php
	#Header
	header('Content-Type: text/html; charset=utf-8');

	#Requirements
	require 'vendor/autoload.php';
	use GuzzleHttp\Client;

	#Set MySQL login creds
	$GLOBALS['mySQLUsername'] = '';
	$GLOBALS['mySQLPassword'] = '';

	#Start session
	session_name('PHPSESSID-Ldb');
	session_start();
	$session_timeout = 900;
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	
	<!--  Dokumentkompatibilitätsmodus für IE-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Lesedatenbank</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
	<link href="/css/style.css" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" defer></script>
	<script src="/js/script.js" defer></script>
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container-fluid">
			<a class="navbar-brand" href="/">mint</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end">
				<ul class="navbar-nav">
					<li><a class="dropdown-item active" href="/lesedatenbank.php">Lesedatenbank</a></li>
				</ul>
			</div>
		</div>
	</nav>
	
	<div class="container marginTop">
		<div class="row justify-content-center">
			<div class="col-md-8 col-md-offset-2">
				<h1>Lesedatenbank</h1>
				<!-- Modal OK -->
				<div class="modal fade" id="okModal" tabindex="-1" aria-labelledby="okModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="okModalTitle">Lesedatenbank</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body" id="okModal-body"></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="okModal-OK">OK</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question -->
				<div class="modal fade" id="qModal" tabindex="-1" aria-labelledby="qModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qModalTitle">Lesedatenbank</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body" id="qModal-body"></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qModal-OK">Ja</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question Input -->
				<div class="modal fade" id="qiModal" tabindex="-1" aria-labelledby="qiModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qiModalTitle">Lesedatenbank</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div id="qiModal-body-text"></div><br>
								<input type="text" class="form-control" placeholder="Neuer Benutzername" id="qiModal-body-input">
								<div class="invalid-feedback">Geben Sie bitte einen anderen Benutzernamen ein!</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qiModal-OK">OK</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question Input Password-->
				<div class="modal fade" id="qipModal" tabindex="-1" aria-labelledby="qipModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qipModalTitle">Lesedatenbank</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" required></button>
							</div>
							<div class="modal-body">
								<div id="qipModal-body-text"></div><br>
								<div class="input-group">
									<input type="password" class="form-control" placeholder="Passwort" id="qipModal-body-password" required>
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="shidePassword('qipModal-body-password')">
											<i class="bi bi-eye-slash-fill" id="qipModal-body-password-eye"></i>
										</button>
									</div>
									<div class="invalid-feedback">Geben Sie bitte ein Passwort ein!</div>
								</div>
								<br>
								<div class="input-group">
									<input type="password" class="form-control" placeholder="Passwort wiederholen" id="qipModal-body-passwordRepeat" required>
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="shidePassword('qipModal-body-passwordRepeat')">
											<i class="bi bi-eye-slash-fill" id="qipModal-body-passwordRepeat-eye"></i>
										</button>
									</div>
									<div class="invalid-feedback">Die beiden Passwörter stimmen nicht überein!</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qipModal-OK">OK</button>
							</div>
						</div>
					</div>
				</div>
				<?php
					#HTML Login form snipptet
					$loginForm = '
						<div id="signin">
							<form action="/lesedatenbank.php" method="post" enctype="multipart/form-data" class="requires-validation" id="signin-form" novalidate>
								<div>
									<div class="input-group">
										<input type="text" class="form-control" name="username" placeholder="Benutzername" id="ldbSigninUsername" value="{{username}}" required>
										<div class="invalid-feedback">Geben Sie bitte einen Benutzernamen ein!</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" name="password" placeholder="Passwort" id="ldbSigninPassword" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'ldbSigninPassword\')">
												<i class="bi bi-eye-slash-fill" id="ldbSigninPassword-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">Geben Sie bitte ein Passwort ein!</div>
									</div>
								</div>
								<br>
								<input type="submit" value="Einloggen" class="btn btn-primary">
								<input type="button" value="Registrieren" class="btn btn-secondary" onclick="signInUp(\'up\')">
							</form>
						</div>

						<div id="signup" style="display: none;">
							<form action="/lesedatenbank.php" method="post" enctype="multipart/form-data" class="requires-validation" id="signup-form" novalidate>
								<div>
									<div class="input-group">
										<input type="text" class="form-control" name="username" placeholder="Benutzername" id="ldbSignupUsername" required>
										<div class="invalid-feedback">Geben Sie bitte einen anderen Benutzernamen ein!</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" name="password" placeholder="Passwort" id="ldbSignupPassword" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'ldbSignupPassword\')">
												<i class="bi bi-eye-slash-fill" id="ldbSignupPassword-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">Geben Sie bitte ein Passwort ein!</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" placeholder="Passwort wiederholen" id="ldbSignupPasswordRepeat" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'ldbSignupPasswordRepeat\')">
												<i class="bi bi-eye-slash-fill" id="ldbSignupPasswordRepeat-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">Die beiden Passwörter stimmen nicht überein!</div>
									</div>
								</div>
								<br>
								<input type="text" name="cmd" value="register" style="display:none;">
								<input type="button" value="Einloggen" class="btn btn-secondary" onclick="signInUp(\'in\')">
								<input type="submit" value="Registrieren" class="btn btn-primary">
							</form>
						</div>
					';

					function hardClean($string) {
						return strtolower(preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-]+/', '', $string));
					}

					function clean($string) {
						return preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?\^°´<>|+*~#()\[\]{}.:,; ]+/', '', $string);
					}

					function userExists($username) {
						#Initialize DB
						$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);

						$username = $_POST['username'];

						#Get data table
						$sql = "SELECT * FROM data;";
						$data = $pdo->query($sql)->fetchAll();

						#Check if user exists
						$exists = false;
						foreach ($data as $user) {
							if ($user[0] == $username) {
								$exists = true;
							}
						}

						#Return result
						return $exists;
					}

					function authenticate($username, $password) {
						#Initialize DB
						$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);

						#Get password hash for username
						$sql = "SELECT * FROM data WHERE username='$username'";
						$passwordHash = $pdo->query($sql)->fetchAll()[0]['passwordHash'];

						#Return result
						return password_verify($password, $passwordHash);
					}

					function content() {
						#Get parameters
						$username = $_SESSION['username'];
						$password = $_SESSION['password'];

						#Get selected table
						$selectedTable = '';
						if (isset($_GET['table'])) {
							$selectedTable = $_GET['table'];
						}

						#HTML Content snippet
						$html = '
							<div class="row">
								<div class="col-auto">
									<form action="/lesedatenbank.php" method="get" enctype="multipart/form-data">
										<div class="input-group">
											<select class="form-select" id="ldbTableSelect" name="table" onchange="this.form.submit()"></select>
											<input type="text" class="form-control" id="ldbTableSelect-text" placeholder="Name" style="display: none;">
											<button type="button" class="btn btn-outline-secondary" id="ldbEditCat-button" onclick="ldbEditCat()"><i class="bi bi-pencil-fill"></i></button>
											<button type="button" class="btn btn-outline-secondary" onclick="ldbDeleteCat()"><i class="bi bi-trash3-fill"></i></button>
										</div>
									</form>
								</div>
								<div class="col-auto">
									<form>
										<div class="input-group">
											<input type="text" class="form-control" id="ldbAddCat-catName" placeholder="Name">
											<input type="button" value="+" class="btn btn-outline-secondary" onclick="ldbAddCat()">
										</div>
									</form>
								</div>
							</div>
							<br>
							<div id="content"></div>
							<br>
							<div  class="row">
								<div class="col-auto">
									<form>
										<div class="input-group">
											<input type="text" class="form-control" id="ldbAddBook-bookName" placeholder="Name">
											<input type="text" class="form-control" id="ldbAddBook-authorName" placeholder="Autor">
											<span class="input-group-text">
												<input type="checkbox" class="form-check-input" id="ldbAddBook-read">
											</span>
											<input type="button" value="+" class="btn btn-outline-secondary" onclick="ldbAddBook()">
										</div>
									</form>
								</div>
							</div>
							<br>
							<h3>Kontoverwaltung</h3>
							<button type="button" class="btn btn-primary" onclick="ldbChangeUsername()">Benutzername ändern</button>
							<button type="button" class="btn btn-primary" onclick="ldbChangePassword()">Passwort ändern</button>
							<button type="button" class="btn btn-primary" onclick="ldbDeleteAccount()">Konto löschen</button>
							<br><br>
						';

						#Get list of categories
						$client = new GuzzleHttp\Client(['base_uri' => 'https://mint/', 'verify' => false]);
						$response = json_decode($client->request('GET', '/api/lesedatenbank.php', [
							'query' => [
								'username' => $username,
								'password' => $password,
								'cmd' => 'getCategories'
							]
						])->getBody(), true)['content'];

						#Build head - Categorie selecter
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$tableSelect = $doc->getElementById('ldbTableSelect');
						
						if (!$selectedTable) {
							if ((bool) $response) {
								$selectedTable = $response[0];
							}
						}

						foreach ($response as $table) {
							$hardCleanedTable = hardClean($table);
							$cleanedTable = clean($table);

							$fragment = $doc->createDocumentFragment();

							if ($cleanedTable == $selectedTable) {
								$fragment->appendXML("<option id=\"$hardCleanedTable\" value=\"$cleanedTable\" selected=\"selected\">$cleanedTable</option>");
							} else {
								$fragment->appendXML("<option id=\"$hardCleanedTable\" value=\"$cleanedTable\">$cleanedTable</option>");
							}
							$tableSelect->appendChild($fragment);
						}
						
						#If a table is selected
						if ($selectedTable) {
							#Get list of books (in category)
							$response = json_decode($client->request('GET', '/api/lesedatenbank.php', [
								'query' => [
									'username' => $username,
									'password' => $password,
									'cmd' => 'getBooks',
									'categoryName' => $selectedTable
								]
							])->getBody(), true)['content'];

							#Build head - Book list
							$bodyHTML = '';

							foreach ($response as $book) {
								$name = $book['bookName'];
								$author = $book['author'];
								$hardCleanedName = hardClean($name);

								if ($book['read']) {
									$bodyHTML .= "
										<div class=\"input-group\" id=\"$hardCleanedName-group\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-name\" value=\"$name\" name=\"$name\" readonly onfocus=\"ldbEditBook($(this).attr('id'))\" onfocusout=\"ldbSaveBook($(this).attr('id'))\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-author\" value=\"$author\" readonly onfocus=\"ldbEditBook($(this).attr('id'))\" onfocusout=\"ldbSaveBook($(this).attr('id'))\">
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" id=\"$hardCleanedName-read\" checked onchange=\"ldbSaveBook($(this).attr('id'))\">
											</span>
											<button type=\"button\" class=\"btn btn-outline-secondary\" id=\"$hardCleanedName-delete\" onclick=\"ldbDeleteBook($(this).attr('id'))\"><i class=\"bi bi-trash3-fill\"></i></button>
										</div>
									";
								} else {
									$bodyHTML .= "
										<div class=\"input-group\" id=\"$hardCleanedName-group\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-name\" value=\"$name\" name=\"$name\" readonly onfocus=\"ldbEditBook($(this).attr('id'))\" onfocusout=\"ldbSaveBook($(this).attr('id'))\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-author\" value=\"$author\" readonly  onfocus=\"ldbEditBook($(this).attr('id'))\" onfocusout=\"ldbSaveBook($(this).attr('id'))\">
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" id=\"$hardCleanedName-read\" onchange=\"ldbSaveBook($(this).attr('id'))\">
											</span>
											<button type=\"button\" class=\"btn btn-outline-secondary\" id=\"$hardCleanedName-delete\" onclick=\"ldbDeleteBook($(this).attr('id'))\"><i class=\"bi bi-trash3-fill\"></i></button>
										</div>
									";
								}
							}

							#Maybe show empty list note
							if ($bodyHTML == '') {
								$bodyHTML = '<span>Sie haben noch keine Bücher hinzugefügt.</span>';
							}
						} else {
							$bodyHTML = '<span>Sie haben noch keine Kategorien erstellt.</span>';
						}

						#Add HTML to body
						$helper = new DOMDocument();
						$helper->loadHTML(mb_convert_encoding($bodyHTML, 'HTML-ENTITIES', 'UTF-8'));

						$contentDiv = $doc->getElementById('content');
						$contentDiv->appendChild($doc->importNode($helper->documentElement, true));

						#Return HTML
						echo $doc->saveHTML();
					}


					#Check if it's a login
					if (isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['cmd'])) {
						$username = $_POST['username'];
						$password = $_POST['password'];

						#Check if user exists
						if (userExists($username)) {
							#Try to authenticate user
							if (authenticate($username, $password)) {
								#Set session data
								$_SESSION['username'] = $username;
								$_SESSION['password'] = $password;
								$_SESSION['start_time'] = time();

								#Return HTML
								content();
							} else {
								#Return error
								echo "Das von Ihnen eingegebene Passwort ist falsch.<br><a href=\"/lesedatenbank.php?username=$username\">Hier</a> können Sie versuchen, sich noch einmal einzuloggen.";
							}
						} else {
							#Return error
							echo 'Der von Ihnen eingegebene Benutzername existiert nicht.<br><a href="/lesedatenbank.php">Hier</a> können Sie versuchen, sich noch einmal einzuloggen.';
						}
					#Check if it's a register request
					} elseif (isset($_POST['cmd']) && $_POST['cmd'] == 'register') {
						$username = $_POST['username'];
						$password = $_POST['password'];

						#Check if user already exists
						if (userExists($username)) {
							#Return error
							echo 'Der von Ihnen eingegebene Benutzername existiert bereits.<br><a href="/lesedatenbank.php?signup">Hier</a> können Sie versuchen, sich mit einem anderen Benutzernamen zu registrieren.';
						} else {
							#Register user
							$client = new GuzzleHttp\Client(['base_uri' => 'https://mint/', 'verify' => false]);
							$response = $client->request('POST', '/api/lesedatenbank.php', [
								'form_params' => [
									'username' => $username,
									'password' => $password,
									'cmd' => 'register'
									]
							])->getBody();

							#Set session cookies
							$_SESSION['username'] = $username;
							$_SESSION['password'] = $password;
							$_SESSION['start_time'] = time();

							#Return response
							echo json_decode($response, true)['content'];

							#Return redirect
							echo '<br><a href="/lesedatenbank.php">Weiter</a>';
						}
					} else {
						#Check if session is expired
						if(!isset($_SESSION['start_time']) || (time() - $_SESSION['start_time']) > $session_timeout) {
							#If session has username
							if (isset($_SESSION['username'])) {
								#Get username
								$username = $_SESSION['username'];
							} else {
								$username = '';
							}

							#Destroy session
							session_destroy();

							#Return login form
							echo str_replace('{{username}}', $username, $loginForm);
						#Check if session is running
						} elseif (isset($_SESSION['start_time'])) {
							#Return HTML
							content();
						} else {
							#Return login form
							echo str_replace('{{username}}', $_GET['username'], $loginForm);
						}
					}
				?>
				<br>
				<a href="/md.php?file=ldb-api-doc">API Dokumentation</a>
				<br><br>
			</div>
		</div>
	</div>

	<!-- Footer -->
	<footer class="bg-dark text-light">
		© Pascal Dietrich, 2022</a>
		<span id="lang"></span>
	</footer>

	<script>
		window.addEventListener("load", function() {
			//Signup form validation
			ldbSignupFormValidation();
			
			//Signin form validation
			const signinForm = document.getElementById("signin-form");
			signinForm.addEventListener('submit', function (event) {
                if (!signinForm.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                signinForm.classList.add('was-validated')
            }, false)

			
			let searchParams = new URLSearchParams(window.location.search);
			if (searchParams.has("signup")) {
				signInUp("up");
			}
		});
	</script>
</body>
</html>
