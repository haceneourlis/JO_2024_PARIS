<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Input Form</title>
</head>

<body>
    <h2>Enter a Date</h2>
    <form method="POST">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date_naissance">
        <button type="submit">Submit</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["date_naissance"])) {

            $date_naissance = $_POST["date_naissance"];
            $date_naissance = preg_replace("/^([0-9]+)-([0-9]+)-([0-9]+)$/", "$3/$2/$1", $date_naissance);


            echo "<p>Submitted Date: $date_naissance</p>";
        } else {
            echo "<p>No date submitted.</p>";
        }
    }
    ?>
</body>

</html>