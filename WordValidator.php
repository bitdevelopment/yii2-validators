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
 * Validator that checks number of words, minimum and maximum of words validation
 *
 * @author Milos Radojevic
 */
class WordValidator extends \yii\validators\Validator {
  
    /**
     *
     * @var integer Maximum number of words
     */
    public $max = NULL;
    /**
     *
     * @var integer Minimum number of words
     */
    public $min = NULL;

    /**
     *
     * @var string A list of additional characters which will be considered as 'word'
     */
    public $charlist = NULL;
    /**
     *
     * @var string Error message if not passed string. Default "Only text is allowed."
     */
    public $notString;
    /**
     *
     * @var string Error message if maximum number of words are exceeded. Default "Maximum number of words are exceeded."
     */
    public $maxWordsExceeded;
    /**
     *
     * @var string Error message if maximum number of words are exceeded. Default "Minimum :min words are required."
     */
    public $minWordsRequired;
    /**
     * @var int Word count
     */
    public $wCount; 
    /**
     *
     * @var boolean Sometimes we need to show error during typing, so they will be informed how much words are left.
     */
    public $validateOnType = true;
    /**
     *
     * @var boolean In case of WYSIWIG editor we sometimes need to strip tags and count words without html tags, if set to true,
     * it will ignore HTML tags in word count.
     */
    public $stripTags = false;
    
    
    public function init() {
    
        if ($this->notString===NULL) {
            $this->notString = 'Only text is allowed.';
        }
    
        if ($this->maxWordsExceeded===NULL) {
            $this->maxWordsExceeded = 'Maximum number of words are exceeded.';
        }
    
        if ($this->minWordsRequired===NULL) {
            $this->minWordsRequired = 'Minimum {min} words are required. {words} words more needed.';
        }
    
        parent::init();    
    } 
    

    protected function validateValue($value) {
            
        if (!is_string($value)) {
            return [\Yii::t('bitdevelopment', $this->notString),[]];
        }
        
        if ($this->max===NULL && $this->min===NULL) {
            throw new \yii\base\InvalidConfigException('You have to define atleast min or max option! Follow README.md for more information.');
        }
      
        if ($this->stripTags) {
            $value = strip_tags($value);
        } 
        
        $this->wCount = str_word_count($value, 0, $this->charlist);
      
        if ($this->max!==NULL && $this->wCount>$this->max) {
            $message = \Yii::t('bitdevelopment', $this->maxWordsExceeded, [
                    'min'=>  $this->min, 
                    'max'=> $this->max, 
                    'words'=> $this->wCount - $this->max 
            ]);
            
            return [$message,[]];
        }
      
        if ($this->min!==NULL && $this->wCount<$this->min) {
            
            $message = \Yii::t('bitdevelopment', $this->minWordsRequired,  [
                    'min'=>  $this->min, 
                    'max'=> $this->max, 
                    'words'=> $this->min-$this->wCount
            ]);
            
            return [$message,[]];
        }
    }

        
    public function clientValidateAttribute($model, $attribute, $view) {
        WordValidatorAsset::register($view);
        return 'yii.validation.wordvalidator(value, messages, ' . json_encode($this) . ');';
    }
}