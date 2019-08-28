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
          <a class="dashboard-link" href="/index.php">Pluggdagbok</a>
        </h1>
      </td>
      <td>
        <a href="/view-all/index.php">View all entries</a>
      </td>
      <td>
        <a href="/create/index.php">Create new entry</a>
      </td>
      <td>
        <a href="/repeat/index.php">Repeat entries</a>
      </td>
    </tr>
  </table>
  <?php
  $date = $subject = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = test_input($_POST["date"]);
    $subject = test_input($_POST["subject"]);
  }

  // Anslut till databas.
  $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
  $conn = pg_connect($string);

  // Query till databas som raderar ett inlägg.
  $sql = "DELETE FROM Entries WHERE (date=$1 AND subject=$2);";

  // Förbereder ett statement utan namn som raderar ett inlägg.
  pg_prepare($conn, "", $sql);

  // Exekverar statementet med datumet och ämnet som användaren skrev in.
  pg_execute($conn, "", array($date, $subject));

  echo "Entry deleted.";

  pg_close($conn);

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  ?>
</body>
</html>
