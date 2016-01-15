<?php
	$dbh = new Pgsql("host=localhost dbname=zendtest user=postgres password=grabdeal")or die("Can't connect to database".pg_last_error());