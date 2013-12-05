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
    }

    /**
     * Returns whether the item is currently published or not
     *
     * @return string
     */
    public function setSeoMetadata(\Nos\Orm\Model $item)
    {
        if ($this->_properties['fields']['seo_meta_title'] && $item->{$this->_properties['fields']['seo_meta_title']}) {
            \Nos\Nos::main_controller()->setTitle($item->{$this->_properties['fields']['seo_meta_title']});
        } else if (method_exists($item, $this->_properties['automatic_optimization_callback']['title'])) {
            \Nos\Nos::main_controller()->setTitle($item->{$this->_properties['automatic_optimization_callback']['title']}());
        }

        if ($this->_properties['fields']['seo_meta_keywords'] && $item->{$this->_properties['fields']['seo_meta_keywords']}) {
            \Nos\Nos::main_controller()->setMetaKeywords($item->{$this->_properties['fields']['seo_meta_keywords']});
        } else if (method_exists($item, $this->_properties['automatic_optimization_callback']['keywords'])) {
            \Nos\Nos::main_controller()->setMetaKeywords($item->{$this->_properties['automatic_optimization_callback']['keywords']}());
        }

        if ($this->_properties['fields']['seo_meta_description'] && $item->{$this->_properties['fields']['seo_meta_description']}) {
            \Nos\Nos::main_controller()->setMetaDescription($item->{$this->_properties['fields']['seo_meta_description']});
        } else if (method_exists($item, $this->_properties['automatic_optimization_callback']['description'])) {
            \Nos\Nos::main_controller()->setMetaDescription($item->{$this->_properties['automatic_optimization_callback']['description']}());
        }

        if ($this->_properties['fields']['seo_meta_noindex'] && $item->{$this->_properties['fields']['seo_meta_noindex']}) {
            \Nos\Nos::main_controller()->setMetaRobots('noindex');
        }
    }
    public function crudConfig(&$config, $crud)
    {
        if (!is_array($this->_properties['fields'])) {
            $this->_properties['fields'] = array();
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
                        $config[$layout_name][$name]['params']['menu'][__('SEO')] = $menu;
                        break;
                    }
                }
            }
        }
    }
}
