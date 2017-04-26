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

$MESES = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho',
          'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');



/*****************************************************************************************/
IF ( $acao=='editarRENOVACAO'  ) {
  $arq = fopen('renovacao.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $resp=str_replace('vRENOVACAO_CORRETOR', ($_REQUEST['corretor']=='0' ? '' : $_REQUEST['corretor']), $resp);
  $resp=str_replace('v_RENOVACAO_CORRETOR', $_REQUEST['lblcorretor'], $resp);
  $resp=str_replace('vPROTECAO', $_REQUEST['protecao'], $resp);
  $resp=str_replace('vPARCELAS', $_REQUEST['parcelas'], $resp);
  $resp=str_replace('vPREMIO', $_REQUEST['premio'], $resp);
  $resp=str_replace('vCOMISSAO', $_REQUEST['comissao'], $resp);

  $resp=str_replace('@idLINHA', $_REQUEST['idLIN'], $resp);
}

/*****************************************************************************************/
IF ( $acao=='editarSINISTRO'  ) {

  $arq = fopen('sinistro.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $resp=str_replace('vTIPOSINISTRO', ($_REQUEST['tipo']=='0' ? '' : $_REQUEST['tipo']), $resp);
  $resp=str_replace('v_TIPOSINISTRO', $_REQUEST['lbltipo'], $resp);
  $resp=str_replace('vDATASINISTRO', $_REQUEST['datasin'], $resp);
  $resp=str_replace('vDATALIBERACAO', $_REQUEST['datalib'], $resp);
  $resp=str_replace('vTERCEIROS', $_REQUEST['terceiros'], $resp);

  $resp=str_replace('@idLINHA', $_REQUEST['idLIN'], $resp);
}




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
  
  $id = $cmps[0];
  
  $cmps[9]=$cmps[9]!='null' ? "'$cmps[9]'" : $cmps[9];
  $cmps[3]=$cmps[3]!='null' ? "'$cmps[3]'" : $cmps[3];

  if ($id=='') 
    $sql = "insert into seguros_apolices(cliente, tipoCLIENTE, idTIPO, idSEGURADORA, idCORRETOR, dataASSINATURA, apolice, fones, ".
            "email, dataNASCIMENTO, vlrPREMIO, percentual, obs) values('$cmps[2]', $cmps[1], $cmps[6], $cmps[7], $cmps[8], $cmps[9], ".
            "  '$cmps[10]', '$cmps[4]', '$cmps[5]', $cmps[3], $cmps[11], $cmps[12], '$cmps[13]'); ";

  
  else  
    $sql = "update seguros_apolices set cliente='$cmps[2]', tipoCLIENTE=$cmps[1], idTIPO=$cmps[6], idSEGURADORA=$cmps[7], idCORRETOR=$cmps[8], ".
            " dataASSINATURA=$cmps[9], apolice='$cmps[10]', fones='$cmps[4]', ".
            "email='$cmps[5]', dataNASCIMENTO=$cmps[3], vlrPREMIO=$cmps[11], percentual=$cmps[12], obs='$cmps[13]' ".
            " where numreg=$id ";  
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error() . '<br><br>'.$sql;

  else   {
    if ($id=='')  $id = mysql_insert_id();

    $sql="delete from seguros_sinistros where idAPOLICE=$id" ;
    mysql_query($sql) or die( mysql_error() );

    if ($_REQUEST['sin']!='') {
      $sinistros = explode('|', $_REQUEST['sin']);
  
      for ($e=0; $e<count($sinistros); $e++) {
        $sin = explode(';', $sinistros[$e]);
        
        $tipo = $sin[0];        
        $dataSIN = $sin[1]=='null' ? 'null' : "'$sin[1]'";
        $dataLIB = $sin[2]=='null' ? 'null' : "'$sin[2]'";
        $terceiros = $sin[3]; 
    
        $sql="insert into seguros_sinistros(idAPOLICE, dataSINISTRO, dataLIBERACAO, idTIPO, terceiros) " .
              " select $id, $dataSIN, $dataLIB, $tipo, '$terceiros'  ";
    
        mysql_query($sql) or die( mysql_error() .'<br><br>'.$sql);
      }            
    }



    $sql="delete from seguros_renovacoes where idAPOLICE=$id" ;
    mysql_query($sql) or die( mysql_error() );

    if ($_REQUEST['renova']!='') {
      $renovacoes = explode('|', $_REQUEST['renova']);
  
      for ($e=0; $e<count($renovacoes); $e++) {
        $renova = explode(';', $renovacoes[$e]);
        
        $corretor = $renova[0];        
        $protecao = trim($renova[1])=='' ? 'null' : $renova[1];
        $parcelas = trim($renova[2])=='' ? 'null' : $renova[2];
        $premio = trim($renova[3])=='' ? 'null' : $renova[3];
        $comissao = trim($renova[4])=='' ? 'null' : $renova[4];
    
        $sql="insert into seguros_renovacoes(idAPOLICE, idCORRETOR, protecao,parcelas,vlrpremio,comissao) " .
              " select $id, $corretor, $protecao, $parcelas, $premio, $comissao  ";
    
        mysql_query($sql) or die( mysql_error() .'<br><br>'.$sql);
      }            
    }
    $resp = 'OK;' . $id;      
  }
  
  
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
   $ativos = $_REQUEST['ativos'];
   $tipoDATA = $_REQUEST['tipo'];

   $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy
   
   $palavra ='';
   if ($_REQUEST['vlr3']!='') $palavra = $_REQUEST['vlr3'];
  
 //else { 
     $dataTRAB = substr($dataTRAB, 4) . substr($dataTRAB, 2, 2) . substr($dataTRAB, 0, 2);
     $avancarDATA = $_REQUEST['vlr2'];       
     
     // obtem datas inicial e final do mes/ano sendo lido
     $sql = "select date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%Y%m%d') as dataINI,  ".  
            "       date_format(DATE_ADD(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA + 1  month), " . 
           "                       INTERVAL -1 day), '%Y%m%d') as dataFIN,  " .
           " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%m') as mesATUAL, " .
           " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%Y') as anoATUAL, "  .
           " date_format(DATE_ADD('$dataTRAB', INTERVAL $avancarDATA month), '%d%m%Y') as novaDATA_JAVASCRIPT " ;                
   
     $resultado = mysql_query($sql, $conexao) or die (mysql_error());
     $row = mysql_fetcH_object($resultado);
     $dataINI = $row->dataINI;
     $dataFIN = $row->dataFIN;
     $mesATUAL = (int)$row->mesATUAL;  
     $anoATUAL = $row->anoATUAL;
     $novaDATA_JAVASCRIPT = $row->novaDATA_JAVASCRIPT;  
     
     mysql_free_result($resultado);          
 // }      

  if ($palavra=='') {
    $somarASSINATURA = $tipoDATA=='assinadas' ? '0' : '12'; 
    $criterio =  "where DATE_ADD(apo.dataASSINATURA, INTERVAL $somarASSINATURA month) between '$dataINI' and '$dataFIN' " ;
  } 
  else {
//    $cmp = $_REQUEST['cmp'];
    $criterio =  "where apo.cliente like '%$palavra%' " ;
  }

  $sql  = "select apo.numreg, apo.cliente, apo.idTIPO, apo.idSEGURADORA, date_format(apo.dataASSINATURA, '%d/%m/%y') as data, tipoCLIENTE, ".
          " ifnull(tipapo.nome, '* erro *') as nomeTIPO, ifnull(seg.nome, '* erro *') as nomeSEGURADORA, ifnull(apo.excluido,0) as excluido,    ".
          " ifnull(tipcli.nome, '* erro *') as nomeTIPOCLIENTE " .
          "from seguros_apolices apo " .
          "left join seguros_tipos tipapo ".
          "     on tipapo.numreg = apo.idTIPO ".
          "left join seguros_seguradoras seg ".
          "     on seg.numreg = apo.idSEGURADORA ".
          "left join seguros_tiposcliente tipcli ".
          "     on tipcli.numreg = apo.tipoCLIENTE ".
          $criterio .
          ($ativos=='S' ? " and ifnull(apo.excluido,0)=0 " : "" ) .    
          ($ativos=='N' ? " and ifnull(apo.excluido,0)<>0 " : "" ) .          
          "order by apo.cliente " ;
//die($sql);  
$resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.4;
  $largura2 = $_SESSION['largIFRAME'] * 0.05;
  $largura3 = $_SESSION['largIFRAME'] * 0.2;
  $largura4 = $_SESSION['largIFRAME'] * 0.10;
    
	$header = "$largura1 px,Cliente|$largura3 px,Produto|$largura3 px,Seguradora|$largura4 px,Assinatura|$largura4 px,Tipo";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";
      $largura3="width=\"$largura3 px\"";
    } else {    
      $largura1='';$largura2='';$largura3='';$largura4='';
    }
    $i++;
  
    $seguradora = $row->nomeSEGURADORA=='' ? '' : "$row->nomeSEGURADORA ($row->idSEGURADORA)"; 
    $produto = $row->nomeTIPO=='' ? '' : "$row->nomeTIPO ($row->idTIPO)";

    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=$row->numreg onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row->cliente</td>".
            "<td align=\"left\" $largura3>$produto</td>".
            "<td align=\"left\" $largura3>$seguradora</td>".
            "<td align=\"center\" $largura4>$row->data</td>".
            "<td align=\"left\" $largura4>$row->nomeTIPOCLIENTE</td>".            
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row->excluido==1) ? 'style="color:red"' : ''), $lin);
               
    $resp = $resp . ($lin);
  }
  if ($palavra != '')
    $resp .= '^'.$qtdeREGS.'^FILTRO= '.$palavra;
  else
    $resp .= '^'.$qtdeREGS.'^'.($tipoDATA=='assinadas' ? 'Assinadas em ' : 'Vencendo em '). $MESES[ $mesATUAL-1 ] . '/' .$anoATUAL;

  $resp .=  '^' . $novaDATA_JAVASCRIPT ;

}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('seguro.txt', 'r');
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
  
  $usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;
  $tabRENOVACOES = '<table width="99%" id=tabRENOVACOES width="99%" cellpadding=3  cellspacing=0 ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
  $resp = str_replace('@titRENOVACOES',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "35%,Corretor|15%,Proteção (%)|15%,Parcelas|15%,Vlr Prêmio|15%,Comissão" ).'</table>', $resp);
  $resp=str_replace('@altDivRENOVACOES', ($usandoTelaMaior1024_768 ? '50px' : '70px'), $resp);

  $tabSINISTROS = '<table width="99%" id=tabSINISTROS width="99%" cellpadding=3  cellspacing=0 ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
  $resp = str_replace('@titSINISTROS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "45%,Tipo|10%,Data sinistro|10%,Data liberação|20%,Terceiros" ).'</table>', $resp);
  $resp=str_replace('@altDivSINISTROS', ($usandoTelaMaior1024_768 ? '50px' : '70px'), $resp);


  if ($acao!='incluirREG')   {
    $sql  = "select apo.tipoCLIENTE, ifnull(tipocli.nome, '') as nomeTIPOCLIENTE, apo.cliente, apo.apolice, ".
          "date_format(apo.dataASSINATURA, '%d/%m/%y') as dataASSINATURA, date_format(apo.dataNASCIMENTO, '%d/%m/%y') as dataNASCIMENTO, apo.fones, ".
          " apo.email, apo.idTIPO, ifnull(tipoapo.nome, '') as nomeTIPOAPOLICE, apo.idSEGURADORA, ifnull(tiposeg.nome, '') as nomeSEGURADORA, ".
          " apo.idCORRETOR, ifnull(repre.nome, '') as nomeCORRETOR, apo.vlrPREMIO, apo.percentual, apo.obs ".    
          "from seguros_apolices apo ".
          "left join seguros_tiposcliente tipocli ".
          "   on tipocli.numreg = apo.tipoCLIENTE " .
          "left join seguros_tipos tipoapo ".
          "   on tipoapo.numreg = apo.idTIPO " .
          "left join seguros_seguradoras tiposeg ".
          "   on tiposeg.numreg = apo.idSEGURADORA " .
          "left join seguros_corretores repre ".
          "   on repre.numreg = apo.idCORRETOR " .           
          "where apo.numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->cliente, $resp);
    $resp=str_replace('vTIPOCLIENTE', $row->tipoCLIENTE, $resp);
    $resp=str_replace('v_TIPOCLIENTE', $row->nomeTIPOCLIENTE, $resp);
    $resp=str_replace('vNASC', $row->dataNASCIMENTO, $resp);
    $resp=str_replace('vFONE', $row->fones, $resp);
    $resp=str_replace('vEMAIL', $row->email, $resp);
    $resp=str_replace('vTIPOSEGURO', $row->idTIPO, $resp);
    $resp=str_replace('v_TIPOSEGURO', $row->nomeTIPOAPOLICE, $resp);
    $resp=str_replace('vSEGURADORA', $row->idSEGURADORA, $resp);
    $resp=str_replace('v_SEGURADORA', $row->nomeSEGURADORA, $resp);
    $resp=str_replace('vCORRETOR', $row->idCORRETOR, $resp);
    $resp=str_replace('v_CORRETOR', $row->nomeCORRETOR, $resp);
    $resp=str_replace('vASSINATURA', $row->dataASSINATURA, $resp);
    $resp=str_replace('vAPOLICE', $row->apolice, $resp);
    $resp=str_replace('vVALOR', number_format($row->vlrPREMIO, 2, ',', ''), $resp);
    $resp=str_replace('vPERCENTUAL', number_format($row->percentual, 2, ',', ''), $resp);

    $resp=str_replace('@numREG', $vlr, $resp);

    $resp .= '^' . $row->obs;



    // renovacoes      
    $sql = "select ren.numREG, ren.idCORRETOR, repre.nome as nomeCORRETOR, protecao, parcelas, vlrPREMIO, comissao ".
           "from seguros_renovacoes ren ".
           "left join representantes repre ".
           "    on repre.numero=ren.idCORRETOR ".
           "where idAPOLICE=$vlr";

    $renovacoes = mysql_query($sql) or die (mysql_error());  
    while ($renova = mysql_fetcH_object($renovacoes) )  {
      
      $corretor="$renova->nomeCORRETOR ($renova->idCORRETOR)";
      $vlrPREMIO = number_format($renova->vlrPREMIO, 2, ',', '.'); 
      $comissao = number_format($renova->comissao, 2, ',', '.');
            
      $lin = "<tr id=RENOVA_$renova->numREG onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarRENOVACAO(this.id);" > '.
             "<td align=left width='35%'>$corretor</td>". 
             "<td align=right width='15%'>$renova->protecao</td>".             
             "<td align=right width='15%'>$renova->parcelas</td>".
             "<td align=right width='15%'>$vlrPREMIO</td>".
             "<td align=right width='15%'>$comissao</td>".
             "<td onmousedown=\"removeRENOVACAO('RENOVA_$renova->numREG')\"  width='5%' align=center >".
                            '<font color=red style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style='display:none'>$renova->idCORRETOR</td>".
             "<td style='display:none'>$renova->nomeCORRETOR</td>".
             "</tr>";                            
      $tabRENOVACOES .= $lin;       
    }
    mysql_free_result($renovacoes);


    // sinistros
    $sql = "select sin.numREG, date_format(sin.dataSINISTRO, '%d/%m/%y') as dataSINISTRO_MOSTRAR, date_format(sin.dataLIBERACAO, '%d/%m/%y') as dataLIBERACAO, ".
           "   sin.idTIPO, tip.nome as nomeTIPO, terceiros ".      
           "from seguros_sinistros sin ".
           "left join seguros_tipos_sinistros tip ".
           "    on tip.numreg=sin.idTIPO ".
           "where idAPOLICE=$vlr ".
            ' order by sin.dataSINISTRO desc  ';

    $sinistros = mysql_query($sql) or die (mysql_error());  
    while ($sinistro = mysql_fetcH_object($sinistros) )  {
      
      $tipo="$sinistro->nomeTIPO ($sinistro->idTIPO)";
            
      $lin = "<tr id=SINISTRO_$sinistro->numREG onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarSINISTRO(this.id);" > '.
             "<td align=left width='45%'>$tipo</td>". 
             "<td align=left width='10%'>$sinistro->dataSINISTRO_MOSTRAR</td>".             
             "<td align=left width='10%'>$sinistro->dataLIBERACAO</td>".
             "<td align=left width='10%'>$sinistro->terceiros</td>".
             "<td onmousedown=\"removeSINISTRO('SINISTRO_$sinistro->numREG')\"  width='5%' align=center >".
                            '<font color=red style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style='display:none'>$sinistro->idTIPO</td>".
             "<td style='display:none'>$sinistro->numREG</td>".
           "<td style='display:none'>$sinistro->nomeTIPO</td>".
             "</tr>";                            
      $tabSINISTROS .= $lin;       
    }
    mysql_free_result($sinistros);


  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vTIPOCLIENTE', '', $resp);
    $resp=str_replace('v_TIPOCLIENTE', '', $resp);
    $resp=str_replace('vNASC', '', $resp);
    $resp=str_replace('vFONE', '', $resp);
    $resp=str_replace('vEMAIL', '', $resp);
    $resp=str_replace('vTIPOSEGURO', '', $resp);
    $resp=str_replace('v_TIPOSEGURO', '', $resp);
    $resp=str_replace('vSEGURADORA', '', $resp);
    $resp=str_replace('v_SEGURADORA', '', $resp);
    $resp=str_replace('vCORRETOR', '', $resp);
    $resp=str_replace('v_CORRETOR', '', $resp);
    $resp=str_replace('vASSINATURA', '', $resp);
    $resp=str_replace('vAPOLICE', '', $resp);
    $resp=str_replace('vVALOR', '', $resp);
    $resp=str_replace('vPERCENTUAL', '', $resp);

    $resp=str_replace('@numREG', '', $resp);
    $resp .= '^';
  }
  $resp=str_replace('@tabSINISTROS', $tabSINISTROS, $resp);
  $resp=str_replace('@tabRENOVACOES', $tabRENOVACOES, $resp);
}




/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


