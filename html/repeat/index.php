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
      <td class="nav-cell-current">
        <a href="/repeat/index.php">Repeat entries</a>
      </td>
    </tr>
  </table>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    <label for="date">Date </label>
    <input id="date" name="date" type="date" value="<?php echo date("Y-m-d")?>" required>
    <input type="submit" value="Go">
  </form>
  <hr>
  <!-- här ska man få resultaten -->
  <?php
  $date = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = test_input($_POST["date"]);
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function print_entries($cursor) {
    while ($row = pg_fetch_assoc($cursor)) {
      print_entry($row);
    }
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
    echo "<hr>";
  }

  if (!empty($date)) {
    // Anslut till databas.
    $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
    $conn = pg_connect($string);

    // Query till databas som hämtar alla inlägg och sorterar dem enligt datum.
    $sql = "SELECT * FROM Entries WHERE (date = date '$date' OR
    date = (date '$date' - INTERVAL '1' DAY) OR
    date = (date '$date' - INTERVAL '7' DAY) OR
    date = (date '$date' - INTERVAL '30' DAY)) ORDER BY date DESC, subject ASC;";

    $result = pg_query($conn, $sql);

    // Varje rad i resultaten läggs till som en rad i tabellen.
    if (pg_num_rows($result) > 0) {
      print_entries($result);
    } else {
      echo "No entries to repeat.";
    }

    pg_close($conn);
  }
  ?>
</body>
</html>
