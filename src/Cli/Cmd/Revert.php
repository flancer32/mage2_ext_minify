<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Flancer32\Minify\Cli\Cmd;

/**
 * Revert minified JS/CSS files in './pub/static/' folder.
 */
class Revert
    extends \Flancer32\Minify\Cli\Cmd\Base
{
    const DESC = "Revert minified JS/CSS files in './pub/static/' folder.";
    const NAME = 'fl32:minify:revert';


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
        $revertedJs = $this->process($files[self::A_JS], $output);
        $revertedCss = $this->process($files[self::A_CSS], $output);
        $output->writeln("Total $totalJs JS and $totalCss CSS files are found in './pub/static/' folder.");
        $output->writeln("Total $revertedJs JS and $revertedCss CSS files are reverted.");
        $output->writeln("Don't forget reset permissions for the files.");
    }

    protected function process($files, \Symfony\Component\Console\Output\OutputInterface $out)
    {
        $result = 0;
        foreach ($files as $file) {
            $bakFile = $file . self::EXT_BAK;
            if (!file_exists($bakFile)) {
                $out->writeln("There is no backup for '$file'. Revert is skipped");
                continue;
            }
            unlink($file);
            rename($bakFile, $file);
            $out->writeln("File '$file' is reverted.");
            $result++;
        }
        return $result;
    }
}