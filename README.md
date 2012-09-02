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


## Usage

### Include

Before use use any of the libraries in our code we must first include
the libraries.

This is simple, in every file of code you want to use the library insert
the following at the top of your page:

    include('toure/autoload');

All the classes will be autoloaded as and when they are needed.

### Getting Started With The Database

We can create an instance ready for queries using the following:

    $dbh = Database::make();

Now we have access to everything via $dbh, the first thing that should
be done is the setting of our current table we plan use:

    $dbh->table('posts');

once the table id set we can start to run our query methods inline:

### Selecting

Selecting can be done using the following:

    $dbh->table('posts')->select('title', 'content')->row();

The second method `select();` allows us to specify columns to fetch, if
this is left blank it will select all. The second column specifies
whether to return the first `row();` or `all();` of the rows either the `row();`
or `all();` method must be included to run the query. Toure will return an
assoc array by default but we can specify it to return different data: 

    $dbh->table('posts')->select()->all('assoc');
    $dbh->table('posts')->select()->all('num');
    $dbh->table('posts')->select()->all('obj'); 

To further our query we could use any of the conditional methods which
can be added one after another for easy readable usage:

* where();
* where_email();
* or_();
* and_();

Which can be used like so:

    $dbh->table('posts')
        ->select('title')
        ->where('id', '=', '3')
        ->or_('title', '=', 'something')
        ->all();

So we can mix and match different conditionals for different results,
the `where();`, `or_();` and `and_();` methods take three params, the first being
the column the second being the condition and the last is the value.

Check out the class files for more documentation all methods are
documented.

### Inserting

Inserting is made extremely easy by toure, we can use the `insert();` method:

    $dbh->table('posts')->insert();

The insert method takes an array like so:

    $input = array(
               'title' => 'This is our title',
               'content' => 'This is our content'
             );

    $dbh->table('posts')->insert($input)->save();

Our insert method will automagically insert the values into the columns
specified where the array key is the column and the array value is the
input value.

It is important to note here that when inserting or updating it is
required to use `save();` or your insert or update will not be executed.

### Updating

Updating is done in exactly the same way as inserting, i.e using an
array but we just specify the column that needs to be updated like so: 

     $input = array(
                'content' => 'This is our content'
              );
 
     $dbh->table('posts')->update($input)->save();

The difference with update though is that we will often require
condition otherwise if you where to execute the code above you would
change every row in the table so we can use the same conditionals as we
did in the select section.

    $dbh->table('posts')->update($input)->where('id', '=', '3')->save();

### Deleting 

Deleting can be dangerous so we have a few different methods available
to us here. 

* `delete();`
* `delete_id($id);`
* `delete_all_where();`

The `delete();` method can be used just like the other CRUD operations
using the conditional methods but is allways limited to just 1 row for
safety. 

The `delete_id();` method allows us to specify an id for deletion this
again is limited to just one row.

The `delete_all_where();` method allows us to delete multiple rows that
meet the condition.

 




