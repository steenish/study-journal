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

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function print_entry($row) {
    echo "<table><tbody><tr>";
    echo "<td><form method=\"post\" action=\"/view-all/edit/index.php\">";  // Skapa formulär för att redigera.
    echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
    echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
    echo "<input type=\"submit\" value=\"Edit\"></form></td>";

    echo "<td><form method=\"post\" action=\"/view-all/delete.php\">";  // Skapa formulär för att radera.
    echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
    echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
    echo "<input type=\"submit\" value=\"Delete\"></form></td></tr></tbody></table>";

    echo "<table>";
    echo "<tr><td><b>Date: </b></td><td>".$row["date"]."</td></tr>";
    echo "<tr><td><b>Subject: </b></td><td>".$row["subject"]."</td></tr>";
    echo "<tr><td><b>Text: </b></td></tr></table>";
    echo $row["text"];
  }

  // Anslut till databas.
  $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
  $conn = pg_connect($string);

  // Query till databas som hämtar inlägget.
  $sql = "SELECT * FROM Entries WHERE (date::varchar = '$date' AND subject = '$subject');";

  $result = pg_query($conn, $sql);

  if (pg_num_rows($result) > 0) {
    print_entry(pg_fetch_assoc($result));
  } else {
    echo "Entry not found.";
  }

  pg_close($conn);
  ?>
</body>
</html>
