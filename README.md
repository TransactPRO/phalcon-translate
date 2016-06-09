Phalcon Translation Module 
=====
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/09a405f1-d1b4-4b8d-aa9d-1841964b64ea/big.png)](https://insight.sensiolabs.com/projects/09a405f1-d1b4-4b8d-aa9d-1841964b64ea)

[![Latest Version](https://img.shields.io/packagist/v/transactpro/phalcon-translate.svg?style=flat-square)](https://github.com/transactpro/phalcon-translate/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/transactpro/phalcon-translate.svg?style=flat-square)](https://packagist.org/packages/transactpro/phalcon-translate)

##How it works:
This translation module is designed to be able to store/access any key in/from database. All translation map is accessible at any time without spawning new queries.
There are three steps to match your key in translation map:
- provided or default language
- fallback language
- key as it is

Example #1: 

    We have `test` key with value `Test value` in our translation map for English only. 
    
    When we will ask for `test` with language key (`en` is enabled as fallback language):
        - en: `Test value`
        - de: `Test value`
        - ru: `Test value`
    When we will ask for `test` with language key (without fallback language):
        - en: `Test value`
        - de: `test`
        - ru: `test`

Example #2: 

    We have `test` key with value `Test value`/`Тестовое значение` in our translation map for English and Russian. 
    
    When we will ask for `test` with language key (`en` is enabled as fallback language):
        - en: `Test value`
        - de: `Test value`
        - ru: `Тестовое значение`
        - without: `Test value`
    When we will ask for `test` with language key (without fallback language):
        - en: `Test value`
        - de: `test`
        - ru: `Тестовое значение`
        - without: `Test value`

## Installation

```json
"require": {
	"transactpro/phalcon-translate": "~1.0"
}
```

## Usage and Configuration

####First of all you have to create translation table:
```sql
CREATE TABLE `translation` (
	`translation_id` INT(11) NOT NULL AUTO_INCREMENT,
	`language` VARCHAR(5) NOT NULL,
	`key_name` VARCHAR(48) NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY (`translation_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=1;
```

####Set shared service `translate` to your `Dependency Injection` container, providing your translation model class name. 
Example:
```php
$di->setShared('translate', function ($lang = false) use ($config, $di) {
    $adapter = new \TransactPro\Translation\Translate(
        TranslationModel::class,
        $lang
    );
    // setting default language that will be used when _() called without language parameter
    $adapter->setDefaultLanguage('en');
    // if fallback language is set, it will look in that translation map for value
    $adapter->setFallbackLanguage('de');
    /*
    * You can also provide them as options in third param array like:
    * [
    *   'default' => 'en',
    *   'fallback' => 'de'
    * ]
    */

    return $adapter;
});
```

####You can add filter to your Volt or Twig. 
Example:
```php
trans($key, $lang = null)
{
    $di = Di::getDefault();
    $translate = $di->getShared('translate');
    return $translate->_($key, $lang);
}
```

####If your table (model) does not match to provided `translation.sql`
You can map your own columns as option set in third param array:
```php
$di->setShared('translate', function ($lang = false) use ($config, $di) {
    $adapter = new Translate(
        TranslationModel::class,
        $lang,
        [
          'languageColumn' => 'lang',
          'keyColumn' => 'lang-key',
          'valueColumn' => 'lang-value'
        ]
    );
    
    return $adapter;
});
```
