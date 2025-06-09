<script>
    <?php
    if (alert_is_set()) {
        echo "alert(`" . alert_get() . "`)";
        alert_clear();
    }
    ?>
</script>