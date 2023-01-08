<?php

#데이터베이스 정의
$DBMS = 'MySQL';
$DB = array();
$DB[ 'db_server' ]   = '127.0.0.1';
$DB[ 'db_database' ] = 'dvwa';
$DB[ 'db_user' ]     = 'root';
$DB[ 'db_password' ] = '';
$DB[ 'db_port'] = '3306';

#데이터베이스 연결
$conn = mysqli_connect($DB['db_server'], $DB['db_user'], $DB['db_password'], $DB['db_database']);

?>
