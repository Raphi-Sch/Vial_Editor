<?php session_start(); ?>

<h1>Cache viewer</h1>

<h2>Alert</h2>
<pre><?php print_r($_SESSION['alert']);?></pre>

<h2>Cache</h2>
<pre><?php print_r($_SESSION['vil']);?></pre>

<script>
let blurred = false;
window.onblur = function() {
    blurred = true;
};
window.onfocus = function() {
    blurred && (location.reload());
};
</script>