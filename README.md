# mage2_ext_minify
Minify static JS/CSS in Magento 2


## Overview

There are some troubles with JS/CSS minification in Magento 2.
 
This extension just minifies all JS and CSS files in `./pub/static/**` folder using 
[matthiasmullie/minify](https://github.com/matthiasmullie/minify) module. All minified files stay on the places 
of the original files. All original files are backed up with extension `*.not_minified`.

Before minification:

![](./docs/img/before.png)

After minification:

![](./docs/img/after.png)

CAUTION: JS and CSS files in `./pub/static/**` folder are links to the files in `./vendor/**` folder, 
these files will be minified in result. Use this module in case you can re-deploy original files.


## Usage

Perform compilation of the static content before minification:

    $ ./bin/magento setup:static-content:deploy
    
or switch to production mode:

    $ ./bin/magento deploy:mode:set production
    
then run minification:

    $ ./bin/magento fl32:minify:make
    ...
    File '/home/alex/work/prj/sample_mage2_module/work/pub/static/frontend/Magento/luma/en_US/tiny_mce/themes/simple/skins/o2k7/ui.css' is minified.
    Total 2263 JS and 225 CSS files are found in './pub/static/' folder.
    Total 1985 JS and 212 CSS files are minified.
    Don't forget reset permissions for the files.


To revert minification:

    $ ./bin/magento fl32:minify:revert
    ...
    File '/home/alex/work/prj/sample_mage2_module/work/pub/static/frontend/Magento/luma/en_US/tiny_mce/themes/simple/skins/o2k7/ui.css' is reverted.
    Total 2263 JS and 225 CSS files are found in './pub/static/' folder.
    Total 2263 JS and 225 CSS files are reverted.
    Don't forget reset permissions for the files.



## Installation

    $ composer require flancer32/mage2_ext_minify