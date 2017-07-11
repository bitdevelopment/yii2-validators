yii2-validators
===================

Yii2-validators are validators for yii for some specific cases that are not covered by standard yii2 validators. 
Currently it holds 2 validators but will be extended in future.
  - WordCount Validator
  - ReCaptcha Validator and InputWidget

Recaptcha validator and InputWidget is having new features: 
  - Compatible with Pjax
  - Added ability to have more than one ReCaptcha widget on same page

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/). Check the [composer.json](https://github.com/bitdevelopment/yii2-validators/blob/master/composer.json) for this extension's requirements and dependencies.

To install, either run

```
$ php composer.phar require bitdevelopment/yii2-validators "v1.1.0"
```

or add

```
"bitdevelopment/yii2-validators": "v1.1.0"
```

to the ```require``` section of your `composer.json` file.

## Latest Release

> NOTE: The latest version of the module is v1.1.0.

## Usage

### WordCount Validator

Add validator to your rules list in your model

```php
use bitdevelopment\yii2-validators\WordValidator;

class Post extends \yii\base\Model {

  public function rules() {
    return [
            [['myField'],WordValidator::className(),'min'=>200,'max'=>500]
    ];
  }
  ...
```

If you want to validation occurs while user is typing you can use validateOnType option, for example:

```php
$form->field($model, 'comment',['validateOnType' => true])->textarea([
                    'rows'=> 15
            ]);
```

> Since version v1.1.0 stripTags feature has been added, that strips HTML tags before it counts words, by default stripTags is setup to false.

### ReCaptcha Validator and Widget setup

There are two ways of setting ReCaptcha Validator in your project, one is inline setup, if you decide to use multiple key:secret pairs and setup in your `params.php` file if you have global setup 

##### Setting up in params

In your `params.php` file of your project setup new key so it will look like this: 
```php
return [
       /*
	* Your params...
	*/
 	'reCaptcha' => [
        	'siteKey' => 'site_key',
        	'secret' => 'secret_key',
    	],
];
```

Note that `site_key` and `secret_key` you can obtain on your [Google ReCaptcha Admin](https://www.google.com/recaptcha/admin).

Now let's setup validation in your Model:

```php 
use bitdevelopment\yii2-validators\ReCaptchaValidator;

class Post extends \yii\base\Model {

  public $reCaptcha;

  public function rules() {
    return [
            [['reCaptcha'], ReCaptchaValidator::className()],
	    [['reCaptcha'], 'required'],
    ];
  }
  ...
```

And Last step is to setup inputWidget on your view file, where your form is:
```php 
echo $form->field($comments, 'reCaptcha')->widget(
            	bitdevelopment\yii2validators\ReCaptcha::className(),
        )->label(false) ?>
```


##### Inline Settings

Similarly like when setting up with `params.php` you can setup params inline against a field, like in this example:

```php
  //In your rules
  [['reCaptcha'], ReCaptchaValidator::className(),'secret'=>'secret_key'],
  ...
```

```php
  //In your view
  echo $form->field($comments, 'reCaptcha')->widget(
            	bitdevelopment\yii2validators\ReCaptcha::className(),
		['siteKey','site_key']
        )->label(false) ?>
```
