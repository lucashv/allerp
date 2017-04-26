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
if ($acao=='confirmarDEPOSITO') {
  mysql_query("update pagamentos_corretor set pago=1 where numreg=$vlr") or die('ERRO: <br>'.mysql_error());
  mysql_close($conexao);
  echo $resp; die();
}

/*****************************************************************************************/
if ($acao=='cancelarDEPOSITO') {
  mysql_query("update pagamentos_corretor set pago=0 where numreg=$vlr")  or die('ERRO: <br>'.mysql_error());
  mysql_close($conexao);
  echo $resp; die();
}  

 
/*****************************************************************************************/
IF ($acao=='verPGTOS') {
  $sql  = "select pgto.numREG, idREPRESENTANTE, substr(repre.nome,1,17) as nomeREPRESENTANTE, valor, ifnull(pago,0) as pago,  " .
          " substr(ban.nome,1,15) as nomeBANCO, repre.idBANCO, repre.agencia, repre.operacao, repre.num_conta,  ".
          " substr(repre.favorecido, 1, 15) as favorecido, concat(ucase(repre.nome), ' (',idREPRESENTANTE,')') as infoREPRESENTANTE " .
          "from pagamentos_corretor pgto ".
          "left join representantes repre " .
          "   on repre.numero = pgto.idREPRESENTANTE ".
          "left join bancos ban " .
          "   on ban.numero = repre.idBANCO ".
          " where pgto.idRELATORIO = $vlr and valor>0 ".
          "order by repre.nome " ;
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.1;
  $largura3 = $_SESSION['largIFRAME'] * 0.15;
  $largura4 = $_SESSION['largIFRAME'] * 0.05;
    
	$header = "$largura1 px,Corretor|$largura2 px,Valor pagar|$largura1 px,Banco|".
              "$largura2 px,Agência?|$largura2 px,Operação|$largura2 px,Conta|$largura3 px,Favorecido|$largura4 px,Pago|1%,&nbsp;?";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id=tabREGS width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  if ( mysql_num_rows($resultado)==0 ) die('nada');
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";
      $largura4="width=\"$largura4 px\"";
    } else {    
      $largura1='';$largura2='';$largura3='';$largura4='';
    }
    $i++;
  
    $vlr = number_format($row->valor, 2, ',', '.');
    $pago = $row->pago==0 ? '-' : 'Sim';

    $banco = $row->idBANCO!='' ? "$row->nomeBANCO ($row->idBANCO)" : '-';
    
    $lin = "<tr id='pgto_$row->numREG' onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" " . 
            "onmouseout=\"this.style.backgroundColor='#E6E8EE';\"   onclick='pagarCORRETOR(\"pgto_$row->numREG\");'  >" . 
            "<td align=left $largura1>$row->nomeREPRESENTANTE ($row->idREPRESENTANTE)</td>".
            "<td align=right $largura2>$vlr&nbsp;&nbsp;</td>".
            "<td align=left $largura1>$banco</td>".
            "<td align=left $largura2>$row->agencia</td>".
            "<td align=left $largura2>$row->operacao</td>".
            "<td align=left $largura2>$row->num_conta</td>".
            "<td align=left $largura3>$row->favorecido</td>".
            "<td align=center $largura4>$pago</td>".
            "<td style='display:none'>$row->infoREPRESENTANTE</td>".
            "</tr>";
    $resp = $resp . ($lin);
  }
  $resp .= '</table>';
}

/*****************************************************************************************/
if ($acao=='excluirREG') {
  $sql = "update periodos_pgto set dataPGTO=null, excluida=1 where numreg=$vlr"; 
  
  mysql_query($sql);
  mysql_close($conexao);
  echo $resp; die();
}  



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[0];
  $dataini_conf = $cmps[1]=='null' ? 'null' : "'$cmps[1]'";
  $datafin_conf = $cmps[2]=='null' ? 'null' : "'$cmps[2]'";
  $dataini_vales = $cmps[3]=='null' ? 'null' : "'$cmps[3]'";
  $datafin_vales = $cmps[4]=='null' ? 'null' : "'$cmps[4]'";
  $dataPGTO = $cmps[5]=='null' ? 'null' : "'$cmps[5]'";
  $idOPERADORA = $cmps[6];
  
  if ($id=='') 
    $sql = "insert into periodos_pgto(idOPERADORA, dataini_conf, datafin_conf, dataini_vales, datafin_vales, dataPGTO) ".
            "  values($idOPERADORA, $dataini_conf, $datafin_conf, $dataini_vales, $datafin_vales, $dataPGTO ) ";
  else
    $sql = "update periodos_pgto set idOPERADORA=$idOPERADORA, dataini_conf=$dataini_conf, datafin_conf=$datafin_conf , " .
               "  dataini_vales=$dataini_vales, datafin_vales=$datafin_vales, dataPGTO=$dataPGTO where numreg=$id";
 
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();

  else {
    if ($id=='') $id=mysql_insert_id();       
    $resp = 'OK;' . $id;      
  }
  mysql_close($conexao);
  echo $resp; die();
}  
           

/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  // certifica se que ha registros relativos a meses para operadora 1, AMIL

  // necessario olha aqui com mais calma depois.......porque os codigos das operadoras mudam....necessario
  // criar uma opcao no registro de cada operadora que informe se fechamento é mensal, semanal etc
  $sql = "select date_format(DATE_ADD(now(),INTERVAL +3 month), '%Y-%m-%01') as data,date_format(DATE_ADD(now(),INTERVAL +3 month), '%m') as mes, ".
          " date_format(DATE_ADD(now(),INTERVAL +3 month), '%Y') as ano ".
         "union " .
         "select date_format(DATE_ADD(now(),INTERVAL +2 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL +2 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL +2 month), '%Y')  ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL +1 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL +1 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL +1 month), '%Y') ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL 0 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL 0 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL 0 month), '%Y') ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL -1 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL -1 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL -1 month), '%Y') ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL -2 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL -2 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL -2 month), '%Y') ".        
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL -3 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL -3 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL -3 month), '%Y') ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL -4 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL -4 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL -4 month), '%Y') ".         
          "union " .
         "select date_format(DATE_ADD(now(),INTERVAL -5 month), '%Y-%m-%01') ,date_format(DATE_ADD(now(),INTERVAL -5 month), '%m') , ".
         " date_format(DATE_ADD(now(),INTERVAL -5 month), '%Y') ";         


  // certifica-se que haja registros a frente da data atual e para tras, se nao houver, inclui automaticamente
  // isso em se tratando da operadora 1  AMIL !!
  $rsMESES = mysql_query($sql, $conexao) or die (mysql_error());
  while ($regMESES = mysql_fetcH_object($rsMESES)) {
    $sql  = "select numREG ".
            "from periodos_pgto ".
            "where date_format(mes_ano, '%Y-%m-%d')='$regMESES->data' and idOPERADORA=1 ";

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    if (mysql_num_rows($resultado)==0)  
      mysql_query("insert into periodos_pgto(idOPERADORA,mes_ano) values(1,'$regMESES->data') ", $conexao) or die (mysql_error());
  }


  $offset = strtotime(date('d-m-Y'));
  if(date('w',$offset) == 1)
    $segunda = date('d/m/y',$offset);
  else
    $segunda = date('d/m/y',strtotime("last Monday",$offset));
  if(date('w',$offset) == 5)
    $domingo = date('d/m/y',$offset);
  else
    $domingo = date('d/m/y',strtotime("next Friday",$offset));

  $offset = strtotime(date('d-m-Y'));
  if(date('w',$offset) == 1)
    $segundaGRAVAR = date('Y-m-d',$offset);
  else
    $segundaGRAVAR = date('Y-m-d',strtotime("last Monday",$offset));
  if(date('w',$offset) == 5)
    $domingoGRAVAR = date('Y-m-d',$offset);
  else
    $domingoGRAVAR = date('Y-m-d',strtotime("next Friday",$offset));

/*
  // certifica-se que haja registros da semana atual para operadora sulamerica (5)
  $sql  = "select numREG ".
          "from periodos_pgto ".
          "where date_format(dataini_conf, '%d/%m/%y')='$segunda' and date_format(datafin_conf, '%d/%m/%y')='$domingo' and idOPERADORA=5";

//die($sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0)  
    mysql_query("insert into periodos_pgto(idOPERADORA,dataini_conf,datafin_conf, dataPGTO) ".
                " values(5,'$segundaGRAVAR', '$domingoGRAVAR', '$domingoGRAVAR') ", $conexao) or die (mysql_error());



  // certifica-se que haja registros da semana atual para operadora bradesco (3)
  $sql  = "select numREG ".
          "from periodos_pgto ".
          "where date_format(dataini_conf, '%d/%m/%y')='$segunda' and date_format(datafin_conf, '%d/%m/%y')='$domingo' and idOPERADORA=3";

//die($sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0)  
    mysql_query("insert into periodos_pgto(idOPERADORA,dataini_conf,datafin_conf, dataPGTO) ".
                " values(3,'$segundaGRAVAR', '$domingoGRAVAR', '$domingoGRAVAR') ", $conexao) or die (mysql_error());



*/

  // lista os periodos ja registrados
  $sql = "select idOPERADORA, ope.nome as nomeOPERADORA, periodos_pgto.numREG, date_format(mes_ano, '%m') as mes, date_format(mes_ano, '%Y') as ano, ".
          " date_format(dataini_conf, '%d/%m/%y') as dataini_conf, date_format(datafin_conf, '%d/%m/%y') as datafin_conf, ".
          " date_format(dataini_vales, '%d/%m/%y') as dataini_vales, date_format(datafin_vales, '%d/%m/%y') as datafin_vales, ".
         " date_format(dataPGTO, '%d/%m/%y') as dataPGTOMOSTRAR, ifnull(qtdeCorretoresPagar,0) as qtdeCorretoresPagar, ".
          ' ifnull(qtdeCorretoresPagos,0) as qtdeCorretoresPagos '.
          'from periodos_pgto '.
          'inner join operadoras ope '.
          '   on ope.numreg = idOPERADORA '.
          ' where ifnull(excluida,0)=0  '.
            'order by idOPERADORA, dataPGTO desc ';
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.20;
  $largura3 = $_SESSION['largIFRAME'] * 0.15;
  $largura4 = $_SESSION['largIFRAME'] * 0.15;
  $largura5 = $_SESSION['largIFRAME'] * 0.05;

	$header = "$largura5 px,Nº|$largura4 px,Operadora|$largura4 px,Mês/ano|$largura2 px,Pagamento<br>confirmações".
               "|$largura3 px,Pagamento<br>créditos/débitos|$largura1 px,Dia<br>&nbsp;&nbsp;&nbsp;Pagamento|$largura1 px,Qtde Corretores <br>Pagar".
              "|$largura1 px,Qtde Corretores <br>Pagos";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabRELATORIOS" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";
      $largura5="width=\"$largura5 px\"";
    } else {    
      $largura1='';$largura2=''; $largura3=''; $largura4='';  $largura5='';
    }
    $i++;

    $periodo='-';  
    if ($row->mes!='')
      $periodo = $MESES[ $row->mes-1 ]  . '/' . $row->ano;

    $qtdeCorretoresPagar = $row->qtdeCorretoresPagar==0 ? '-' : $row->qtdeCorretoresPagar;
    $qtdeCorretoresPagos = $row->qtdeCorretoresPagos==0 ? '-' : $row->qtdeCorretoresPagos;
    $lin = "<tr ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numREG\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=right $largura5>&nbsp;$row->numREG&nbsp;&nbsp;</td>".
            "<td align=\"left\" $largura4>&nbsp;$row->nomeOPERADORA ($row->idOPERADORA)</td>".
            "<td align=\"left\" $largura4>&nbsp;&nbsp;$periodo</td>".
            "<td align=\"left\" $largura2>$row->dataini_conf - $row->datafin_conf</td>".
            "<td align=\"left\" $largura3>$row->dataini_vales - $row->datafin_vales</td>".            
            "<td align=\"center\" $largura1>$row->dataPGTOMOSTRAR</td>".
            "<td align=right $largura1>$qtdeCorretoresPagar</td>".
            "<td align=right $largura1>$qtdeCorretoresPagos</td>".
            "</tr>";
            
    $resp = $resp . ($lin);
  }
}


/*****************************************************************************************/
IF ( $acao=='editarREG' || $acao=='incluirREG'  ) {
  $arq = fopen('periodo_pgto.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  
  $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);

  IF ( $acao=='editarREG'   ) {  
    $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
  //  $resp=str_replace('readonly', '',$resp);
   
    $sql  = "select per.numREG, op.nome as nomeOPERADORA, per.idOPERADORA, date_format(mes_ano, '%m') as mes, date_format(mes_ano, '%Y') as ano, ".
            " date_format(dataini_conf, '%d/%m/%y') as dataini_conf, date_format(datafin_conf, '%d/%m/%y') as datafin_conf, ".
            " date_format(dataini_vales, '%d/%m/%y') as dataini_vales, date_format(datafin_vales, '%d/%m/%y') as datafin_vales, ".
           " date_format(dataPGTO, '%d/%m/%y') as dataPGTO ".
            "from periodos_pgto per  ".
            "inner join operadoras op ".
            "   on op.numreg=idOPERADORA ".
            "where per.numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vOPERADORA', $row->idOPERADORA, $resp);
    $resp=str_replace('v_OPERADORA', $row->nomeOPERADORA, $resp);
  
    if ($row->idOPERADORA!=1)
      $resp=str_replace('@estiloMUDAR', 'style="display:none"', $resp);
    else
      $resp=str_replace('@estiloMUDAR', '', $resp);
  
    if ($row->mes!='' ) 
      $resp=str_replace('vPERIODO', $MESES[ $row->mes-1 ]  . '/' . $row->ano, $resp);
    else
      $resp=str_replace('vPERIODO', '', $resp);
    $resp=str_replace('@numREG', $vlr, $resp);
    
    $resp=str_replace('vDATAINI_CONF', $row->dataini_conf, $resp);
    $resp=str_replace('vDATAFIN_CONF', $row->datafin_conf, $resp);
    $resp=str_replace('vDATAINI_VALES', $row->dataini_vales, $resp);
    $resp=str_replace('vDATAFIN_VALES', $row->datafin_vales, $resp);
    $resp=str_replace('vDATAPGTO', $row->dataPGTO, $resp);
  }
  else {
    $resp=str_replace('TITULO_JANELA', "Incluir Registro",$resp);    

    $resp=str_replace('vOPERADORA', '', $resp);
    $resp=str_replace('v_OPERADORA', '', $resp);
  
    $resp=str_replace('@estiloMUDAR', '', $resp);
    $resp=str_replace('vPERIODO', '', $resp);
    $resp=str_replace('@numREG', '', $resp);
    
    $resp=str_replace('vDATAINI_CONF', '', $resp);
    $resp=str_replace('vDATAFIN_CONF', '', $resp);
    $resp=str_replace('vDATAINI_VALES', '', $resp);
    $resp=str_replace('vDATAFIN_VALES', '', $resp);
    $resp=str_replace('vDATAPGTO', '', $resp);
  }

}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


