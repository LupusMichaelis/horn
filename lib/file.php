<?php

define('KILO', 1024) ;
define('MEGA', KILO * 1024) ;
define('SIZE_LIMIT', 10 * MEGA) ;

/** \todo	Files have to know about filesystem issue. So, when a file is contained in
 *			another file, he'll try to write himself to filesystem. That's stupid.
 *	\bug	Copying ODT template doesn(t work, the original file is altered instead.
 */

/**
 */
abstract
class replacer
{
	const		PATTERN = '%s' ;

	public		function __construct($all = true)
	{
		$this->set_pattern_format(static::PATTERN) ;
		$this->_replace_all = $all ;
	}

	public		function set_translator(translator $translator)
	{
		$this->_translator = $translator ;
	}

	public		function set_pattern_format($format)
	{
		$this->_pattern_format = $format ;
	}

	abstract
	public		function __invoke(& $subject, $pattern, $value) ;

	protected	$_replace_all = false ;
	protected	$_translator = null ;
	protected	$_pattern_format = null ;
}

class replacer_plain
	extends replacer
{
	const		PATTERN = '{%s}' ;
	public		function __invoke(& $subject, $pattern, $value)
	{
		$pattern = sprintf(static::PATTERN, $pattern) ;

		$subject = str_replace($pattern, $value, $subject, $count) ;
		return $count ;
	}
}

class replacer_regex
	extends replacer
{
	const		PATTERN = 'µ%sµ' ;
	public		function __invoke(& $subject, $pattern, $value)
	{
		/// \todo	implement a fallback to avoid delimiter collision
		if(strpos($pattern, 'µ') !== false)
			throw new exception('µ delimiter was found in pattern...') ;

		$pattern = sprintf(self::PATTERN, $pattern) ;

		$subject = preg_replace($pattern, $value, $subject, -1, $count) ;
		return $count ;
	}
}

/**
 *
 *	\todo	fallback mecanism on errors instead of throwing an exception ?
 */
class translator
{
	public		function __construct(a_file $template)
	{
		$this->_template = $template ;
		$this->set_replacer() ;
	}

	public		function process()
	{
		$target = clone $this->_template ;

		foreach($this->_translators as $name => $value)
			$target->replace($name, $value, $this->_replacer) ;

		return $target ;
	}

	public		function set_template(a_file $template)
	{
		$this->_template = $template ;
	}

	public		function set_replacer(replacer $replacer = null)
	{
		if(is_null($replacer))
			$replacer = new replacer_plain ;

		$this->_replacer = $replacer ;

		return $this ;
	}

	public		function add_tanslators(& $pairs)
	{
		$this->_translators = array_merge($this->_translators, $pairs) ;
	}

	/** prototype for target output */
	protected	$_template = null ;

	protected	$_translators = array() ;
}

interface i_file
{
	static
	function create($filename) ;
	static
	function load($filename) ;

	function open() ;
	function write() ;
	function close() ;
	function replace($pattern, $value, replacer $replacer) ;
}

/**
 *	\todo	Lazy mode
 *	\todo	Filesystem operation are global, relatives them to a directory object.
 */
abstract
class a_file
	implements i_file
{
	const		SIZE_LIMIT = SIZE_LIMIT ;

	protected	function __construct($name = null)
	{
		$this->set_name($name) ;
	}

	public		function __destruct()
	{
		$this->close() ;
	}

	abstract
	protected	function _open() ;

	abstract
	protected	function _write() ;

	abstract
	protected	function _load() ;

	public		function open()
	{
		if(is_null($this->_name))
			$this->_throw_anonymous() ;
		$this->_open() ;

		return $this ;
	}

	public		function write()
	{
		if(is_null($this->_name))
			$this->_throw_anonymous() ;
		$this->_write() ;

		return $this ;
	}

	public		function close()
	{
		$this->_close() ;
		$this->_is_loaded = false ;

		return $this ;
	}

	/*
	abstract
	public		function assign|copy(a_file & $file) ;
	*/

	abstract
	public		function replace($pattern, $value, replacer $replacer) ;

	static
	public		function create($filename)
	{
		$new = new static($filename) ;
		return $new ;
	}

	static
	public		function load($filename)
	{
		$new = static::create($filename) ;
		$new->_load() ;

		$new->_is_loaded = true ;

		return $new ;
	}

	/**
	 */
	static
	public		function copy(a_file $copied, $filename)
	{
		if(copy($copied->_name, $filename))
			$new = static::create($filename) ;
		else
			$copied->_throw_cant_copy($filename) ;

		return $new ;
	}

	/*
	public		function write_in(a_file & $file)
	{
		$file->assign($this) ;
		return $this ;
	}
	*/

	public		function reopen()
	{
		$this->write() ;
		$this->close() ;
		$this->open() ;
	}

	public		function set_name($name)
	{
		$this->_name = $name ;
		return $this ;
	}

	public		function get_name()
	{
		return $this->_name ;
	}

	/// \todo	move _throw to object_base
	protected	function _throw($fmt)
	{
		$args = & func_get_args() ;
		$error = & call_user_func_array('sprintf', $args) ;

		throw new exception($error) ;
	}

	protected	function _throw_file_not_exists()
	{
		$this->_throw('File \'%s\' doesn\'t exist', $this->_name) ;
	}

	protected	$_is_loaded = null ;
	protected	$_name = null ;

	protected	$_parent = null ; ///< \todo
}

class file_factory
{
	static
	public		function register($class_name)
	{
		$mime = $class_name::MIME_TYPE ;
		self::$_registry[$mime] = $class_name ;
	}

	static
	public		function create($mime)
	{
		$class_name = self::$_registry[$mime] ;
		$new = $mime::create() ;

		return $new ;
	}

	static
	public		function create_from_file($mime, $file_name)
	{
		$class_name = self::$_registry[$mime] ;
		$new = $mime::load($file_name) ;

		return $new ;
	}

	/*
	static
	public		function create_from_binary($mime, $file_content)
	{
		$class_name = self::$_registry[$mime] ;
		$new = $mime::create() ;
		$new->set_content($file_content) ;

		return $new ;
	}
	*/

	static
	protected	$_registry = array() ;
}

/**
 *	\todo	Do a memory aware implementation (for big files).
 */
class file_text
	implements i_file
{
	const		MIME_TYPE = 'text/plain' ;

	protected	function _write()
	{
		file_put_contents($this->_name, $this->_text) ;
	}

	protected	function _open()
	{
	}

	protected	function _load()
	{
		$this->_text = file_get_contents($this->_name) ;
		$this->_encoding = mb_detect_encoding($this->_text) ;
	}

	protected	function _close()
	{
		$this->_text = null ;
		$this->_encoding = null ;
	}

	public		function replace($pattern, $value, replacer $replacer)
	{
		if(is_null($replacer))
			$replacer = new replacer_plain ;

		$count = $replacer($this->_text, $pattern, $value) ;
		return $count ;
	}

	protected	$_text = null ;
	protected	$_encoding = null ;
}

/*
abstract
class file_image
	extends a_file
{
}

class file_jpeg
	extends file_image
{
}

class file_pdf
	extends a_file
{
}
*/

class composed_file
	extends a_file
{
	public		function get_file(a_path $path)
	{
		return $this->_files[$path] ;
	}

	public		function set_file($path, aFile $file)
	{
		$this->_files[$path]->assign($file) ;
	}

	protected	$_files = array() ;
}

class file_zip
	extends composed_file
{
	const		MIME_TYPE = 'application/zip' ;

	protected	function __construct($name)
	{
		parent::__construct($name) ;
		$this->_init() ;
	}

	public		function addFile($path, a_file & $content = null)
	{
		if(is_null($content))
			$this->_content->addFile($path) ;
		else
			$this->_content->addFromString($path, $content) ;
	}

	public		function addString($path, & $content = null)
	{
		if(is_null($content))
			$this->_content->addFile($path) ;
		else
			$this->_content->addFromString($path, $content) ;
	}

	protected	function _init()
	{
		$this->_content = new ziparchive ;
	}

	protected	$_content = array() ;
	/// \todo	implements a directory container instead of an array
	protected	$_files = array() ;
}

class file_xml
	extends a_file
{
	protected	$_content ;
}

class file_odt
	extends composed_file
{
	const		MIME_TYPE = 'application/vnd.oasis.opendocument.text' ;
	const		CONTENT_FILENAME = 'content.xml' ;

	protected	function __construct($name)
	{
		parent::__construct($name) ;
		$this->_init() ;
	}

	protected	function _init()
	{
		$this->_archive = new file_zip ;
		$this->_content = new domdocument('1.0', 'UTF-8') ;
	}

	public		function __destruct()
	{
		if($this->_archive instanceof ziparchive)
			@ /* can't check if archive is actually open and valid, so mute */
				$this->_archive->close() ;
	}

	public		function __clone()
	{
		$new = new static($this->_name) ;
		$new->_init() ;

		return $new ;
	}

	protected	function _write()
	{
		$content = & $this->_content->saveXML() ;
		$this->_archive->add(self::CONTENT_FILENAME, $content) ;
	}

	public		function replace($pattern, $value, replacer & $replacer)
	{
		if(strcasecmp(mb_detect_encoding($value), 'utf-8'))
			$value = utf8_encode($value) ;

		$xpath = new domxpath($this->_content) ;
		$textNodeList = $xpath->query('//text()') ;

		$count = 0 ;
		foreach($textNodeList as $text_node)
		{
			$text = $text_node->wholeText ;
			$count += $replacer($text, $pattern, $value) ;
			$text_node->replaceData(0, strlen($text_node->wholeText), $text) ;
		}

		return $count ;
	}

	protected	function _open()
	{
	}

	protected	function _load()
	{
		$this->_load_zip() ;
		$this->_load_content() ;
	}

	protected	function _load_zip()
	{
		file_exists($this->_name)
			or $this->_throw_file_not_exists() ;

		$stats = stat($this->_name) ;
		if($stats === false)
			$this->_throw_file_not_exists() ;

		if($stats['size'] > self::SIZE_LIMIT)
			$this->_throw_too_big($this->_name, $stats['size']) ;

		$this->_archive->open($this->_name, ZIPARCHIVE::CREATE)
			or $this->_throw_open_zip($this->_name) ;
	}

	protected	function _load_content()
	{
		$stats = $this->_archive->statname(self::CONTENT_FILENAME) ;
		if($stats['size'] > self::SIZE_LIMIT)
			$this->_throw_too_big($this->_name, $stats['size']) ;

		$content = & $this->_archive->getfromname(self::CONTENT_FILENAME) ;
		if($content === false)
			$this->_throw('Can\'t extract \'%s\' file from \'%s\' (%d).'
					, self::CONTENT_FILENAME
					, $this->_name
					, $content
					) ;

		$this->_content->loadXML($content) ;
		@ /* No DTD, so mute error */ $this->_content->validate() ;
	}

	protected	function _throw($fmt)
	{
		$args = & func_get_args() ;
		$error = & call_user_func_array('sprintf', $args) ;

		throw new exception($error) ;
	}

	protected	function _throw_too_big($filename, $size, $limit = self::SIZE_LIMIT)
	{
		$this->_throw('File \'%s\' too big (%d). Exceed (%d)'
				, $filename, $size, self::SIZE_LIMIT) ;
	}

	protected	function _throw_open_zip($filename, $code = null)
	{
		$errors = array
			( ZIPARCHIVE::ER_EXISTS => 'File exists yet.'
			, ZIPARCHIVE::ER_INCONS => '?'
			, ZIPARCHIVE::ER_INVAL => '?'
			, ZIPARCHIVE::ER_MEMORY => 'Not enough memory'
			, ZIPARCHIVE::ER_NOENT => '?'
			, ZIPARCHIVE::ER_NOZIP => '?'
			, ZIPARCHIVE::ER_OPEN => '?'
			, ZIPARCHIVE::ER_READ => '?'
			, ZIPARCHIVE::ER_SEEK => '?'
			) ;

		$this->_throw('File \'%s\' too big (%d). Exceed (%d)'
				, $filename, $code, self::SIZE_LIMIT) ;
	}

	protected $_archive = null ;
	protected $_content = null ;
}

file_factory::register('file_text') ;
file_factory::register('file_odt') ;

