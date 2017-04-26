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
if ($acao=='resetarSENHA') {
  $id = $_REQUEST['vlr'];
  
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  if ($logado[1]>2) die('naopode');
  
  mysql_query("update operadores set senha='123' where numero=$id") or  die (mysql_error());
    
  echo('ok'); die();
}    


/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update operadores set ativo=case ativo when 'S' then 'N' else 'S' end where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  $permissoes = $_REQUEST['vlr2'];
  $escritorios = $_REQUEST['vlr3'];
  
  $id = $cmps[1];
  
  if ($id=='') 
    $sql = "insert into operadores(nome, ativo, senha, permissoes) values('$cmps[0]', 'S', '123', '$permissoes')";
  
  else  
    $sql = "update operadores set nome='$cmps[0]',permissoes='$permissoes',escritorios='$escritorios' where numero=$id"; 
  
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
  
  $sql  = "select numero, nome, ativo ".
          " from operadores " .
          ($ativos=='S' ? " where ifnull(ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ativo,'')<>'S' " : "" ) .          
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.8;
    
	$header = "$largura1 px,Número|$largura2 px,Nome";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
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
  
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {
  $arq = fopen('operador.txt', 'r');
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
      if ($vlr==1)
        $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr <br><font color=red><span id=lblADMIN>** ADMINISTRADOR DO SISTEMA **</span></font>",$resp);
      else
        $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  $sql  = "select nome, numero  " .
          "from escritorios  ".
          " where ifnull(ativo,'')='S' " .
          "order by numero  ";
  $rsESCRITORIOS = mysql_query($sql) or die (mysql_error());  
  $escrit = "<table><tr>";

  $chk=0;
  while ($row = mysql_fetcH_object($rsESCRITORIOS)) {
    $escrit .= "<td><table onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" ".  
                "   onmouseout=\"this.style.backgroundColor='#FFCCCC';\" onclick='checarESCRIT($chk);'> ".   					
          			"    <tr>". 
                "     <td onclick='checarESCRIT($chk);'><input type='checkbox' id='chkESCRIT' name='chkESCRIT' value=$row->numero checked_{$row->numero}_ /></td> ".
                "     <td>$row->nome</td> ".
                "    </tr> ".
  		          "</table></td>";
//                "<td width='10px'>&nbsp;</td>";
    $chk++;
  }
  $escrit .="</tr></table>";

  if ( mysql_num_rows($rsESCRITORIOS)==0 ) 
    $resp=str_replace('@escritorios', '<font color=red>NENHUM ESCRITÓRIO DEFINIDO</font>', $resp);


  if ($acao!='incluirREG')   {
    $sql  = "select numero, nome, ativo, permissoes, escritorios " .
            "from operadores rep ".
            "where numero=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);    

    for ($r=0; $r<=25; $r++) {
      $letra = chr(65+$r);
      if ($vlr==1) 
        $resp=str_replace("checked$letra", 'checked', $resp);
      else { 
        if (strpos($row->permissoes, $letra)!==false ) $resp=str_replace("checked$letra", 'checked', $resp);
        else $resp=str_replace("checked$letra", '', $resp);
      }
    }
    $escritorios = $row->escritorios;

    if ( mysql_num_rows($rsESCRITORIOS)>=1 ) {
      mysql_data_seek($rsESCRITORIOS, 0);
      while ($row = mysql_fetcH_object($rsESCRITORIOS)) {
        
        $codEQUIVALENTE = chr($row->numero+64);
  
        if ($vlr==1)
          $escrit=str_replace("checked_{$row->numero}_", 'checked', $escrit);
        else {
          if (strpos($escritorios , $codEQUIVALENTE)!==false ) $escrit=str_replace("checked_{$row->numero}_", 'checked', $escrit);
          else $escrit=str_replace("checked_{$row->numero}_", '', $escrit);
        }
      }
    } 
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);

    for ($r=0; $r<=10; $r++) {
      $letra = chr(65+$r); 
      $resp=str_replace("checked$letra", '', $resp);
    }
    if ( mysql_num_rows($rsESCRITORIOS)>=1 ) {
      mysql_data_seek($rsESCRITORIOS, 0);
      while ($row = mysql_fetcH_object($rsESCRITORIOS)) {
        
        $codEQUIVALENTE = chr($row->numero+64);
  
        $escrit=str_replace("checked_{$row->numero}_", '', $escrit);
      }
    }
  }
  $resp=str_replace('@escritorios', $escrit, $resp);

}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


