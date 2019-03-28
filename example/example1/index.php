<?php
/*
 * Объявляем заголовок
 *
 */
header("Content-Type: text/xml");
include "../../class/ingatexml.php";
$xml = new IngateXml();

$params = array(
    'name'=>'Звезды общепита'
    ,'company'=>'Звезды общепита'
    ,'url'=>'https://www.zvezdy.ru/'
);
$catalog = array(
        '109' => array(
            'id' => 109,
            'name' => 'Ботинки',
            'parendId' => '',
            'VALUE' => '',
            'ATTR' => 'id="109"',
        ),
        '110' => array(
            'id' => 110,
            'name' => 'Ботинки женские',
            'parendId' => '109',
            'VALUE' => '',
            'ATTR' => 'id="109" parentId="109"',
        )
);
$product = array(
    '3867' => array
        (
        'ATTR' => 'id="3867" available="true"',
        'price' => '6400.0000',
        'description' => 'Изделия из полиэстера, современного качественного материала, получаются оригинальными и яркими, как жилет Меган.',
        'categoryId' => '42',
        'url' => 'http://10xl.dev-z.ru/zhenskaya-odezhda/verkhnyaya-odezhda?product_id=3867',
    ),

    '4358' => array
    (
        'ATTR' => 'id="4358" available="true"',
        'price' => '6200.0000',
        'description' => '',
        'categoryId' => '35',
        'url' => 'http://10xl.dev-z.ru/muzhskaya-obuv/ms-tufli?product_id=4358',
    ),

        '4268' => array
        (
        'ATTR' => 'id="4268" available="true"',
        'price' => '2600.0000',
        'description' => '',
        'categoryId' => '84',
        'url' => 'http://10xl.dev-z.ru/muzhskaya-obuv/tapki?product_id=4268',
    ),

        '5554' => array
        (
        'ATTR' => 'id="5554" available="true"',
        'price' => '7850.0000',
        'vendor' => 'Голд',
        'categoryId' => '71',
        'url' => 'http://10xl.dev-z.ru/muzhskaya-odezhda/m-sportivnye-kostiumy?product_id=5554',
    ),

    '4587' => array
        (
        'ATTR' => 'id="4587" available="true"',
        'price' => '7450.0000',
        'vendor' => 'Голд',
        'description' => '',
        'categoryId' => '71',
        'url' => 'http://10xl.dev-z.ru/muzhskaya-odezhda/m-sportivnye-kostiumy?product_id=4587',
    ),
);
// Задаем корневой элемент с параметрами
$export = $xml->startYandexGoods($params);

//  Открываем элемень с валютами с именем  currencies
$export .= $xml->xmlBeginTag('currencies');

/*
 * Записываем тег currency с пустым значением и атрибутом 'id="RUB" rate="1" '
 * */
$export .= $xml->xmlItem('currency',false,'id="RUB" rate="1" ');

/*
 * Закрываем тег
 * */
$export .= $xml->xmlEndTag('currencies');

/*
 * Записываем доставвку
 * */
$export .= $xml->xmlBeginTag('delivery-options');
$export .= $xml->xmlItem('option',false,'cost="700"');
$export .= $xml->xmlEndTag('delivery-options');

/*
 * ПРостое преобразования массива в вложенный хмл элемень (без вложенности дочерних)
 * где первый параметры массив данных
 * второй названия корневого элмента
 * названия дочернего элемента (если нет то ключи)
 * */
$export .= $xml->rowItemsSimple($catalog,false ,'categories','category');

/*
 * Преобразования массива в вложенный хмл элемень (c дочерними элементами)
 * где первый параметры массив данных
 * второй названия корневого элмента
 * названия дочернего элемента (если нет то ключи)
 * */
$export .= $xml->rowItemsWithParametrs($product,'available="true"','offers','offer','ATTR');

/*
 * Закрываем корневой каталог
 */
$export .= $xml->endYandexGoods();

/*выводим*/
print_r($export);


/*
 * Cоздаём файл
 * */
$xml->createClearXML($export, '/testing/sitemap/example/example1/ym.xml');


