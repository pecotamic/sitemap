# Sitemap Addon for Statamic

Automatically adds a sitemap to your Statamic v3 web site. The default path is &lt;your web site&gt;/sitemap.xml

## Installation

Include the package with composer:

```sh
composer require pecotamic/sitemap
```

The package requires Laravel 7+ and PHP 7.3+. It will auto register.

## Configuration (optional)

You can override the default options by publishing the configuration:

```
php artisan vendor:publish --provider="Pecotamic\Sitemap\ServiceProvider" --tag=config
```

This will copy the default config file to `config/pecotamic/sitemap.php`.

### View (optional)

You can also override the view template to adjust the output by publishing the view:

```
php artisan vendor:publish --provider="Pecotamic\Sitemap\ServiceProvider" --tag=view
```

The view template will be copied to `resources/views/vendor/pecotamic/sitemap/sitemap.blade.php`.

If you prefer another view engine, it is also possible. For example to use Antlers, create a file named `sitemap.antlers.html` instead of the blade template.

#### View variables

An array of sitemap **entries** is passed to the view template. Each **entry** has these properties: 

 * **path**: The page path (without host name)
 * **loc**:  The absolute url
 * **lastmod**: A date object of the last modification date 
 * **changefreq**: The entry property `change_frequency`, if available 
 * **priority**: The entry property `property`, if available 

The missing values can be overriden in the template.

## Credits

Thanks for code contribution to Prageeth Silva.
