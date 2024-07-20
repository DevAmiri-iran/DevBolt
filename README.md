# DevBolt

## Introduction
DevBolt is a PHP framework designed to streamline web application development. It provides a robust structure for your application and a set of tools to make common web development tasks easier.

## Table of Contents
- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Dependencies](#dependencies)
- [Configuration](#configuration)
- [Examples](#examples)
- [Contributors](#contributors)
- [License](#license)

## Installation
To install DevBolt, clone the repository and install dependencies using Composer:
```bash
composer create-project devamiri/devbolt
```
or
```bash
git clone https://github.com/DevAmiri-iran/DevBolt.git
```
and
```bash
cd DevBolt
composer install
```

## Usage

Start the server:
```txt
Running the project with a browser
```

## Features

- Blade templating engine
- Database migrations
- Internal api management

## Dependencies

- PHP 8.2 or higher
- Composer
- MySQL

## Configuration

Copy .env.example to .env and set your environment variables:
```bash
cp .env.example .env
```

## Examples

### Route
Here is an example of a simple route definition:

```php
Route::api('/', 'index');

Route::view('/', 'index');
```
or
```php
Route::post('/', function (){
    return 'hello world';
});

Route::get('/', function (){
    return 'hello world';
});
```

### database
To use the database:
```php
System::useDatabase();
```
Put in app/bootstrap.php file

### migrations
Here is an example of a simple migrations definition:
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return new class
{
    public string $table = 'users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Capsule::Schema()->create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('password', 50);
            $table->string('email', 100);
            $table->string('phone_number', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Capsule::Schema()->dropIfExists($this->table);
    }
};
```
### model
Here is an example of a simple models definition:
```php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'password', 'email', 'phone_number'];
}

```

### Middleware
Here is an example of a simple middleware definition:
```php
namespace App\Middleware;

class Test
{
    public function handle($value=null)
    {
        if ($value == 'start')
        {
            dd('The project was started');
        }
    }
}
```

### APIManager
Here is an example of a simple api handler definition:
```php
use App\Support\APIManager;

$API = new APIManager($request = APIManager::input(), true, true);
$API->validateParameters('password');


$API->handle(function () use ($request){

    if ($request['password'] == '123')
        APIManager::respond(true, 'Your password is correct');
    else
        APIManager::respond(false, 'Your password is not correct');

});
```

## Contributors

[DevAmiri](https://github.com/DevAmiri-iran)

## License
This project is licensed under the MIT License. See the LICENSE file for details.

```txt
Feel free to modify this template based on additional details or specific requirements of your project.
```
