<?php
#Header
header('Content-Type: text/html; charset=utf-8');

#Set MySQL login creds
$GLOBALS['mySQLUsername'] = '';
$GLOBALS['mySQLPassword'] = '';

#Forbidden usernames
$forbiddenUsernames = array("data");

#Configure session
session_name('PHPSESSID-booksDB');
session_start();
$session_timeout = 86400;

include '../lang.php';

function authenticate($username, $password) {
	#Initialize DB
	$pdo = new PDO('mysql:host=localhost;dbname=booksDB;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
	
	#Get password hash for username
	$sql = "SELECT * FROM data WHERE username='$username'";
	$passwordHash = $pdo->query($sql)->fetchAll()[0]['passwordHash'];

	#Verify password
	return password_verify($password, $passwordHash);
}

function hardClean($string) {
	return strtolower(preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-]+/', '', $string));
}
function clean($string) {
	return preg_replace('/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?°<>|+*~#()\[\]{}.:,; ]+/', '', $string);
}

function randomString($length) {
	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function authenticated($username) {
	#Initialize DB
	$pdo = new PDO('mysql:host=localhost;dbname=booksDB;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
	
	#Check if cmd is given
	if (explode('?', explode(basename(__FILE__), $_SERVER['REQUEST_URI'])[1])[0] != null) {
		#Get cmd
		$cmd = explode('?', explode(basename(__FILE__), $_SERVER['REQUEST_URI'])[1])[0];
		
		#Check if cmd is allowed
		if ($cmd == '/category/rename') {
			#Get parameters
			$oldCategoryName = clean($_POST['oldCategoryName']);
			$newCategoryName = clean($_POST['newCategoryName']);
			
			#Build & execute sql statement
			$statement = "UPDATE `$username` SET category=`$oldCategoryName` WHERE category='$newCategoryName'";
			$pdo->exec($statement);
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.categoryHasBeenRenamed')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/category/delete') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			
			$statement = "DELETE FROM `$username` where category='$categoryName';";
			$pdo->exec($statement);
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.categoryHasBeenDeleted')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/category/getAll') {
			#Build & execute sql statement
			$sql = "SELECT category FROM `$username` GROUP BY category ORDER BY category;";
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
		} elseif ($cmd == '/category/startSharing') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			
			$key = '';
			
			#Build & execute sql statement
			$sql = "SELECT * FROM `$username` WHERE category='$categoryName' ORDER BY `read`, name;";
			$data = $pdo->query($sql)->fetchAll();
			
			$books = [];
			foreach ($data as $book) {
				if ($book[4] != '') {
					$key = $book[4];
				}
			}
			
			if ($key == '') {
				$key = randomString(10);
			}
			
			#Build & execute sql statement
			$sql = "UPDATE `$username` SET `key`='$key' WHERE category='$categoryName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' =>  sprintf(getString('api.booksDB.startSharingSuccessfull'), $categoryName, $username, $key),
				'key' => $key
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/category/stopSharing') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			
			#Build & execute sql statement
			$sql = "UPDATE `$username` SET `key`='' WHERE category='$categoryName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' =>  getString('api.booksDB.stopSharingSuccessfull')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/book/add') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			$name = $_POST['bookName'];
			$author = $_POST['author'];
			$read = (int) ($_POST['read'] == 'true' ? true : false);
			
			#Build & execute sql statement
			$sql = "INSERT INTO `$username` (category, name, author, `read`, `key`) VALUES ('$categoryName','$name','$author',$read, '');";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.bookHasBeenAdded')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/book/edit') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			$oldName = $_POST['oldBookName'];
			$newName = $_POST['newBookName'];
			$author = $_POST['author'];
			$read = (int) ($_POST['read'] == 'true' ? true : false);
			
			#Build & execute sql statement
			$sql = "UPDATE `$username` SET name='$newName', author='$author', `read`='$read' WHERE category='$categoryName' AND name='$oldName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.bookHasBeenEdited')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/book/delete') {
			#Get parameters
			$categoryName = clean($_POST['categoryName']);
			$bookName = $_POST['bookName'];
			
			$sql = "DELETE FROM `$username` WHERE category='$categoryName' AND name='$bookName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.bookHasBeenDeleted')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/book/getAll') {
			if (isset($_GET['categoryName'])) {
				#Get parameters
				$categoryName = $_GET['categoryName'];

				#Build & execute sql statement
				$sql = "SELECT * FROM `$username` WHERE category='$categoryName' ORDER BY `read`, name;";
				$data = $pdo->query($sql)->fetchAll();

				$books = [];

				foreach ($data as $book) {
					array_push($books, ['bookName' => $book[1], 'author' => $book[2], 'read' => (bool) $book[3], 'key' => $book[4]]);
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
		} elseif ($cmd == '/book/startSharing') {
			#Get parameters
			$categoryName = $_POST['categoryName'];
			$bookName = $_POST['bookName'];
			
			$key = '';
			
			#Build & execute sql statement
			$sql = "SELECT * FROM `$username` WHERE category='$categoryName' ORDER BY `read`, name;";
			$data = $pdo->query($sql)->fetchAll();
			
			$books = [];
			foreach ($data as $book) {
				if ($book[4] != '') {
					$key = $book[4];
				}
			}
			
			if ($key == '') {
				$key = randomString(10);
			}
			
			#Build & execute sql statement
			$sql = "UPDATE `$username` SET `key`='$key' WHERE category='$categoryName' AND name='$bookName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' =>  sprintf(getString('api.booksDB.startSharingSuccessfull'), $categoryName, $username, $key),
				'key' => $key
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/book/stopSharing') {
			#Get parameters
			$categoryName = $_POST['categoryName'];
			$bookName = $_POST['bookName'];
			
			#Build & execute sql statement
			$sql = "UPDATE `$username` SET `key`='' WHERE category='$categoryName' AND name='$bookName';";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.stopSharingSuccessfull')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/account/delete') {
			#Destroy session
			session_destroy();
			
			#Build & execute sql statement (remove categories)
			$statement = "DROP TABLE $username;";
			$pdo->prepare($sql)->execute();
					
			##Build & execute sql statement (remove username & passwordhash)
			$sql = "DELETE FROM data where username='$username'";
			$pdo->prepare($sql)->execute();
			
			#Return result
			$result = array(
				'content' => getString('api.booksDB.accountHasBeenDeleted')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/account/changePassword') {
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
				'content' => getString('api.booksDB.passwordHasBeenChanged')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/account/register') {
			session_destroy();
			
			#Return error
			$result = array(
				'content' => sprintf(getString('api.booksDB.usernameIsAlreadyTaken'), $username)
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/account/isUsernameAvailable') {
			#Initialize DB
			$pdo = new PDO('mysql:host=localhost;dbname=booksDB;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
			
			$name = $_GET['name'];

			#Build & execute sql statement
			$sql = "SELECT * FROM data;";
			$data = $pdo->query($sql)->fetchAll();

			#Check if username is available
			$available = true;
			foreach ($data as $user) {
				if ($user[0] == $name) {
					$available = false;
				}
			}
			
			#Check if username is forbidden
			if (in_array($name, $forbiddenUsernames)) {
				$available = false;
			}

			#Build result
			$result = array(
				'content' => $available
			);

			#Return result
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} elseif ($cmd == '/shared') {
			#Get parameters
			$username = $_GET['username'];
			$categoryName = clean($_GET['category']);
			$key = $_GET['key'];
			
			$books = [];
			if ($key != '') {
				#Build & execute sql statement
				$sql = "SELECT * FROM `$username` WHERE category='$categoryName' AND `key`='$key' ORDER BY `read`, name;";
				$pdo->prepare($sql)->execute();

				foreach ($data as $book) {
					array_push($books, ['bookName' => $book[1], 'author' => $book[2], 'read' => (bool) $book[3], 'key' => $book[4]]);
				}
			}

			#Return result
			$result = array(
				'content' => $books
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} else {
			session_destroy();
			
			#Return error
			$result = array(
				'content' => getString('api.booksDB.cmdIsInvalid')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			http_response_code(400);
		}
	} else {
		session_destroy();
		
		#Return error
		$result = array(
				'content' => getString('api.booksDB.noCmdWasGiven')
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
	if ((isset($_POST['username']) && isset($_POST['password'])) || isset($_GET['username']) && isset($_GET['password'])) {
		#Get login credentials from GET or POST
		if (isset($_POST['username']) && isset($_POST['password'])) {
			$username = hardClean($_POST['username']);
			$password = $_POST['password'];
		} elseif (isset($_GET['username']) && isset($_GET['password'])) {
			$username = hardClean($_GET['username']);
			$password = $_GET['password'];
		}
		
		#Get cmd
		$cmd = explode('?', explode(basename(__FILE__), $_SERVER['REQUEST_URI'])[1])[0];
		
		#If authenticating is successfull
		if (authenticate($username, $password)) {
			if ($cmd == '/account/authenticate') {
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
			#Initialize DB
			$pdo = new PDO('mysql:host=localhost;dbname=booksDB;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
			if ($cmd == '/account/register') {
				#Check if username is forbidden
				if (!in_array($name, $forbiddenUsernames)) {
					#Get password hash
					$passwordHash = password_hash($password, PASSWORD_ARGON2ID);

					#Create data table if not exits
					$sql = "CREATE TABLE IF NOT EXISTS data(
						username VARCHAR(100),
						passwordHash VARCHAR(100)
						);";
					$pdo->exec($sql);

					#Register user
					$sql = "INSERT INTO data (username, passwordHash) VALUES ('$username', '$passwordHash')";
					$pdo->prepare($sql)->execute();

					#Create user's data table if not exits
					$sql = "CREATE TABLE IF NOT EXISTS $username(
						category TEXT,
						name TEXT,
						author TEXT,
						`read` TINYINT
						);";
					$pdo->exec($sql);

					#Return result
					$result = array(
						'content' => getString('api.booksDB.youHaveBeenSuccessfullyRegistered')
					);
				} elseif ($cmd == '/shared') {
					#Get parameters
					$username = $_GET['username'];
					$categoryName = clean($_GET['category']);
					$key = $_GET['key'];

					$books = [];
					if ($key != '') {
						#Build & execute sql statement
						$sql = "SELECT * FROM `$username` WHERE category='$categoryName' AND `key`='$key' ORDER BY `read`, name;";
						$pdo->prepare($sql)->execute();

						foreach ($data as $book) {
							array_push($books, ['bookName' => $book[1], 'author' => $book[2], 'read' => (bool) $book[3], 'key' => $book[4]]);
						}
					}

					#Return result
					$result = array(
						'content' => $books
					);
					echo json_encode($result, JSON_UNESCAPED_UNICODE);
				} else {
					#Return result
					$result = array(
						'content' => getString('api.booksDB.usernameIsAlreadyTaken')
					);
				}
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else {
				session_destroy();
				
				#Return error
				$result = array(
					'content' => getString('api.booksDB.loginCredsAreInvalid')
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			}
		}
	#If no login credentials are given
	} else {
		if (explode('?', explode(basename(__FILE__), $_SERVER['REQUEST_URI'])[1])[0] != null) {
			#Initialize DB
			$pdo = new PDO('mysql:host=localhost;dbname=booksDB;charset=utf8', $GLOBALS['mySQLUsername'], $GLOBALS['mySQLPassword']);
			
			#Get cmd
			$cmd = explode('?', explode(basename(__FILE__), $_SERVER['REQUEST_URI'])[1])[0];
			
			if ($cmd == '/account/isUsernameAvailable') {
				#Get username
				$name = hardClean($_GET['name']);

				#Build & execute sql statement
				$sql = "SELECT * FROM data;";
				$data = $pdo->query($sql)->fetchAll();

				#Check if username is available
				$available = true;
				foreach ($data as $user) {
					if ($user[0] == $name) {
						$available = false;
					}
				}
				
				#Check if username is forbidden
				if (in_array($name, $forbiddenUsernames)) {
					$available = false;
				}

				#Build result
				$result = array(
					'content' => $available
				);

				#Return result
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} elseif ($cmd == '/shared') {
				#Get parameters
				$username = $_GET['username'];
				$categoryName = clean($_GET['categoryName']);
				$key = $_GET['key'];

				#Build & execute sql statement
				$sql = "SELECT * FROM `$username` WHERE category='$categoryName' AND `key`='$key' ORDER BY `read`, name;";
				$data = $pdo->query($sql)->fetchAll();

				$books = [];

				foreach ($data as $book) {
					array_push($books, ['bookName' => $book[1], 'author' => $book[2], 'read' => (bool) $book[3], 'key' => $book[4]]);
				}

				#Return result
				$result = array(
					'content' => $books
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
			} else {
				session_destroy();

				#Return error
				$result = array(
					'content' => getString('api.booksDB.noCredsGiven')
				);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);
				http_response_code(400);
			}
		} else {
			session_destroy();

			#Return error
			$result = array(
				'content' => getString('api.booksDB.noCredsGiven')
			);
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			http_response_code(400);
		}

	}
}
?>
