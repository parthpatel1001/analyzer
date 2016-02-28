# Analyzer

## Setup

#### Install php7 on mac

```
brew update
brew install homebrew/php/php70

# if you get errors about having an existing php version linked do
# brew unlink php{current version here}
# brew link php70
```

#### Make sure you have composer

[Installing composer](http://www.abeautifulsite.net/installing-composer-on-os-x/)


#### Download packages:
```
composer install
```


#### Run server:
```
php -S localhost:8000
```


#### Using the app

Go to [http://localhost:8000/web/index.html](http://localhost:8000/web/index.html)



