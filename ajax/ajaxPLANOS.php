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
  mysql_query("update configuracao set numTabelaPadrao=$vlr ")  or  die (mysql_error());
    
  echo('ok'); die();
}



/*****************************************************************************************/
if ($acao=='mudarSITUACAO_PLANO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update planos set ativo=case ativo when 'S' then 'N' else 'S' end where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='mudarSITUACAO_TABELA') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update tabelasprecos set ativo=case ativo when 'S' then 'N' else 'S' end where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}


/*****************************************************************************************/
IF ($acao=='lerPLANOS') {
  
  $ativos = $_REQUEST['ativos'];
  $idTABELA = $_REQUEST['idTABELA'];
  
  $sql  = "select pl.numero, pl.nome, pl.ativo, ifnull(pr.precos, '') as precos,  ".
          " ifnull(pr.precos2, '') as precos2, ifnull(pr.precos3, '') as precos3, ".
          " ifnull(pr.precos4, '') as precos4, ifnull(pr.precos5, '') as precos5, ".
          " ifnull(pr.precos6, '') as precos6, ifnull(pr.precos7, '') as precos7, ".
          " ifnull(pr.precos8, '') as precos8 " . 
          " from planos pl ".
          "left join precosplanos pr on " .
          "   pr.numPLANO = pl.numero and pr.numTABELA = $idTABELA " .  
          ($ativos=='S' ? " where ifnull(pl.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(pl.ativo,'')<>'S' " : "" ) .          
          "order by pl.numero " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $larguraPRECOS = $_SESSION['largIFRAME'] * 0.8;
  
  $faixas = array('0..18','19..23','24..28','29..33','34..38','39..43','44..48','49..53','54..58','+59');
  $titPRECOS = '';
  for ($i=0; $i<10; $i++) {
    $larg = round($larguraPRECOS * .1) ;
    $titPRECO = $faixas[$i];
    
    $titPRECOS .= "|$larg px_PRECO,$titPRECO";
  }    
    
	$header = "$largura1 px,Plano$titPRECOS ";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table rules=rows id="tabREGs" width="99%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
    } else {    
      $largura1='';
    }
    $i++;

    $cor = '#a9b2ca';
     
    // linha para nome do plano
    $lin = "<tr  style=\"@mudaCOR border-bottom:2px solid white;\" >" . 
            "<td style='color:blue;' onclick=\"mudarVLR(this.id)\" id=\"$row->numero" . "_0\" ".
            "align=\"left\"  onmouseout=\"this.style.backgroundColor='white'; \" " .
      " onmouseover=\"colATUAL='$row->numero" . "_0' " .
      ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \"  " . 
            " $largura1>$row->nome &nbsp;&nbsp;&nbsp;&nbsp;(Nº: <font color=blue><b>$row->numero</b></font>)</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>".
            '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';            
    $lin= str_replace('@mudaCOR', (($row->ativo!='S') ? 'color:red;' : ''), $lin);
    $resp = $resp . ($lin);    


    /* separa string precos em pedaços com 10 bytes */
    for ($uu=1; $uu<=8; $uu++)   {

      $precos='';
      for ($i=0; $i<10; $i++) {
        
        if ($uu==1) {$valores = $row->precos; $tit='Individual';}
        if ($uu==2) {$valores = $row->precos2; $tit='Individual com débito - 5%';}
        if ($uu==3) {$valores = $row->precos3; $tit='2 pessoas - 10%';}
        if ($uu==4) {$valores = $row->precos4; $tit='2 pessoas com débito - 15%';}
        if ($uu==5) {$valores = $row->precos5; $tit='3 pessoas - 15%';}
        if ($uu==6) {$valores = $row->precos6; $tit='3 pessoas com débito - 20%';}
        if ($uu==7) {$valores = $row->precos7; $tit='4 pessoas - 20%';}
        if ($uu==8) {$valores = $row->precos8; $tit='4 pessoas com débito - 25%';}
        
        $preco =  number_format(substr($valores, 7 * $i, 7), 2, ',', '')  ; 
        
        $coluna = ($i+1) . "P$uu";
        $precos .= "<td onclick=\"mudarVLR(this.id)\" align=\"right\" width=\"$larg px\" " .
        " onmouseout=\"this.style.backgroundColor='white'; \" " .
        " onmouseover=\"colATUAL='$row->numero" . "_$coluna' " .
        ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" id=\"$row->numero" .
        "_$coluna\">$preco</td>";
      }  
        
      // linha para preços          
      $lin = "<tr style=\"@mudaCOR border-bottom:2px solid white;\" ><td align=right style='color:grey;'>$tit</td>$precos</tr>";
      $lin= str_replace('@mudaCOR', (($row->ativo!='S') ? 'color:red;' : ''), $lin);      
      $resp .= $lin;
    
    }
    
    // separa string descontos em pedaços com 10 bytes
    /* 
    $descontos='';
    for ($i=0; $i<7; $i++) {
      $desconto =  substr($row->descontos, 7 * $i, 7);
      if (trim($desconto)=='') $desconto='-'; 
      
      if ($i==0) $desconto1=$desconto;
      if ($i==1) $desconto2=$desconto;      
      if ($i==2) $desconto3=$desconto;      
      if ($i==3) $desconto4=$desconto;      
      if ($i==4) $desconto5=$desconto;      
      if ($i==5) $desconto6=$desconto;      
      if ($i==6) $desconto7=$desconto;      
    }   */ 

    
/*
    // linha para descontos    
    $lin = "<tr style=\"@mudaCOR border-bottom:2px solid grey;text-align:right\" ><td colspan='11'>".
          "<table width='100%'>".
          '<tr style="color:grey">'.
          ' <td width="14%">Individual com débito</td><td width="14%">2 pessoas</td><td width="14%">2 pessoas débito</td>'.
          ' <td width="14%">3 pessoas</td><td width="14%">3 pessoas débito</td>'.
          ' <td width="14%">4 pessoas</td><td width="14%">4 pessoas débito</td>'.
          '</tr>'.
          '<tr style="text-align:right;">'.
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_1' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_1'>$desconto1</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_2' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_2'>$desconto2</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_3' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_3'>$desconto3</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_4' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_4'>$desconto4</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_5' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_5'>$desconto5</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_6' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_6'>$desconto6</td>" .                  
          " <td onclick='mudarDESCONTO(this.id);' width='14%'  onmouseout=\"this.style.backgroundColor='white'; \" " .
                  " onmouseover=\"colATUAL='DESCONTO$row->numero" . "_7' " .
                  ";this.style.backgroundColor='$cor';this.style.cursor='pointer'; \" " .
                  "id='DESCONTO$row->numero" . "_7'>$desconto7</td>" .                  
          '</tr>'.
          '</table></td></tr>';
    
    $resp = $resp . ($lin);
    
*/    
    $resp= str_replace('@mudaCOR', (($row->ativo!='S') ? 'color:red;' : ''), $resp);
    
    
  }
  $resp .= '^'.$qtdeREGS;
}
    



/*****************************************************************************************/
IF ($acao=='lerTABELAS') {
  
  $ativos = $_REQUEST['ativos'];
  
  $sql  = "select NUMERO,NOME, ATIVO, ifnull(config.numTabelaPadrao, '') as numTabelaPadrao".
          " from tabelasprecos tab ".
          "left join configuracao config " .
          "   on config.numTabelaPadrao = tab.numero " .           
          ($ativos=='S' ? " where ifnull(ATIVO, 'N')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ATIVO, 'N')<>'S' " : "" ) .
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp = '<select id="lstTABS" onchange="lerPLANOS()">';  
    
  if (mysql_num_rows($resultado)>0) { 
    while ($row = mysql_fetcH_object($resultado)) {
    
      $lin = "<option @mudaCOR value=\"$row->NUMERO\">$row->NOME @padrao</option>";  
              
      if ($row->numTabelaPadrao!='') {
        $lin= str_replace('@mudaCOR', 'style="color:blue;font-size:15px" selected ', $lin);
        $lin= str_replace('@padrao', '       < EM USO >', $lin);
      }else  
        $lin= str_replace('@mudaCOR', (($row->ATIVO!='S') ? 'style="color:red"' : ''), $lin);      
      
      $lin= str_replace('@padrao', '', $lin);
  
      $resp = $resp . ($lin);
    }
  }
  else $resp = 'nada';  
}


/*****************************************************************************************/
if ($acao=='mudarVALOR') {
  $id = $_REQUEST['id'];
  $novoVALOR = $_REQUEST['vlr'];
  $qualPRECO = $_REQUEST['preco'];
  
  $numPLANO = substr($id, 0, strpos($id, '_'));
  $numCOLUNA = substr($id, strpos($id, '_')+1);
  
  $idTABELA = $_REQUEST['idTABELA'];
  
  if ( $numCOLUNA == 0 ) 
    $sql = "update planos set nome='$novoVALOR' where numero=$numPLANO";
  
  else {
    $precos = explode(';', $_REQUEST['precos'] );
    $strPRECOS='';
    for ($t=0; $t<count($precos); $t++) {
      
      if ($numCOLUNA==$t+1) 
        $strPRECOS .= str_pad($novoVALOR, 7, ' ', 0);
      else
        $strPRECOS .= str_pad($precos[$t], 7, ' ', 0);        
    }
    $strPRECOS = str_replace(',','.',$strPRECOS);  
      
    $varPRECO = $qualPRECO==1 ? 'precos' : ("precos$qualPRECO");
    $resultado = mysql_query("select $varPRECO from precosplanos where numPLANO=$numPLANO and numTABELA=$idTABELA", $conexao) or 
      die (mysql_error());
  
    if ( mysql_num_rows($resultado) ==0 )
      $sql = "insert into precosplanos($varPRECO,numPLANO,numTABELA) select '$strPRECOS',$numPLANO,$idTABELA";
    else
      $sql = "update precosplanos set $varPRECO='$strPRECOS' where numPLANO=$numPLANO and numTABELA=$idTABELA";

    mysql_free_result($resultado);                
  }
  //echo($sql);die();
  mysql_query($sql) or  die (mysql_error());
    
  echo('ok'); die();
}


/*****************************************************************************************/
if ($acao=='mudarDESCONTOS') {
  $id = $_REQUEST['id'];
  $novoVALOR = $_REQUEST['vlr'];
  
  $numPLANO = substr(str_replace('DESCONTO','',$id), 0, strpos(str_replace('DESCONTO','',$id), '_'));
  $numCOLUNA = substr($id, strpos($id, '_')+1);
  
  $idTABELA = $_REQUEST['idTABELA'];
  
  if ( $numCOLUNA == 0 ) 
    $sql = "update planos set nome='$novoVALOR' where numero=$numPLANO";
  
  else {
    $descontos = explode(';', $_REQUEST['descontos'] );
    $strDESCONTOS='';
    for ($t=0; $t<count($descontos); $t++) {
      
      if ($numCOLUNA==$t+1) 
        $strDESCONTOS .= str_pad($novoVALOR, 7, ' ', 0);
      else
        $strDESCONTOS .= str_pad($descontos[$t], 7, ' ', 0);        
    }
    $strDESCONTOS = str_replace(',','.',$strDESCONTOS);  
      
    $resultado = mysql_query("select precos from precosplanos where numPLANO=$numPLANO and numTABELA=$idTABELA", $conexao) or 
      die (mysql_error());
  
    if ( mysql_num_rows($resultado) ==0 )
      $sql = "insert into precosplanos(descontos,numPLANO,numTABELA) select '$strDESCONTOS',$numPLANO,$idTABELA";
    else
      $sql = "update precosplanos set descontos='$strDESCONTOS' where numPLANO=$numPLANO and numTABELA=$idTABELA";

    mysql_free_result($resultado);                
  }
  //echo($sql);die();
  mysql_query($sql) or  die (mysql_error());
    
  echo('ok'); die();
}


/*****************************************************************************************/
if ($acao=='novaTABELA') {
  $vlr = $_REQUEST['vlr'];

  $sql = "insert into tabelasprecos(nome,ativo) select '$vlr','S'";

  //echo($sql);die();
  mysql_query($sql) or  die (mysql_error());
    
  echo('ok'); die();
}


/*****************************************************************************************/
if ($acao=='editarTABELA') {
  $nome = $_REQUEST['vlr'];
  $idTABELA = $_REQUEST['idTABELA'];

  $sql = "update tabelasprecos set nome='$nome' where numero=$idTABELA";

  //echo($sql);die();
  mysql_query($sql) or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='novoPLANO') {
  $nome = $_REQUEST['vlr'];

  $sql = "insert into planos(nome,ativo) select '$nome', 'S'";

  //echo($sql);die();
  mysql_query($sql) or  die (mysql_error());
    
  echo('ok'); die();
}



/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


