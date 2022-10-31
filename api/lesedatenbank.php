<?php
#Header
header('Content-Type: text/html; charset=utf-8');

#Set MySQL login creds
$GLOBALS['mySQLUsername'] = '';
$GLOBALS['mySQLPassword'] = '';

#Configure session
session_name('PHPSESSID-Ldb');
session_start();
$session_timeout = 900;

function authenticate($username, $password) {
	#Initialize DB
	$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
	
	#Get password hash for username
	$sql = "SELECT * FROM data WHERE username='$username'";
	$passwordHash = $pdo->query($sql)->fetchAll()[0]['passwordHash'];

	#Verify password
	return password_verify($password, $passwordHash);
}

function hardClean($string) {
	return strtolower(preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-]/gm+/', '', $string));
}
function clean($string) {
	return preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?\^°´<>|+*~#()\[\]{}.:,; ]+/', '', $string);
}

function authenticated($username) {
	#Initialize DB
	$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
	
	#Check if cmd is given
	if (isset($_POST['cmd']) || isset($_GET['cmd'])) {
		#Get cmd from GET or POST parameter
		if (isset($_POST['cmd'])) {
			$cmd = $_POST['cmd'];
		} elseif (isset($_GET['cmd'])) {
			$cmd = $_GET['cmd'];
		}
		
		#Check if cmd is allowed
		if ($cmd == 'addCategory') {
			#Get parameters
			$name = $username . '--' . clean($_POST['categoryName']);
			
			#Build & execute sql statement
			$statement = "CREATE TABLE IF NOT EXISTS `$name`(
				name VARCHAR(50),
				author VARCHAR(50),
				`read` TINYINT(0)
				);";
			$pdo->exec($statement);
			
			#Return result
			$result = array(
				'content' => 'Die Kategorie wurde hinzugefügt.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'deleteCategory') {
			#Get parameters
			$name = $username . '--' . clean($_POST['categoryName']);
			
			$statement = "DROP TABLE`$name`;";
			$pdo->exec($statement);
			
			#Return result
			$result = array(
				'content' => 'Die Kategorie wurde gelöscht.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'renameCategory') {
			#Get parameters
			$oldName = $username . '--' . clean($_POST['oldCategoryName']);
			$newName = $username . '--' . clean($_POST['newCategoryName']);
			
			#Build & execute sql statement
			$statement = "RENAME TABLE `$oldName` TO `$newName`";
			$pdo->exec($statement);
			
			#Return result
			$result = array(
				'content' => 'Die Kategorie wurde umbenannt.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'getCategories') {
			#Build & execute sql statement
			$sql = "SHOW TABLES LIKE '$username--%';";
			$data = $pdo->query($sql)->fetchAll();
			
			#Generate list of categories
			$categories = [];
			foreach ($data as $category) {
				array_push($categories, str_replace($username . '--', '', $category[0]));
			}
			
			#Return result
			$result = array(
				'content' => $categories
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'addBook') {
			#Get parameters
			$category = $username . '--' . $_POST['categoryName'];
			$name = $_POST['bookName'];
			$author = $_POST['author'];
			$read = (int) ($_POST['read'] == 'true' ? true : false);
			
			#Build & execute sql statement
			$sql = "INSERT INTO `$category` (name, author, `read`) VALUES ('$name','$author',$read);";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Das Buch wurde hinzugefügt.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'deleteBook') {
			#Get parameters
			$category = $username . '--' . $_POST['categoryName'];
			$name = $_POST['bookName'];
			
			$sql = "DELETE FROM `$category` where name='$name'";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Das Buch wurde gelöscht.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'editBook') {
			#Get parameters
			$category = $username . '--' . $_POST['categoryName'];
			$oldName = $_POST['oldBookName'];
			$newName = $_POST['newBookName'];
			$author = $_POST['author'];
			$read = (int) ($_POST['read'] == 'true' ? true : false);
			
			#Build & execute sql statement
			$sql = "UPDATE `$category` SET name='$newName', author='$author', `read`='$read' WHERE name='$oldName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Der Bucheintrag wurde bearbeitet.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'getBooks') {
			if (isset($_GET['categoryName'])) {
				#Get parameters
				$category = $username . '--' . $_GET['categoryName'];

				#Build & execute sql statement
				$sql = "SELECT * FROM `$category` ORDER BY `read`, name;";
				$data = $pdo->query($sql)->fetchAll();

				$books = [];

				foreach ($data as $book) {
					array_push($books, ['bookName' => $book[0], 'author' => $book[1], 'read' => (bool) $book[2]]);
				}

				#Return result
				$result = array(
					'content' => $books
				);
			} else {
				#Return result
				$result = array(
					'content' => []
				);
			}
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'deleteAccount') {
			#Destroy session
			session_destroy();
			
			#Build & execute sql statement
			$sql = "SHOW TABLES LIKE '$username--%';";
			$data = $pdo->query($sql)->fetchAll();
			
			$categories = '';
			foreach ($data as $category) {
				if ($categories == '') {
					$categories = "`$category[0]`";
				} else {
					$categories = $categories . ", `$category[0]`";
				}
			}
			
			#Build & execute sql statement (remove categories)
			$statement = "DROP TABLE $categories;";
			$pdo->prepare($sql)->execute();
					
			##Build & execute sql statement (remove username & passwordhash)
			$sql = "DELETE FROM data where username='$username'";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Ihr Konto wurde gelöscht.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'changePassword') {
			#Destroy session
			session_destroy();
			
			#Get parameters
			$newPassword = $_POST['newPassword'];
			$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
			
			#Build & execute sql statement
			$sql = "UPDATE `data` SET passwordHash='$passwordHash' WHERE username='$username';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Ihr Passwort wurde geändert. Sie müssen sich nun erneut anmelden.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'changeUsername') {
			#Destroy session
			session_destroy();
			
			#Get new username
			$newUsername = $_POST['newUsername'];
			
			#Get categories
			#Build & execute sql statement
			$sql = "SHOW TABLES LIKE '$username--%';";
			$data = $pdo->query($sql)->fetchAll();
			
			#Rename categories
			foreach ($data as $category) {
				$newCatName = str_replace($username, $newUsername, $category[0]);
				
				#Build & execute sql statement
				$statement = "RENAME TABLE `$category[0]` TO `$newCatName`";
				$pdo->exec($statement);
			}
			
			#Change username in data
			#Build & execute sql statement
			$sql = "UPDATE `data` SET username='$newUsername' WHERE username='$username';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => 'Ihr Benutzername wurde geändert. Sie müssen sich nun erneut anmelden.'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == 'register') {
			session_destroy();
			
			#Return error
			$result = array(
				'content' => "Der Benutzername \"$username\" ist bereits registriert."
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			http_response_code(400);
		} elseif ($cmd == 'isUsernameAvailable') {
			#Initialize DB
			$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
			
			$name = $_GET['name'];

			#Build & execute sql statement
			$sql = "SELECT * FROM data;";
			$data = $pdo->query($sql)->fetchAll();

			#Check if username is available
			$exists = true;
			foreach ($data as $user) {
				if ($user[0] == $name) {
					$exists = false;
				}
			}

			#Build result
			$result = array(
				'content' => $exists
			);

			#Return result
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} else if ($cmd == 'getUsernames') {
			#Initialize DB
			$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);

			#Build & execute sql statement
			$sql = "SELECT * FROM data;";
			$data = $pdo->query($sql)->fetchAll();

			#Build list
			$usernames = array();
			foreach ($data as $uname) {
				array_push($usernames, $uname[0]);
			}

			#Build result
			$result = array(
				'content' => $usernames
			);

			#Return result
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} else {
			session_destroy();
			
			#Return error
			$result = array(
				'content' => 'Der Befehl ist ungültig'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			http_response_code(400);
		}
	} else {
		session_destroy();
		
		#Return error
		$result = array(
				'content' => 'Es wurde kein Befehl angegeben'
			);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		http_response_code(400);
	}
}

#If session
if(isset($_SESSION['start_time']) && (time() - $_SESSION['start_time']) < $session_timeout) {
	#Start authenticated part
	authenticated($_SESSION['username']);
} else {
	#If credentials are given
	if ((isset($_POST['username']) && isset($_POST['password'])) || (isset($_GET['username']) && isset($_GET['password']))) {
		#Get login credentials from GET or POST
		if (isset($_POST['username']) && isset($_POST['password'])) {
			$username = $_POST['username'];
			$password = $_POST['password'];
		} elseif (isset($_GET['username']) && isset($_GET['password'])) {
			$username = $_GET['username'];
			$password = $_GET['password'];
		}
		
		#Get cmd from GET or POST parameter
		if (isset($_POST['cmd'])) {
			$cmd = $_POST['cmd'];
		} elseif (isset($_GET['cmd'])) {
			$cmd = $_GET['cmd'];
		}
		
		#If authenticating is successfull
		if (authenticate($username, $password)) {
			if ($cmd == 'authenticate') {
				#Return result
				$result = array(
					'content' => true
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else {
				#Start authenticated part
				authenticated($username);
			}
		} else {
			if ($cmd == 'register') {
				#Initialize DB
				$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
				
				#Get password hash
				$passwordHash = password_hash($password, PASSWORD_DEFAULT);
				
				#Create data table if not exits
				$sql = "CREATE TABLE IF NOT EXISTS data(
					username VARCHAR(100),
					passwordHash VARCHAR(100)
					);";
				$pdo->exec($sql);

				#Register user
				$sql = "INSERT INTO data (username, passwordHash) VALUES ('$username', '$passwordHash')";
				$pdo->prepare($sql)->execute();
				
				#Return result
				$result = array(
					'content' => 'Sie wurden erfolgreich registriert.'
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else {
				session_destroy();
				
				#Return error
				$result = array(
					'content' => 'Ihre Zugangsdaten sind ungültig'
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
				http_response_code(400);
			}
		}
	#If no login credentials are given
	} else {
		if (isset($_GET['cmd'])) {
			#Get cmd from GET parameter
			$cmd = $_GET['cmd'];

			if ($cmd == 'isUsernameAvailable') {
				#Initialize DB
				$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);

				#Get username
				$name = $_GET['name'];

				#Build & execute sql statement
				$sql = "SELECT * FROM data;";
				$data = $pdo->query($sql)->fetchAll();

				#Check if username is available
				$exists = true;
				foreach ($data as $user) {
					if ($user[0] == $name) {
						$exists = false;
					}
				}

				#Build result
				$result = array(
					'content' => $exists
				);

				#Return result
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else if ($cmd == 'getUsernames') {
				#Initialize DB
				$pdo = new PDO('mysql:host=localhost;dbname=Lesedatenbank;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);

				#Build & execute sql statement
				$sql = "SELECT * FROM data;";
				$data = $pdo->query($sql)->fetchAll();

				#Build list
				$usernames = array();
				foreach ($data as $username) {
					array_push($usernames, $username[0]);
				}

				#Build result
				$result = array(
					'content' => $usernames
				);

				#Return result
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else {
				session_destroy();

				#Return error
				$result = array(
					'content' => 'Es wurde keine Sitzung / Es wurden keine Zugangsdaten angegeben'
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
				http_response_code(400);
			}
		} else {
			session_destroy();

			#Return error
			$result = array(
				'content' => 'Es wurde keine Sitzung / Es wurden keine Zugangsdaten angegeben'
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			http_response_code(400);
		}

	}
}
?>
