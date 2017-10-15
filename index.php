<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

class Manage {
  public static function autoload($class) {
     include $class . '.php';
  }
}

spl_autoload_register(array('Manage', 'autoload'));

$obj = new main();

class main {

  public function __construct() {
     $pageRequest = 'homepage';
     if(isset($_REQUEST['page'])) {
        $pageRequest = $_REQUEST['page'];
     }
     $page = new $pageRequest;

     if($_SERVER['REQUEST_METHOD'] == 'GET') {
        $page->get();
     }
     else {
        $page->post();
     }
  }
}

abstract class page {
  protected $html;

  public function __construct() {
     $this->html .= '<html>';
     $this->html .= '<title>project1</title>';
     $this->html .= '<body>';
  }
  public function __destruct() {
     $this->html .= '</body></html>';
     stringFunctions::printThis($this->html);
  }
  public function get() {
     echo 'default get message from page class';
  }
  public function post() {
     print_r($_POST);
  }
}

class homepage extends page {
  public function get() {
     $form = '<form action="index.php" method="post">';
     $form .= 'Upload .CSV file:<br>';
     $form .= '<input type="file" name="selectFile" id="selectFile">';
     $form .= '<input type="submit" value="submit">';
     $form .= '</form>';
     $this->html .= '<h1>.CSV file upload</h1>';
     $this->html .= $form;
  }
  public function post() {
     print_r($_FILES);
  }
}

class stringFunctions {
  public static function printThis($text) {
     print($text);
  }
}

?>
