# JQueue bundle
Simple queueing for Symfony2

## Features
- Worker command
- Base job entity

## Installation
Run the following command to require the bundle via composer:
```sh
composer require an1zhegorodov/j-queue-bundle
```
Register bundle in AppKernel.php:
```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new An1zhegorodov\JQueueBundle(),
        // ...
    );
}
```

## TODO
* Ability to process all job types with one worker
* Page displaying the grid with jobs