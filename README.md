lib_seo_metadata
===========

A behaviour which allow you to easily add the common seo fields to any item that you want.

Licensed under [MIT License](http://opensource.org/licenses/MIT)

Current version: 1.0

**Get started**

* Install the application
* Add the behaviour in the model that you want to have seo fields like this :
    protected static $_behaviours = array(
        'Lib\SEO\Metadata\Orm_Behaviour_SeoMetadata' => array(
            'fields' => array(
                'seo_meta_noindex' => 'your_no_index_field',
                'seo_meta_title' => 'your_meta_title_field',
                'seo_meta_description' => 'your_meta_description_field',
                'seo_meta_keywords' => 'your_meta_keywords_field',
            ),
        ),
    );

* Do not forget to add your fields into your model properties and in your database. All fields are text or varchar, except of the no index field that is a boolean.
* This app normaly automacally insert your seo metadata in Front Office if your Front Controller use the "setItemDisplayed" system provide by the Front Controller of NOS.
* If you want to use them manually, just call `$item->setSeoMetadata();` into your front action, and the behaviour will do the job !

Thank you for using this application !