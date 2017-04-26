<?
header("Content-Type: text/html; charset=iso-8859-1");
 
$txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
$arq = fopen("./ajax/txts/$txt", 'r');

while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;
  
  if (strpos($oQueAuxiliar, 'Warning')===false) {
    $lin = nl2br($lin);
    
    $lin = preg_replace("/\n/", '', $lin);
    
    $lin = str_replace('<negrito>', '', $lin);  
    $lin = str_replace('FIM PAGINA', '', $lin);    
    
  	
    echo($lin );
  }        
}
unlink("./ajax/txts/$txt");



?>