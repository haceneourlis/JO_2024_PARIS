<?php
session_start();

if (empty($_SESSION['ID_MEMBRE_CONNECTE'])) {
  echo "you are not allowed to enter this page ";
  die;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Navigation</title>
</head>
<style>
  /* this styling was written by CHATGPT-3.5 */
  .container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  .box {
    width: 150px;
    height: 150px;
    margin: 10px;
    background-color: #3498db;
    /* Blue color */
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .box a {
    text-decoration: none;
    color: #fff;
    /* White color */
    font-size: 18px;
    font-weight: bold;
  }

  .box:hover {
    background-color: #2980b9;
    /* Darker blue color on hover */
  }

  /* Style for specific boxes */
  .box:nth-child(2) {
    background-color: #2ecc71;
    /* Green color */
  }

  .box:nth-child(2):hover {
    background-color: #27ae60;
    /* Darker green color on hover */
  }

  .box:nth-child(3) {
    background-color: #e74c3c;
    /* Red color */
  }

  .box:nth-child(3):hover {
    background-color: #c0392b;
    /* Darker red color on hover */
  }

  .box:nth-child(4) {
    background-color: #f39c12;
    /* Orange color */
  }

  .box:nth-child(4):hover {
    background-color: #d35400;
    /* Darker orange color on hover */
  }
</style>

<body>
  <div class="container">
    <div class="box"><a href="athletes.administration.php">Athletes</a></div>
    <div class="box"><a href="arbitres.html">Arbitres</a></div>
    <div class="box"><a href="compets.administration.php">Competition</a></div>
    <div class="box">
      <a href="categories.html">Catégories de Competitions</a>
    </div>
  </div>
</body>

</html>