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
if ($acao=='verEscritorioCentral') {
  $sql  = "select numero, nome  ".      
          "from escritorios ".
          "where ifnull(escritorioCENTRAL,1)=1 ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if ( mysql_num_rows($resultado)==0 ) die('naoDEF');
  $row = mysql_fetcH_object($resultado);

  $resp="$row->nome ($row->numero)";
}


/*****************************************************************************************/
IF ( $acao=='opCAIXA_RESUMIDA'  ) {
  $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, idFUNCIONARIO, " .
          "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO, valor, cx.faltaVERIFICAR, ".
          "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO, ifnull(pl.contaENTREGA_PROPOSTA,0) as contaENTREGA_PROPOSTA, ".
          "ifnull(repre.nome, '* ERRO *') as nomeREPRE, cx.contabilizarSAIDA, ".
         " ifnull(cx.alterada2_excluida1,0) as alterada2_excluida1, concat(opEXC.nome, '(', operadorEXCLUSAO, ')') as operadorEXCLUSAO, ".
          ' idESCRITORIO, esc.nome as nomeEscritorioAtual, idEscritorioOrigem, esc2.nome as nomeEscritorioOrigem '.
          "from caixa cx ".                          
          "left join operadores ope ".                        
          "   on ope.numero=cx.opRESPONSAVEL ".
          "left join contas pl " .
          " 		on pl.numero = cx.idOPERACAO " .
          "left join representantes repre " .
          " 		on repre.numero = cx.idFUNCIONARIO " .
          "left join operadores opEXC " .  
          "	  on cx.operadorEXCLUSAO = opEXC.numero " .
          "left join escritorios esc " .  
          "	  on esc.numero = idESCRITORIO " .
          "left join escritorios esc2 " .  
          "	  on esc2.numero = idEscritorioOrigem " .  
          "where cx.numreg=$vlr";

  $resultado = mysql_query($sql) or die (mysql_error());  
  $row = mysql_fetcH_object($resultado);

  $entregaPROP = $row->contaENTREGA_PROPOSTA;

  if ($entregaPROP==1)
    $arq = fopen('entrega_resumido.txt', 'r'); 
  else
    $arq = fopen('caixa_resumido.txt', 'r');
  
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;
  
  $resp = str_replace('@titPROPOSTAS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Corretor|20%,Tipo contrato|10%,CPF/CNPJ|10%,Valor|15%,&nbsp;&nbsp;&nbsp;&nbsp;Valor recebido|15%,Vlr AllCross|10%,Adesão" ).'</table>', $resp);
  $resp=str_replace('@altDivPROPOSTAS', ($usandoTelaMaior1024_768 ? '160px' : '110px'), $resp);


  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "100%,Pagamento" ).'</table>', $resp);
    
  $resp=str_replace('@altDivPAGAMENTOS', ($usandoTelaMaior1024_768 ? '100px' : '80px'), $resp);


  $resp = str_replace('@titVALES',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Tipo|10%,Nº|35%,Corretor|10%,Valor|10%,Desconto (%)|15%,Data pagar" ).'</table>', $resp);
  $resp=str_replace('@altDivVALES', ($usandoTelaMaior1024_768 ? '100px' : '80px'), $resp);

  $tabPROP = '<table width="99%" id="tabPROPOSTAS" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  $tabVALES = '<table width="99%" id="tabVALES" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

        
  $resp=str_replace('vDATA', $row->data, $resp);
  $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);

  if ($row->idEscritorioOrigem!='')
    $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioOrigem ($row->idEscritorioOrigem)", $resp);
  else
    $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);
  $resp=str_replace('@local', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);
  
                  
  $resp=str_replace('vCONTA', $row->idOPERACAO, $resp);
  $resp=str_replace('vdescCONTA', $row->descCONTA, $resp);
    
  $resp=str_replace('vFUNCIONARIO', $row->idFUNCIONARIO, $resp);
  $resp=str_replace('v_FUNCIONARIO', $row->nomeREPRE, $resp);

  $resp=str_replace('vDESCRICAO', $row->descOPERACAO, $resp);    
  $resp=str_replace('vVALOR', number_format($row->valor, 2, ',', ''), $resp);

  if ($row->alterada2_excluida1==2) {
    $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que alterou:', $resp);
    $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
    $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
    $bloquearGRAVACAO='<font color=red><b>* REGISTRO ALTERADO *</b></font> ';
  }
  else if ($row->alterada2_excluida1==1) {
    $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que excluiu:', $resp);
    $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
    $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
    $bloquearGRAVACAO='<font color=red><b>* REGISTRO EXCLUÍDO *</b></font> ';
  }
  else { 
    $resp=str_replace('@estiloOPERADOR_EXCLUIU', 'style="display:none"', $resp);
    $resp=str_replace('@titOPERADOR_EXCLUIU', '', $resp);
    $resp=str_replace('@nomeOPERADOR_EXCLUIU', '', $resp);
  }



  $sql = "select ep.numREG, idREPRESENTANTE, ifnull(repre.nome, '* ERRO *') as nomeREPRESENTANTE, ep.cpf, ".
         "ep.valor, ep.vlrRECEBIDO, ep.vlrADESAO, ep.vlrPRESTADORA, ep.idTIPO_CONTRATO, ".
         " ifnull(tipoprop.descricao, '* ERRO *') as descTIPO_CONTRATO, ep.percentualPRESTADORA ".
         "from entregaspropostas ep ".
         "left join representantes repre ".
         "    on repre.numero=ep.idREPRESENTANTE ".
         "left join tipos_contrato tipoprop " .
         "	  on tipoprop.numreg = ep.idTIPO_CONTRATO " .
       "where idCAIXA=$vlr";

  $propostas = mysql_query($sql) or die (mysql_error());  
  while ($proposta = mysql_fetcH_object($propostas) )  {
    
    $vlrADESAO = number_format($proposta->vlrADESAO, 2, ',', '')  ;
    $vlrRECEBIDO = number_format($proposta->vlrRECEBIDO, 2, ',', '')  ;
    $vlrPRESTADORA = number_format($proposta->vlrPRESTADORA, 2, ',', '')  ;
    $percPRESTADORA = number_format($proposta->percentualPRESTADORA, 0, ',', '')  ;
    $valor = number_format($proposta->valor, 2, ',', '')  ;
    
    $lin = "<tr id=\"PROP_$proposta->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
            ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" > '.
           "<td align=\"left\" width=\"20%\">$proposta->nomeREPRESENTANTE ($proposta->idREPRESENTANTE)</td>". 
           "<td align=\"left\" width=\"20%\">$proposta->descTIPO_CONTRATO ($proposta->idTIPO_CONTRATO)</td>".
           "<td align=\"left\" width=\"10%\">$proposta->cpf</td>".
           "<td align=\"right\" width=\"10%\">$valor</td>".             
           "<td align=\"right\" width=\"15%\">$vlrRECEBIDO</td>".
           "<td align=\"right\" width=\"15%\">$vlrPRESTADORA ($percPRESTADORA%)</td>".
           "<td align=\"right\" width=\"10%\">$vlrADESAO</td>".                                       
           "<td width=\"5%\">&nbsp;</td>".
           '</tr>';               

    $tabPROP .= $lin;       
  }
  mysql_free_result($propostas);

  

  $sql  = "select vale.tipo, vale.numVALE_CREDITO as numVALE, repre.nome as nomerepreVALE,  vale.numero as numREG,  ".
          " vale.valor as valorVALE, vale.representante as repreVALE, vale.descontoVALE, date_format(vale.pagarVALE, '%d/%m/%y') as pagarVALE, ".
          ' ifnull(inseridoManualmente, 0) as inseridoManualmente, vale.descricao '.
          "from creditos_descontos vale ".                        
          "left join representantes repre ".                        
          "   on repre.numero = vale.representante ".
          "where vale.idCAIXA=$vlr ";

  $rsVALES = mysql_query($sql) or die (mysql_error());  
  while ($regVALE = mysql_fetcH_object($rsVALES) )  {

    $valor = number_format($regVALE->valorVALE, 2, ',', '');
    $desconto = number_format($regVALE->descontoVALE, 2, ',', '');

    if ($regVALE->tipo=='D') $tipo='Débito';
    else {
      if ($regVALE->numVALE=='') $tipo='Crédito';
      else $tipo='Vale Crédito';
    }
    // o campo "inseridoManualmente" existe para diferenciar cred/deb   que sao inseridos por uma automatizacao do sistema
    // exemplo, qdo a operacao do caixa é um ADIANTAMENTO SALARIAL, o sistema automaticamente gera um debito em nome do favorecido
    // este devito NAO foi inserido manualmente e esta ligado com a operacao ADTO SALARIAL, so pode ser manipulado, retirado, dependendo
    // do que ocorrer com a operacao ADTO SALARIAL , estes possuem o campo inseridoManualmente= 0

    // cred/deb que foram inseridos manualmente, sem ligacao com a operacao do caixa, podem ser excluidos, manipulados
    // sem restricoes       estes possuem o campo inseridoManualmente= 1

    $lin = "<tr @cor id=\"VALE_$regVALE->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
            ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
            '  > '.
           "<td align=\"left\" width=\"20%\">$tipo</td>". 
           "<td align=\"left\" width=\"10%\">$regVALE->numVALE</td>".
           "<td align=\"left\" width=\"35%\">$regVALE->nomerepreVALE ($regVALE->repreVALE)</td>".
           "<td align=\"right\" width=\"10%\">$valor</td>".             
           "<td align=\"right\" width=\"10%\">$desconto</td>".
           "<td align=\"right\" width=\"15%\">$regVALE->pagarVALE</td>".
           '<td>&nbsp;</td>'.
           "<td style=\"display:none\">$regVALE->repreVALE</td>".
           "<td style=\"display:none\">$regVALE->nomerepreVALE</td>".
           "<td style=\"display:none\">$regVALE->inseridoManualmente</td>".
           "<td style=\"display:none\">$regVALE->descricao</td>".
           '</tr>';

    $tabVALES .= $lin;
  }
  mysql_free_result($rsVALES);



  // pagamentos      
  $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO,  pg.idREPRESENTANTE, cheque, pg.idPagouBoleto, ".
          " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
         "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
         " ifnull(ban.nome, '* ERRO *') as nomeBANCO, ".
         " ifnull(repre.nome, '* ERRO *') as nomeREPRE, pg.infoCHEQUE, pg.nomeCHEQUE ".
         "from pagamentos pg ".
         "left join representantes repre ".
         "    on repre.numero=pg.idREPRESENTANTE ".
         "left join operadores ope " .
         "	  on ope.numero = pg.idPagouBoleto " .
         "left join bancos ban " .
         "	  on ban.numero = pg.idBANCO " .
         "where idCAIXA=$vlr";

  $pags = mysql_query($sql) or die (mysql_error());  
  while ($pag = mysql_fetcH_object($pags) )  {
    
    $tipo=$pag->tipoPGTO;
          
    $valor = number_format($pag->valor, 2, ',', '')  ;
    if ($tipo=='CHEQUE') {
      $detalhes='<table border=0><tr>'.
                '<td>_cinzaNº: </font></td><td align=left  width="80px">_azul'.$pag->cheque.'</font></td>'.
                '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'.$pag->nomeBANCO.' ('.$pag->idBANCO.')</font></td>'.
                '<td>_cinzaData: </font></td><td align=left width="80px">_azul'.$pag->dataCHEQUE.'</font></td>'.            
                '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'.$valor.'</font></td>'.
                '</tr></table>';
    }
    if ($tipo=='BOLETO') {
      if ($pag->nomeBOLETOPAGO!='') {
        $boletoPAGO= "$pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
        $funcao="<img title='Cancelar pagamento do boleto' src='images/cancelarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,2)' />";
      }
      else {
        $boletoPAGO= "<font color=red>NÃO</font>";
        $funcao="<img  title='Pagar boleto' src='images/pagarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,1)' />";
      } 
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Vencimento: </font></td><td align=right width="60px">_azul'.$pag->dataCHEQUE.'</font></td>'.
                '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Nº: </font></td><td align=right width="60px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Pago: </font></td><td align=left width="250px">_azul'.$boletoPAGO.'</font></td>'.
                "<td>$funcao</td>".
                '</tr></td>'.
                '</table>';
    }
    if ($tipo=='CARTÃO') {
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                '</tr></td>'.
                '</table>';
    }
    if ($tipo=='DINHEIRO') {
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                '</tr></td>'.
                '</table>';
    }
    if ($tipo=='INTERNET') {
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                '</tr></td>'.
                '</table>';
    }
    
    if ($tipo=='VALE') {
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaCorretor: </font></td><td align=left width="300px">_azul'.$pag->nomeREPRE.' ('.$pag->idREPRESENTANTE.')</font></td>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                '</tr></td>'.
                '</table>';
    }

    if ($tipo=='VALE CRÉDITO') {
      $detalhes='<table  border=0><tr>'.
                '<td>_cinzaNº: </font></td><td align=left width="80px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                '</tr></td>'.
                '</table>';
    }
    $detalhes = str_replace('_cinza', '<font color=gray>', $detalhes);
    $detalhes = str_replace('_azul', '<font style="color:blue;font-size:12px;">', $detalhes);

    $lin = "<tr id=\"PGTO_$pag->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
            ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
            '  > '.
           "<td align=\"left\" width=\"15%\">$tipo</td>". 
           "<td align=\"left\" width=\"80%\">$detalhes</td>".             
           "<td width=\"5%\" align=\"center\" >&nbsp;</font></td>".
           "</tr>";                            
                          
    $tabPGTO .= $lin;       
  }
  mysql_free_result($pags);
    

  $tabPGTO .= '</table>';
  $tabVALES .= '</table>';
  $tabPROP .= '</table>';

  $resp=str_replace('TITULO_JANELA', "Visualizando operação do caixa nº $vlr  ",$resp);    
  $resp=str_replace('@tabPROPOSTAS', $tabPROP, $resp);
  $resp=str_replace('@tabPAGAMENTOS', $tabPGTO, $resp);  
  $resp=str_replace('@tabVALES', $tabVALES, $resp);
  $resp .= "^$entregaPROP";
}

          
/*****************************************************************************************/
if ($acao=='marcarOperacaoCaixaAlterada') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);   $logado = $logado[1];

  $id = $_REQUEST['vlr'];
  mysql_query("update caixa set alterada2_excluida1=2, operadorEXCLUSAO=$logado where numreg=$id") or  die (mysql_error());  
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='buscaCHEQUES') {
  // pagamentos      
  $sql = "select pg.numREG, pg.idBANCO,  pg.idREPRESENTANTE, cheque,  ".
         "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
         " ifnull(ban.nome, '* ERRO *') as nomeBANCO, ".
         " ifnull(repre.nome, '* ERRO *') as nomeREPRE, pg.infoCHEQUE, pg.nomeCHEQUE ".
         "from pagamentos pg ".
         "left join representantes repre ".
         "    on repre.numero=pg.idREPRESENTANTE ".
         "left join bancos ban " .
         "	  on ban.numero = pg.idBANCO " .
         "left join caixa cx " .
         "	  on cx.numreg = pg.idCAIXA " .
         "left join contas con " .
         "	  on con.numero = cx.idOPERACAO " .
         "where date_format(cx.dataop, '%Y%m%d') between '$vlr' and '$vlr' and con.entOUsai='E' and tipoPGTO='CHEQUE' ";

  $resp = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  $pags = mysql_query($sql) or die (mysql_error());
  if ( mysql_num_rows($pags)==0 ) die('nada' );  
  while ($pag = mysql_fetcH_object($pags) )  {
    
    $valor = number_format($pag->valor, 2, ',', '')  ;
    $detalhes='<table border=0><tr>'.
              '<td>_cinzaNº: </font></td><td align=left  width="80px">_azul'.$pag->cheque.'</font></td>'.
              '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'.$pag->nomeBANCO.' ('.$pag->idBANCO.')</font></td>'.
              '<td>_cinzaData: </font></td><td align=left width="80px">_azul'.$pag->dataCHEQUE.'</font></td>'.            
              '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'.$valor.'</font></td>'.
              '</tr></table>';
              
    $colunasOCULTAS="<td style=\"display:none\">$pag->cheque</td>".                  
                    "<td style=\"display:none\">$pag->idBANCO</td>".
                    "<td style=\"display:none\">$pag->nomeBANCO</td>".
                    "<td style=\"display:none\">$pag->dataCHEQUE</td>".
                    "<td style=\"display:none\">$valor</td>".                                                                        
                    "<td style=\"display:none\">$pag->infoCHEQUE</td>".
                    "<td style=\"display:none\">$pag->nomeCHEQUE</td>";

    $detalhes = str_replace('_cinza', '<font color=gray>', $detalhes);
    $detalhes = str_replace('_azul', '<font style="color:blue;font-size:12px;">', $detalhes);      

    $lin = "<tr id=\"PGTO_$pag->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
            ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
            ' onclick="editarPGTO(this.id);" > '.
           "<td align=\"left\" width=\"15%\">CHEQUE</td>". 
           "<td align=\"left\" width=\"80%\">$detalhes</td>".             
           "<td onmousedown=\"removePGTO('PGTO_$pag->numREG')\"  width=\"5%\" align=\"center\" >".
                          '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
           $colunasOCULTAS.
           "</tr>";                            
                          
    $resp .= $lin;       
  }
  mysql_free_result($pags);

  $resp .= '</table>';
}

/*****************************************************************************************/
if ($acao=='pesqCPF_SACADO') {
  $sql  = "select idCAIXA, date_format(cx.dataOP, '%d%m%Y') as dataOP ".      
          "from pagamentos ".
          "inner join caixa cx ". 
          "   on cx.numreg=pagamentos.idCAIXA ".
          "where nomeCHEQUE like '%$vlr%' or cheque like '%$vlr%' ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if ( mysql_num_rows($resultado)==0 ) die('nada');
  $row = mysql_fetcH_object($resultado);

  $resp="$row->dataOP;$row->idCAIXA";
}




/*****************************************************************************************/
if ($acao=='setarESCRITORIO') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  mysql_query("update operadores set escritorioATUAL='$vlr' where numero=$logado[1];" ) or die (mysql_error());
    
  $resp='ok';  
}


/*****************************************************************************************/
if ($acao=='verificada') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  $sql  = "select permissoes ".      
          "from operadores ".
          "where numero=$logado[1]";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  if ( strpos($row->permissoes, 'O')===false ) $resp='semACESSO';
  else {
    $sql  = "select numero ".      
            "from escritorios ".
            "where ifnull(escritorioCENTRAL,1)=1 ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);

    $escCENTRAL = $row->numero;
  
    if (isset( $_REQUEST['vlr']))    
      mysql_query("update caixa set faltaVERIFICAR='N', dataOP=now(), ".
                  " idEscritorioOrigem=idESCRITORIO, idESCRITORIO=$escCENTRAL  where numreg=$vlr;" ) or die (mysql_error());
  
    $resp='ok';
  }    
}


/*****************************************************************************************/
if ($acao=='lerESCRITORIOS') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  $resultado = mysql_query('select nome,numero from escritorios order by numero', $conexao) or die (mysql_error());

  $escritorios=array();
  while ($row = mysql_fetcH_object($resultado)) {    
    $escritorios[$row->numero]=$row->nome;
  }
  $sql  = "select escritorios, escritorioATUAL ".      
          "from operadores ".
          "where numero=$logado[1]";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  $resp = '<select id="lstESCRITORIOS" style="font-family:verdana;font-size:15px;color:blue;font-weight:bold;width:250px;">';
  for ($rr=0; $rr<strlen($row->escritorios); $rr++) {
    $letraESCRITORIO= substr($row->escritorios, $rr, 1);
  
    // acredite se quiser, mas tive que fazer o xunxo abaixo pq quando vc usa ORD()
    // ele retorna INT(78) - se vc fizer qq operacao matematica com INT(78) vai dar merda, resultado sai em branco        
    if ($letraESCRITORIO=='A') $codigo=1;  
    if ($letraESCRITORIO=='B') $codigo=2;
    if ($letraESCRITORIO=='C') $codigo=3;
    if ($letraESCRITORIO=='D') $codigo=4;
    if ($letraESCRITORIO=='E') $codigo=5;
    if ($letraESCRITORIO=='F') $codigo=6;
    if ($letraESCRITORIO=='G') $codigo=7;
    if ($letraESCRITORIO=='H') $codigo=8;
    if ($letraESCRITORIO=='I') $codigo=9;
    if ($letraESCRITORIO=='J') $codigo=10;

    $nomeESCRITORIO = $escritorios[ $codigo ];

    $lin = "<option value=$codigo selected title='$letraESCRITORIO'>$nomeESCRITORIO</option>";

    if($row->escritorioATUAL!=$letraESCRITORIO) $lin=str_replace('selected', '',$lin);
    $resp .= $lin;
  }

  $resp .= '</select>&nbsp; '.
           '<input class=botao type=button value=" OK " onclick="acessarESCRITORIO();" />&nbsp;'.
           '<input class=botao type=button value=" CANCELAR " onclick="voltaESCRITORIO();"/>';
}


/*****************************************************************************************/
if ($acao=='cancelarPGTOBOLETO') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  // obtem eventual credito gerado no ato do pagamento do boleto
  $sql  = "select idCreditoGeradoBoleto from pagamentos where numreg=$vlr";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $idCreditoGeradoBoleto = $row->idCreditoGeradoBoleto;

  mysql_query("update pagamentos set idPagouBoleto=null, dataBOLETOPAGO=null where numreg=$vlr" ) or die (mysql_error());

  if ($idCreditoGeradoBoleto!='') 
    mysql_query("delete from creditos_descontos where numero=$idCreditoGeradoBoleto") or die (mysql_error());    
    
  $resp='ok';  
}


/*****************************************************************************************/
if ($acao=='pagarBOLETO') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $data = $_REQUEST['data'];
  $vlrREPRE = $_REQUEST['vlr2'];
  $idREPRE = $_REQUEST['idREPRE'];

  if ($vlrREPRE!=-1) {
    // obtem num do boleto
    $sql  = "select infoCHEQUE from pagamentos where numreg=$vlr";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);
    $numBOLETO = $row->infoCHEQUE;
  
    $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
          " select '$data', 'C', concat('VALOR PAGAR AO CORRETOR (REF BOLETO Nº $numBOLETO)'), $idREPRE, $vlrREPRE, $logado[1], null, null, null, '$data'";

    mysql_query($sql) or die ($sql.'.....'.mysql_error());

    $numregGERADO= mysql_insert_id();

    // registra q boleto foi pago e registra o num do credito gerado, caso o pgto boleto seja cancelado e necessario excluir o credito 
    mysql_query("update pagamentos set idCreditoGeradoBoleto=$numregGERADO, ".
                " idPagouBoleto=$logado[1], dataBOLETOPAGO='$data' where numreg=$vlr" ) or die (mysql_error());
  }
  else
    mysql_query("update pagamentos set idPagouBoleto=$logado[1], dataBOLETOPAGO='$data' where numreg=$vlr" ) or die (mysql_error());



    
  $resp='ok';  
}  


/*****************************************************************************************/
if ($acao=='verPodePagarBoleto') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $sql  = "select permissoes from operadores where numero=$logado[1]";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $resp='nao';
  if ( strpos($row->permissoes, 'M')!==false ) $resp='sim';
}

/*****************************************************************************************/
if ($acao=='verBOLETO') {
  $idCAIXA = $_REQUEST['vlr2'];    // idCAIXA vazio= incluindo entrega proposta
  if ($idCAIXA=='')
    $sql="select numreg from pagamentos where infoCHEQUE='$vlr' ";
  else
    $sql="select numreg from pagamentos where infoCHEQUE='$vlr' and idCAIXA<>$idCAIXA";
       
  $resultado = mysql_query($sql) or die (mysql_error());
  $resp='ERR';
  if (mysql_num_rows($resultado)==0) $resp='OK';
}  


/*****************************************************************************************/
if ($acao=='lerValeCredito') {
  $idCAIXA =$_REQUEST['vlr2'];

  $sql="select descontoVALE, idCAIXA, valor, pagoVALE_CREDITO from creditos_descontos where numVALE_CREDITO=$vlr ";

  $resultado = mysql_query($sql) or die (mysql_error());
  $resp='ERR';
  if (mysql_num_rows($resultado)>0) {
    $row = mysql_fetcH_object($resultado);

    $vlrDESCONTADO = $row->valor - $row->valor * ($row->descontoVALE/100);

    if ($row->idCAIXA==$idCAIXA) $resp='INVALIDA';
    else if ($row->pagoVALE_CREDITO=='1') $resp='PAGO';
    else {
      // vale credito nao pago, verifica os pagamentos ja efetuados dele
      $sql = "select sum(pag.valor) as vlrPAGO  ".
             "from pagamentos pag ".
             'left join caixa cx '.
             '    on cx.numreg=idCAIXA '.                 
             "where infoCHEQUE=$vlr and tipoPGTO='VALE CRÉDITO' and ifnull(cx.alterada2_excluida1,0)=0 ";

      $resultado = mysql_query($sql) or die (mysql_error());
      $vlrPAGO=0;
      $vlrDISPONIVEL=0;
      if (mysql_num_rows($resultado)>0) {
        $row = mysql_fetcH_object($resultado);
        $vlrPAGO += $row->vlrPAGO;
      }
      $vlrDISPONIVEL = number_format(($vlrDESCONTADO - $vlrPAGO), 2, ',', '') ;
      $vlrPAGO = number_format($vlrPAGO, 2, ',', '') ;

      $vlrDESCONTADO = number_format($vlrDESCONTADO, 2, ',', '') ;
      $resp=$vlrDESCONTADO.'^'.$vlrPAGO.'^'.$vlrDISPONIVEL;
    } 
  }
}  


/*****************************************************************************************/
if ($acao=='verValeCredito') {
  $idCAIXA = $_REQUEST['vlr2'];    // idCAIXA vazio= incluindo entrega proposta
  if ($idCAIXA=='')
    $sql="select numero, pagoVALE_CREDITO from creditos_descontos where numVALE_CREDITO=$vlr ";
  else
    $sql="select numero, pagoVALE_CREDITO from creditos_descontos where numVALE_CREDITO=$vlr and idCAIXA<>$idCAIXA";
       
  $resultado = mysql_query($sql) or die (mysql_error());
  $resp='ERR';
  if (mysql_num_rows($resultado)==0) {
    if ($row->pagoVALE_CREDITO!=1) $resp='OK';
    else $resp='PAGO';
  }  
}  

/*****************************************************************************************/
if ($acao=='gravarCAIXA') {
  $cmps = explode('|', $_REQUEST['vlr']);
  $pgtos= explode('|', $_REQUEST['pgto']);

  $contabilizarSAIDA = 1;     //$_REQUEST['somar']=='true' ? 1 : 0;
  
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];
  
  $entOUsai = $cmps[6];
  $temBOLETO=0;
  if ( strpos($_REQUEST['pgto'], 'BOLETO')!==false ) $temBOLETO=1;
  $numBOLETO=$_REQUEST['bl']=='' ? 'null' : $_REQUEST['bl']; 

  if (isset($_REQUEST['esc']))   $idESCRITORIO =$_REQUEST['esc'];
  
  // verifica se o usuario logado precisa ter "auditoria" em suas operacoes do caixa
  $sql  = "select permissoes from operadores where numero=$logado";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $faltaVERIFICAR='N';
  if ( strpos($row->permissoes, 'O')===false && $logado!=1) $faltaVERIFICAR='S';

  $temFormasPgto = (count($pgtos)>=1 && trim($_REQUEST['pgto'])!='') ? 1 : 0;

  $id = $cmps[0];

  if ($id=='') {
    $sql = "insert into caixa(entOuSai, dataOP, idOPERACAO, descOPERACAO, valor, opRESPONSAVEL, idFUNCIONARIO, contabilizarSAIDA,temBOLETO,".
            " numBOLETO,idESCRITORIO,faltaVERIFICAR,temFormasPgto) ".
            " values('$entOUsai', concat('$cmps[1] ', curtime()), $cmps[3], upper('$cmps[4]'), 0, $logado, $cmps[2], ".
            " $contabilizarSAIDA,$temBOLETO,$numBOLETO, $idESCRITORIO, '$faltaVERIFICAR', $temFormasPgto)";

    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    
    $idCAIXA = mysql_insert_id();
  }
  else {
    /* se a data da operacao foi alterada, grava */
    if ($cmps[1]!='NAOMUDAR')  
      $sql = "update caixa set temBOLETO=$temBOLETO, entOuSai='$entOUsai', dataOP=concat('$cmps[1] ', curtime()), idOPERACAO=$cmps[3], ".
              "descOPERACAO=upper('$cmps[4]'), valor=0, opRESPONSAVEL=$logado, idFUNCIONARIO=$cmps[2], contabilizarSAIDA=$contabilizarSAIDA,  ".
              " numBOLETO=$numBOLETO, temFormasPgto=$temFormasPgto where numreg=$id";
    // data da operacao nao foi alterada, nao mexe no campo dataOP por ele ser datetime, ou seja, nao queremos mexer na data sem necessidade
    else
      $sql = "update caixa set temBOLETO=$temBOLETO, entOuSai='$entOUsai', idOPERACAO=$cmps[3], contabilizarSAIDA=$contabilizarSAIDA,  ".
              "descOPERACAO=upper('$cmps[4]'), valor=0, opRESPONSAVEL=$logado, idFUNCIONARIO=$cmps[2], numBOLETO=$numBOLETO,   ". 
              " temFormasPgto=$temFormasPgto  where numreg=$id";

    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    
    $idCAIXA = $id;
  }
  $sql = "delete from pagamentos where idCAIXA=$idCAIXA ";
  mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

  $sql="delete from creditos_descontos where idCAIXA=$idCAIXA  ;";
  mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  
  $dataOPERACAO=$cmps[8];

  // *****************************************************************************************************************************************
  // *****************************************************************************************************************************************
  // PAGAMENTOS (dependendo do tipo de pgto, gera-se um debito, exemplo: pagamento com vale, sera gerado um debito em nome do corretor) 
  // *****************************************************************************************************************************************
  // *****************************************************************************************************************************************
  $vlrEM_DINHEIRO=0;
  for ($f=0; $f<count($pgtos); $f++) {
    $pgto = explode(';', $pgtos[$f]);
    $tipoPGTO = $pgto[0];
     
    $sql='';
    if ($tipoPGTO=='CHEQUE') 
      $sql="insert into pagamentos(tipoPGTO, cheque, idBANCO, dataCHEQUE, valor, idCAIXA, infoCHEQUE, nomeCHEQUE) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], '$pgto[3]', $pgto[4], $idCAIXA, '$pgto[5]', '$pgto[6]'";
    else if ($tipoPGTO=='BOLETO') {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'D', concat('PROPOSTA(S) PAGA(S) COM BOLETO'), $pgto[1], $pgto[6], $logado, $idCAIXA, null, null, '$dataOPERACAO'";

      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 
      $sql="insert into pagamentos(tipoPGTO, valor, dataCHEQUE, infoCHEQUE, idCAIXA,nomeCHEQUE,cheque) ".
            " select '$tipoPGTO', $pgto[1], '$pgto[2]', '$pgto[3]', $idCAIXA, '$pgto[4]', '$pgto[5]'";
    }
    else if ($tipoPGTO=='CARTÃO') 
      $sql="insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
            " select '$tipoPGTO', $pgto[1], $idCAIXA";
    else if ($tipoPGTO=='DINHEIRO') { 
      $sql="insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
            " select '$tipoPGTO', $pgto[1], $idCAIXA";
      $vlrEM_DINHEIRO += $pgto[1];
    }
    else if ($tipoPGTO=='INTERNET') { 
      $sql="insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
            " select '$tipoPGTO', $pgto[1], $idCAIXA";
    }
    else if ($tipoPGTO=='VALE') {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'D', concat('PROPOSTA(S) PAGA(S) COM VALE'), $pgto[1], $pgto[2], $logado, $idCAIXA, null, null, '$dataOPERACAO'";

      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 
      $sql="insert into pagamentos(tipoPGTO, idREPRESENTANTE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";

    }
    else if ($tipoPGTO=='VALE CRÉDITO') { 
      $sql="insert into pagamentos(tipoPGTO, infoCHEQUE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";
    }
    if ($sql!='')    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }

  $dataOPERACAO=$cmps[8];
  $infoCRED_DEB=$_REQUEST['cred'];
  $idOPERACAO=$cmps[3];

  if ($infoCRED_DEB!='') {
    $cred_deb_S=explode('|',$infoCRED_DEB);
    for ($rr=0; $rr<count($cred_deb_S); $rr++) {
      $cred_deb=explode(';',$cred_deb_S[$rr]);
      
      $tipo=$cred_deb[0];
      $numVALE=$cred_deb[1]=='' ? 'null' : $cred_deb[1];
      $idREPRE=$cred_deb[2];
      $vlrVALE=$cred_deb[3];
      $descVALE=$cred_deb[4];
      $dataVALE=$cred_deb[5];
      $descricaoVALE=$cred_deb[6];

      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, ".
          " idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE, inseridoManualmente) ".
            " select '$dataOPERACAO', '$tipo', '$descricaoVALE', $idREPRE, $vlrVALE, $logado, $idCAIXA, $numVALE, $descVALE, '$dataVALE', 1";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    }
  }
  // se o tipo de operacao de caixa é "adiantamento salarial" ou "adiantamento de comissao" gera um debito em nome do envolvido
  $sql  = "select ifnull(contaADIANTAMENTOSALARIAL, 0) as contaADIANTAMENTOSALARIAL, ".
          "ifnull(contaADIANTAMENTOCOMISSAO, 0) as contaADIANTAMENTOCOMISSAO ".
          " from contas where numero=$idOPERACAO";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  if ($row->contaADIANTAMENTOSALARIAL==1 || $row->contaADIANTAMENTOCOMISSAO==1) {
    $descricao = $row->contaADIANTAMENTOSALARIAL==1 ? 'ADIANTAMENTO SALARIAL' : 'ADIANTAMENTO DE COMISSÃO';   

    $sql="insert into creditos_descontos(data, pagarVALE, tipo, descricao, representante, valor,operador, idCAIXA) ".
          " select '$dataOPERACAO', '$dataOPERACAO', 'D', '$descricao',  $cmps[2], $vlrEM_DINHEIRO, $logado, $idCAIXA;";
    
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }


  $resp = "OK;$idCAIXA";                  
   
}



/*****************************************************************************************/
if ($acao=='excluirREG') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);   $logado = $logado[1];
  
  mysql_query("update caixa set alterada2_excluida1=1, operadorEXCLUSAO=$logado  where numreg=$vlr" ) or die (mysql_error());
  mysql_query("update creditos_descontos set excluido=1 where idCAIXA=$vlr" ) or die (mysql_error());

  // dezfaz o vinculo entre cadastro - caixa das propostyas entregues na operacao atual */ 
  
  /* COMENTADO POR FILIPE, PARA MANTER OS VÍNCULOS EXISTENTES NO SISTEMA.
  $sql = "update propostas set numregPropostaEntregueCaixa=null ".
        " where numregPropostaEntregueCaixa in (select numreg from entregaspropostas where idCAIXA=$vlr) ";
  */
  
  mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 

//  mysql_query("delete from caixa where numreg=$vlr" ) or die (mysql_error());
//  mysql_query("delete from entregaspropostas where idCAIXA=$vlr" ) or die (mysql_error());  
//  mysql_query("delete from pagamentos where idCAIXA=$vlr" ) or die (mysql_error());
    
  $resp='ok';  
}  

/*****************************************************************************************/
if ($acao=='verificaCONTA') {
  $sql="select numero from contas where contaENTREGA_PROPOSTA=1";
       
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0) {  
    $row = mysql_fetcH_object($resultado);
    $resp=$row->numero;
  } 
  else
    $resp='NAO';  
}

/*****************************************************************************************/
if ($acao=='verificaCONTAVALECREDITO') {
  $sql="select numero from contas where contaVALE_CREDITO=1";
       
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0) {  
    $row = mysql_fetcH_object($resultado);
    $resp=$row->numero;
  } 
  else
    $resp='NAO';  
}  
  
/*****************************************************************************************/
if ($acao=='gravarENTREGA') {
  $cmps = explode('^', $_REQUEST['vlr']);
  
  $info= explode('|', $cmps[0]);
  $props= explode('|', $cmps[1]);

  $pgtos= explode('|', $_REQUEST['pgto']);
  $idCONTA=$cmps[3];
  $infoVALE=$cmps[4];
  $dataOPERACAO=$cmps[5];

  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  // verifica se o usuario logado precisa ter "auditoria" em suas operacoes do caixa
  $sql  = "select permissoes from operadores where numero=$logado";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $faltaVERIFICAR='N';
  if ( strpos($row->permissoes, 'O')===false && $logado!=1 ) $faltaVERIFICAR='S';


  $idESCRITORIO = $_REQUEST['esc'];

  $totDINHEIRO = $_REQUEST['din']; if (trim($totDINHEIRO=='')) $totDINHEIRO=0;
  $totCHEQUE = $_REQUEST['ch']; if (trim($totCHEQUE=='')) $totCHEQUE=0;

  $temBOLETO=0;
  if ( strpos($_REQUEST['pgto'], 'BOLETO')!==false ) $temBOLETO=1;
  $numBOLETO=$_REQUEST['bl']=='' ? 'null' : $_REQUEST['bl'];

//  $temFormasPgto = trim($cmps[2]=='') ? 0 : 1;
  $temFormasPgto = (count($pgtos)>=1 && trim($_REQUEST['pgto'])!='') ? 1 : 0;

  $id = $info[0];
  if ($id=='') {
    $sql = "insert into caixa(entOuSai, dataOP,idOPERACAO,descOPERACAO,valor,opRESPONSAVEL,temFormasPgto,totDINHEIRO,totCHEQUE,".
            " temBOLETO,numBOLETO,idESCRITORIO,faltaVERIFICAR) ".
            " values('E', concat('$info[1] ', curtime()), $idCONTA, '', 0, $logado, $temFormasPgto,".
            " $totDINHEIRO,$totCHEQUE,$temBOLETO,$numBOLETO,$idESCRITORIO,'$faltaVERIFICAR')";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    
    $idCAIXA = mysql_insert_id();                
  }
  else {
    $idCAIXA=$id;
    
    if ($info[1]!='NAOMUDAR')   
      $sql = "update caixa set temBOLETO=$temBOLETO, dataOP=concat('$info[1] ', curtime()), temFormasPgto=$temFormasPgto, numBOLETO=$numBOLETO, ".
              " totDINHEIRO=$totDINHEIRO, totCHEQUE=$totCHEQUE where numreg=$idCAIXA";
    else
      $sql = "update caixa set numBOLETO=$numBOLETO, temBOLETO=$temBOLETO, totDINHEIRO=$totDINHEIRO, ".     
                " totCHEQUE=$totCHEQUE ,temFormasPgto=$temFormasPgto where numreg=$idCAIXA ";

    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

    // dezfaz o vinculo entre cadastro - caixa das propostyas entregues na operacao atual */
    if ($_REQUEST['erroCAIXA']==1) { 
	/* COMENTADO POR FILIPE, PARA EVITAR REMOÇÃO DE VÍNCULOS DO SISTEMA.
      $sql = "update propostas set numregPropostaEntregueCaixa=null ".
            " where numregPropostaEntregueCaixa in (select numreg from entregaspropostas where idCAIXA=$idCAIXA) ";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

      // apaga as entregas de proposta e insere mais abaixo, com isso desfaz definitivamente o vinculo CAIXA -> CADASTRO
      $sql = "delete from entregaspropostas where idCAIXA=$idCAIXA ";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
	*/  
    }
    
    // registra vales creditos atuais como EM ABERTO, NAO PAGOS
    $sql="select infoCHEQUE as numVALE_CREDITO from pagamentos where tipoPGTO='VALE CRÉDITO' and idCAIXA=$idCAIXA";
    $vales = mysql_query($sql, $conexao) or die (mysql_error());
    if ( mysql_num_rows($vales)>0 ) {
      while ( $vale = mysql_fetcH_object($vales) ) {    
        mysql_query("update creditos_descontos set pagoVALE_CREDITO=0 where numVALE_CREDITO=$vale->numVALE_CREDITO") or 
                  die( 'voltar reg pgto vale credito' . '<br>'.mysql_error() );
      }
    }
    mysql_free_result($vales);

    $sql = "delete from pagamentos where idCAIXA=$idCAIXA ";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

    $sql="delete from creditos_descontos where idCAIXA=$idCAIXA  ;";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }
  
  // propostas
  if ($_REQUEST['erroCAIXA']==1) {
    $idRepreBoleto=9999;
    for ($e=0; $e<count($props); $e++) {
      $prop = explode(';', $props[$e]);
      
      if (count($prop)>0) { 
        $sql="insert into entregaspropostas(idREPRESENTANTE, cpf, valor, vlrRECEBIDO, vlrADESAO, vlrPRESTADORA, ".
              " idTIPO_CONTRATO, idCAIXA, percentualPRESTADORA) ".
              "values($prop[0], '$prop[2]', $prop[3], $prop[4], $prop[5], $prop[6], $prop[1], $idCAIXA, $prop[7])";
  
        $idRepreBoleto = $prop[0]; 
        mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
      }                    
    }
  }
  else {
    $idRepreBoleto=9999;
    for ($e=0; $e<count($props); $e++) {
      $prop = explode(';', $props[$e]);
      
      if (count($prop)>0) { 
        $sql="update entregaspropostas set idREPRESENTANTE=$prop[0], cpf='$prop[2]', valor=$prop[3], vlrRECEBIDO=$prop[4], ".
              " vlrADESAO=$prop[5], vlrPRESTADORA=$prop[6], idTIPO_CONTRATO=$prop[1], percentualPRESTADORA=$prop[7]  ".
              " where numREG=$prop[8] ";
  
        $idRepreBoleto = $prop[0]; 
        mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
      }                    
    }
  }

  // *****************************************************************************************************************************************
  // *****************************************************************************************************************************************
  // PAGAMENTOS (dependendo do tipo de pgto, gera-se um debito, exemplo: pagamento com vale, sera gerado um debito em nome do corretor) 
  // *****************************************************************************************************************************************
  // *****************************************************************************************************************************************
  for ($f=0; $f<count($pgtos); $f++) {
    $pgto = explode(';', $pgtos[$f]);
    $tipoPGTO = $pgto[0];
     
    $sql='';
    if ($tipoPGTO=='CHEQUE') 
      $sql="insert into pagamentos(tipoPGTO, cheque, idBANCO, dataCHEQUE, valor, idCAIXA, infoCHEQUE, nomeCHEQUE) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], '$pgto[3]', $pgto[4], $idCAIXA, '$pgto[5]', '$pgto[6]'";
    else if ($tipoPGTO=='BOLETO') {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'D', concat('PROPOSTA(S) PAGA(S) COM BOLETO'), $idRepreBoleto, $pgto[6], $logado, $idCAIXA, null, null, '$dataOPERACAO'";

      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 
      $sql="insert into pagamentos(tipoPGTO, valor, dataCHEQUE, infoCHEQUE, idCAIXA, nomeCHEQUE, cheque) ".
            " select '$tipoPGTO', $pgto[1], '$pgto[2]', '$pgto[3]', $idCAIXA, '$pgto[4]', '$pgto[5]'";
    }
    else if ($tipoPGTO=='CARTÃO') 
      $sql="insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
            " select '$tipoPGTO', $pgto[1], $idCAIXA";
    else if ($tipoPGTO=='DINHEIRO' || $tipoPGTO=='INTERNET') 
      $sql="insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
            " select '$tipoPGTO', $pgto[1], $idCAIXA";
    else if ($tipoPGTO=='VALE') {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'D', concat('PROPOSTA(S) PAGA(S) COM VALE'), $pgto[1], $pgto[2], $logado, $idCAIXA, null, null, '$dataOPERACAO'";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 
      $sql="insert into pagamentos(tipoPGTO, idREPRESENTANTE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";
    }
    else if ($tipoPGTO=='VALE CRÉDITO') { 
      $sql="insert into pagamentos(tipoPGTO, infoCHEQUE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";
    }

  
    if ($sql!='')    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }

  $infoCRED_DEB=$_REQUEST['cred'];
  if ($infoCRED_DEB!='') {
    $cred_deb_S=explode('|',$infoCRED_DEB);
    for ($rr=0; $rr<count($cred_deb_S); $rr++) {
      $cred_deb=explode(';',$cred_deb_S[$rr]);
      
      $tipo=$cred_deb[0];
      $numVALE=$cred_deb[1]=='' ? 'null' : $cred_deb[1];
      $idREPRE=$cred_deb[2];
      $vlrVALE=$cred_deb[3];
      $descVALE=$cred_deb[4];
      $dataVALE=$cred_deb[5];
      $descricaoVALE=$cred_deb[6];

      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, ".
            "idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE, inseridoManualmente) ".
            " select '$dataOPERACAO', '$tipo', '$descricaoVALE', $idREPRE, $vlrVALE, $logado, $idCAIXA, $numVALE, $descVALE, '$dataVALE', 1";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    }
  }
  $resp = "OK;$idCAIXA";
}  
  
  



/*****************************************************************************************/
IF ( $acao=='incluirCAIXA' || $acao=='editarCAIXA'  ) {
  $arq = fopen('caixa.txt', 'r'); 
  
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;
  
  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "100%,Pagamento" ).'</table>', $resp);
    
  $resp=str_replace('@altDivPAGAMENTOS', ($usandoTelaMaior1024_768 ? '100px' : '80px'), $resp);

  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  $resp = str_replace('@titVALES',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Tipo|10%,Nº|35%,Corretor|10%,Valor|10%,Desconto (%)|15%,Data pagar" ).'</table>', $resp);
  $resp=str_replace('@altDivVALES', ($usandoTelaMaior1024_768 ? '100px' : '80px'), $resp);

  $tabVALES = '<table width="99%" id="tabVALES" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

        
  $bloquearGRAVACAO='';
  if ( $acao=='editarCAIXA'  ) {
    $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, idFUNCIONARIO, " .
            "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO, valor, cx.faltaVERIFICAR, ".
            "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO,  ".
            "ifnull(repre.nome, '* ERRO *') as nomeREPRE, cx.contabilizarSAIDA, ".
           " ifnull(cx.alterada2_excluida1,0) as alterada2_excluida1, concat(opEXC.nome, '(', operadorEXCLUSAO, ')') as operadorEXCLUSAO, ".
          ' idESCRITORIO, esc.nome as nomeEscritorioAtual, idEscritorioOrigem, esc2.nome as nomeEscritorioOrigem '.
            "from caixa cx ".                          
            "left join operadores ope ".                        
            "   on ope.numero=cx.opRESPONSAVEL ".
            "left join contas pl " .
            " 		on pl.numero = cx.idOPERACAO " .
            "left join representantes repre " .
            " 		on repre.numero = cx.idFUNCIONARIO " .
            "left join operadores opEXC " .  
            "	  on cx.operadorEXCLUSAO = opEXC.numero " .
            "left join escritorios esc " .  
            "	  on esc.numero = idESCRITORIO " .
            "left join escritorios esc2 " .  
            "	  on esc2.numero = idEscritorioOrigem " .  
            "where cx.numreg=$vlr";
       
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);

    if ($row->idEscritorioOrigem!='')
      $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioOrigem ($row->idEscritorioOrigem)", $resp);
    else
      $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);
    $resp=str_replace('@local', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);

                  
    $resp=str_replace('@numREG', $vlr , $resp);
    $resp=str_replace('@faltaVERIFICAR', $row->faltaVERIFICAR, $resp);

    $resp=str_replace('vCONTA', $row->idOPERACAO, $resp);
    $resp=str_replace('vdescCONTA', $row->descCONTA, $resp);
    
    $resp=str_replace('vFUNCIONARIO', $row->idFUNCIONARIO, $resp);

    $resp=str_replace('v_FUNCIONARIO', $row->nomeREPRE, $resp);

    $resp=str_replace('vDESCRICAO', $row->descOPERACAO, $resp);    
    $resp=str_replace('vVALOR', number_format($row->valor, 2, ',', ''), $resp);

    $resp=str_replace('checkedCONTABILIZAR', $row->contabilizarSAIDA=='1' ? 'checked' : '', $resp);
    $resp=str_replace('@faltaVERIFICAR', $row->faltaVERIFICAR, $resp);

    if ($row->alterada2_excluida1==2) {
      $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que alterou:', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
      $bloquearGRAVACAO='<font color=red><b>* REGISTRO ALTERADO *</b></font> ';
    }
    else if ($row->alterada2_excluida1==1) {
      $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que excluiu:', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
      $bloquearGRAVACAO='<font color=red><b>* REGISTRO EXCLUÍDO *</b></font> ';
    }
    else { 
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', 'style="display:none"', $resp);
      $resp=str_replace('@titOPERADOR_EXCLUIU', '', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', '', $resp);
    }  




    $sql  = "select vale.tipo, vale.numVALE_CREDITO as numVALE, repre.nome as nomerepreVALE,  vale.numero as numREG,  ".
            " vale.valor as valorVALE, vale.representante as repreVALE, vale.descontoVALE, date_format(vale.pagarVALE, '%d/%m/%y') as pagarVALE, ".
            ' ifnull(inseridoManualmente, 0) as inseridoManualmente, vale.descricao '.
            "from creditos_descontos vale ".                        
            "left join representantes repre ".                        
            "   on repre.numero = vale.representante ".
            "where vale.idCAIXA=$vlr ";

    $rsVALES = mysql_query($sql) or die (mysql_error());  
    while ($regVALE = mysql_fetcH_object($rsVALES) )  {

      $valor = number_format($regVALE->valorVALE, 2, ',', '');
      $desconto = number_format($regVALE->descontoVALE, 2, ',', '');

      if ($regVALE->tipo=='D') $tipo='Débito';
      else {
        if ($regVALE->numVALE=='') $tipo='Crédito';
        else $tipo='Vale Crédito';
      }
      // o campo "inseridoManualmente" existe para diferenciar cred/deb   que sao inseridos por uma automatizacao do sistema
      // exemplo, qdo a operacao do caixa é um ADIANTAMENTO SALARIAL, o sistema automaticamente gera um debito em nome do favorecido
      // este devito NAO foi inserido manualmente e esta ligado com a operacao ADTO SALARIAL, so pode ser manipulado, retirado, dependendo
      // do que ocorrer com a operacao ADTO SALARIAL , estes possuem o campo inseridoManualmente= 0

      // cred/deb que foram inseridos manualmente, sem ligacao com a operacao do caixa, podem ser excluidos, manipulados
      // sem restricoes       estes possuem o campo inseridoManualmente= 1

      $lin = "<tr @cor id=\"VALE_$regVALE->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarCRED_DEB(this.id);" > '.
             "<td align=\"left\" width=\"20%\">$tipo</td>". 
             "<td align=\"left\" width=\"10%\">$regVALE->numVALE</td>".
             "<td align=\"left\" width=\"35%\">$regVALE->nomerepreVALE ($regVALE->repreVALE)</td>".
             "<td align=\"right\" width=\"10%\">$valor</td>".             
             "<td align=\"right\" width=\"10%\">$desconto</td>".
             "<td align=\"right\" width=\"15%\">$regVALE->pagarVALE</td>".
             "<td onclick=\"removeCRED_DEB('VALE_$regVALE->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style=\"display:none\">$regVALE->repreVALE</td>".
             "<td style=\"display:none\">$regVALE->nomerepreVALE</td>".
             "<td style=\"display:none\">$regVALE->inseridoManualmente</td>".
             "<td style=\"display:none\">$regVALE->descricao</td>".
             '</tr>';

      if ($regVALE->inseridoManualmente==0) $lin=str_replace('@cor', 'style="color:grey"', $lin);
      else $lin=str_replace('@cor', '', $lin);
      $tabVALES .= $lin;
    }
    mysql_free_result($rsVALES);



    // pagamentos      
    $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO,  pg.idREPRESENTANTE, cheque, pg.idPagouBoleto, ".
            " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
           "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
           " ifnull(ban.nome, '* ERRO *') as nomeBANCO, ".
           " ifnull(repre.nome, '* ERRO *') as nomeREPRE, pg.infoCHEQUE, pg.nomeCHEQUE ".
           "from pagamentos pg ".
           "left join representantes repre ".
           "    on repre.numero=pg.idREPRESENTANTE ".
           "left join operadores ope " .
           "	  on ope.numero = pg.idPagouBoleto " .
           "left join bancos ban " .
           "	  on ban.numero = pg.idBANCO " .
           "where idCAIXA=$vlr";

    $pags = mysql_query($sql) or die (mysql_error());  
    while ($pag = mysql_fetcH_object($pags) )  {
      
      $tipo=$pag->tipoPGTO;
            
      $valor = number_format($pag->valor, 2, ',', '')  ;
      if ($tipo=='CHEQUE') {
        $detalhes='<table border=0><tr>'.
                  '<td>_cinzaNº: </font></td><td align=left  width="80px">_azul'.$pag->cheque.'</font></td>'.
                  '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'.$pag->nomeBANCO.' ('.$pag->idBANCO.')</font></td>'.
                  '<td>_cinzaData: </font></td><td align=left width="80px">_azul'.$pag->dataCHEQUE.'</font></td>'.            
                  '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'.$valor.'</font></td>'.
                  '</tr></table>';
                  
        $colunasOCULTAS="<td style=\"display:none\">$pag->cheque</td>".                  
                        "<td style=\"display:none\">$pag->idBANCO</td>".
                        "<td style=\"display:none\">$pag->nomeBANCO</td>".
                        "<td style=\"display:none\">$pag->dataCHEQUE</td>".
                        "<td style=\"display:none\">$valor</td>".                                                                        
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>".
                        "<td style=\"display:none\">$pag->nomeCHEQUE</td>";
                        
      }
      if ($tipo=='BOLETO') {
        if ($pag->nomeBOLETOPAGO!='') {
          $boletoPAGO= "$pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
          $funcao="<img title='Cancelar pagamento do boleto' src='images/cancelarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,2)' />";
        }
        else {
          $boletoPAGO= "<font color=red>NÃO</font>";
          $funcao="<img  title='Pagar boleto' src='images/pagarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,1)' />";
        } 
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Vencimento: </font></td><td align=right width="60px">_azul'.$pag->dataCHEQUE.'</font></td>'.
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Nº: </font></td><td align=right width="60px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Pago: </font></td><td align=left width="250px">_azul'.$boletoPAGO.'</font></td>'.
                  "<td>$funcao</td>".
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>".
                        "<td style=\"display:none\">$pag->dataCHEQUE</td>".
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>".
                        "<td style=\"display:none\">$pag->nomeCHEQUE</td>".
                        "<td style=\"display:none\">$pag->cheque</td>";
      }
      if ($tipo=='CARTÃO') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>";                                                                        
      }
      if ($tipo=='DINHEIRO') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>";                                                                        
      }
      if ($tipo=='VALE') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaCorretor: </font></td><td align=left width="300px">_azul'.$pag->nomeREPRE.' ('.$pag->idREPRESENTANTE.')</font></td>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$pag->idREPRESENTANTE</td>".
                        "<td style=\"display:none\">$pag->nomeREPRE</td>".
                        "<td style=\"display:none\">$valor</td>";                                                                        
      }

      if ($tipo=='VALE CRÉDITO') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaNº: </font></td><td align=left width="80px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>".
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>";
                                                                                                
      }
      $detalhes = str_replace('_cinza', '<font color=gray>', $detalhes);
      $detalhes = str_replace('_azul', '<font style="color:blue;font-size:12px;">', $detalhes);

      $lin = "<tr id=\"PGTO_$pag->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarPGTO(this.id);" > '.
             "<td align=\"left\" width=\"15%\">$tipo</td>". 
             "<td align=\"left\" width=\"80%\">$detalhes</td>".             
             "<td onmousedown=\"removePGTO('PGTO_$pag->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             $colunasOCULTAS.
             "</tr>";                            
                            
      $tabPGTO .= $lin;       
    }
    mysql_free_result($pags);
    
  }
  else {
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = "$logado[0] ($logado[1])";
    
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('vOPERADOR', $logado, $resp);

    $resp=str_replace('@localORIGINAL', '', $resp);
    $resp=str_replace('@local', '', $resp);
                  
    $resp=str_replace('vCONTA', '', $resp);
    $resp=str_replace('vdescCONTA', '', $resp);
    
    $resp=str_replace('v_FUNCIONARIO', '', $resp);
    $resp=str_replace('vFUNCIONARIO', '', $resp);
    
    $resp=str_replace('vDESCRICAO', '', $resp);    
    $resp=str_replace('vVALOR', '', $resp);

    $resp=str_replace('vVALE', '', $resp);
    $resp=str_replace('vCORRETOR_VALE', '', $resp);
    $resp=str_replace('v_CORRETOR_VALE', '', $resp);
    $resp=str_replace('vVLR_VALE', '', $resp);

    $resp=str_replace('vDESCONTO_VALE', '', $resp);
    $resp=str_replace('vPAGAR_VALE', '', $resp);

    $resp=str_replace('checkedCONTABILIZAR', '', $resp);
        
    $resp=str_replace('@estiloOPERADOR_EXCLUIU', 'style="display:none"', $resp);
    $resp=str_replace('@titOPERADOR_EXCLUIU', '', $resp);
    $resp=str_replace('@nomeOPERADOR_EXCLUIU', '', $resp);

    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('@faltaVERIFICAR', '', $resp);
  }

  $tabPGTO .= '</table>';
  $tabVALES .= '</table>';

  if ($bloquearGRAVACAO=='') {
    switch ($acao) { 
      case 'incluirCAIXA':
        $resp=str_replace('TITULO_JANELA', "Novo registro",$resp);
        $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
        $resp=str_replace('readonly', '',$resp);
        break;      
      case 'editarCAIXA':
        $resp=str_replace('TITULO_JANELA', "Registro nº $vlr  ",$resp);    
        $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
        $resp=str_replace('readonly', '',$resp);
        break;
    }
  }
  else {
    switch ($acao) {
      case 'editarCAIXA':
        $resp=str_replace('TITULO_JANELA', "Visualizando operação do caixa nº $vlr  ",$resp);    
        break;
    }
    $resp=str_replace('texto_botao', $bloquearGRAVACAO, $resp);
  }        

  
  $resp=str_replace('@tabPAGAMENTOS', $tabPGTO, $resp);  
  $resp=str_replace('@tabVALES', $tabVALES, $resp);
  $resp .= '^';


}

/*****************************************************************************************/
IF ( $acao=='addPGTO_VALE_CREDITO' ) {
  $arq = fopen('vale_credito.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}

/*****************************************************************************************/
IF ( $acao=='addPGTO_VALE' ) {
  $arq = fopen('vale.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}

/*****************************************************************************************/
IF ( $acao=='addPGTO_CARTAO' ) {
  $arq = fopen('cartao.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}

/*****************************************************************************************/
IF ( $acao=='addPGTO_DINHEIRO'  ) {
  $arq = fopen('dinheiro.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}

/*****************************************************************************************/
IF ( $acao=='addPGTO_INTERNET' ) {
  $arq = fopen('internet.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}


/*****************************************************************************************/
IF ( $acao=='addPGTO_CHEQUE' ) {
  $arq = fopen('cheque.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}

/*****************************************************************************************/
IF ( $acao=='addPGTO_BOLETO' ) {
  $arq = fopen('boleto.txt', 'r'); 
  
  $form = '';
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
}  
           


/*****************************************************************************************/
IF ($acao=='lerPercAdeRepre') {
  $idREPRE = $_REQUEST['id'];
  $idPROD = $_REQUEST['idPROD'];  

  $sql  = "select ifnull(comiADESAO,0) as comiADESAO   ".
          " from representantes rep ".
          "  where rep.numero=$idREPRE";
          
	$resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetch_object($resultado);
  
//  mysql_free_result($resultado); 

  // le comissao adesao do tipo comissionamento identificado
  $sql  = "select ifnull(adesao, 0) as comiADESAO ".
          "from comissoes_adesao ".
          " where idCOMISSAO=$row->comiADESAO and idPRODUTO=$idPROD  ";
	$resultado = mysql_query($sql, $conexao) or die (mysql_error());
	$resp='0';
  if (mysql_num_rows($resultado)>0) {  
    $row = mysql_fetch_object($resultado);
    $resp = $row->comiADESAO;
  }  
}

/*****************************************************************************************/
if ( $acao=='lerDataHoje' )  {
  $resp = date("d/m/Y");
}

/*****************************************************************************************/
IF ( $acao=='lerDataHoje_2' )  {
  $resp = date("d/m/y");
}

//*****************************************************************************************/
IF ($acao=='lerREGS') {
  $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy
  $cmpPESQUISA=''; $infoPESQUISA='';
  if (isset( $_REQUEST['vlr3'])) $cmpPESQUISA = $_REQUEST['vlr2'];     
  if (isset( $_REQUEST['vlr3'])) $infoPESQUISA = $_REQUEST['vlr3'];

  $vendoEXCLUIDAS = $_REQUEST['excluidas'];

  if (isset($_REQUEST['esc']))   $idESCRITORIO =$_REQUEST['esc'];
  else $idESCRITORIO ='9999';
          
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  // verifica se operador tem direito ver caixa geral, ou somente o cx interno 
  $sql  = "select permissoes, escritorioATUAL, ifnull(escritorios, '') as escritorios, esc.nome as nomeESCRITORIO ".
          "from operadores ".
          "left join escritorios esc ".
          "   on esc.numero = (ascii(operadores.escritorioATUAL)-64)   ".
          "where operadores.numero=$logado";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  if (mysql_num_rows($resultado)==0 && $logado!=1) die('semAcesso');
  $row = mysql_fetcH_object($resultado);

  $tipoCAIXA='I';  
  if ( strpos($row->permissoes, 'H')!==false ) $tipoCAIXA='E';
  if ($logado==1) $tipoCAIXA='E';    // usuario 1= administrador


  if ($row->escritorios=='' && $logado!=1) die('semAcesso');

  $escritorioATUAL=$row->escritorioATUAL;

  if ($escritorioATUAL=='') {
    $escritorioATUAL=substr($row->escritorios,0,1);
  
    mysql_query("update operadores set escritorioATUAL='$escritorioATUAL' where numero=$vlr" ) or die (mysql_error());
  }

  // Z= todos
  if ($row->escritorioATUAL!='Z') {
    // le dados do escritorio atual do usuario
    $resultado = mysql_query("select nome,numero from escritorios where numero=(ascii('$escritorioATUAL')-64)", $conexao) or die (mysql_error());
    $row = mysql_fetcH_object($resultado);
  
    $nomeESCRITORIO = $row->nome;
    $idESCRITORIO = $row->numero;
  }
  else {
    $nomeESCRITORIO = 'TODOS';
    $idESCRITORIO = 9999;
  }

  $sql  = "select numero, nome  " .
          "from representantes ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $repres=array();
  while ($row = mysql_fetcH_object($resultado)) {    
    $repres[$row->numero]=$row->nome;
  }
  
  $sql  = "select ep.sequencia, cx.numreg as idOP,  date_format(cx.dataOP, '%d/%m  %H:%i') as dataOPERACAO, plano.tipoENVOLVIDO,  " .
          "ifnull(plano.nome, '* erro *') as descCONTA, cx.descOPERACAO, ifnull(cx.faltaVERIFICAR, 'N') as faltaVERIFICAR, cx.idESCRITORIO, " .            
          "ifnull(tipoprop.descricao, '* erro *') as descTIPO_CONTRATO,  ep.idTIPO_CONTRATO, plano.contaENTREGA_PROPOSTA, " .
          " ep.vlrRECEBIDO, ep.cpf, cx.valor, ep.numreg as idENTREGA, ep.idREPRESENTANTE, cx.idFUNCIONARIO, cx.idOPERACAO,  ".            
          " plano.entOUsai, ifnull(cx.totDINHEIRO,0) as totDINHEIRO, ifnull(totCHEQUE, 0) as totCHEQUE, cx.contabilizarSAIDA, cx.temFormasPgto, ".
          ' ifnull(cx.alterada2_excluida1,0) as alterada2_excluida1 '.
          "from caixa cx " .
          "left join entregaspropostas ep  " .
          " 	on cx.numreg = ep.idCAIXA  " .
          "left join contas plano  " .
          "	  on plano.numero = cx.idOPERACAO " .
          "left join tipos_contrato tipoprop " .
          "	  on tipoprop.numreg = ep.idTIPO_CONTRATO    " .
          "  where 1=1 @criterioEXCLUIDAS  ".
          ($idESCRITORIO!=9999 ? " and cx.idESCRITORIO=$idESCRITORIO " : ' and 1=1 ');
  if ($cmpPESQUISA=='')
    $sql .= " and date_format(cx.dataop, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' @criterioTIPOCAIXA order by cx.dataOP desc ";
  else {
    // cpf    
    if ($cmpPESQUISA=='1')  {$cmpPESQUISA='CPF/CNPJ'; 
      $sql .= " and replace(replace(replace(cpf, '.',''), '-',''),'/','')='$infoPESQUISA' ";}  
    if ($cmpPESQUISA=='2')  {

      // separa operacoes do caixa que tenham determinado vale credito ou em seus pagamentos ou em seus creditos/debitos gerados
      //******************** pagamentos com determinado vale credito
      $operacoesQueUsamOValeCredito='-1';
      $sqlTMP = "select idCAIXA  ".
             "from pagamentos pag ".
             'left join caixa cx '.
             '    on cx.numreg=idCAIXA '.                 
             "where infoCHEQUE=$infoPESQUISA and tipoPGTO='VALE CRÉDITO' and ifnull(cx.alterada2_excluida1,0)=0 ";
      $rsTEMP = mysql_query($sqlTMP, $conexao) or die ($sqlTMP .'<br><br>'.mysql_error());

      if (mysql_num_rows($rsTEMP)>0) {
        while ($regTMP = mysql_fetcH_object($rsTEMP)) {
          $operacoesQueUsamOValeCredito .= ',' . $regTMP->idCAIXA;
        }
      }
      mysql_free_result($rsTEMP);



      //******************** creditos/descontos determinado vale credito
      $sqlTMP = "select idCAIXA  ".
             "from creditos_descontos cred ".
             'left join caixa cx '.
             '    on cx.numreg=idCAIXA '.                 
             "where numvale_credito=$infoPESQUISA and ifnull(cx.alterada2_excluida1,0)=0 ";
      $rsTEMP = mysql_query($sqlTMP, $conexao) or die ($sqlTMP .'<br><br>'.mysql_error());

      if (mysql_num_rows($rsTEMP)>0) {
        while ($regTMP = mysql_fetcH_object($rsTEMP)) {
          $operacoesQueUsamOValeCredito .= ',' . $regTMP->idCAIXA;
        }
      }
      mysql_free_result($rsTEMP);

      $cmpPESQUISA='Nº VALE CRÉDITO'; 
      $sql .= " and cx.numreg in ($operacoesQueUsamOValeCredito) ";
    }  
    if ($cmpPESQUISA=='3')  {$cmpPESQUISA='Nº OPERAÇÃO DO CAIXA'; $sql .= " and cx.numreg=$infoPESQUISA ";}
    if ($cmpPESQUISA=='4')  {$cmpPESQUISA='Nº BOLETO'; $sql .= " and cx.numBOLETO='$infoPESQUISA' ";}
    if ($cmpPESQUISA=='6')  {$cmpPESQUISA='Pendentes de verificação'; $sql .= " and cx.faltaVERIFICAR='S' ";}
  }
  if ($vendoEXCLUIDAS=='sim')
    $sql = str_replace('@criterioEXCLUIDAS', '', $sql);
  else
    $sql = str_replace('@criterioEXCLUIDAS', ' and ifnull(alterada2_excluida1,0)=0 ', $sql);

  // filtra operacoes do cx interno, se usuario é limnitado
  $sql=str_replace('@criterioTIPOCAIXA', (($tipoCAIXA=='I') ? " and plano.tipoCAIXA='I' " : ''), $sql);

  // se listando caixa de determinada data, procura um dia anterior a esta data em que haja alguma operacao de caixa
  // se encontrando, soma cheques, DH e grava como TRANSPORTADO
  $dataTRANSPORTADO='NAO CALC'; 
  $TRANSP_totSaiCHEQUE=0; $TRANSP_totEntCHEQUE=0; $TRANSP_totSaiDINHEIRO=0; $TRANSP_totEntDINHEIRO=0;
  $TRANSP_saldoDINHEIRO = 0; 
  $TRANSP_saldoCHEQUE = 0;

    //echo $sql;

  if ($cmpPESQUISA=='') {
    $sqlANT="select date_format(dataop, '%Y%m%d') as dataOPERACAO, date_format(dataop, '%d/%m/%y') as dataTRANSPORTADO ".
            "from caixa ".
            " where date_format(dataop, '%Y%m%d')<'$dataTRAB' order by dataop desc limit 1";
    //echo $sqlANT;
    $rsANT = mysql_query($sqlANT, $conexao) or   die ($sqlANT.'<br>'.mysql_error());
    if ( mysql_num_rows($rsANT) > 0 ) {      
      $regANT = mysql_fetcH_object($rsANT);

      // faz a somatoria de cheques/dinheiro das operacoes de caixa feitas na 1a data anterior (encontrada) à data atual
      $dataTRANSPORTADO = $regANT->dataTRANSPORTADO;
      $sqlPGTO_ANT="select pl.entOUsai, pag.tipoPGTO,pag.valor ".
                    "from pagamentos pag ".
                    "inner join caixa cx ".
                    "  on cx.numreg = pag.idCAIXA ".
                    "inner join contas pl ".
                    "  on pl.numero=cx.idOPERACAO ".
                    "where (pag.tipopgto='CHEQUE' or pag.tipopgto='DINHEIRO') and ifnull(cx.alterada2_excluida1,0)=0 and cx.idESCRITORIO=$idESCRITORIO ".
                    "and date_format(dataop, '%Y%m%d') between '$regANT->dataOPERACAO' and '$regANT->dataOPERACAO'    ";
		//echo "<br><br>" . $sqlPGTO_ANT;
      $rsPGTOS = mysql_query($sqlPGTO_ANT, $conexao) or die ($sqlPGTO_ANT .'<br><br>'.mysql_error());

      while ($regPGTOS = mysql_fetcH_object($rsPGTOS)) {
        if ($regPGTOS->tipoPGTO=='CHEQUE') {
          if ($regPGTOS->entOUsai=='S') $TRANSP_totSaiCHEQUE+=$regPGTOS->valor; else $TRANSP_totEntCHEQUE+=$regPGTOS->valor;
        }
        if ($regPGTOS->tipoPGTO=='DINHEIRO') {
          if ($regPGTOS->entOUsai=='S') $TRANSP_totSaiDINHEIRO+=$regPGTOS->valor; else $TRANSP_totEntDINHEIRO+=$regPGTOS->valor;
        }
      }
      mysql_free_result($rsPGTOS);

	

      $saldoTRANSP_DINHEIRO = $TRANSP_totEntDINHEIRO-$TRANSP_totSaiDINHEIRO;
      $saldoTRANSP_CHEQUE = $TRANSP_totEntCHEQUE-$TRANSP_totSaiCHEQUE;
      

      // alem de fazer a somatoria, le qual era o "transportado" na 1a data anterior à data atual
      $sqlANT="select saldoCHEQUE, saldoDINHEIRO from transportado where data='$regANT->dataOPERACAO' and idESCRITORIO=$idESCRITORIO;";
      //echo $sqlANT;
      $rsTRANSP = mysql_query($sqlANT, $conexao) or die ($sqlANT.'<br><br>'.mysql_error());

      if ( mysql_num_rows($rsTRANSP) > 0 ) {
        $regTRANSP = mysql_fetcH_object($rsTRANSP);

        $TRANSP_saldoDINHEIRO = $regTRANSP->saldoDINHEIRO;
        $TRANSP_saldoCHEQUE = $regTRANSP->saldoCHEQUE;
      }
      //die($TRANSP_saldoCHEQUE);
      mysql_free_result($rsTRANSP);
    }
    mysql_free_result($rsANT);
  }
              
  $resultado = mysql_query($sql, $conexao) or die ($sql.'<br><br>'.mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;   
  $largura2 = $_SESSION['largIFRAME'] * 0.2;   
  $largura3 = $_SESSION['largIFRAME'] * 0.15;     
  $largura4 = $_SESSION['largIFRAME'] * 0.25;
  $largura5 = $_SESSION['largIFRAME'] * 0.05;
    
	$header = "$largura1 px,Data|$largura3 px,Envolvido&nbsp;&nbsp;&nbsp;|$largura2 px,Tipo contrato|".
            "$largura2 px,Conta/Descrição|$largura1 px,CPF/CNPJ|$largura1 px,Cadastrada?|$largura5 px,&nbsp;".
            "|$largura1 px,&nbsp;";  
   
  $resp = tabelaPADRAO('width="98%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="99%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS =  mysql_num_rows($resultado);
  
  if ($qtdeREGS>0)   mysql_data_seek($resultado, 0);    
  
  $totEntDINHEIRO=0;  $totEntCHEQUE=0;
  $totSaiDINHEIRO=0;  $totSaiCHEQUE=0;

  $i=1;
  $idOPATUAL=-1; $tipoOPATUAL='';
  $cor='white';  
  while ($row = mysql_fetcH_object($resultado)) {    
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";
      $largura5="width=\"$largura5 px\"";
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4='';  $largura5='';
    }
    $i++;
    
    // contabilizarSAIDA,   quando 1,   NAO CONTABILIZA SAIDA,  houve um equivoco na criacao do nome do campo
    if ($idOPATUAL!=$row->idOP && $idOPATUAL!=-1)    {
      $cor=($cor=='white') ? '#EDEDED' : 'white';
/*
      if ($row->contabilizarSAIDA!='1') {
        $totDINHEIRO += $row->totDINHEIRO;
        $totCHEQUE += $row->totCHEQUE;
      }
*/
      $sql = "select pg.tipoPGTO, pg.valor ". 
             "from pagamentos pg ".
             "where idCAIXA=$idOPATUAL and (tipoPGTO='CHEQUE' or tipoPGTO='DINHEIRO'); ";
      $pags = mysql_query($sql) or die (mysql_error());
      while ($pag = mysql_fetcH_object($pags) )  {
        if ($pag->tipoPGTO=='CHEQUE') {
          if ($tipoOPATUAL=='S') $totSaiCHEQUE+=$pag->valor; else $totEntCHEQUE+=$pag->valor;
        }
        if ($pag->tipoPGTO=='DINHEIRO') {
          if ($tipoOPATUAL=='S') $totSaiDINHEIRO+=$pag->valor; else $totEntDINHEIRO+=$pag->valor;
        }
      }
    }
    if ($row->entOUsai=='S')   $corFORE='red'; else $corFORE='blue';
    if ($row->faltaVERIFICAR=='S')    $corFORE='green'; 

//    if ($row->contabilizarSAIDA=='1') $corFORE='#FFa0a0';
    
    $idOPATUAL=$row->idOP;  $tipoOPATUAL=$row->entOUsai;
    if ($row->idENTREGA!='') {      
      // se a operacao do caixa atual trata-se de uma entrega de proposta, o envolvido sera obrigatoriamente um CORRETOR
      // ate porque a identificacao do envolvido é obtida pelo campo ID CORRETOR QUE ENTREGOU A PROPOSTA            
      $envolvido = $repres[$row->idREPRESENTANTE].' ('.$row->idREPRESENTANTE.')';

      $cpf = $row->cpf ;
      $vlrRECEBIDO = number_format($row->vlrRECEBIDO, 2, ',', '');
      $contrato = "$row->descTIPO_CONTRATO ($row->idTIPO_CONTRATO)";

    }
    else {
      if ($row->contabilizarSAIDA!='1') {
/*
        if ($row->entOUsai=='S')    
          $totDINHEIRO -= $row->valor;    else      $totDINHEIRO += $row->valor;
*/
      }

      // se definido no tipo de conta envolvida (plano de contas) na operacao do caixa, que
      // ela diz respeito a um corretor, obviamente busca o nome do corretor, se nao, do funcionario
//      if ($row->tipoENVOLVIDO=='C')   $envolvido = $repres[$row->idFUNCIONARIO].' ('.$row->idFUNCIONARIO.')';
//      else $envolvido = $funcionarios[$row->idFUNCIONARIO] . ' ('.$row->idFUNCIONARIO.')';

      $envolvido = $repres[$row->idFUNCIONARIO].' ('.$row->idFUNCIONARIO.')';
          

      $cpf='-';
      $vlrRECEBIDO = number_format($row->valor, 2, ',', '');
      $contrato = '-';
    }
    
    if ($row->descOPERACAO!='') 
//      $infoCONTA=trim($row->descCONTA) . " ($row->idOPERACAO)" . ' - ' .substr($row->descOPERACAO, 0, 15);
      $infoCONTA=substr(trim($row->descCONTA), 0,30) . " ($row->idOPERACAO)";
    else
      $infoCONTA=substr(trim($row->descCONTA), 0,30) . " ($row->idOPERACAO)";
            
    $idUNICO = 'cx'.$i . '_'. $row->idOP;
    if ($row->contaENTREGA_PROPOSTA=='1') {
      $cadastrada=$row->sequencia=='' ? 'Não' : 'Sim';
      $pendente = $row->temFormasPgto=='1' ? "<img src='images/pendenteCX_OK.png'  />" : 
          "<img src='images/pendenteCX.png' title='Nenhum forma de pagamento registrada' />";
    }
    else {
      $cadastrada='-';
      $pendente = "<img src='images/pendenteCX_OK.png'  />"; 

    }
//         "<td align=\"right\" $largura1>$vlrRECEBIDO&nbsp;&nbsp;</td>".
   
    $adicional='';
    if ($row->alterada2_excluida1==1) $adicional= '<font color=red><b>[ EXCLUÍDA ]</b></font>';
    if ($row->alterada2_excluida1==2) $adicional= '<font color=red><b>[ ALTERADA ]</b></font>';
    $lin = "<tr title='@title' @corBACK @corFORE ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$idUNICO\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;$row->dataOPERACAO</td>".
            "<td align=\"left\" $largura3>&nbsp;$envolvido </td>".
            "<td align=\"left\" $largura2>$contrato</td>". 
            "<td align=\"left\" $largura2>$infoCONTA</td>".
            "<td align=\"left\" $largura1>$row->cpf</td>".                        
            "<td align=\"center\" $largura1>$cadastrada</td>".
            "<td align=\"center\" $largura5>$pendente</td>".
            "<td align=\"center\" $largura1>$adicional</td>".
            "</tr>";
    $lin=str_replace('@corBACK', "bgcolor='$cor'" , $lin);          
    if ($corFORE=='green')
      $lin=str_replace('@corFORE', "style='color:$corFORE;font-weight:bold;'" , $lin);
    else
      $lin=str_replace('@corFORE', "style='color:$corFORE'" , $lin);
    $lin=str_replace('@title', ($cor=='#EDEDED' ? '.' : ''), $lin);    
    $resp .= $lin;
  }

  if ($idOPATUAL!=-1) {
    $sql = "select pg.tipoPGTO, pg.valor ". 
           "from pagamentos pg ".
           "where idCAIXA=$idOPATUAL and (tipoPGTO='CHEQUE' or tipoPGTO='DINHEIRO'); ";
    //echo $sql;

    $pags = mysql_query($sql) or die (mysql_error());
    while ($pag = mysql_fetcH_object($pags) )  {
      if ($pag->tipoPGTO=='CHEQUE') {
        if ($tipoOPATUAL=='S') $totSaiCHEQUE+=$pag->valor; else $totEntCHEQUE+=$pag->valor;
      }
      if ($pag->tipoPGTO=='DINHEIRO') {
        if ($tipoOPATUAL=='S') $totSaiDINHEIRO+=$pag->valor; else $totEntDINHEIRO+=$pag->valor;
      }
    }
  }
  //echo $totEntDINHEIRO;

  // grava o "tranportado" da data atual, mas somente o faz, se a data atual tem algum saldo em dinheiro ou cheque
  $saldoDINHEIRO = $totEntDINHEIRO-$totSaiDINHEIRO;
  $saldoCHEQUE = $totEntCHEQUE-$totSaiCHEQUE;

  
  $saldoTRANSP_DINHEIRO = $TRANSP_saldoDINHEIRO + $TRANSP_totEntDINHEIRO-$TRANSP_totSaiDINHEIRO;
  

  
  //echo $TRANSP_saldoCHEQUE . "<br />";
  //echo $TRANSP_totEntCHEQUE . "<br />";
  //echo $TRANSP_totSaiCHEQUE . "<br />";
  $saldoTRANSP_CHEQUE = $TRANSP_saldoCHEQUE + $TRANSP_totEntCHEQUE-$TRANSP_totSaiCHEQUE;
  //$saldoTRANSP_CHEQUE = 0;
  
  //echo $TRANSP_saldoDINHEIRO . "<br />" . $TRANSP_totEntDINHEIRO . "<br />" . $TRANSP_totSaiDINHEIRO . "<br />";
  //echo $saldoTRANSP_DINHEIRO;

  if ($dataTRANSPORTADO=='24/10/12') {
    $saldoTRANSP_DINHEIRO = 0;
    $saldoTRANSP_CHEQUE = 0;
  } 
   


//  if ($saldoCHEQUE!=0 || $saldoDINHEIRO!=0) {
  if ($cmpPESQUISA=='') {
  	
    $sqlATUAL="select numreg from transportado where date_format(data, '%Y%m%d')='$dataTRAB' and idESCRITORIO=$idESCRITORIO ";
    //echo $sqlATUAL;
    $rsTRANSP = mysql_query($sqlATUAL, $conexao) or die (mysql_error());

    $dataGRAVAR = substr($dataTRAB, 0, 4).'-'.substr($dataTRAB, 4, 2).'-'.substr($dataTRAB, 6, 2);
    if ( mysql_num_rows($rsTRANSP) > 0 ) {
      $regTRANSP=mysql_fetch_object($rsTRANSP); 
      mysql_query("update transportado set saldoCHEQUE=$saldoTRANSP_CHEQUE, saldoDINHEIRO=$saldoTRANSP_DINHEIRO  ".
                    "where numreg=$regTRANSP->numreg", $conexao) or die (mysql_error());
    }
    else
      mysql_query("insert into transportado(saldoCHEQUE, saldoDINHEIRO, data, idESCRITORIO) ".
                  " values($saldoTRANSP_CHEQUE, $saldoTRANSP_DINHEIRO, '$dataGRAVAR', $idESCRITORIO) ", $conexao) or die ('ins 1<br><br>'.mysql_error());
    mysql_free_result($rsTRANSP);
  }          

  $saldoTRANSP_DINHEIRO = number_format($saldoTRANSP_DINHEIRO , 2, ',', '');
  $saldoTRANSP_CHEQUE = number_format($saldoTRANSP_CHEQUE, 2, ',', '');

  
  $auxTransP_Dinheiro = str_replace(',', '.', $saldoTRANSP_DINHEIRO);
  //echo $auxTransP_Dinheiro;
  
  $descCAIXA=($tipoCAIXA=='I') ? 'Caixa interno' : 'Caixa interno/caixa geral';
  $saldoDINHEIRO = number_format($saldoDINHEIRO + $auxTransP_Dinheiro, 2, ',', '');
  $saldoCHEQUE = number_format($saldoCHEQUE + $saldoTRANSP_CHEQUE, 2, ',', '');



  $totEntDINHEIRO = number_format($totEntDINHEIRO , 2, ',', '');
  $totEntCHEQUE = number_format($totEntCHEQUE, 2, ',', '');
  $totSaiDINHEIRO = number_format($totSaiDINHEIRO , 2, ',', '');
  $totSaiCHEQUE = number_format($totSaiCHEQUE, 2, ',', '');

  //echo $saldoTRANSP_DINHEIRO;

  //echo $saldoTRANSP_DINHEIRO;
  //echo $saldoTRANSP_CHEQUE;
  $diaSEMANA=diasemana(date($dataTRAB));
  if ($cmpPESQUISA=='')
  {
    $resp .= "^$qtdeREGS^^$descCAIXA^$totEntDINHEIRO^$totEntCHEQUE^$nomeESCRITORIO^$idESCRITORIO^$totSaiDINHEIRO^$totSaiCHEQUE^".
                  "$saldoDINHEIRO^$saldoCHEQUE^$saldoTRANSP_DINHEIRO^$saldoTRANSP_CHEQUE^$dataTRANSPORTADO^$diaSEMANA";
  }
  else {
    if (trim($infoPESQUISA)=='nada')
      $resp .= "^$qtdeREGS^$cmpPESQUISA^$descCAIXA^$totEntDINHEIRO^$totEntCHEQUE^$nomeESCRITORIO^$idESCRITORIO^".
                "$totSaiDINHEIRO^$totSaiCHEQUE^$saldoDINHEIRO^$saldoCHEQUE^$saldoTRANSP_DINHEIRO^$saldoTRANSP_CHEQUE^$dataTRANSPORTADO^$diaSEMANA"; 
    else
      $resp .= "^$qtdeREGS^$cmpPESQUISA=$infoPESQUISA^$descCAIXA^$totEntDINHEIRO^$totEntCHEQUE^$nomeESCRITORIO^$idESCRITORIO^".
                "$totSaiDINHEIRO^$totSaiCHEQUE^$saldoDINHEIRO^$saldoCHEQUE^$saldoTRANSP_DINHEIRO^$saldoTRANSP_CHEQUE^$dataTRANSPORTADO^$diaSEMANA";
  }  
  
  
  //echo $dataTRANSPORTADO;
  //echo($dataTRAB);
  //die($totEntCHEQUE);   
}


/*****************************************************************************************/
IF ( $acao=='incluirENTREGA' || $acao=='editarENTREGA'  ) {

  $arq = fopen('entrega.txt', 'r'); 
  
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;

  $bloquearGRAVACAO='';
  $resp = str_replace('@titPROPOSTAS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Corretor|20%,Tipo contrato|10%,CPF/CNPJ|10%,Valor|15%,&nbsp;&nbsp;&nbsp;&nbsp;Valor recebido|15%,Vlr AllCross|10%,Adesão" ).'</table>', $resp);
  $resp=str_replace('@altDivPROPOSTAS', ($usandoTelaMaior1024_768 ? '130px' : '55px'), $resp);
  
  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "100%,Pagamento" ).'</table>', $resp);
  $resp=str_replace('@altDivPAGAMENTOS', ($usandoTelaMaior1024_768 ? '100px' : '50px'), $resp);

  $resp = str_replace('@titVALES',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Tipo|10%,Nº|35%,Corretor|10%,Valor|10%,Desconto (%)|15%,Data pagar|1%,&nbsp;" ).'</table>', $resp);
  $resp=str_replace('@altDivVALES', ($usandoTelaMaior1024_768 ? '100px' : '50px'), $resp);

  $tabVALES = '<table width="99%" id="tabVALES" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  
  $tabPROP = '<table width="99%" id="tabPROPOSTAS" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        
  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        
  if ($acao!='incluirENTREGA')   {            
    $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, " .
            "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO,  cx.faltaVERIFICAR, ".
            "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO,  ".
           " ifnull(cx.alterada2_excluida1,0) as alterada2_excluida1, concat(opEXC.nome, '(', operadorEXCLUSAO, ')') as operadorEXCLUSAO, ".
          ' idESCRITORIO, esc.nome as nomeEscritorioAtual, idEscritorioOrigem, esc2.nome as nomeEscritorioOrigem '.
            "from caixa cx ".                          
            "left join operadores ope ".                        
            "   on ope.numero=cx.opRESPONSAVEL ".
            "left join contas pl " .
            " 		on pl.numero = cx.idOPERACAO " . 
            "left join operadores opEXC " .  
            "	  on cx.operadorEXCLUSAO = opEXC.numero " .
            "left join escritorios esc " .  
            "	  on esc.numero = idESCRITORIO " .
            "left join escritorios esc2 " .  
            "	  on esc2.numero = idEscritorioOrigem " .  
            "where cx.numreg=$vlr";

    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);

    if ($row->idEscritorioOrigem!='')
      $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioOrigem ($row->idEscritorioOrigem)", $resp);
    else
      $resp=str_replace('@localORIGINAL', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);

    $resp=str_replace('@local', "$row->nomeEscritorioAtual ($row->idESCRITORIO)", $resp);
                  
    $resp=str_replace('@numREG', $vlr , $resp);
    $resp=str_replace('@faltaVERIFICAR', $row->faltaVERIFICAR, $resp);
    $idCAIXA = $row->idCAIXA;


    if ($row->alterada2_excluida1==2) {
      $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que alterou:', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
      $bloquearGRAVACAO='<font color=red><b>* REGISTRO ALTERADO *</b></font> ';
    }
    else if ($row->alterada2_excluida1==1) {
      $resp=str_replace('@titOPERADOR_EXCLUIU', 'Operador(a) que excluiu:', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', $row->operadorEXCLUSAO, $resp);
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', '', $resp);
      $bloquearGRAVACAO='<font color=red><b>* REGISTRO EXCLUÍDO *</b></font> ';
    }
    else { 
      $resp=str_replace('@estiloOPERADOR_EXCLUIU', 'style="display:none"', $resp);
      $resp=str_replace('@titOPERADOR_EXCLUIU', '', $resp);
      $resp=str_replace('@nomeOPERADOR_EXCLUIU', '', $resp);
    }  
    $sql = "select ep.numREG, idREPRESENTANTE, ifnull(repre.nome, '* ERRO *') as nomeREPRESENTANTE, ep.cpf, ".
           "ep.valor, ep.vlrRECEBIDO, ep.vlrADESAO, ep.vlrPRESTADORA, ep.idTIPO_CONTRATO, ".
           " ifnull(tipoprop.descricao, '* ERRO *') as descTIPO_CONTRATO, ep.percentualPRESTADORA ".
           "from entregaspropostas ep ".
           "left join representantes repre ".
           "    on repre.numero=ep.idREPRESENTANTE ".
           "left join tipos_contrato tipoprop " .
           "	  on tipoprop.numreg = ep.idTIPO_CONTRATO " .
         "where idCAIXA=$idCAIXA";

    $propostas = mysql_query($sql) or die (mysql_error());
    while ($proposta = mysql_fetcH_object($propostas) )  {
      
      $vlrADESAO = number_format($proposta->vlrADESAO, 2, ',', '')  ;
      $vlrRECEBIDO = number_format($proposta->vlrRECEBIDO, 2, ',', '')  ;
      $vlrPRESTADORA = number_format($proposta->vlrPRESTADORA, 2, ',', '')  ;
      $percPRESTADORA = number_format($proposta->percentualPRESTADORA, 0, ',', '')  ;
      $valor = number_format($proposta->valor, 2, ',', '')  ;
      
      $linPROP = "<tr id=\"PROP_$proposta->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarPROPOSTA(this.id);" > '.
             "<td align=\"left\" width=\"20%\">$proposta->nomeREPRESENTANTE ($proposta->idREPRESENTANTE)</td>". 
             "<td align=\"left\" width=\"20%\">$proposta->descTIPO_CONTRATO ($proposta->idTIPO_CONTRATO)</td>".
             "<td align=\"left\" width=\"10%\">$proposta->cpf</td>".
             "<td align=\"right\" width=\"10%\">$valor</td>".             
             "<td align=\"right\" width=\"15%\">$vlrRECEBIDO</td>".
             "<td align=\"right\" width=\"15%\">$vlrPRESTADORA ($percPRESTADORA%)</td>".
             "<td align=\"right\" width=\"10%\">$vlrADESAO</td>".                                       
             "<td onclick=\"removePROPOSTA('PROP_$proposta->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style=\"display:none\">$proposta->idREPRESENTANTE</td>".
             "<td style=\"display:none\">$proposta->idTIPO_CONTRATO</td>".               
             "<td style=\"display:none\">$proposta->nomeREPRESENTANTE</td>".
             "<td style=\"display:none\">$proposta->descTIPO_CONTRATO</td>".                  
             "<td style=\"display:none\">$proposta->numREG</td>".
             '</tr>';

      $tabPROP .= $linPROP;
    }
    mysql_free_result($propostas);





    $sql  = "select vale.tipo, vale.numVALE_CREDITO as numVALE, repre.nome as nomerepreVALE,  vale.numero as numREG,  ".
            " vale.valor as valorVALE, vale.representante as repreVALE, vale.descontoVALE, date_format(vale.pagarVALE, '%d/%m/%y') as pagarVALE, ".
            ' ifnull(inseridoManualmente, 0) as inseridoManualmente, vale.descricao '.
            "from creditos_descontos vale ".                        
            "left join representantes repre ".                        
            "   on repre.numero = vale.representante ".
            "where vale.idCAIXA=$idCAIXA  ";

    $rsVALES = mysql_query($sql) or die (mysql_error());  
    while ($regVALE = mysql_fetcH_object($rsVALES) )  {

      $valor = number_format($regVALE->valorVALE, 2, ',', '');
      $desconto = number_format($regVALE->descontoVALE, 2, ',', '');

      if ($regVALE->tipo=='D') $tipo='Débito';
      else {
        if ($regVALE->numVALE=='') $tipo='Crédito';
        else $tipo='Vale Crédito';
      }
      // o campo "inseridoManualmente" existe para diferenciar cred/deb   que sao inseridos por uma automatizacao do sistema
      // exemplo, qdo a operacao do caixa é um ADIANTAMENTO SALARIAL, o sistema automaticamente gera um debito em nome do favorecido
      // este devito NAO foi inserido manualmente e esta ligado com a operacao ADTO SALARIAL, so pode ser manipulado, retirado, dependendo
      // do que ocorrer com a operacao ADTO SALARIAL , estes possuem o campo inseridoManualmente= 0

      // cred/deb que foram inseridos manualmente, sem ligacao com a operacao do caixa, podem ser excluidos, manipulados
      // sem restricoes       estes possuem o campo inseridoManualmente= 1

      $lin = "<tr @cor id=\"VALE_$regVALE->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarCRED_DEB(this.id);" > '.
             "<td align=\"left\" width=\"20%\">$tipo</td>". 
             "<td align=\"left\" width=\"10%\">$regVALE->numVALE</td>".
             "<td align=\"left\" width=\"35%\">$regVALE->nomerepreVALE ($regVALE->repreVALE)</td>".
             "<td align=\"right\" width=\"10%\">$valor</td>".             
             "<td align=\"right\" width=\"10%\">$desconto</td>".
             "<td align=\"right\" width=\"15%\">$regVALE->pagarVALE</td>".
             "<td onclick=\"removeCRED_DEB('VALE_$regVALE->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style=\"display:none\">$regVALE->repreVALE</td>".
             "<td style=\"display:none\">$regVALE->nomerepreVALE</td>".
             "<td style=\"display:none\">$regVALE->inseridoManualmente</td>".
             "<td style=\"display:none\">$regVALE->descricao</td>".
             '</tr>';

      if ($regVALE->inseridoManualmente==0) $lin=str_replace('@cor', 'style="color:grey;"', $lin);
      else $lin=str_replace('@cor', '', $lin);

      $tabVALES .= $lin;
    }
    mysql_free_result($rsVALES);



    $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO, pg.idREPRESENTANTE, cheque, ".
           "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, pg.idPagouBoleto, ".
            " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
           " ifnull(ban.nome, '* ERRO *') as nomeBANCO,  ".
           " ifnull(repre.nome, '* ERRO *') as nomeREPRE, pg.infoCHEQUE, pg.nomeCHEQUE ".
           "from pagamentos pg ".
           "left join representantes repre ".
           "    on repre.numero=pg.idREPRESENTANTE ".
           "left join bancos ban " .
           "	  on ban.numero = pg.idBANCO " .
           "left join operadores ope " .
           "	  on ope.numero = pg.idPagouBoleto " .
           "where idCAIXA=$idCAIXA";

    $pags = mysql_query($sql) or die (mysql_error());  
    while ($pag = mysql_fetcH_object($pags) )  {
      
      $tipo=$pag->tipoPGTO;
            
      $valor = number_format($pag->valor, 2, ',', '')  ;
      if ($tipo=='CHEQUE') {
        $detalhes='<table border=0><tr>'.
                  '<td>_cinzaNº: </font></td><td align=left  width="80px">_azul'.$pag->cheque.'</font></td>'.
                  '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'.$pag->nomeBANCO.' ('.$pag->idBANCO.')</font></td>'.
                  '<td>_cinzaData: </font></td><td align=left width="80px">_azul'.$pag->dataCHEQUE.'</font></td>'.            
                  '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'.$valor.'</font></td>'.
                  '</tr></table>';
                  
        $colunasOCULTAS="<td style=\"display:none\">$pag->cheque</td>".                  
                        "<td style=\"display:none\">$pag->idBANCO</td>".
                        "<td style=\"display:none\">$pag->nomeBANCO</td>".
                        "<td style=\"display:none\">$pag->dataCHEQUE</td>".
                        "<td style=\"display:none\">$valor</td>".                                                                        
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>".
                        "<td style=\"display:none\">$pag->nomeCHEQUE</td>";
                        
      }
      if ($tipo=='BOLETO') {
        if ($pag->nomeBOLETOPAGO!='') {
          $boletoPAGO= "$pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
          $funcao="<img title='Cancelar pagamento do boleto' src='images/cancelarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,2)' />";
        }
        else {
          $boletoPAGO= "<font color=red>NÃO</font>";
          $funcao="<img  title='Pagar boleto' src='images/pagarBOLETO.png' onmousedown='pagarBOLETO($pag->numREG,1)' />";
        } 
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Vencimento: </font></td><td align=right width="60px">_azul'.$pag->dataCHEQUE.'</font></td>'.
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Nº: </font></td><td align=right width="60px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                  '<td>_cinza&nbsp;&nbsp;&nbsp;&nbsp;Pago: </font></td><td align=left width="250px">_azul'.$boletoPAGO.'</font></td>'.
                  "<td>$funcao</td>".
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>".
                        "<td style=\"display:none\">$pag->dataCHEQUE</td>".
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>".
                        "<td style=\"display:none\">$pag->nomeCHEQUE</td>".
                        "<td style=\"display:none\">$pag->cheque</td>";
      }
      if ($tipo=='CARTÃO') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>";                                                                        
      }
      if ($tipo=='DINHEIRO' || $tipo=='INTERNET') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>";                                                                        
      }
      if ($tipo=='VALE') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaCorretor: </font></td><td align=left width="300px">_azul'.$pag->nomeREPRE.' ('.$pag->idREPRESENTANTE.')</font></td>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$pag->idREPRESENTANTE</td>".
                        "<td style=\"display:none\">$pag->nomeREPRE</td>".
                        "<td style=\"display:none\">$valor</td>";                                                                        
      }

      if ($tipo=='VALE CRÉDITO') {
        $detalhes='<table  border=0><tr>'.
                  '<td>_cinzaNº: </font></td><td align=left width="80px">_azul'.$pag->infoCHEQUE.'</font></td>'.
                  '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'.$valor.'</font></td>'.            
                  '</tr></td>'.
                  '</table>';
        $colunasOCULTAS="<td style=\"display:none\">$valor</td>".
                        "<td style=\"display:none\">$pag->infoCHEQUE</td>";
                                                                                                
      }


      $detalhes = str_replace('_cinza', '<font color=gray>', $detalhes);
      $detalhes = str_replace('_azul', '<font style="color:blue;font-size:12px;">', $detalhes);      
                                    
      $lin = "<tr id=\"PGTO_$pag->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onclick="editarPGTO(this.id);" > '.
             "<td align=\"left\" width=\"15%\">$tipo</td>". 
             "<td align=\"left\" width=\"80%\">$detalhes</td>".             
             "<td onmousedown=\"removePGTO('PGTO_$pag->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             $colunasOCULTAS.
             "</tr>";                            
                            
      $tabPGTO .= $lin;       
    }
    mysql_free_result($pags);
  }    
  else {  
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = "$logado[0] ($logado[1])";
        
    $resp=str_replace('vOPERADOR', $logado, $resp);

    $resp=str_replace('@localORIGINAL', '', $resp);
    $resp=str_replace('@local', '', $resp);

    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('@faltaVERIFICAR', '', $resp);

    $resp=str_replace('vVALE', '', $resp);
    $resp=str_replace('vCORRETOR_VALE', '', $resp);
    $resp=str_replace('v_CORRETOR_VALE', '', $resp);
    $resp=str_replace('vVALOR_VALE', '', $resp);

    $resp=str_replace('vDESCONTO_VALE', '', $resp);
    $resp=str_replace('vPAGAR_VALE', '', $resp);

    $resp=str_replace('@estiloOPERADOR_EXCLUIU', 'style="display:none"', $resp);
    $resp=str_replace('@titOPERADOR_EXCLUIU', '', $resp);
    $resp=str_replace('@nomeOPERADOR_EXCLUIU', '', $resp);
  }
  $tabPROP .= '</table>';
  $tabPGTO .= '</table>';  
  $tabVALES .= '</table>';

  if ($bloquearGRAVACAO=='') {
    switch ($acao) { 
      case 'incluirENTREGA':
        $resp=str_replace('TITULO_JANELA', "Novo registro",$resp);
        $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
        $resp=str_replace('readonly', '',$resp);
        break;      
      case 'editarENTREGA':
        $resp=str_replace('TITULO_JANELA', "Registro nº $vlr",$resp);    
        $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
        $resp=str_replace('readonly', '',$resp);
        break;
    }        
  }
  else {
    switch ($acao) {
      case 'editarENTREGA':
        $resp=str_replace('TITULO_JANELA', "Visualizando operação caixa nº $vlr (entrega proposta)",$resp);    
        break;
    }
    $resp=str_replace('texto_botao', $bloquearGRAVACAO, $resp);
  }        
  // acopla tabela de propostas entregues em $resp  
  $resp=str_replace('@tabPROPOSTAS', $tabPROP, $resp);
  $resp=str_replace('@tabPAGAMENTOS', $tabPGTO, $resp);  
  $resp=str_replace('@tabVALES', $tabVALES, $resp);
//  $resp .= '^' . $strCHEQUES;
  $resp .= '^';
  
}

/*****************************************************************************************/
function diasemana($data) {
	$ano =  substr("$data", 0, 4);
	$mes =  substr("$data", 4, 2);
	$dia =  substr("$data", 6, 2);

	$diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

	switch($diasemana) {
		case"0": $diasemana = "Domingo";       break;
		case"1": $diasemana = "Segunda-Feira"; break;
		case"2": $diasemana = "Terça-Feira";   break;
		case"3": $diasemana = "Quarta-Feira";  break;
		case"4": $diasemana = "Quinta-Feira";  break;
		case"5": $diasemana = "Sexta-Feira";   break;
		case"6": $diasemana = "Sábado";        break;
	}

	return "$diasemana";
}



/*****************************************************************************************/
if( $acao=='atualizaTranspostado' ) 
{
	//echo "<pre>"; print_r($_GET); echo "</pre>";
	$txtValorDinheiro = $_GET['txtValorDinheiro'];
	$txtValorCheque = $_GET['txtValorCheque'];
	//$data = date("Y-m-d", strtotime("-1 days"));
	$data = date("Y-m-d");
	
	if(trim($txtValorDinheiro)=="")
		$txtValorDinheiro = 0;
	
	if(trim($txtValorCheque)=="")
		$txtValorCheque = 0;
	
	$sqlDataUltimoTransportado = "
		select 
    		date_format(dataop, '%Y-%m-%d') as dataOPERACAO,
    		date_format(dataop, '%Y-%m-%d') as dataTRANSPORTADO
		from
    		caixa
		where
    		date_format(dataop, '%Y-%m-%d') < '$data'
			order by dataop desc
		limit 1
	";
	
	$rsDataUltimoTransportado = mysql_query($sqlDataUltimoTransportado);
	
	while($rstDataUltimoTransportado = mysql_fetch_array($rsDataUltimoTransportado))
	{
		$dataTranspostado = $rstDataUltimoTransportado['dataTRANSPORTADO'];
	}
	
	$sqlUp = "UPDATE transportado SET saldoCHEQUE = $txtValorCheque, saldoDINHEIRO = $txtValorDinheiro WHERE data = '$dataTranspostado'";
	//echo $sqlUp . "<br />";
	$rsUp = mysql_query($sqlUp);
	//$linhas = mysql_num_rows($rsUp);

	//$linhas = 0;
	
	if($rsUp > 0)
		$resp = "Alterado com sucesso";
	else
		$resp = "Erro ao alterar valores";
}
/*****************************************************************************************/


    
/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>