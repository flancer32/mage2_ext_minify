<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Flancer32\Minify\Cli\Cmd;

/**
 * Base class for JS/CSS files minification.
 */
class Base
    extends \Symfony\Component\Console\Command\Command
{
    const A_CSS = '.css';
    const A_JS = '.js';
    const EXT_BAK = '.not_minified';

    /** @var  \Magento\Framework\App\Filesystem\DirectoryList */
    protected $dirList;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $dirList
    )
    {
        $this->dirList = $dirList;
        /* props initialization should be above parent constructor cause $this->configure() will be called inside */
        parent::__construct();
    }

    /**
     * Get JS & CSS files from './pub/static/'.
     *
     * @return array [self::A_JS => [], self::A_CSS => []]
     */
    protected function getAllStaticFiles()
    {
        $dir = $this->dirList->getPath('static');
        $js = [];
        $css = [];
        $this->getDirContents($dir, $js, $css);
        $result = [self::A_JS => $js, self::A_CSS => $css];
        return $result;
    }

    /**
     * http://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
     *
     * @param $dir
     * @param array $js
     */
    protected function getDirContents($dir, &$js, &$css)
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                if (substr($path, -3) == self::A_JS) $js[] = $path;
                if (substr($path, -4) == self::A_CSS) $css[] = $path;
            } else {
                if ($value != "." && $value != ".." && !is_link($path)) {
                    $this->getDirContents($path, $js, $css);
                }
            }
        }
    }

}