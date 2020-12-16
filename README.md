# Live Video Commerce

### 18n翻译相关
```shell script
composer require google/cloud-translat
```

```shell script
php twig.php en
php twig.php zhHans
php twig_i18n.php zhHans
```
**`twig.php`**
```php
<?php

$words = [];
$_lang = isset($argv[1]) ? $argv[1] : 'en';
$_dir = isset($argv[2]) ? $argv[2] : '';

$path = realpath(__DIR__ . "/Template");
$path = !empty($_dir) ? $path . '\\' . $_dir : $path;
/**
* Run Path
* string $path
* @param $path
*/
function run_path($path)
{
    $dir = opendir($path);
    while ($file = readdir($dir)) {
        $_path = $path;
        if ($file !== '.' && $file !== '..') {
            $_path = $_path . '\\' . $file;

            if (is_dir($_path)) {
                run_path($_path);
            } elseif (substr($file, -5) === '.twig') {
                $content = file_get_contents($_path);
                getI18nFromTwig($content);
            }
        }
    }
    closedir($dir);
}
/**
* getI18nFromTwig
* string $string
* @param $string
*/
function getI18nFromTwig($string)
{
    global $words;

    $matches = [];
    preg_match_all("#{{\s*[\"'](?<words>.*?)[\"']\s*\|\s*i18n\s*}}#", $string, $matches);
    $match_words = isset($matches['words']) ? $matches['words'] : [];
    $words = array_merge($words, $match_words);
    preg_match_all("#{{.*?default\(\"(?<words>.*?)\"\)\s*\|\s*i18n\s*?}}#", $string, $matches);
    $match_words = isset($matches['words']) ? $matches['words'] : [];
    $words = array_merge($words, $match_words);
    preg_match_all("/\(\"(?<words>.*?)\"\s*\|\s*i18n,/", $string, $matches);
    $match_words = isset($matches['words']) ? $matches['words'] : [];
    $words = array_merge($words, $match_words);
}


run_path($path);

require_once __DIR__ . "/Module/I18n/{$_lang}.php";
$class = '\\Module\\I18n\\'.$_lang;
$i18n = new $class();
$oldWorlds = $i18n();

foreach ($words as $word) {
    if (!isset($oldWorlds[$word])) {
        $oldWorlds[$word] = $word;
    }
}

file_put_contents(__DIR__ . "/twig_{$_lang}.php", "<?php\n return " . var_export($oldWorlds, true) . ";");
```

**`twig_i18n.php`**
```php
<?php
require 'vendor/autoload.php';

use Google\Cloud\Translate\V2\TranslateClient;

$projectId = 'e-arcana-255907';
$config = '{
  "type": "service_account",
  "project_id": "e-arcana-255907",
  "private_key_id": "d2a50b748ae5673b738c3254d786e6327f2383c6",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDFqs5i1m1AOfvM\nD+zypqIlOKzsDZG9JH9ULF4c3Sps9kNHjqCMEUMNN6mG7HIbMNdluRDBl786poPn\nrGTO1vEw2FkijZTVNerVGVkOrF0Yog+6T32jG53sGohAp6NjTuTq1/J733fSfcRX\nnO+u4ERLI/irYJoihDpTLZOQ4kZdWXkTRDaiO803zULtZtaP6KyEQjzL76srqnQ2\nDWd/YtpYwKpyjgRfdWH+BNkYwKRKOS2LIv58rB+/wLAL6iZKwZwH+E/sVonfiKBW\nssTEGttJP6ZQlO/48iJFFiEr2ZBYV1Cy4JqZpJqPis9VTr4w2X2HTdIfuxJPelAP\nJekGEGEtAgMBAAECggEAEXSMloHPp/+hfVsIAO2MyAisjIkuONZY6s/4MkhS5Mhi\nbJaVwDNjXm7Ek7DxlUvHhFNxaAcZ0KNqZoenZ0nn2/kJoK7WrnVScCW1jZbUVHE1\n6QKir51StsopBLyeWw4D0maQzTY7WI/ZSiYGVxpgLYUsV8XyFouNXacFWaKsYAsi\nQP5etUHxKfUEpICfMikcz82AvRcvKUwkBeoNBuOhGFLokfYLyo8cU5MvsAViU5Xh\nHJdh8rsepkHgBX281nZ4blgvDkdeb6dwwkc54qQLgQ369ysQWvL431YaWG2nfsyF\nY2FvtBSYL/bD0IXS7jVTBmBMvoG+2zedcrPe8q5v6QKBgQDiR6EarbZrB5DJ4dP0\nAR6h02gM33j2Xxx4WFqmDjBY/It6FzY5DJ3F0lQXqknbWy1Sv11+7ddGe5nDWOTW\nKPsT47WP8lZUPhWVrCuyeSGT5KInxjXoaNROibrGDwvftrNNyhp9sFOeGkj1khhA\nPv6U4Dv/eF6iBDQSGYbgLd03SwKBgQDfoR1qiDxRpi/arWMBz8zjhHxi031FdEON\nbLhRKa9soYmyP/qPDKV3x5Ck+sIOhBuKte1fu8kUid85C/FdCiA6uYKmrvXTtYmN\ns4XL0YJAiEL1xodkfAE634btHzi9rOAh79CfToMEsME59b6j/CpMqlfmWL/Y2fRC\nwJHlyU4mZwKBgQCkwKyPRS3Njeug4hk1zt7jyo422Ts0fxm2kfYmc6xGDlLraR0k\nuofhcfuiY211FlTQq97CPSACBJp2/jFXsOzmlWQr07GBktaabIpAXyvQh6Z9OTck\n6bazHFruPCRUFa1FlUJmg0zOj6rRija4CGXKNd3Za2XTpyXWi9mFPj+UMwKBgQCv\nVQvSNcRNI+YLJrwkdH8otvwrI7PpG5HHNUGB6RPwMOrbxYu2Umz9b4s5vp0dcniB\nlcfpsjqijsJkYLe5gbHpOP91nmGAvql9Xw580eO0ouEU/7WxlAQG27BXA46iMAN+\nuB6yeIUCzW6B+emhepjTiQ7nvBeWdrQrDf6V0fIi6wKBgQCuFAm6TbZ66sfjGun/\ntos2dWKjFMA0QaIU6k5GUJ1QDHkS1NfrHG/yz4JECJqZjwK1PoUAW3wEAj9Is7vo\nXMEYqu2WrrNqOE4lzv92EI2hXYkE+Ux1wju1yPEp60GyNSA0ED5thwwsm2mvhQ2N\nk54XnncYxRip/ndliBa9hBdI0A==\n-----END PRIVATE KEY-----\n",
  "client_email": "starting-account-uajast6mopyt@e-arcana-255907.iam.gserviceaccount.com",
  "client_id": "114547890055298662378",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/starting-account-uajast6mopyt%40e-arcana-255907.iam.gserviceaccount.com"
}';

$translate = new TranslateClient([
    'projectId' => $projectId,
    'keyFile' => json_decode($config, true),
    'requestTimeout' => 3.0
]);

$_lang2 = $_lang = isset($argv[1]) ? $argv[1] : 'en';
if ($_lang === 'zhHans')
    $_lang2 = 'zh';
//Old
$file_path = __DIR__ . "/Module/I18n/{$_lang}.php";
if (!file_exists($file_path)) {
    echo 'file not exist';
    return;
}

require_once $file_path;
$class = '\\Module\\I18n\\' . $_lang;
$i18n = new $class();
//$words = $i18n();
$words = [];

//New
$file_path_translate = __DIR__ . "/twig_{$_lang}.php";
if (!file_exists($file_path_translate)) {
    echo 'file translate not exist';
    return;
}
$translate_words = require_once $file_path_translate;

/**
* Google Translate
* string $text
* strting $to
* @param $text
* @param string $to
* @return string
*/
function googleTranslate($text, $to = 'en')
{
    global $_lang2;
    global $translate;

    $result = $translate->translate($text, [
        'target' => $_lang2,
//        'source' => $from,
        'model' => 'nmt'
    ]);
    $result = isset($result['text']) ? $result['text'] : $text;

    return $result;
}

foreach ($translate_words as $key => &$text) {
    try {
        if (!isset($words[$key])) {
            $newString = googleTranslate($text, $_lang);
            if ($newString) {
                echo "$text -> $newString \n";
                $text = $newString;
            }
        }
    } catch (Exception $ex) {
        echo $ex->getMessage() . "\n";
        exit;
    }
}

file_put_contents($file_path_translate, var_export($translate_words, true));
```