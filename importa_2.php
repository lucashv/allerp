<?
header("Content-Type: text/html; charset=iso-8859-1");

  $servidor = 'localhost';
  $loginMYSQL = 'root';
  $baseMYSQL = 'rae';

$conexao = mysql_connect($servidor, $loginMYSQL, 'sucesso') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query('select * from propostas_canceladas', $conexao) or die (mysql_error());
while ($row = mysql_fetcH_object($resultado)) {

  if ($row->Campo3!='') $data=chr(39) .'20'.substr($row->Campo3, 6) . substr($row->Campo3, 3, 2) . substr($row->Campo3, 0, 2).chr(39);
  else $data='null';

  $idREPRE=$row->idREPRE=='' ? -1 : $row->idREPRE;
  $sql="insert into entregaspropostas(numrepresentante, opresponsavel, propostas,data) ".
       " select $idREPRE, 2, '$row->Campo1', $data ";
  
  mysql_query($sql) or  die (mysql_error());
  
  $numENT = mysql_insert_id();
  $sql="insert into propostasentregues(nument,numprop,tipo,idcancel) ".
       " select $numENT,  '$row->Campo1',  0, 2; ";

  mysql_query($sql) or  die (mysql_error());       
}    
    
  
?>