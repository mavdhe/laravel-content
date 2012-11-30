# Content - Laravel bundle

*Content* is a bundle for the [Laravel PHP Framework](https://github.com/laravel/laravel) that enables
you to quickly add Markdown based content pages to your site. It is inspired by the
[Stacey CMS](http://staceyapp.com).

Features:

- Easy to add new pages. Content is based on Markdown.
- All content is text-based and therefore easy to version using GIT.
- No database needed.
- Customizable with user-defined properties.
- Filter functions.
- Image array generator for images related to a page.
- Sitemap data generator.

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

And finally, create a directory called 'content' under the storage directory. This is where the
content pages will reside.

## Usage

### Define the content

First we need to create a content definition file. This file describes all the pages that will be
handled by the *Content* bundle.

The content definition file should be created at *storage/content/content.json* and looks like this:

```javascript
{
	"pages": [
		{
			"path": "projects",
		},
		{
			"path": "projects/moon-tower",
		},
		{
			"path": "projects/time-machine",
		},
		{
			"path": "about"
		}
	]
}
```

The pages array contains page objects that each contain a path.
This path references the URI to handle and also a directory under 
the *storage/content* directory that contains the actual content page. This content page
has the name *page.md* and is in the Markdown format. 

Given the above content definition and the domain www.mydomain.com, this means that the following
URIs will be handled:

www.mydomain.com/projects (content file: storage/content/projects/page.md)
www.mydomain.com/projects/moon-tower (content file: storage/content/projects/moon-tower/page.md)
www.mydomain.com/projects/time-machine (content file: storage/content/projects/time-machine/page.md)
www.mydomain.com/about (content file: storage/content/projects/page.md)

To have Laravel catch all remaining routes and show a page based on the content definitions you should 
add this route below your other routes in *application/routes.php*:

```php
Route::get('(.*)', function () {
	return Content::makeView(URI::current());
});
```

### Filling a template

By default the Content::makeView call will render the view named *page.blade.php*. You can then 
fill the view using the *$page* object.

**$page->getTitle()** - The title of the page.  
**$page->getContent()** - The Markdown formatted content of the content file.

### Overriding behaviour

**Overriding the view**

The view used to render the page can be overridden by defining a property called *template*:

```javascript
{
	"path": "projects",
	"template": "mypage"
}
```

**Overriding the title**

The title of the page is derived from the slug of the path. It is created by removing 
the hypens, making all words lowercase and then make the first letter of each word uppercase.
This behaviour can be overridden by defining a property called *title*:

```javascript
{
	"path": "projects/iphone-game",
	"title": "iPhone game"
}
```

### Additional properties

By specifying additional properties in the content definition file, you can pass any value
to the view. 

Define the properties:

```javascript
{
	"path": "projects/time-machine",
	"description": "a short meta description",
	"comments": true,
	"disqusid": "XXX-YYY-ZZZ",
	"script-id": "time-machine"
}
```

Get the properties with **$page->getValue('key')**. If a property does not exist for a page
*getValue()* will return null so that your template can easily handle non-defined properties.

### Sitemap data generator

The *Content* bundle can create an array of sitemap data. You can use this data 
to generate a sitemap, or you can feed the data into the Sitemapper bundle to create a
sitemap automatically.
The following optional page properties will be passed to the sitemap: 
*priority*, *lastmod* and *changefreq*.
You can find more information about sitemaps and these properties at [http://www.sitemaps.org/]().

### Image array generator

Another feature of this bundle is to generate arrays of images that correspond to a page. 

For example let's say you have defined a page with the path *projects/time-machine* in the contents.json file
and in your view you want to show all the photos of this fantastic time machine.
To do this you have to create the following folder *[public]/img/content/[path]*, so in this case
*public/img/content/projects/time-machine*. Then put your images in this directory. Now you
can call `$page->getImages()` to get an array containing the paths to the images.
As a bonus you can also add thumbnails by creating images that end with *_sml*. For example if you 
have an image called 'the-time-machine.jpg' then the thumbnail should be called 'the-time-machine_sml.jpg'.

An example Blade view for a slideshow could look like this:

```php
@foreach ($page->getImages() as $img)
<div class="gallery-image">
	<a href="{{ $img['normal'] }}" rel="photos"> 
		<img src="{{ $img['small'] }}" width="160" height="120">
	</a>
</div>
@endforeach
```

### Filtering

-TODO-

### Handling the index page

Take these steps if you want to have the Content bundle handle the root page ('/') of your site.

Add a page with a path named *index*.

```javascript
{
	"path": "index"
}
```

Change the content of the catch-all route to:

```php
$uri = URI::current();
if ($uri == '/') {
 	$uri = 'index';
}
return Content::makeView($uri);
```

And create your root content file at *storage/content/index/page.md*.

## Roadmap

Ideas:

- Functions for iterating over the parents and children of a page (to easily create menus).
- Turn the image generator array into an asset generator array to allow other extensions.

Pull requests are welcome!

## License

Licensed under the MIT License.

