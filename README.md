# Getting Started
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Setup Application

1.Setup dependence
```bash
composer install
```
2.Create .env file and Change DB Credentials
```bash
cp .env.example .env
```
3.Generate APP Key 
```bash
php artisan key:generate
```
4.Start Application 
```bash
php artisan serve
```
5.Start Application with custom IP address and port 
```bash
php artisan serve --host=0.0.0.0 --port=8080 
```

## Database Actions
```bash
# Create Model,Migration,Controller file
$ php artisan make:model Test --migration --controller --resource 
# Change and affect Database
$ php artisan migrate:refresh
# Refresh the database and run all database seeds...
$ php artisan migrate:refresh --seed
```

## Important Artisan Command
```bash
# create resource api route
$ php artisan make:controller UserController --api
# API Resources for Fetching a Single Item and Multiple items
$ php artisan make:resource UserResource
# API Collection for Fetching Custom Collection-Level and Multiple items
php artisan make:resource UserCollection

```

## Run Application With Docker

```bash
# For With Docker
$ cp .env.example .env  #create .env.production file
# change API_HOST, API_PORT 
$ docker compose build --no-cache --force-rm
# build for the production server
$ docker compose up -d
# start the production server
$ docker ps
# Show All Container
$ docker compose down
```


- [E-commerce demo](https://www.jrecommerce.com/demo.php)
- [E-commerce Admin](https://www.ecomdeveloper.com/demo/admin/index.php?route=common/dashboard&user_token=k258hqpdI1g9fSGLJYmtPt9BVlI4mg58)