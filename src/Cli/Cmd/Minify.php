<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Flancer32\Minify\Cli\Cmd;

use Symfony\Component\Console\Input\InputOption;

/**
 * Minify JS/CSS files in './pub/static/' folder.
 */
class Minify
    extends \Symfony\Component\Console\Command\Command
{
    const A_CSS = '.css';
    const A_JS = '.js';
    const DESC = "Minify JS/CSS files in './pub/static/' folder.";
    const NAME = 'fl32:app:minify';
    const OPT_REVERT = 'revert';
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
        /* UI related config (Symfony) */
        $this->setName(self::NAME);
        $this->setDescription(self::DESC);
        $this->addOption(
            self::OPT_REVERT,
            'r',
            InputOption::VALUE_OPTIONAL,
            'Revert minified files.',
            1
        );
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

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $files = $this->getAllStaticFiles();
        $this->processJs($files[self::A_JS]);
        $this->processCss($files[self::A_CSS]);
        $output->writeln('<info>Command is completed.<info>');
    }

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

    protected function processCss($files)
    {
        foreach ($files as $file) {
            $minifier = new \MatthiasMullie\Minify\CSS($file);
            $minifier->minify($file);
        }
    }

    protected function processJs($files)
    {
        foreach ($files as $file) {
            $minifier = new \MatthiasMullie\Minify\JS($file);
            $minifier->minify($file);
        }
    }
}