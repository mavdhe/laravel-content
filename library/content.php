<?php

class Content 
{
	private $pages = array();

	/**
	 * Creates and returns a view based on the URI. Returns 404 if there is no valid content
	 * definition for the given URI.
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
	 */
	public static function getPages($uri)
	{
		$struc = new Content();
		$struc->load();

		$pages = array();
		$pages = $struc->findPages($uri);
		return $pages;
	}

    public static function getSitemap()
    {
		$struc = new Content();
		$struc->load();

		$urls = array();
		foreach ($struc->pages as $page) {
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

		return $urls;
    }

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
