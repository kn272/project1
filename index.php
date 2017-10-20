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
     $form = '<form action="index.php" method="post" enctype="multipart/form-data">';
     $form .= 'Upload .CSV file:<br>';
     $form .= '<input type="file" name="selectFile" id="selectFile">';
     $form .= '<input type="submit" value="submit" name="submit">';
     $form .= '</form>';
     $this->html .= '<h1>.CSV file upload</h1>';
     $this->html .= $form;
  }
  
  public function post() {
     /*$targetDir = "uploads/";
     print_r($_FILES);
     $targetFile = $targetDir . $_FILES["selectFile"]["name"];
     $fileType = pathinfo($targetFile,PATHINFO_EXTENSION);
     if(isset($_POST["submit"])) {
        $fileName=$_FILES["selectFile"]["tmp_name"];
        // move_uploaded_file($_FILES["selectFile"]["tmp_name"], "uploads/" . $_FILES["selectFile"]["name"]);
        move_uploaded_file($fileName,$targetFile);
	echo 'File uploaded successfully.';
	header('Location:?page=table&fileName='. $targetFile);
     }*/
    if(isset($_POST["submit"])) {
    $fileName = $_FILES["selectFile"]["tmp_name"];
    header('Location:?page=table&fileName='. $fileName);
    }
  }
}

class uploadfile extends page {
  public function get() {
      $targetDir = "uploads/";
      $fileName = $_GET['fileName'];
      //print_r($_FILES);
      $targetFile = $targetDir . $_FILES["selectFile"]["name"];
      $fileType = pathinfo($targetFile,PATHINFO_EXTENSION);
      //if(isset($_POST["submit"])) {
        // $fileName=$_FILES["selectFile"]["tmp_name"];
         // move_uploaded_file($_FILES["selectFile"]["tmp_name"], "uploads/" . $_FILES["selectFile"]["name"]);
	 move_uploaded_file($fileName,$targetFile);
         //echo 'File uploaded successfully.';
         header('Location:?page=table&fileName='. $targetFile);
      //}								    
  }
}

class table extends page {
  public function get() {
     $fileName = $_GET['fileName'];
     echo trim($fileName, "uploads/"). " was uploaded successfully and table is as follows:<br><br>";
     $heading = 1;
     $handle = fopen($fileName,"r");
     $table = '<table border="1">';
     while(($data = fgetcsv($handle))!=FALSE) {
        if ($heading == 1) {
           $table .= '<thead><tr>';
	   foreach ($data as $value) {
              if(!isset($value))
	         $value = "&nbsp";
              else
	         $table .= "<th>". $value ."</th>";
	   }
	   $table .=  '</tr></thead><tbody>';
	}
	else {
           $table .= '<tr>';
	   foreach ($data as $value) {
              if(!isset($value)) 
                 $value = "&nbsp";
              else
	         $table .=  "<td>". $value . "</td>";
	   $table .= '</tr>';
	   }
	}
    }
    $heading++;
    $table .= '</tbody></table>';
    $this->html .= $table;
    fclose($handle);
    stringFunctions::printThis($this->html);
  }
}

class stringFunctions {
  public static function printThis($text) {
     print($text);
  }
}


?>     
