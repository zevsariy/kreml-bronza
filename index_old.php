<?php
//Подключаю файл конфиг для подключения к БД и библиотеку с функциями
include_once 'config.php';
include 'functions.php';




error_reporting(E_ALL);
ini_set('display_errors', 1);

// Назначаем модуль и действие по умолчанию.
$module = 'pages';
$id = '1';
$lang = 'ru';
// Массив параметров из URI запроса.
$params = array();

// Если запрошен любой URI, отличный от корня сайта.
if ($_SERVER['REQUEST_URI'] != '/') {
	try {
		// Для того, что бы через виртуальные адреса можно было также передавать параметры
		// через QUERY_STRING (т.е. через "знак вопроса" - ?param=value),
		// необходимо получить компонент пути - path без QUERY_STRING.
		// Данные, переданные через QUERY_STRING, также как и раньше будут содержаться в 
		// суперглобальных массивах $_GET и $_REQUEST.
		$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// Разбиваем виртуальный URL по символу "/"
		$uri_parts = explode('/', trim($url_path, ' /'));

		// Если количество частей не кратно 2, значит, в URL присутствует ошибка и такой URL
		// обрабатывать не нужно - кидаем исключение, что бы назначить в блоке catch модуль и действие,
		// отвечающие за показ 404 страницы.
		/*if (count($uri_parts) % 2) {
			throw new Exception();
		}
        */
        if(count($uri_parts) == 3)
        {
            $lang = array_shift($uri_parts);
            $module = array_shift($uri_parts);
            $module = 'sections';
            $product_id = array_shift($uri_parts);
        }
        else if(count($uri_parts) == 2)
        {
            $lang = array_shift($uri_parts);
            $name = array_shift($uri_parts);
            $id = main::page_name_to_id($name);
            if($id!=-1)
            {
                $module = 'pages';
            }
            else
            {
                $id = main::section_name_to_id($name);
                if($id!=-1)
                {
                    $module = 'sections';
                }
                else
                {
                    $module = '404';
                }
            }
            
        }
        else if(count($uri_parts) == 1)
        {
            $lang = array_shift($uri_parts); // Получили имя действия
            $id = 1;
            $module = 'pages';
        }

		// Получили в $params параметры запроса
		for ($i=0; $i < count($uri_parts); $i++) {
			$params[$uri_parts[$i]] = $uri_parts[++$i];
		}
	} catch (Exception $e) {
		$module = '404';
		$id = '';
        $lang = 'ru';
	}
}
else
{
    header("Location:/ru/");
    $id = 1;
    $module = 'pages';
}

echo "\$module: $module\n";
echo "\$id: $id\n";
echo "\$lang: $lang\n";
echo "\$params:\n";
print_r($params);


if ($module == 'pages')
{
    $content = pages::get_content($id);
    $tkd = pages::get_tkd($id);    
}
else if($module == 'sections')
{
    if(isset($product_id))
    {
        $content = products::get_content($product_id);
        $tkd = products::get_tkd($product_id);
    }
    else
    {
        $content = sections::get_content($id);
        $tkd = sections::get_tkd($id);
    }
}
else if($module == '404')
{
    $content = pages::get_404($id);
}

$menu_pages = menu::get_pages();
$menu_sections = menu::get_sections();
$content = $content;

$tkd['title'] = $tkd['title'];
$tkd['description'] = $tkd['description'];
$tkd['keywords'] = $tkd['keywords'];

$template = file_get_contents("templates/main.html");

$template = str_replace("{{{title}}}", $tkd['title'], $template);
$template = str_replace("{{{keywords}}}", $tkd['keywords'], $template);
$template = str_replace("{{{description}}}", $tkd['description'], $template);

$template = str_replace("{{{menu_pages}}}", $menu_pages, $template);
$template = str_replace("{{{menu_sections}}}", $menu_sections, $template);

$template = str_replace("{{{content}}}", $content, $template);

echo $template;
?>
