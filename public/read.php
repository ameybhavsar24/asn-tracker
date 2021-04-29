<?php  
include 'autoload.php';
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
function read_file_docx($filename){  
  $striped_content = '';  
  $content = '';  
  if(!$filename || !file_exists($filename)) return false;  
  $zip = zip_open($filename);  
  if (!$zip || is_numeric($zip)) return false;  
  while ($zip_entry = zip_read($zip)) {  
  if (zip_entry_open($zip, $zip_entry) == FALSE) continue;  
  if (zip_entry_name($zip_entry) != "word/document.xml") continue;  
  $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));  
  zip_entry_close($zip_entry);  
  }// end while  
  zip_close($zip);  
  $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);  
  $content = str_replace('</w:r></w:p>', "\r\n", $content);  
  $striped_content = strip_tags($content);  
  return $striped_content;  
}

function read_file_pdf($filename) {
  $parser = new Smalot\PdfParser\Parser();
  $pdf    = $parser->parseFile($filename);
  $text = $pdf->getText();
  return $text;
}
function fileExtension($s) {
  $n = strrpos($s,".");
  return ($n===false) ? "" : substr($s,$n+1);
}
function read_file($filepath) {
  $fileExt = fileExtension(explode('/', $filepath)[1]);
  if ($fileExt == 'docx') {
    $content = read_file_docx($filepath);
    if ($content !== false) {
      return $content;
    }
  } else {
    $content =  read_file_pdf($filepath);
    if ($content !== false) {
      return $content;
    }
  }
  return "";
  // return read_file_pdf('uploads/deep.pdf');
}
// $content = read_file_pdf('test.pdf');
// var_dump($content);
// $filename = "test.docx";// or /var/www/html/file.docx  
// $content = read_file_docx($filename);  
// if($content !== false) {  
//   echo nl2br($content);  
// }  
// else {  
//   echo 'Couldn\'t the file. Please check that file.';  
// }

// $filename = "test.pdf";
// $content = read_file_pdf($filename);
?>  

