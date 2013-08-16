<?php
include('app/auth/Bcrypt.php');
use Strong\Provider\Bcrypt;
ini_set( "display_errors", 0);

// inital config
$config = array(
		'status' => array(),
		'db' => array(
			'provider' => 'mysql',
			'username' => 'root#',
			'password' => '',
			'hostname' => 'localhost',
			'port' => '3306',
			'dbname' => 'test',
		),
		'admin' => array(
			'username' => '',
			'password' => '',
			'email' => ''
		),
		'email' => array(
			'shopaddress' => '',
			'supportaddress' => ''
		),
);

// database handle
$dbh = null;

session_start();

if(isset($_SESSION["config"])){
	$config = $_SESSION["config"];
}


// create image upload directory
if (!file_exists('upload')) {
	mkdir('upload', 0777, true);
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Tapeshop Setup</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/bootstrap-responsive.min.css" rel="stylesheet">
 	</head>
 	<body>
 	<h1>Tapeshop Setup</h1>
	<?php
	/*
	 * Check template cache.
	 */
	if(!in_array('cache', $config["status"])){
		if (!file_exists('templates_c')) {
			if(mkdir('templates_c', 0777, true)){
	?>
		<div class="alert alert-success">
			<strong>Template Cache:</strong> Cache created.
		</div>
	<?php
			}else{
	?>
		<div class="alert alert-error">
			<strong>Template cache:</strong> Could not create template cache directory.
			<br>Please create the directory <em>templates_c</em> in the application root and grant write permissions!
		</div>
	<?php				
			}
		}else{
			if(!is_writable('templates_c')){
				if(chmod('templates_c', 0777)){
	?>
		<div class="alert alert-success">
			<strong>Template Cache:</strong> Permissions fixed.
		</div>
	<?php
				}else{
	?>
		<div class="alert alert-error">
			<strong>Template cache:</strong> Could not set permissions for template cache directory.
			<br>Please grant write permissions on <em>templates_c</em>!
		</div>
	<?php	
				}
			}else{
				array_push($config["status"], "cache");
	?>
		<div class="alert alert-success">
			<strong>Template Cache:</strong> Ok!
		</div>
	<?php
			}
		}
	}else{
	?>
		<div class="alert alert-success">
			<strong>Template Cache:</strong> Ok!
		</div>
	<?php
	} 
	?>
	
	<?php
	/*
	 * Check upload directory.
	 */
	if(!in_array('upload', $config["status"])){
		if (!file_exists('upload')) {
			if(mkdir('upload', 0777, true)){
	?>
		<div class="alert alert-success">
			<strong>Upload directory:</strong> Cache created.
		</div>
	<?php
			}else{
	?>
		<div class="alert alert-error">
			<strong>Upload directory:</strong> Could not create upload directory.
			<br>Please create the directory <em>upload</em> in the application root and grant write permissions!
		</div>
	<?php				
			}
		}else{
			if(!is_writable('upload')){
				if(chmod('upload', 0777)){
	?>
		<div class="alert alert-success">
			<strong>Upload directory:</strong> Permissions fixed.
		</div>
	<?php
				}else{
	?>
		<div class="alert alert-error">
			<strong>Upload directory:</strong> Could not set permissions for upload directory.
			<br>Please grant write permissions on <em>upload</em>!
		</div>
	<?php	
				}
			}else{
				array_push($config["status"], "upload");
	?>
		<div class="alert alert-success">
			<strong>Upload directory:</strong> Ok!
		</div>
	<?php
			}
		}
	}else{
	?>
		<div class="alert alert-success">
			<strong>Upload directory:</strong> Ok!
		</div>
	<?php
	} 
	?>
	
	<?php 
	/*
	 * Check database connection.
	 */

	if(isset($_POST["provider"])){
		$config['db']["provider"] = $_POST["provider"];
	}
	if(isset($_POST["dbusername"])){
		$config['db']["username"] = $_POST["dbusername"];
	}
	if(isset($_POST["dbpassword"])){
		$config['db']["password"] = $_POST["dbpassword"];
	}
	if(isset($_POST["hostname"])){
		$config['db']["hostname"] = $_POST["hostname"];
	}
	if(isset($_POST["port"])){
		$config['db']["port"] = $_POST["port"];
	}
	if(isset($_POST["dbname"])){
		$config['db']["dbname"] = $_POST["dbname"];
	}
	
	try{
		$cs = $config['db']['provider'].':host='.$config['db']['hostname'].':'.$config['db']['port'].';dbname='.$config['db']['dbname'].';charset=UTF-8';
		$dbh = new pdo( $cs,
				$config['db']["username"],
				$config['db']["password"],
				array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	?>
		<div class="alert alert-success">
			<strong>Database connection:</strong> Connection sccessful.
		</div>
	<?php
		array_push($config["status"], "dbconnection");
	}catch(PDOException $ex){
	?>
		<div class="alert alert-error">
			<strong>Database connection:</strong> Connection failed.
		</div>
		<form class="well form-horizontal" method="post">
			<div class="control-group">
				<label class="control-label" for="provider">Provider</label>
				<div class="controls">
					<input type="text" name="provider" id="provider" value="<?php print $config['db']['provider']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="hostname">Hostname</label>
				<div class="controls">
					<input type="text" name="hostname" id="hostname" value="<?php print $config['db']['hostname']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="port">Port</label>
				<div class="controls">
					<input type="text" name="port" id="port" value="<?php print $config['db']['port']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbusername">Username</label>
				<div class="controls">
					<input type="text" name="dbusername" id="dbusername" value="<?php print $config['db']['username']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbpassword">Password</label>
				<div class="controls">
					<input type="password" name="dbpassword" id="dbpassword" value="<?php print $config['db']['password']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbname">Database name</label>
				<div class="controls">
					<input type="text" name="dbname" id="dbname" value="<?php print $config['db']['dbname']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn"/>
				</div>
			</div>
		</form>
	<?php 
	}
	?>
	
	<?php 
	/*
	 * Load database structure.
	 */
	
	if(in_array("dbconnection", $config["status"])){
		$sql = file_get_contents("tapeshop.sql");
		if($sql){
			try{
				$dbh->exec($sql);
	?>
		<div class="alert alert-success">
			<strong>Database data:</strong> Database structure loaded.
		</div>
	<?php
			}catch(PDOException $e){
	?>
		<div class="alert alert-error">
			<strong>Database data:</strong> Error loading the database structure! <br>
			Please check the <em>tapeshop.sql</em> file.<br>
			<?php print $e->getMessage();?>
		</div>
	<?php				
			}
		}else{
	?>
		<div class="alert alert-error">
			<strong>Database data:</strong> Error reading file <em>tapeshop.sql</em>!
		</div>
	<?php
		}
	}else{
	?>
		<div class="alert">
			<strong>Database data:</strong> Can not continue, database connection required.
		</div>
	<?php 
	}
	?>
	
	<?php 
	/*
	 * Create admin user.
	 */
	if(!in_array("user", $config["status"])){
		if(in_array("dbconnection", $config["status"])){
			if(isset($_POST["username"])){
				$config['admin']["username"] = $_POST["username"];
			}
			if(isset($_POST["password"])){
				$config['admin']["password"] = $_POST["password"];
			}
			if(isset($_POST["email"])){
				$config['admin']["email"] = $_POST["email"];
			}
			
			if($config['admin']["username"] !='' && $config['admin']["password"] != '' && $config['admin']["email"] != ''){
				$sth = $dbh->prepare('INSERT INTO users (username, password, email, admin) values (:username, :password, :email, 1)');
				$bcrypt = new Bcrypt();
				$sth->execute(array(
					":username" => $config['admin']["username"],
					":password" => $bcrypt->hash($config['admin']["password"]),
					":email" => $config['admin']["email"]
				));
				array_push($config["status"], "user");
			}else{
	?>
		<form method="post" class="well form-horizontal">
			<div class="control-group">
				<label class="control-label" for="dbname">Login name</label>
				<div class="controls">
					<input type="text" name="username" id="username" value="<?php print $config['admin']['username']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbname">Password</label>
				<div class="controls">
					<input type="password" name="password" id="password" value="<?php print $config['admin']['password']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbname">Email</label>
				<div class="controls">
					<input type="text" name="email" id="email" value="<?php print $config['admin']['email']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn"/>
				</div>
			</div>
		</form>
	<?php 
			}
		}else{
	?>
		<div class="alert">
			<strong>Admin user:</strong> Can not continue, database connection required.
		</div>
	<?php 
		}
	}else{
	?>
		<div class="alert alert-success">
			<strong>Admin user:</strong> Ok.
		</div>
	<?php		
	}
	?>
	
	<?php 
	/*
	 * Email notifications.
	 */
	if(isset($_POST['shopmail']) && $_POST['shopmail'] != ''){
		$config['email']['shopaddress'] = $_POST['shopmail'];
	}
	
	if(isset($_POST['supportmail']) && $_POST['supportmail'] != ''){
		$config['email']['supportaddress'] = $_POST['supportmail'];
	}
	
	if($config['email']['shopaddress'] != '' && $config['email']['supportaddress'] != ''){
		if(!in_array('email', $config["status"])){
			array_push($config['status'], 'email');
		}
	}
	
	if(!in_array('email', $config["status"])){
		?>
		<form method="post" class="well form-horizontal">
			<div class="control-group">
				<label class="control-label" for="shopmail">Shop email</label>
				<div class="controls">
					<input type="text" name="shopmail" id="shopmail" value="<?php print $config['email']['shopaddress']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="supportmail">Support email</label>
				<div class="controls">
					<input type="password" name="supportmail" id="supportmail" value="<?php print $config['email']['supportaddress']; ?>"/>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn"/>
				</div>
			</div>
		</form>
	<?php 
	}else{
	?>
		<div class="alert alert-success">
			<strong>Email:</strong> Ok.
		</div>
	<?php		
	}
	?>
	
	<?php 
	/*
	 * Write config.
	 */
	if(isset($_POST['writeconfig'])){
		$ok = true;
		$configstring = <<<EOT
<?php
/**
 * Database Details
 */
define( 'DB_HOSTNAME', '{$config['db']['hostname']}' );
define( 'DB_USERNAME', '{$config['db']['username']}' );
define( 'DB_PASSWORD', '{$config['db']['password']}' );
define( 'DB_NAME', '{$config['db']['dbname']}' );
define( 'DB_PROVIDER', '{$config['db']['provider']}' );

/*
 * Email
 */
define( 'SHOPADRESS', '{$config['email']['shopaddress']}');
define ('SUPPORTADRESS', '{$config['email']['supportaddress']}');
		
EOT;
		if(!$res = fopen ( 'config.php' , 'w' )){
			$ok = false;
	?>
		<div class="alert alert-error">
			<strong>Write config</strong> Could not create file <em>config.php</em>. Please create the file yourself with the following content:<br>
			<pre><?php print htmlspecialchars($configstring);?></pre>
		</div>
	<?php		
		}else{
			if(!fwrite($res, $configstring)){
				$ok = false;
	?>
		<div class="alert alert-error">
			<strong>Write config</strong> Could not write file <em>config.php</em>. Please change the file yourself to the following content:<br>
			<pre><?php print htmlspecialchars($configstring);?></pre>
		</div>
	<?php
			}
		}
		if($ok){
	?>
		<div class="alert alert-success">
			<strong>Write config</strong> successful.
		</div>
		<div class="alert">
			Please remove this file from your server. Continue to <a href=".">shop</a>.
		</div>
	<?php
		}
	}
	?>
		<form method="post" class="well">
			<button type="submit" name="writeconfig" class="btn"><i class="icon-pencil"></i> Write config</button>
		</form>
	</body>
</html>
<?php 
$_SESSION["config"] = $config;