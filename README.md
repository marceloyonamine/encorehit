
ENCOREHIT Project

By Marcelo T Yonamine

This is a music show search project using the Google search API and the Gemini API to filter the results. 
It also includes a web push notification server as a reminder system.
The idea behind this project is a concert search engine and a web push notification reminder system to remind you of the date of this music concert.


This project was tested on PHP Version 8.1.2-1ubuntu2.20
Apache 2
Browsers: Firefox, Chrome, Edge

in php.ini

enable PDO
enable pdo_pgsql
enable pgsql
enable mongodb
enable mbstring
enable ctype
enable curl
enable json
enable libxml
enable session



#############################################################################################################

Install mongodb:

in Linux terminal:

#sudo apt-get update

#sudo apt-get install -y mongodb-org

include security:
authorization: enabled
to /etc/mongod.conf to enable password in mongodb then create a root user in the admin database and username admin
enable PHP mongodb extension in php.ini

#mongosh

#use encorehitdb //create a database

#db.createUser({user:'admin', pwd: 'mypassword', roles:['userAdminAnyDatabase']}) //create user in mongodb

#db.auth('admin', 'mypassword') //authenticate to mongodb

#db.createCollection("encorehitauth"); // create a collection named encorehitauth

#show collections

RUN create_db_mongo.php



################################################################################################################

Install postgreSQL:

in Linux terminal:

#sudo apt update

#sudo apt-get -y install postgresql

#sudo -u postgres psql

//inside the terminal postgres command to create database

postgres=# CREATE DATABASE usuarios;

// command to change postgres password enter
postgres=#  \password postgres

enable PDO
enable pdo_pgsql
enable pgsql

in php.ini

RUN create_table_usuarios.php



################################################################################################################



VAPID.php

Run VAPID.php copy public key number and private key number

Edit send_push_notification.php and insert public key and private key numbers

Edit app.js and insert in const applicationServerKey = a public key number 


##############################################################################################################




Edit the busca.php file and insert your Google Gemini API and your Google Search API:


// value of API Google Search e Gemini

  // Replace with your API key Google Gemini https://ai.google.dev/gemini-api/docs/api-key?hl=pt-br
  $apiKeygemini = 'AIzaSyBC2xbCrJ8J-c3z6UdYKnssU-9vsHvI700'; // change it

  // Define the API URL Gemini
  $apiUrlgemini = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=$apiKeygemini";


 // https://console.cloud.google.com 
$apiKey = 'AIzaSyBh2bB5eVJXQ7DugINBJh6yxPXo9c29Doo'; // change it
$cseId = 'a55d2709f45445555'; // change it https://programmablesearchengine.google.com/controlpanel/create/congrats?cx=a55d2709f45444555





##############################################################################################################


create a crontab to test send a web push notification

*/15 * * * * php /var/www/html/encorehit/send_push_notification.php

run the script every 15 minutes


Note: The test web server computer cannot be turned off for the web push notification system to work.



#############################################################################################################

MIT License

Copyright (c) 2024 Marcelo T Yonamine

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.









