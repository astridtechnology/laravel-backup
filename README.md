[![Latest Version on Packagist](https://img.shields.io/packagist/v/astridtechnology/laravel-backup?style=for-the-badge)](https://github.com/astridtechnology/laravel-backup)
[![License](https://img.shields.io/github/license/astridtechnology/laravel-backup?style=for-the-badge)](https://tldrlegal.com/license/mit-license)
[![Total Downloads](https://img.shields.io/packagist/dt/astridtechnology/laravel-backup?style=for-the-badge)](https://github.com/astridtechnology/laravel-backup)

# Laravel Backup

Laravel Backup offers a comprehensive solution for managing backups within your Laravel application, streamlining the process with ease.

<a target="_blank" rel="noopener noreferrer nofollow" href="https://astridtechnology.com/wp-content/uploads/2024/05/laravel-backup.gif"><img src="https://astridtechnology.com/wp-content/uploads/2024/05/laravel-backup.gif" style="width: 100%; max-width: 100%; display: inline-block;">
</a>

## Installation

Before installing this package, please ensure you have installed and configured the prerequisite package <a href="https://packagist.org/packages/spatie/laravel-backup">spatie/laravel-backup</a>:

    composer require spatie/laravel-backup

To integrate Laravel Backup into your project, execute the following Composer command:

    composer require atindia/laravel-backup

## Configuration

In earlier versions of Laravel, providers were typically registered in the config/app.php file. However, with the evolution of Laravel 11.x and beyond, many configurations have been relocated to the /bootstrap directory.

In Laravel 11.x and later, the bootstrap/providers.php file returns an array containing all the providers that will be registered in your application. To include the Laravel Backup provider, add the following line to this array:

    AstridTechnology\LaravelBackup\Providers\AstridBackupServiceProvider::class,

Generate the configuration file by running:

    php artisan vendor:publish --provider="AstridTechnology\LaravelBackup\Providers\AstridBackupServiceProvider" --tag="config"

In the newly created config/projectbackup.php file, specify authorized email addresses to control route access:

    return [
        'authorized_emails' => ['example@test.com']
    ];

## Access Panel Route

You can access the backup panel via the following route:

    http://yourdomain.com/backup-panel


### License
The MIT License (MIT). Please see [License](LICENSE.md) File for more information   