<?php
defined( '_JEXEC' ) or die; // защита

// главная функция этого файла, имя этой функции изменять нельзя
function pagination_list_render($list){
    $html = '';
    $gwpages = array();
    // перебираем массив со всеми кнопками пагинации
    foreach ($list['pages'] as $number => $page) {
        if($page['active'] === false){
            $current = $number;
        }
        $countPages = $number;
    }
    // конец перебора массива

    // Далее отрисовываем пагинацию так, как нам захочется
    if($current > 1) $html .= GWaddRow(&$gwpages, $list['previous'], 'pagination-previous');
    $html .= GWaddRow(&$gwpages, $list['start'], 'pagination-start');
    if($current > 3) $html .= '...';
    if(isset($list['pages'][$current-1])) $html .= GWaddRow(&$gwpages, $list['pages'][$current-1]);
    $html .= GWaddRow(&$gwpages, $list['pages'][$current]);
    if(isset($list['pages'][$current+1])) $html .= GWaddRow(&$gwpages, $list['pages'][$current+1]);
    if($current < ($countPages - 2)) $html .= '...';
    $html .= GWaddRow(&$gwpages, $list['end'], 'pagination-end');
    if($current < $countPages) $html .= GWaddRow(&$gwpages, $list['next'], 'pagination-next');
    return '<ul>'.$html.'</ul>';
}
// конец главной функции

// Вспомогательная функция, она необязательна, но в данном случае было удобно делать с помощью неё
function GWaddRow(&$pages, $page, $class = ''){
    if(in_array($page, $pages)){
        return;
    }
    $row = '<li';
    $row .= ($class)? ' class="'.$class.'">' : '>';
    $row .= $page['data']. '</li>';
    $pages[] = $page;
    return $row;
}
?>