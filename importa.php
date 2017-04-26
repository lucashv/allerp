<?
header("Content-Type: text/html; charset=iso-8859-1");

  $servidor = 'localhost';
  $loginMYSQL = 'root';
  $baseMYSQL = 'rae';

$conexao = mysql_connect($servidor, $loginMYSQL, 'sucesso') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query('select * from pendentes_ordem', $conexao) or die (mysql_error());
while ($row = mysql_fetcH_object($resultado)) {

  $data='20'.substr($row->Campo3, 6) . substr($row->Campo3, 3, 2) . substr($row->Campo3, 0, 2);

  $idREPRE=$row->idREPRE=='' ? -1 : $row->idREPRE;
  $sql="insert into recebimentospropostas(numrepresentante, opresponsavel, propostas,data) ".
       " select $idREPRE, 2, '$row->Campo1', '$data' ";
  
  mysql_query($sql) or  die (mysql_error());
  
  $numREC = mysql_insert_id();
  $sql="insert into propostasrecebidas(numrec,priprop,ultprop,tipo) ".
       " select $numREC,  '$row->Campo1', '$row->Campo1', 0; ";

  mysql_query($sql) or  die (mysql_error());       
}


    
  
?>