<?php
/**
 * @package   yii2-validators
 * @author    Milos Radojevic <crnimilos@gmail.com>
 * @copyright Copyright &copy; Milos Radojevic, 2015-2016
 * @version   1.0.0
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
  
  public function init() {
    
    if ($this->notString===NULL) {
      $this->notString = \Yii::t('bitdevelopment', 'Only text is allowed.');
    }
    
    if ($this->maxWordsExceeded===NULL) {
      $this->maxWordsExceeded = \Yii::t('bitdevelopment', 'Maximum number of words are exceeded.');
    }
    
    if ($this->minWordsRequired===NULL) {
      $this->minWordsRequired = \Yii::t('bitdevelopment', 'Minimum :min words are required.',  [ 'min'=>  $this->min ]);
    }
    
    parent::init();    
  }

  public function validateAttribute($model, $attribute) {
      
      $value=$model->$attribute;
      
      if (!is_string($value)) {
        $this->addError($model, $attribute, $this->notString);
        
        return;
      }
      if ($this->max===NULL && $this->min===NULL) {
        throw new Exception(\Yii::t('bitdevelopment', 'You have to define atleast min or max option!'), 500);
      }
      
      $this->wCount = str_word_count($value, 0, $this->charlist);
      
      if ($this->max!==NULL && $this->wCount>$this->max) {
        $this->addError($model, $attribute, $this->maxWordsExceeded);
                
        return;
      }
      
      if ($this->min!==NULL && $this->wCount<$this->min) {
        $this->addError($model, $attribute, $this->minWordsRequired);
      }
  }
}
