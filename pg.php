<?php
$connection = pg_connect("host=localhost port=5432 dbname=stms_dev user=stms_dev password=p@ssword");
pg_query("select * from tcomponent limit 10");
