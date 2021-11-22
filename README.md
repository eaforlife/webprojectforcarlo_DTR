# webprojectforcarlo_DTR
Session Based Login

-This is the Session Based Login system. A basic website where users can time-in and time-out via login session, edit profile, view session summary and modify employees.
The system uses the following:
* HTML5
* BOOTSTRAP 5.1
* Javascript
* PHP

Tested environment:
* Windows 11 x64
* Microsoft Edge v96
* XAMPP version 8.0.12 with Apache and MySQL module active

Supported environent:
* OS - Windows 7 and above 32bit (x86) or 64bit (x64) and Linux (Redhat, Fedora, FentOS or atleast Ubuntu 16.04 LTS)
* Software - Webhosting with MySQL/MariaDB and PHP support and phpMyAdmin. For Windows you may use WAMP or XAMPP.

Used Editor:
* Notepad++ (you can use any text editor as long as encoding is set to UTF-8 for universal support)
* phpMyAdmin - for database

# How to install
*Prerequisite: Install WAMP or XAMPP. Run WAMP or XAMPP, to setup necessary web services, as administrator*
1. Download as zip from **Clone or Download** or use download using one of the links in the pre-release section.
1. Unzip and extract content to a folder. Name the new folder something short and easy to remember.
1. Copy the folder you created to your WAMP (```<WAMP Install Directory\www\```) or XAMPP (```<XAMPP Install Directory\htdocs\```).
1. Open the database folder inside the content of the folder you made in step 2.
1. Run WAMP or XAMPP and make sure the status is running or the green indicator from the taskbar.
1. Open phpMyAdmin via going to your internet browser and go to ```http://localhost/phpmyadmin/``` or ```http://127.0.0.1/phpmyadmin/```.
1. Login using username: root and password: *password you had set during the installation of your WAMP or XAMPP*.
1. Go to ```Import``` on the main tab.
1. Under ```File to Import``` click on the button ```browse``` and go to the database folder in step 4 and select the ```createdatbase.sql``` file. Leave the rest of the options as default then hit the ```Go``` button below the page.
1. You may now close the phpMyAdmin page and now go to the directory of the folder as stated in step 3.
1. Open the file ```myCon.php``` with notepad. Edit the following inside the double quotes: ```$serverDB -> leave as is or the ip of your database server. $usernameDB and $passwordDB -> are the credentials you used to login phpMyAdmin like in step 7. $dbName -> leave as is or the name of the schema/table you named.``` Then save and close the notepad.
1. That's it. To view the page, open your web browser and go to the url ```http://localhost/<foldername like in step 2>/``` or ```http://127.0.0.1/<foldername like in step 2>/```. Default username and password in the website is: username => admin and password => pass.

## Important notice
For learning purposes, since html/css doesn't have WYSIWYG editor, it is important to have a browser ready with developer mode on and set it to console whenever testing the site. This way you may debug any issue with javascript errors and warnings.

The site heavily uses [AJAX(Asynchronous JavaScript And XML)](https://www.w3schools.com/xml/ajax_intro.asp) to populate divs from the output of a different html/php file (let's call this AJAX file).
Debugging with AJAX is almost impossible, but if error occurs, then you will redirected to the AJAX page.
