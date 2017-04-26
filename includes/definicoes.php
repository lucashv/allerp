<?

//**************************************************************************************
// DEFINICOES PADRAO PARA O SITE
//**************************************************************************************


//** string de conexao com a base


$servidor =  $_SERVER['HTTP_HOST'];
if ( strpos($servidor, 'allcross')!==false ) {

//  $servidor = 'mysql.netnigro.com.br';
//  $loginMYSQL = 'netnigbr27';
//  $baseMYSQL = 'netnigbr27';
//  $senha = "49netn15";
  
}
else {
  //$servidor = 'localhost';
  //$loginMYSQL = 'root';
  //$baseMYSQL = 'allcross';
  //$senha = "sucesso";  
}

  
  $servidor = 'localhost';
  $loginMYSQL = 'chat_fenix'; //'all01_software';
  $baseMYSQL = 'chat_fenix'; //'all01_fenix2';
  $senha = 'F3n1x'; //"3483304";


ini_set('session.gc_maxlifetime', '3600');
ini_set('session.cookie_lifetime', '3600');


?>
