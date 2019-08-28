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
  $date = $subject = $text = "";
  $edited = false;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = test_input($_POST["date"]);
    $subject = test_input($_POST["subject"]);
    if (array_key_exists("text", $_POST)) {
      $text = test_input($_POST["text"]);
    }
    if (array_key_exists("edited", $_POST)) {
      $edited = true;
    }
  }

  if (!empty($date) and !empty($subject)) {
    // Anslut till databas.
    $string = "host=localhost user=pluggdagbok password=NeverForget dbname=pluggdagbok";
    $conn = pg_connect($string);

    // Ta eventuellt bort det gamla inlägget och lägg till den nya versionen av det.
    if ($edited == true) {
      // Query till databas som raderar ett inlägg.
      $sql = "DELETE FROM Entries WHERE (date=$1 AND subject=$2);";

      // Förbereder ett statement utan namn som raderar ett inlägg.
      pg_prepare($conn, "", $sql);

      // Exekverar statementet med datumet och ämnet som användaren skrev in.
      pg_execute($conn, "", array($date, $subject));

      // Query till databas som skapar ett inlägg.
      $sql = "INSERT INTO Entries VALUES ($1, $2, $3)";

      // Förbereder ett statement utan namn som skapar ett inlägg.
      pg_prepare($conn, "", $sql);

      // Exekverar statementet med datumet, ämnet och texten som användaren skickade senast.
      pg_execute($conn, "", array($date, $subject, $text));
    }

    // Query till databas som hämtar inläggets data.
    $sql = "SELECT * FROM Entries WHERE (date::varchar = '$date' AND subject = '$subject');";

    $result = pg_query($conn, $sql);

    $row = pg_fetch_assoc($result);
    $text = $row["text"];

    create_buttons($row);

    pg_close($conn);
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  // Skapa knapparna
  function create_buttons($row) {
    echo "<table><tbody><tr>";
    echo "<td><form method=\"post\" action=\"/view-all/view/index.php\">";  // Skapa formulär för att redigera.
    echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
    echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
    echo "<input type=\"submit\" value=\"Show\"></form></td>";

    echo "<td><form method=\"post\" action=\"/view-all/delete.php\">";  // Skapa formulär för att radera.
    echo "<input name=\"date\" type=\"hidden\" value=\"".$row["date"]."\">";
    echo "<input name=\"subject\" type=\"hidden\" value=\"".$row["subject"]."\">";
    echo "<input type=\"submit\" value=\"Delete\"></form></td></tr></tbody></table>";
  }
  ?>
  <form method="post" action="/view-all/edit/index.php">
    <table>
      <tr>
        <td>Date:</td><td><?php echo $date;?><input name="date" type="hidden" value="<?php echo $date;?>"></td>
      </tr>
      <tr>
        <td>Subject:</td><td><?php echo $subject;?><input name="subject" type="hidden" value="<?php echo $subject;?>"></td>
      </tr>
      <tr>
        <td>
          <label for="text">Text </label>
        </td>
      </tr>
    </table>
    <textarea id="text" name="text" rows="5" cols="50" required><?php echo $text;?></textarea>
    <br>
    <input name="edited" type="submit" value="Save">
    <?php
    if ($edited == true) {
      echo "Edited entry!";
    }
    ?>
  </form>
</body>
</html>
