<?php

require_once __DIR__.'/markdown.php';

class Page 
{
	/**
	 * All properties of the page.
	 * @var array All properties of the page.
	 */
	public $properties;

	/**
	 * Returns the name of the template to be used for the page. If the user didn't specify a template name, 'page' is used.
	 * @return string Name of the page template.
	 */
	public function getTemplate()
	{
		$template = 'page';
		if (array_key_exists('template', $this->properties)) {
			$template = $this->properties['template'];
		}
		return $template;
	}

	/**
	 * Returns the title of the page. If a 'title' property exists, that value will be used,
	 * otherwise the title will be created based on the path.
	 * @return string Title of the page.
	 */
	public function getTitle() 
	{
		$title = '';
		if (array_key_exists('title', $this->properties)) {
			$title = $this->properties['title'];
		} else {
	 		$split_url = explode("/", $this->properties['path']);
	 		$slug = $split_url[count($split_url) - 1];
			$title = ucfirst(preg_replace('/[-_](.)/e', "' '.strtoupper('\\1')", $slug));
		}
		return $title;
	}

	/**
	 * Returns the Markdown parsed content of the page.
	 * @return string Page content.
	 */
	public function getContent() 
	{
		$content = '';
		if (array_key_exists('content', $this->properties)) {
			$content = $this->properties['content'];
		} else {
			$path = path('storage').'content/'.$this->properties['path'].'/page.md';
			if (file_exists($path)) {
			    $content = Markdown(file_get_contents($path));
			}
		}
		return $content;
	}

	/**
	 * Returns if the page should be visisble.
	 * @return bool Visibility of the page.
	 */
	public function getVisible()
	{
		$visible = true;
		if (array_key_exists('visible', $this->properties)) {
			$visible = ($this->properties['visible'] == 'true');
		}
		return $visible;
	}

	/**
	 * Returns the value of the property with the key $key, or returns null if the 
	 * property does not exist.
	 * @param  string $key Property identifier.
	 * @return string Value of the property or null.
	 */
	public function getValue($key) 
	{
		$value = null;
		if (array_key_exists($key, $this->properties))
			$value = $this->properties[$key];
		return $value;
	}

	/**
	 * Returns the assets that belong to the property with the key $key.
	 * @param  string $key Property identifier.
	 * @return array An array where each element has a 'normal' and a 'small' key to idendify the location
	 * of the assets.
	 */
	public function getAssets($key)
	{
		$assets = array();

		if (array_key_exists($key, $this->properties)) {
			$assetsPath = $this->properties[$key];
			$searchPath = path('public').$assetsPath.'/*.*';
			$files = glob($searchPath);
			foreach ($files as $filename) {
				$file = pathinfo($filename, PATHINFO_BASENAME);
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$match = preg_match('/_sml.'.$ext.'$/', $filename);
				if ($match == 0) {
					$assets[] = array(
						'normal' => '/'.$assetsPath.'/'.$file, 
						'small' => '/'.$assetsPath.'/'.preg_replace('/.'.$ext.'$/', '_sml.'.$ext, $file)
					);
				}
			}
		}

		return $assets;
	}	
}
