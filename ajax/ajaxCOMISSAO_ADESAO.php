<?
header("Content-Type: text/html; charset=iso-8859-1");


session_start();

require_once( '..'.$_SESSION['barra'].'includes'.$_SESSION['barra'].'definicoes.php'  );
require_once( '..'.$_SESSION['barra'].'includes'.$_SESSION['barra'].'funcoes.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$resp = 'INEXISTENTE';

/*****************************************************************************************/
if ($acao=='padrao') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update configuracao set idComiAdesaoPadraoRepresentante=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}          
          


/*****************************************************************************************/
if ($acao=='editarCOMISSAO') {
  $idPRODUTO = $_REQUEST['prod'];
  $parcela = $_REQUEST['parcela'];    
  $vlr = $_REQUEST['vlr'];  
  $idCOMISSAO = $_REQUEST['idCOMISSAO'];

  $sql="select numreg from comissoes_adesao where idCOMISSAO=$idCOMISSAO and idPRODUTO=$idPRODUTO  ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if ( mysql_num_rows($resultado)==0 ) 
    mysql_query("insert into comissoes_adesao(idCOMISSAO, idPRODUTO, $parcela) select $idCOMISSAO, $idPRODUTO, $vlr") or  die (mysql_error());
  else {
    $row = mysql_fetcH_object($resultado);
    $numreg = $row->numreg;
    
    mysql_query("update comissoes_adesao set $parcela = $vlr where numreg=$numreg;") or  die (mysql_error());
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
					'</td></tr>' .
          '<tr>' .
          ' <td valign="top"  >' .
          '   <div>@titVALORES</div>' .
          '   <div style="overflow:auto;min-height:400px;height:400px;">@divVALORES</div>' .
          ' </td>' .
          '</tr>' .
					'</table>';

					
  $sql  = "select prod.numreg as idPRODUTO, prod.descricao, ifnull(op.nome, '') as nomeOPERADORA, prod.idOPERADORA, prod.ativo,  ".
          "  comi.numreg as idCOMISSAO, ifnull(adesao, '-') as adesao  ".
          "from tipos_contrato prod ".
          "left join operadoras op ".
          '   on op.numreg=prod.idOPERADORA '.
          "left join comissoes_adesao comi " .
          "    on  comi.idCOMISSAO=$vlr and comi.idPRODUTO=prod.numreg  ".
          " where ifnull(prod.ativo,'')='S' ".     
          " order by op.nome, prod.descricao " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.8;
  $largura4 = $_SESSION['largIFRAME'] * 0.2;   // adesao
    
	$header = "$largura1 px,Produto|$largura4 px,Adesão";
   
  $titVALORES = tabelaPADRAO('width="97%" ', $header ) . '</table>';
  $divVALORES = '<table id="tabVALORES" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura4="width=\"$largura4 px\"";      
    } else {    
      $largura1='';$largura4='';
    }
    $i++;
  
    $adesao=($row->adesao==0) ? '-' : "$row->adesao%";
    
        
    $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=left $largura1>$row->nomeOPERADORA - $row->descricao (<font color=blue><b>$row->idPRODUTO</font></b>)</td>".
            "<td align=right $largura4 @mouse1>$adesao</td>".
            "</tr>";

    if ($i % 2==0) {
      $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
      $cor='lightgrey';
    } 
    else {
      $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
      $cor='#F6F7F7';
    }
    $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
            
    $divVALORES .= $lin;
  }
  $divVALORES .= '</table>';  
  $resp = str_replace('@titVALORES', $titVALORES, $resp);
  $resp = str_replace('@divVALORES', $divVALORES, $resp);  
}					

 
/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update tipos_comissao_adesao set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  
  if ($id=='') 
    $sql = "insert into tipos_comissao_adesao(nome, ativo) values('$cmps[0]', 'S')";
  
  else  
    $sql = "update tipos_comissao_adesao set nome='$cmps[0]' where numreg=$id"; 
  
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
  
  $sql  = "select idComiAdesaoPadraoRepresentante from configuracao";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $comiPADRAO=$row->idComiAdesaoPadraoRepresentante;
  mysql_free_result($resultado);

  $sql  = "select numreg, nome, ativo ".
          " from tipos_comissao_adesao " .
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
            "from tipos_comissao_adesao rep ".
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


