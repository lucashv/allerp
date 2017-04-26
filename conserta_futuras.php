<?
header("Content-Type: text/html; charset=iso-8859-1");


  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";


/*
$servidor = 'mysql.premiercorretora.kinghost.net';
$loginMYSQL = 'premiercorreto';
$baseMYSQL = 'premiercorreto';


$servidor = 'mysql.imperiocorretora.kinghost.net';
$loginMYSQL = 'imperiocorreto';
$baseMYSQL = 'imperiocorreto';
*/  

$conexao = mysql_connect($servidor, $loginMYSQL, '$senha') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$sql="select sequencia, date_format(dataassinatura, '%Y%-%m%-%d') as dataassinatura, ifnull(1aMensIgualVigencia, 'N') as _1aMensIgualVigencia  ".
     "from propostas ".
     "inner join operadoras on operadoras.numreg=propostas.idOPERADORA ".
     " where date_format(datacadastro, '%Y%-%m%-%d')>='2013-02-19' ";
$resultado = mysql_query($sql, $conexao) or die (mysql_error());
                      
while ($row = mysql_fetcH_object($resultado)) {

  $futuras = mysql_query("select numreg,date_format(datavencimento, '%Y%-%m%-%d') as vencimento, ordem from futuras where sequencia=$row->sequencia  order by ordem", $conexao) 
    or die (mysql_error());

  while ($futura = mysql_fetcH_object($futuras)) {
    $somar = $futura->ordem;    

    if ( ($row->_1aMensIgualVigencia=='S') || ($row->_1aMensIgualVigencia=='O') )  {
      $somar--;
    }   

    $vencimento = date("Y-m-d", strtotime(date("Y-m-d", strtotime($row->dataassinatura)) ." +$somar month"  )) ;
                    
    if ($vencimento != $futura->vencimento) {
      $sql="update futuras set datavencimento='$vencimento' where numreg=$futura->numreg ";
      mysql_query($sql, $conexao) or die (mysql_error());      
      echo( "seq= $row->sequencia   , vencto certo= $vencimento   vcnto errado= $futura->vencimento  assinatura= $row->dataassinatura   ".
            " ordem= $futura->ordem <br>" ); 
    }
  }
}    
    
  
?>