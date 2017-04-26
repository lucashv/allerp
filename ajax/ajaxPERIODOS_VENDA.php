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
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update periodos_vendas set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    

/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[2];
  
  $dataini = $cmps[0]=='null' ? 'null' : "'$cmps[0]'"; 
  $datafin = $cmps[1]=='null' ? 'null' : "'$cmps[1]'";
  if ($id=='') 
    $sql = "insert into periodos_vendas(dataini, datafin, ativo) values($dataini, $datafin, 'S')";
  
  else  
    $sql = "update periodos_vendas set dataini=$dataini, datafin=$datafin where numreg=$id"; 
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();

  else   {
    /* busca ultimo ID gerado */
    if ($id=='')
      $resp = 'OK;INC_' . mysql_insert_id();
    else
      $resp = 'OK;' . $id;      
  }
  
  
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $ativos = $_REQUEST['ativos'];
  
  $sql  = "select numreg,date_format(dataini, '%d/%m/%y') as dataini_mostrar, date_format(datafin, '%d/%m/%y') as datafin_mostrar, dataini, ".
          " ifnull(ativo, 'N') as ativo  " .
          " from periodos_vendas  " .
          ($ativos=='S' ? " where ifnull(ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ativo,'')<>'S' " : "" ) .          
          "order by dataini desc " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.5;
    
	$header = "$largura1 px,Data inicial|$largura1 px,Data final";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
    } else {    
      $largura1='';
    }
    $i++;
  
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numreg\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=center $largura1>&nbsp;&nbsp;$row->dataini_mostrar</td>".
            "<td align=center $largura1>$row->datafin_mostrar</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row->ativo!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('periodo_venda.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  switch ($acao) { 
    case 'incluirREG':
      $resp=str_replace('TITULO_JANELA', 'Novo Registro',$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarREG':
      $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  if ($acao!='incluirREG')   {
  $sql  = "select date_format(dataini, '%d/%m/%y') as dataini_mostrar, date_format(datafin, '%d/%m/%y') as datafin_mostrar  ".
          " from periodos_vendas  " .
          " where numreg=$vlr " ;
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATAINI', $row->dataini_mostrar, $resp);
    $resp=str_replace('vDATAFIN', $row->datafin_mostrar, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);
  }    
  else {
    $resp=str_replace('vDATAINI', '', $resp);
    $resp=str_replace('vDATAFIN', '', $resp);
    $resp=str_replace('@numREG', '', $resp);    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


