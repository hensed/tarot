<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>French Tarot</title>

<!-- Bootstrap -->
<link href="pt.blueprint.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Top Nav bar -->
<div class="navbar navbar-inverse navbar-static-top" role="navigation">
  <div class="navbar-logo"></div>
  <a href="index.php">
  <div class="navbar-brand">PALAN<span style="color:red;">TAROT </span><small>v2.0</small></div>
  </a> </div>
<?php if ($_GET["rs"] == 2) {?>
<div class="alert alert-success text-center" role="alert">Excellent!! Your tarot score has been saved.</div>
<?php }?>
<!-- Top Nav bar -->
<div class="container text-center">
  <p>
  <h3><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>
    Welcome to the French Tarot site, brought to you by RQE</h3>
  </p>
  <p> If you are here to report a score, <b>please make sure new players have been added to the game's database first</b></p>
  <a href="score.php">
  <button style="height:60px" type="button" class="btn btn-success btn-lg text-center">Enter Score</button>
  </a><br><br>	
  <a href="add_player.php">
  <button style="height:50px" type="button" class="btn btn-primary btn-md text-center">Add a new Player</button><br><br>	
  </a> <a href="charts.php">
  <button style="height:50px" type="button" class="btn btn-primary btn-md text-center">View Tarot score Charts</button>
  </a></div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="pt.blueprint.js"></script>
</body>
</html>