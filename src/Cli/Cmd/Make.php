<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Flancer32\Minify\Cli\Cmd;

/**
 * Minify JS/CSS files in './pub/static/' folder.
 */
class Make
    extends \Flancer32\Minify\Cli\Cmd\Base
{
    const DESC = "Minify JS/CSS files in './pub/static/' folder.";
    const NAME = 'fl32:minify:make';


    /**
     * Sets area code to start a adminhtml session and configure Object Manager.
     */
    protected function configure()
    {
        parent::configure();
        /* UI related config (Symfony) */
        $this->setName(self::NAME);
        $this->setDescription(self::DESC);
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* perform operation */
        $files = $this->getAllStaticFiles();
        $totalJs = count($files[self::A_JS]);
        $totalCss = count($files[self::A_CSS]);
        $processedJs = $this->process($files[self::A_JS], self::A_JS, $output);
        $processedCss = $this->process($files[self::A_CSS], self::A_CSS, $output);
        $output->writeln("Total $totalJs JS and $totalCss CSS files are found in './pub/static/' folder.");
        $output->writeln("Total $processedJs JS and $processedCss CSS files are minified.");
        $output->writeln("Don't forget reset permissions for the files.");
    }

    protected function process($files, $type, \Symfony\Component\Console\Output\OutputInterface $out)
    {
        $result = 0;
        foreach ($files as $file) {
            $bakFile = $file . self::EXT_BAK;
            if (file_exists($bakFile)) {
                $out->writeln("Minification is skipped for '$file'. There is backup file already (*" . self::EXT_BAK . ").");
                continue;
            }
            rename($file, $bakFile);
            $minifier = null; // exception will be thrown for unknown types
            if ($type == self::A_JS) $minifier = new \MatthiasMullie\Minify\JS($bakFile);
            if ($type == self::A_CSS) $minifier = new \MatthiasMullie\Minify\CSS($bakFile);
            $minifier->minify($file);
            $out->writeln("File '$file' is minified.");
            $result++;
        }
        return $result;
    }

}