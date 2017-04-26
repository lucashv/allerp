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
  
  mysql_query("update grupos_venda set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  
  if ($id=='') 
    $sql = "insert into grupos_venda(nome, ativo, idCOMISSAO) values('$cmps[0]', 'S', $cmps[2])";
  
  else  
    $sql = "update grupos_venda set nome='$cmps[0]', idCOMISSAO=$cmps[2] where numreg=$id"; 
  
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
  
  $sql  = "select grp.numreg, grp.nome, grp.ativo, grp.idCOMISSAO, ifnull(tipcom.nome, '* erro *') as nomeCOMISSAO  ".
          " from grupos_venda grp " .
          "left join tipos_comissao tipcom ".
          "     on tipcom.numreg = idCOMISSAO ".
          ($ativos=='S' ? " where ifnull(grp.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(grp.ativo,'')<>'S' " : "" ) .          
          "order by grp.nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.4;
  $largura3 = $_SESSION['largIFRAME'] * 0.4;  
    
	$header = "$largura1 px,Número|$largura2 px,Nome|$largura3 px,Tipo de comissão";
   
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
  
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "<td align=\"left\" $largura3>$row[4] ($row[3])</td>".            
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('grupo_venda.txt', 'r');
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
    if (strpos($_SESSION['permissoes'], 'Z')) {
      $resp = str_replace('v_PERMISSAO', ", 'Z'", $resp);
      $resp = str_replace('v_READONLY', '', $resp);
    } else {
      $resp = str_replace('v_PERMISSAO', '', $resp);
      $resp = str_replace('v_READONLY', 'readonly', $resp);
    }

    $sql  = "select grp.numreg, grp.nome, grp.ativo, ifnull(grp.idCOMISSAO, '') as idCOMISSAO, ifnull(tipcom.nome, '* ERRO* ') as nomeCOMISSAO  " .
            "from grupos_venda grp ".
            "left join tipos_comissao tipcom ".
            "   on tipcom.numreg=grp.idCOMISSAO " .            
            "where grp.numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);
    
    $resp=str_replace('vCOMISSAO_REPRESENTANTE', $row->idCOMISSAO, $resp);
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', $row->nomeCOMISSAO, $resp);    
  }    
  else {
    $resp = str_replace('v_PERMISSAO', ", 'Z'", $resp);
    $resp = str_replace('v_READONLY', '', $resp);

    $sql  = "select ifnull(idComiPadraoRepresentante, '') as idCOMISSAO, ifnull(tipcom.nome, '') as nomeCOMISSAO ".
            " from configuracao ".
            "left join tipos_comissao tipcom ".
            "   on tipcom.numreg = idComiPadraoRepresentante " ;

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);
  
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);
    
    $resp=str_replace('vCOMISSAO_REPRESENTANTE', $row->idCOMISSAO, $resp);
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', $row->nomeCOMISSAO, $resp);    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


