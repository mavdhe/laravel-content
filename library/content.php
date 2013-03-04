<?php

/**
 * Content.
 */
class Content 
{
	/**
	 * @var array Collection of pages.
	 */
	private $pages = array();

	/**
	 * Creates and returns a view based on the URI. Returns 404 if there is no valid content
	 * definition for the given URI.
	 *
	 * @param string URI to make the view for.
	 */
	public static function makeView($uri)
	{
		$struc = new Content();
		$struc->load();

		$strucPage = $struc->findPage($uri);

		if ($strucPage == null) {
			return Response::error('404');
		} else {
			$data = array('page' => $strucPage);
	    	return View::make($strucPage->getTemplate(), $data);
		}
	}

	/**
	 * Returns the pages that contain the URI in their path.
	 * 
	 * @param  string $uri The URI to check.
	 * @return array       Pages that contain the URI in their path.
	 */
	public static function getPages($uri)
	{
		$struc = new Content();
		$struc->load();

		$pages = array();
		$pages = $struc->findPages($uri);
		return $pages;
	}

	/**
	 * Returns an array of all the pages with the properties that can be used in a sitemap
	 * (loc, priority, lastmod, changefreq), for all the pages that do not have the
	 * 'visible' attribute set to false.
	 * 
	 * @return array An array of pages with sitemap relevant properties.
	 */
    public static function getSitemap()
    {
		$struc = new Content();
		$struc->load();

		$urls = array();
		foreach ($struc->pages as $page) {
			if ($page->getVisible()) {
				$url['loc'] = URL::base().'/'.$page->properties['path'];
				if (array_key_exists('priority', $page->properties)) {
	        		$url['priority'] = $page->properties['priority'];
	        	}
	        	if (array_key_exists('lastmod', $page->properties)) {
	        		$url['lastmod'] = $page->properties['lastmod'];
	        	}
	        	if (array_key_exists('changefreq', $page->properties)) {
	        		$url['changefreq'] = $page->properties['changefreq'];
	        	}
	        	$urls[] = $url;
			}
		}

		return $urls;
    }

    /**
     * Loads the content file and turns the content into page objects (object property $pages).
     */
    private function load() 
    {
        $path = path('storage').'content/content.json';
		$jsonText = file_get_contents($path);
		$json = json_decode($jsonText, true);
		foreach ($json['pages'] as $jsonPage) {
			$page = new Page();
			$page->properties = $jsonPage;
			array_push($this->pages, $page);
		}
    }

    /**
     * Returns the Page where the path property equals $uri.
     * 
     * @param  string $uri The $uri of the page to find.
     * @return Page The page specified by $uri, or null if the page is not in the collection.
     */
    private function findPage($uri) 
    {
    	$page = null;
    	foreach ($this->pages as $pageItem) {
			if ($pageItem->properties['path'] == $uri) {
				$page = $pageItem;
			}
	    }
		return $page;
	}

	/**
	 * Returns the pages that contain the uri as part of the path property.
	 * 
	 * @param  string $uri URI to match.
	 * @return array An array of the pages that match the URI.
	 */
    private function findPages($uri) 
    {
    	$foundPages = array();
    	foreach ($this->pages as $pageItem) {
			if (strlen(strstr($pageItem->properties['path'], $uri))) {
				array_push($foundPages, $pageItem);
			}
	    }
		return $foundPages;
	}

}
