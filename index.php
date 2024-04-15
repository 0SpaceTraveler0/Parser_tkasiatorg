<?php
ini_set('max_execution_time', '300');
include('simplehtmldom/simple_html_dom.php');

$domen = 'https://bouz.ru';

$html = file_get_html('https://bouz.ru/catalog/');

$mydiv = $html->find('div.item_block');
$arUrlSection = [];

//все разделы
foreach($mydiv as $element){
    $li = $element->find('li.name');
    $arUrlSection[] = $li[0]->children(0)->getAttribute('href');
}
$arUrlProduct = [];
foreach($arUrlSection as $sectionUrl){
    // $sectionUrl = $arUrlSection[0];
    $page = 1;
    $html = file_get_contents($domen . $sectionUrl . '?PAGEN_1=' . $page);
    $html_dom = new simple_html_dom();
    $html_dom->load($html);
    
    $modulePagination = $html_dom->find('div.module-pagination');
    if($modulePagination[0]){
        $lastPageForSection = (int)$modulePagination[0]->children()[0]->last_child()->innertext();
    }

    getAllProducts($domen, $sectionUrl, $page, $arUrlProduct, $lastPageForSection);
}
echo'<pre>'; print_r($arUrlProduct); echo'</pre>';



function getAllProducts($domen, $sectionUrl, $page, &$arUrlProduct, $lastPageForSection = 1){
    $url = $domen . $sectionUrl . '?PAGEN_1=' . $page;
    $html = file_get_contents($domen . $sectionUrl . '?PAGEN_1=' . $page);
    if($html){
        // Создаем объект библиотеки simple_html_dom
        $html_dom = new simple_html_dom();
        $html_dom->load($html);
        $product = '';
        foreach ($html_dom->find('div.item-title') as $element) {            
            if($product != $element->find('a', 0)->href){
                $arUrlProduct[] = $element->find('a', 0)->href;
                $product = $element->find('a', 0)->href;
            }                        
        }
        $html_dom->clear();
        unset($html, $html_dom);
        if($lastPageForSection != $page){
            getAllProducts($domen, $sectionUrl, $page + 1, $arUrlProduct, $lastPageForSection);
        }        
    }    
}