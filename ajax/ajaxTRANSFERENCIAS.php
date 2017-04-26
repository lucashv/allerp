<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$resp = 'INEXISTENTE';


/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $sql  = "select DATE_FORMAT(data, '%d/%m/%Y') as dataMOSTRAR, ifnull(op.nome, '* erro *') as nomeOPERADOR," .           
          " TIME_FORMAT(data, '%H:%i') as hora, idOPERADOR, numREG, nomearq " .
          " from transferencias trans " .
          " left join operadores op ".
          "   on op.numero = trans.idOPERADOR " .           
          "order by data desc " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.1;
  $largura3 = $_SESSION['largIFRAME'] * 0.4;
  $largura4 = $_SESSION['largIFRAME'] * 0.4;    
    
	$header = "$largura1 px,Data|$largura2 px,Hora|$largura3 px,Operador responsável|$largura4 px,Nome do arquivo";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";      
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4='';
    }
    $i++;
  
    $lin = "<tr @mudaCOR onmousedown=\"Selecionar(this.id);\" id=\"$row[4]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[2]</td>".
            "<td align=\"left\" $largura3>$row[1] ($row[3])</td>".
            "<td align=\"left\" $largura4>$row[5]</td>".            
            "</tr>";
            
    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ($acao=='verTEXTO') {
  
  $sql  = "select nomearq " .           
          " from transferencias " .
          " where numreg=$vlr ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='erro';  
  while ($row = mysql_fetch_object($resultado)) {  
    $resp = "ajax/transfere/$row->nomearq";
  }
  
 
}




/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


