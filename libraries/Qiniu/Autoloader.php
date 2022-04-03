<?php
/**
 *
 * Autoloader.php
 * @author  : Skiychan <dev@skiy.net>
 * @link    : https://www.zzzzy.com
 * @created : 5/7/16
 * @modified:
 * @version : 0.0.1
 * @doc     : https://www.zzzzy.com/201605094039.html
 */

namespace Qiniu;

class Autoloader {
    private $directory;
    private $prefix;
    private $prefixLength;

    public function __construct($baseDirectory = __DIR__)
    {
        $this->directory = $baseDirectory;
        $this->prefix = __NAMESPACE__.'\\';
        $this->prefixLength = strlen($this->prefix);
    }

    public function autoload($class)
    {
        if (0 === strpos($class, $this->prefix)) {
            $parts = explode('\\', substr($class, $this->prefixLength));
            $filepath = $this->directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

            if (is_file($filepath)) {
                require $filepath;
            }
        }
    }

    public static function register()
    {
        spl_autoload_register(array(new self(), 'autoload'));
    }
}

