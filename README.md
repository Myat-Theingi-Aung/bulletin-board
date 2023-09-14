# AMK POS System

## Requirements
- PHP 8.1
- MySQL 5.7 or 8

## Installation

Clone the repo locally:
```
git clone https://github.com/Myat-Theingi-Aung/bulletin-board.git
```

`cd` into cloned directory and install dependencies. run below command one by one.
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
composer dump-autoload
```

### Configuration in `.env` file

Database **eg.**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bulletinBoard
DB_USERNAME=root
DB_PASSWORD=
```

## Database Migration

Run database migrations:
```
php artisan migrate
```

Run database seeder:
```
php artisan db:seed
```

## Server Run

Run the dev server:
```
php artisan serve
```

Visit below url:
```
http://localhost:8000
```
