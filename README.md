AllData
=======

To install AllData you need to create a MySQL database and insert the database structure from the **app/alldata_database_structure.sql** file. 

Then you have to setup the database connection by renaming **app/Config/database_must_be_setup.php** to **database.php** and setting the database name, host, username and password in this file. Then you are ready to go!

Documentation
=============

The code documentation for PHP is in the folder: **app/webroot/docs/php**
The code documentation for Javascript is in the folder: **app/webroot/docs/js**
The documentation can be generated using the **app/GenerateDocs.bat**
