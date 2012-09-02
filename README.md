# Toure
=======================

## Set Up

In order to get everything set up correctly we must first define our
constants in the conf.php file:

    define('DB_HOST', ''); // your database host
    define('DB_NAME', ''); // your database name
    define('DB_USER', ''); // your database username
    define('DB_PASS', ''); // your database password

There are also three additional settings:

### Defult Users Table

    define('DEFAULT_USERS_TABLE', 'users');

the above defines the default table that holds your users when dealing
with a user, password type situation it is 'users' by default.

### Timestamps

    define('TIMESTAMPS', true); 

if your tables follow a timestamp pattern ( they have created_at and
updated_at in every table) then setting this to true will automagically
deal with timestamps for you.

If you require timestamps on only select tables then set to false and
specify the columns explicitly during execution.

### Secure

    define('SECURE', true);

if you would like to make use of the libraries encryption system then
set this to true, whenever you insert into a column named password it
will automatically run Secure::make() and encrypt your password. If you
are using this make sure that whenever you check your password you use
Secure::check(); 


