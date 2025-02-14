<?php

	include 'functions/init.php';

	session_destroy();

	header("Location: login.php");

	exit();

?>