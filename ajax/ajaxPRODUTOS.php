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
          " from produtos " .
          " where numero=$vlr ";
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='ok';
  
  if (mysql_num_rows($resultado)>0)  {  
    if ($op=='incluir') $resp = 'jaCAD';
    else {
      $row = mysql_fetcH_object($resultado);      
      if ($row->numREG != $numREG_editando) $resp = 'jaCAD';
    }
  }      
}


/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update produtos set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[0];
  $descricao = $cmps[1];
  $operadora = $cmps[2];
  $numero = $cmps[3];
  
  if ($id=='') 
    $sql = "insert into produtos(descricao,idOPERADORA, ativo, numero) ".
            " values('$descricao', $operadora,'S', $numero)";
  
  else  
    $sql = "update produtos set descricao='$descricao', idOPERADORA=$operadora, numero=$numero ".
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
  
  $sql  = "select prod.numreg, prod.descricao, ifnull(op.nome, '') as nomeOPERADORA, prod.idOPERADORA, prod.ativo, prod.numero  ".
          " from produtos prod ".
          " left join operadoras op ".
          '   on op.numreg=prod.idOPERADORA '.
          ($ativos=='S' ? " where ifnull(prod.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(prod.ativo, '')<>'S' " : "" ) .          
          ($operadora!='200' ? " and idOPERADORA=$operadora " : "" ) .          
          " order by op.nome, prod.descricao " ;

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.7;
  $largura3 = $_SESSION['largIFRAME'] * 0.2;  
    
	$header = "$largura1 px,Número|$largura2 px,Nome|$largura3 px,Operadora";
   
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
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[5]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "<td align=\"left\" $largura3>$row[2] ($row[3])</td>".            
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[4]!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('produto.txt', 'r');
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
    $sql  = "select prod.numreg, prod.descricao, op.nome as nomeOPERADORA, prod.idOPERADORA, ifnull(prod.numero, '') as numero  ".
            "from produtos prod ".
            "left join operadoras op ".
            '   on op.numreg=prod.idOPERADORA '. 
            "where prod.numreg=$vlr ";
  
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->descricao, $resp);
    $resp=str_replace('vNUMERO', $row->numero, $resp);    
    $resp=str_replace('vOPERADORA', $row->idOPERADORA, $resp);    
    $resp=str_replace('v_OPERADORA', $row->nomeOPERADORA, $resp);    
    $resp=str_replace('@numREG', $vlr, $resp);
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
    
        
    $resp=str_replace('vOPERADORA', $row->idOPERADORA, $resp);    
    $resp=str_replace('v_OPERADORA', $row->nomeOPERADORA, $resp);    

  
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vNUMERO', '', $resp);    
    
    $resp=str_replace('@numREG', '', $resp);
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


