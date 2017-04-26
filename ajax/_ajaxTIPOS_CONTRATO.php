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
  
  mysql_query("update tipos_contrato set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[0];
  $descricao = $cmps[1];
  $operadora = $cmps[2];
  $adesao = $cmps[3];  
  $tipo = $_REQUEST['tipo'];
  $vlrPRODUCAO = $_REQUEST['prod']=='true' ? 'S' : 'N';

  if ($id=='') 
    $sql = "insert into tipos_contrato(descricao,idOPERADORA, ativo, vlradesao, cpf_cnpj,qtde1,qtde2,perc1,qtde3,qtde4,".
              " perc2,qtde5,qtde6,perc3,vlrPRODUCAO, vidas1, vidas2,vidas3,vidas4,vidas5,vidas6,vidas7,vidas8,vidas9,vidas10) ".
            " values('$descricao', $operadora,'S', $adesao, $tipo,$cmps[4],$cmps[5],$cmps[6],".
            " $cmps[7],$cmps[8],$cmps[9],$cmps[10],$cmps[11],$cmps[12],'$vlrPRODUCAO', $cmps[13],$cmps[14],$cmps[15],$cmps[16], ".
            " $cmps[17], $cmps[18], $cmps[19], $cmps[20], $cmps[21], $cmps[22]); ";
  else  
    $sql = "update tipos_contrato set descricao='$descricao', idOPERADORA=$operadora, vlradesao=$adesao, cpf_cnpj=$tipo, ".
          " qtde1=$cmps[4], qtde2=$cmps[5], perc1=$cmps[6], qtde3=$cmps[7], qtde4=$cmps[8], perc2=$cmps[9], qtde5=$cmps[10], ".
          " qtde6=$cmps[11], perc3=$cmps[12], vlrPRODUCAO='$vlrPRODUCAO', vidas1=$cmps[13], vidas2=$cmps[14], vidas3=$cmps[15], vidas4=$cmps[16],  ".
          " vidas5=$cmps[17], vidas6=$cmps[18], vidas7=$cmps[19], vidas8=$cmps[20], vidas9=$cmps[21], vidas10=$cmps[22] ".
            " where numreg=$id"; 
  
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
  
  $ativos = $_REQUEST['ativos'];
  $operadora = $_REQUEST['operadora'];  
  
  $sql  = "select tip.numreg, descricao, ifnull(op.nome, '') as nomeOPERADORA, idOPERADORA, tip.ativo, vlrADESAO, cpf_cnpj  ".
          " from tipos_contrato tip ".
          " left join operadoras op ".
          '   on op.numreg=tip.idOPERADORA '.
          ($ativos=='S' ? " where ifnull(tip.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(tip.ativo, '')<>'S' " : "" ) .          
          ($operadora!='200' ? " and idOPERADORA=$operadora " : "" ) .          
          " order by descricao " ;

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.4;
  $largura3 = $_SESSION['largIFRAME'] * 0.2;  
  $largura4 = $_SESSION['largIFRAME'] * 0.1;  
  $largura5 = $_SESSION['largIFRAME'] * 0.2;
    
	$header = "$largura1 px,Número|$largura2 px,Nome|$largura3 px,Operadora|$largura4 px,Vlr adesão|$largura3 px,Cliente identificado por";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";
    } else {    
      $largura1='';$largura2=''; $largura3='';
    }
    $i++;
    
    $adesao=number_format($row[5], 2, ',', '')  ;
    $ident = $row[6]==1 ? 'CPF' : 'CNPJ';
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "<td align=\"left\" $largura3>$row[2] ($row[3])</td>".            
            "<td align=\"right\" $largura4>$adesao</td>".
            "<td  $largura3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$ident</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[4]!='S') ? 'style="color:red"' : ''), $lin);

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('tipo_contrato.txt', 'r');
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
    $sql  = "select tip.numreg, tip.descricao, op.nome as nomeOPERADORA, idOPERADORA, vlrADESAO, cpf_cnpj, ".
            " qtde1, qtde2, perc1, qtde3, qtde4, perc2, qtde5, qtde6, perc3, vlrPRODUCAO, vidas1, vidas2, vidas3, vidas4, vidas5, vidas6, ".
            " vidas7, vidas8, vidas9, vidas10 ".
            "from tipos_contrato tip ".
            "left join operadoras op ".
            '   on op.numreg=tip.idOPERADORA '. 
            "where tip.numreg=$vlr ";
  
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->descricao, $resp);
    $resp=str_replace('vOPERADORA', $row->idOPERADORA, $resp);    
    $resp=str_replace('v_OPERADORA', $row->nomeOPERADORA, $resp);    

    $resp=str_replace('vQTDE1', $row->qtde1, $resp);
    $resp=str_replace('vQTDE2', $row->qtde2, $resp);
    $resp=str_replace('vPERC1', $row->perc1, $resp);

    $resp=str_replace('vQTDE3', $row->qtde3, $resp);
    $resp=str_replace('vQTDE4', $row->qtde4, $resp);
    $resp=str_replace('vPERC2', $row->perc2, $resp);

    $resp=str_replace('vQTDE5', $row->qtde5, $resp);
    $resp=str_replace('vQTDE6', $row->qtde6, $resp);
    $resp=str_replace('vPERC3', $row->perc3, $resp);
    
    $resp=str_replace('vVIDAS10', $row->vidas10, $resp);
    $resp=str_replace('vVIDAS9', $row->vidas9, $resp);
    $resp=str_replace('vVIDAS8', $row->vidas8, $resp);
    $resp=str_replace('vVIDAS7', $row->vidas7, $resp);
    $resp=str_replace('vVIDAS6', $row->vidas6, $resp);
    $resp=str_replace('vVIDAS5', $row->vidas5, $resp);
    $resp=str_replace('vVIDAS4', $row->vidas4, $resp);
    $resp=str_replace('vVIDAS3', $row->vidas3, $resp);
    $resp=str_replace('vVIDAS2', $row->vidas2, $resp);
    $resp=str_replace('vVIDAS1', $row->vidas1, $resp);                
    
    
    $resp=str_replace('vADESAO', number_format($row->vlrADESAO, 2, ',', ''), $resp);
    $resp=str_replace('@numREG', $vlr, $resp);

    if ($row->cpf_cnpj==1) { 
      $resp=str_replace('checked_1', 'checked', $resp);
      $resp=str_replace('checked_2', '', $resp);
    }
    else {
      $resp=str_replace('checked_2', 'checked', $resp);
      $resp=str_replace('checked_1', '', $resp);
    }
    if ($row->vlrPRODUCAO=='S') $resp=str_replace('checkedPROD', 'checked', $resp);
    else $resp=str_replace('checkedPROD', '', $resp);
        

  }    
  else {
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
    $sql  = "select ifnull(operadoraATUAL,1) as idOPERADORA, ifnull(op.nome,'') as nomeOPERADORA ".
            "from operadores ".
            "left join operadoras op ".
            "     on op.numreg=operadores.operadoraATUAL " . 
            " where numero=$logado[1]  ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);
    
    if ($row->idOPERADORA==200) {
      $idOPERADORA='';   $nomeOPERADORA='';
    } else {
      $idOPERADORA=$row->idOPERADORA;   $nomeOPERADORA=$row->nomeOPERADORA;    
    }
     
    $resp=str_replace('vOPERADORA', $idOPERADORA, $resp);    
    $resp=str_replace('v_OPERADORA', $nomeOPERADORA, $resp);
    
    $resp=str_replace('vADESAO', '0,00', $resp);        
  
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);

    $resp=str_replace('vQTDE1', '', $resp);
    $resp=str_replace('vQTDE2', '', $resp);
    $resp=str_replace('vPERC1', '', $resp);

    $resp=str_replace('vQTDE3', '', $resp);
    $resp=str_replace('vQTDE4', '', $resp);
    $resp=str_replace('vPERC2', '', $resp);

    $resp=str_replace('vQTDE5', '', $resp);
    $resp=str_replace('vQTDE6', '', $resp);
    $resp=str_replace('vPERC3', '', $resp);
    
    $resp=str_replace('vVIDAS10', '', $resp);
    $resp=str_replace('vVIDAS9', '', $resp);
    $resp=str_replace('vVIDAS8', '', $resp);
    $resp=str_replace('vVIDAS7', '', $resp);
    $resp=str_replace('vVIDAS6', '', $resp);
    $resp=str_replace('vVIDAS5', '', $resp);
    $resp=str_replace('vVIDAS4', '', $resp);
    $resp=str_replace('vVIDAS3', '', $resp);
    $resp=str_replace('vVIDAS2', '', $resp);
    $resp=str_replace('vVIDAS1', '', $resp);                
    


    $resp=str_replace('checked_1', 'checked', $resp);
    $resp=str_replace('checked_2', '', $resp);
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


