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
	      //header('Location:index.php');
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
