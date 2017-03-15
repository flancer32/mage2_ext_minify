# mage2_ext_minify
Minify static JS/CSS in Magento 2


## Overview

There are some troubles with JS/CSS minification in Magento 2 
([one](http://magento.stackexchange.com/questions/134206/magento-2-how-to-work-with-minified-js-and-css-files),
 [two](http://magento.stackexchange.com/questions/120405/how-to-minify-optimise-javascript-on-magento-2), 
 [three](https://community.magento.com/t5/Hosting-Performance/CSS-and-js-minify-dont-work/td-p/1330), ...).
 
This extension just minifies all JS and CSS files in `./pub/static/**` folder using 
[matthiasmullie/minify](https://github.com/matthiasmullie/minify) module. All minified files stay on the places 
of the original files. All original files are backed up with extension `*.not_minified`.

Sample Magento 2 application with default Luma theme has 60/100 points on Google PageSpeed Insights and
2.4 MB size for it's home page before minification and 69/100 points and 1.5 MB after minification 
([details](./docs/overview.md)).

CAUTION: JS and CSS files in `./pub/static/**` folder are links to the files in `./vendor/**` folder, 
these files will be minified in result. Use this module in case you can re-deploy original files.

## Installation

    $ composer require flancer32/mage2_ext_minify
    $ ./bin/magento setup:upgrade
    
    

## Usage

Perform compilation of the static content before minification:

    $ ./bin/magento setup:static-content:deploy
    
or switch to production mode:

    $ ./bin/magento deploy:mode:set production
    
then run minification:

    $ ./bin/magento fl32:minify:make
    ...
    File '/var/www/vhosts/sample_mage2_module/work/pub/static/frontend/Magento/luma/en_US/tiny_mce/themes/simple/skins/o2k7/ui.css' is minified.
    Total 2266 JS and 225 CSS files are found in './pub/static/' folder.
    Total 2266 JS and 225 CSS files are minified.
    Don't forget reset permissions for the files.


To revert minification:

    $ ./bin/magento fl32:minify:revert
    ...
    File '/var/www/vhosts/sample_mage2_module/work/pub/static/frontend/Magento/luma/en_US/tiny_mce/themes/simple/skins/o2k7/ui.css' is reverted.
    Total 2266 JS and 225 CSS files are found in './pub/static/' folder.
    Total 2266 JS and 225 CSS files are reverted.
    Don't forget reset permissions for the files.

