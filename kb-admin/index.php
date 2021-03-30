<?php
//Стартую сессию
session_start();
define('BASE_PATH',$_SERVER["DOCUMENT_ROOT"]);
//Объявляю кодировку и тип файла
header("Content-Type: text/html;charset=UTF-8");
//Проверяю параметры для выхода из админки
if ($_GET['module']=='exit')
{
    session_destroy();
    $_SESSION = array();
    header("Location:/kb-admin/index.php"); 
}

//Проверяю авторизован ли пользователь
if ($_SESSION['auth']!=md5(md5($_SESSION['login']).md5($_SESSION['password'])))
{  
    //header("Location:/kb-admin/login.php");
    //exit;
}
//Подключаю файл конфиг для подключения к БД и библиотеку с функциями
include BASE_PATH.'/config.php';
include BASE_PATH.'/functions.php';

//Если модуль не выбран то это модуль СТРАНИЦЫ
if(!isset($_GET['module']))
{
    $module = 'pages';
}
else
{
    $module = $_GET['module'];
}

//Если событие не выбрано то это ЛИСТ или отобразить таблицы в админ панели
if(!isset($_GET['action']))
{
    $action = 'list';
}
else
{
    $action = $_GET['action'];
}

//Объявля пустую переменную дл вывода формы авторизации
$body = '';


//Объявляю пустую переменную для вывода модулей
$module_body ='';

//Проверка модуля, который выбран
if($module == 'pages')
{
    //проверка событий и выполнение функции
    if($action == 'list')
    {
        $module_body = admin_pages::get_list();
    }
    //проверка событий и выполнение функции
    else if($action == 'add')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_pages::add_submit();
        }
        else
        {
            $module_body = admin_pages::add_form();
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'edit')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_pages::edit_submit();
        }
        else
        {
            $module_body = admin_pages::edit_form($_GET['id']);
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'delete')
    {
        admin_pages::delete($_GET['id']);
    }
}
else if($module == 'crm')
{
    //проверка событий и выполнение функции
    if($action == 'list')
    {
        $module_body = Crm::get_table();
    }
    //проверка событий и выполнение функции
    else if($action == 'edit')
    {
        Crm::edit();
    }
    //проверка событий и выполнение функции
    else if($action == 'add')
    {
        Crm::add();
    }
    //проверка событий и выполнение функции
    else if($action == 'delete')
    {
        Crm::delete();
    }
}

else if($module == 'sections')
{
    //проверка событий и выполнение функции
    if($action == 'list')
    {
        $module_body = admin_sections::get_list();
    }
    //проверка событий и выполнение функции
    else if($action == 'add')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_sections::add_submit();
        }
        else
        {
            $module_body = admin_sections::add_form();
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'edit')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_sections::edit_submit();
        }
        else
        {
            $module_body = admin_sections::edit_form($_GET['id']);
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'delete')
    {
        admin_sections::delete($_GET['id']);
    }
}


else if($module == 'products')
{
    //проверка событий и выполнение функции
    if($action == 'list')
    {
        $module_body = admin_products::get_list();
    }
    //проверка событий и выполнение функции
    else if($action == 'add')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_products::add_submit();
        }
        else
        {
            $module_body = admin_products::add_form();
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'edit')
    {
        //Проверка отправлена ли форма
        if(isset($_POST['submit']))
        {
            admin_products::edit_submit();
        }
        else
        {
            $module_body = admin_products::edit_form($_GET['id']);
        }
    }
    //проверка событий и выполнение функции
    else if($action == 'delete')
    {
        admin_products::delete($_GET['id']);
    }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>KB-Панель администратора</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- styles -->
    <link href="/css/styles.css" rel="stylesheet">
    <style>
    .page-content
    {
        min-height: 768px;
    }
    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/AjexFileManager/ajex.js"></script>
  </head>
  <body>
  	<div class="header">
	     <div class="container">
	        <div class="row">
	           <div class="col-md-5">
	              <!-- Logo -->
	              <div class="logo">
	                 <h1><a href="/kb-admin/index.php">KB-Панель администратора</a></h1>
	              </div>
	           </div>
	           <div class="col-md-2">
	              <div class="navbar navbar-inverse" role="banner">
	                  <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
	                    <ul class="nav navbar-nav">
                          <li><a href="/kb-admin/index.php?module=exit">Выйти</a></li>
	                    </ul>
	                  </nav>
	              </div>
	           </div>
	        </div>
	     </div>
	</div>

    <div class="page-content">
    	<div class="row">
		  <div class="col-md-2">
		  	<div class="sidebar content-box" style="display: block;">
                <ul class="nav">
                    <!-- Main menu -->
                    <?php
                        admin_menu::get();
                    ?>
                </ul>
             </div>
		  </div>
		  <div class="col-md-10">
                <?php
                        echo $module_body;
                    ?>
		  </div>
		</div>
    </div>

    <footer>
         <div class="container">
            <div class="copy text-center">
               Все права защищены © 2017
            </div>
         </div>
      </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/custom.js"></script>
  </body>
</html>