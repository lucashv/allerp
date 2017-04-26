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
if ($acao=='padrao') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update configuracao set idComiPadraoPrestadora=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}          
          


/*****************************************************************************************/
if ($acao=='editarCOMISSAO') {
  $idPRODUTO = $_REQUEST['prod'];
  $parcela = $_REQUEST['parcela'];    
  $vlr = $_REQUEST['vlr'];  
  $idCOMISSAO = $_REQUEST['idCOMISSAO'];

  $sql="select numreg from comissoes_prestadora where idCOMISSAO=$idCOMISSAO and idPRODUTO=$idPRODUTO  ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if ( mysql_num_rows($resultado)==0 ) 
    mysql_query("insert into comissoes_prestadora(idCOMISSAO, idPRODUTO, $parcela) select $idCOMISSAO, $idPRODUTO, $vlr") or  die (mysql_error());
  else {
    $row = mysql_fetcH_object($resultado);
    $numreg = $row->numreg;
    
    mysql_query("update comissoes_prestadora set $parcela = $vlr where numreg=$numreg;") or  die (mysql_error());
  }  
}    

/*****************************************************************************************/
if ($acao=='valores') {
  $descricao = $_REQUEST['desc'];   

	$resp = '<table class=frmJANELA border=1 width="100%" cellpadding=3 >' .
					'<tr><td><table width="100%" > ' .
					'	<tr width="100%" >' .
					"		<td style=\"width:90%\" style=\"cursor: move;\"><span class=lblTitJanela id=tituloVALORES>Tipo de comissão: $descricao</span></td>" .
					'		<td><span onclick="fecharVALORES()" style="cursor: pointer;" class=lblTitJanela>[ X ]</td>' .
					'	</tr></table>' .
					'</td</tr>' .
          '<tr>' .
          ' <td valign="top" height="95%" >' .
          '   <div id="titVALORES">@titVALORES</div>' .
          '   <div id="divVALORES" style="overflow:auto;min-height:95%;height:95%">@divVALORES</div>' .
          ' </td>' .
          '</tr>' .
					'</table>';
					
  $sql  = "select prod.numreg as idPRODUTO, prod.descricao, ifnull(op.nome, '') as nomeOPERADORA, prod.idOPERADORA, prod.ativo,  ".
          "  comi.numreg as idCOMISSAO, ifnull(p1a, '-') as p1a, ifnull(p2a, '-') as p2a, ifnull(p3a, '-') as p3a,  ".
          " ifnull(p4a, '-') as p4a, ifnull(p5a, '-') as p5a, ifnull(adesao, '-') as adesao, ifnull(pVITALICIA, '-') as pVITALICIA ".
          "from tipos_contrato prod ".
          "left join operadoras op ".
          '   on op.numreg=prod.idOPERADORA '.
          "left join comissoes_prestadora comi " .
          "    on  comi.idCOMISSAO=$vlr and comi.idPRODUTO=prod.numreg  ".
          " where ifnull(prod.ativo,'')='S' ".     
          " order by op.nome, prod.descricao " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.3;
  $largura4 = $_SESSION['largIFRAME'] * 0.1;   // 1a
  $largura5 = $_SESSION['largIFRAME'] * 0.1;   // 2a
  $largura6 = $_SESSION['largIFRAME'] * 0.1;   // 3a
  $largura7 = $_SESSION['largIFRAME'] * 0.1;   // 4a
  $largura8 = $_SESSION['largIFRAME'] * 0.1;   // 5a
  $largura9 = $_SESSION['largIFRAME'] * 0.1;   // vitalícia  
    
	$header = "$largura1 px,Produto|$largura4 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_1ª|$largura5 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_2ª|$largura6 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_3ª".
	         "|$largura7 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_4ª|$largura8 px,&nbsp;&nbsp;&nbsp;&nbsp;5ª|$largura9 px,Vitalícia";
   
  $titVALORES = tabelaPADRAO('width="97%" ', $header ) . '</table>';
  $divVALORES = '<table id="tabVALORES" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
      $largura6="width=\"$largura6 px\"";      
      $largura7="width=\"$largura7 px\"";      
      $largura8="width=\"$largura8 px\"";      
      $largura9="width=\"$largura9 px\"";      
    } else {    
      $largura1='';$largura4='';$largura5='';$largura6='';$largura7='';$largura8='';$largura9='';
    }
    $i++;
  
    $p1a=($row->p1a==0) ? '-' : "$row->p1a%";
    $p2a=($row->p2a==0) ? '-' : "$row->p2a%";    
    $p3a=($row->p3a==0) ? '-' : "$row->p3a%";    
    $p4a=($row->p4a==0) ? '-' : "$row->p4a%";    
    $p5a=($row->p5a==0) ? '-' : "$row->p5a%";
    $pVITALICIA=($row->pVITALICIA==0) ? '-' : "$row->pVITALICIA%";        
    
        
    $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>$row->nomeOPERADORA - $row->descricao (<font color=blue><b>$row->idPRODUTO</font></b>)</td>".
            "<td align=\"right\" $largura4 @mouse1>$p1a</td>".
            "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
            "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
            "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
            "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
            "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
            "</tr>";

    if ($i % 2==0) {
      $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
      $cor='lightgrey';
    } 
    else {
      $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
      $cor='#F6F7F7';
    }
    $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
    $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
    $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
    $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
    $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
    $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
            
    $divVALORES .= $lin;
  }
  $resp = str_replace('@titVALORES', $titVALORES, $resp);
  $resp = str_replace('@divVALORES', $divVALORES, $resp);  
}					

 
/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update tipos_comissao_prestadora set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  
  if ($id=='') 
    $sql = "insert into tipos_comissao_prestadora(nome, ativo) values('$cmps[0]', 'S')";
  
  else  
    $sql = "update tipos_comissao_prestadora set nome='$cmps[0]' where numreg=$id"; 
  
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
  
  $sql  = "select idComiPadraoPrestadora from configuracao";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $comiPADRAO=$row->idComiPadraoPrestadora;
  mysql_free_result($resultado);

  $sql  = "select numreg, nome, ativo ".
          " from tipos_comissao_prestadora " .
          ($ativos=='S' ? " where ifnull(ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ativo,'')<>'S' " : "" ) .          
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.8;
    
	$header = "$largura1 px,Número|$largura2 px,Descrição";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
    } else {    
      $largura1='';$largura2='';
    }
    $i++;
  
    $padrao='';
    if ($comiPADRAO==$row[0]) $padrao='<font color=blue face=arial style="font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>** COMISSÃO PADRÃO **</b></font>';
      
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1] $padrao</td>".
            "</tr>";
            
    
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('funcionario.txt', 'r');
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
    $sql  = "select numreg, nome, ativo " .
            "from tipos_comissao_prestadora rep ".
            "where numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);    
        
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


