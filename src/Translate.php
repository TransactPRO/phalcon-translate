<?php

namespace TransactPro\Translation;

class Translate
{
    public $translateMap = [];
    private $defaultLanguage = 'en';
    private $fallbackLanguage = null;

    private $languageColumn = 'language';
    private $keyColumn = 'key_name';
    private $valueColumn = 'value';

    /**
     * @param \Phalcon\Mvc\Model|array $modelOrMap Provide translation map as
     * array(
     *     'lang-key' => array(
     *         'translation-key' => 'translation-value'
     *     )
     * )
     * or
     * \Phalcon\Mvc\Model
     * @param bool $lang
     * @param array $options array('default' => ?, 'fallback' => ?)
     */
    public function __construct($modelOrMap, $lang = false, $options = [])
    {
        if (is_array($modelOrMap)) {
            $this->translateMap = $modelOrMap;
        } else {
            $this->loadFromDatabase($modelOrMap, $lang);
        }

        if (isset($options['default'])) {
            $this->setDefaultLanguage($options['default']);
        }

        if (isset($options['fallback'])) {
            $this->setFallbackLanguage($options['fallback']);
        }
    }

    /**
     * @param \Phalcon\Mvc\Model $modelClassName
     * @param bool|string $lang
     */
    private function loadFromDatabase($modelClassName, $lang = false)
    {
        if (false === $lang) {
            $translations = $modelClassName::find();

            foreach ($translations as $item) {
                $this->translateMap[$item->{$this->languageColumn}][$item->{$this->keyColumn}] = $item->{$this->valueColumn};
            }
        } else {
            $translations = $modelClassName::find([
                'condition' => $this->languageColumn . ' = :language:',
                'bind'      => [
                    'language' => $lang
                ]
            ]);

            foreach ($translations as $item) {
                $this->translateMap[$lang][$item->{$this->keyColumn}] = $item->{$this->valueColumn};
            }
        }
    }

    /**
     * @param string $key Translation key
     * @param null|string $lang Translation language
     * @return string Translation
     */
    public function _($key, $lang = null)
    {
        if (null === $lang) {
            $lang = $this->defaultLanguage;
        }

        if (isset($this->translateMap[$lang][$key])) {
            return $this->translateMap[$lang][$key];
        } else if (isset($this->translateMap[$this->fallbackLanguage][$key])) {
            return $this->translateMap[$this->fallbackLanguage][$key];
        } else {
            return $key;
        }
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @param string $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @return null|string
     */
    public function getFallbackLanguage()
    {
        return $this->fallbackLanguage;
    }

    /**
     * @param string|null $fallbackLanguage
     */
    public function setFallbackLanguage($fallbackLanguage)
    {
        $this->fallbackLanguage = $fallbackLanguage;
    }

    /**
     * @return string
     */
    public function getLanguageColumn()
    {
        return $this->languageColumn;
    }

    /**
     * @param string $languageColumn
     */
    public function setLanguageColumn($languageColumn)
    {
        $this->languageColumn = $languageColumn;
    }

    /**
     * @return string
     */
    public function getKeyColumn()
    {
        return $this->keyColumn;
    }

    /**
     * @param string $keyColumn
     */
    public function setKeyColumn($keyColumn)
    {
        $this->keyColumn = $keyColumn;
    }

    /**
     * @return string
     */
    public function getValueColumn()
    {
        return $this->valueColumn;
    }

    /**
     * @param string $valueColumn
     */
    public function setValueColumn($valueColumn)
    {
        $this->valueColumn = $valueColumn;
    }
}