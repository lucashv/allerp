<?
header("Content-Type: text/html; charset=iso-8859-1");


  $servidor = 'mysql.labspaulo.com.br';
  $loginMYSQL = 'labspaulo02';
  $baseMYSQL = 'labspaulo02';
  $senha = "sucesso";


die("CONEXÃO NEGADA A BASE DE SÃO PAULO");

$conexao = mysql_connect($servidor, $loginMYSQL, 'sucesso') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$sqlATUAL="select cx.numreg, cx.dataop " .
          "from caixa cx " .
          "inner join contas con " .
          "  on con.numero=cx.idoperacao " .
          "where con.tipoenvolvido='F' " ;

$rsTRANSP = mysql_query($sqlATUAL, $conexao) or die (mysql_error());

while ( $regTRANSP=mysql_fetch_object($rsTRANSP) ) {
  echo($regTRANSP->numreg).'<br>'; 

}
    
    
  
?>