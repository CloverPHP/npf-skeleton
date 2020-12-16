<?php

namespace Module;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * Class I18n
 * @package Module\Twig
 */
final class I18n extends AbstractExtension
{
    private $lang = '';

    private $languages = [
        'zhHans' => '中文',
        'en' => 'English',
    ];

    private $discData = [];

    /**
     * @return array|TwigFilter
     */
    final public function getSupportedLang()
    {
        return $this->languages;
    }

    /**
     * @return array|TwigFilter
     */
    final public function getFilters()
    {
        return [
            new TwigFilter('i18n', [$this, 'translation']),
        ];
    }

    /**
     * @param $text
     * @return string
     */
    final public function translation($text)
    {
        return isset($this->discData[$text]) ? (string)$this->discData[$text] : $text;
    }

    /**
     * @param $lang
     * @param string $defaultLang
     * @return mixed
     */
    final public function setLang($lang, $defaultLang = 'en')
    {
        $lang = str_replace("-", "", $lang);
        if (!isset($this->languages[$lang]))
            $lang = $defaultLang;
        $dictClass = "Module\\I18n\\{$lang}";
        if (class_exists($dictClass)) {
            $dict = new $dictClass();
            $this->discData = $dict();
            $this->lang = $lang;
        }
        return $lang;
    }
}