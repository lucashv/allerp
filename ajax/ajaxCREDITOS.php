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
if ($acao=='senhaEXCLUIR') {
  $sql = 'select nome  '   . 
         "from operadores ". 
         "where senha='$vlr' and numero=1";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) $resp='nao';
  else $resp='ok';
}

/*****************************************************************************************/
IF ( $acao=='lerDataHoje' )  {
  $resp = date("d/m/Y");
}


/*****************************************************************************************/
if ($acao=='alternarTipoCreditoLendo') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  mysql_query("update operadores set tipoCreditoLendo=case when tipoCreditoLendo=1 then 2 else 1 end where numero=$logado[1]") or
     die (mysql_error());

  echo('ok'); die();
}    


 
/*****************************************************************************************/
if ($acao=='excluir') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("delete from creditos_descontos where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  $cmps = explode('|', $_REQUEST['vlr']);
  $numVALECRED = $_REQUEST['numVALECRED'];
  $pago = $_REQUEST['pago']=='true' ? '1' : '0';
  $datapago = $_REQUEST['pago']=='true' ? 'now()' : 'null';
  $oppago = $_REQUEST['pago']=='true' ? $logado[1] : 'null';
  $opCaixaPagamento = $_REQUEST['opCaixaPagamento'];
  $idCONTA = $_REQUEST['idCONTA'];

  $darSAIDACAIXA = $_REQUEST['saida'];
  
  $id = $cmps[5];
  $pagarVALE = $cmps[6];

  $representante=$cmps[3];
  $representante=rtrim($representante)=='' ? 'null': $representante;
  
  if ($id=='') 
    $sql = "insert into creditos_descontos(data,tipo,descricao,representante,valor,operador,pagarVALE) ".
            " values('$cmps[0]', '$cmps[1]', upper('$cmps[2]'), $representante, $cmps[4], $logado[1], '$pagarVALE')";
  
  else {
    // se estamos lidando com vale credito, grava 3 campos a mais
    if ($numVALECRED!='') {
      // se ja gerou em algum momento uma op de caixa para o pagamento do vale credito
      if ($opCaixaPagamento!='') {
        // e se foi cancelado o pagamento do vale credito, elimina aquela operacao de caixa gerada
        if  ($pago=='0')  {
          mysql_query("delete from caixa where numreg=$opCaixaPagamento;") or die ($sql . '<br>' . mysql_error());
          mysql_query("delete from pagamentos where idcaixa=$opCaixaPagamento;") or die ($sql . '<br>' . mysql_error());
          mysql_query("delete from creditos_descontos where idcaixa=$opCaixaPagamento;") or die ($sql . '<br>' . mysql_error());
        }
      }
      // se nunca foi gerada operacao de caixa para o pagamento do vale credito, e se, o vale credito foi pago, gera op do caixa 
      else {
        if ($pago=='1' && $darSAIDACAIXA=='true' ) {
          $sql  = "select ifnull(escritorioATUAL,'A') as escritorioATUAL ".      
                  "from operadores ".
                  "where numero=$logado[1]";
          $resultado = mysql_query($sql, $conexao) or die (mysql_error());
          $row = mysql_fetcH_object($resultado);
          $letraESCRITORIO=$row->escritorioATUAL;

          // acredite se quiser, mas tive que fazer o xunxo abaixo pq quando vc usa ORD()
          // ele retorna INT(78) - se vc fizer qq operacao matematica com INT(78) vai dar merda, resultado sai em branco        
          if ($letraESCRITORIO=='A') $idESCRITORIO=1;  
          if ($letraESCRITORIO=='B') $idESCRITORIO=2;
          if ($letraESCRITORIO=='C') $idESCRITORIO=3;
          if ($letraESCRITORIO=='D') $idESCRITORIO=4;
          if ($letraESCRITORIO=='E') $idESCRITORIO=5;
          if ($letraESCRITORIO=='F') $idESCRITORIO=6;
          if ($letraESCRITORIO=='G') $idESCRITORIO=7;
          if ($letraESCRITORIO=='H') $idESCRITORIO=8;
          if ($letraESCRITORIO=='I') $idESCRITORIO=9;
          if ($letraESCRITORIO=='J') $idESCRITORIO=10;

          $sql = "insert into caixa(entOuSai, dataOP, idOPERACAO, descOPERACAO, valor, opRESPONSAVEL, idFUNCIONARIO, idESCRITORIO) ".
                " values('S', now(), $idCONTA, 'Vale crédito nº $numVALECRED pago', $cmps[4], $logado[1], $cmps[3], $idESCRITORIO)";

//          $sql = "insert into caixa(entOuSai, dataOP, idOPERACAO, descOPERACAO, valor, opRESPONSAVEL, idFUNCIONARIO, idESCRITORIO) ".
//                " values('S', concat('$cmps[0] ', curtime()), $idCONTA, 'Vale crédito nº $numVALECRED pago', $cmps[4], $logado[1], $cmps[3], $idESCRITORIO)";

          mysql_query($sql) or die ($sql . '<br>' . mysql_error());
          $opCaixaPagamento = mysql_insert_id();

          $sql = "insert into pagamentos(tipoPGTO, valor, idCAIXA) ".
                " values('DINHEIRO', $cmps[4], $opCaixaPagamento)";

          mysql_query($sql) or die ($sql . '<br>' . mysql_error());
        }
        else
           $opCaixaPagamento = 'null';
      }
      if ($pago=='0') $opCaixaPagamento='null'; 

      $sql = "update creditos_descontos set data='$cmps[0]',tipo='$cmps[1]',descricao=upper('$cmps[2]'), opCaixaPagamento=$opCaixaPagamento, ".
             "representante=$cmps[3], valor=$cmps[4], pagoVALE_CREDITO=$pago, datapagoVALE_CREDITO=$datapago, oppagoVALE_CREDITO=$oppago, " . 
            " pagarVALE='$pagarVALE' where numero=$id";
    }
    else
      $sql = "update creditos_descontos set data='$cmps[0]',tipo='$cmps[1]',descricao=upper('$cmps[2]'), opCaixaPagamento=null, ".
              "representante=$cmps[3],valor=$cmps[4], pagarVALE='$pagarVALE' " . 
            " where numero=$id";
  }
  mysql_query($sql) or die ($sql . '<br>' . mysql_error());
  
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
  $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy

  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

  $resultado = mysql_query("select ifnull(tipoCreditoLendo, 1) as tipoCreditoLendo from operadores where numero=$logado[1]") or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $tipoCreditoLendo = $row->tipoCreditoLendo;

  $palavra = '';
  if ( strpos($dataTRAB, 'palavra')!==false )  {
    $palavra = str_replace('palavra', '', $dataTRAB);
    // le regs cuja descricao tem a palavra pesquisada
    $sql  = "select cre.descontoVALE, cre.valor, cre.numero, descricao, ifnull(rep.nome, '') as nomeREPRESENTANTE, ".
            " date_format(cre.datapagoVALE_CREDITO, '%d/%m/%y') as datapagoVALE_CREDITO, " .
            " cre.representante as idREPRESENTANTE, date_format(data, '%d/%m/%y') as data, numVALE_CREDITO, pagoVALE_CREDITO, ".
            " ifnull(op.nome, '* erro *') as nomeOPERADOR, operador as idOPERADOR, ucase(cre.tipo) as tipo,  " .
            " date_format(pagarVALE, '%d/%m/%y') as pagarVALE_MOSTRAR ".
            " from creditos_descontos cre ".
            "left join representantes rep ".
            " on rep.numero = cre.representante " .
            "left join operadores op ".
            " on op.numero = cre.operador " .
            " where descricao like '%$palavra%' or numvale_credito like '%$palavra%'  and ifnull(cre.excluido,0)=0 " .          
            "order by data desc " ;
  }
  else {
    $cmpDATA = $tipoCreditoLendo==1 ? 'data' : 'pagarVALE';   
    // le regs do dia atual
    $sql  = "select cre.descontoVALE, cre.valor, cre.numero, descricao, ifnull(rep.nome, '') as nomeREPRESENTANTE, " .
            " cre.representante as idREPRESENTANTE, date_format(data, '%d/%m/%y') as data, numVALE_CREDITO, pagoVALE_CREDITO, ".
            " date_format(cre.datapagoVALE_CREDITO, '%d/%m/%y') as datapagoVALE_CREDITO, " .
            " ifnull(op.nome, '* erro *') as nomeOPERADOR, operador as idOPERADOR, ucase(cre.tipo) as tipo, " .
            " date_format(pagarVALE, '%d/%m/%y') as pagarVALE_MOSTRAR  ".
            " from creditos_descontos cre ".
            "left join representantes rep ".
            " on rep.numero = cre.representante " .
            "left join operadores op ".
            " on op.numero = cre.operador " .
            " where @criterioDATA @criterioTIPO  and ifnull(cre.excluido,0)=0  " .          
            "order by data desc " ;

    $lendoATUAL = $_REQUEST['lendoATUAL'];
    if ($lendoATUAL=='valecreditos') {
      $sql = str_replace('@criterioTIPO', " and ifnull(numVALE_CREDITO,'')<>'' ", $sql); 
      $sql = str_replace('@criterioDATA', " date_format($cmpDATA, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' ", $sql);
    }
    else if ($lendoATUAL=='creditos') {
      $sql = str_replace('@criterioTIPO', " and ifnull(numVALE_CREDITO,'')='' ", $sql);
      $sql = str_replace('@criterioDATA', " date_format($cmpDATA, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' ", $sql);
    }
    else if ($lendoATUAL=='valecreditospendentes') {
      $sql = str_replace('@criterioTIPO', " and ifnull(numVALE_CREDITO,'')<>'' and  ifnull(pagoVALE_CREDITO,0)<>1 ", $sql);
      $sql = str_replace('@criterioDATA', ' 1=1 ', $sql);
    }
    else if ($lendoATUAL=='valecreditos45') {
      $sql = str_replace('@criterioTIPO', " and ifnull(numVALE_CREDITO,'')<>'' ", $sql);
      $sql = str_replace('@criterioDATA', 
        " date_format(data, '%Y%m%d') between date_format(DATE_ADD(now(),INTERVAL -45 DAY), '%Y%m%d') and date_format(now(), '%Y%m%d') ", $sql);
    }
    else if ($lendoATUAL=='creditos45') {
      $sql = str_replace('@criterioTIPO', " and ifnull(numVALE_CREDITO,'')='' ", $sql);
      $sql = str_replace('@criterioDATA', 
        " date_format(data, '%Y%m%d') between date_format(DATE_ADD(now(),INTERVAL -45 DAY), '%Y%m%d') and date_format(now(), '%Y%m%d') ", $sql);
    }
    else {
      $sql = str_replace('@criterioTIPO', '', $sql);
      $sql = str_replace('@criterioDATA', " date_format($cmpDATA, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' ", $sql);
    }
  }
  //echo $sql;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.05;
  $largura3 = $_SESSION['largIFRAME'] * 0.25;  
  $largura4 = $_SESSION['largIFRAME'] * 0.1;
  $largura5 = $_SESSION['largIFRAME'] * 0.20;    
  $largura6 = $_SESSION['largIFRAME'] * 0.05;
    
	$header = "$largura1 px,Data registro|$largura1 px,Data pagar|$largura1 px,Data pgto|$largura2 px,Tipo|$largura3 px,Descrição|$largura4 px,Valor".
            "|$largura5 px,Representante|$largura1 px,Nº vale créd";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  $pagar=0;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4=''; $largura5='';
    }
    $i++;
  
    $vlr =  number_format($row->valor, 2, ',', '')  ;
    $valeCREDITO = ($row->numVALE_CREDITO!='' && $row->numVALE_CREDITO!='0') ? $row->numVALE_CREDITO : '-';

    $descricao = substr($row->descricao, 0, 30);

    $dataPGTO = $row->datapagoVALE_CREDITO=='' ? '-' : $row->datapagoVALE_CREDITO;
    $lin = "<tr @cor ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numero\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"center\" $largura1>$row->data</td>".
            "<td align=\"center\" $largura1>$row->pagarVALE_MOSTRAR</td>".
            "<td align=\"center\" $largura1>$dataPGTO</td>".
            "<td align=\"center\" $largura2>$row->tipo</td>".
            "<td align=\"left\" $largura3>$descricao</td>".
            "<td align=\"right\" $largura4>$vlr&nbsp;&nbsp;</td>".
            "<td align=\"left\" $largura5>$row->nomeREPRESENTANTE ($row->idREPRESENTANTE)</td>".                        
            "<td align=\"left\" $largura2>$valeCREDITO</td>".
            "</tr>";

    if ($row->numVALE_CREDITO!='' && $row->numVALE_CREDITO!='0') {
      if ($row->pagoVALE_CREDITO!='1') {
        $vlrDESCONTADO = $row->valor - $row->valor * ($row->descontoVALE/100);

        $sql = "select sum(pag.valor) as vlrPAGO  ".
               "from pagamentos pag ".
               'left join caixa cx '.
               '    on cx.numreg=idCAIXA '.                 
               "where infoCHEQUE=$row->numVALE_CREDITO and tipoPGTO='VALE CRÉDITO' and ifnull(cx.alterada2_excluida1,0)=0 ";

        $rsPGTOS = mysql_query($sql) or die (mysql_error());
        $vlrPAGO=0;
        if (mysql_num_rows($rsPGTOS)>0) {
          $regPGTO = mysql_fetcH_object($rsPGTOS); 
          $vlrPAGO += $regPGTO->vlrPAGO;
        }
        mysql_free_result($rsPGTOS);
        if ($vlrPAGO==$vlrDESCONTADO)
          $lin = str_replace('@cor', "style='color:blue;font-weight:normal'", $lin);
        else
        $lin = str_replace('@cor', "style='color:blue;font-weight:bold'", $lin);

        $pagar += $vlrDESCONTADO - $vlrPAGO;
      }
      else 
        $lin = str_replace('@cor', "style='color:blue;font-weight:normal'", $lin);
    } 
    else {
      $lin = str_replace('@cor', $row->tipo=='D' ? "style='color:red;'" : "style='color:blue;'", $lin);
    }
    $resp = $resp . ($lin);
  }
  $pagar = number_format($pagar, 2, ',', '')  ;
  if ($palavra != '')
    $resp .= '^'.$qtdeREGS.'^'.'palavra= '.$palavra. '^' . date("dmY").'^'.$pagar;
  else
    $resp .= "^$qtdeREGS-$tipoCreditoLendo-$pagar";
          
}
/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {
  $arq = fopen('credito.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  if ($acao!='incluirREG')   {
    $sql  = "select cre.numero, descricao, ifnull(rep.nome, '') as nomeREPRESENTANTE, " .
            " cre.representante as idREPRESENTANTE, date_format(data, '%d/%m/%y') as data, ".
            " cre.valor, ifnull(op.nome, '* erro *') as nomeOPERADOR, operador as idOPERADOR, cre.tipo, numVALE_CREDITO,  " .
            " date_format(datapagoVALE_CREDITO, '%d/%m/%y') as datapagoVALE_CREDITO, ifnull(op2.nome, '') as operadorVALE_CREDITO, ".
            " cre.oppagoVALE_CREDITO, cre.opCaixaPagamento, date_format(pagarVALE, '%d/%m/%y') as pagarVALE, cre.idCAIXA, ".
            " ifnull(cre.descontoVALE, 0) as descontoVALE, ifnull(pagoVALE_CREDITO,0) as pagoVALE_CREDITO ".
            " from creditos_descontos cre ".
            "left join representantes rep ".
            " on rep.numero = cre.representante " .
            "left join operadores op ".
            " on op.numero = cre.operador " .
            "left join operadores op2 ".
            " on op2.numero = cre.oppagoVALE_CREDITO " .
            " where cre.numero = $vlr  ";          

//die($sql);
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $editavel='';
//    if ( ($row->numVALE_CREDITO!='' && $row->numVALE_CREDITO!='0') || (strpos($row->descricao, 'PROPOSTA(S) PAGA(S)')!==false) )
    if ( $row->idCAIXA!='' )
      $editavel = "<font color=red><b>** Este registro foi criado automaticamente pela operação de caixa Nº ".
            "<span onmouseout=\"this.style.backgroundColor='#F6F7F7';\" onmouseover=\"this.style.backgroundColor='#A9B2CA';\" "  .
            " onclick='opCAIXA($row->idCAIXA)' style='font-size:14px;padding-left:10px;padding-right:10px;cursor:pointer;'>$row->idCAIXA</span>".
            "  e só pode ser alterado por lá **</b></font>";  

    else 
      $resp=str_replace('readonly', '', $resp);      

    $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr<br> ",$resp);    
    $resp=str_replace('@editavel', $editavel,$resp);
    $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);

    $resp=str_replace('@numVALE_CREDITO', $row->numVALE_CREDITO, $resp);
    $resp=str_replace('vN_VALE', $row->numVALE_CREDITO, $resp);
    $resp=str_replace('@opCaixaPagamento', $row->opCaixaPagamento, $resp);

    $valor =  number_format($row->valor, 2, ',', '')  ;
    
    // verifica os pagamentos ja efetuados do vl credito
    $pagamentosFEITOS='-';
    if ($row->numVALE_CREDITO!='') {

      // opCaixaPagamento VAZIO, significa, vale credito nao foi pago em 1 unica parcela
      if ($row->opCaixaPagamento=='') {
        $sql = "select idCAIXA, pag.valor  ".
               "from pagamentos pag ".
               'left join caixa cx '.
               '    on cx.numreg=idCAIXA '.                 
               "where infoCHEQUE=$row->numVALE_CREDITO and tipoPGTO='VALE CRÉDITO' and ifnull(cx.alterada2_excluida1,0)=0 ";
        $rsPAGOS = mysql_query($sql) or die (mysql_error());
    
        $totPAGO=0;
        $pagamentosFEITOS=''; 
        if (mysql_num_rows($rsPAGOS)>0) {
          while ($regPAGO = mysql_fetcH_object($rsPAGOS)) {
            $pagamentosFEITOS .= $pagamentosFEITOS=='' ? '' : ', ';
            $pagamentosFEITOS .= "<span onmouseout=\"this.style.backgroundColor='#FFCCCC';\" onmouseover=\"this.style.backgroundColor='#A9B2CA';\" "  .
              " onclick='opCAIXA($regPAGO->idCAIXA)' style='font-size:14px;padding-left:10px;padding-right:10px;cursor:pointer;'>$regPAGO->idCAIXA</span>";
    
            $totPAGO += $regPAGO->valor;
          }
        }
        mysql_free_result($rsPAGOS);
        if ($totPAGO>0)
          $pagamentosFEITOS .= '&nbsp;&nbsp;&nbsp;(Total pago: '. number_format($totPAGO, 2, ',', '') .')' ;
      }
      // opCaixaPagamento DIFERENTE DE VAZIO, significa, vale credito foi pago em 1 unica parcela
      else {
        $pagamentosFEITOS = "<span onmouseout=\"this.style.backgroundColor='#FFCCCC';\" onmouseover=\"this.style.backgroundColor='#A9B2CA';\" "  .
          " onclick='opCAIXA($row->opCaixaPagamento)' ".
          " style='font-size:14px;padding-left:10px;padding-right:10px;cursor:pointer;'>$row->opCaixaPagamento</span>";
      }
    }
  
    // se vale credito foi explicitamente pago, o usuario clicou na opcao VL CREDITO PAGO  
    if ($row->pagoVALE_CREDITO=='1') {
      $resp=str_replace('vDATA_VALE', $row->datapagoVALE_CREDITO, $resp);
      $resp=str_replace('vPAGO_VALE', "$row->operadorVALE_CREDITO ($row->oppagoVALE_CREDITO)", $resp);
      $resp=str_replace('vOP_PGTO_VALE', "$row->operadorVALE_CREDITO ($row->oppagoVALE_CREDITO)", $resp);
      $resp=str_replace('checkedPAGO', 'checked', $resp);

      $resp=str_replace('vOPERACOES_PGTO', '-', $resp);
      $resp=str_replace('vOPERACAO_UNICA_PGTO', $pagamentosFEITOS, $resp);
    } 
    else {
      $resp=str_replace('vDATA_VALE', '-', $resp);
      $resp=str_replace('vPAGO_VALE', '-', $resp);
      $resp=str_replace('vOP_PGTO_VALE', '-', $resp);
      $resp=str_replace('checkedPAGO', '', $resp);

      $resp=str_replace('vOPERACAO_UNICA_PGTO', '-', $resp);
      $resp=str_replace('vOPERACOES_PGTO', $pagamentosFEITOS, $resp);
    }
    $vlrDESCONTADO = number_format($row->valor - $row->valor * ($row->descontoVALE/100), 2, ',', '') ; 
    $vlrVALE = number_format($row->valor, 2, ',', '') ;
    $resp=str_replace('vDESCONTO', "$row->descontoVALE% &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Valor com desconto: R$ $vlrDESCONTADO)" , $resp);
    $resp=str_replace('vV_VALE', $vlrVALE, $resp);


    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vPAGARDATA', $row->pagarVALE, $resp);
    $resp=str_replace('vTIPO', $row->tipo, $resp);    
    $resp=str_replace('vREPRESENTANTE', $row->idREPRESENTANTE, $resp);
    $resp=str_replace('vNomeREPRESENTANTE', $row->nomeREPRESENTANTE, $resp);    
    $resp=str_replace('vDESCRICAO', $row->descricao, $resp);        
    $resp=str_replace('vVALOR', $valor, $resp);    
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->idOPERADOR)", $resp);    
    $resp=str_replace('@numREG', $vlr, $resp);    
        
  }    
  else {
    $resp=str_replace('readonly', '', $resp);
    $resp=str_replace('TITULO_JANELA', 'Novo Registro',$resp);
    $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
    $resp=str_replace('@editavel', '',$resp);

    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = "$logado[0] ($logado[1])";
        
    $resp=str_replace('vDESCONTO', '-', $resp);
    $resp=str_replace('vOPERACOES_PGTO', '-', $resp);
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('vPAGARDATA', date("d/m/y"), $resp);
    $resp=str_replace('vTIPO', '', $resp);    
    $resp=str_replace('vREPRESENTANTE', '', $resp);
    $resp=str_replace('vNomeREPRESENTANTE', '', $resp);    
    $resp=str_replace('vDESCRICAO', '', $resp);        
    $resp=str_replace('vVALOR', '', $resp);
    $resp=str_replace('vOPERADOR', $logado, $resp);         
    $resp=str_replace('@numREG', '', $resp);   
    $resp=str_replace('@numVALE_CREDITO', '', $resp);
    $resp=str_replace('@opCaixaPagamento', '', $resp);
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


