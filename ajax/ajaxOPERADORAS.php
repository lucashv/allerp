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
IF ($acao=='excecoesParaMensNuncaPagas') { 
  $sql  = "select numreg, nome ".
          " from operadoras  ".
          " where 1aMensIgualVigencia='S' ".    
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='';
  $codigos='';
    
  while ($row = mysql_fetcH_object($resultado)) {
    $resp .= $resp=='' ? '' : ', ';
    $resp .= $row->nome;

    $codigos .= $codigos=='' ? '' : ', ';
    $codigos .= $row->numreg;
  }
  $resp .= ';' . $codigos;
}

/*****************************************************************************************/
if ($acao=='uploadARQ') {
 if (  $_FILES["arqLOGO"]["name"] == '' )
    die("<font color=red face=arial>ARQUIVO NÃO SELECIONADO</font>");

if (  $_FILES["arqLOGO"]["type"] != "image/png" )
  die("<font color=red face=arial>TIPO ERRADO DE ARQUIVO - SELECIONE IMAGEM PNG</font>");

if ($_FILES["arqLOGO"]["error"] > 0)  
  die("<font color=red face=arial>ERRO <br>CÓDIGO ERRO= ".
      $_FILES["file"]["error"]."</font>");

$arqDESTINO='logos/'.$_SERVER['REMOTE_ADDR'].'.png';
move_uploaded_file($_FILES["arqLOGO"]["tmp_name"], $arqDESTINO ) or 
  die("<font color=red face=arial>ERRO DE UPLOAD</font>");
  
die('<font color=blue face=arial>UPLOAD FEITO COM SUCESSO!</font><br>'.
  "<img src='$arqDESTINO' />");  

}    

/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update operadoras set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  
  if ($id=='') {
    //if (! file_exists( 'logos/'.$_SERVER['REMOTE_ADDR'].'.png' ))
    //  die('ERRO - Arquivo de logo não definido');
         
    $sql = "insert into operadoras(nome,ativo,segundoNOME,1aMensIgualVigencia,qtdeMENS) ".
            " values('$cmps[0]','S','$cmps[2]','$cmps[3]',$cmps[4])";
  }            
  else  
    $sql = "update operadoras set nome='$cmps[0]', segundoNOME='$cmps[2]', 1aMensIgualVigencia='$cmps[3]', qtdeMENS=$cmps[4] ".
    " where numreg=$id"; 
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();   
  else   {
    /* busca ultimo ID gerado */
    if ($id=='')     $id = mysql_insert_id();
      
    if ( file_exists( 'logos/'.$_SERVER['REMOTE_ADDR'].'.png') ) {
      $arqDESTINO='logos/'.$_SERVER['REMOTE_ADDR'].'.png';
      $arqLOGO='logos/'.str_pad($id, 5, '0', 0).'.png';
    
      unlink($arqLOGO);
      rename($arqDESTINO, $arqLOGO);
    }  
    
    $resp = 'OK;' . $id ;
  }
  
  
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $ativos = $_REQUEST['ativos'];
  
  $sql  = "select numreg, nome,  ativo ".
          " from operadoras  ".
          ($ativos=='S' ? " where ifnull(ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ativo, '')<>'S' " : "" ) .          
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.7;
  $largura3 = $_SESSION['largIFRAME'] * 0.2;  
    
	$header = "$largura1 px,Número|$largura2 px,Nome|$largura3 px,Logotipo";
   
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
    
    $arqLOGO='ajax/logos/'.str_pad($row[0], 5, '0', 0).'.png';  
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "<td align=\"left\" $largura3><img src='$arqLOGO' /></td>".            
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('operadora.txt', 'r');
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
    $sql  = "select numreg, nome,segundoNOME, 1aMensIgualVigencia as tipoDATA, ifnull(qtdeMENS, 1) as qtdeMENS ".
            "from operadoras ".
            "where numreg=$vlr ";
  
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('vSEGUNDO_NOME', $row->segundoNOME, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);

    $resp=str_replace('checkedQTDEMENS_1', ($row->qtdeMENS==1 ? 'checked' : ''), $resp);
    $resp=str_replace('checkedQTDEMENS_2', ($row->qtdeMENS==2 ? 'checked' : ''), $resp);
    $resp=str_replace('checkedQTDEMENS_3', ($row->qtdeMENS==3 ? 'checked' : ''), $resp);

    if ($row->tipoDATA=='S') {  
      $resp=str_replace('setado1', 'checked', $resp);
      $resp=str_replace('setado2', '', $resp);
      $resp=str_replace('setado3', '', $resp);
    }
    else if ($row->tipoDATA=='N') {
      $resp=str_replace('setado2', 'checked', $resp);
      $resp=str_replace('setado1', '', $resp);
      $resp=str_replace('setado3', '', $resp);
    }
    else if ($row->tipoDATA=='O') {
      $resp=str_replace('setado3', 'checked', $resp);
      $resp=str_replace('setado1', '', $resp);
      $resp=str_replace('setado2', '', $resp);
    }        
        
    $arqLOGO= "ajax/logos/" . str_pad($row->numreg, 5, '0', 0).'.png';
    $resp=str_replace('@logo', "<img src='$arqLOGO' />", $resp);
        
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vSEGUNDO_NOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);

    $resp=str_replace('setado1', 'checked', $resp);
    $resp=str_replace('setado2', '', $resp);
    $resp=str_replace('setado3', '', $resp);

    $resp=str_replace('checkedQTDEMENS_1', 'checked', $resp);
    $resp=str_replace('checkedQTDEMENS_2', '', $resp);
    $resp=str_replace('checkedQTDEMENS_3', '', $resp);

    $resp=str_replace('@logo', "", $resp);    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


