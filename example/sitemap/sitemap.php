<pre>
<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "class/ingatexml.php";
$sitemaArr = array(
'http://xml.dev-z.ru/'
,'http://xml.dev-z.ru/home5/'
,'http://xml.dev-z.ru/home/home6'
,'http://xml.dev-z.ru/home/home/121/'
,'http://xml.dev-z.ru/home/home?ger=1'
,'http://xml.dev-z.ru/home/home/?ger=1'
);
	$item1 = array(
		'url'=>'http://xml.dev-z.ru4/'
		,'lastmod'=>'2018-01-30'
		,'changefreq'=>'daily'
		,'priority'=> 1
		);
	$item2 = array(
		'url'=>'http://xml.dev-z.ru/home3/'
		,'changefreq'=>'daily'
		,'priority'=> 0.75
	);
$sitemaArr2 = array(
				 $item1
				,$item2
				,'http://xml.dev-z.ru/home2/'
				,'http://xml.dev-z.ru/home/home1'
				,'http://xml.dev-z.ru/home/home/121/'
				,'http://xml.dev-z.ru/home/home?ger=1'
				,'http://xml.dev-z.ru/home/home/?ger=1'
);


$xml = new ingateXML();
$xml->init(array('siteurl' => 'http://xml.dev-z.ru/','itemCount' => 3));
$xml->createSitemapXML($sitemaArr2, '/testing/sitemap/')
//print_r($_SERVER);
?>