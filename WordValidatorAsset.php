<?php
/**
 * @package   yii2-validators
 * @subpackage yii2-validators\word-validator
 * @author    Milos Radojevic <crnimilos@gmail.com>
 * @copyright Copyright &copy; Milos Radojevic, 2015-2017
 * @version   1.0.1 (stable)
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace bitdevelopment\yii2validators;

/**
 * WordValidator Asset Bundle
 *
 * @author Milos Radojevic
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