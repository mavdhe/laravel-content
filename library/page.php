<?php

require_once __DIR__.'/markdown.php';

class Page 
{
	public $properties;

	public function getTemplate()
	{
		$template = 'page';
		if (array_key_exists('template', $this->properties)) {
			$template = $this->properties['template'];
		}
		return $template;
	}

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

	public function getValue($key) 
	{
		$value = null;
		if (array_key_exists($key, $this->properties))
			$value = $this->properties[$key];
		return $value;
	}

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
