<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();



  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";




$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$sql  = "update configuracao set ultVersaoTechVendas='2.2.4' ";
 
$resultado = mysql_query($sql, $conexao) or die (mysql_error());





$servidor = 'mysql.premiercorretora.kinghost.net';
$loginMYSQL = 'premiercorreto';
$baseMYSQL = 'premiercorreto';
$senha = "f2a9b0";

die("CONEXÃO NEGADA A BASE DE SÃO PAULO");

$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query($sql, $conexao) or die (mysql_error());





$servidor = 'mysql.imperiocorretora.kinghost.net';
$loginMYSQL = 'imperiocorreto';
$baseMYSQL = 'imperiocorreto';
$senha = "f2a9b0";

die("CONEXÃO NEGADA A BASE DE SÃO PAULO");

$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query($sql, $conexao) or die (mysql_error());





?>


