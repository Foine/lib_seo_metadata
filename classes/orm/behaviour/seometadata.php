<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Lib\SEO\Metadata;

use \Nos\Orm_Behaviour;
use \Nos\I18n;

class Orm_Behaviour_SeoMetadata extends Orm_Behaviour
{
    public static function _init()
    {
        I18n::current_dictionary(array('lib_seo_metadata::common', 'nos::orm', 'nos::common'));
    }

    /**
     * fields => array(
     *      seo_meta_noindex
     *      seo_meta_title
     *      seo_meta_description
     *      seo_meta_keywords
     * ),
     * automatic_optimization_callback => array(
     *      title
     *      description
     *      keywords
     * )
     */
    protected $_properties = array();

    protected $_fields_properties = array(
        'seo_meta_title' => array(
            'label' => 'SEO title:',
            'form' => array(
                'type' => 'text',
            ),
        ),
        'seo_meta_description' => array(
            'label' => 'Description:',
            'form' => array(
                'type' => 'textarea',
                'rows' => 6,
            ),
        ),
        'seo_meta_keywords' => array(
            'label' => 'Keywords:',
            'form' => array(
                'type' => 'textarea',
                'rows' => 3,
            ),
        ),
        'seo_meta_noindex' => array(
            'label' => "Don’t index on search engines",
            'form' => array(
                'type' => 'checkbox',
                'value' => '1',
                'empty' => '0',
            ),
        ),
    );

    public function __construct($class)
    {
        parent::__construct($class);

        //Retrieve custom field properties for CRUD display.
        $this->_fields_properties = \Arr::merge(
            $this->_fields_properties,
            \Arr::get($this->_properties, 'fields_properties', array())
        );
        \Arr::delete($this->_properties, 'fields_properties');

        //Set default options to avoid warnings
        $this->_properties = \Arr::merge(
            array(
                'automatic_optimization_callback' => array(
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ),
            ),
            $this->_properties
        );
    }

    /**
     * Returns whether the item is currently published or not
     *
     * @return string
     */
    public function setSeoMetadata(\Nos\Orm\Model $item)
    {
        if (isset($this->_properties['fields']['seo_meta_title']) && $item->{$this->_properties['fields']['seo_meta_title']}) {
            \Nos\Nos::main_controller()->setTitle($item->{$this->_properties['fields']['seo_meta_title']});
        } else if (isset($this->_properties['automatic_optimization_callback']['title'])) {
            $value = $this->getValueFromFunction($this->_properties['automatic_optimization_callback']['title'], $item);
            if ($value !== false) {
                \Nos\Nos::main_controller()->setTitle($value);
            }
        }

        if (isset($this->_properties['fields']['seo_meta_keywords']) && $item->{$this->_properties['fields']['seo_meta_keywords']}) {
            \Nos\Nos::main_controller()->setMetaKeywords($item->{$this->_properties['fields']['seo_meta_keywords']});
        } else if (isset($this->_properties['automatic_optimization_callback']['keywords'])) {
            $value = $this->getValueFromFunction($this->_properties['automatic_optimization_callback']['keywords'], $item);
            if ($value !== false) {
                \Nos\Nos::main_controller()->setMetaKeywords($value);
            }
        }

        if (isset($this->_properties['fields']['seo_meta_description']) && $item->{$this->_properties['fields']['seo_meta_description']}) {
            \Nos\Nos::main_controller()->setMetaDescription($item->{$this->_properties['fields']['seo_meta_description']});
        } else if (isset($this->_properties['automatic_optimization_callback']['description'])) {
            $value = $this->getValueFromFunction($this->_properties['automatic_optimization_callback']['description'], $item);
            if ($value !== false) {
                \Nos\Nos::main_controller()->setMetaDescription($value);
            }
        }

        if (isset($this->_properties['fields']['seo_meta_noindex']) && $item->{$this->_properties['fields']['seo_meta_noindex']}) {
            if ($value !== false) {
                \Nos\Nos::main_controller()->setMetaRobots('noindex');
            }
        }
    }

    protected function getValueFromFunction($function, \Nos\Orm\Model $item)
    {
        $value = false;
        if (is_string($function) && method_exists($item, $function)) {
            $value = $item->{$function}();
        } else if (is_callable($function)) {
            $value = call_user_func_array($function, array('item' => $item));
        }

        return $value;
    }

    public function crudConfig(&$config, $crud)
    {
        if (!is_array(\Arr::get($this->_properties,'fields'))) {
            \Log::info(__('"fields" property on behaviour SeoMetadata should be an array.'));
            return;
        }
        $fields = array();
        foreach ($this->_properties['fields'] as $field_property_name => $field_name) {
            if (!$field_name) continue;
            //Line to get translation for label.
            $this->_fields_properties[$field_property_name]['label'] = __($this->_fields_properties[$field_property_name]['label']);
            $fields[$field_name] = $this->_fields_properties[$field_property_name];
        }

        if (empty($fields)) return;

        $menu = array_keys($fields);
        $config['fields'] = \Arr::merge($fields, $config['fields']);

        foreach (array('layout', 'layout_insert', 'layout_update') as $layout_name) {
            if (!empty($config[$layout_name])) {
                foreach ($config[$layout_name] as $name => $layout) {
                    if (isset($layout['view']) && in_array($layout['view'], array('nos::form/layout_standard', 'form/layout_standard'))) {
                        if (!isset($config[$layout_name][$name]['params'])) {
                            $config[$layout_name][$name]['params'] = array();
                        }
                        $is_extended = false;
                        if (!empty($config[$layout_name][$name]['params']['menu']) && is_array($config[$layout_name][$name]['params']['menu'])) {
                            //get the last key to reinsert with it
                            end($config[$layout_name][$name]['params']['menu']);
                            $key = key($config[$layout_name][$name]['params']['menu']);
                            $last_menu = array_pop($config[$layout_name][$name]['params']['menu']);
                            if (is_array($last_menu) && !empty($last_menu['view'])) {
                                $is_extended = true; //prevent from inserting some unused configuration
                                if (!empty($last_menu['params']) && isset($last_menu['params']['accordions'])) {
                                    //if the menu is using accordions
                                    $last_menu['params']['accordions']['seo_fields'] = array(
                                        'title' => __('SEO'),
                                        'fields' => $menu,
                                    );
                                }
                            }
                            //does not modify config wether the extended config has been added or not
                            $config[$layout_name][$name]['params']['menu'][$key] = $last_menu;
                        }
                        if (!$is_extended) {
                            $config[$layout_name][$name]['params']['menu'][__('SEO')] = $menu;
                        }
                        break;
                    }
                }
            }
        }
    }
}
