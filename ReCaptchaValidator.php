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

use Yii;
use yii\validators\Validator;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use ReCaptcha\ReCaptcha;

/**
 * ReCaptcha widget validator.
 *
 * @author Milos Radojevic
 */
class ReCaptchaValidator extends Validator
{
    /** @var boolean Whether to skip this validator if the input is empty. */
    public $skipOnEmpty = false;

    /** @var string The shared key between your site and ReCAPTCHA. */
    public $secret;

    public function init()
    {
        parent::init();
        if (empty($this->secret)) {
            $this->secret = ArrayHelper::getValue(Yii::$app->params,'reCaptcha.secret',NULL);
        }
        
        if (empty($this->secret)) {
            throw new InvalidConfigException("ReCaptcha secret is not set. Follow README.md for more information.");
        }

        if ($this->message === null) {
            $this->message = Yii::t('yii', 'The verification code is incorrect.');
        }
    }

    /**
     * @param string $value
     * @return array|null
     */
    protected function validateValue($value)
    {
        $recaptcha = new ReCaptcha($this->secret);

        $response = $recaptcha->verify($value, Yii::$app->request->userIP);
        
        if ($response->isSuccess()) {
            return null;
        }
        
        return [$this->message, []];
    }
}
