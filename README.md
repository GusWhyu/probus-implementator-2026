<p align="center"><img src="/public/img/logo.png" width="200"></p>
<h1 align="center" color="white">PROBUS IMPLEMENTATOR</h1>

## About Probus Implementator

Probus Implementator is a system that manages several features used by the Probus System. In this project the system focuses on request management. This system can be developed again in the future to suit user needs.

## This system was built with:
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![DaisyUI](https://img.shields.io/badge/daisyui-5A0EF8?style=for-the-badge&logo=daisyui&logoColor=white)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

## Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
# Make sure you have connected the database to .env before migrate
php artisan migrate:fresh --seed
```

## Usage
```bash
php artisan serve
```
