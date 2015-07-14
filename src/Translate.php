<?php

namespace TransactPro\Translation;


class Translate
{
    public $translateMap = [];
    private $defaultLanguage = 'en';
    private $fallbackLanguage = null;

    /**
     * @param \Phalcon\Mvc\Model|array $modelOrMap Provide translation map as
     * array(
     *     'lang-key' => array(
     *         'translation-key' => 'translation-value'
     *     )
     * )
     * or
     * Phalcon\Mvc\Model
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

    private function loadFromDatabase($modelClassName, $lang = false)
    {
        if (false === $lang) {
            $translations = $modelClassName::find();

            foreach ($translations as $item) {
                $this->translateMap[$item->language][$item->key_name] = $item->value;
            }
        } else {
            $translations = $modelClassName::find([
                'condition' => 'language = :language:',
                'bind'      => [
                    'language' => $lang
                ]
            ]);

            foreach ($translations as $item) {
                $this->translateMap[$lang][$item->key_name] = $item->value;
            }
        }
    }

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
     * @return null
     */
    public function getFallbackLanguage()
    {
        return $this->fallbackLanguage;
    }

    /**
     * @param null $fallbackLanguage
     */
    public function setFallbackLanguage($fallbackLanguage)
    {
        $this->fallbackLanguage = $fallbackLanguage;
    }
}