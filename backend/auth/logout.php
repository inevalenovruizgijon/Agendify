<?php
session_start();
session_destroy();
header("Location: ../../public/views/index.php");
exit();