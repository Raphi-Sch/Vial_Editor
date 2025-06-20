<?php
session_start();
require_once 'src/functions.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Vial Editor - Keys layout explorer</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Vial editor - Keys layout explorer</h1>

    <?php
    if (isset($_SESSION['vial_editor']['data'])) {
        $i = 0;
        foreach ($_SESSION['vial_editor']['data']['layout'] as $layer) {
            echo "<h4>Layer $i</h4>";
            $j = 0;
            foreach ($layer as $element) {
                $HTML_th = "";
                $HTML_td = "";
                foreach ($element as $key => $value) {
                    $HTML_th .= "<th>$key</th>";
                    $HTML_td .= "<td>$value</td>";
                }
                echo "<table><tr>$HTML_th</tr><tr>$HTML_td</tr></table><br/>";
            }
            $i++;
        }
    } else {
        echo "No data loaded.";
    }

    ?>

    <?php require_once 'src/alert.php'; ?>

    <script>
        let blurred = false;
        window.onblur = function() {
            blurred = true;
        };
        window.onfocus = function() {
            blurred && (location.reload());
        };
    </script>
</body>

</html>