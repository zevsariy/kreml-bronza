<?php
//Стартую сессию
session_start();
define('BASE_PATH',$_SERVER["DOCUMENT_ROOT"]);
//Объявляю кодировку и тип файла
header("Content-Type: text/html;charset=UTF-8");
//Подключаю файл конфиг для подключения к БД и библиотеку с функциями
include BASE_PATH.'/functions.php';
//Объявля пустую переменную дл вывода формы авторизации
$body = '';
//Проверяю авторизован ли пользователь
if ($_SESSION['auth']!=md5(md5($_SESSION['login']).md5($_SESSION['password'])))
{    
//Проверяю отправлены ли логи и пароль
    if (!empty($_POST['login']) && !empty($_POST['password']))
    {
        $stmt = $mysqli->prepare("SELECT * FROM kb_users WHERE login=? AND password=?");
        $stmt->bind_param('ss', $_POST['login'], md5($_POST['password']));
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $login_sql = $row['login'];
            $password_sql = $row['password'];
            $user_id_sql = $row['id'];
            $user_name_sql = $row['name'];
        }
        $stmt->close();
        //Шифрую пароль в мд5
        $password = md5($_POST['password']);
        //Сверяю пароли из БД и введенный пользователем( на самом деле их хеши)
        if ($password_sql!==$password)
        {
            $body = "<div class='panel-body'>
                <form action='' method='POST'>
                    <fieldset>
                    <p>Ошибка авторизации</p>
                        <div class='form-group'>
                            <label>Имя пользователя</label>
                            <input name='login' class='form-control' placeholder='Логин...' type='text'>
                        </div>
                        <div class='form-group'>
                            <label>Пароль</label>
                            <input name='password' class='form-control' placeholder='Пароль...' type='password'>
                        </div>
                    </fieldset>
                    <div>
                            <input type='submit' class='btn btn-info' name='enter' value='Войти'>
                    </div>
                </form>
            </div>";
        }
        else
        {
            //Если авторизованы то записываем сессию
            $_SESSION['login']= $_POST['login'];
            $_SESSION['password']= md5($_POST['password']);
            $_SESSION['user_id']= $user_id_sql;
            $_SESSION['name']= $user_name_sql;
            $_SESSION['auth'] = md5(md5($_SESSION['login']).md5($_SESSION['password']));
            header("Location:/kb-admin/index.php"); 
        }
    }
    else
    {
        //Иначе авторизуйтесь плиз
       $body = "<div class='panel-body'>
                <form action='' method='POST'>
                    <fieldset>
                        <div class='form-group'>
                            <label>Имя пользователя</label>
                            <input name='login' class='form-control' placeholder='Логин...' type='text'>
                        </div>
                        <div class='form-group'>
                            <label>Пароль</label>
                            <input name='password' class='form-control' placeholder='Пароль...' type='password'>
                        </div>
                    </fieldset>
                    <div>
                            <input type='submit' class='btn btn-info' name='enter' value='Войти'>
                    </div>
                </form>
            </div>";
    }
}
else
{
   header("Location:/kb-admin/index.php"); 
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
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
	        </div>
	     </div>
	</div>

    <div class="page-content"> 
   <?php
    //Вывод тела и модуля
    echo $body;
    ?>

    </div>

    <footer>
         <div class="container">
            <div class="copy text-center">
               Все права защищены © 2016 <a href='#'></a>
            </div>
         </div>
      </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
  </body>
</html>