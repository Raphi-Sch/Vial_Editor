<?php
session_start();
require_once 'src/functions.php';

// POST processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'clear':
            $_SESSION['vial_editor'] = null;
            $_SESSION['vial_editor']['last_action'] = 'clear';
            break;

        case 'import':
            $_SESSION['vial_editor']['last_action'] = 'import';
            upload_file_to_memory('file', dirname(__FILE__) . '/tmp');
            break;

        case 'export':
            header("Content-Disposition: attachment; filename=layout.vil");
            echo json_encode($_SESSION['vial_editor']['data']);
            exit();

        case 'swap-layout':
            $_SESSION['vial_editor']['last_action'] = 'swap-layout';
            swap_layout(intval($_POST['a']), intval($_POST['b']));
            break;
    }

    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Vial Editor - Main</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Vial editor</h1>

    <h2>Import layout <?php echo (isset($_SESSION['vial_editor']['data'])) ? "(OK)" : "(Empty)"; ?></h2>
    <form action='index.php' method='post' enctype='multipart/form-data'>
        <input type='hidden' name='action' value='import'>
        <input type='file' name='file' required>
        <input type='submit' value='Import'>
    </form>
    <br />

    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='export'>
        <input type='submit' value='Export'>
    </form>
    <br />

    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='clear'>
        <input type='submit' value='Clear'>
    </form>

    <h2>Layout explorer</h2>
    <a target="_blank" rel="noopener noreferrer" href='layout_keys.php'>Keys layout</a>
    <a target="_blank" rel="noopener noreferrer" href='layout_encoders.php'>Encoders layout</a>

    <h2>Swap layout</h2>
    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='swap-layout'>
        <input type='number' name='a' min='0' max='15' step='1' value='0'>
        <input type='number' name='b' min='0' max='15' step='1' value='0'>
        <input type='submit' value="Swap">
    </form>

    <?php
    if (isset($_SESSION['vial_editor']['last_change'])) {
        $last = $_SESSION['vial_editor']['last_change'];

        echo "<h2>Last change</h2>";
        echo "Performed action : " . $last['text'];

        // Change on A
        echo "<h4>Layout " . $last['id_A'] . " was</h4>";
        echo display_layout($last['layout']['old_A']);
        echo display_layout($last['encoder_layout']['old_A']);
        echo "<h4>Layout " . $last['id_A'] . " is now</h4>";
        echo display_layout($last['layout']['new_A']);
        echo display_layout($last['encoder_layout']['new_A']);

        // Change on B
        echo "<h4>Layout " . $last['id_B'] . " was</h4>";
        echo display_layout($last['layout']['old_B']);
        echo display_layout($last['encoder_layout']['old_B']);
        echo "<h4>Layout " . $last['id_B'] . " is now</h4>";
        echo display_layout($last['layout']['new_B']);
        echo display_layout($last['encoder_layout']['new_B']);
    }
    ?>

    <?php require_once 'src/alert.php'; ?>
</body>

</html>