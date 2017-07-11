<?php
/**
 * @package   yii2-validators
 * @subpackage yii2-validators\recaptcha-inputwidget
 * @author    Milos Radojevic <crnimilos@gmail.com>
 * @copyright Copyright &copy; Milos Radojevic, 2015-2017, some parts under copyright of 2014 HimikLab, 
 * @version   1.0.1 (stable)
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace bitdevelopment\yii2validators;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\ArrayHelper;

/**
 * Yii2 Google reCAPTCHA widget.
 *
 *
 * @see https://developers.google.com/recaptcha
 * @author Milos Radojevic
 */
class ReCaptcha extends InputWidget
{
    
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';

    const SIZE_NORMAL = 'normal';
    const SIZE_COMPACT = 'compact';

    /** @var string Your sitekey. */
    public $siteKey;

    /** @var string Your secret. */
    public $secret;

    /** @var string The color theme of the widget. [[THEME_DARK]] (default) or [[THEME_DARK]] */
    public $theme;

    /** @var string The type of CAPTCHA to serve. [[TYPE_IMAGE]] (default) or [[TYPE_AUDIO]] */
    public $type;

    /** @var string The size of the widget. [[SIZE_NORMAL]] (default) or [[SIZE_COMPACT]] */
    public $size;

    /** @var int The tabindex of the widget */
    public $tabindex;

    /** @var string Your JS callback function that's executed when the user submits a successful CAPTCHA response. */
    public $jsCallback;

    /**
     * @var string Your JS callback function that's executed when the recaptcha response expires and the user
     * needs to solve a new CAPTCHA.
     */
    public $jsExpiredCallback;

    /** @var array Additional html widget options, such as `class`. */
    public $widgetOptions = [];

    public function run()
    {
        if (empty($this->siteKey)) {
            $this->siteKey = ArrayHelper::getValue(Yii::$app->params,'reCaptcha.siteKey',NULL);
        }
        if (empty($this->siteKey)) {
            throw new InvalidConfigException("ReCaptcha siteKey is not set. Follow README.md for more information.");
        }
        
        $view = $this->view;
        
        $view->registerJs("var recaptchaLoad = function () {"
                . " [].forEach.call(document.getElementsByClassName('recaptcha'), function (e) {
                        if (e.dataset.loaded !== 'true') {
                            grecaptcha.render(e.id, e.dataset);
                            e.dataset.loaded = true;
                        }
                    });"
                . "}", $view::POS_HEAD,'ReCaptchaLoad');
        
        //Fixes Ajax Loading Trough Pjax
        $headers = Yii::$app->getRequest()->getHeaders();
        
        if ($headers->get('X-Pjax')) {
            $view->registerJs("recaptchaLoad();",$view::POS_READY);
        }

        $this->customFieldPrepare();
        
        $class = ArrayHelper::getValue($this->widgetOptions,'class','');
        
        $this->widgetOptions = array_filter([
            'class'                 => "recaptcha $class",
            'id'                    => $this->getId(),
            'data-callback'         => $this->jsCallback,
            'data-expired-callback' => $this->jsExpiredCallback,
            'data-theme'            => $this->theme, 
            'data-type'             => $this->type,
            'data-size'             => $this->size,
            'data-tabindex'         => $this->tabindex,
            'data-sitekey'          => $this->siteKey
        ]) + $this->widgetOptions;

        echo Html::tag('div', '', $this->widgetOptions);
        
        echo Html::script('',[
                'src'=> '//www.google.com/recaptcha/api.js?onload=recaptchaLoad&render=explicit&hl=' . $this->getLanguageSuffix(), 
                'async' => true, 
                'defer' => true
            ]
        );
    }

    protected function getLanguageSuffix()
    {
        $currentAppLanguage = Yii::$app->language;
        $langsExceptions = ['zh-CN', 'zh-TW', 'zh-TW'];

        if (strpos($currentAppLanguage, '-') === false) {
            return $currentAppLanguage;
        }

        if (in_array($currentAppLanguage, $langsExceptions)) {
            return $currentAppLanguage;
        } else {
            return substr($currentAppLanguage, 0, strpos($currentAppLanguage, '-'));
        }
    }

    protected function customFieldPrepare()
    {
        $view = $this->view;
        if ($this->hasModel()) {
            $inputName = Html::getInputName($this->model, $this->attribute);
            $inputId = Html::getInputId($this->model, $this->attribute);
        } else {
            $inputName = $this->name;
            $inputId = 'recaptcha-' . $this->name;
        }

        $id = $this->getId();
        
        if (empty($this->jsCallback)) {
            $jsCode = "var recaptchaCallback_$id = function(response){jQuery('#{$inputId}').val(response);};";
        } else {
            $jsCode = "var recaptchaCallback_$id = function(response){jQuery('#{$inputId}').val(response); {$this->jsCallback}(response);};";
        }
        $this->jsCallback = 'recaptchaCallback_'.$id;

        if (empty($this->jsExpiredCallback)) {
            $jsExpCode = "var recaptchaExpiredCallback_$id = function(){jQuery('#{$inputId}').val('');};";
        } else {
            $jsExpCode = "var recaptchaExpiredCallback_$id = function(){jQuery('#{$inputId}').val(''); {$this->jsExpiredCallback}();};";
        }
        $this->jsExpiredCallback = 'recaptchaExpiredCallback_'.$id;

        $view->registerJs($jsCode, $view::POS_BEGIN);
        $view->registerJs($jsExpCode, $view::POS_BEGIN);
        
        echo Html::input('hidden', $inputName, null, ['id' => $inputId]);
    }
}
