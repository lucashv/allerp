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
if ($acao=='contaENTREGA') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update contas set contaENTREGA_PROPOSTA=0 ;") or  die (mysql_error());
  mysql_query("update contas set contaENTREGA_PROPOSTA=1 where numero=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}          


/*****************************************************************************************/
if ($acao=='contaVALECREDITO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update contas set contaVALE_CREDITO=0 ;") or  die (mysql_error());
  mysql_query("update contas set contaVALE_CREDITO=1 where numero=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}

/*****************************************************************************************/
if ($acao=='contaADTOSALARIAL') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update contas set contaADIANTAMENTOSALARIAL=0 ;") or  die (mysql_error());
  mysql_query("update contas set contaADIANTAMENTOSALARIAL=1 where numero=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}

/*****************************************************************************************/
if ($acao=='contaADTOCOMISSAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update contas set contaADIANTAMENTOCOMISSAO=0 ;") or  die (mysql_error());
  mysql_query("update contas set contaADIANTAMENTOCOMISSAO=1 where numero=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}          
          

          



/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update contas set ativo=case ativo when 'S' then 'N' else 'S' end where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  $tipo = $_REQUEST['tipo'];  
  $tipoCAIXA = $_REQUEST['tipoCAIXA'];
  $tipoENVOLVIDO = $_REQUEST['tipoENVOLVIDO'];
  $saidaCHEQUE = $_REQUEST['saidaCHEQUE'];
  $agrupador = $_REQUEST['agr']=='' ? 'null' : $_REQUEST['agr'];
  $gerarDEBITO = $_REQUEST['gerarDEBITO']=='true' ? 1 : 0;
  
  if ($id=='') 
    $sql = "insert into contas(nome, ativo, entOUsai, tipoCAIXA, saidaCHEQUE, tipoENVOLVIDO, idAGRUPADOR, gerarDEBITO) ".
            " values(ucase('$cmps[0]'), 'S', '$tipo', '$tipoCAIXA', $saidaCHEQUE, '$tipoENVOLVIDO', $agrupador, $gerarDEBITO)";
  
  else  
    $sql = "update contas set nome=ucase('$cmps[0]'),entOUsai='$tipo', tipoCAIXA='$tipoCAIXA', ".
            " gerarDEBITO=$gerarDEBITO, tipoENVOLVIDO='$tipoENVOLVIDO', saidaCHEQUE=$saidaCHEQUE, idAGRUPADOR=$agrupador where numero=$id";
 
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
  
  $sql  = "select con.numero, con.nome, con.ativo, con.contaENTREGA_PROPOSTA, con.entOUsai, con.tipoCAIXA, con.contaVALE_CREDITO, con.tipoENVOLVIDO,  ".
          " con.idAGRUPADOR, ifnull(agr.nome, '-') as nomeAGRUPADOR, contaADIANTAMENTOSALARIAL, contaADIANTAMENTOCOMISSAO ".
          " from contas con " .
          "left join agrupadores agr ".
          "   on agr.numero = con.idAGRUPADOR   ".
          ($ativos=='S' ? " where ifnull(con.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(con.ativo,'')<>'S' " : "" ) .          
          "order by con.nome " ;

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.45;
  $largura3 = $_SESSION['largIFRAME'] * 0.1;  
  $largura4 = $_SESSION['largIFRAME'] * 0.10;
  $largura5 = $_SESSION['largIFRAME'] * 0.25;
    
	$header = "$largura3 px,Número|$largura2 px,Descrição|$largura3 px,Tipo|$largura4 px,Caixa:".
            "|$largura5 px,Agrupador";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";
      $largura5="width=\"$largura5 px\"";
    } else {    
      $largura1='';$largura2='';$largura3='';$largura4='';$largura5='';
    }
    $i++;
  
    $padrao='';
    if ($row[4]=='S') $corPADRAO='red';
    else  $corPADRAO='blue'; 

    if ($row[3]==1) 
      $padrao="<font color=$corPADRAO face=arial style='font-size:14px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><br>".
        "** CONTA: ENTREGA DE PROPOSTA **</b></font>";

    if ($row[6]==1) 
      $padrao="<font color=$corPADRAO face=arial style='font-size:14px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><br>".
        "** CONTA: PGTO DE VALE CRÉDITO **</b></font>";

    if ($row[10]==1) 
      $padrao="<font color=$corPADRAO face=arial style='font-size:14px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><br>".
          "** CONTA: ADIANTAMENTO SALARIAL **</b></font>";

    if ($row[11]==1) 
      $padrao="<font color=$corPADRAO face=arial style='font-size:14px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><br>".
          "** CONTA: ADIANTAMENTO COMISSÃO **</b></font>";

    
    $tipo = ($row[4]=='E') ? 'Entrada' : 'Saída';
    $tipoCAIXA = ($row[5]=='I') ? 'Interno' : 'Geral';
    $tipoENVOLVIDO = ($row[7]=='F') ? 'Funcionário' : 'Corretor';

    $agrupador = $row[9]=='-' ? '-' : "$row[9] ($row[8]) ";
              
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura3>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1] $padrao</td>".
            "<td align=\"left\" $largura3>$tipo</td>".            
            "<td align=\"left\" $largura4>$tipoCAIXA</td>".
            "<td align=\"left\" $largura5>$agrupador</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', "style='color:$corPADRAO'", $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {
  $arq = fopen('conta.txt', 'r');
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
    $sql  = "select con.numero, con.nome, con.ativo, con.entOUsai, con.tipoCAIXA, con.saidaCHEQUE, con.tipoENVOLVIDO,  " .
            "       con.idAGRUPADOR, agr.nome as nomeAGRUPADOR, con.gerarDEBITO ".
            "from contas con ".
            "left join agrupadores agr ".
            "   on agr.numero = con.idAGRUPADOR   ".
            "where con.numero=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('vAGRUPADOR', $row->idAGRUPADOR, $resp);
    $resp=str_replace('v_AGRUPADOR', $row->nomeAGRUPADOR, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);

    $resp=str_replace('checkedSAIDA', ($row->saidaCHEQUE==1) ? 'checked' : '', $resp);    

    if ($row->entOUsai=='E') { 
      $resp=str_replace('checked_1', 'checked', $resp);
      $resp=str_replace('checked_2', '', $resp);
    }
    else {
      $resp=str_replace('checked_2', 'checked', $resp);
      $resp=str_replace('checked_1', '', $resp);
    }
    $resp=str_replace('checkedDEBITO', ($row->gerarDEBITO==1 ? 'checked' : ''), $resp);

    if ($row->tipoCAIXA=='I') { 
      $resp=str_replace('checked2_1', 'checked', $resp);
      $resp=str_replace('checked2_2', '', $resp);
    }
    else {
      $resp=str_replace('checked2_2', 'checked', $resp);
      $resp=str_replace('checked2_1', '', $resp);
    }
    if ($row->tipoENVOLVIDO=='C') { 
      $resp=str_replace('checked3_2', 'checked', $resp);
      $resp=str_replace('checked3_1', '', $resp);
    }
    else {
      $resp=str_replace('checked3_1', 'checked', $resp);
      $resp=str_replace('checked3_2', '', $resp);
    }        
        
        
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vAGRUPADOR', '', $resp);
    $resp=str_replace('v_AGRUPADOR', '', $resp);

    $resp=str_replace('@numREG', '', $resp);
    
    $resp=str_replace('checkedSAIDA', '', $resp);

    $resp=str_replace('checked_1', 'checked', $resp);
    $resp=str_replace('checked_2', '', $resp);

    $resp=str_replace('checked2_1', 'checked', $resp);
    $resp=str_replace('checked2_2', '', $resp);

    $resp=str_replace('checked3_1', 'checked', $resp);
    $resp=str_replace('checked3_2', '', $resp);

  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


