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
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\Filesystem\DirectoryList $dirList
    ) {
        $this->manObj = $manObj;
        $this->dirList = $dirList;
        /* props initialization should be above parent constructor cause $this->configure() will be called inside */
        parent::__construct();
    }

    /**
     * Sets area code to start a adminhtml session and configure Object Manager.
     */
    protected function configure()
    {
        parent::configure();
        /* Magento related config (Object Manager) */
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->manObj->get(\Magento\Framework\App\State::class);
        try {
            /* area code should be set only once */
            $appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_FRONTEND;
            $appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
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