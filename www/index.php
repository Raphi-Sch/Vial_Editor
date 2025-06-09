<?php
session_start();
require_once 'src/functions.php';

// POST processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'clear') {
        $_SESSION['vial_editor'] = null;
        $_SESSION['vial_editor']['last_action'] = 'clear';
        header('Location: index.php');
        exit();
    }

    if ($_POST['action'] == 'import') {
        $_SESSION['vial_editor']['last_action'] = 'import';

        // Download file from user and copy it in memory
        upload_file_to_memory('file', dirname(__FILE__) . '/tmp');

        header('Location: index.php');
        exit();
    }

    if ($_POST['action'] == 'export') {
        header("Content-Disposition: attachment; filename=layout.vial_editor");
        echo json_encode($_SESSION['vial_editor']['data']);
        exit();
    }

    if ($_POST['action'] == "swap-key") {
        $_SESSION['vial_editor']['last_action'] = 'swap-key';
        swap_layout('layout', intval($_POST['a']), intval($_POST['b']));
        header('Location: index.php');
        exit();
    }

    if ($_POST['action'] == "swap-rotary") {
        $_SESSION['vial_editor']['last_action'] = 'swap-rotary';
        swap_layout('encoder_layout', intval($_POST['a']), intval($_POST['b']));
        header('Location: index.php');
        exit();
    }

    header('Location: index.php');
    exit();
}

?>

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
    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='export'>
        <input type='submit' value='Export'>
    </form>
    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='clear'>
        <input type='submit' value='Clear'>
    </form>

    <h2>Layout explorer</h2>
    <a target="_blank" rel="noopener noreferrer" href='layout_keys.php'>Keys layout</a>
    <a target="_blank" rel="noopener noreferrer" href='layout_encoders.php'>Encoders layout</a>

    <h2>Swap key layout</h2>
    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='swap-key'>
        <input type='number' name='a' min='0' max='15' step='1' value='0'>
        <input type='number' name='b' min='0' max='15' step='1' value='0'>
        <input type='submit' value="Swap">
    </form>

    <h2>Swap encoders layout</h2>
    <form action='index.php' method='post'>
        <input type='hidden' name='action' value='swap-rotary'>
        <input type='number' name='a' min='0' max='15' step='1' value='0'>
        <input type='number' name='b' min='0' max='15' step='1' value='0'>
        <input type='submit' value="Swap">
    </form>



    <?php require_once 'src/alert.php'; ?>
</body>

</html>