<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Connection...</title>
  <link rel="stylesheet" href="css/styleconnection.css">

</head>
<body>

<!DOCTYPE html>
<html>
<head>
	<title>Slide Navbar</title>
	<link rel="stylesheet" type="text/css" href="slide navbar style.css">
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
	<div class="main">  	
		<input type="checkbox" id="chk" aria-hidden="true">

			<div class="signup">

				<form action="/loginA" method="post">
					<label for="chk" aria-hidden="true">Login Admin</label>
					<input type="text" name="Nom" placeholder="User name" required="">
					<input type="password" name="mdp" placeholder="Password" required="">
					<button>Login</button>
					<?php if (isset($mess)) { ?>
						<strong style="color:rgb(251 ,238 ,202);"><?php echo $mess ;?></strong>
					<?php } ?>
				</form>
			</div>
	</div>
	
</body>
</html>
  
</body>
</html>
