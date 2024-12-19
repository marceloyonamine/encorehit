<?php

// by Marcelo T Yonamine

// Install PostgreSQL on Linux Server
   
   // command to access postgres using sudo
//sudo -u postgres psql

// inside the postgres terminal list all databases
//\l

// inside the terminal postgres command to create database
// CREATE DATABASE usuarios;


   
   $host        = "host = localhost";
   $port        = "port = 5432";
   $dbname      = "dbname = usuarios";
   $credentials = "user=postgres password=changepasswordexample"; //change postgreSQL parameters here

   $db = pg_connect( "$host $port $dbname $credentials"  );
   if(!$db) {
      echo "Error : Unable to open database\n";
      echo pg_last_error($db);
   } else {
      echo "Opened database successfully\n";
   }
   
   $sql =<<<EOF
      CREATE TABLE USERS
      (ID SERIAL PRIMARY KEY,
 email VARCHAR(100) NOT NULL,
 first_name VARCHAR(50) NOT NULL,
 last_name VARCHAR(50) NOT NULL,
 gender VARCHAR(50) NOT NULL,
 full_name VARCHAR(100) NOT NULL,
 picture VARCHAR(255) NOT NULL,
 verifiedEmail INT DEFAULT 0,
 token VARCHAR(255) NOT NULL,      
 numanota INT DEFAULT 0,
 numgeminiai INT DEFAULT 0,
 numnotif INT DEFAULT 0,
 numanotaref INT DEFAULT 15,
 numgeminiairef INT DEFAULT 15,
 numnotifref INT DEFAULT 15,
 transactionid VARCHAR(150) DEFAULT NULL,
 created TIMESTAMP NOT NULL);
EOF;

   $ret = pg_query($db, $sql);
   if(!$ret) {
      echo pg_last_error($db);
   } else {
      echo "Table created successfully\n";
   }
   pg_close($db);
?>
