<?php namespace Meido\HTML;

use Illuminate\Container;

class HTML {

	/**
	 * The encoding to use
	 * 
	 * @var string
	 */
	protected $encoding = 'utf-8';

	/**
	 * The inversion of control container instance
	 * @var Illuminate\Container
	 */
	protected $container;

	public function __construct(Container $container = null)
	{
		$this->container = $container;
	}

	/**
	 * Convert HTML characters to HTML entities
	 *
	 * The encoding in $encoding will be used
	 * 
	 * @param  string $value
	 * @return string
	 */
	public function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, $this->encoding, false);
	}

	/**
	 * Convert HTML entities to HTML characters
	 *
	 * The encoding in $encoding will be used
	 * 
	 * @param  string $value 
	 * @return string
	 */
	public function decode($value)
	{
		return html_entity_decode($value, ENT_QUOTES, $this->encoding);
	}

	/**
	 * Convert HTML special characters
	 *
	 * The encoding in $encoding will be used
	 * 
	 * @param  string $value
	 * @return string
	 */
	public function specialchars($value)
	{
		return htmlspecialchars($value, ENT_QUOTES, $this->encoding, false);
	}

	/**
	 * Generate a link to a JS file
	 * 
	 * @param  string $url
	 * @param  array $attributes
	 * @return string
	 */
	public function script($url, $attributes = array())
	{
		$url = $this->container->url->to($url);

		return '<script src="'.$url.'"'.$this->attributes($attributes).'></script>'.PHP_EOL;
	}

	/**
	 * Generate a link to a CSS file.
	 *
	 * If no media type is selected, "all" will be used
	 * 
	 * @param  string $url
	 * @param  array $attributes
	 * @return string
	 */
	public function style($url, $attributes = array())
	{
		$defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');

		$attributes = $attributes + $defaults;

		$url = $this->container->url->to($url);

		return '<link href="'.$url.'"'.$this->attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Generate a HTML span
	 * 
	 * @param  string $value
	 * @param  array $attributes
	 * @return string
	 */
	public function span($value, $attributes = array())
	{
		return '<span'.$this->attributes($attributes).'>'.$this->entities($value).'</span>';
	}

	/**
	 * Generate a HTML link
	 * 
	 * @param  string $url
	 * @param  string $title
	 * @param  array $attributes
	 * @param  bool $https
	 * @return string
	 */
	public function link($url, $title = null, $attributes = array(), $https = null)
	{
		$url = $this->container->url->to($url, $https);

		if (is_null($title)) $title = $url;

		return '<a href="'.$url.'"'.$this->attributes($attributes).'>'.$this->entities($title).'</a>';
	}

	/**
	 * Generate a HTTPS HTML link
	 * 
	 * @param  string $url
	 * @param  string $title
	 * @param  array $attributes
	 * @return string 
	 */
	public function link_secure($url, $title = null, $attributes = array())
	{
		return $this->link($url, $title, $attributes, true);
	}

	/**
	 * Generate a HTML link to an asset
	 * 
	 * @param  string $url
	 * @param  string $title
	 * @param  array $attributes
	 * @param  bool $https
	 * @return string
	 */
	public function link_asset($url, $title = null, $attributes = array(), $https = null)
	{
		$url = $this->container->url->to($url, $https);

		if (is_null($title)) $title = $url;

		return '<a href="'.$url.'"'.$this->attributes($attributes).'>'.$this->entities($title).'</a>';
	}

	/**
	 * Generate a HTTPS HTML link to an asset
	 * 
	 * @param  string $url
	 * @param  string $title
	 * @param  array $attributes
	 * @return string
	 */
	public function link_secure_asset($url, $title = null, $attributes = array())
	{
		return $this->link_asset($url, $title, $attributes, true);
	}

	/**
	 * Generate a HTML link to a route
	 *
	 * An array of parameters may be specified to fill in URI segment wildcards.
	 * 
	 * @param  string $name
	 * @param  string $title
	 * @param  array $parameters
	 * @param  array $attributes
	 * @return string
	 */
	public function link_route($name, $title = null, $parameters = array(), $attributes = array())
	{
		return $this->link($this->container->url->route($name, $parameters), $title, $attributes);
	}

	/**
	 * Generate a HTML link to a controller action
	 *
	 * An array of parameters may be specified to fill in URI segment wildcards.
	 * 
	 * @param  string $action
	 * @param  string $title
	 * @param  array $parameters
	 * @param  array $attributes
	 * @return string
	 */
	public function link_action($action, $title = null, $parameters = array(), $attributes = array())
	{
		return $this->link($this->container->url->action($action, $parameters), $title, $attributes);
	}

	/**
	 * Generate an HTML mailto link.
	 *
	 * The E-Mail address will be obfuscated to protect it from spam bots.
	 *
	 * @param  string $email
	 * @param  string $title
	 * @param  array $attributes
	 * @return string
	 */
	public function mailto($email, $title = null, $attributes = array())
	{
		$email = $this->email($email);

		if (is_null($title)) $title = $email;

		$email = '&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email;

		return '<a href="'.$email.'"'.$this->attributes($attributes).'>'.$this->entities($title).'</a>';
	}

	/**
	 * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
	 *
	 * @param  string $email
	 * @return string
	 */
	public function email($email)
	{
		return str_replace('@', '&#64;', $this->obfuscate($email));
	}

	/**
	 * Generate an HTML image element.
	 *
	 * @param  string $url
	 * @param  string $alt
	 * @param  array  $attributes
	 * @return string
	 */
	public function image($url, $alt = '', $attributes = array())
	{
		$attributes['alt'] = $alt;

		return '<img src="'.$this->container->url->asset($url).'"'.$this->attributes($attributes).'>';
	}

	/**
	 * Generate an ordered list of items.
	 *
	 * @param  array  $list
	 * @param  array  $attributes
	 * @return string
	 */
	public function ol($list, $attributes = array())
	{
		return $this->listing('ol', $list, $attributes);
	}

	/**
	 * Generate an un-ordered list of items.
	 *
	 * @param  array  $list
	 * @param  array  $attributes
	 * @return string
	 */
	public function ul($list, $attributes = array())
	{
		return $this->listing('ul', $list, $attributes);
	}

	/**
	 * Generate an ordered or un-ordered list.
	 *
	 * @param  string $type
	 * @param  array  $list
	 * @param  array  $attributes
	 * @return string
	 */
	private function listing($type, $list, $attributes = array())
	{
		$html = '';

		if (count($list) == 0) return $html;

		foreach ($list as $key => $value)
		{
			// If the value is an array, we will recurse the function so that we can
			// produce a nested list within the list being built. Of course, nested
			// lists may exist within nested lists, etc.
			if (is_array($value))
			{
				if (is_int($key))
				{
					$html .= $this->listing($type, $value);
				}
				else
				{
					$html .= '<li>'.$key.$this->listing($type, $value).'</li>';
				}
			}
			else
			{
				$html .= '<li>'.$this->entities($value).'</li>';
			}
		}

		return '<'.$type.$this->attributes($attributes).'>'.$html.'</'.$type.'>';
	}
	
	/**
	 * Generate a definition list.
	 *
	 * @param  array  $list
	 * @param  array  $attributes
	 * @return string
	 */
	public function dl($list, $attributes = array())
	{
		$html = '';

		if (count($list) == 0) return $html;
		
		foreach ($list as $term => $description)
		{
			$html .= '<dt>'.$this->entities($term).'</dt>';
			$html .= '<dd>'.$this->entities($description).'</dd>';
		}
		
		return '<dl'.$this->attributes($attributes).'>'.$html.'</dl>';
	}

	/**
	 * Build a list of HTML attributes from an array
	 * 
	 * @param  array $attributes 
	 * @return string
	 */
	public function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value)
		{
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will convert HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) $key = $value;

			if ( ! is_null($value))
			{
				$html[] = $key.'="'.$this->entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	* Obfuscate a string to prevent spam-bots from sniffing it.
	*
	* @param string $value
	* @return string
	*/
	/**
	 * Obfuscate a string to prevent spam-bots from sniffing it
	 * 
	 * @param  string $value 
	 * @return string
	 */
	protected function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter)
		{
			// To properly obfuscate the value, we will randomly convert each
			// letter to its entity or hexadecimal representation, keeping a
			// bot from sniffing the randomly obfuscated letters.
			switch (rand(1, 3))
			{
				case 1:
				$safe .= '&#'.ord($letter).';';
				break;

				case 2:
				$safe .= '&#x'.dechex(ord($letter)).';';
				break;

				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

}