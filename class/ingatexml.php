<?
class IngateXML
{

    public static $arrparams = array();
    public static $debug = false;
    public static $dateformat = 'Y-m-d';
    public static $file = false;
    public static $itemCount = 20000;
    public static $start = '<?xml version="1.0" encoding="UTF-8"?>';
    public static $siteurl = '';
    public static $changefreq = 'daily';
    /*
    ** Задаем переменную соглавно массиву
    */
    public static function Init($arrparams)
    {
        self::$arrparams = $arrparams;
        if (isset($arrparams['siteurl'])) {
            self::$siteurl = $arrparams['siteurl'];
        }
        if (isset($arrparams['dateformat'])) {
            self::$dateformat = $arrparams['dateformat'];
        }
        if (isset($arrparams['changefreq'])) {
            self::$changefreq = $arrparams['changefreq'];
        }
        if (isset($arrparams['start'])) {
            self::$start = $arrparams['start'];
        }
        if (isset($arrparams['itemCount'])) {
            self::$itemCount = $arrparams['itemCount'];
        }
    }

    /*
    ** Функция для тестирования
    */
    public static function developDebug()
    {
        self::$debug = true;
    }

    /**
     * Задаем формат дат
     * @param $date
     */
    public static function setDateFormat($date)
    {
        self::$dateformat = $date;
    }

    /*
    ** Сделать простой XML файл
    */
    public static function createClearXML($text, $file)
    {
        $fileTemp = $_SERVER['DOCUMENT_ROOT'] . $file . '-' . time();
        $file = $_SERVER['DOCUMENT_ROOT'] . $file;
        $fp = fopen($fileTemp, "w");
        fwrite($fp, $text);
        fclose($fp);
        if (rename($fileTemp, $file)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $url
     * @param bool $lastmod
     * @param bool $changefreq
     * @param bool $priority
     * @return string
     */
    public static function createSitemapItem($url, $lastmod = false, $changefreq = false, $priority = false)
    {
        $item = '<url>' . PHP_EOL;
        $item .= '<loc>' . $url . '</loc>' . PHP_EOL;
        if (!$lastmod) {
            $lastmod = date(self::$dateformat);
        }
        if (!$changefreq) {
            $changefreq = self::$changefreq;
        }
        $item .= '<lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        if (!$priority) {
            $url = parse_url($url);
            if (trim($url['path'], '/') == '') {
                $priority = 1;
            } else {
                $priority = 1 - (0.1 * count(explode('/', trim($url['path'], '/'))));
            }
        }
        $item .= '<changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $item .= '<priority>' . $priority . '</priority>' . PHP_EOL;
        $item .= '</url>' . PHP_EOL;
        return $item;
    }

    /*
    ** Вывести карту сайта
    **
    */
    public static function viewSitemapXML($params)
    {
        $text = self::$start . PHP_EOL;
        $text .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $lastmod = false;
                $changefreq = false;
                $priority = false;
                if (isset($value['lastmod'])) {
                    $lastmod = $value['lastmod'];
                }
                if (isset($value['changefreq'])) {
                    $changefreq = $value['changefreq'];
                }
                if (isset($value['priority'])) {
                    $priority = $value['priority'];
                }
                $text .= self::createSitemapItem($value['url'], $lastmod, $changefreq, $priority);
            } else {
                $text .= self::createSitemapItem($value);
            }
        }
        $text .= '</urlset>';
        return $text;
    }

    /*
    ** Делаем файлы sitemap.xml c ограничениями на кол-во
    **
    */
    public static function createSitemapXML($params, $path)
    {
        if (count($params) > self::$itemCount) {
            $params = array_chunk($params, self::$itemCount);
            $i = 0;
            $textIndex = self::$start . PHP_EOL;
            $textIndex .= "<sitemapindex>" . PHP_EOL;

            foreach ($params as $key => $part) {
                $i++;
                $file = $path . 'sitemap-' . $i . '.xml';
                $text = self::viewSitemapXML($part);
                self::createClearXML($text, $file);
                $textIndex .= '<sitemap>'
                    . PHP_EOL .
                    '<loc>http://' . $_SERVER['SERVER_NAME'] . $file . '</loc>'
                    . PHP_EOL
                    . '<lastmod>' . date(self::$dateformat) . '</lastmod>'
                    . PHP_EOL .
                    '</sitemap>' . PHP_EOL;
            }
            $textIndex .= "</sitemapindex>" . PHP_EOL;
            self::createClearXML($textIndex, $path . 'sitemap.xml');
        } else {
            $text = self::viewSitemapXML($params);
            self::createClearXML($text, $path . 'sitemap.xml');
        }
    }

    /*
    ** Делаем элемент xml
    **
    */
    public static function xmlItem($param, $value = false, $attr = '')
    {
        if ($value || $value != '') {
            return '<' . $param . ' ' . $attr . '>' . $value . '</' . $param . '>' . PHP_EOL;
        } else {
            return '<' . $param . ' ' . $attr . '/>' . PHP_EOL;
        }
    }

    /*
    ** Начало элемента
    **
    */
    public static function xmlBeginTag($param, $attr = '')
    {
        return '<' . $param . ' ' . $attr . '>' . PHP_EOL;
    }

    /*
    ** Конец элемента
    **
    */
    public static function xmlEndTag($param)
    {
        return '</' . $param . '>' . PHP_EOL;
    }

    /*
    ** Начало YML Товары и цены (Для простоты)
    **
    */
    public static function startYandexGoods($params = '')
    {
        $text = self::$start. PHP_EOL;
        $text .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'. PHP_EOL;
        $text .= self::xmlBeginTag('yml_catalog', 'date="' . date('Y-m-d h:i') . '"');
        $text .= self::xmlBeginTag('shop');
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $text .= self::xmlItem($key, $value);
            }
        } else {
            if ($params != '') {
                $text .= self::xmlItem($params);
            }
        }

        return $text;
    }

    /*
    ** Конец YML Товары и цены (Для простоты)
    **
    */
    public static function endYandexGoods()
    {
        $text = '';
        $text .= self::xmlEndTag('shop');
        $text .= self::xmlEndTag('yml_catalog');
        return $text;
    }

    /*
    ** Преобразуем массив в простой вывод формата XML (массив, есть ли родитель, есть ли общее имя / берем ключи, атрибут для родителя)
    **
    */

    public static function rowItemsSimple($params,$attrlist = '', $namelist = false, $nameitem = false)
    {
        $text = '';
        if (is_array($params)) {
            if ($namelist) {
                $text .= self::xmlBeginTag($namelist, $attrlist);
            }
            if ($nameitem) {
                foreach ($params as $key => $value) {
                    if (!is_array($value)) {
                        $text .= self::xmlItem($nameitem, $value);
                    } else {
                        $text .= self::xmlItem($nameitem, $value['VALUE'], $value['ATTR']);
                    }
                }
            } else {
                foreach ($params as $key => $value) {
                    if (!is_array($value)) {
                        $text .= self::xmlItem($key, $value);
                    } else {
                        $text .= self::xmlItem($key, $value['VALUE'], $value['ATTR']);
                    }
                }
            }
            if ($namelist) {
                $text .= self::xmlEndTag($namelist);
            }
        }
        return $text;
    }

    /*
    ** Преобразуем массив в структирированный вывод формата XML (массив, есть ли родитель, есть ли общее имя / берем ключи, атрибут для родителя, ключ атрибута для элемента списка)
    **
    */

    public static function rowItemsWithParametrs(
        $params,
        $attrlist = '',
        $namelist = false,
        $nameitem = false,
        $rowattrname = false
    ) {
        $text = '';
        if (is_array($params)) {
            if ($namelist) {
                $text .= self::xmlBeginTag($namelist, $attrlist);
            }
            foreach ($params as $key => $value) {
                $text .= self::xmlBeginTag($nameitem, $value[$rowattrname]);
                unset($value[$rowattrname]);
                $text .= self::rowItemsSimple($value);
                $text .= self::xmlEndTag($nameitem);
            }
            if ($namelist) {
                $text .= self::xmlEndTag($namelist);
            }

        } else {
            $text = self::xmlItem($params, $attrlist);
        }
        return $text;
    }

    /*
    ** Формируем короткое описания
    **
    */
    public static function getShortDesc($str, $wordCount = 30)
    {
        $str = implode('', array_slice(
                preg_split('/([\s,\.;\?\!]+)/', $str, $wordCount * 2 + 1, PREG_SPLIT_DELIM_CAPTURE)
                , 0, $wordCount * 2 - 1)
        );
        return $str . '...';

    }

    /*
    ** Чистим текст под яндекс турбо
    **
    */
    public static function getYandexTurboContent($str)
    {
        $NewContent = preg_replace('/<img(.*)src=\"http(.*)\"(.*)\>/iU', '', $str);
        $NewContent = preg_replace('/<img(.*)src=\"\/(.*)\"(.*)\>/iU',
            '<figure><img src="' . self::$siteurl . '/$2"> </figure>', $NewContent);
        $clearArr = array();
        $clearArr[] = '/<script(.*)\>(.*)<\/script>/sU';
        $clearArr[] = '/<style(.*)\>(.*)<\/style>/sU';
        $clearArr[] = '/<frame(.*)\>(.*)<\/frame>/sU';
        $clearArr[] = '/<a(.*)href="#(.*)\>(.*)<\/a>/sU';
        $clearArr[] = '/<a(.*)onclick="(.*)\>(.*)<\/a>/sU';
        $clearArr[] = '/<meta(.*)\">/sU';
        $clearArr[] = '/\[(.*)\]/m';
        $NewContent = preg_replace($clearArr, '', $NewContent);
        $NewContent = preg_replace('/<a(.*)href="\/(.*)"\>(.*)<\/a>/sU', '<a href="' . self::$siteurl . '/$2">$3</a>',
            $NewContent);
        $NewContent = preg_replace('/<a(.*)href="#(.*)"\>(.*)<\/a>/sU', '$3', $NewContent);
        $NewContent = preg_replace('/<div(.*)\>/', "", $NewContent);
        $NewContent = preg_replace('/<\/div(.*)\>/', "<br>", $NewContent);
        $NewContent = html_entity_decode($NewContent);
        $NewContent = str_replace(array('&nbsp;'), " ", $NewContent);
        $NewContent = strip_tags($NewContent, '<p><b><br><ul><h2><h3><h4><h5><h6><a><li><strong><figure><img>');
        return $NewContent;
    }
}