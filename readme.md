# Content - Laravel 3 bundle

[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

***Content* is a text-based CMS bundle for the [Laravel PHP Framework (version 3)](http://laravel.com)**.  
It uses a JSON file to describe the site structure, (custom) page properties and page assets. 
It uses Markdown textfiles for content. 
Being text-based it is easy to version your content using a VCS, like GIT.  

Features:

- All content is text-based and therefore easy to version using a vcs like Git.
- No database needed.
- Content is based on Markdown.
- Per page template, title and visibility settings, with sensible defaults.
- Support for user-defined properties.
- Filter functions.
- Asset function for connecting various types of assets to a page.
- Sitemap data generator.

## Installation

### Download

Use Artisan to install *Content*:

``` bash
php artisan bundle:install content
```

Alternatively you can download it from [https://github.com/ofnof/laravel-content/](https://github.com/ofnof/laravel-content/).
Put the downloaded files in a new folder called *bundles/content*.

### Enable

Enable the bundle by adding the following to your *bundles.php* file:

``` php
return array(
  'content' => array('auto' => true),
)
```

### Create the content directory

And finally, create a directory called 'content' under the storage directory. This is where the
content pages will reside.

## Usage

### Define the content

First we need to create a content definition file. This file describes all the pages that will be
handled by the *Content* bundle.

The content definition file should be created at *storage/content/content.json* and looks like this:

``` json
{
  "pages": [
    { "path": "projects" },
    { "path": "projects/moon-tower" },
    { "path": "projects/time-machine" },
    { "path": "about" }
  ]
}
```

The pages array contains page objects that each contain a path.
This path references the URI to handle and also a directory under
the *storage/content* directory that contains the actual content page. This content page
has the name *page.md* and is in the [Markdown Extra](http://michelf.ca/projects/php-markdown/extra/) format.

Given the above content definition and the domain www.mydomain.com, this means that the following
URIs will be handled:

www.mydomain.com/projects (content file: storage/content/projects/page.md)  
www.mydomain.com/projects/moon-tower (content file: storage/content/projects/moon-tower/page.md)  
www.mydomain.com/projects/time-machine (content file: storage/content/projects/time-machine/page.md)  
www.mydomain.com/about (content file: storage/content/projects/page.md)  

To have Laravel catch all remaining routes and show a page based on the content definitions you should
add this route below your other routes in *application/routes.php*:

``` php
Route::get('(.*)', function () {
  return Content::makeView(URI::current());
});
```

### Filling a template

By default the `Content::makeView` call will render the view named *page.blade.php*. You can then
fill the view using the *$page* object.

`$page->getTitle()` - The title of the page.  
`$page->getContent()` - The Markdown formatted content of the content file.

### Defaults

**Overriding the view**

The view used to render the page can be overridden by defining a property called *template*:

``` json
	{
		"pages": [
			{
				"path": "projects",
				"template": "mypage"
			}
		]
	}
```

**Overriding the title**

The title of the page is derived from the slug of the path. It is created by removing
the hypens, making all words lowercase and then make the first letter of each word uppercase.
This behaviour can be overridden by defining a property called *title*:

``` json
	{
		"pages": [
			{
				"path": "projects/iphone-game",
				"title": "iPhone game"
			}
		]
	}
```

**Making a page invisible**

All pages are visible by default. By adding a property called *visible* this behaviour can be changed:

``` json
	{
		"pages": [
			{
				"path": "projects/my-secret-project",
				"visible": "false"
			}
		]
	}
```	

Invisible pages:

1. will not appear in the sitemap data.
2. can use the method `$page->getVisible()` to request if the page should be visible.
   You can use this method for example to prevent your page from being indexed. See the following Blade snippet:

-
``` php
	<!DOCTYPE html>
	<html>
    	<head>
        	<meta charset="utf-8">
			@if (!$page->getVisible())
			<meta name="robots" content="noindex">
			@endif
	    	</head>
	    <body></body>
	</html>
```

### Additional properties

By specifying additional properties in the content definition file, you can pass any value
to the view.

Define the properties:

``` json
	{
		"pages": [
			{
				"path": "projects/time-machine",
				"description": "a short meta description",
				"disqusid": "XXX-YYY-ZZZ",
				"script-id": "time-machine"
			}
		]
	}
```

Get the properties with `$page->getValue('key')`. If a property does not exist for a page
`getValue()` will return *null* so that your template can easily handle non-defined properties.

### Sitemap data generator

The *Content* bundle can create an array of sitemap data. You can use this data
to create your own sitemap, or you can feed the data into the Sitemapper bundle to create a
sitemap automatically.

The following optional page properties will be passed to the sitemap:
*priority*, *lastmod* and *changefreq*.
You can find more information about sitemaps and these properties at [http://www.sitemaps.org/]().

To prevent a page from appearing in the sitemap array, add a `"visible": "false"` property to the page.

### Assets array generator

Another feature of this bundle is to generate arrays of assets that belong to a page.

For example, let's assume you have defined a page with the path *projects/time-machine* in the contents.json file
and in that view you want to show all the photos of this fantastic time machine.
To do so you have to create a folder under your public folder where you put the images. Next, make a page
reference in your contents.json file to this folder. You can call this reference anything you like.

The example below shows two different asset sets: *img* and *pdf*.

``` json
	{
		"pages": [
			{
				"path": "projects/time-machine",
				"img": "img/tm-photos",
			},
			{
				"path": "projects/a-project-without-assets",
			},
			{
				"path": "projects/a-project-with-multiple-assets",
				"img": "img/great-photos",
				"pdf": "various/pdf"
			}
		]
	}
```

Now calling the function `$page->getAssets([your-asset-set])` will return an array of files located in the
given folder.  
Each array element contains two values, one for the key *normal* and one for the key *small*. Under normal
circumstances, only the *normal* value will have a value. The *small* key will only be filled when a file
end with the name *_sml*. This makes it easy to define sets of images with a small thumbnail. So for
example if you have an image called 'the-time-machine.jpg' and a thumbnail called 'the-time-machine_sml.jpg'
they will appear in the same array set.

An example Blade view for a slideshow could look like this:

``` php
	<?php $assets = $page->getAssets('img'); ?>
	@if (count($assets) > 0)
	<div class="gallery-container">
		<ul class="gallery clearfix">
			@foreach ($assets as $img)
			<li class="gallery-image">
				<a href="{{ $img['normal'] }}" rel="photos">
					<img src="{{ $img['small'] }}" width="160" height="120">
				</a>
			</li>
			@endforeach
		</ul>
	</div>
	@endif
```

### Filtering

Filtering pages is easy. Just call `Content::getPages` with a path filter.
This allows you to create menus or lists for example.

``` php
	Route::get('guides', function () {
		$guides = Content::getPages('guides/');

		$txt = '<div class="guides">';
		foreach ($guides as $guide) {
			$txt = $txt.'<b><a href="'.$guide->properties['path'].'">'.$guide->getTitle().'</a></b> - ';
			$txt = $txt.$guide->properties['description'];
			$txt = $txt.'<br><br>';
		}
		$txt = $txt.'</div>';

		$page = new Page();
		$page->properties['path'] = 'guides';
		$page->properties['content'] = $txt;

		$data = array('page' => $page);
	    return View::make('page', $data);
	});
```

### Handling the index page

Take these steps if you want to have the Content bundle handle the root page ('/') of your site.

Add a page with a path named *index*.

``` json
	{
		"pages": [
			{ "path": "index" }
		]
	}
```

Change the content of the catch-all route to:

``` php
	$uri = URI::current();
	if ($uri == '/') {
	 	$uri = 'index';
	}
	return Content::makeView($uri);
```

And create your root content file at *storage/content/index/page.md*.

## Roadmap

I will not personally update this project anymore. For reference: the following ideas were considered at the time 
of initial development (2013)

- Make it framework independent and available as a composer package.
- Functions for iterating over the parents and children of a page (to easily create menus).
- CMS end-user interface.

## License

Licensed under the [MIT License](http://opensource.org/licenses/MIT).
