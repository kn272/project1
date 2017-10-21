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
        $pageToLoad = frontController::pageLoader();
        $page = new $pageToLoad;
        frontController::methodLoader($page);
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
        $form = '<form action="index.php" method="post" enctype="multipart/form-data">';
        $form .= 'Upload .CSV file:<br>';
        $form .= '<input type="file" name="selectFile" id="selectFile">';
        $form .= '<input type="submit" value="submit" name="submit">';
        $form .= '</form>';
        $this->html .= '<h1>.CSV file upload</h1>';
        $this->html .= $form;
     }
  
     public function post() {
        if (isset($_POST["submit"])) {
           $fileName = $_FILES["selectFile"]["name"];
           $tmpFileName = $_FILES["selectFile"]["tmp_name"];
           $fileParts = pathinfo($fileName);
           $fileExt = $fileParts['extension'];
           if (fileHandling::checkFile($fileExt) == true) {
              $fileName =  fileHandling::uploadCsv($fileName,$tmpFileName);
              header('Location:?page=table&fileName='. $fileName);
           }
           else {
              echo "<script type='text/javascript'>alert('Please select a csv file')</script>";
           }
        }
     }
  }

  class table extends page {

     public function get() {
        $fileName = $_GET['fileName'];
        echo trim($fileName, "uploads/"). " was uploaded successfully and table is as follows:<br><br>";
        $heading = 1;
        $handle = fopen($fileName,"r");
        $table = '<table border="1">';
        while (($data = fgetcsv($handle))!=FALSE) {
           if ($heading == 1) {
              $table .= '<thead><tr>';
	      foreach ($data as $value) {
                 if(!isset($value)) {
	            $value = "&nbsp";
		 }
                 else {
	            $table .= "<th>". $value ."</th>";
		 }
	      }
	      $table .=  '</tr></thead><tbody>';
	   }
	   else {
              $table .= '<tr>';
	      foreach ($data as $value) {
                 if(!isset($value)) {
                    $value = "&nbsp";
		 }
                 else {
	            $table .=  "<td>". $value . "</td>";
	       	 }   
	      }
	      $table .= '</tr>';
	   }
	   $heading++;
        }
        $table .= '</tbody></table>';
        $this->html .= $table;
        fclose($handle);
     }
  }

  class frontController {

     public static function pageLoader() {     
        if (isset($_REQUEST['page'])) {
           return $_REQUEST['page'];
        }
        else {
           $pageToLoad = 'homepage';
	   return $pageToLoad;
        }
     }

     public static function methodLoader($page) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
           $page->get();
        }
        else {
           $page->post();
        }
     }

  } 

  class stringFunctions {
     public static function printThis($text) {
        print($text);
     }
  }

  class htmlTags {
     
     public static function startTag($text) {
        $tag = '<'. $text .'>';
	return $tag;
     }

     public static function endTag($text) {
        $tag = '</'. $text .'>';
	return $tag;
     }

     public static function titleTag($text) {
        $tag = '<title>'. $text .'</title>';
	return $tag;
     }

     public static function inputTag($text) {
        $tag = '<input '. $text .'>';
	return $tag;
     }

     public static function brTag($text) {
        $tag = '<br>';
	return $tag;
     }

     public static function h1Tag($text) {
        $tag = '<h1>'. $text .'</h1>';
	return $tag;
     }

     public static function thTag($text) {
        $tag = '<th>'. $text .'</th>';
	return $tag;
     }

     public static function tdTag($text) {
        $tag = '<td>'. $text .'</td>';
	return $tag;
     }
  }

  class fileHandling {
     public static function checkFile($fileExt) {
        if ($fileExt == 'csv')
           return true;
        else
           return false;
     }
     public static function uploadCsv($fileName,$tmpFileName) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . $fileName;
        $fileName=$tmpFileName;
        move_uploaded_file($fileName,$targetFile);
        return $targetFile;
     }
  }

?>     
