t is Toure?

Toure (pronounced tour-ray) is a MySQL abstraction and input validation
library, what this means is that you can say goodbye to all that
complicated database code and validation and use a set of simple and
easy to understand methods for example. 

### Connecting to a database?

Normal

    $dbh = new PDO('mysql=host=localhost;dbname=test', 'user', 'pass');

Toure

    $dbh = Database::make();

### Selecting a table?

PDO 
 
    $stmt = $dbh->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute(array(4));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

Toure
 
    $result = $dbh->table('posts')->select()->where('id', '=',
'3')->all();

Everything is escaped and safe and the library even has its on secure
encryption system. Please read on to get up and running and save
yourself time!

# Requirements

* PHP 5.3+
* PDO

# Set Up

First of all you need to just move the "toure" folder into the base of
your app.

In order to get everything set up correctly we must first define our
constants in the conf.php file:

    define('DB_HOST', ''); // your database host
    define('DB_NAME', ''); // your database name
    define('DB_USER', ''); // your database username
    define('DB_PASS', ''); // your database password

There are also three additional settings:

### Default Users Table

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

### Include

Before use use any of the libraries in our code we must first include
the libraries.

This is simple, in every file of code you want to use the library insert
the following at the top of your page:

    include('toure/autoload');

All the classes will be autoloaded as and when they are needed.


# Database Usage

We can create an instance ready for queries using the following:

    $dbh = Database::make();

Now we have access to everything via $dbh, the first thing that should
be done is the setting of our current table we plan use:

    $dbh->table('posts');

once the table id set we can start to run our query methods inline:

## Selecting

Selecting can be done using the following:

    $dbh->table('posts')->select('title', 'content')->row();

The second method `select();` allows us to specify columns to fetch, if
this is left blank it will select all. The second column specifies
whether to return the first `row();` or `all();` of the rows either the
`row();`
or `all();` method must be included to run the query. Toure will return
an
assoc array by default but we can specify it to return different data: 

    $dbh->table('posts')->select()->all('assoc');
    $dbh->table('posts')->select()->all('num');
    $dbh->table('posts')->select()->all('obj'); 

To further our query we could use any of the conditional methods which
can be added one after another for easy readable usage:

* `where();`
* `where_email();`
* `or_();`
* `and_();`

Which can be used like so:

    $dbh->table('posts')
        ->select('title')
        ->where('id', '=', '3')
        ->or_('title', '=', 'something')
        ->all();

So we can mix and match different conditionals for different results,
the `where();`, `or_();` and `and_();` methods take three params, the
first being
the column the second being the condition and the last is the value.

Your can also use the `order_by():` method to order your results like
so:

    $dbh->table('posts')->select()->order_by('created_at',
'DESC')->all();

We can specify the column to sort on in the first argument and then the
direction we want to sort using 'DESC' for descending and 'ASC' for
ascending.

Check out the class files for more documentation all methods are
documented.

## Inserting

Inserting is made extremely easy by toure, we can use the `insert();`
method:

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

## Updating

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

## Deleting 

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

## Joins

A database join can also be performed using `join();` The join method 
can be used along with a `select();` method like so:

    $dbh->table('users')
        ->select('users.email', 'posts.title')
        ->join('users', 'users.id = posts.user_id')
        ->all();

So in the table above you can see we can specify the join with two
arguments, the first is the table to join and the second is the ON rule.
If you require more fine control over your join statements then use the
`query();` method that allows you to insert a raw query safely.


# Validation Usage

## Initializing

Validation is an essential part of most applications that deal with a
database interaction, we need to make sure that user submitted data is
clean before we run it through our system and into the database. 

This would usually have to be done in a long drawn out way that we are
all to familiar with, but don't worry! Toure offers a nice and easy
solution.

We start a validation test as follows:

    $v = Validation::make($input, $rules, $messages);

This will initialize our validation and store it in the $v variable. Our
validator takes three arguments. The first $input is an array of input
from the user for example:

    $input = array(
                'email' => $_POST['email'],             
                'password' => $_POST['password'],
                'passwordconf' => '$_POST['passwordconf']
             );

The next argument are our rules which are explained in the Rules
section:

    $rules = array(
               'email' => 'required|valid_email',
               'password' => 'required|min:6',
               'passwordconf' => 'same:password'
             );

and the final optional argument is our custom error messages, if the
messages are not included then default error messages will be include.

## Rules

We have the following rules to choose from:

* required - Specifies input is required
* string - Checks if input is string
* numeric - Checks if input is numeric
* int - Checks if input is integer
* unique:? - Queries database using default users table from config,
  column can be specified like unique:username default column is email
* same:? - Checks that input is the same as specified param eg.
  same:password
* min:? - Checks if a minimum amount of characters has been entered eg.
  min:6
* max:? - Checks if a maximum amount of characters has been entered eg.
  max:10
* in:? - Checks for a value in an array eg. in:products
* valid_email - Checks if email is valid
* valid_url - Checks if URL is valid

rules should be included as the second argument as an array with the
keys corresponding to the same keys in the input array for the first
argument. Multiple rules can be included as long as you separate each
rule using a pipe '|' :

    $rules = array(
               'email' => 'required|valid_email',
               'password' => 'required|min:6',
               'passwordconf' => 'same:password'
             );

## Running The Validation

To check whether the input has validated we can use two methods that
return Boolean results:

    $v->passed();

The above checks if there where no errors and return true if everything
passed and false if not. If needed you can check the opposite using:

    $v->failed();

We can then retrieve all our errors using the following:

    $v->get_errors();

This will return and associative array including any errors that where
returned to us from the validation if nothing went wrong this will be
empty. We can retrieve errors specific to an input if we like using:

    $v->errors('email');

The above will only return the errors from the email input field.

## Custom Error Messages

The validation class will automatically return error messages but if you
would like to use your own and it is recommended that you do, you can
include a third argument to the `Validation::make();` method, an array
of message where the key is the field and rule your error message
applies to separated by an underscore like so:

    $messages = array(
                  'email_required' => 'Email is required!', 
                  'email_valid_email' => 'Please enter a valid email
address!',
                  'passwordconf_same' => 'Passwords do not match!'
                );
 
The above will then map the error messages to the right rules and input
fields so that when you use `$v->get_errors();` your messages will be
returned instead.

## Validation Example

Putting all the above together can be quite confusing at first so let's
go through a real world example to show you how useful the validation
library is.

Let's say we have someone who will be entering information to create a
new user through a form using the POST method. First lets catch all the
data and assign it to variables:

    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordconf = $_POST['passwordconf'];

Next we need to fire up our validation and pass in three arguments that
consist of the input, rules and custom error messages:

    $input = array(
               'email' => $email,
               'password' => $password,
               'passwordconf' => $passwordconf
             );

    $rules = array(
               'email' => 'required|valid_email',
               'password' => 'required|min:6',
               'passwordconf' => 'required|same:password'
             );

    $messages = array(
               'email_required' => "Email is required",
               'email_valid_email' => 'Email is not valid',
               'passwordconf_same' => 'Passwords do not match' 
             );

    $v = Validation::make($input, $rules, $messages);

    if ($v->passed()){
        echo "passed!";
    } else {
        $errors = $v->get_errors();
    }


The code above will now run our validation and if it passes will show us
a message saying "passed" or will set our errors to a variable called
errors which we can manipulate further if we like.

