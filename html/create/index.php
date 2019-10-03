<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Pluggdagbok</title>
  <meta charset = "UTF-8">
  <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
  <table class="nav-table">
    <tr>
      <td>
        <h1>
          <a class="dashboard-link" href="/create/cat/index.html">Pluggdagbok</a>
        </h1>
      </td>
      <td>
        <a href="/view-all/index.php">View all entries</a>
      </td>
      <td class="nav-cell-current">
        <a href="/create/index.php">Create new entry</a>
      </td>
      <td>
        <a href="/repeat/index.php">Repeat entries</a>
      </td>
    </tr>
  </table>
  <?php // Spara det inlägg som just gjordes.
  $date = $subject = $text = "";
  $added = false;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = test_input($_POST["date"]);
    $subject = test_input($_POST["subject"]);
    $text = test_input($_POST["text"]);
  }

  if (!empty($date) and !empty($subject) and !empty($text)) {
    // Anslut till databas.
    $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
    $conn = pg_connect($string);

    // Query till databas som skapar ett inlägg.
    $sql = "INSERT INTO Entries VALUES ($1, $2, $3)";

    // Förbereder ett statement utan namn som skapar ett inlägg.
    pg_prepare($conn, "", $sql);

    // Exekverar statementet med datumet, ämnet och texten som användaren skrev in.
    pg_execute($conn, "", array($date, $subject, $text));

    pg_close($conn);

    $added = true;
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  ?>
  <form method="post" action="/create/index.php">
    <table>
      <tr>
        <td>
          <label for="date">Date </label>
        </td>
        <td>
          <input id="date" name="date" type="date" value="<?php echo date("Y-m-d")?>" required>
        </td>
      </tr>
      <tr>
        <td>
          <label for="subject">Subject </label>
        </td>
        <td>
          <input id="subject" name="subject"type="text" maxlength="255" value="" required>
        </td>
      </tr>
      <tr>
        <td>
          <label for="text">Text </label>
        </td>
      </tr>
    </table>
    <textarea id="text" name="text" rows="5" cols="50" maxlength="500" required></textarea>
    <br>
    <input type="submit" value="Save">
    <?php
    if ($added == true) {
      echo "Added entry!";
    }
    ?>
  </form>
</body>
</html>
