<?php

	include 'fonksiyonlar/bagla.php';

	session_destroy();

	header("Location: giris.php");

	exit();

?>