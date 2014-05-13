<?php
Event::register('front.parse_wysiwyg', function($params)
{
    $methodVariable = array(\Nos\Nos::main_controller(), 'getItemDisplayed');
    if (is_callable($methodVariable)) {
        $item = \Nos\Nos::main_controller()->getItemDisplayed();
        $behaviour = $item->behaviours('Lib\SEO\Metadata\Orm_Behaviour_SeoMetadata');
        if (!empty($behaviour)) {
            $item->setSeoMetadata();
        }
    }
});