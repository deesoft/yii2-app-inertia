# Yii2 Inertia App

## Persyaratan
Aplikasi ini memerlukan PHP versi 7.1 atau yang lebih tinggi, juga nodejs versi 18. 
`composer` juga diperlukan untuk menginstall dependensi yang diperlukan.
Ekstensi yang digunakan dalam aplikasi ini adalah [deesoft/yii2-inertia](https://github.com/deesoft/yii2-inertia) dan [deesoft/yii2-gii](https://github.com/deesoft/yii2-gii).


## Installation
Download source code aplikasi dengan perintah berikut:

```
git clone https://github.com/deesoft/yii2-app-inertia.git yii-app
cd yii-app
php init
composer install
```
Edit file `.env` untuk menambahkan konfigurasi database dan yang lain.

Untuk menyiapkan bagian frontend, jalankan perintah berikut:

```
yarn install
```

Untuk menjalankan aplikasi, gunakan perintah:
```
yarn dev-php
```
Lalu buka aplikasi di ```http://localhost:8080```.


