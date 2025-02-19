# Final-Webpage


to get the website to run successfully on your system. 

1 ensure that UniServerZ is located at a root level. Example: "E:\UniServerZ\" 

2 download the zip file and extract it to your desktop.

3 open the extracted file from the github zip open the file  and drag .env into acetraining and then drag acetraining into "E:\UniServerZ\www\

4 open UniZerverZ and start both my sql and apache and then click view www and phpmyadmin.

5 go to phpmyadmin and import schema.sql located within "E:\UniServerZ\www\acetraining\lib\schema.sql

6 load the webpage by opening it from the unizerverz serverpage. when the website loads the index.php should show. but before all logic can work these commands must be ran in the url

7 /localhost/acetraining/config/checkdb.php

8 /localhost/acetraining/config/hashdbpass.php

9 after these have been ran redirect to the main website page by using the directory

/localhost/acetraining/

if db tables are present and exisitng passwords are hashed the website should work as intended.


