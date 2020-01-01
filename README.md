# LoadSmile PHP Technical Task
Suggested recipes for lunch API

## How to Deploy
__1. Install Git__

Open your console and run this command
```console
$ sudo apt-get install git
```

__2. Install Composer__
```console
  cd ~
  curl -sS https://getcomposer.org/installer -o composer-setup.php
  sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

__3. Clone this Repository__
```console
git clone https://github.com/el-aprico/elka-aprico-techtask-php.git && cd elka-aprico-techtask-php
```
__4. Copy .env File__
```console
  cp .env.example .env
```

__5. Install Composer Project__
```console
  composer install
```

__6. Run Symfony Server__
```console
  symfony server:start
```

__7. Test on your browser__

Open your browser and type http://127.0.0.1:8000/

__8. Launch Lunch API__
- Use `http://127.0.0.1:8000/api/v1/lunch` to get recipes
- Use `http://127.0.0.1:8000/api/v1/lunch?best-before=2019-12-06` to get recipes filtered by best-before
- Use `http://127.0.0.1:8000/api/v1/lunch?use-by=2019-12-06` to get recipes filtered by use-by

__9. Unit Test__
Create new tab on your console, and run this command
```console
  ./bin/phpunit
```

## Thank You
