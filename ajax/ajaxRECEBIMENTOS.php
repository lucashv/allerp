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
  
  $resp = str_replace('@titCheques',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "10%,Cheque|40%,Banco|20%,Data|25%,Valor (R$)|5%,&nbsp;" ).'</table>', $resp);
  $resp=str_replace('@altDivCheques', '60px', $resp);
  
  $tabCH = '<table id="tabCHEQUES" width="99%" cellpadding=3  cellspacing=0 ".
        "style="font-family:verdana;font-size:10px;color:black;">';  
  

  if ( $acao=='editarREG'  ) {
    $sql = "select numOP, date_format(cx.dataOP, '%d/%m/%y') as data, " .
           "  ifnull(pl.descricao, '* erro *') as operacao, descOP,  " .
           " ifnull(func.nome' '') as nomeFUNCIONARIO ".
           "from caixa cx " .              
           "left join planocontas pl " .
           " 		on pl.codigo = cx.codOP  " .
           "left join operadores func " .
           " 		on func.numero = cx.idFUNCIONARIO " .
           "where numOP=$vlr";
       
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
  
    $resp=str_replace('vDATA', $row->data, $resp);
    $resp=str_replace('vCONTA', $row->codOP, $resp);
    $resp=str_replace('vdescCONTA', $row->operacao, $resp);
    $resp=str_replace('vTIPO', "$row->tipoOPERACAO, $row->nivelOPERACAO", $resp);    
    $resp=str_replace('vPROPOSTA', $row->proposta, $resp);    
    $resp=str_replace('vDESCRICAO', $row->descOP, $resp);    
    $resp=str_replace('vVALOR', number_format($row->valor, 2, ',', ''), $resp);    
        
    $resp=str_replace('@numREG', $vlr, $resp);
        

    // le cheques 
    $sql = "select ch.numCHEQUE, date_format(ch.data, '%d/%m/%y') as data, ch.valor, ch.proposta, ch.opCAIXA, " .
            " ifnull(ban.nome, '* erro *') as nomeBANCO, banco as idBANCO " .
            'from cheques ch  '.
            "left join bancos ban ".
            "   on ban.numero = ch.banco ".
            "where opCAIXA = $row->numOP ";
    

    $cheques = mysql_query($sql) or die (mysql_error());
    $cont = 1;
    
    $infoCORES = explode(',', $_SESSION['cores']);
    $corFORM = $infoCORES[0]; 
      
    while ($cheque = mysql_fetcH_object($cheques) )  {
    
      $valor =  number_format($cheque->valor, 2, ',', '')  ; 
      
      $tabCH .= "<tr id='CH_$cont' onmouseout=\"this.style.backgroundColor='$corFORM';\" " . 
                ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" >'.
              "<td width='10%' align='right'>$cheque->numCHEQUE</td>".
              "<td width='40%' align='left'>&nbsp;&nbsp;&nbsp;$cheque->nomeBANCO ($cheque->idBANCO)</td>".
              "<td align='center' width='20%' >$cheque->data</td>".
             "<td width='25%' align='right'>$valor</td>".
             "<td style='display:none;' >$cheque->idBANCO</td>".
             "<td onmousedown=\"removeCHEQUE('CH_$cont')\"  width='5%' align='center' >".
                              "<font color='red' >X</font></td>".
             "</tr>";
    }
    mysql_free_result($cheques);
  }
  else {  
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('vCONTA', '', $resp);
    $resp=str_replace('vdescCONTA', '', $resp);

    $resp=str_replace('vOPERADOR', '', $resp);
    $resp=str_replace('v_OPERADOR', '', $resp);
    
    $resp=str_replace('vTIPO', '', $resp);    
    $resp=str_replace('vPROPOSTA', '', $resp);    
    $resp=str_replace('vDESCRICAO', '', $resp);
    $resp=str_replace('vVALOR', '', $resp);        
        
    $resp=str_replace('@numREG', '', $resp);
    
  }

  $tabCH .= '</table>';
  $resp=str_replace('@tabCheques', $tabCH, $resp);    
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

  // verifica se repre tem comissao especifica para ele
  // ou se ele usa comissao do grupo de vendas
  $sql  = "select ifnull(rep.idTIPO_COMISSAO, 0) as idTIPO_COMISSAO_REPRE, ifnull(grp.idCOMISSAO, 0) as idTIPO_COMISSAO_GRUPO, ".
          " ifnull(interno_externo, 1) as interno_externo   ".
          " from representantes rep ".
          " left join grupos_venda grp ".
          "     on grp.numreg=rep.idGRUPO ".
          " left join tipos_comissao tipcom ".
          "     on tipcom.numreg=grp.idCOMISSAO ".                   
          "  where rep.numero=$idREPRE";
          
	$resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetch_object($resultado);
  
  $idTIPO_COMISSAO= $row->idTIPO_COMISSAO_REPRE=='0' ? $row->idTIPO_COMISSAO_GRUPO : $row->idTIPO_COMISSAO_REPRE;
  $tipoREPRE= ($row->interno_externo==1) ? 1 : 2;  
  mysql_free_result($resultado); 

  // le comissao adesao do tipo comissionamento identificado
  $sql  = "select ifnull(adesao, 0) as comiADESAO ".
          "from comissoes_representante ".
          " where idCOMISSAo=$idTIPO_COMISSAO and idPRODUTO=$idPROD and interno_externo=$tipoREPRE ";
	$resultado = mysql_query($sql, $conexao) or die (mysql_error());
	$resp='0';
  if (mysql_num_rows($resultado)>0) {  
    $row = mysql_fetch_object($resultado);
    $resp = $row->comiADESAO;
  }  
}    

/*****************************************************************************************/
if ($acao=='excluirENT') {
  $id = $_REQUEST['vlr'];
  $id = str_replace('ent_', '', $id);
  
  $rsPROPS = mysql_query("select numprop from propostasentregues where nument=$id ", $conexao) or die (mysql_error());
 	
  if (mysql_num_rows($rsPROPS)>0)  {
    while ($operacao = mysql_fetch_object($rsPROPS)) {
      $rsCAIXA = mysql_query("select numop from caixa where proposta=$operacao->numprop and codop=101", $conexao) or die (mysql_error());
      if ( mysql_num_rows($rsCAIXA)>0 ) {
        $regCAIXA = mysql_fetch_object($rsCAIXA);
        
        $opCAIXA = $regCAIXA->numop;
        mysql_query("delete from cheques where opcaixa=$opCAIXA") or  die (mysql_error());
        mysql_query("delete from caixa where numop=$opCAIXA") or die( mysql_error() );
      }
      mysql_free_result( $rsCAIXA );
        
    }
  }      
  
  mysql_free_result( $rsPROPS );
  mysql_query("delete from entregaspropostas where numero=$id") or  die (mysql_error());
  mysql_query("delete from creditos_descontos where numero in (select numreg_debito from propostasentregues where numENT=$id)") or die( mysql_error() );  
  mysql_query("delete from propostasentregues where nument=$id") or  die (mysql_error());
    

  die('ok'); 
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
IF ($acao=='lerENTREGAS') {
  $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy
  
  $sql  = "select cx.numreg as idOP,  date_format(cx.dataOP, '%d/%m/%y') as dataOPERACAO, " .
          "ifnull(func.nome, '* erro *') as nomeFUNCIONARIO, cx.idFUNCIONARIO,  " .
          "ifnull(repre.nome, '* erro *') as nomeREPRESENTANTE, ep.idREPRESENTANTE,  " .
          "ifnull(plano.descricao, '* erro *') as descCONTA, cx.descOPERACAO,  " .            
          "ifnull(tipoprop.descricao, '* erro *') as descTIPO_CONTRATO,  " .
          " ep.vlrRECEBIDO, ep.cpf, cx.valor ".            
          "from caixa cx " .
          "left join entregaspropostas ep  " .
          " 	on ep.numreg = cx.idENTREGA  " .
          "left join representantes repre  " .
          "	  on ep.idREPRESENTANTE = repre.numero " .
          "left join operadores func  " .
          "	  on func.numero = cx.idFUNCIONARIO " .
          "left join planocontas plano  " .
          "	  on plano.numreg = cx.idOPERACAO " .
          "left join tipos_contrato tipoprop " .
          "	  on tipoprop.numreg = ep.idTIPO_CONTRATO " .
          " where cx.dataop between '$dataTRAB' and '$dataTRAB' ";
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;   // data
  $largura2 = $_SESSION['largIFRAME'] * 0.2;   // corretor/fucnionario
  $largura3 = $_SESSION['largIFRAME'] * 0.2;   // tipo contrato  
  $largura4 = $_SESSION['largIFRAME'] * 0.3;   // descricao da operacao  
  $largura5 = $_SESSION['largIFRAME'] * 0.1;   // cpf  
  $largura6 = $_SESSION['largIFRAME'] * 0.1;   // vlr recebido  
    
	$header = "$largura1 px,Data|$largura2 px,Envolvido|$largura3 px,Tipo contrato|$largura4 px,Descrição|$largura5 px,CPF|$largura5 px,Vlr recebido";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="99%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS =  mysql_num_rows($resultado);
  
  if ($qtdeREGS>0)   mysql_data_seek($resultado, 0);    
  
  $i=1;  
  while ($row = mysql_fetcH_object($resultado)) {    
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";
      $largura5="width=\"$largura5 px\"";
      $largura6="width=\"$largura6 px\"";                  
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4=''; $largura5=''; $largura6='';
    }
    $i++;
    
    $envolvido = ($row->idENTREGA!='') ? $row->idREPRESENTANTE : $row->idFUNCIONARIO;
    $cpf = ($row->idENTREGA!='') ? $row->cpf : '-';
    $vlrRECEBIDO =  ($row->idENTREGA!='') ? number_format($row->vlrRECEBIDO, 2, ',', '') : number_format($row->valor, 2, ',', '');
        
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"ent_$entATUAL\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;$row->dataOPERACAO</td>".
            "<td align=\"left\" $largura2>$envolvido</td>".
            "<td align=\"left\" $largura3>$row->descTIPO_CONTRATO</td>".
            "<td align=\"left\" $largura4>$row->descOPERACAO</td>".
            "<td align=\"left\" $largura3>$row->cpf</td>".                        
            "<td align=\"right\" $largura3>$vlrRECEBIDO</td>".
            "</tr>";
              
    $resp .= $lin;
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

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
    case 'incluirREG':
      $resp=str_replace('TITULO_JANELA', "Nova entrega de proposta",$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarREG':
      $idREAL = $vlr;
      $idREAL = str_replace('rec_', '', $idREAL);
      $idREAL = str_replace('ent_', '', $idREAL);
    
      $resp=str_replace('TITULO_JANELA', "Editar entrega de proposta",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  $resp = str_replace('@titPROPOSTAS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "20%,Corretor|25%,Tipo contrato|10%,CPF|10%,Valor|10%,Valor recebido|15%,Vlr AllCross|10%,Adesão" ).'</table>', $resp);
  $resp=str_replace('@altDivPROPOSTAS', '100px', $resp);
  
  $resp = str_replace('@titPAGAMENTOS',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "15%,Tipo pagamento|80%,Detalhes" ).'</table>', $resp);
  $resp=str_replace('@altDivPAGAMENTOS', '110px', $resp);
  
  $tabPROP = '<table width="99%" id="tabPROPOSTAS" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        
  $tabPGTO = '<table width="99%" id="tabPGTO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';
        

  if ($acao!='incluirREG')   {            
      // le registro de entrega em questao
      $sql  = "select ent.numero,  date_format(ent.data, '%d/%m/%y') as data, " .
              "ifnull(op.nome, '* erro *') as nomeOPERADOR, ent.opresponsavel as idOPERADOR, " .  
              "ifnull(rep.nome, '* erro *') as nomeREPRESENTANTE, ent.numrepresentante as idREPRESENTANTE, " .  
              "ent.propostas   " .
              "from entregaspropostas ent " .  
              "left join representantes rep  " .
              " 	on rep.numero = ent.numrepresentante " .  
              "left join operadores op " .  
              "	  on op.numero = ent.opresponsavel " .  
              "inner join propostasentregues prop   " .
              "   on ent.numero = prop.numENT " . 
              " where ent.numero = $idREAL   ";
              
      $resultado = mysql_query($sql) or die (mysql_error());  
      $row = mysql_fetcH_object($resultado);
    
      $resp=str_replace('vDATA', $row->data, $resp);
      $resp=str_replace('vREPRESENTANTE', $row->idREPRESENTANTE, $resp);
      $resp=str_replace('vNomeREPRESENTANTE', $row->nomeREPRESENTANTE, $resp);    
      $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->idOPERADOR)", $resp);
                  
      $resp=str_replace('@numREG', $idREAL , $resp);
      
      // le propostas entregues da entrega em questao
      $sql  = "select numREG, numPROP, vlrPROP, vlrADESAO, vlr1Mens, ifnull(idCANCEL,0) as idCANCEL, opCAIXA,  ".
              " ifnull(mot.descricao, '* erro *') as descMOTIVO, vlrREPRE, ifnull(prop.60porcento, 0) as Ficou60porcento " .
              " from propostasentregues prop " . 
              "left join motivos_cancelamento mot " .
               "   on mot.numero = prop.idCANCEL " .
              "where prop.numENT = $idREAL " . 
              "order by prop.numPROP ";

      $infoCORES = explode(',', $_SESSION['cores']);
      $corFORM = $infoCORES[0]; 
      
      $propostas = mysql_query($sql) or die (mysql_error());  
      while ($proposta = mysql_fetcH_object($propostas) )  {

        $adesao = number_format($proposta->vlrADESAO, 2, ',', '')  ;

        if ($proposta->Ficou60porcento=='1') 
          $vlrREPRE = number_format($proposta->vlrADESAO*0.6, 2, ',', '')  . ' (60%)';
        else
          $vlrREPRE = number_format($proposta->vlrREPRE, 2, ',', '')  ;         
        
                
        $mens = number_format($proposta->vlr1Mens, 2, ',', '')  ;
        $total = number_format($proposta->vlrPROP, 2, ',', '')  ;
        $descMOTIVO = $proposta->idCANCEL==0 ? '' : $proposta->descMOTIVO;         
        $idMOTIVO = $proposta->idCANCEL==0 ? '' : $proposta->idCANCEL ;
        $motivo = $proposta->idCANCEL==0 ? '' : "$descMOTIVO ($idMOTIVO)";        
        
        $lin = "<tr id=\"PE_$proposta->numREG\" onmouseout=\"this.style.backgroundColor='$corFORM';\" " . 
                ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '.
               "  onmousedown=\"lstCHEQUES(this.id); editarPropEnt(this.id)\" >" .
               "<td align=\"left\" width=\"15%\">$proposta->numPROP</td>". 
               "<td align=\"right\" width=\"15%\">$adesao</td>".
               "<td align=\"left\" width=\"47%\">$motivo</td>".
               "<td onmousedown=\"removePropEnt('PE_$proposta->numREG')\"  width=\"5%\" align=\"center\" >".
                              '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
               "<td style=\"display:none\">$idMOTIVO</td>".
               "<td style=\"display:none\">$descMOTIVO</td>".               
               "<td style=\"display:none\">$proposta->opCAIXA</td>".               
               "<td align=right>$vlrREPRE</td>".               
               '</tr>';               
               
                         
        $tabPROP .= $lin;       
          
        $idCAIXA = $proposta->opCAIXA;
        
        
        if ($idCAIXA!='') {
          // le cheques da entrega em questao
          $sql = "select ch.numCHEQUE, date_format(ch.data, '%d/%m/%y') as data, ch.valor, ch.proposta, ch.opCAIXA, " .
                  " ifnull(ban.nome, '* erro *') as nomeBANCO, banco as idBANCO " .
                  'from cheques ch  '.
                  "left join bancos ban ".
                  "   on ban.numero = ch.banco ".
                  "where opCAIXA = $idCAIXA ";
          
  
          $cheques = mysql_query($sql) or die (mysql_error());  
          $c = 0;
          while ($cheque = mysql_fetcH_object($cheques) )  {
          
            $valor =  number_format($cheque->valor, 2, ',', '')  ; 
            $strCHEQUES .= $strCHEQUES=='' ? '' : '|';
            $strCHEQUES .= "$c;PE_$proposta->numREG;$cheque->opCAIXA;$cheque->numCHEQUE;$cheque->idBANCO;$cheque->nomeBANCO;$cheque->data;$valor";
  
            $c++;
          }
          mysql_free_result($cheques);
        }  
      }
      mysql_free_result($propostas);
      
  }    
  else {  
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = "$logado[0] ($logado[1])";
        
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('vREPRESENTANTE', '', $resp);
    $resp=str_replace('vNomeREPRESENTANTE', '', $resp);
    
    $resp=str_replace('v_TIPO_CONTRATO', '', $resp);
    $resp=str_replace('vTIPO_CONTRATO', '', $resp);
        
    $resp=str_replace('vCPF', '', $resp);        
    $resp=str_replace('vVALOR', '', $resp);
    $resp=str_replace('vRECEBIDO', '', $resp);
    $resp=str_replace('vADESAO', '', $resp);        
    $resp=str_replace('vOPERADOR', $logado, $resp);         
    $resp=str_replace('@numREG', '', $resp);
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
if ($acao=='gravarENTREGA') {
  $cmps = explode('|', $_REQUEST['vlr']);
  $props = explode('|', $_REQUEST['prop']);  
  $chs = explode('|', $_REQUEST['ch']);
  $incluirCAIXA = $_REQUEST['incluirCAIXA'];  
  
  $id = $cmps[0];
  
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $representante=$cmps[2];
  $representante=rtrim($representante)=='' ? 'null': $representante;
  
  if ($id=='')  
    $sql = "insert into entregaspropostas(data,numREPRESENTANTE,opRESPONSAVEL,propostas) ".
            " values('$cmps[1]', $cmps[2], $logado[1], '$cmps[3]')";
            
  else {
    $rsPROPS = mysql_query("select numprop from propostasentregues where nument=$id ", $conexao) or die (mysql_error());
    
   	
    if (mysql_num_rows($rsPROPS)>0)  {
      while ($operacao = mysql_fetch_object($rsPROPS)) {
        $rsCAIXA = mysql_query("select numop from caixa where proposta=$operacao->numprop and codop=101", $conexao) or die (mysql_error());
        
        $regCAIXA = mysql_fetch_object($rsCAIXA);
        if ( mysql_num_rows($rsCAIXA)>0 ) {
          $opCAIXA = $regCAIXA->numop;
        
          mysql_query("delete from cheques where opcaixa=$opCAIXA") or  die (mysql_error());
          mysql_query("delete from caixa where numop=$opCAIXA") or die( mysql_error() );
        }          
        mysql_free_result( $rsCAIXA );
        
      }
    }      
    mysql_free_result( $rsPROPS );
  
  
    mysql_query("delete from creditos_descontos where numero in (select numreg_debito from propostasentregues where numENT=$id)") or die( mysql_error() );
    mysql_query("delete from propostasentregues where numENT=$id") or die( mysql_error() );
        
    $sql = "update entregaspropostas set data='$cmps[1]',numREPRESENTANTE=$cmps[2],opRESPONSAVEL=$logado[1],".
            "propostas='$cmps[3]' " . 
          " where numero=$id";
          
  }          
          
  mysql_query($sql) or die( mysql_error() );
  
  if ($id=='')    $id = mysql_insert_id();
    
  for ($g=0; $g<count($props); $g++) {
    $proposta = explode(';', $props[$g]);
    
    $numPROP = $proposta[0];
    $idCANCELA = $proposta[1];
    $adesao = $proposta[2];
    $vlrREPRE = $proposta[4];
    $Ficou60porcento=0;
    if (strpos($vlrREPRE, '(60%)')!==false )  {
      $vlrREPRE= str_replace('(60%)', '', $vlrREPRE);
      $Ficou60porcento=1;
    }  
    
    $idLinPROPOSTA = $proposta[3];
    
    $ultOP = 'null';
    
    if ((float)$adesao>0) {

      /* obtem o tipo do representante
      se tipo= 1 (representante base), tipo da operacao no baixa = base (tipoCAIXA=2)
      senao (representante <> base), tipo da operacao no baixa = tele (tipoCAIXA=1)
      */
      
      $rsREPRE = mysql_query("select tipo from representantes where numero=$cmps[2] ", $conexao) or die (mysql_error());
 	    $regREPRE= mysql_fetch_object($rsREPRE);
 	    
 	    $tipoCAIXA = $regREPRE->tipo==1 ? 2 : 1; 
 	    
 	    mysql_free_result($rsREPRE);
 	    
      $rst_UltOP = mysql_query('select max(numOP) as ultOP from caixa', $conexao) or die (mysql_error());
      $reg_UltOP = mysql_fetcH_object($rst_UltOP);
    
      $ultOP = $reg_UltOP->ultOP;
      $ultOP++;
      
      mysql_free_result($rst_UltOP);
       
      if ($incluirCAIXA=='1') {
        if ((float)$adesao>0) {        
          $sql = "insert into caixa(ENTouSAI, dataOP, codOP, descOP, proposta, valor, numOP, tipoCAIXA)" .
                " select 'E', '$cmps[1]', 101, 'PROPOSTA Nº $numPROP', $numPROP, $adesao, $ultOP, $tipoCAIXA ";
          mysql_query($sql) or die( mysql_error() );
        }
      }  
      
    }          
                
    // se representante ficou com mais de 60% (sua comissao, o que ele tem direito) - gera debito com o valor pego a mais
    $idDEBITO=0;
    $vlrMERECIDO = round($adesao * 0.6, 2); 
    if ($vlrREPRE > $vlrMERECIDO) {
      $vlrDEBITO = ($vlrMERECIDO - $vlrREPRE) * -1; 
      $sql = "insert into creditos_descontos(data,tipo,descricao,representante,valor,operador) ".
              " values(now(), 'D', 'DÉBITO ENTREGA PROP $numPROP, VALOR RETIDO ($vlrREPRE)- 60% COMISSÃO ($vlrMERECIDO)', $cmps[2], $vlrDEBITO, $logado[1])";  
      mysql_query($sql) or die( mysql_error() );
      $idDEBITO = mysql_insert_id();
    }      
              
          
    $idCANCELA = ($idCANCELA=='') ? 'null' : $idCANCELA;
    $sql = "insert into propostasentregues(numENT, numPROP, vlrPROP, vlrADESAO, vlr1Mens, idCANCEL, opCAIXA, vlrREPRE, 60porcento, numreg_debito)" .
          " select $id, $numPROP, $adesao, $adesao, 0, $idCANCELA, $ultOP, $vlrREPRE, $Ficou60porcento, $idDEBITO";
    mysql_query($sql) or die( mysql_error() );
    
    

    // cheques
    $sql="delete from cheques where proposta=$numPROP";
    mysql_query($sql) or die( mysql_error() );
    
    for ($e=0; $e<count($chs); $e++) {
      if (trim($chs[$e])=='') continue;
      
      $cheque = explode(';', $chs[$e]);
      
      $idPropENTREGUE = $cheque[0];
      $numCH = $cheque[1];        
      $idBANCO = $cheque[2];        
      $dataCH = $cheque[3];        
      $vlrCH = $cheque[4];

      if (trim($idPropENTREGUE) == trim($idLinPROPOSTA) ) {
        $sql="insert into cheques(opCAIXA, numCHEQUE, banco, data, valor, proposta) " .
              " select $ultOP, $numCH, $idBANCO, '$dataCH', $vlrCH, $numPROP ";
                                
        mysql_query($sql) or die( mysql_error() );
      }  
    }
  }            

  $resp = "OK;ent_$id";
}  



/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


