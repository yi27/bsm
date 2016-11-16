<?php
// Bsm GUI configuration file.
global $DB;

$DB['TYPE']     = 'MYSQL';
$DB['SERVER']   = 'localhost';
$DB['PORT']     = '3306';
$DB['DATABASE'] = 'zabbix';
$DB['USER']     = 'root';
$DB['PASSWORD'] = 'root';

// Schema name. Used for IBM DB2 and PostgreSQL.
$DB['SCHEMA'] = '';

$BS_SERVER      = '127.0.0.1';
$BS_SERVER_PORT = '10051';
$BS_SERVER_NAME = '';

@$IMAGE_FORMAT_DEFAULT = IMAGE_FORMAT_PNG;
