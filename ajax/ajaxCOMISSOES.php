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
if ($acao=='excluir') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("delete from comissoesrepresentante where numero=$id") or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='padrao') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update configuracao set comissaoRepreAtual = $id") or  die (mysql_error());
    
  echo('ok'); die();
}    
    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[2];
  
  if ($id=='') 
    $sql = "insert into comissoesrepresentante(descricao,adesaoRepreEscritorio,comissaoREMOCAO, ".
            "vlrParaCalculoComissaoAdesao, metodoParaCalculoComissaoAdesao,   ".
            " adesaoRepreTeleatendimento, adesaoRepreTerceirizado, " .
            "  adesaoTeleatendente, adesaoSupervisorTele " .             
            " values('$cmps[0]', $cmps[1], $cmps[3], $cmps[5], $cmps[4], $cmps[6], $cmps[7], $cmps[8], $cmps[9])";
            
  
  else  
    $sql = "update comissoesrepresentante set descricao='$cmps[0]',adesaoRepreEscritorio=$cmps[1], " .
            "comissaoREMOCAO=$cmps[3], vlrParaCalculoComissaoAdesao=$cmps[5], ".
            " adesaoRepreTeleatendimento=$cmps[6], adesaoRepreTerceirizado=$cmps[7], " .
            "  adesaoTeleatendente=$cmps[8], adesaoSupervisorTele=$cmps[9], " .            
            "metodoParaCalculoComissaoAdesao=$cmps[4]  where numero=$id"; 
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();
  else   {
  
    /* busca ultimo ID gerado */
    if ($id=='')    $id = mysql_insert_id();
    
    $resp = 'OK;' . $id ;
  }
  
  
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $sql = "select comissaoRepreAtual from configuracao ";
  $rsCONFIG = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($rsCONFIG);
  
  $comiPADRAO = $row->comissaoRepreAtual;
  mysql_free_result($rsCONFIG);
  
   
  
  $sql  = "select descricao, numero " .
          " from comissoesrepresentante ".
          "order by descricao " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 1;
    
	$header = "$largura1 px,Descrição";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
    } else {    
      $largura1='';
    }
    $i++;
  
    $padrao = $row[1]==$comiPADRAO ? 
      "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color='blue'><< PADRÃO ATUAL >></font>" : "";
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[1]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0] $padrao</td>".
            "</tr>";
            
    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('comissoes.txt', 'r');
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
    $sql  = "select descricao, adesaoRepreEscritorio, adesaoRepreTeleatendimento, numero, comissaoREMOCAO, ".
            "adesaoRepreTerceirizado, adesaoTeleatendente, vlrParaCalculoComissaoAdesao, adesaoSupervisorTele, ".
            " ifnull(metodoParaCalculoComissaoAdesao, 1) as metodoParaCalculoComissaoAdesao ".
            "from comissoesrepresentante ".
            "where numero=$vlr ";
            
  
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDESCRICAO', $row->descricao, $resp);
    $resp=str_replace('vADESAO1', $row->adesaoRepreEscritorio, $resp);    
    $resp=str_replace('vADESAO2', $row->adesaoRepreTeleatendimento, $resp);    
    $resp=str_replace('vADESAO3', $row->adesaoRepreTerceirizado, $resp);    
    $resp=str_replace('vADESAO4', $row->adesaoTeleatendente, $resp);    
    $resp=str_replace('vADESAO5', $row->adesaoSupervisorTele, $resp);
        
    $resp=str_replace('vREMOCAO', $row->comissaoREMOCAO, $resp);    
    $resp=str_replace('vCALCULO', $row->vlrParaCalculoComissaoAdesao, $resp);    

    $resp=str_replace('@opcao1', 
      ($row->metodoParaCalculoComissaoAdesao==1 ? 'checked="checked"' : ''), $resp);
    $resp=str_replace('@opcao2', 
      ($row->metodoParaCalculoComissaoAdesao==2 ? 'checked="checked"' : ''), $resp);
                
    $resp=str_replace('@numREG', $vlr, $resp);    
        
  }    
  else {
    $resp=str_replace('vDESCRICAO', '', $resp);
    $resp=str_replace('vADESAO1', '', $resp);    
    $resp=str_replace('vADESAO2', '', $resp);    
    $resp=str_replace('vADESAO3', '', $resp);    
    $resp=str_replace('vADESAO4', '', $resp);    
    $resp=str_replace('vADESAO5', '', $resp);
    
    $resp=str_replace('vREMOCAO', '', $resp);
    $resp=str_replace('vCALCULO', '', $resp);            
    $resp=str_replace('@numREG', '', $resp);
    
    $resp=str_replace('@opcao1', 'checked="checked"', $resp);
    $resp=str_replace('@opcao2', '', $resp);        
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


