<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @package yii2-widgets
 * @subpackage yii2-widget-datetimepicker
 * @version 1.4.1
 */

namespace bitdevelopment\yii2validators;

/**
 * Asset bundle for DateTimePicker Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class WordValidatorAsset extends \yii\web\AssetBundle
{
    public $js = [
        'wordvalidator.js',
    ];
    
    public function init()
    {        
        $this->sourcePath = __DIR__ . '/assets';;
        parent::init();
    }
}
  