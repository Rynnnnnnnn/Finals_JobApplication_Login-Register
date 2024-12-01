<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
	<style>
		body {
			font-family: "Arial";
		}
		input {
			font-size: 1.5em;
			height: 50px;
			width: 200px;
		}
		table, th, td {
			border:1px solid black;
		}
	</style>
</head>
<body>
	<h1>Register here!</h1>
	<?php if (isset($_SESSION['message'])) { ?>
		<h1 style="color: red;"><?php echo $_SESSION['message']; ?></h1>
	<?php } unset($_SESSION['message']); ?>
	
	<form action="core/handleForms.php" method="POST">
		<p>
			<label for="first_name">First Name</label>
			<input type="text" name="first_name" required>
		</p>
		<p>
			<label for="last_name">Last Name</label>
			<input type="text" name="last_name" required>
		</p>
		<p>
			<label for="dob">Date of Birth</label>
			<input type="date" name="dob" required>
		</p>
		<p>
			<label for="username">Username</label>
			<input type="text" name="username" required>
		</p>
		<p>
			<label for="password">Password</label>
			<input type="password" name="password" required>
		</p>
		<p>
			<input type="submit" name="registerUserBtn" value="Register">
		</p>
	</form>
</body>
</html>
