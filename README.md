Active Record Json Column Extension
===================================
Active Record Json Column Ext

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist jberall/yii2-active-record-json-column-ext "*"
```

or add

```
"jberall/yii2-active-record-json-column-ext": "*"
```

to the require section of your `composer.json` file.


Usage
-----

The extension has basic attributes, array_objects override and array_objects set.
If you want to have dimensions just do the 4 attributes as a property like, width, height, length and UOM.
array objects - like Emails and Phone Numbers will automatically overwrite the loading.
