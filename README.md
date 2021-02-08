# Laravel Social App

Simple social network app with Laravel

## Getting Started

Clone the project repository by running the command:

```bash
git clone https://github.com/HT96/social-app.git
```

After cloning, run:

```bash
composer install
```

Duplicate `.env.example` and rename it `.env`

For create and start containers run:

```bash
./vendor/bin/sail up -d
```

Use the following command to get a bash shell in the container.

```bash
./vendor/bin/sail exec app bash
```

Then to set the application key you can run:

```bash
php artisan key:generate
```

Check your database details in `.env` file before running the migrations:

```bash
php artisan migrate
```

Run the following commands one by one:

```bash
npm install
npm run dev
```

And finally visit [http://localhost](http://localhost) to see the application in action.
