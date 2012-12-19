# Content - Laravel bundle

***Content* is a text-based CMS bundle for the [Laravel PHP Framework](http://laravel.com)**.  
It uses a JSON file to describe the site structure, (custom) page properties and page assets. 
It uses Markdown textfiles for content. 
Being text-based it is easy to version your content using a VCS, like GIT.  

## Installation

Use Artisan to install *Content*:

```bash
php artisan bundle:install content
```

Enable it by adding the following to your `bundles.php` file:

```php
return array(
  'content' => array('auto' => true),
)
```

And finally, create a directory called 'content' under the storage directory.

## Usage

[Read the manual here](http://ofnof.com/projects/content-laravel-bundle) for instructions.

## License

Licensed under the MIT License.
