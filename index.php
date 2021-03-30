<?php
session_start();
//Подключаю файл конфиг для подключения к БД и библиотеку с функциями
include_once 'config.php';
include 'functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Назначаем модуль и действие по умолчанию.
$module = 'pages';
$id = '1';
$lang = 'ru';
$section_cur_id = 0;
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
            if($name=='mail-to')
            {
                $to  = "zevsariy.app@gmail.com" ; 

                $subject = "Просьба перезвонить клиенту"; 

                $message = ' 
                <html> 
                    <head> 
                        <title>Просьба перезвонить клиенту</title> 
                    </head> 
                    <body> 
                        <p>Имя:'.$_POST['name'].'<p><p>Телефон или email:'.$_POST['phone'].'</p>
                        <p>Сообщение:</p>
                        <p>'.$_POST['message'].'</p>
                    </body> 
                </html>'; 

                $headers  = "Content-type: text/html; charset=UTF-8 \r\n"; 
                $headers .= "From: Просьба перезвонит клиенту <kremlin@bronze.ru>\r\n"; 
                
                mail($to, $subject, $message, $headers);
                $module = 'mail-to-success';
            }
            else if($name=='shop_cart')
            {
                $module = 'shop_cart';
            }
            else if($name=='favorite')
            {
                $module = 'favorites';
            }
            else if($name=='favorites-delete')
            {
                $module = 'favorites-delete';
            }
            else if($name=='products_on_store')
            {
                $module = 'products_on_store';
            }
			else if($name=='thankyou')
            {
                $module = 'thankyou';
            }
            else
            {
                $id = main::page_name_to_id($name);
                if($id!=-1)
                {
                    $module = 'pages';
                }
                else
                {
                    $id = main::section_name_to_id($name);
                    $section_cur_id = $id;
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
        }
        else if(count($uri_parts) == 1)
        {
            $lang = array_shift($uri_parts); // Получили имя действия
            $id = 0;
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
/*
echo "\$module: $module\n";
echo "\$id: $id\n";
echo "\$lang: $lang\n";
echo "\$params:\n";
print_r($params);
*/
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
else if($module == 'thankyou')
{
    $content = pages::get_thankyou();
	$tkd = sections::get_tkd(1);
}
else if($module == 'mail-to-success')
{
    if($lang == 'ru')
        $content = '<h2>Ваша сообщение успешно отправлено</h2>
        <a href="/ru/">Вернуться на главную страницу</a>';
    else
        $content = '<h2>Success mailed</h2>
        <a href="/en/">Return to main page</a>';
        $tkd['title'] = '';
        $tkd['description'] = '';
        $tkd['keywords'] = '';
}
else if($module == 'favorites')
{
    if(isset($_GET['product_id']))
    {
        $status = favorites::add();
        echo $status;
        exit;
    }
    else
    {
        $content = favorites::get_content();
        $tkd['title'] = '';
        $tkd['description'] = '';
        $tkd['keywords'] = '';
    }  
}
else if($module == 'shop_cart')
{
    if(isset($_GET['product_id']))
    {
        $status = shop_cart::add($_GET['product_id']);
        echo $status;
        exit;
    }
    else
    {
        $content = shop_cart::get_content();
        $tkd['title'] = '';
        $tkd['description'] = '';
        $tkd['keywords'] = '';
    }  
}
else if($module == 'products_on_store')
{
        $content = sections::get_on_store();
        $tkd['title'] = '';
        $tkd['description'] = '';
        $tkd['keywords'] = '';
}

$menu_pages = menu::get_pages();
$menu_sections = menu::get_sections();
$content = $content;

$tkd['title'] = $tkd['title'];
$tkd['description'] = $tkd['description'];
$tkd['keywords'] = $tkd['keywords'];

if($lang == 'ru')
{
    $lang_button = "<a id='lang-button' href='".main::get_lang_url()."'><img src='/images/en.png' width='40px' height='20px'/></a>";
    
    $products_on_store = '<div class="products_store_button"><a href="/ru/products_on_store/"><input type="button" value="Изделия в наличии"></a></div>';
    
    $presentations_button = '<div class="presentations_button"><a href="/ru/prezentacii/"><input type="button" value="Презентации"></a></div>';
}
else
{
    $lang_button = "<a id='lang-button' href='".main::get_lang_url()."'><img src='/images/ru.png' width='40px' height='20px'/></a>";

    $products_on_store = '<div class="products_store_button"><a href="/en/products_on_store/"><input type="button" value="Products on store"></a></div>';
    
    $presentations_button = '<div class="presentations_button"><a href="/en/presentations/"><input type="button" value="Presentations"></a></div>';
}
    

        

$template = file_get_contents("templates/boot_template/index.html");

$template = str_replace("{{{lang_button}}}", $lang_button, $template);

$template = str_replace("{{{title}}}", $tkd['title'], $template);
$template = str_replace("{{{keywords}}}", $tkd['keywords'], $template);
$template = str_replace("{{{description}}}", $tkd['description'], $template);

$template = str_replace("{{{menu_pages}}}", $menu_pages, $template);
$template = str_replace("{{{menu_sections}}}", $menu_sections, $template);

$template = str_replace("{{{content}}}", $content, $template);

$template = str_replace("{{{main_page_url}}}", '/'.$lang, $template);

$template = str_replace("{{{products_on_store}}}", $products_on_store, $template);

$template = str_replace("{{{presentations_button}}}", $presentations_button, $template);




    if($lang == 'ru')
    {
        $modal_form_html = '<div id="modal_form">
        <span id="modal_close">X</span>
            <form action="mail-to" method="post">
                <h3>Форма обратного звонка</h3>
                <p>Заполните форму и мы вам обязательно перезвоним</p>
                <p>Ваше имя<br />
                    <input type="text" name="name" value="" size="40" />
                </p>
                <p>Ваш телефон<br />
                    <input type="text" name="phone" value="" size="40" />
                </p>
                <p style="text-align: center; padding-bottom: 10px;">
                    <input type="submit" value="Отправить" />
                </p>
            </form>
    </div>';
    }
    else
    {
        $modal_form_html = '<div id="modal_form">
            <span id="modal_close">X</span>
                <form action="mail-to" method="post">
                    <h3>Call back form</h3>
                    <p>Fill a form and we will surely call back to you</p>
                    <p>Your name<br />
                        <input type="text" name="name" value="" size="40" />
                    </p>
                    <p>Your phone number<br />
                        <input type="text" name="phone" value="" size="40" />
                    </p>
                    <p style="text-align: center; padding-bottom: 10px;">
                        <input type="submit" value="Send" />
                    </p>
                </form>
        </div>';
    }
    $template = str_replace("{{{modal_form}}}", $modal_form_html, $template);
echo $template;
?>
