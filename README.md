## Date Time Record about

This is the Date Time Record system. A basic website where users can login, time in, time out, and modify employees as well as print time audit.
The system uses the following:
* HTML5
* CSS
* Vanilla Javascript
* PHP 5.3+
* MariaDB

Tested environment:
* Windows 10 x64
* Microsoft Edge v80
* WAMP 3.2.0 64bits
* Apache 2.4.41 - *included in WAMP*
* PHP 7.3.12 - *included in WAMP*
* MariaDB 10.4.10 - *included in WAMP*
* phpMyAdmin - *included in WAMP*

Supported environent:
* OS - Windows XP and above 32bit (x86) or 64bit (x64) and Linux (Redhat, Fedora, FentOS or atleast Ubuntu 16.04 LTS)
* Software - Webhosting with MySQL and PHP support and phpMyAdmin. For Windows you may use WAMP or XAMPP.

Used Editor:
* Notepad++ (you can use any text editor as long as encoding is set to UTF-8 for universal support)
* phpMyAdmin - for database

# How to install
*Prerequisite: Install WAMP or XAMPP. Run WAMP or XAMPP, to setup necessary web services, as administrator*
1. Download as zip from **Clone or Download**.
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

## ./require_header.php about
The system will also contain many conditions as well. But the important ones will be set in the page called *require_header.php* which is called in all the pages. One example will be navigating to different menu which will be different if a user doesn't have special priviledges.
It also contains PHP condition to logout user due to inactivity. Otherwise, every navigate to a page or even a simple refresh will add 1 hour to your activity timer. Here are the important PHP conditions used:
* ```$_SESSION['login-id']``` will be your login ID.
* ```$_SESSION['login-name']``` will be your login name with format: ```<last name>,<nobreakspace><first name>```.
* ```$_SESSION['login-level']``` will be your login level priviledge. 0 for admin or 1 for regular. SQL property for the table is ```BOOLEAN``` or ```TINYINT(1)``` so it only accepts  or 1..
* ```$_SESSION['expire']``` your login duration. If it ends without activity then you'll be logged out.

## ./login.php about
Login page is a bit special, since this page contains both logins and logouts. The page uses ```$_GET``` to differentiate between login or logout. This page contains many conditions to avoid error in ```$_GET``` variables since it is stored along with the site url. So if the variable is unknown we set it to throw an error message. Below are the list of ```$_GET``` variables used:
* ```$_GET['log']``` or ```site.com/login.php?log=0``` = If log variable is 0, it means login.
* ```$_GET['log']``` or ```site.com/login.php?log=1``` = If log variable is 1, it means logout.
* ```$_GET['ref']``` or ```site.com/login.php?log=0&ref=random``` = If ref is not null but not specified then it should show that you need to login first. I set this for when a user goes to a link from browser history and such.
* ```$_GET['ref']``` or ```site.com/login.php?log=0&ref=expire``` = If ref is set with expire then it will show that you need to login again. I set this for when a user is inactive while in a page.
* Of course if the log is not specified it will throw an error. Variable ref is only optional and will only show when specified.


*To be continued..*

