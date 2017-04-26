<?
header("Content-Type: text/html; charset=iso-8859-1");


  $servidor = 'mysql.labspaulo.com.br';
  $loginMYSQL = 'labspaulo02';
  $baseMYSQL = 'labspaulo02';
  $senha = "sucesso";

die("CONEXÃO NEGADA A BASE DE SÃO PAULO");

$conexao = mysql_connect($servidor, $loginMYSQL, 'sucesso') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query("select contratante,vlrcontrato,idrepresentante,sequencia,".
                      " date_format(dataassinatura, '%Y%-%m%-%d') as dataassinatura from propostas where idoperadora=5", $conexao) or die (mysql_error());
while ($row = mysql_fetcH_object($resultado)) {

  mysql_query("delete from futuras where sequencia=$row->sequencia") or  die (mysql_error());
  $data1aMens = $row->dataassinatura;

  for ($ordem=1; $ordem<=100; $ordem++)  {
    $somar = $ordem-1;
//    $vencimento = str_replace('-', '', date("Y-m-d", $row->dataassinatura . " +$somar month")) ;

    $vencimento = str_replace('-', '', date("Y-m-d", strtotime(date("Y-m-d", strtotime($row->dataassinatura)) ." +$somar month"  ))) ;
                    
    $sql = "insert into futuras(sequencia, ordem, dataVENCIMENTO, valor, numREPRESENTANTE) " .
          "select $row->sequencia, $ordem, '$vencimento', $row->vlrcontrato, $row->idrepresentante" ;
              
    mysql_query($sql) or die(mysql_error());
  }
  echo($row->contratante.'<br>');
}    
    
  
?>