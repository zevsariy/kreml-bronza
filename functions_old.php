<?php
$mysqli = new mysqli("localhost", "035848010_devkb", "12345678", "zevsariy_devkb");

class main
{
    static function get_lang_url()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if(strripos($url, "/ru/")===0)
        {
            return 'http://'.$_SERVER['HTTP_HOST'].'/en/';
        }  
        else if(strripos($url, "/en/")===0)
        {
           return 'http://'.$_SERVER['HTTP_HOST'].'/ru/';
        }
            
        
    }
    
    static function translit($insert) 
    {
        //$insert = strtolower($insert);
        //$insert = mb_strtolower($insert);    // ���� �������� � ���������� ��������  
        $insert = mb_strtolower($insert,'UTF-8');      
        // ��� ������-�� ������ ��������� � ��������� � ���������, � ����� ������ strtolower � ������ �����, ��� ����� �������� ������
        $replase = array(
        // �����
        '�'=>'a',
        '�'=>'b',
        '�'=>'v',
        '�'=>'g',
        '�'=>'d',
        '�'=>'e',
        '�'=>'yo',
        '�'=>'zh',
        '�'=>'z',
        '�'=>'i',
        '�'=>'j',
        '�'=>'k',
        '�'=>'l',
        '�'=>'m',
        '�'=>'n',
        '�'=>'o',
        '�'=>'p',
        '�'=>'r',
        '�'=>'s',
        '�'=>'t',
        '�'=>'u',
        '�'=>'f',
        '�'=>'h',
        '�'=>'c',
        '�'=>'ch',
        '�'=>'sh',
        '�'=>'shh',
        '�'=>'j',
        '�'=>'y',
        '�'=>'',
        '�'=>'e',
        '�'=>'yu',
        '�'=>'ya',
        // ������ ����� ���������� � �������
        ' '=>'-',
        ' - '=>'-',
        '_'=>'-',
        //�������
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
        $insert=preg_replace("/  +/"," ",$insert); // ������� ������ �������
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
        $stmt = $mysqli->prepare("SELECT ru_url as url, ru_menu_name as name FROM kb_pages WHERE status=1 ORDER BY priority");
        else
        $stmt = $mysqli->prepare("SELECT en_url as url, en_menu_name as name FROM kb_pages WHERE status=1 ORDER BY priority");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '<li><a href="/'.$lang.'/'.$row['url'].'">'.$row['name'].'</a></li>';
        }
        
        $temp .= "<li><a href='#'><img src='/images/phone.png'/></a></li>";
        $temp .= "<li><a href='#'><img src='/images/sms.png'/></a></li>";
        $temp .= "<li><a href='#'><img src='/images/shop.png'/></a></li>";
        $temp .= "<li><a href='#'><img src='/images/favorite.png'/></a></li>";
        $temp .= "</ul>";
        
        $lang_url = main::get_lang_url();
        
        $temp .= "<p><a href='".$lang_url."'> ����������� ����</a></p>";
        $stmt->close();
        return $temp;
    }
    
    static function get_sections()
    {
        global $mysqli;
        global $lang;
        $temp = '';
        if($lang=='ru')
        $stmt = $mysqli->prepare("SELECT id, ru_url as url, ru_name as name FROM kb_sections WHERE status=1");
        else
        $stmt = $mysqli->prepare("SELECT id, en_url as url, en_name as name FROM kb_sections WHERE status=1");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '<li><a href="/'.$lang.'/'.$row['url'].'">'.$row['name'].'</a></li>';
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
        $temp = '';
        if($lang=='ru')
        {
            $stmt = $mysqli->prepare("SELECT ru_content as content FROM kb_pages WHERE id = $id");
        }
        else
        {
            $stmt = $mysqli->prepare("SELECT en_content as content FROM kb_pages WHERE id = $id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= $row['content'];
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
        return '<h2>404 �������� �� �������</h2>
        <p>�������� �� �������� ������� ��� ���-�� ����� �� ���</p>
        <a href="/">��������� �� ������� ��������</a>';
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
        if($lang == 'ru')
        {
           $sql = $mysqli->prepare("SELECT id, (SELECT ru_url FROM kb_sections WHERE id=section_id) as section_url, ru_name as name, ru_description as description, image, price FROM kb_products WHERE section_id=? AND status=1"); 
        }
        else
        {
            $sql = $mysqli->prepare("SELECT id, (SELECT en_url FROM kb_sections WHERE id=section_id) as section_url, en_name as name, en_description as description, image, price FROM kb_products WHERE section_id=? AND status=1");
        }
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '<div class="item">
            <a href="/'.$lang.'/'.$row['section_url'].'/'.$row['id'].'">
            <img width="200px" src="'.$row['image'].'"/>
            <h2>'.$row['name'].'</h2>
            <p>'.$row['description'].'</p>
            <p>'.$row['price'].'</p>
            </a>
            </div>';
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
            $sql = $mysqli->prepare("SELECT ru_content as content, ru_name as name, ru_description as description, image, price FROM kb_products WHERE status=1 AND id=?");
        }
        else
        {
            $sql = $mysqli->prepare("SELECT en_content as content, en_name as name, en_description as description, image, price FROM kb_products WHERE status=1 AND id=?");
        }
        $sql->bind_param('s', $id);
        $sql->execute();
        $result = $sql->get_result();
        while ($row = $result->fetch_assoc()) 
        {
            $temp .= '<div class="item">
            <img width="200px" src="'.$row['image'].'"/>
            <h2>'.$row['name'].'</h2>
            <p>'.$row['description'].'</p>
            <p>'.$row['price'].'</p>
            </div>';
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
#####################################################################
//������ ������� ��������������
#####################################################################
class admin_pages
{
    function get_list()
    {
        global $mysqli;
        $temp = "<p><a href='/kb-admin/?module=pages&action=add'>�������� ��������</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>��</th>
        <th>���������</th>
        <th>� ����</th>
        <th>������</th>
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
        <h2>������� �������</h2>
        <p>���������: <input type='text' name='ru_title'></p>
        <p>��� � ����: <input type='text' name='ru_menu_name'></p>
        <div class='block'>
            <p>�������� �����: <input type='text' name='ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description'></p>
        </div>
        <p>�������: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>���������� �������</h2>
        <p>���������: <input type='text' name='en_title'></p>
        <p>��� � ����: <input type='text' name='en_menu_name'></p>
        <div class='block'>
            <p>�������� �����: <input type='text' name='en_keywords'></p>
            <p>��������: <input type='text' name='en_description'></p>
        </div>
        <p>�������: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor2
            });
        </script></p>
        <p>������������: <input type='checkbox' name='status' value='1'></p>
            <p>��������� � ����: <input type='number' name='priority'></p>
        <input type='submit' value='��������' name='submit' onClick= 'return window.confirm(`�������� ����� ��������?`);'>
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
        <p><a href='/kb-admin/?module=pages&action=delete&id=$id' onClick= 'return window.confirm(`�� �������, ��� ������ ������� ��������?`);'><span>������� ��������</span></a></p>
        <h2>������� �������</h2>
        <p>���������: <input type='text' name='ru_title' value='$ru_title'></p>
        <p>��� � ����: <input type='text' name='ru_menu_name' value='$ru_menu_name'></p>
        <div class='block'>
            <p>�������� �����: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        <p>�������: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'>$ru_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>���������� �������</h2>
        <p>���������: <input type='text' name='en_title' value='$en_title'></p>
        <p>��� � ����: <input type='text' name='en_menu_name' value='$en_menu_name'></p>
        <div class='block'>
            <p>�������� �����: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>��������: <input type='text' name='en_description' value='$en_description'></p>
        </div>
        <p>�������: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'>$en_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor2
            });
        </script></p>";
        if($status =='1')
        {
            $temp .= "<p>������������: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>������������: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= "<p>��������� � ����: <input type='number' name='priority' value='$priority'></p>
        <input type='submit' value='��������' name='submit'  onClick= 'return window.confirm(`�� �������, ��� ������ ��������� ��������� � ���� ��������?`);'>
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
//������ �������� ��������������
#####################################################################
class admin_sections
{
    function get_list()
    {
        global $mysqli;
        $temp = "<p><a href='/kb-admin/?module=sections&action=add'>�������� ������</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>��</th>
        <th>���</th>
        <th>������</th>
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
        <h2>������� �������</h2>
        <p>��������: <input type='text' name='ru_name'></p> 
        <div class='block'>
            <p>��������� ��������: <input type='text' name='ru_title'></p>
            <p>�������� �����: <input type='text' name='ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description'></p>
        </div>
        
        <h2>���������� �������</h2>
        <p>��������: <input type='text' name='en_name'></p>
        <div class='block'>
            <p>��������� ��������: <input type='text' name='en_title'></p>
            <p>�������� �����: <input type='text' name='en_keywords'></p>
            <p>��������: <input type='text' name='en_description'></p>
        </div>
        
        <p>������������: <input type='checkbox' name='status' value='1'></p>
        <input type='submit' value='��������' name='submit' onClick= 'return window.confirm(`�������� ����� ������??`);'>
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
        <p><a href='/kb-admin/?module=sections&action=delete&id=$id' onClick= 'return window.confirm(`�� �������, ��� ������ ������� ������?`);'><span>������� ������</span></a></p>
        <h2>������� �������</h2>
        <p>��������: <input type='text' name='ru_name' value='$ru_name'></p> 
        <div class='block'>
            <p>��������� ��������: <input type='text' name='ru_title' value='$ru_title'></p>
            <p>�������� �����: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        
        <h2>���������� �������</h2>
        <p>��������: <input type='text' name='en_name' value='$en_name'></p>
        <div class='block'>
            <p>��������� ��������: <input type='text' name='en_title' value='$en_title'></p>
            <p>�������� �����: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>��������: <input type='text' name='en_description' value='$en_description'></p>
        </div>";
        if($status =='1')
        {
            $temp .= "<p>�����������: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>�����������: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= "<input type='submit' value='��������' name='submit' onClick= 'return window.confirm(`�������� ������??`);'>
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
//������ ������� ��������������
#####################################################################
class admin_products
{
    function get_list()
    {
        global $mysqli;
        
        $temp = "<p><a href='/kb-admin/?module=products&action=add'>�������� �������</a></p>
        <table class='table'>
        <thead>
        <tr>
        <th>��</th>
        <th>���</th>
        <th>������</th>
        <th>������</th>
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
        <h2>������� �������</h2>
        <p>��������: <input type='text' name='ru_name'></p> 
        <div class='block'>
            <p>��������� ��������: <input type='text' name='ru_title'></p>
            <p>�������� �����: <input type='text' name='ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description'></p>
        </div>
        <p>�������: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>���������� �������</h2>
        <p>��������: <input type='text' name='en_name'></p>
        <div class='block'>
            <p>��������� ��������: <input type='text' name='en_title'></p>
            <p>�������� �����: <input type='text' name='en_keywords'></p>
            <p>��������: <input type='text' name='en_description'></p>
        </div>
        <p>�������: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'></textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>";
        global $mysqli;
        $option = "<select name='section_id'>
        <option disabled>�������� ������</option>
        <option value='0'>������ �� �����</option>";
        $sql_option = $mysqli->prepare("SELECT id, ru_name as name FROM kb_sections");
        $sql_option->execute();
        $result_option = $sql_option->get_result();
        while ($row = $result_option->fetch_assoc()) 
        {
            $option .=  '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
        $option .= "</select>";
        $temp.= $option;
        $temp.= "<p>����(� ������): <input type='text' name='price'></p>
                <p>������������: <input type='checkbox' name='status' value='1'></p>
                <input type='submit' value='��������' name='submit'>
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
        <option disabled>�������� ������</option>
        <option value='0'>������ �� �����</option>";
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
        <p><a href='/kb-admin/?module=products&action=delete&id=$id' onClick= 'return window.confirm(`�� �������, ��� ������ ������� ��� �������?`);'><span>������� �������</span></a></p>
        <h2>������� �������</h2>
        <input type='hidden' name='id' value='$id'>
        <p>��������: <input type='text' name='ru_name' value='$ru_name'></p> 
        <div class='block'>
            <p>��������� ��������: <input type='text' name='ru_title' value='$ru_title'></p>
            <p>�������� �����: <input type='text' name='ru_keywords' value='$ru_keywords'></p>
            <p>��������: <input type='text' name='ru_description' value='$ru_description'></p>
        </div>
        <p>�������: <textarea id='ru_content_editor' name='ru_content' cols='100' rows='20'>$ru_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'ru_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        
        <h2>���������� �������</h2>
        <p>��������: <input type='text' name='en_name' value='$en_name'></p>
        <div class='block'>
            <p>��������� ��������: <input type='text' name='en_title' value='$en_title'></p>
            <p>�������� �����: <input type='text' name='en_keywords' value='$en_keywords'></p>
            <p>��������: <input type='text' name='en_description' value='$en_description'></p>
        </div>
        <p>�������: <textarea id='en_content_editor' name='en_content' cols='100' rows='20'>$en_content</textarea>
        <script type='text/javascript'>
            var ckeditor1 = CKEDITOR.replace( 'en_content_editor' );
            AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: ckeditor1
            });
        </script></p>
        <p>����(� ������): <input type='text' name='price' value='$price'></p>";
        if($status =='1')
        {
            $temp .= "<p>������������: <input type='checkbox' name='status' value='1' checked></p>";
        }
        else
        {
            $temp .= "<p>������������: <input type='checkbox' name='status' value='1'></p>";
        }
        $temp .= $option;
        $temp .= "<p><input type='submit' value='��������' name='submit' onClick= 'return window.confirm(`�������� �������?`);'></p>
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
        
$temp .= '<li class="current"><a href="/kb-admin/?module=pages"><i class="glyphicon glyphicon-file"></i>��������</a></li>';
$temp .= '<li class="current"><a href="/kb-admin/?module=sections"><i class="glyphicon glyphicon-th-list"></i>�������</a></li>';
$temp .= '<li class="current"><a href="/kb-admin/?module=products"><i class="glyphicon glyphicon-th"></i>�������</a></li>';

        echo $temp;
    }
}
?>