<?
header("Content-Type: text/html; charset=iso-8859-1");


require_once( 'includes/definicoes.php'  );
require_once( 'includes/funcoes.php'  );
require_once( 'includes/funcoesDATA.php'  );

/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$sql="select prop.idtipocontrato, prop.idOPERADORA, prop.vlrCONTRATO, prop.qtdeVIDAS, sequencia, ifnull(qtde1,0) as qtde1, ifnull(qtde2,0) as qtde2, ifnull(qtde3,0) as qtde3, ifnull(qtde4,0) as qtde4, ifnull(qtde5,0) as qtde5, ".
      "  ifnull(qtde6,0) as qtde6 , ifnull(perc1,0) as perc1, ifnull(perc2,0) as perc2, ifnull(perc3,0) as perc3, tip.vlrPRODUCAO ". 
      "from propostas prop ".
       "inner join tipos_contrato tip ".
      " on tip.numreg=idTipoContrato ";  

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  while ($row = mysql_fetcH_object($resultado)) {
  
    $perc=100;
    if ($row->qtdeVIDAS >= $row->qtde1 && $row->qtdeVIDAS <= $row->qtde2 && $row->qtde1>0 and $row->qtde2>0 and $row->perc1>0) $perc=$row->perc1;
    if ($row->qtdeVIDAS >= $row->qtde3 && $row->qtdeVIDAS <= $row->qtde4 && $row->qtde3>0 and $row->qtde4>0 and $row->perc2>0) $perc=$row->perc2;
    if ($row->qtdeVIDAS >= $row->qtde5 && $row->qtdeVIDAS <= $row->qtde6 && $row->qtde5>0 and $row->qtde6>0 and $row->perc3>0) $perc=$row->perc3;
  
    $calc=$row->vlrCONTRATO * ($perc/100);
    if ($row->idOPERADORA==4 || $row->idtipocontrato==13) 
      $sql="update propostas set vlrPLANTAO=$calc, vlrPRODUCAO=0 where sequencia=$row->sequencia";
    else
      $sql="update propostas set vlrPLANTAO=$calc, vlrPRODUCAO=$calc where sequencia=$row->sequencia";

    mysql_query($sql, $conexao) or die (mysql_error());
    echo($row->idtipocontrato.'...'.$row->idOPERADORA.'...'.$sql.'<br>');    
  } 



?>