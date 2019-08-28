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
      <td class="nav-cell-current">
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
  <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    <label for="search">Search </label>
    <input id="search" name="search" type="text" maxlength="255" value="">

    <label for="sort">Sort by </label>
    <select id="sort" name="sort">
      <option value="1" selected="selected">Date</option>
      <option value="2">Subject</option>
    </select>
    <input type="submit" value="Go">
  </form>
  <hr>
  <!-- Här ska man få resultaten. -->
  <?php // Hämta indata från söknignen.
  $search = "";
  $sort = 1;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = test_input($_POST["search"]);
    $sort = test_input($_POST["sort"]);
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function print_table($cursor) {
    $num_preview_chars = 50;

    echo "<table class=\"result-table\">";
    echo "<tr><th>Date</th><th>Subject</th><th>Preview</th>";

    while ($row = pg_fetch_assoc($cursor)) {
      echo "<tr>";                                              // Ny rad
      echo "<td>".$row["date"]."</td>";
      echo "<td>".$row["subject"]."</td>";
      $preview = mb_substr($row["text"], 0, $num_preview_chars);             // Ta ut de tio första tecknen i texten.
      if (strlen($preview) >= $num_preview_chars) {
        $preview = $preview."...";
      }
      echo "<td>".$preview."</td>";
      echo "<td class=\"button-cell\"><form method=\"post\" action=\"/view-all/view/index.php\">";  // Skapa formulär för att visa.
      echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
      echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
      echo "<input type=\"submit\" value=\"Show\"></form></td>";
      echo "<td class=\"button-cell\"><form method=\"post\" action=\"/view-all/edit/index.php\">";  // Skapa formulär för att redigera.
      echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
      echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
      echo "<input type=\"submit\" value=\"Edit\"></form></td>";
      echo "<td class=\"button-cell\"><form method=\"post\" action=\"/view-all/delete.php\">";  // Skapa formulär för att radera.
      echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
      echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
      echo "<input type=\"submit\" value=\"Delete\"></form></td>";
      echo "</tr>";                                             // Avsluta rad
    }

    echo "</table>";
  }

  function sort_string($sort) {
    if ($sort == 1) {
      return "Date DESC, Subject ASC";
    } else {
      return "Subject ASC, Date DESC";
    }
  }

  // Sök enligt användarens indata.
  // Anslut till databas.
  $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
  $conn = pg_connect($string);

  // Om användaren inte sökt efter något.
  if (empty($search)) {
    // Query till databas som hämtar alla inlägg och sorterar dem enligt datum.
    $sql = "SELECT * FROM Entries ORDER BY ".sort_string($sort).";";

    $result = pg_query($conn, $sql);

    // Varje rad i resultaten läggs till som en rad i tabellen.
    if (pg_num_rows($result) > 0) {
      print_table($result);
    } else {
      echo "No entries found.";
    }

    pg_close($conn);
  } else { // Om användaren har sökt efter något.
    // Query till databas som hämtar alla inlägg och sorterar dem enligt datum.
    $sql = "SELECT * FROM Entries WHERE (date::varchar ILIKE '%$search%' OR subject ILIKE '%$search%' OR text ILIKE '%$search%') ORDER BY ".sort_string($sort).";";

    $result = pg_query($conn, $sql);

    // Varje rad i resultaten läggs till som en rad i tabellen.
    if (pg_num_rows($result) > 0) {
      print_table($result);
    } else {
      echo "No entries found.";
    }

    pg_close($conn);
  }
  ?>
</body>
</html>
