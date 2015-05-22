lib_seo_metadata
===========

A behaviour which allow you to easily add the common seo fields to any item that you want.

Licensed under [MIT License](http://opensource.org/licenses/MIT)

Current version: 1.0

**Get started**

* Install the application
* Add the behaviour in the model that you want to have seo fields, like this :

```
    protected static $_behaviours = array(
        'Lib\SEO\Metadata\Orm_Behaviour_SeoMetadata' => array(
            'fields' => array(
                'seo_meta_noindex'      => 'model__seo_noindex',
                'seo_meta_title'        => 'model__seo_title',
                'seo_meta_description'  => 'model__seo_description',
                'seo_meta_keywords'     => 'model__seo_keywords',
            ),
        ),
    );
```

* Add the fields in the model properties, like this :

```
    'properties' => array(
        'model__seo_noindex' => array(
            'default' => 0,
            'data_type' => 'boolean',
            'null' => false,
        ),
        'model__seo_title' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
        ),
        'model__seo_description' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
        ),
        'model__seo_keywords' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
        ),
    ),
```

* Add the fields in the model table (don't forget to create a migration if necessary), like this for a MySQL database :

```
    ALTER TABLE `model`
        ADD `model__seo_noindex` TINYINT( 1 ) NOT NULL DEFAULT '0',
        ADD `model__seo_title` TEXT NULL DEFAULT NULL ,
        ADD `model__seo_description` TEXT NULL DEFAULT NULL ,
        ADD `model__seo_keywords` TEXT NULL DEFAULT NULL
    ;
```

* This app automatically insert your seo metadata in Front Office if your Front Controller use the "setItemDisplayed" method provided by the Front Controller of NOS.
* If you want to use them manually, just call `$item->setSeoMetadata();` into your front action, and the behaviour will do the job !
* This behaviour can also allow you to set automatic seo optimization. it will be documented soon.

Thank you for using this application !
