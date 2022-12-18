<?php
	#Header
	header('Content-Type: text/html; charset=utf-8');

	$baseURL = 'https://mint.jojojux.de/';

	#Requirements
	require 'vendor/autoload.php';
	use GuzzleHttp\Client;

	#Start session
	session_name('PHPSESSID-booksDB');
	session_start();
	$session_timeout = 86400;
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	
	<!--  Dokumentkompatibilitätsmodus für IE-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="Die offizielle Website von mint.">
	<meta name="author" content="Pascal Dietrich">
	<meta name="copyright" content="Pascal Dietrich">
	<meta name="keywords" content="mint, Pascal Dietrich, lesen, Bücher, Datenbank">

	<meta property="og:image" content="https://mint.jojojux.de/images/logo.png" />
	<meta property="og:title" content="mint | booksDB" />
	<meta property="og:description" content="Die offizielle Website von mint." />
	<meta property="og:url" content="https://mint.jojojux.de/" />

	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="apple-touch-startup-image" href="/src/images/favicons/ios-startup.png">

	<link rel="icon" type="image/png" sizes="32x32" href="/src/images/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/src/images/favicons/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/src/images/favicons/apple-touch-icon-180x180.png">
	<link rel="apple-touch-icon" sizes="167x167" href="/src/images/favicons/apple-touch-icon-167x167.png">
	<link rel="mask-icon" href="/src/images/favicons/safari-pinned-tab.svg" color="#fff">
	<link rel="manifest" href="/src/images/favicons/site.webmanifest">

	<title>mint | booksDB</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
	<link href="/src/css/style.css" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="https://langmng.glitch.me/langmng.js"></script>
	<script src="/src/js/script.js" defer></script>
	<!-- Cookie Banner -->
	<script src="/src/js/cookie-banner.js" defer></script>
	<!-- Langmng -->
	<script>
		langmng.loadAndPut("/lang.json", langmng.cached(navigator.language.split("-")[0]), function() {
			langmng.selector("#lang");
			$("#lang_selector").on('change', function() {
				document.cookie = "lang=" + $("#lang_selector").find(":selected").val();
				location.reload();
			});
		});
	</script>
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
					<li class="nav-item"><a href="/" class="nav-link" langid="nav.home">Startseite</a></li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle active" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" langid="nav.tools">Tools</a>
						<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
							<li><a class="dropdown-item active" href="/bdb.php" langid="nav.tools.booksDB">booksDB</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	
	<div class="container marginTop">
		<div class="row justify-content-center">
			<div class="col-md-8 col-md-offset-2">
				<h1 langid="tools.booksDB.title">booksDB</h1>
				<!-- Modal OK -->
				<div class="modal fade" id="okModal" tabindex="-1" aria-labelledby="okModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="okModalTitle" langid="tools.booksDB.title">booksDB</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body" id="okModal-body"></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="okModal-OK" langid="tools.booksDB.ok">OK</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question -->
				<div class="modal fade" id="qModal" tabindex="-1" aria-labelledby="qModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qModalTitle" langid="tools.booksDB.title">booksDB</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body" id="qModal-body"></div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qModal-OK" langid="tools.booksDB.yes">Ja</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question Input -->
				<div class="modal fade" id="qiModal" tabindex="-1" aria-labelledby="qiModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qiModalTitle" langid="tools.booksDB.title">booksDB</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div id="qiModal-body-text"></div><br>
								<input type="text" class="form-control" id="qiModal-body-input">
								<div class="invalid-feedback" langid="tools.booksDB.enterAnotherUsername">Geben Sie bitte einen anderen Benutzernamen ein!</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qiModal-OK" langid="tools.booksDB.ok">OK</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal Question Input Password-->
				<div class="modal fade" id="qipModal" tabindex="-1" aria-labelledby="qipModalTitle" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="qipModalTitle" langid="tools.booksDB.title">booksDB</h5>
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
									<div class="invalid-feedback" langid="tools.booksDB.enterPassword">Geben Sie bitte ein Passwort ein!</div>
								</div>
								<br>
								<div class="input-group">
									<input type="password" class="form-control" placeholder="Passwort wiederholen" id="qipModal-body-passwordRepeat" required>
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="shidePassword('qipModal-body-passwordRepeat')">
											<i class="bi bi-eye-slash-fill" id="qipModal-body-passwordRepeat-eye"></i>
										</button>
									</div>
									<div class="invalid-feedback" langid="tools.booksDB.passwordsDontMatch">Die beiden Passwörter stimmen nicht überein!</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="qipModal-OK">OK</button>
							</div>
						</div>
					</div>
				</div>
				<?php
					include 'lang.php';
				
					$tableParameter = '';
					if (isset($_GET['table'])) {
						$tableParameter = '?table=' . $_GET['table'];
					}
				
					#HTML Login form snipptet
					$loginForm = sprintf('
						<div id="signin">
							<form action="/bdb.php%s" method="post" enctype="multipart/form-data" class="requires-validation" id="signin-form" novalidate>
								<div>
									<div class="input-group">
										<input type="text" class="form-control" name="username" placeholder="%s" id="bdbSigninUsername" value="{{username}}" required>
										<div class="invalid-feedback">%s</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" name="password" placeholder="%s" id="bdbSigninPassword" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'bdbSigninPassword\')">
												<i class="bi bi-eye-slash-fill" id="bdbSigninPassword-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">%s</div>
									</div>
								</div>
								<br>
								<input type="submit" value="%s" class="btn btn-primary">
								<input type="button" value="%s" class="btn btn-secondary" onclick="signInUp(\'up\')">
							</form>
						</div>

						<div id="signup" style="display: none;">
							<form action="/bdb.php" method="post" enctype="multipart/form-data" class="requires-validation" id="signup-form" novalidate>
								<div>
									<div class="input-group">
										<input type="text" class="form-control" name="username" placeholder="%s" id="bdbSignupUsername" required>
										<div class="invalid-feedback">%s</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" name="password" placeholder="%s" id="bdbSignupPassword" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'bdbSignupPassword\')">
												<i class="bi bi-eye-slash-fill" id="bdbSignupPassword-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">%s</div>
									</div>
									<br>
									<div class="input-group">
										<input type="password" class="form-control" placeholder="%s" id="bdbSignupPasswordRepeat" required>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" onclick="shidePassword(\'bdbSignupPasswordRepeat\')">
												<i class="bi bi-eye-slash-fill" id="bdbSignupPasswordRepeat-eye"></i>
											</button>
										</div>
										<div class="invalid-feedback">%s</div>
									</div>
								</div>
								<br>
								<input type="text" name="cmd" value="register" style="display:none;">
								<input type="button" value="%s" class="btn btn-secondary" onclick="signInUp(\'in\')">
								<input type="submit" value="%s" class="btn btn-primary">
							</form>
						</div>
					', $tableParameter, getString('tools.booksDB.username'), getString('tools.booksDB.enterUsername'), getString('tools.booksDB.password'), getString('tools.booksDB.enterPassword'), getString('tools.booksDB.login'), getString('tools.booksDB.register'), getString('tools.booksDB.username'), getString('tools.booksDB.enterAnotherUsername'), getString('tools.booksDB.password'), getString('tools.booksDB.enterPassword'), getString('tools.booksDB.repeatPassword'), getString('tools.booksDB.passwordsDontMatch'), getString('tools.booksDB.login'), getString('tools.booksDB.register'));

					function hardClean($string) {
						return strtolower(preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-]+/', '', $string));
					}

					function clean($string) {
						return preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?°<>|+*~#()\[\]{}.:,; ]+/', '', $string);
					}

					function userExists($username) {
						$client = new GuzzleHttp\Client(['base_uri' => $baseURL, 'verify' => false]);
						$response = json_decode($client->request('GET', '/api/bdb.php/account/isUsernameAvailable', [
								'query' => [
									'name' => $username,
								]
							])->getBody(), true)['content'];
						#Return result
						return $response?0:1;
					}
				
					function authenticate($username, $password) {
						$client = new GuzzleHttp\Client(['base_uri' => $baseURL, 'verify' => false]);
						$response = json_decode($client->request('GET', '/api/bdb.php/account/authenticate', [
								'query' => [
									'username' => $username,
									'password' => $password,
								]
							])->getBody(), true)['content'];
						#Return result
						return $response;
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
						$html = sprintf('
							<div class="row">
								<div class="col-auto">
									<form action="/bdb.php" method="get" enctype="multipart/form-data">
										<div class="input-group">
											<select class="form-select" id="bdbTableSelect" name="table" onchange="this.form.submit()"></select>
											<input type="text" class="form-control" id="bdbTableSelect-text" placeholder="%s" style="display: none;">
											<button type="button" class="btn btn-outline-secondary" id="bdbEditCat-button" onclick="bdbEditCat()"><i class="bi bi-pencil-fill"></i></button>
											<button type="button" class="btn btn-outline-secondary" id="bdbShareCat-button" onclick="bdbShareCat()"><i class="bi bi-share-fill"></i></i></button>
											<button type="button" class="btn btn-outline-secondary" onclick="bdbDeleteCat()"><i class="bi bi-trash3-fill"></i></button>
										</div>
									</form>
								</div>
								<div class="col-auto">
									<form>
										<div class="input-group">
											<input type="text" class="form-control" id="bdbAddCat-catName" placeholder="%s">
											<input type="button" value="+" class="btn btn-outline-secondary" onclick="bdbAddCat()">
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
											<input type="text" class="form-control" id="bdbAddBook-bookName" placeholder="%s">
											<input type="text" class="form-control" id="bdbAddBook-authorName" placeholder="%s">
											<span class="input-group-text">
												<input type="checkbox" class="form-check-input" id="bdbAddBook-read">
											</span>
											<input type="button" value="+" class="btn btn-outline-secondary" onclick="bdbAddBook()">
										</div>
									</form>
								</div>
							</div>
							<br>
							<h3>Kontoverwaltung</h3>
							<button type="button" class="btn btn-primary" onclick="bdbChangeUsername()">%s</button>
							<button type="button" class="btn btn-primary" onclick="bdbChangePassword()">%s</button>
							<button type="button" class="btn btn-primary" onclick="bdbDeleteAccount()">%s</button>
							<br><br>
						', getString('tools.booksDB.name'), getString('tools.booksDB.name'), getString('tools.booksDB.name'), getString('tools.booksDB.author'), getString('tools.booksDB.changeUsername'), getString('tools.booksDB.changePassword'), getString('tools.booksDB.deleteAccount'));

						#Get list of categories
						$client = new GuzzleHttp\Client(['base_uri' => 'https://mint.jojojux.de/', 'verify' => false]);
						$response = json_decode($client->request('GET', '/api/bdb.php/category/getAll', [
							'query' => [
								'username' => $username,
								'password' => $password,
							]
						])->getBody(), true)['content'];

						#Build head - Categorie selecter
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$tableSelect = $doc->getElementById('bdbTableSelect');
						
						if (!$selectedTable) {
							if ((bool) $response) {
								$selectedTable = $response[0];
							}
						} else {
							if (!in_array($selectedTable, $response)) {
								array_push($response, $selectedTable);
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
							$response = json_decode($client->request('GET', '/api/bdb.php/book/getAll', [
								'query' => [
									'username' => $username,
									'password' => $password,
									'categoryName' => $selectedTable
								]
							])->getBody(), true)['content'];

							#Build head - Book list
							$bodyHTML = '';
							
							$allShared = true;

							foreach ($response as $book) {
								$name = $book['bookName'];
								$author = $book['author'];
								$key = $book['key'];
								$keyActive = "";
								if ($key != '') {
									$keyActive = "active";
								}
								$hardCleanedName = hardClean($name);

								if ($book['read']) {
									$bodyHTML .= sprintf("
										<div class=\"input-group\" id=\"$hardCleanedName-group\" key=\"$key\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-name\" value=\"$name\" name=\"$name\" placeholder=\"%s\" readonly onfocus=\"bdbEditBook($(this).attr('id'))\" onfocusout=\"bdbSaveBook($(this).attr('id'))\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-author\" value=\"$author\" placeholder=\"%s\" readonly onfocus=\"bdbEditBook($(this).attr('id'))\" onfocusout=\"bdbSaveBook($(this).attr('id'))\">
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" id=\"$hardCleanedName-read\" checked onchange=\"bdbSaveBook($(this).attr('id'))\">
											</span>
											<button type=\"button\" class=\"btn btn-outline-secondary $keyActive\" id=\"$hardCleanedName-share\" onclick=\"bdbShareBook('$hardCleanedName')\"><i class=\"bi bi-share-fill\"></i></i></button>
											<button type=\"button\" class=\"btn btn-outline-secondary\" id=\"$hardCleanedName-delete\" onclick=\"bdbDeleteBook($(this).attr('id'))\"><i class=\"bi bi-trash3-fill\"></i></button>
										</div>
									", getString('tools.booksDB.name'), getString('tools.booksDB.author'));
								} else {
									$bodyHTML .= sprintf("
										<div class=\"input-group\" id=\"$hardCleanedName-group\" key=\"$key\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-name\" value=\"$name\" name=\"$name\" placeholder=\"%s\" readonly onfocus=\"bdbEditBook($(this).attr('id'))\" onfocusout=\"bdbSaveBook($(this).attr('id'))\">
											<input type=\"text\" class=\"form-control\" id=\"$hardCleanedName-author\" value=\"$author\" placeholder=\"%s\" readonly  onfocus=\"bdbEditBook($(this).attr('id'))\" onfocusout=\"bdbSaveBook($(this).attr('id'))\">
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" id=\"$hardCleanedName-read\" onchange=\"bdbSaveBook($(this).attr('id'))\">
											</span>
											<button type=\"button\" class=\"btn btn-outline-secondary $keyActive\" id=\"$hardCleanedName-share\" onclick=\"bdbShareBook('$hardCleanedName')\"><i class=\"bi bi-share-fill\"></i></i></button>
											<button type=\"button\" class=\"btn btn-outline-secondary\" id=\"$hardCleanedName-delete\" onclick=\"bdbDeleteBook($(this).attr('id'))\"><i class=\"bi bi-trash3-fill\"></i></button>
										</div>
									", getString('tools.booksDB.name'), getString('tools.booksDB.author'));
								}
								
								if ($book['key'] == '') {
									$allShared = false;
								}
								
							}
							
							if ($allShared && $bodyHTML != '') {
								$shareCatButton = $doc->getElementById('bdbShareCat-button');
								$shareCatButton->setAttribute('class', $shareCatButton->getAttribute('class') . ' active');
							}

							#Maybe show empty list note
							if ($bodyHTML == '') {
								$bodyHTML = sprintf('<span>%s</span>', getString("tools.booksDB.noBooks"));
							}
						} else {
							$bodyHTML = sprintf('<span>%s</span>', getString("tools.booksDB.noCategories"));
						}

						#Add HTML to body
						$helper = new DOMDocument();
						$helper->loadHTML(mb_convert_encoding($bodyHTML, 'HTML-ENTITIES', 'UTF-8'));

						$contentDiv = $doc->getElementById('content');
						$contentDiv->appendChild($doc->importNode($helper->documentElement, true));

						#Return HTML
						echo $doc->saveHTML();
					}
				
					function sharedContent() {
						#HTML Content snippet
						$html = '
							<div class="row">
								<div class="col-auto">
									<form>
										<div class="input-group">
											<select class="form-select" id="bdbTableSelect"></select>
											<button type="button" class="btn btn-outline-secondary" onclick="bdbShowShareLink()"><i class="bi bi-share-fill"></i></button>
										</div>
									</form>
								</div>
							</div>
							<br>
							<div id="content"></div>
							<br>
						';
						
						$username = $_GET['username'];
						$category = $_GET['category'];
						$key = $_GET['key'];

						#Build head - Categorie selecter
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$tableSelect = $doc->getElementById('bdbTableSelect');
						
						
						$fragment = $doc->createDocumentFragment();
						$fragment->appendXML("<option selected=\"selected\">$category</option>");
						$tableSelect->appendChild($fragment);

						
						#Get list of books (in category)
						$client = new GuzzleHttp\Client(['base_uri' => 'https://mint.jojojux.de/', 'verify' => false]);
						$response = json_decode($client->request('GET', '/api/bdb.php/shared', [
							'query' => [
								'username' => $username,
								'categoryName' => $category,
								'key' => $key
							]
						])->getBody(), true)['content'];

						#Build head - Book list
						$bodyHTML = '';

						foreach ($response as $book) {
							$name = $book['bookName'];
							$author = $book['author'];
							$hardCleanedName = hardClean($name);

							if ($book['read']) {
									$bodyHTML .= sprintf("
										<div class=\"input-group\">
											<input type=\"text\" class=\"form-control\" value=\"$name\" readonly>
											<input type=\"text\" class=\"form-control\" value=\"$author\" readonly>
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" checked disabled=\"disabled\">
											</span>
										</div>
									", getString('tools.booksDB.name'), getString('tools.booksDB.author'));
								} else {
									$bodyHTML .= sprintf("
										<div class=\"input-group\">
											<input type=\"text\" class=\"form-control\" value=\"$name\" readonly>
											<input type=\"text\" class=\"form-control\" value=\"$author\" readonly>
											<span class=\"input-group-text\">
												<input type=\"checkbox\" class=\"form-check-input\" disabled=\"disabled\">
											</span>
										</div>
									", getString('tools.booksDB.name'), getString('tools.booksDB.author'));
								}

							#Maybe show empty list note
							if ($bodyHTML == '') {
								$bodyHTML = sprintf('<span>%s</span>', getString("tools.booksDB.noBooks"));
							}
						}
						
						#Maybe show empty list note
						if ($bodyHTML == '') {
							$bodyHTML = sprintf('<span>%s</span>', getString("tools.booksDB.noSharedBooks"));
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
								
								if (isset($_GET['category']) && isset($_GET['username']) && isset($_GET['key'])) {
									sharedContent();
								} else {
									#Return HTML
									content();
								}
							} else {
								#Return error
								echo getString('tools.booksDB.usernamePasswordCombiDoesntExist');
							}
						} else {
							#Return error
							echo getString('tools.booksDB.usernamePasswordCombiDoesntExist');
						}
					#Check if it's a register request
					} elseif (isset($_POST['cmd']) && $_POST['cmd'] == 'register') {
						$username = $_POST['username'];
						$password = $_POST['password'];

						#Check if user already exists
						if (userExists($username)) {
							#Return error
							echo getString('tools.booksDB.usernameAlreadyExists');
						} else {
							#Register user
							$client = new GuzzleHttp\Client(['base_uri' => 'https://mint.jojojux.de/', 'verify' => false]);
							$response = $client->request('POST', '/api/bdb.php/account/register', [
								'form_params' => [
									'username' => $username,
									'password' => $password,
									]
							])->getBody();

							#Set session cookies
							$_SESSION['username'] = $username;
							$_SESSION['password'] = $password;
							$_SESSION['start_time'] = time();

							#Return response
							echo json_decode($response, true)['content'];

							#Return redirect
							echo '<br><a href="/bdb.php">Weiter</a>';
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

							if (isset($_GET['category']) && isset($_GET['username']) && isset($_GET['key'])) {
								sharedContent();
							} else {
								#Return login form
								echo str_replace('{{username}}', $_GET['username'], $loginForm);
							}
						#Check if session is running
						} elseif (isset($_SESSION['start_time'])) {
							if (isset($_GET['category']) && isset($_GET['username']) && isset($_GET['key'])) {
								sharedContent();
							} else {
								#Return HTML
								content();
							}
						} else {
							if (isset($_GET['category']) && isset($_GET['username']) && isset($_GET['key'])) {
								sharedContent();
							} else {
								#Return login form
								echo str_replace('{{username}}', $_GET['username'], $loginForm);
							}
						}
					}
				
					ob_end_flush();
				?>
				<br>
				<a href="/swagger/" langid="tools.booksDB.apiDoc">API-Dokumentation</a><br>
				<br><br>
			</div>
		</div>
	</div>

	<span id="foot">
		<!-- Cookie Banner -->
		<div id="cookieBanner" class="alert alert-dark text-center mb-0" role="alert">
			<span  langid="cookies.text">
				&#x1F36A; mint nutzt Cookies, um funktionieren. Wenn Sie keine Cookies möchten, haben Sie Pech gehabt.
			</span>
			<button type="button" class="btn btn-outline-primary btn-sm ms-3" onclick="window.open('https://www.cookiesandyou.com/')" langid="cookies.learnMore">
				Mehr Infos
			</button>
			<button type="button" class="btn btn-primary btn-sm ms-3" onclick="window.hideCookieBanner()" langid="cookies.ok">
				OK
			</button>
		</div>
		<!-- Footer -->
		<footer class="bg-dark text-light">
			© Pascal Dietrich, 2022 | <a href="/datenschutz.html" langid="footer.privacy">Datenschutz</a>
			<span id="lang"></span>
		</footer>
	</span>

	<script>
		window.addEventListener("load", function() {
			loadStatus();
			
			//Signup form validation
			bdbSignupFormValidation();
			
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
