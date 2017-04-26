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

$MESES = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho',
          'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'); 


/*****************************************************************************************/
if ($acao=='excluir') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("delete from cheques where numreg=$id") or  die (mysql_error());
    
  echo('ok'); die();
}    


/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy
  
  $cheque = '';
  if ( strpos($dataTRAB, 'cheque')!==false )  {
    $cheque = str_replace('cheque', '', $dataTRAB);
    
    $sql  = "select ch.numREG, numCHEQUE, banco, ifnull(ban.nome, '* erro *') as nomeBANCO, ".
             "date_format(data, '%d/%m/%y') as data, valor, proposta ". 
             "from cheques ch ".
             "left join bancos ban ". 
             "	on ban.numero = ch.banco ". 
             " where numCHEQUE=$cheque " .          
             "order by data desc " ;
            
  }
  else {   
    $dataTRAB = substr($dataTRAB, 4) . substr($dataTRAB, 2, 2) . substr($dataTRAB, 0, 2);
    $avancarDATA = $_REQUEST['vlr2'];       
    
    // obtem datas inicial e final do mes/ano sendo lido
    $sql = "select date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%Y%m%d') as dataINI,  ".  
           "       date_format(DATE_ADD(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA + 1  month), " . 
          "                       INTERVAL -1 day), '%Y%m%d') as dataFIN,  " .
          " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%m') as mesATUAL, " .
          " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%Y') as anoATUAL, "  .
          " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%d%m%Y') as novaDATA_JAVASCRIPT " ;                
  
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);
    $dataINI = $row->dataINI;
    $dataFIN = $row->dataFIN;
    $mesATUAL = (int)$row->mesATUAL;  
    $anoATUAL = $row->anoATUAL;
    $novaDATA_JAVASCRIPT = $row->novaDATA_JAVASCRIPT;  
    
    mysql_free_result($resultado);          
    
    $sql  = "select ch.numREG, numCHEQUE, banco, ifnull(ban.nome, '* erro *') as nomeBANCO, ".
         "date_format(data, '%d/%m/%y') as data, valor, proposta ". 
         "from cheques ch ".
         "left join bancos ban ". 
         "	on ban.numero = ch.banco ". 
        " where data between '$dataINI' and '$dataFIN' " .          
         "order by data desc " ;
  }            
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.6;
  $largura3 = $_SESSION['largIFRAME'] * 0.1;  
  $largura4 = $_SESSION['largIFRAME'] * 0.1;
  $largura5 = $_SESSION['largIFRAME'] * 0.1;    
    
	$header = "$largura1 px,Cheque|$largura2 px,Banco|$largura3 px,Data|$largura4 px,Valor|$largura5 px,Proposta";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4=''; $largura5='';
    }
    $i++;
  
    $vlr =  number_format($row->valor, 2, ',', '')  ;
    $lin = "<tr onmousedown=\"Selecionar(this.id);\" id=\"$row->numREG\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;$row->numCHEQUE</td>".
            "<td align=\"left\" $largura2>$row->nomeBANCO ($row->banco)</td>".
            "<td align=\"center\" $largura3>$row->data</td>".
            "<td align=\"right\" $largura4>$vlr&nbsp;&nbsp;</td>".
            "<td align=\"left\" $largura5>$row->proposta</td>".                        
            "</tr>";
            
    $resp = $resp . ($lin);
  }
  if ($cheque != '')
    $resp .= '^'.$qtdeREGS.'^'.'cheque= '.$cheque. '^' . date("dmY");
  else
    $resp .= '^'.$qtdeREGS.'^'.$MESES[ $mesATUAL-1 ] . '/' .$anoATUAL . '^' . $novaDATA_JAVASCRIPT ;
          
}


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


