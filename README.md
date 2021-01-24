# cli-splitbills

## setup (todo)
* .env
* google api client credentials
## Export transactions into a google sheet
Download .txt credit card statements (in english) from desjardins.

place them in the `transactions` folder at root of the project

build(optional) and run the php container
```
docker-compose up -d --build
```
login into the container:
```
docker exec -ti php-fpm bash
```
Run manually the script :
```
php src/application.php
```

use the `clear` option to delete the google sheet data before writing the transactions.

```
php src/application.php clear
```

# Dev notes

## todo
* read all files, right now, only the 1st file from the dir is read.
* add a date range to filter the transactions to export
* remove the entry point (application from `src`)
## autoload
Added the auto loader via composer.

the Splitbills namespace is mapped to the src directory

```{
    "autoload": {
        "psr-4": {"Splitbills\\": "src/"}
    },
```

after adding the "autoload" element, running this command is required : 
```
composer dump-autoload
```