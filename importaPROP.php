<?php
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( 'includes/definicoes.php'  );
require_once( 'includes/funcoes.php'  );

$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$sql  = "select idtipocontrato, date_format(datacadastro, '%Y%-%m%-%d %H:%i') as dataGRAVAR, vlrtotal as vlrcontrato, idrepresentante, cpfcontratante " .
        "from propostas " .
        " where date_format(datacadastro, '%Y%m%d') between '20111206'  and '20111208' ";

$resultado = mysql_query($sql, $conexao) or die (mysql_error());
while ($row = mysql_fetcH_object($resultado)) {
  $sql = "insert into caixa(entousai, dataop, idoperacao, valor, opresponsavel,idescritorio) ".
          " values('E', '$row->dataGRAVAR', 1, $row->vlrcontrato, 5,1)";
  mysql_query($sql) or die($sql."\n".mysql_error());
  
  $idCAIXA = mysql_insert_id();

  $sql = "insert into entregaspropostas(idrepresentante, cpf, valor, idtipo_contrato,idcaixa,vlrrecebido,vlradesao,vlrprestadora,percentualPRESTADORA) ".
          " values($row->idrepresentante,'$row->cpfcontratante',$row->vlrcontrato,$row->idtipocontrato,$idCAIXA,$row->vlrcontrato,0,$row->vlrcontrato,100)  ";

  mysql_query($sql) or die($sql."\n".mysql_error());

  echo($idCAIXA."\n");
}


?>