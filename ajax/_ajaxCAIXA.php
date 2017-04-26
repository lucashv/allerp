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
    if (isset( $_REQUEST['vlr']))    
      mysql_query("update caixa set faltaVERIFICAR='N', dataOP=now() where numreg=$vlr;" ) or die (mysql_error());
  
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

    //$vlrDESCONTADO = number_format($row->valor - $row->valor * ($row->descontoVALE/100), 2, ',', '') ;
    $vlrDESCONTADO = $row->valor - $row->valor * ($row->descontoVALE/100);

    if ($row->idCAIXA==$idCAIXA) $resp='INVALIDA';
    else if ($row->pagoVALE_CREDITO=='1') $resp='PAGO';
    else {
      // vale credito nao pago, verifica os pagamentos ja efetuados dele
      $sql = "select sum(valor) as vlrPAGO   ".
             "from pagamentos  ".
             "where infoCHEQUE=$vlr ";
      $resultado = mysql_query($sql) or die (mysql_error());
      $vlrPAGO='0,00';
      $vlrDISPONIVEL='0,00';
      if (mysql_num_rows($resultado)>0) {
        $row = mysql_fetcH_object($resultado);
        $vlrPAGO = number_format($row->vlrPAGO, 2, ',', '') ;
        $vlrDISPONIVEL = number_format(($vlrDESCONTADO - $row->vlrPAGO), 2, ',', '') ;
      }
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
    if ($cmps[1]!='NAOMUDAR')  
      $sql = "update caixa set temBOLETO=$temBOLETO, entOuSai='$entOUsai', dataOP=concat('$cmps[1] ', curtime()), idOPERACAO=$cmps[3], ".
              "descOPERACAO=upper('$cmps[4]'), valor=0, opRESPONSAVEL=$logado, idFUNCIONARIO=$cmps[2], contabilizarSAIDA=$contabilizarSAIDA,  ".
              " numBOLETO=$numBOLETO, temFormasPgto=$temFormasPgto where numreg=$id";
    else
      $sql = "update caixa set temBOLETO=$temBOLETO, entOuSai='$entOUsai', idOPERACAO=$cmps[3], contabilizarSAIDA=$contabilizarSAIDA,  ".
              "descOPERACAO=upper('$cmps[4]'), valor=0, opRESPONSAVEL=$logado, idFUNCIONARIO=$cmps[2], numBOLETO=$numBOLETO,   ". 
              " temFormasPgto=$temFormasPgto  where numreg=$id";

    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    
    $idCAIXA = $id;
  }

  // se ha algum vale credito nas formas d pgto, marca o vale credito como NAO PAGO inicialmente,
  // caso o operador tenha mantido o vale credito, o codigo abaixo vai reconsierar e pagar
  $sql = "select tipoPGTO, infoCHEQUE   ".
         "from pagamentos  ".
         "where idCAIXA=$idCAIXA ";
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0) {
    while ($row = mysql_fetcH_object($resultado)) {
      if ($row->tipoPGTO='VALE CRÉDITO')  
        mysql_query("update creditos_descontos set pagoVALE_CREDITO=0, datapagoVALE_CREDITO=null, oppagoVALE_CREDITO=null " . 
                    "where numVALE_CREDITO=$row->infoCHEQUE") or die( 'reg pgto vale credito' . '<br>'.mysql_error() );
    }
  }


  $sql = "delete from pagamentos where idCAIXA=$idCAIXA ";
  mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

  $sql="delete from creditos_descontos where idCAIXA=$idCAIXA and tipo='D' ;";
  mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  
  $dataOPERACAO=$cmps[8];

  // pagamentos
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
    else if ($tipoPGTO=='VALE') {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'D', concat('PROPOSTA(S) PAGA(S) COM VALE'), $pgto[1], $pgto[2], $logado, $idCAIXA, null, null, '$dataOPERACAO'";

      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
 
      $sql="insert into pagamentos(tipoPGTO, idREPRESENTANTE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";

    }
    else if ($tipoPGTO=='VALE CRÉDITO') { 
      // le valor total do vale credito
      $sql="select descontoVALE, valor from creditos_descontos where numVALE_CREDITO=$pgto[1] ";
      $resultado = mysql_query($sql) or die (mysql_error());
      $vlrDISPONIVEL=0;
      if (mysql_num_rows($resultado)>0) {
        $row = mysql_fetcH_object($resultado);
        $vlrDESCONTADO = $row->valor - $row->valor * ($row->descontoVALE/100);
      }

      // le quanto do vale credito ja foi pago, se pagou tudo, registra como PAGO oficialmente
      $sql = "select sum(valor) as vlrPAGO   ".
             "from pagamentos  ".
             "where infoCHEQUE=$pgto[1] ";
      $resultado = mysql_query($sql) or die (mysql_error());
      $vlrPAGO=0;
      if (mysql_num_rows($resultado)>0) {
        $row = mysql_fetcH_object($resultado);
        $vlrPAGO = $row->vlrPAGO;
      }

      $vlrPAGO += $pgto[2];

      // se valor pago = valor do vale, registra como pago
      if ($vlrDESCONTADO - $vlrPAGO< 0.1)
        // registra que vale credito foi pago
        mysql_query("update creditos_descontos set pagoVALE_CREDITO=1, datapagoVALE_CREDITO='$dataOPERACAO', oppagoVALE_CREDITO=$logado " . 
                    "where numVALE_CREDITO=$pgto[1]") or die( 'reg pgto vale credito' . '<br>'.mysql_error() );      
      else
        mysql_query("update creditos_descontos set pagoVALE_CREDITO=0, datapagoVALE_CREDITO=null, oppagoVALE_CREDITO=null " . 
                    "where numVALE_CREDITO=$pgto[1]") or die( 'reg pgto vale credito' . '<br>'.mysql_error() );

      $sql="insert into pagamentos(tipoPGTO, infoCHEQUE, valor, idCAIXA) ".
            " select '$tipoPGTO', '$pgto[1]', $pgto[2], $idCAIXA";
    }
    if ($sql!='')    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }

  $dataOPERACAO=$cmps[8];
  $infoVALE=$cmps[7];
  $idOPERACAO=$cmps[3];

  if ($infoVALE=='') {
    $sql="delete from creditos_descontos where idCAIXA=$idCAIXA and tipo='C' ;";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }
  // se ha informacao de vale creditos para gravar, insere ou atualiza
  else {
    $vale = explode(';', $infoVALE);

    $resultado = mysql_query("select numero, data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE ".
                            " from creditos_descontos where idCAIXA=$idCAIXA and tipo='C' ") or die (mysql_error());
    if (mysql_num_rows($resultado)>0) {  
      $sql="update creditos_descontos set representante=$vale[1], valor=$vale[2], ".  
            "operador=$logado, numVALE_CREDITO=$vale[0], descontoVALE=$vale[3], pagarVALE='$vale[4]' where idCAIXA=$idCAIXA and tipo='C' ";
    }
    else {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'C', concat('VALE CRÉDITO Nº ',$vale[0]),  $vale[1], $vale[2], $logado, $idCAIXA, $vale[0], $vale[3], '$vale[4]'";
    }
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
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
  mysql_query("update caixa set excluida=1 where numreg=$vlr" ) or die (mysql_error());
  mysql_query("update creditos_descontos set excluido=1 where idCAIXA=$vlr" ) or die (mysql_error());

  // dezfaz o vinculo entre cadastro - caixa das propostyas entregues na operacao atual */ 
  $sql = "update propostas set numregPropostaEntregueCaixa=null ".
        " where numregPropostaEntregueCaixa in (select numreg from entregaspropostas where idCAIXA=$vlr) ";
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
  
//  if count($cmps)<5 die('mtoGRANDE');

  $info= explode('|', $cmps[0]);
  $props= explode('|', $cmps[1]);
//  $pgtos= explode('|', $cmps[2]);

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
      $sql = "update propostas set numregPropostaEntregueCaixa=null ".
            " where numregPropostaEntregueCaixa in (select numreg from entregaspropostas where idCAIXA=$idCAIXA) ";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

      // apaga as entregas de proposta e insere mais abaixo, com isso desfaz definitivamente o vinculo CAIXA -> CADASTRO
      $sql = "delete from entregaspropostas where idCAIXA=$idCAIXA ";
      mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
    }
    
    // registra vales creditos atuais como EM ABERTO, NAO PAGOS
    $sql="select infoCHEQUE as numVALE_CREDITO from pagamentos where tipoPGTO='VALE CRÉDITO' and idCAIXA=$idCAIXA";
    $vales = mysql_query($sql, $conexao) or die (mysql_error());
    while ( $vale = mysql_fetcH_object($vales) ) {    
      mysql_query("update creditos_descontos set pagoVALE_CREDITO=0 where numVALE_CREDITO=$vale->numVALE_CREDITO") or 
                die( 'voltar reg pgto vale credito' . '<br>'.mysql_error() );
    }
    mysql_free_result($vales);

    $sql = "delete from pagamentos where idCAIXA=$idCAIXA ";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );

    $sql="delete from creditos_descontos where idCAIXA=$idCAIXA and tipo='D' ;";
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

  // pagamentos
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
    else if ($tipoPGTO=='DINHEIRO') 
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

      // registra que vale credito foi pago
      mysql_query("update creditos_descontos set pagoVALE_CREDITO=1, datapagoVALE_CREDITO='$dataOPERACAO', oppagoVALE_CREDITO=$logado " . 
                  "where numVALE_CREDITO=$pgto[1]") or die( 'reg pgto vale credito' . '<br>'.mysql_error() );      
    }

  
    if ($sql!='')    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }


  if ($infoVALE=='') {
    $sql="delete from creditos_descontos where idCAIXA=$idCAIXA and tipo='C' ";
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
  }
  // se ha informacao de vale creditos para gravar, grava
  else {
    $vale = explode(';', $infoVALE);

    $resultado = mysql_query("select numero, data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE ".
                            " from creditos_descontos where idCAIXA=$idCAIXA and tipo='C'") or die (mysql_error());
    if (mysql_num_rows($resultado)>0) {  
      $sql="update creditos_descontos set representante=$vale[1], valor=$vale[2], ".  
            "operador=$logado, numVALE_CREDITO=$vale[0], descontoVALE=$vale[3], pagarVALE='$vale[4]' where idCAIXA=$idCAIXA and tipo='C'";
    }
    else {
      $sql="insert into creditos_descontos(data, tipo, descricao, representante, valor,operador, idCAIXA, numVALE_CREDITO, descontoVALE, pagarVALE) ".
            " select '$dataOPERACAO', 'C', concat('VALE CRÉDITO Nº ',$vale[0]),  $vale[1], $vale[2], $logado, $idCAIXA, $vale[0], $vale[3], '$vale[4]'";
    }
    mysql_query($sql) or die( $sql . '<br>'.mysql_error() );
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
  
  switch ($acao) { 
    case 'incluirCAIXA':
      $resp=str_replace('TITULO_JANELA', "Incluir operação no caixa",$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarCAIXA':
      $idREAL = $vlr;
      $idREAL = str_replace('rec_', '', $idREAL);
      $idREAL = str_replace('ent_', '', $idREAL);
    
      $resp=str_replace('TITULO_JANELA', "Editar operação do caixa nº $idREAL  ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }


  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "100%,Pagamento" ).'</table>', $resp);
  $resp=str_replace('@altDivPAGAMENTOS', '220px', $resp);

  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        
  if ( $acao=='editarCAIXA'  ) {
    $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, idFUNCIONARIO, " .
            "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO, valor, cx.faltaVERIFICAR, ".
            "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO,  ".
            "ifnull(repre.nome, '* ERRO *') as nomeREPRE, cx.contabilizarSAIDA ".
            "from caixa cx ".                          
            "left join operadores ope ".                        
            "   on ope.numero=cx.opRESPONSAVEL ".
            "left join contas pl " .
            " 		on pl.numero = cx.idOPERACAO " .
            "left join representantes repre " .
            " 		on repre.numero = cx.idFUNCIONARIO " .
            "where cx.numreg=$vlr";
       
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);
                  
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


    // vale credito
    $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, " .
            "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO,  ".
            "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO, vale.numVALE_CREDITO as numVALE, repre.nome as nomerepreVALE,   ".
            " vale.valor as valorVALE, vale.representante as repreVALE, vale.descontoVALE, date_format(vale.pagarVALE, '%d/%m/%y') as pagarVALE ".
            "from caixa cx ".                          
            "left join operadores ope ".                        
            "   on ope.numero=cx.opRESPONSAVEL ".
            "left join creditos_descontos vale ".                        
            "   on vale.idCAIXA = cx.numreg and tipo='C' ".
            "left join representantes repre ".                        
            "   on repre.numero = vale.representante ".
            "left join contas pl " .
            " 		on pl.numero = cx.idOPERACAO " .
            "where cx.numreg=$vlr";
                
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);

    $resp=str_replace('vVALE', $row->numVALE, $resp);
    $resp=str_replace('vCORRETOR_VALE', $row->repreVALE, $resp);
    $resp=str_replace('v_CORRETOR_VALE', $row->nomerepreVALE, $resp);
    $resp=str_replace('vVLR_VALE', number_format($row->valorVALE, 2, ',', ''), $resp);

    $resp=str_replace('vDESCONTO_VALE', $row->descontoVALE, $resp);
    $resp=str_replace('vPAGAR_VALE', $row->pagarVALE, $resp);
                  
    $resp=str_replace('@numREG', $vlr , $resp);
    $idCAIXA = $row->idCAIXA;
      

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
        
    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('@faltaVERIFICAR', '', $resp);
  }

  $tabPGTO .= '</table>';
  
  $resp=str_replace('@tabPAGAMENTOS', $tabPGTO, $resp);  
//  $resp .= '^' . $strCHEQUES;
  $resp .= '^';


}

/*****************************************************************************************/
IF ( $acao=='addVALE_CREDITO' ) {
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
IF ( $acao=='addVALE' ) {
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
IF ( $acao=='addCARTAO' ) {
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
IF ( $acao=='addDINHEIRO' ) {
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
IF ( $acao=='addCHEQUE' ) {
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
IF ( $acao=='addBOLETO' ) {
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
IF ( $acao=='lerDataHoje' )  {
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
          " ep.vlrRECEBIDO, ep.cpf, cx.valor, ep.numreg as idENTREGA, ep.idREPRESENTANTE, cx.idFUNCIONARIO, cx.idOPERACAO, cred.numVALE_CREDITO,  ".            
          " plano.entOUsai, ifnull(cx.totDINHEIRO,0) as totDINHEIRO, ifnull(totCHEQUE, 0) as totCHEQUE, cx.contabilizarSAIDA, cx.temFormasPgto ".
          "from caixa cx " .
          "left join entregaspropostas ep  " .
          " 	on cx.numreg = ep.idCAIXA  " .
          "left join contas plano  " .
          "	  on plano.numero = cx.idOPERACAO " .
          "left join creditos_descontos cred  " .
          "	  on cred.idCAIXA = cx.numreg and tipo='C' " .
          "left join pagamentos pag ".
          "   on pag.tipoPGTO='VALE CRÉDITO' and pag.idCAIXA=cx.numreg ".
          "left join tipos_contrato tipoprop " .
          "	  on tipoprop.numreg = ep.idTIPO_CONTRATO    " .
          "  where ifnull(excluida,0)<>1 ".
          ($idESCRITORIO!=9999 ? " and cx.idESCRITORIO=$idESCRITORIO " : ' and 1=1 ');
  if ($cmpPESQUISA=='')
    $sql .= " and date_format(cx.dataop, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' @criterioTIPOCAIXA order by cx.dataOP desc ";
  else {
    // cpf    
    if ($cmpPESQUISA=='1')  {$cmpPESQUISA='CPF/CNPJ'; 
      $sql .= " and replace(replace(replace(cpf, '.',''), '-',''),'/','')='$infoPESQUISA' ";}  
    if ($cmpPESQUISA=='2')  {$cmpPESQUISA='Nº VALE CRÉDITO'; $sql .= " and cred.numVALE_CREDITO=$infoPESQUISA or pag.infoCHEQUE='$infoPESQUISA' ";}  
    if ($cmpPESQUISA=='3')  {$cmpPESQUISA='Nº OPERAÇÃO DO CAIXA'; $sql .= " and cx.numreg=$infoPESQUISA ";}
    if ($cmpPESQUISA=='4')  {$cmpPESQUISA='Nº BOLETO'; $sql .= " and cx.numBOLETO='$infoPESQUISA' ";}
  }
  
  // filtra operacoes do cx interno, se usuario é limnitado
  $sql=str_replace('@criterioTIPOCAIXA', (($tipoCAIXA=='I') ? " and plano.tipoCAIXA='I' " : ''), $sql);
//die($sql);

  // se listando caixa de determinada data, procura um dia anterior a esta data em que haja alguma operacao de caixa
  // se encontrando, soma cheques, DH e grava como TRANSPORTADO
  $dataTRANSPORTADO='NAO CALC'; 
  $TRANSP_totSaiCHEQUE=0; $TRANSP_totEntCHEQUE=0; $TRANSP_totSaiDINHEIRO=0; $TRANSP_totEntDINHEIRO=0;
  $TRANSP_saldoDINHEIRO = 0; 
  $TRANSP_saldoCHEQUE = 0;      

  if ($cmpPESQUISA=='') {
    $sqlANT="select date_format(dataop, '%Y%m%d') as dataOPERACAO, date_format(dataop, '%d/%m/%y') as dataTRANSPORTADO ".
            "from caixa ".
            " where date_format(dataop, '%Y%m%d')<'$dataTRAB' order by dataop desc limit 1";
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
                    "where (pag.tipopgto='CHEQUE' or pag.tipopgto='DINHEIRO') and ifnull(cx.excluida,0)=0 and cx.idESCRITORIO=$idESCRITORIO ".
                    "and date_format(dataop, '%Y%m%d') between '$regANT->dataOPERACAO' and '$regANT->dataOPERACAO'    ";

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


      $saldoTRANSP_DINHEIRO = $TRANSP_totEntDINHEIRO-$TRANSP_totSaiDINHEIRO ;
      $saldoTRANSP_CHEQUE = $TRANSP_totEntCHEQUE-$TRANSP_totSaiCHEQUE;



      // alem de fazer a somatoria, le qual era o "transportado" na 1a data anterior à data atual
      $sqlANT="select saldoCHEQUE, saldoDINHEIRO from transportado where data='$regANT->dataOPERACAO' and idESCRITORIO=$idESCRITORIO;";
      $rsTRANSP = mysql_query($sqlANT, $conexao) or die ($sqlANT.'<br><br>'.mysql_error());

      if ( mysql_num_rows($rsTRANSP) > 0 ) {
        $regTRANSP = mysql_fetcH_object($rsTRANSP);

        $TRANSP_saldoDINHEIRO = $regTRANSP->saldoDINHEIRO;
        $TRANSP_saldoCHEQUE = $regTRANSP->saldoCHEQUE;
      }
      mysql_free_result($rsTRANSP);


      
    }
    mysql_free_result($rsANT);
  }
              
  $resultado = mysql_query($sql, $conexao) or die ($sql.'<br><br>'.mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;   
  $largura2 = $_SESSION['largIFRAME'] * 0.2;   
  $largura3 = $_SESSION['largIFRAME'] * 0.15;     
  $largura4 = $_SESSION['largIFRAME'] * 0.25;
    
	$header = "$largura1 px,Data|$largura3 px,Envolvido&nbsp;&nbsp;&nbsp;|$largura2 px,Tipo contrato|".
            "$largura4 px,Conta/Descrição|$largura1 px,CPF/CNPJ|$largura1 px,Cadastrada?|$largura1 px,&nbsp;";
   
  $resp = tabelaPADRAO('width="98%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="99%" cellpadding=\"3\"  cellspacing=\"0\" style="font-family:verdana;font-size:10px;color:black;">';									 
  
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
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4='';
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

    }else {
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
   
    $lin = "<tr title='@title' @corBACK @corFORE ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$idUNICO\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;$row->dataOPERACAO</td>".
            "<td align=\"left\" $largura3>&nbsp;$envolvido </td>".
            "<td align=\"left\" $largura2>$contrato</td>". 
            "<td align=\"left\" $largura4>$infoCONTA</td>".
            "<td align=\"left\" $largura1>$row->cpf</td>".                        
            "<td align=\"center\" $largura1>$cadastrada</td>".
            "<td align=\"center\" $largura1>$pendente</td>".
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



  // grava o "tranportado" da data atual, mas somente o faz, se a data atual tem algum saldo em dinheiro ou cheque
  $saldoDINHEIRO = $totEntDINHEIRO-$totSaiDINHEIRO;
  $saldoCHEQUE = $totEntCHEQUE-$totSaiCHEQUE;

  $saldoTRANSP_DINHEIRO = $TRANSP_saldoDINHEIRO + $TRANSP_totEntDINHEIRO-$TRANSP_totSaiDINHEIRO;
  $saldoTRANSP_CHEQUE = $TRANSP_saldoCHEQUE + $TRANSP_totEntCHEQUE-$TRANSP_totSaiCHEQUE;

//  if ($saldoCHEQUE!=0 || $saldoDINHEIRO!=0) {
  if ($cmpPESQUISA=='') {
    $sqlATUAL="select numreg from transportado where date_format(data, '%Y%m%d')='$dataTRAB' and idESCRITORIO=$idESCRITORIO ";
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




  $descCAIXA=($tipoCAIXA=='I') ? 'Caixa interno' : 'Caixa interno/caixa geral';
  $saldoDINHEIRO = number_format($saldoDINHEIRO + $saldoTRANSP_DINHEIRO, 2, ',', '');
  $saldoCHEQUE = number_format($saldoCHEQUE + $saldoTRANSP_CHEQUE, 2, ',', '');

  $saldoTRANSP_DINHEIRO = number_format($TRANSP_saldoDINHEIRO + $TRANSP_totEntDINHEIRO-$TRANSP_totSaiDINHEIRO , 2, ',', '');
  $saldoTRANSP_CHEQUE = number_format($TRANSP_saldoCHEQUE + $TRANSP_totEntCHEQUE-$TRANSP_totSaiCHEQUE, 2, ',', '');

  $totEntDINHEIRO = number_format($totEntDINHEIRO , 2, ',', '');
  $totEntCHEQUE = number_format($totEntCHEQUE, 2, ',', '');
  $totSaiDINHEIRO = number_format($totSaiDINHEIRO , 2, ',', '');
  $totSaiCHEQUE = number_format($totSaiCHEQUE, 2, ',', '');

  $diaSEMANA=diasemana(date($dataTRAB));
  if ($cmpPESQUISA=='')
    $resp .= "^$qtdeREGS^^$descCAIXA^$totEntDINHEIRO^$totEntCHEQUE^$nomeESCRITORIO^$idESCRITORIO^$totSaiDINHEIRO^$totSaiCHEQUE^".
                  "$saldoDINHEIRO^$saldoCHEQUE^$saldoTRANSP_DINHEIRO^$saldoTRANSP_CHEQUE^$dataTRANSPORTADO^$diaSEMANA";
  else
    $resp .= "^$qtdeREGS^$cmpPESQUISA=$infoPESQUISA^$descCAIXA^$totEntDINHEIRO^$totEntCHEQUE^$nomeESCRITORIO^$idESCRITORIO^".
              "$totSaiDINHEIRO^$totSaiCHEQUE^$saldoDINHEIRO^$saldoCHEQUE^$saldoTRANSP_DINHEIRO^$saldoTRANSP_CHEQUE^$dataTRANSPORTADO^$diaSEMANA"; 
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
  
  switch ($acao) { 
    case 'incluirENTREGA':
      $resp=str_replace('TITULO_JANELA', "Nova entrega de proposta",$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarENTREGA':
      $idREAL = $vlr;
      $idREAL = str_replace('rec_', '', $idREAL);
      $idREAL = str_replace('ent_', '', $idREAL);
    
      $resp=str_replace('TITULO_JANELA', "Editar operação caixa nº $vlr (entrega proposta)",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  $resp = str_replace('@titPROPOSTAS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Corretor|20%,Tipo contrato|10%,CPF/CNPJ|10%,Valor|15%,&nbsp;&nbsp;&nbsp;&nbsp;Valor recebido|15%,Vlr AllCross|10%,Adesão" ).'</table>', $resp);
  $resp=str_replace('@altDivPROPOSTAS', '150px', $resp);
  
  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "100%,Pagamento" ).'</table>', $resp);
  $resp=str_replace('@altDivPAGAMENTOS', '80px', $resp);
  
  $tabPROP = '<table width="99%" id="tabPROPOSTAS" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        
  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        

  if ($acao!='incluirENTREGA')   {            
    $sql  = "select date_format(cx.dataOP, '%d/%m/%y') as data, opRESPONSAVEL, cx.numREG as idCAIXA, " .
            "ifnull(ope.nome, '* ERRO *') as nomeOPERADOR, idOPERACAO,  cx.faltaVERIFICAR, ".
            "ifnull(pl.nome, '* ERRO *') as descCONTA, descOPERACAO, vale.numVALE_CREDITO as numVALE, repre.nome as nomerepreVALE,   ".
            " vale.valor as valorVALE, vale.representante as repreVALE, vale.descontoVALE, date_format(vale.pagarVALE, '%d/%m/%y') as pagarVALE ".
            "from caixa cx ".                          
            "left join operadores ope ".                        
            "   on ope.numero=cx.opRESPONSAVEL ".
            "left join creditos_descontos vale ".                        
            "   on vale.idCAIXA = cx.numreg and tipo='C' ".
            "left join representantes repre ".                        
            "   on repre.numero = vale.representante ".
            "left join contas pl " .
            " 		on pl.numero = cx.idOPERACAO " .
            "where cx.numreg=$vlr";
                
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->opRESPONSAVEL)", $resp);

    $resp=str_replace('vVALE', $row->numVALE, $resp);
    $resp=str_replace('vCORRETOR_VALE', $row->repreVALE, $resp);
    $resp=str_replace('v_CORRETOR_VALE', $row->nomerepreVALE, $resp);
    $resp=str_replace('vVALOR_VALE', number_format($row->valorVALE, 2, ',', ''), $resp);

    $resp=str_replace('vDESCONTO_VALE', $row->descontoVALE, $resp);
    $resp=str_replace('vPAGAR_VALE', $row->pagarVALE, $resp);
                  
    $resp=str_replace('@numREG', $vlr , $resp);
    $resp=str_replace('@faltaVERIFICAR', $row->faltaVERIFICAR, $resp);
    $idCAIXA = $row->idCAIXA;
      
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
      
      $lin = "<tr id=\"PROP_$proposta->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
              ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
              ' onmousedown="editarPROPOSTA(this.id);" > '.
             "<td align=\"left\" width=\"20%\">$proposta->nomeREPRESENTANTE ($proposta->idREPRESENTANTE)</td>". 
             "<td align=\"left\" width=\"20%\">$proposta->descTIPO_CONTRATO ($proposta->idTIPO_CONTRATO)</td>".
             "<td align=\"left\" width=\"10%\">$proposta->cpf</td>".
             "<td align=\"right\" width=\"10%\">$valor</td>".             
             "<td align=\"right\" width=\"15%\">$vlrRECEBIDO</td>".
             "<td align=\"right\" width=\"15%\">$vlrPRESTADORA ($percPRESTADORA%)</td>".
             "<td align=\"right\" width=\"10%\">$vlrADESAO</td>".                                       
             "<td onmousedown=\"removePROPOSTA('PROP_$proposta->numREG')\"  width=\"5%\" align=\"center\" >".
                            '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
             "<td style=\"display:none\">$proposta->idREPRESENTANTE</td>".
             "<td style=\"display:none\">$proposta->idTIPO_CONTRATO</td>".               
             "<td style=\"display:none\">$proposta->nomeREPRESENTANTE</td>".
             "<td style=\"display:none\">$proposta->descTIPO_CONTRATO</td>".                  
             "<td style=\"display:none\">$proposta->numREG</td>".
             '</tr>';               
             
      $tabPROP .= $lin;       
    }
    mysql_free_result($propostas);
    

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
        
    $resp=str_replace('vOPERADOR', $logado, $resp);
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('@faltaVERIFICAR', '', $resp);

    $resp=str_replace('vVALE', '', $resp);
    $resp=str_replace('vCORRETOR_VALE', '', $resp);
    $resp=str_replace('v_CORRETOR_VALE', '', $resp);
    $resp=str_replace('vVALOR_VALE', '', $resp);

    $resp=str_replace('vDESCONTO_VALE', '', $resp);
    $resp=str_replace('vPAGAR_VALE', '', $resp);

  }

  $tabPROP .= '</table>';
  $tabPGTO .= '</table>';  

  // acopla tabela de propostas entregues em $resp  
  $resp=str_replace('@tabPROPOSTAS', $tabPROP, $resp);
  $resp=str_replace('@tabPAGAMENTOS', $tabPGTO, $resp);  
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
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


