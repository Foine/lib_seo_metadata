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

**Another method : automatic optimization callback**

This behaviour can also allow you to set automatic seo optimization. This option can be used with or without the classical SEO fields.
If fields are used and filled, automatic optimization will do nothing. But if there is no field or if the field is empty, automatic optimization will be used.
Here is 2 configuration samples :

```
    protected static $_behaviours = array(
        'Lib\SEO\Metadata\Orm_Behaviour_SeoMetadata' => array(
            'automatic_optimization_callback' => array(
                'title'         => 'seo_title',
                'description'   => 'seo_description',
                'keywords'      => 'seo_keywords',
            ),
        ),
    );

```
With this one, the behaviour will try to call public methods `seo_title`, `seo_description` and `seo_keywords` into the current Model. Those methods must return strings that will be used into you seo metadata.
The second example do the same thing but use anonymous function that take the current model as argument.

```

    public static function _init()
    {
        self::$_behaviours['Lib\SEO\Metadata\Orm_Behaviour_SeoMetadata'] = array(
            'automatic_optimization_callback' => array(
                'title'         => function($item) {
                        return __('My enhanced title for ').$item->title_item();
                    },
                'description'   => function($item) {
                       return __('My enhanced description for ').$item->title_item();
                   },
                'keywords'      => function($item) {
                       return 'keywords, used, for, '.$item->title_item();
                   },
            ),
        );
    }

```

* This app automatically insert your seo metadata in Front Office if your Front Controller use the "setItemDisplayed" method provided by the Front Controller of NOS.
* If you want to use them manually, just call `$item->setSeoMetadata();` into your front action, and the behaviour will do the job !

Thank you for using this application !
