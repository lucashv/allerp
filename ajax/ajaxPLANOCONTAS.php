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
IF ($acao=='verDUPLICIDADE') {

  $op = $_REQUEST['op'];
  $numREG_editando = $_REQUEST['numreg'];
  
  $sql  = "select descricao, numREG ".
          " from planocontas " .
          " where codigo=$vlr ";
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='ok';
  
  if (mysql_num_rows($resultado)>=0)  {
  
    if ($op=='incluir') $resp = 'jaCAD';
    else {
      $row = mysql_fetcH_object($resultado);
      
      if ($row->numREG != $numREG_editando) $resp = 'jaCAD';
    }
  }      
}


/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[3];
  
  if ($id=='') 
    $sql = "insert into planocontas(codigo, descricao, nivel) values($cmps[0], '$cmps[1]', $cmps[2])";
  
  else  
    $sql = "update planocontas set codigo=$cmps[0], descricao='$cmps[1]', nivel=$cmps[2] where numREG=$id"; 
    
//    die($sql);
  
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
  
  $sql  = "select numREG, codigo, descricao, nivel ".
          " from planocontas " .
          "order by codigo " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.5;
  $largura3 = $_SESSION['largIFRAME'] * 0.4;  
    
	$header = "$largura1 px,Código|$largura2 px,Descrição|$largura2 px,Nível";
   
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
  
    $codigo= ($row[1]==100 ? '1' : $row[1]);
    $codigo= ($row[1]==200 ? '2' : $codigo);
    
    $nivel = $row[3];
    $nivel = $nivel==1 ? 'interno' : $nivel;
    $nivel = $nivel==2 ? 'geral' : $nivel;
    $nivel = $nivel==3 ? 'ambos' : $nivel;
    $nivel = $row[1]==100 || $row[1]==200 ? '' : $nivel;    
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$codigo</td>".
            "<td align=\"left\" $largura2>$row[2]</td>".
            "<td align=\"center\" $largura3>$nivel</td>".            
            "</tr>";
            
    if ($row[1]==100 || $row[1]==200)
      $lin = str_replace('@mudaCOR', 'style="font-size:14px;color:blue"', $lin);
    else
      $lin = str_replace('@mudaCOR', '', $lin);          
      
    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('planoCONTAS.txt', 'r');
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
    $sql  = "select codigo, descricao, nivel " .
            "from planocontas ".
            "where numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDESCRICAO', $row->descricao, $resp);
    $resp=str_replace('vCODIGO', $row->codigo, $resp);    
    $resp=str_replace('vNIVEL', $row->nivel, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);    
        
  }    
  else {
    $resp=str_replace('vDESCRICAO', '', $resp);
    $resp=str_replace('vCODIGO', '', $resp);    
    $resp=str_replace('vNIVEL', '', $resp);
    $resp=str_replace('@numREG', '', $resp);    
    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


