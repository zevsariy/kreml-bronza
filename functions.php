<?php
$mysqli = new mysqli("localhost", "035848010_devkb", "12345678", "zevsariy_devkb");

//error_reporting(0);

function gaParseCookie() {
  if (isset($_COOKIE['_ga'])) {
    list($version,$domainDepth, $cid1, $cid2) = split('[\.]', $_COOKIE["_ga"],4);
    $contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1.'.'.$cid2);
    $cid = $contents['cid'];
  }
  else $cid = 666000666000666;
  return $cid;
}


class main
{
    function XMail( $from, $to, $subj, $text, $filename)
    {
    $f         = fopen($filename,"rb");
    $un        = strtoupper(uniqid(time()));
    $head      = "From: $from\n";
    $head     .= "To: $to\n";
    $head     .= "Subject: $subj\n";
    $head     .= "X-Mailer: PHPMail Tool\n";
    $head     .= "Reply-To: $from\n";
    $head     .= "Mime-Version: 1.0\n";
    $head     .= "Content-Type:multipart/mixed;";
    $head     .= "boundary=\"----------".$un."\"\n\n";
    $zag       = "------------".$un."\nContent-Type:text/html;\n";
    $zag      .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
    $zag      .= "------------".$un."\n";
    $zag      .= "Content-Type: application/octet-stream;";
    $zag      .= "name=\"".basename($filename)."\"\n";
    $zag      .= "Content-Transfer-Encoding:base64\n";
    $zag      .= "Content-Disposition:attachment;";
    $zag      .= "filename=\"".basename($filename)."\"\n\n";
    $zag      .= chunk_split(base64_encode(fread($f,filesize($filename))))."\n";

    if (!@mail("$to", "$subj", $zag, $head))
     return 0;
    else
     return 1;
}

    static function get_lang_url()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if(strripos($url, "/ru")===0)
        {
            return 'http://'.$_SERVER['HTTP_HOST'].'/en/';
        }  
        else if(strripos($url, "/en")===0)
        {
           return 'http://'.$_SERVER['HTTP_HOST'].'/ru/';
        }
            
        
    }
    
    static function translit($insert) 
    {
        //$insert = strtolower($insert);
        //$insert = mb_strtolower($insert);    // ???????? ???????????????? ?? ???????????????????? ????????????????  
        $insert = mb_strtolower($insert,'UTF-8');      
        // ?????? ????????????-???? ???????????? ?????????????????? ?? ?????????????????? ?? ??????????????????, ?? ?????????? ???????????? strtolower ?? ???????????? ??????????, ?????? ?????????? ???????????????? ????????????
        $replase = array(
        // ??????????
        '??'=>'a',
        '??'=>'b',
        '??'=>'v',
        '??'=>'g',
        '??'=>'d',
        '??'=>'e',
        '??'=>'yo',
        '??'=>'zh',
        '??'=>'z',
        '??'=>'i',
        '??'=>'j',
        '??'=>'k',
        '??'=>'l',
        '??'=>'m',
        '??'=>'n',
        '??'=>'o',
        '??'=>'p',
        '??'=>'r',
        '??'=>'s',
        '??'=>'t',
        '??'=>'u',
        '??'=>'f',
        '??'=>'h',
        '??'=>'c',
        '??'=>'ch',
        '??'=>'sh',
        '??'=>'shh',
        '??'=>'j',
        '??'=>'y',
        '??'=>'',
        '??'=>'e',
        '??'=>'yu',
        '??'=>'ya',
        // ???????????? ?????????? ???????????????????? ?? ??????????????
        ' '=>'-',
        ' - '=>'-',
        '_'=>'-',
        //??????????????
        '.'=>'',
        ':'=>'',
        ';'=>'',
        ','=>'',
        '!'=>'',
        '?'=>'',
        '>'=>'',
        '<'=>'',
        '&'=>'',
        '*'=>'',
        '%'=>'',
        '$'=>'',
        '"'=>'',
        '\''=>'',
        '('=>'',
        ')'=>'',
        '`'=>'',
        '+'=>'',
        '/'=>'',
        '\\'=>'',
        );
        $insert=preg_replace("/  +/"," ",$insert); // ?????????????? ???????????? ??????????????
        $insert=iconv("UTF-8","UTF-8//IGNORE",strtr($insert,$replase));
        return $insert;
    }
    
    static function page_name_to_id($page_name)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT id FROM kb_pages WHERE (ru_url=? OR en_url=?) AND status=1");
        $stmt->bind_param('ss', $page_name, $page_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            return $row['id'];
        }
        $stmt->close();
        return -1;
    }
    
    static function section_name_to_id($section_name)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT id FROM kb_sections WHERE (ru_url=? OR en_url=?) AND status=1");
        $stmt->bind_param('ss', $section_name, $section_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            return $row['id'];
        }
        $stmt->close();
        return -1;
    }
}


class menu
{
    static function get_pages()
    {
        global $mysqli;
        global $lang;
        $temp = '<ul>';
        if($lang=='ru')
        $stmt = $mysqli->prepare("SELECT id, ru_url as url, ru_menu_name as name FROM kb_pages WHERE status=1 AND id>0 AND id NOT IN ('11') ORDER BY priority");
        else
        $stmt = $mysqli->prepare("SELECT id, en_url as url, en_menu_name as name FROM kb_pages WHERE status=1 AND id>0 AND id NOT IN ('11')ORDER BY priority");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            if($row['id'] == '10')
                $temp .= '<a href="/'.$lang.'/'.$row['url'].'" id="modal_button_sms"><img src="/images/sms.svg" height="25px"/></a>';
            else
                $temp .= '<a href="/'.$lang.'/'.$row['url'].'">'.$row['name'].'</a>';
        }
        $temp .= "<a href='/".$lang."/shop_cart'><img src='/images/shop.svg' height='25px'/></a>";
        $temp .= "<a href='/".$lang."/favorite'><img src='/images/favorite.png'/></a>";
        $temp .= "</ul>";
         
        $stmt->close();
        return $temp;
    }
    
    static function get_sections()
    {
        global $mysqli;
        global $lang;
        global $section_cur_id;
        $temp = '';
        if($lang=='ru')
        $stmt = $mysqli->prepare("SELECT id, ru_url as url, ru_name as name FROM kb_sections WHERE status=1");
        else
        $stmt = $mysqli->prepare("SELECT id, en_url as url, en_name as name FROM kb_sections WHERE status=1");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $class = ' ';
            if($section_cur_id == $row['id'] && $row['id'] != '6')
                $class = ' class="current_section" ';
            else if($row['id'] == '6')
            {
                if($section_cur_id == $row['id'])
                {
                    $class = ' class="sections_small_font_red" ';
                }
                else
                {
                    $class = ' class="sections_small_font" ';
                }
            }
            else
                $class = ' class="sections_simple_font" ';
            $temp .= '<li class="home menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-15" id="menu-item-15 left-menu-id"><a'.$class.'href="/'.$lang.'/'.$row['url'].'">'.$row['name'].'</a></li>';
        }
        $stmt->close();
        return $temp;
    }
}

class pages
{
    static function get_content($id)
    {
        global $mysqli;
        global $lang;
        $id = (int)$id;
        
        if($lang=='ru')
        {
            $stmt = $mysqli->prepare("SELECT id, ru_content as content FROM kb_pages WHERE id = $id");
        }
        else
        {
            $stmt = $mysqli->prepare("SELECT id, en_content as content FROM kb_pages WHERE id = $id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $temp = '';
        while ($row = $result->fetch_assoc()) 
        {
            if($row['id'] == 0)
            {
                $slider = '<div class="csa-head" style="height:400px; width: 100%; background: url(/images/photos/1.jpg) 100% 100% no-repeat; background-size: cover;"></div>';
                /*$slider = '<div id="rotator">
                              <ul>
                                <li><img src="/images/photos/1.jpg"/></li>
                                <li><img src="/images/photos/2.jpg"/></li>
                                <li><img src="/images/photos/3.jpg"/></li>
                              </ul>
                            </div>
                            <div class="clr"></div>';*/
                            
                $row['content'] = str_replace("[slider]", $slider, $row['content']);
                $temp .= $row['content'];
            }
            else if($row['id'] == 10)
            {
                if($lang=='ru')
                {
                    $temp .= '<form action="/ru/mail-to" method="post">';
                    $temp .= $row['content'];
                    $temp .= '<p>???????? ??????<br />
                    <input name="name" size="40" type="text" value="" required/></p>
                    <p>?????? ?????????? ???????????????? ?????? email</p>
                    <p><input name="phone" size="40" type="text" value="" required/></p>
                    <p>???????? ??????????????????</p>
                    <p><textarea name="message" rows="10" cols="45"></textarea>
                    <p class="send_button"><input type="submit" value="??????????????????" /></p>
                    </form>';
                }
                else
                {
                    $temp .= '<form action="/en/mail-to" method="post">';
                    $temp .= $row['content'];
                    $temp .= '<p>Your name<br />
                    <input name="name" size="40" type="text" value="" required/></p>
                    <p>Your phone or email</p>
                    <p><input name="phone" size="40" type="text" value="" required/></p>
                    <p>Your message</p>
                    <p><textarea name="message" rows="10" cols="45"></textarea></p>
                    <p class="send_button"><input type="submit" value="Send" /></p>
                    </form>
                    ';
                }
                
            }
            else
            {
                $temp .= $row['content'];
            }
        }
        $stmt->close();
        if($temp == '')
        {
            return false;
        }
        else
        {
            return $temp;          
        }
    }
    
    static function get_404()
    {
        return '<h2>404 ???????????????? ???? ??????????????</h2>
        <p>???????????????? ???? ???????????????? ?????????????? ?????? ??????-???? ?????????? ???? ??????</p>
        <a href="/">?????????????????? ???? ?????????????? ????????????????</a>';
    }
	
	static function get_thankyou()
    {
        return '<h2>?????????????? ???? ?????? ??????????!!!</h2>
        <p>?????? ???????????????? ?? ?????????????????? ?????????? ???????????????? ?? ???????? ???? ???????????? ???????????? ?? ?????????????????? ????????????.</p>
        <a href="/">?????????????????? ???? ?????????????? ????????????????</a>';
    }
    
    static function get_tkd($id)
    {
        global $mysqli;
        global $lang;
        $id = (int)$id;
        $temp = array();
        if($lang=='ru')
        {
            $stmt = $mysqli->prepare("SELECT ru_title as title, ru_description as description, ru_keywords as keywords FROM kb_pages WHERE id = $id");
        }
        else
        {
            $stmt = $mysqli->prepare("SELECT en_title as title, en_description as description, en_keywords as keywords FROM kb_pages WHERE id = $id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp['title'] = $row['title'];
            $temp['keywords'] = $row['keywords'];
            $temp['description'] = $row['description'];
        }
        $stmt->close();
        if(count($temp) == 0)
        {
            $temp['title'] = '';
            $temp['keywords'] = '';
            $temp['description'] = '';
        }
        else
        {
            return $temp;  
        }
    }
}

class sections
{
    static function get_content($id)
    {
        global $mysqli;
        global $lang;
        $temp = '';
        
        if($id=='3')
        {
            $template = file_get_contents("templates/boot_template/products_wide_stairs.html");
        }
        else if($id=='4')
        {
            $template = file_get_contents("templates/boot_template/products_wide_kamin.html");
        }
        else
        {
            $template = file_get_contents("templates/boot_template/products_tall.html");
        }
        if($lang == 'ru')
        {
           $sql = $mysqli->prepare("SELECT id,(SELECT ru_content FROM kb_sections WHERE id=section_id) as content, (SELECT ru_url FROM kb_sections WHERE id=section_id) as section_url, ru_name as name, ru_description as description, price FROM kb_products WHERE section_id=? AND status=1"); 
        }
        else
        {
            $sql = $mysqli->prepare("SELECT id,(SELECT ru_content FROM kb_sections WHERE id=section_id) as content, (SELECT en_url FROM kb_sections WHERE id=section_id) as section_url, en_name as name, en_description as description, price FROM kb_products WHERE section_id=? AND status=1");
        }
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        $first_run = true;
        while ($row = $result->fetch_assoc()) 
        {
            if($first_run)
            {
                $temp .= '<div class="section_description">'.$row['content'].'</div>';
                $first_run = false;
            }
            $get_image_sql = $mysqli->prepare("SELECT src FROM kb_images WHERE product_id=?");
            $get_image_sql->bind_param('s', $row['id']);
            $get_image_sql->execute();
            $sql_image = $get_image_sql->get_result();
            while ($image = $sql_image->fetch_assoc()) 
            {
                $row['image'] = $image['src'];
            }
            if(!isset($row['image']))
            {
                $row['image'] = '/images/no-image.jpg';
            }
                
            
            $output_html = $template;
            
            $output_html = str_replace("{{{lang}}}", $lang, $output_html);
            $output_html = str_replace("{{{img_src}}}", $row['image'], $output_html);
            $output_html = str_replace("{{{section_url}}}", $row['section_url'], $output_html);
            $output_html = str_replace("{{{id}}}", $row['id'], $output_html);
            $output_html = str_replace("{{{name}}}", $row['name'], $output_html);
            $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
            $output_html = str_replace("{{{description}}}", mb_substr($row['description'], 0, 500).'...', $output_html);
            if($lang=='ru')
            {
                $output_html = str_replace("{{{price_rub}}}", "??????.", $output_html);
                $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
                $output_html = str_replace("{{{shop_class}}}", "add_to_card_ru", $output_html);
                $output_html = str_replace("{{{to_basket}}}", "?? ??????????????", $output_html);
            }  
            else
            {
                $output_html = str_replace("{{{price_rub}}}", "rub.", $output_html);
                $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
                $output_html = str_replace("{{{shop_class}}}", "add_to_card_en", $output_html);
                $output_html = str_replace("{{{to_basket}}}", "To cart", $output_html);
            }
            $temp .= $output_html;
        }
        $sql->close();
        if($temp == '')
        {
            return false;
        }
        else
        {
            return $temp;            
        }
    }
    
    static function get_on_store()
    {
        global $mysqli;
        global $lang;
        $temp = '';
        $template = file_get_contents("templates/boot_template/products_tall.html");
        if($lang == 'ru')
        {
           $sql = $mysqli->prepare("SELECT id, (SELECT ru_url FROM kb_sections WHERE id=section_id) as section_url, ru_name as name, ru_description as description, price FROM kb_products WHERE section_id IN ('1','2','5') AND status=1 AND count>0"); 
        }
        else
        {
            $sql = $mysqli->prepare("SELECT id, (SELECT en_url FROM kb_sections WHERE id=section_id) as section_url, en_name as name, en_description as description, price FROM kb_products WHERE section_id IN ('1','2','5') AND status=1 AND count>0");
        }
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $get_image_sql = $mysqli->prepare("SELECT src FROM kb_images WHERE product_id=?");
            $get_image_sql->bind_param('s', $row['id']);
            $get_image_sql->execute();
            $sql_image = $get_image_sql->get_result();
            while ($image = $sql_image->fetch_assoc()) 
            {
                $row['image'] = $image['src'];
            }
            if(!isset($row['image']))
            {
                $row['image'] = '/images/no-image.png';
            }
                
            
            $output_html = $template;
            
            $output_html = str_replace("{{{lang}}}", $lang, $output_html);
            $output_html = str_replace("{{{img_src}}}", $row['image'], $output_html);
            $output_html = str_replace("{{{section_url}}}", $row['section_url'], $output_html);
            $output_html = str_replace("{{{id}}}", $row['id'], $output_html);
            $output_html = str_replace("{{{name}}}", $row['name'], $output_html);
            $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
            if($lang=='ru')
            {
                $output_html = str_replace("{{{price_rub}}}", "??????.", $output_html);
                $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
                $output_html = str_replace("{{{shop_class}}}", "add_to_card_ru", $output_html);
                $output_html = str_replace("{{{to_basket}}}", "?? ??????????????", $output_html);
            }  
            else
            {
                $output_html = str_replace("{{{price_rub}}}", "rub.", $output_html);
                $output_html = str_replace("{{{price}}}", $row['price'], $output_html);
                $output_html = str_replace("{{{shop_class}}}", "add_to_card_en", $output_html);
                $output_html = str_replace("{{{to_basket}}}", "To cart", $output_html);
            }
            $temp .= $output_html;
        }
        $sql->close();
        if($temp == '')
        {
            return false;
        }
        else
        {
            return $temp;            
        }
    }
    
    static function get_tkd($id)
    {
        global $mysqli;
        global $lang;
        $id = (int)$id;
        $temp = array();
        if($lang=='ru')
        {
            $stmt = $mysqli->prepare("SELECT ru_title as title, ru_description as description, ru_keywords as keywords FROM kb_sections WHERE id = $id");
        }
        else
        {
            $stmt = $mysqli->prepare("SELECT en_title as title, en_description as description, en_keywords as keywords FROM kb_sections WHERE id = $id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp['title'] = $row['title'];
            $temp['keywords'] = $row['keywords'];
            $temp['description'] = $row['description'];
        }
        $stmt->close();
        if(count($temp) == 0)
        {
            $temp['title'] = '';
            $temp['keywords'] = '';
            $temp['description'] = '';
        }
        else
        {
            return $temp;  
        }
    }
}


class products
{
    static function get_content($id)
    {
        global $mysqli;
        global $lang;
        $temp = '';
        if($lang == 'ru')
        {
            $sql = $mysqli->prepare("SELECT id, ru_content as content, ru_name as name, ru_description as description, image, price FROM kb_products WHERE status=1 AND id=?");
        }
        else
        {
            $sql = $mysqli->prepare("SELECT id, en_content as content, en_name as name, en_description as description, image, price FROM kb_products WHERE status=1 AND id=?");
        }
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '<div class="item">
            <h2>'.$row['name'].'</h2>';
            if($lang=='ru')
                $temp .= '<a href="#" data-product-id="'.$row['id'].'" id="add-to-favorite-ru"><img src="/images/favorite.png"/> ???????????????? ?? ??????????????????</a>';
            else
                $temp .= '<a href="#" data-product-id="'.$row['id'].'" id="add-to-favorite-en"><img src="/images/favorite.png"/>Add to favorite</a>';
            $temp .= '<p id="block-777"></p>';
            
            $sql_images = $mysqli->prepare("SELECT src FROM kb_images WHERE product_id=?");
            $sql_images->bind_param('s', $row['id']);
            $sql_images->execute();
            $images_src = $sql_images->get_result();
            
            $images ='';
            $first_img = true;
            while ($img = $images_src->fetch_assoc()) 
            {
                if($first_img)
                {
                    $images .= '<div id="product_images"><img height="400px" src="'.$img['src'].'"></div>
                                <nav id="gallery_nav">
                                  <a href="'.$img['src'].'" class="current"><img height="100px" src="'.$img['src'].'"></a>';
                                  $first_img = false;
                }
                else
                    $images .= '<a href="'.$img['src'].'"><img height="100px" src="'.$img['src'].'"></a>';
            }
            $images .='</nav>';
            
            $temp .= $images;
            $temp .= '<p>'.$row['content'].'</p>
            <p>'.$row['price'].'</p>
            </div>';
        }
        $sql->close();
        if($temp == '')
            return false;
        else
            return $temp;            
    }
    
    static function get_tkd($id)
    {
        global $mysqli;
        global $lang;
        $id = (int)$id;
        $temp = array();
        if($lang=='ru')
        {
            $stmt = $mysqli->prepare("SELECT ru_title as title, ru_description as description, ru_keywords as keywords FROM kb_sections WHERE id = $id");
        }
        else
        {
            $stmt = $mysqli->prepare("SELECT en_title as title, en_description as description, en_keywords as keywords FROM kb_sections WHERE id = $id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp['title'] = $row['title'];
            $temp['keywords'] = $row['keywords'];
            $temp['description'] = $row['description'];
        }
        $stmt->close();
        if(count($temp) == 0)
        {
            $temp['title'] = '';
            $temp['keywords'] = '';
            $temp['description'] = '';
        }
        else
        {
            return $temp;  
        }
    }
}

class favorites
{
    static function get_content()
    {
        global $mysqli;
        global $lang;
        
        $temp = '';
        $favorites_cookie = '';
        
        if (isset($_COOKIE['favorites-list']) && !empty($_COOKIE['favorites-list'])) 
        {
            $favorites_cookie = $_COOKIE['favorites-list'];
        }
        if($favorites_cookie == '')
            return false;
        
        $favorites_cookie = mb_substr($favorites_cookie, 0, -1);
        
        if($lang == 'ru')
        {
           $sql = $mysqli->prepare("SELECT id, (SELECT ru_url FROM kb_sections WHERE id=section_id) as section_url, ru_name as name, ru_description as description, image, price FROM kb_products WHERE id in (".$favorites_cookie.") AND status=1"); 
        }
        else
        {
            $sql = $mysqli->prepare("SELECT id, (SELECT en_url FROM kb_sections WHERE id=section_id) as section_url, en_name as name, en_description as description, image, price FROM kb_products WHERE id in (".$favorites_cookie.") AND status=1");
        }
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '
                <div class="product_block">
                <img src="'.$row['image'].'" width="200" height="200"  onclick="window.location.href=`/'.$lang.'/'.$row['section_url'].'/'.$row['id'].'`; return false">
                <h3  onclick="window.location.href=`/'.$lang.'/'.$row['section_url'].'/'.$row['id'].'`; return false">'.$row['name'].'</h3>
                <a href="#" class="add-to-cart" tid="'.$row['id'].'">?? ??????????????</a><br>
                <strong>'.$row['price'].' ??????.</strong></div>';
        }
        $sql->close();
        if($temp == '') return false;
        else return $temp;            
    }
    
    static function add()
    {
        $product_id = $_GET['product_id'];
        if(isset($_COOKIE['favorites-list']) && !empty($_COOKIE['favorites-list']))
        $value = $_COOKIE['favorites-list']."'".$product_id."',";
        else
        {
            $value = "'".$product_id."',";
        }
        setcookie("favorites-list", $value, time() + 360000);
        echo "??????????!!!";
        exit; 
    }
}

class shop_cart
{
    static function get_content()
    {
        global $mysqli;
        global $lang;
        $cart_items = '';
        
        if(!isset($_SESSION['cart']))
        {
            $_SESSION['cart'] = array();
        }
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart']))
        {
            foreach ($_SESSION['cart'] as $id => $count) 
            {
                $cart_items .= "'".$id."',";
            }
            
            $cart_items = mb_substr($cart_items, 0, -1);
            
            if($lang == 'ru')
            {
                $temp = "<div class='content-box-large'>
                <div class='panel-heading'>
                    <div class='panel-title'>Border Table</div>
                </div>
                <div class='panel-body'>
                <table class='table table-bordered'>
                <thead>
                <tr>
                <th>????????</th>
                <th>????????????????????????</th>
                <th>????????</th>
                <th>????????????????????</th>
                <th>??????????</th>
                </tr>
                </thead>";
                $sql = $mysqli->prepare("SELECT id, ru_name as name, image, price FROM kb_products WHERE status=1 AND id in (".$cart_items.")");
            }
            else
            {
                $temp = "<div class='content-box-large'>
                <div class='panel-heading'>
                    <div class='panel-title'>Border Table</div>
                </div>
                <div class='panel-body'>
                <table class='table table-bordered'>
                <thead>
                <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Price</th>
                <th>Count</th>
                <th>Sum</th>
                </tr>
                </thead>
                <tbody>";
                $sql = $mysqli->prepare("SELECT id, en_name as name, image, price FROM kb_products WHERE status=1 AND id in (".$cart_items.")");
            }
            $sql->execute();
            $result = $sql->get_result();
			$row_info = array();
            while ($row = $result->fetch_assoc()) 
            {
                $product_id = $row['id'];
                
                $price = str_replace(" ","",$row['price']);
                $row['count'] = $_SESSION['cart'][$product_id]; 
                $sum = $price * $row['count'];
                
                $temp .= "<tr>
                            <td><img src='".$row['image']."' width='100px'/></td>
                            <td>$row[name]</td>
                            <td>$row[price]</td>
                            <td>$row[count]</td>
                            <td>$sum</td>
                        </tr>";
						$row_info = $row;
            }
            $sql->close();
            $temp .= "</tbody>
                    </table>
					
					<form action='/kb-admin/?module=crm&action=add' method='POST'>
						<input type='hidden' name='client_id' value='".gaParseCookie()."'>
						<input type='hidden' name='product_id' value='$row_info[id]'>
						<input type='hidden' name='price' value='$row_info[price]'>
						<input type='hidden' name='status' value='lead'>
						<input type='text' name='fio' placeholder='??????'>
						<input type='phone' name='phone' placeholder='??????????????'>
						<input type='email' name='email' placeholder='??????????'>
						<input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`?????????????????`);'>
					</form>
								
                    </div>
                    </div>";
            return $temp;
        }
        else
        {
            if($lang=='ru')
                $temp = "<h2>?????????????? ??????????</h2>";
            else
                $temp = "<h2>Your shop cart is empty</h2>";
            return $temp;
        }
                    
    }
    
    static function add($product_id)
    {
        $product_id = (int)$product_id;
        if(isset($_SESSION['cart'][$product_id]))
        {
            $_SESSION['cart'][$product_id] += 1;
            var_dump($product_id);
            return print_r($_SESSION);
        }
        else
        {
            var_dump($product_id);
            $_SESSION['cart'][$product_id] = 1;
            return print_r($_SESSION);  
        }
            
    }
}
#####################################################################
//???????????? ?????????????? ????????????????????????????
#####################################################################
class admin_pages
{
    function get_list()
    {
        global $mysqli;
        $temp = "<p><a href='/kb-admin/?module=pages&action=add'>???????????????? ????????????????</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>????</th>
        <th>??????????????????</th>
        <th>?? ????????</th>
        <th>????????????</th>
        </tr>
        </thead>";
        $sql = $mysqli->prepare("SELECT id, ru_title as title, ru_menu_name as menu_name, status FROM kb_pages");
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            if($row['status'] == 1)
            {
                $row['status'] = '+';
            }
            else
            {
                $row['status'] = '-';
            }
            $temp .= "<tr onclick='window.location.href=`/kb-admin/?module=pages&action=edit&id=$row[id]`; return false'>
						<td>$row[id]</td>
						<td>$row[title]</td>
						<td>$row[menu_name]</td>
                        <td>$row[status]</td>
					</tr>";
        }
        $sql->close();
        $temp .= "</table>";
        return $temp;
    }
    
    function add_form()
    {
        return "<div class='forma'>
        <form action='/kb-admin/?module=pages&action=add' method='POST'>
        <h2>?????????????? ??????????????</h2>
        <p>??????????????????: <input type='text' name='ru_title'></p>
        <p>?????? ?? ????????: <input type='text' name='ru_menu_name'></p>
        <div class='block'>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description'></p>
        </div>
        <p>??????????????: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>???????????????????? ??????????????</h2>
        <p>??????????????????: <input type='text' name='en_title'></p>
        <p>?????? ?? ????????: <input type='text' name='en_menu_name'></p>
        <div class='block'>
            <p>???????????????? ??????????: <input type='text' name='en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description'></p>
        </div>
        <p>??????????????: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor2
            });
        </script></p>
        <p>????????????????????????: <input type='checkbox' name='status' value='1'></p>
            <p>?????????????????? ?? ????????: <input type='number' name='priority'></p>
        <input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`???????????????? ?????????? ?????????????????`);'>
        </form>
        </div>";
    }
    
    function add_submit()
    {
        $ru_title = $_POST['ru_title'];
        $ru_menu_name = $_POST['ru_menu_name'];
        $ru_keywords = $_POST['ru_keywords'];
        $ru_description = $_POST['ru_description'];
        $ru_content = html_entity_decode($_POST['ru_content']);
        
        $en_title = $_POST['en_title'];
        $en_menu_name = $_POST['en_menu_name'];
        $en_keywords = $_POST['en_keywords'];
        $en_description = $_POST['en_description'];
        $en_content = html_entity_decode($_POST['en_content']);
        
        $ru_url = main::translit($ru_menu_name);
        $en_url = main::translit($en_menu_name);
        
        $status = $_POST['status'];
        $priority = $_POST['priority'];
        
        global $mysqli;
        $sql = $mysqli->prepare("INSERT INTO kb_pages (ru_title, ru_menu_name, ru_keywords, ru_description, ru_content, en_title, en_menu_name, en_keywords, en_description, en_content, status, priority, ru_url, en_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $sql->bind_param('ssssssssssssss', $ru_title, $ru_menu_name, $ru_keywords, $ru_description, $ru_content, $en_title, $en_menu_name, $en_keywords, $en_description, $en_content, $status, $priority, $ru_url, $en_url);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=pages");
    }
        
    function edit_form($id)
    {
        global $mysqli;
        $temp = "";
        $sql = $mysqli->prepare("SELECT * FROM kb_pages WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $id = $row['id'];
            
            $ru_title = $row['ru_title'];
            $ru_menu_name = $row['ru_menu_name'];
            $ru_keywords = $row['ru_keywords'];
            $ru_description = $row['ru_description'];
            $ru_content = html_entity_decode($row['ru_content']);
            
            $en_title = $row['en_title'];
            $en_menu_name = $row['en_menu_name'];
            $en_keywords = $row['en_keywords'];
            $en_description = $row['en_description'];
            $en_content = html_entity_decode($row['en_content']);
            $status = $row['status'];
            $priority = $row['priority'];
        }
        $temp = "<div class='forma'>
        <form action='/kb-admin/?module=pages&action=edit' method='POST'>
        <input type='hidden' name='id' value='$id'>
        <p><a href='/kb-admin/?module=pages&action=delete&id=$id' onClick= 'return window.confirm(`???? ??????????????, ?????? ???????????? ?????????????? ?????????????????`);'><span>?????????????? ????????????????</span></a></p>
        <h2>?????????????? ??????????????</h2>
        <p>??????????????????: <input type='text' name='ru_title' value='$ru_title'></p>
        <p>?????? ?? ????????: <input type='text' name='ru_menu_name' value='$ru_menu_name'></p>
        <div class='block'>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        <p>??????????????: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'>$ru_content</textarea>
        <script type='text/javascript'>
            var ru_content_editor = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ru_content_editor
            });
        </script></p>
        
        <h2>???????????????????? ??????????????</h2>
        <p>??????????????????: <input type='text' name='en_title' value='$en_title'></p>
        <p>?????? ?? ????????: <input type='text' name='en_menu_name' value='$en_menu_name'></p>
        <div class='block'>
            <p>???????????????? ??????????: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description' value='$en_description'></p>
        </div>
        <p>??????????????: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'>$en_content</textarea>
        <script type='text/javascript'>
            var en_content_editor = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: en_content_editor
            });
        </script></p>";
        if($status =='1')
        {
            $temp .= "<p>????????????????????????: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>????????????????????????: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= "<p>?????????????????? ?? ????????: <input type='number' name='priority' value='$priority'></p>
        <input type='submit' value='????????????????' name='submit'  onClick= 'return window.confirm(`???? ??????????????, ?????? ???????????? ?????????????????? ?????????????????? ?? ???????? ?????????????????`);'>
        </form>
        </div>";
        return $temp;
    }
    
    function edit_submit()
    {
        $id = $_POST['id'];
            
        $ru_title = $_POST['ru_title'];
        $ru_menu_name = $_POST['ru_menu_name'];
        $ru_keywords = $_POST['ru_keywords'];
        $ru_description = $_POST['ru_description'];
        $ru_content = html_entity_decode($_POST['ru_content']);
        
        $en_title = $_POST['en_title'];
        $en_menu_name = $_POST['en_menu_name'];
        $en_keywords = $_POST['en_keywords'];
        $en_description = $_POST['en_description'];
        $en_content = html_entity_decode($_POST['en_content']);
        
        $status = $_POST['status'];
        $priority = $_POST['priority'];
        
        $ru_url = main::translit($_POST['ru_menu_name']);
        $en_url = main::translit($_POST['en_menu_name']);
       
        global $mysqli;
        $sql = $mysqli->prepare("UPDATE kb_pages SET ru_title=?, ru_menu_name=?, ru_keywords=?, ru_description=?, ru_content=?, en_title=?, en_menu_name=?, en_keywords=?, en_description=?, en_content=?, status=?, priority=?, ru_url=?, en_url=? WHERE id=?");
        $sql->bind_param('sssssssssssssss', $ru_title, $ru_menu_name, $ru_keywords, $ru_description, $ru_content, $en_title, $en_menu_name, $en_keywords, $en_description, $en_content, $status, $priority, $ru_url, $en_url, $id);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=pages");
    }
    
    function delete($id)
    {
        global $mysqli;
        $sql = $mysqli->prepare("DELETE FROM kb_pages WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        header("Location:/kb-admin/?module=pages");
    }
}
#####################################################################
//???????????? ???????????????? ????????????????????????????
#####################################################################
class admin_sections
{
    function get_list()
    {
        global $mysqli;
        $temp = "<p><a href='/kb-admin/?module=sections&action=add'>???????????????? ????????????</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>????</th>
        <th>??????</th>
        <th>????????????</th>
        </tr>
        </thead>";
        $sql = $mysqli->prepare("SELECT id, ru_name as name, status FROM kb_sections");
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            if($row['status'] == 1)
            {
                $row['status'] = '+';
            }
            else
            {
                $row['status'] = '-';
            }
            $temp .= "<tr onclick='window.location.href=`/kb-admin/?module=sections&action=edit&id=$row[id]`; return false'>
                        <td>$row[id]</td>
                        <td>$row[name]</td>
                        <td>$row[status]</td>
                    </tr>";
        }
        $sql->close();
        $temp .= "</table>";
        return $temp;
    }
    
    function add_form()
    {
        return "<div class='forma'>
        <form action='/kb-admin/?module=sections&action=add' method='POST'>
        <h2>?????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='ru_name'></p> 
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='ru_title'></p>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description'></p>
        </div>
        
        <h2>???????????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='en_name'></p>
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='en_title'></p>
            <p>???????????????? ??????????: <input type='text' name='en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description'></p>
        </div>
        
        <p>????????????????????????: <input type='checkbox' name='status' value='1'></p>
        <input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`???????????????? ?????????? ??????????????`);'>
        </form>
        </div>";
    }
    
    function add_submit()
    {
        $ru_title = $_POST['ru_title'];
        $ru_name = $_POST['ru_name'];
        $ru_keywords = $_POST['ru_keywords'];
        $ru_description = $_POST['ru_description'];
        
        $en_title = $_POST['en_title'];
        $en_name = $_POST['en_name'];
        $en_keywords = $_POST['en_keywords'];
        $en_description = $_POST['en_description'];
        
        $status = $_POST['status'];
        
        $ru_url = main::translit($ru_name);
        $en_url = main::translit($en_name);
        
        global $mysqli;
        $sql = $mysqli->prepare("INSERT INTO kb_sections (ru_title, ru_name, ru_keywords, ru_description, en_title, en_name, en_keywords, en_description, status, ru_url, en_url) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $sql->bind_param('sssssssssss', $ru_title, $ru_name, $ru_keywords, $ru_description, $en_title, $en_name, $en_keywords, $en_description, $status, $ru_url, $en_url);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=sections");
    }
        
    function edit_form($id)
    {
        global $mysqli;
        $temp = "";
        $sql = $mysqli->prepare("SELECT * FROM kb_sections WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $id = $row['id'];
            
            $ru_title = $row['ru_title'];
            $ru_name = $row['ru_name'];
            $ru_keywords = $row['ru_keywords'];
            $ru_description = $row['ru_description'];
            
            $en_title = $row['en_title'];
            $en_name = $row['en_name'];
            $en_keywords = $row['en_keywords'];
            $en_description = $row['en_description'];
            
            $status = $row['status'];
            $priority = $row['priority'];
        }
        $temp = "<div class='forma'>
        <form action='/kb-admin/?module=sections&action=edit' method='POST'>
        <input type='hidden' name='id' value='$id'>
        <p><a href='/kb-admin/?module=sections&action=delete&id=$id' onClick= 'return window.confirm(`???? ??????????????, ?????? ???????????? ?????????????? ?????????????`);'><span>?????????????? ????????????</span></a></p>
        <h2>?????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='ru_name' value='$ru_name'></p> 
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='ru_title' value='$ru_title'></p>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        
        <h2>???????????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='en_name' value='$en_name'></p>
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='en_title' value='$en_title'></p>
            <p>???????????????? ??????????: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description' value='$en_description'></p>
        </div>";
        if($status =='1')
        {
            $temp .= "<p>??????????????????????: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>??????????????????????: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= "<input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`???????????????? ??????????????`);'>
        </form>
        </div>";
        return $temp;
    }
    
    function edit_submit()
    { 
        $id = $_POST['id'];
            
            $ru_title = $_POST['ru_title'];
            $ru_name = $_POST['ru_name'];
            $ru_keywords = $_POST['ru_keywords'];
            $ru_description = $_POST['ru_description'];
            
            $en_title = $_POST['en_title'];
            $en_name = $_POST['en_name'];
            $en_keywords = $_POST['en_keywords'];
            $en_description = $_POST['en_description'];
              
            $status = $_POST['status'];
            
            $ru_url = main::translit($ru_name);
            $en_url = main::translit($en_name);
       
        global $mysqli;
        $sql = $mysqli->prepare("UPDATE kb_sections SET ru_title=?, ru_name=?, ru_keywords=?, ru_description=?, en_title=?, en_name=?, en_keywords=?, en_description=?, status=?, ru_url=?, en_url=? WHERE id=?");
        $sql->bind_param('ssssssssssss', $ru_title, $ru_name, $ru_keywords, $ru_description, $en_title, $en_name, $en_keywords, $en_description, $status, $ru_url, $en_url, $id);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=sections");
    }
    
    function delete($id)
    {
        global $mysqli;
        $sql = $mysqli->prepare("DELETE FROM kb_sections WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        header("Location:/kb-admin/?module=sections");
    }
}
#####################################################################
//???????????? ?????????????? ????????????????????????????
#####################################################################
class admin_products
{
    function get_list()
    {
        global $mysqli;
        
        $temp = "<p><a href='/kb-admin/?module=products&action=add'>???????????????? ??????????????</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>????</th>
        <th>??????</th>
        <th>????????????</th>
        <th>????????????</th>
        </tr>
        </thead>";
        $sql = $mysqli->prepare("SELECT id, ru_name as name, (SELECT ru_name FROM kb_sections WHERE id=section_id) as section, status FROM kb_products");
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            if($row['status'] == 1)
            {
                $row['status'] = '+';
            }
            else
            {
                $row['status'] = '-';
            }
            $temp .= "<tr onclick='window.location.href=`/kb-admin/?module=products&action=edit&id=$row[id]`; return false'>
                        <td>$row[id]</td>
                        <td>$row[name]</td>
                        <td>$row[section]</td>
                        <td>$row[status]</td>
                    </tr>";
        }
        $sql->close();
        $temp .= "</table>";
        return $temp;
    }
    
    function add_form()
    {
        $temp = "<div class='forma'>
        <form action='/kb-admin/?module=products&action=add' method='POST'>
        <h2>?????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='ru_name'></p> 
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='ru_title'></p>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description'></p>
        </div>
        <p>??????????????: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>???????????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='en_name'></p>
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='en_title'></p>
            <p>???????????????? ??????????: <input type='text' name='en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description'></p>
        </div>
        <p>??????????????: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>";
        global $mysqli;
        $option = "<select name='section_id'>
        <option disabled>???????????????? ????????????</option>
        <option value='0'>???????????? ???? ??????????</option>";
        $sql_option = $mysqli->prepare("SELECT id, ru_name as name FROM kb_sections");
        $sql_option->execute();
        $result_option = $sql_option->get_result();
        while ($row = $result_option->fetch_assoc()) 
        {
            $option .=  '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
        $option .= "</select>";
        $temp.= $option;
        $temp.= "<p>????????(?? ????????????): <input type='text' name='price'></p>
                <p>????????????????????????: <input type='checkbox' name='status' value='1'></p>
                <input type='submit' value='????????????????' name='submit'>
                </form>
                </div>";
                return $temp;
    }
    
    function add_submit()
    {
        $ru_title = $_POST['ru_title'];
        $ru_name = $_POST['ru_name'];
        $ru_keywords = $_POST['ru_keywords'];
        $ru_description = $_POST['ru_description'];
        $ru_content = html_entity_decode($_POST['ru_content']);
        
        $en_title = $_POST['en_title'];
        $en_name = $_POST['en_name'];
        $en_keywords = $_POST['en_keywords'];
        $en_description = $_POST['en_description'];
        $en_content = html_entity_decode($_POST['en_content']);
        
        $status = $_POST['status'];
        $price = $_POST['price'];
        $section_id = $_POST['section_id'];
        
        global $mysqli;
        $sql = $mysqli->prepare("INSERT INTO kb_products (ru_title, ru_name, ru_keywords, ru_description, ru_content, en_title, en_name, en_keywords, en_description, en_content, status, price, section_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $sql->bind_param('sssssssssssss', $ru_title, $ru_name, $ru_keywords, $ru_description, $ru_content, $en_title, $en_name, $en_keywords, $en_description, $en_content, $status, $price, $section_id);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=products");
    }
        
    function edit_form($id)
    {
        global $mysqli;
        $temp = "";
        $sql = $mysqli->prepare("SELECT * FROM kb_products WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $id = $row['id'];
            
            $ru_title = $row['ru_title'];
            $ru_name = $row['ru_name'];
            $ru_keywords = $row['ru_keywords'];
            $ru_description = $row['ru_description'];
            $ru_content = html_entity_decode($row['ru_content']);
            
            $en_title = $row['en_title'];
            $en_name = $row['en_name'];
            $en_keywords = $row['en_keywords'];
            $en_description = $row['en_description'];
            $en_content = html_entity_decode($row['en_content']);
            
            $status = $row['status'];
            $price = $row['price'];
            $section_id = $row['section_id'];
        }
        
        $option = "<select name='section_id'>
        <option disabled>???????????????? ????????????</option>
        <option value='0'>???????????? ???? ??????????</option>";
        $sql_option = $mysqli->prepare("SELECT id, ru_name as name FROM kb_sections");
        $sql_option->execute();
        $result_option = $sql_option->get_result();
        while ($row = $result_option->fetch_assoc()) 
        {
            if($row['id'] == $section_id)
                $option .=  '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
            else
                $option .=  '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
        $option .= "</select>";
        
        
        $temp = "<div class='forma'>
        <form action='/kb-admin/?module=products&action=edit' method='POST'>
        <p><a href='/kb-admin/?module=products&action=delete&id=$id' onClick= 'return window.confirm(`???? ??????????????, ?????? ???????????? ?????????????? ?????? ???????????????`);'><span>?????????????? ??????????????</span></a></p>
        <h2>?????????????? ??????????????</h2>
        <input type='hidden' name='id' value='$id'>
        <p>????????????????: <input type='text' name='ru_name' value='$ru_name'></p> 
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='ru_title' value='$ru_title'></p>
            <p>???????????????? ??????????: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>????????????????: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        <p>??????????????: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'>$ru_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>???????????????????? ??????????????</h2>
        <p>????????????????: <input type='text' name='en_name' value='$en_name'></p>
        <div class='block'>
            <p>?????????????????? ????????????????: <input type='text' name='en_title' value='$en_title'></p>
            <p>???????????????? ??????????: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>????????????????: <input type='text' name='en_description' value='$en_description'></p>
        </div>
        <p>??????????????: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'>$en_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        <p>????????(?? ????????????): <input type='text' name='price' value='$price'></p>";
        if($status =='1')
        {
            $temp .= "<p>????????????????????????: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>????????????????????????: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= $option;
        $temp .= "<p><input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`???????????????? ???????????????`);'></p>
        </form>
        </div>";
        return $temp;
    }
    
    function edit_submit()
    { 
        $id = $_POST['id'];
        
        $ru_title = $_POST['ru_title'];
        $ru_name = $_POST['ru_name'];
        $ru_keywords = $_POST['ru_keywords'];
        $ru_description = $_POST['ru_description'];
        $ru_content = html_entity_decode($_POST['ru_content']);
        
        $en_title = $_POST['en_title'];
        $en_name = $_POST['en_name'];
        $en_keywords = $_POST['en_keywords'];
        $en_description = $_POST['en_description'];
        $en_content = html_entity_decode($_POST['en_content']);
        
        $status = $_POST['status'];
        $price = $_POST['price'];
        $section_id = $_POST['section_id'];
       
        global $mysqli;
        $sql = $mysqli->prepare("UPDATE kb_products SET ru_title=?, ru_name=?, ru_keywords=?, ru_description=?, ru_content=?, en_title=?, en_name=?, en_keywords=?, en_description=?, en_content=?, status=?, price=?, section_id=? WHERE id=?");
        $sql->bind_param('ssssssssssssss', $ru_title, $ru_name, $ru_keywords,$ru_description, $ru_content, $en_title, $en_name, $en_keywords, $en_description, $en_content, $status, $price, $section_id, $id);
        $sql->execute();
        $result = $sql->get_result();
        header("Location:/kb-admin/?module=products");
    }
    
    function delete($id)
    {
        global $mysqli;
        $sql = $mysqli->prepare("DELETE FROM kb_products WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        header("Location:/kb-admin/?module=products");
    }
}

class admin_menu
{
    function get()
    {
        $temp = '';
        
$temp .= '<li class="current"><a href="/kb-admin/?module=pages"><i class="glyphicon glyphicon-file"></i>????????????????</a></li>';
$temp .= '<li class="current"><a href="/kb-admin/?module=sections"><i class="glyphicon glyphicon-th-list"></i>??????????????</a></li>';
$temp .= '<li class="current"><a href="/kb-admin/?module=products"><i class="glyphicon glyphicon-th"></i>??????????????</a></li>';
$temp .= '<li class="current"><a href="/kb-admin/?module=crm"><i class="glyphicon glyphicon-th"></i>CRM</a></li>';

        echo $temp;
    }
}




















#####################################################################
//???????????? CRM
#####################################################################
class Crm
{
    function get_table()
    {
        global $mysqli;
        
        $temp = "<table class='table'>
        <thead>
        <tr>
			<th>????</th>
			<th>??????</th>
			<th>????????</th>
			<th>??????????????</th>
			<th>??????????</th>
			<th>client_id</th>
			<th>????????</th>
			<th>????????????</th>
			<th>???????????????? ????????????</th>
        </tr>
        </thead>";
        $sql = $mysqli->prepare("SELECT * FROM crm");
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= "<tr>
                        <td>$row[id]</td>
                        <td>$row[fio]</td>
                        <td>$row[date]</td>
                        <td>$row[phone]</td>
						<td>$row[email]</td>
                        <td>$row[client_id]</td>
                        <td>$row[price]</td>
                        <td>$row[status]</td>
						<td>
							<form action='/kb-admin/?module=crm&action=edit' method='POST'>
								<input type='hidden' name='id' value='$row[id]'>
								<select name='status'>
								   <option value='lead'>?????????????? ??????????</option>
								   <option value='contract'>???????????????????? ??????????</option>
								   <option value='pay'>?????????????? ??????????</option>
								   <option value='unpay'>?????????????? ??????????</option>
							   </select>
								<input type='submit' value='????????????????' name='submit' onClick= 'return window.confirm(`?????????????????`);'>
							</form>
							<form action='/kb-admin/?module=crm&action=delete' method='POST'>
								<input type='hidden' name='id' value='$row[id]'>
								<input type='submit' value='??????????????' name='submit' onClick= 'return window.confirm(`???????????????`);'>
							</form>
						</td>
                    </tr>";
        }
        $sql->close();
        $temp .= "</table>";
        return $temp;
    }
    
    function add($status='lead')
    {
        $client_id = $_POST['client_id'];
        $product_id = $_POST['product_id'];
        $fio = $_POST['fio'];
        $phone = $_POST['phone'];
		$email = $_POST['email'];
		$price = $_POST['price'];
        
        global $mysqli;
        $sql = $mysqli->prepare("INSERT INTO crm (client_id, product_id, fio, phone, email, price, status) VALUES (?,?,?,?,?,?,?)");
        $sql->bind_param('sssssss', $client_id, $product_id, $fio, $phone, $email, $price, $status);
        $sql->execute();
        $result = $sql->get_result();
		$insert_id = mysqli_insert_id;
		
		//$GMP = new GMP();
		//$GMP->AddParam('v', '1');
		//$GMP->AddParam('tid', 'UA-101501856-1');
		//$GMP->AddParam('cid', gaParseCookie());
		//$GMP->AddParam('cid', "fsdfdsfsd");
		//$GMP->AddParam('t', 'event');
		//$GMP->Send();
		
		$data = array();
		$data['cid'] = gaParseCookie();
		$data['t'] = 'event';
		$data['ec'] = "lead";
		$data['ea'] = "site";
		$data['el'] = "form";
		$data['cd1'] = $fio;
		$data['cd2'] = $product_id;
		$data['z'] = 666;
		GMP($data);
		


        header("Location:/ru/thankyou");
    }
  
    
    function edit()
    { 
        $id = $_POST['id'];
        $status = $_POST['status'];
		var_dump($status);
        global $mysqli;
        $sql = $mysqli->prepare("UPDATE crm SET status=? WHERE id=?");
        $sql->bind_param('ss', $status, $id);
        $sql->execute();
        $result = $sql->get_result();
		
		$sql = $mysqli->prepare("SELECT * FROM crm WHERE id=?");
		$sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
		$contract = array();
		
        while ($row = $result->fetch_assoc()) 
        {
			$contract = $row;
		}
		
		
		
		$sql9 = $mysqli->prepare("SELECT price, ru_name, (SELECT ru_name FROM kb_sections WHERE id=section_id) as section FROM kb_products WHERE id=?");
		$sql9->bind_param('s', $contract['product_id']);
        $sql9->execute();
        $result = $sql9->get_result();
		$product = array();
		
        while ($row = $result->fetch_assoc()) 
        {
			$product = $row;
		}
		
		if($status == 'contract')
		{
			$data = array();
			$data['cid'] = $contract['clientid'];
			$data['t'] = 'event';
			$data['ec'] = "contract";
			$data['ea'] = "create";
			$data['el'] = $insert_id;
			$data['cd1'] = $fio;
			$data['cd2'] = $product_id;
			$data['z'] = 666;
			GMP($data);
		}
		else if($status == 'pay')
		{
			$data = array();
			$data['cid'] = $contract['clientid'];
			$data['t'] = 'transaction';
			$data['ti'] = $contract['id'];
			$data['ta'] = "Moscow";
			$data['tr'] = $contract['price'];
			$data['cu'] = "EUR";
			$data['z'] = 666;
			GMP($data);

			$data = array();
			$data['cid'] = $contract['clientid'];
			$data['t'] = 'item';
			$data['ti'] = $contract['id'];
			$data['in'] = $product['ru_name'];
			$data['ip'] = $product['price'];
			$data['iq'] = 1;
			$data['ic'] = md5($product['ru_name']);
			$data['iv'] = $product['section'];
			$data['cu'] = "EUR";
			$data['z'] = 666;
			GMP($data);
		}
		else if($status == 'unpay')
		{
			$data = array();
			$data['cid'] = $contract['clientid'];
			$data['t'] = 'transaction';
			$data['ti'] = $contract['id'];
			$data['ta'] = "Moscow";
			$data['tr'] = '-'.$contract['price'];
			$data['cu'] = "EUR";
			$data['z'] = 666;
			GMP($data);

			$data = array();
			$data['cid'] = $contract['clientid'];
			$data['t'] = 'item';
			$data['ti'] = $contract['id'];
			$data['in'] = $product['ru_name'];
			$data['ip'] = '-'.$product['price'];
			$data['iq'] = 1;
			$data['ic'] = md5($product['ru_name']);
			$data['iv'] = $product['section'];
			$data['cu'] = "EUR";
			$data['z'] = 666;
			GMP($data);
		}
        header("Location:/kb-admin/?module=crm");
    }
    
    function delete()
    {
		$id = $_POST['id'];
        global $mysqli;
        $sql = $mysqli->prepare("DELETE FROM crm WHERE id=?");
        $sql->bind_param('s', $id);
        $sql->execute();
        header("Location:/kb-admin/?module=crm");
    }
}

function GMP($data)
{
		$data['v'] = 1;
		$data['tid'] = 'UA-101501856-1';
		
		$url = 'http://www.google-analytics.com/collect';
		$content = http_build_query($data);
		$content = utf8_encode($content);
		$user_agent = 'Example/1.0 (http://example.com/)';

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
		curl_setopt($ch,CURLOPT_POST, TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $content);
		curl_exec($ch);
		curl_close($ch);
}
?>