<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( 'includes/senha.php'  );


  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";



$conexao = mysql_connect('$servidor', '$loginMYSQL', '$senha') or die(mysql_error());
mysql_select_db('$baseMYSQL', $conexao) or die(mysql_error());

$sql  = "select numero from representantes";
        
$rst=mysql_query($sql) or die (mysql_error());

$exec='';
while ($row = mysql_fetch_object($rst)) {
  
  $id= $row->numero;
  
  $senha = getUniqueCode(5);
  $exec = "update representantes set senha='$senha' where numero=$id;";
  mysql_query($exec) or die(mysql_error());  
}


mysql_free_result($rst);

?>




