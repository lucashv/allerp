<?
header("Content-Type: text/html; charset=iso-8859-1");


session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );
require_once( '../includes/funcoesDATA.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];

/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$resp = 'INEXISTENTE';

$MESES = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho',
          'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');


/*****************************************************************************************/
IF ($acao=='verENTPROP') {
  // procura proposta(s) entregue no caixa (dos ultimos 2 dias) que seja do mesmo tipo d contrato e tenha o mesmo 
  // id (cpf ou cnpj,podendo ser em branco)
  // se houver mais de uma proposta encontrada, cria table para que usuario escolha qual delas 
  // *** esse processo existe para que o cadastro da proposta encontre a entrega da proposta 
  $tipoCONTRATO = $_REQUEST['tipo'];
  $cpf = $_REQUEST['id'];
  $sequencia = $_REQUEST['sequencia'];
  $sql  = "select ent.numreg, ent.valor as vlrCONTRATO, ent.idREPRESENTANTE, rep.nome as nomeREPRESENTANTE, ent.vlrADESAO, ent.vlrRECEBIDO, ".
          "      ent.idTIPO_CONTRATO, tip.descricao as nomeTIPO_CONTRATO, ent.cpf, date_format(cx.dataOP, '%d/%m/%y') as dataENTREGA, ".
          " ent.valor+ent.vlrADESAO as vlrTOTAL ".
          "from entregaspropostas ent ".
          "inner join representantes rep ".
          "  on rep.numero=ent.idREPRESENTANTE ".
          "inner join tipos_contrato tip ".
          "  on tip.numreg = ent.idTIPO_CONTRATO ".
          "inner join caixa cx ".
          "  on cx.numreg = ent.idCAIXA ".
          "where datediff(now(), dataop)<=200 and ent.cpf='$cpf' and ent.idTIPO_CONTRATO=$tipoCONTRATO and ".
            "   @criterioSEQUENCIA ";
  
  // aqui embaixo estou dizendo o seguinte......sequencia=0    proposta nao foi entregue no caixa
  // sequencia = $sequencia , a proposta entregue no caixa é a mesma que estamos lidando,  faço isso porque
  // as vezes o valor no caixa é alterado, e precisamos reler, importar este valor para o cadastro
  if ($sequencia!='')
    $sql = str_replace('@criterioSEQUENCIA', " (ifnull(ent.sequencia,0)=0 or ifnull(ent.sequencia,0)=$sequencia);  " , $sql);
  else
    $sql = str_replace('@criterioSEQUENCIA', " ifnull(ent.sequencia,0)=0   " , $sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if ( mysql_num_rows($resultado)==0 ) $resp='nenhuma';
  else {
    if (mysql_num_rows($resultado)==1) {
      $row = mysql_fetcH_object($resultado);  
      $resp="somenteUMA$row->numreg;$row->idREPRESENTANTE;$row->nomeREPRESENTANTE;$row->vlrCONTRATO;$row->vlrADESAO;$row->vlrRECEBIDO;$row->vlrTOTAL";
    }
    else { 
      $resp='';  

      $largura1 = $_SESSION['largIFRAME'] * 0.1;
      $largura2 = $_SESSION['largIFRAME'] * 0.35;
      
    	$header = "$largura1 px,Data entrega|$largura1 px,Valor|$largura2 px,Corretor|$largura2 px,Tipo contrato".
                "|$largura1 px,CPF/CNPJ|1%,&nbsp;|1%,&nbsp;|1%,&nbsp;";
     
      $resp = tabelaPADRAO('width="97%" ', $header );
      $resp .= '</table>|<table id="lstPROPENTREGUES" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
    
      $i=1;
      while ($row = mysql_fetcH_object($resultado)) {
        if ($i==1) {
          $largura1="width=\"$largura1 px\"";
          $largura2="width=\"$largura2 px\"";
        } else {    
          $largura1='';$largura2='';
        }
        $i++;
    
        $vlr = number_format($row->vlrCONTRATO, 2, ',', '');
        $vlrADESAO = number_format($row->vlrADESAO, 2, ',', '');
        $vlrRECEBIDO = number_format($row->vlrRECEBIDO, 2, ',', '');
        $vlrTOTAL = number_format($row->vlrTOTAL, 2, ',', '');        
      
        $lin = "<tr id=$row->numreg onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" " . 
                "onmouseout=\"this.style.backgroundColor='#E6E8EE';\"  ".
                "onclick='avisaENTREGUECAIXA($row->numreg,1);fechaESCOLHEPROP();document.getElementById(\"txtASSINATURA\").focus();' >" . 
                "<td align=\"left\" $largura1>&nbsp;$row->dataENTREGA</td>".
                "<td align=\"right\" $largura1>$vlr&nbsp;&nbsp;</td>".
                "<td align=\"left\" $largura2>$row->nomeREPRESENTANTE ($row->idREPRESENTANTE)</td>".
                "<td align=\"left\" $largura2>$row->nomeTIPO_CONTRATO ($row->idTIPO_CONTRATO)</td>".
                "<td align=\"center\" $largura1>$row->cpf</td>".
                "<td style='display:none'>$row->idREPRESENTANTE</td>".
                "<td style='display:none'>$row->nomeREPRESENTANTE</td>".
                "<td style='display:none'>$vlr</td>".
                "<td style='display:none'>$vlrADESAO</td>".
                "<td style='display:none'>$vlrRECEBIDO</td>".
                "<td style='display:none'>$vlrTOTAL</td>".                
                "</tr>";

        $resp .= $lin;
      }
    }
  }
}


/*****************************************************************************************/
if ($acao=='cancelarMENS') {
  
  mysql_query("update futuras set extrato=null,dataGeracaoRel=null,situacaoPARCELA=null, dataSITUACAOPARCELA=null, dataPgtoParcela=null, valorpagoParcela=null, ". 
              " periodoAPURACAO=null,opResponsavel=null where numreg=$vlr") or  die (mysql_error());

  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='baixarMENS') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);                  
  $data = $_REQUEST['data'];
  $repasse = $_REQUEST['repasse'];
  $valor = $_REQUEST['valor'];
  
  mysql_query("update futuras set situacaoPARCELA=1, dataSITUACAOPARCELA=now(), dataPgtoParcela='$data', valorpagoParcela=$valor, ". 
              " dataGeracaoRel='$repasse', valor=$valor, opResponsavel=$logado[1] where numreg=$vlr") or  die (mysql_error());

  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='adiantarMENS') {
  mysql_query("update futuras set marcadaParaPagarAdiantamento=1  ". 
              " where numreg=$vlr") or  die (mysql_error());

  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='cancelar_adiantarMENS') {
  mysql_query("update futuras set marcadaParaPagarAdiantamento=0  ". 
              " where numreg=$vlr") or  die (mysql_error());
  //echo "update futuras set marcadaParaPagarAdiantamento=0  where numreg=$vlr";
  //die();
  echo('ok'); die();
}


/*****************************************************************************************/
if ($acao=='devolucao') {                  
  mysql_query("update propostas set pendente='S' where sequencia=$vlr") or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='anulaPENDENCIA') {
  mysql_query("update propostas set pendente='N' where sequencia=$vlr") or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='marcarPENDENTE') {
  mysql_query("update propostas set pendente='S' where sequencia=$vlr") or  die (mysql_error());
    
  echo('ok'); die();
}

/*****************************************************************************************/
IF ($acao=='calcVlrPlantao') {
  $idPRODUTO = $_REQUEST['vlr'];
  $vlrCONTRATO = $_REQUEST['vlr2'];
  $qtdeVIDAS = $_REQUEST['vlr3'];
  
  $sql  = "select ifnull(qtde1,0) as qtde1, ifnull(qtde2,0) as qtde2, ifnull(qtde3,0) as qtde3, ifnull(qtde4,0) as qtde4, ifnull(qtde5,0) as qtde5, ".
          "  ifnull(qtde6,0) as qtde6 , ifnull(perc1,0) as perc1, ifnull(perc2,0) as perc2, ifnull(perc3,0) as perc3, vlrPRODUCAO ".
          "from tipos_contrato tip ".
          "where tip.numreg=$vlr ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  $perc=100;
  if ($qtdeVIDAS >= $row->qtde1 && $qtdeVIDAS <= $row->qtde2 && $row->qtde1>0 and $row->qtde2>0 and $row->perc1>0) $perc=$row->perc1;
  if ($qtdeVIDAS >= $row->qtde3 && $qtdeVIDAS <= $row->qtde4 && $row->qtde3>0 and $row->qtde4>0 and $row->perc2>0) $perc=$row->perc2;
  if ($qtdeVIDAS >= $row->qtde5 && $qtdeVIDAS <= $row->qtde6 && $row->qtde5>0 and $row->qtde6>0 and $row->perc3>0) $perc=$row->perc3;

  $vlrCONTRATO = str_replace(',', '.', $vlrCONTRATO);

  $resp = number_format($vlrCONTRATO * ($perc/100), 2, ',', '').'|'."($perc%)|$row->vlrPRODUCAO"; 
}  



/*****************************************************************************************/
if ($acao=='fixaOPERADORA') {
  $id = $_REQUEST['vlr'];
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  mysql_query("update operadores set operadoraATUAL=$id where numero=$logado[1];") or  die (mysql_error());  
  
  $resp='ok';  
}          
          
/*****************************************************************************************/
IF ($acao=='lerOPERADORAS') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $sql  = "select ifnull(operadoraATUAL,1) as operadoraATUAL from operadores where numero=$logado[1]  ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  
  $operadoraATUAL=$row->operadoraATUAL;
  $operadoraATUAL=($operadoraATUAL==0) ? 1 : $operadoraATUAL;
  mysql_free_result($resultado);

  $sql  = "select numreg, nome  ".
          " from operadoras  where ifnull(ativo,'')='S' " .    
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $qtde=mysql_num_rows($resultado);
  $qtde++;
  
  $tab="<table width='100%'><tr>" ;
  $perc = intval(100/$qtde);  
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    
    $arqLOGO='ajax/logos/'.str_pad($row[0], 5, '0', 0).'.png';
    $lin = "<td id=operadora$row[0] @cor onclick='fixaOPERADORA($row[0]);'' align=center style='cursor:pointer;' onmouseout=\"tiraFocoOperadora(this.id);\" onmouseover=\"this.style.backgroundColor='#DADADA'\" ".    
              " width='$perc%' title='$row[1]'><img src='$arqLOGO' /></td>";
              
    if ($operadoraATUAL == $row[0])
      $lin = str_replace('@cor', 'bgcolor="lightgrey"', $lin);
    else
      $lin = str_replace('@cor', '', $lin);
    $tab .= $lin;                          
  }
  $lin = "<td @cor id=operadora200 onclick='fixaOPERADORA(200);' align=center style='cursor:pointer;' onmouseout=\"tiraFocoOperadora(this.id);\" onmouseover=\"this.style.backgroundColor='#DADADA'\" ".    
            " width='$perc%'><font face=verdana size='+1'>TODAS</font></td>";
  if ($operadoraATUAL == '200')
    $lin = str_replace('@cor', 'bgcolor="lightgrey"', $lin);
  else
    $lin = str_replace('@cor', '', $lin);
            
  $tab .= $lin;  
  
  $resp = $tab . '^' . $operadoraATUAL;  
}  

/*****************************************************************************************/
IF ( $acao=='lerDiasUteis' )  {
  $data=$_REQUEST['data'];
  $days = 0;
  $data= str_replace('/', '.', $data);
 
  for($i = strtotime($data); $i < time(); $i=$i+86400) {
    $weekday = date('w', $i);
    if($weekday > 0 &&  $weekday < 6)   $days++;
  }   

  die( "$days" );
}  


/*****************************************************************************************/
IF ( $acao=='lerDataHoje' )  {
  $resp = date("d/m/Y");
}


/*****************************************************************************************/
if ($acao=='alteraDataCadastro') {
  $id = $_REQUEST['vlr'];
  $data = $_REQUEST['data'];
  
  mysql_query("update propostas set datacadastro='$data' where sequencia=$id") or  die (mysql_error());
  
  $resp='ok';  
}

/*****************************************************************************************/
if ($acao=='excluir') {
  $id = $_REQUEST['vlr'];
  
  //mysql_query("delete from futuras where sequencia=$id") or  die (mysql_error());
  // antes de excluir a proposta, desfaz a ligacao caixa - cadastro, registra na entrega da proposta (caixa) vinculada ao cadastro
  // que a proposta em si, o cadastro nao existe mais, ou seja, campo sequencia= null  
  //mysql_query("update entregaspropostas set sequencia=null where sequencia=$id") or  die (mysql_error());
  //mysql_query("delete from propostas where sequencia=$id") or  die (mysql_error());      
    
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='cancelar') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update propostas set cancelada=case cancelada when 'S' then 'N' else 'S' end where sequencia=$id") or  die (mysql_error());
    
  echo('ok'); die();
}




/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $sequencia = $cmps[0];
  $idENTREGA = $_REQUEST['idENTREGA']=='' ? 'null' : $_REQUEST['idENTREGA'] ;
  
  // soma 1 mes à data da assinatura OU NAO, dependendo como esta configurada a OPERADORA
  $idOPERADORA=$cmps[18];
  $sql  = "select 1aMensIgualVigencia as priMensIgualVigencia from operadoras ".
          "where numreg=$idOPERADORA ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  // $row->priMensIgualVigencia=='O' - tive que criar esse 3o tipo, para operadoras quase iguais ao tipo $row->priMensIgualVigencia=='S'
  // ambas têm data 1a mens = data vigencia, mas o tipo $row->priMensIgualVigencia=='O', emite relat de confirmacoes para corretor
  // sobre a 1a mensalidade, enquanto que operadoras do tipo $row->priMensIgualVigencia=='S', nao emitem repasse sobre a 1a mens  
  if ($row->priMensIgualVigencia=='S' || $row->priMensIgualVigencia=='O') 
    $data1aMens = date("Y-m-d", strtotime(date("Y-m-d", strtotime($cmps[9])) ." +0 month"  ) );
  else
    $data1aMens = date("Y-m-d", strtotime(date("Y-m-d", strtotime($cmps[9])) ." +1 month"  ) );
  
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];
  $nascimento=$cmps[20]=='null' ? 'null' : "'$cmps[20]'";
  $idBAIRRO=$cmps[25]=='' ? 'null' : $cmps[25];
  $qtdeMENS = $_REQUEST['qtdeMENS'];
  $contrato2=$cmps[32]=='' ? 'null' : "'".$cmps[32]."'";
  
  // ****************************************** PROPOSTAS
  if ($sequencia=='') 
    $sql = "insert into propostas(idOPERADORA, idTipoContrato, numCONTRATO, dataASSINATURA, dataCADASTRO, contratante, cpfCONTRATANTE, ".
            " foneRES, qtdeVIDAS, idREPRESENTANTE, idComiRepresentante, idComiPrestadora, vlrADESAO, vlrCONTRATO, vlrTOTAL, ".
            " vlrRECEBIDO, vlrPRODUCAO, vlrPLANTAO, observacoes, opRESPONSAVEL, dataNASC, sexo, endereco, numero, complemento, " .
            " idBAIRRO, municipio, uf, cep, foneCOM, foneCEL, email, qtdeMENS, numregPropostaEntregueCaixa, numCONTRATO2) ".
           "  select $cmps[18], $cmps[14],'$cmps[1]', '$cmps[9]', now(), '$cmps[12]', '$cmps[13]', '$cmps[2]', $cmps[11], $cmps[19], ".
           "       $cmps[15], $cmps[16], $cmps[3], $cmps[4], $cmps[5], $cmps[6], $cmps[7], $cmps[8], '$cmps[10]', " .
           " $logado, $nascimento, '$cmps[21]', '$cmps[22]', '$cmps[23]', '$cmps[24]', $idBAIRRO, '$cmps[26]', '$cmps[27]', '$cmps[28]', ".
           " '$cmps[29]', '$cmps[30]', '$cmps[31]', $qtdeMENS, $idENTREGA, $contrato2";  
  else  
    $sql = "update propostas set idOPERADORA=$cmps[18], idTipoContrato=$cmps[14], numCONTRATO='$cmps[1]', dataASSINATURA='$cmps[9]', ".
            "  contratante='$cmps[12]', cpfCONTRATANTE='$cmps[13]', ".
            " foneRES='$cmps[2]', qtdeVIDAS=$cmps[11], idREPRESENTANTE=$cmps[19], idComiRepresentante=$cmps[15], idComiPrestadora=$cmps[16], ".
            "vlrADESAO=$cmps[3], vlrCONTRATO=$cmps[4], vlrTOTAL=$cmps[5], ".
            " vlrRECEBIDO=$cmps[6], vlrPRODUCAO=$cmps[7], vlrPLANTAO=$cmps[8], observacoes='$cmps[10]', " .
            " dataNASC=$nascimento, sexo='$cmps[21]', endereco='$cmps[22]', numero='$cmps[23]', complemento='$cmps[24]', ". 
            " idBAIRRO=$idBAIRRO, municipio='$cmps[26]', uf='$cmps[27]', cep='$cmps[28]', foneCOM='$cmps[29]', foneCEL='$cmps[30]', ".
            " email='$cmps[31]', qtdeMENS=$qtdeMENS, numregPropostaEntregueCaixa=$idENTREGA, numCONTRATO2=$contrato2  ".
           "where sequencia=$sequencia ;";
//die($sql); 
  $representante = $cmps[19];
  
  mysql_query($sql) or die($sql . "\n". mysql_error());

  if ($sequencia=='')   $sequenciaUSAR = mysql_insert_id();
  else $sequenciaUSAR=$sequencia;

  // estipula a ligacao entre caixa-cadastro, marcando na entrega de proposta (caixa), qual o seu respectivo cadastro de proposta
  mysql_query("update entregaspropostas set sequencia=$sequenciaUSAR where numreg=$idENTREGA") or  die($sql . "\n". mysql_error());

  
  
  // ****************************************** FUTURAS
  $vlr=$cmps[5];
  if ($qtdeMENS==3)
    $parcelas = 100;
  else if ($qtdeMENS==2) {
    $parcelas = 15;
    mysql_query("delete from futuras where sequencia=$sequenciaUSAR and ordem > 15", $conexao) or die (mysql_error());
  }
  else if ($qtdeMENS==1) {
    $parcelas = 10;
    mysql_query("delete from futuras where sequencia=$sequenciaUSAR and ordem > 10", $conexao) or die (mysql_error());
  }    
    
  for ($ordem=1; $ordem<=$parcelas; $ordem++)  {
    $somar = $ordem-1;
    $vencimento = str_replace('-', '', date("Y-m-d", strtotime(date("Y-m-d", strtotime($data1aMens)) ." +$somar month"  ))) ;
                    
    $sql = "select * from futuras where sequencia=$sequenciaUSAR and ordem=$ordem "; 
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    if ( mysql_num_rows($resultado)>0 )
      $sql = "update futuras set dataVENCIMENTO='$vencimento', valor=$vlr, numREPRESENTANTE=$representante " .
            " where sequencia=$sequenciaUSAR and ordem=$ordem ";
    else
      $sql = "insert into futuras(sequencia, ordem, dataVENCIMENTO, valor, numREPRESENTANTE) " .
            "select $sequenciaUSAR, $ordem, '$vencimento', $vlr, $representante" ;
                
    mysql_query($sql) or die(mysql_error());
  }
  
  
  mysql_close($conexao);
  die("OK;$sequenciaUSAR"); 
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
   $dataTRAB = $_REQUEST['vlr'];     // ddmmyyyy
   
   $operadora = '200';  // todas as operadoras   
   if (isset( $_REQUEST['operadora'] )) $operadora = $_REQUEST['operadora'];
  
   $palavra ='';
   if (isset( $_REQUEST['vlr3'] )) $palavra = $_REQUEST['vlr3'];
    
   // se nao especificado uma pesquisa, le regs do mes ano atual

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
      

   $ordenar = " order by sequencia desc";
   
   if ($palavra=='') $criterio =  "where date_format(dataCADASTRO, '%Y%m%d') between '$dataINI' and '$dataFIN' " ; 
   else {
      $cmp = $_REQUEST['cmp'];
      
      if ($cmp==1)  {$criterio = "where sequencia=$palavra "; $palavra="sequência= $palavra";}
      if ($cmp==2)  {$criterio = "where (numCONTRATO=$palavra)"; $palavra="nº contrato= $palavra";}
      if ($cmp==3)  {$criterio =   
        " where contratante like '$palavra%'  ";
        $palavra="começo nome contratante= $palavra";}
      if ($cmp==4)  {$criterio =   
        " where contratante like '%$palavra%'  ";
        $palavra="qq parte nome contratante= $palavra";} 
      if ($cmp==5)  {$criterio = " where prop.pendente='S' and ifnull(prop.cancelada,'N')<>'S' and ifnull(prop.jaENVIADA,0)=0 ";$palavra=' (propostas pendentes)' ;}
      if ($cmp==6)  {$criterio =        
        " where replace(replace(replace(prop.cpfcontratante,'-',''),'.',''),'/','')=replace(replace(replace('$palavra','-',''),'.',''),'/','')  ";
        $palavra=" cpf/cnpj= $palavra " ;        
      }
      if ($cmp==7)  {$criterio =        
        $palavra="propostas com numeração duplicada" ;
        $ordenar = " order by numCONTRATO desc";
      
        $sql="select numCONTRATO ". 
             "from propostas ".
             "where numCONTRATO<>'' ".
             "group by numCONTRATO, idOPERADORA ".
             "having count(*)>1; ";
        $resultado = mysql_query($sql, $conexao) or die (mysql_error());
        $duplicadas='-1';
        while ($row = mysql_fetcH_object($resultado)) {
          $duplicadas .= $duplicadas=='' ? '' : ','; 
          $duplicadas .= "'$row->numCONTRATO'" ;
        }
        $criterio = " where numCONTRATO in($duplicadas) ";
      }
    }
    
    $criterio .= " AND (alterada2_excluida1 IS NULL OR alterada2_excluida1 = '')";
              
   $sql  = "select sequencia, dataCADASTRO, numCONTRATO, left(contratante, 26) as contratante, ".
           "date_format(dataCADASTRO, '%d/%m/%y') as dataMOSTRAR, date_format(dataASSINATURA, '%d/%m/%y') as dataASSINATURA, " .
           "idREPRESENTANTE, left(ifnull(rep.nome, '* erro *'), 20) as nomeREPRESENTANTE,  " .
           " ifnull(jaENVIADA, 0) as jaENVIADA, prop.idOPERADORA, ifnull(op.nome, '* ERRO *') as nomeOPERADORA, ".
           " idTipoContrato, ifnull(tip.descricao, '* ERRO *') as descTIPO_CONTRATO, prop.cancelada, pendente, prop.numregPropostaEntregueCaixa  ". 
           "from propostas prop "   .
           "left join representantes rep " . 
           "	on rep.numero = idREPRESENTANTE " .
           "left join tipos_contrato tip " . 
           "	on tip.numreg = prop.idTipoContrato " .
           "left join operadoras op " . 
           "	on op.numreg = prop.idOPERADORA " .
           $criterio .
          ($operadora!='200' ? " and op.numreg=$operadora " : "" ) .           
           "$ordenar " ;
//echo $sql;
//die($sql);
           
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.1;  
  $largura8 = $_SESSION['largIFRAME'] * 0.2;  
  $largura3 = $_SESSION['largIFRAME'] * 0.25;
  $largura4 = $_SESSION['largIFRAME'] * 0.1;  
  $largura5 = $_SESSION['largIFRAME'] * 0.1;
  $largura7 = $_SESSION['largIFRAME'] * 0.15;  
  $largura9 = $_SESSION['largIFRAME'] * 0.05;
    
	$header = "$largura2 px,Operadora|$largura7 px,Tipo|$largura1 px,Proposta|$largura3 px,Contratante|$largura4 px,Vigência|".
              "$largura5 px,Cadastro|$largura7 px,Corretor|$largura9 px,Caixa?";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
      $largura7="width=\"$largura7 px\"";      
      $largura8="width=\"$largura8 px\"";      
      $largura9="width=\"$largura9 px\"";
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4=''; $largura5='';  $largura7='';  $largura8='';$largura9='';
    }
    $i++;
    
    $nomeREPRE = substr($row->nomeREPRESENTANTE,0,25);

    if ($row->idOPERADORA=='0' || trim($row->idOPERADORA)=='') $operadora='-';
    else $operadora="$row->nomeOPERADORA ($row->idOPERADORA)";

    if ($row->idTipoContrato=='0' || trim($row->idTipoContrato)=='') $tipoCONTRATO='-';
    else $tipoCONTRATO="$row->descTIPO_CONTRATO ($row->idTipoContrato)";

    $entregueCAIXA = $row->numregPropostaEntregueCaixa!='' ? '' : '<font color=red><b>ERRO!</b></font>'; 

    $lin = "<tr @cor ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->sequencia\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura2>&nbsp;$operadora</td>".
            "<td align=\"left\" $largura7>&nbsp;$tipoCONTRATO</td>".           
            "<td align=\"left\" $largura1>&nbsp;$row->numCONTRATO</td>".
            "<td align=\"left\" $largura3>$row->contratante</td>".
            "<td align=\"center\"  $largura4>$row->dataASSINATURA</td>".
            "<td align=\"center\"  $largura5>$row->dataMOSTRAR</td>".            
            "<td align=\"left\" $largura7>$row->nomeREPRESENTANTE ($row->idREPRESENTANTE)</td>".                        
            "<td align=\"right\" $largura9>$entregueCAIXA</td>".
            "</tr>";
            
    if ($row->cancelada=='S') 
      $lin = str_replace('@cor', 'style="color:red;font-weight:normal;"', $lin);
      
    if ($row->pendente=='S') 
    	$lin = str_replace('@cor', 'style="color:blue;font-weight:normal;"', $lin);    

    if ($row->jaENVIADA=='1') 
    {
	  //$lin = str_replace('@cor', 'style="color:#9F9F9F;font-weight:normal;"', $lin);
      $lin = str_replace('@cor', 'style="color:green;font-weight:normal;"', $lin);
    }
    
    
      
            
    $resp = $resp . ($lin);
  }
  if ($palavra != '')
    $resp .= '^'.$qtdeREGS.'^FILTRO= '.$palavra. '^' . $novaDATA_JAVASCRIPT ;
  else
    $resp .= '^'.$qtdeREGS.'^Cadastradas em '.$MESES[ $mesATUAL-1 ] . '/' .$anoATUAL . '^' . $novaDATA_JAVASCRIPT ;
          
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG' || $acao=='refazer'  ) {
  $arq = fopen('proposta.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  $resp=str_replace('texto_botao', '[ F2= gravar ]',$resp);
  
  if ($acao!='incluirREG')   {
    $sql  = "select prop.sequencia, numCONTRATO, numCONTRATO2, contratante, date_format(prop.dataCADASTRO, '%d/%m/%Y') as dataCADASTRO, ".  
            "prop.idREPRESENTANTE, ifnull(rep.nome, '* erro *') as nomeREPRESENTANTE,  prop.numregPropostaEntregueCaixa, ".
            " cpfCONTRATANTE, idTipoContrato, ifnull(prop.qtdeMENS, 1) as qtdeMENS, ".
            "date_format(prop.dataAssinatura, '%d/%m/%y') as dataAssinatura,    ".
            " prop.vlrCONTRATO, prop.vlrADESAO, prop.vlrTOTAL, prop.vlrPRODUCAO, prop.vlrPLANTAO, prop.vlrRECEBIDO, " .
            " observacoes,  ".
            "opRESPONSAVEL, ifnull(ope.nome, '* erro *') as nomeOPERADOR, qtdeVIDAS, ifnull(tipo.descricao, '* erro *') as tipoCONTRATO, ".
            " date_format(prop.dataEnvioOperadora, '%d/%m/%y') as dataEnvioOperadora , ifnull(prop.jaENVIADA, 0) as jaENVIADA, ".
            " ifnull(idComiRepresentante,'') as idComiRepresentante, idComiPrestadora, ifnull(comiprest.nome, '') as nomeComiPrestadora, ".
            " ifnull(comirepre.nome, '') as nomeComiRepresentante, date_format(prop.dataNASC, '%d/%m/%y') as dataNASC,  ".
            "  prop.sexo, prop.endereco, prop.numero, prop.idBAIRRO, ifnull(bai.nome, '') as nomeBAIRRO, prop.municipio, ".
            " prop.cep, prop.foneRES, prop.foneCOM, prop.foneCEL, email, prop.complemento, prop.uf, prop.idOPERADORA, ".
            "  ifnull(opera.nome, '* erro *') as nomeOPERADORA,  date_format(prop.dataReenvioOperadora, '%d/%m/%y') as dataReenvioOperadora ".
            "from propostas prop ". 
            "left join representantes rep  ".
            "	  on rep.numero = prop.idREPRESENTANTE ".
            "left join operadores ope ".
            "	  on ope.numero = opRESPONSAVEL ".
            "left join tipos_contrato tipo ".
            "	  on tipo.numreg = idTipoContrato ".
            "left join tipos_comissao_prestadora comiprest ".
            "	  on comiprest.numreg = prop.idComiPrestadora ".
            "left join bairro bai ".
            "	  on bai.numreg = prop.idBAIRRO ".
            "left join operadoras opera ".
            "	  on opera.numreg = prop.idOPERADORA ".
            "left join tipos_comissao comirepre ".
            "	  on comirepre.numreg = prop.idComiRepresentante ".         
            " where prop.sequencia = $vlr  ";          

    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vQTDEUSUARIOS', $row->qtdeVIDAS, $resp);
    $resp=str_replace('@sequencia', $row->sequencia, $resp);

    $resp=str_replace('vPROPOSTA', $row->numCONTRATO, $resp);
    $resp=str_replace('vCONTRATO', $row->numCONTRATO2, $resp);
    $resp=str_replace('vCONTRATANTE', $row->contratante, $resp);    
    $resp=str_replace('vNASCCONTRATANTE', ($row->dataNASC=='00/00/00' ? '' : $row->dataNASC), $resp);
    $resp=str_replace('vSEXOC', $row->sexo, $resp);
    $resp=str_replace('vENDERECO', $row->endereco, $resp);
    $resp=str_replace('vEND_NUMERO', $row->numero, $resp);
    $resp=str_replace('vEND_COMPLEMENTO', $row->complemento, $resp);

    $resp=str_replace('vBAIRRO', ($row->idBAIRRO=='0' ? '' : $row->idBAIRRO), $resp);
    $resp=str_replace('v_BAIRRO', $row->nomeBAIRRO, $resp);
    
    $resp=str_replace('vMUNICIPIO', $row->municipio, $resp);
    $resp=str_replace('vUF', $row->uf, $resp);
    $resp=str_replace('vCEP', $row->cep, $resp);
    $resp=str_replace('vFONERES', $row->foneRES, $resp);
    $resp=str_replace('vFONECOM', $row->foneCOM, $resp);
    $resp=str_replace('vCELULAR', $row->foneCEL, $resp);
    $resp=str_replace('vEMAIL', $row->email, $resp);
    
    $resp=str_replace('vCPF', $row->cpfCONTRATANTE, $resp);
    $resp=str_replace('vREPRESENTANTE', $row->idREPRESENTANTE, $resp);    
    $resp=str_replace('v_REPRESENTANTE', $row->nomeREPRESENTANTE, $resp);
    
    $resp=str_replace('vTIPO_CONTRATO', $row->idTipoContrato, $resp);    
    $resp=str_replace('v_TIPO_CONTRATO', $row->tipoCONTRATO, $resp);
    
    $resp=str_replace('vCOMISSAO_REPRESENTANTE', $row->idComiRepresentante, $resp);    
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', $row->nomeComiRepresentante, $resp);
    
    $resp=str_replace('vCOMISSAO_PRESTADORA', $row->idComiPrestadora, $resp);    
    $resp=str_replace('v_COMISSAO_PRESTADORA', $row->nomeComiPrestadora, $resp);   
    
    $resp=str_replace('vOPERADORA', $row->idOPERADORA, $resp);    
    $resp=str_replace('v_OPERADORA', $row->nomeOPERADORA, $resp);    

    $resp=str_replace('vvlrCONTRATO', number_format($row->vlrCONTRATO, 2, ',', ''), $resp);
    $resp=str_replace('vvlrADESAO', number_format($row->vlrADESAO, 2, ',', ''), $resp);        
    $resp=str_replace('vvlrTOTAL', number_format($row->vlrTOTAL, 2, ',', ''), $resp);    
    $resp=str_replace('vvlrRECEBIDO', number_format($row->vlrRECEBIDO, 2, ',', ''), $resp);    
    $resp=str_replace('vvlrPRODUCAO', number_format($row->vlrPRODUCAO, 2, ',', ''), $resp);
    $resp=str_replace('vvlrPLANTAO', number_format($row->vlrPLANTAO, 2, ',', ''), $resp);
            
    $resp=str_replace('vASSINATURA', $row->dataAssinatura, $resp);    
    if ($row->jaENVIADA==0)
      $resp=str_replace('vDATA_ENVIO', 'NÃO ENVIADA', $resp);
    else if (  ($row->dataEnvioOperadora!='00/00/00') && ($row->dataEnvioOperadora!='') )
      $resp=str_replace('vDATA_ENVIO', $row->dataEnvioOperadora, $resp);
    else
      $resp=str_replace('vDATA_ENVIO', '??/??/??', $resp);

    if (  ($row->dataReenvioOperadora!='00/00/00') && ($row->dataReenvioOperadora!='') )
      $resp=str_replace('vDATA_REENVIO', $row->dataReenvioOperadora, $resp);
    else
      $resp=str_replace('vDATA_REENVIO', '-', $resp);          
          
    $resp=str_replace('checkedQTDEMENS_1', (($row->qtdeMENS==1) ? 'checked' : ''), $resp);
    $resp=str_replace('checkedQTDEMENS_2', (($row->qtdeMENS==2) ? 'checked' : ''), $resp);
    $resp=str_replace('checkedQTDEMENS_3', (($row->qtdeMENS==3) ? 'checked' : ''), $resp);

    $resp=str_replace('@dataCADASTRO',$row->dataCADASTRO, $resp);
    
    $resp=str_replace('@infoCADASTRO', "Cadastrada em: $row->dataCADASTRO ($row->nomeOPERADOR) ", $resp);
    
    $resp=str_replace('@numREG', $vlr, $resp);
    $resp=str_replace('@numregPropostaEntregueCaixa', $row->numregPropostaEntregueCaixa, $resp);
                
  }    
  else {  
    $resp=str_replace('vQTDEUSUARIOS', '' , $resp);
    $resp=str_replace('@sequencia', '', $resp);

    $resp=str_replace('vPROPOSTA', '', $resp);
    $resp=str_replace('vCONTRATO', '', $resp);
    $resp=str_replace('vCONTRATANTE', '' , $resp);    
    
    $resp=str_replace('vCPF', '', $resp);
    $resp=str_replace('vREPRESENTANTE', '', $resp);    
    $resp=str_replace('v_REPRESENTANTE', '', $resp);
    
    $resp=str_replace('vTIPO_CONTRATO', '', $resp);    
    $resp=str_replace('v_TIPO_CONTRATO', '', $resp);
    
    $resp=str_replace('vCOMISSAO_REPRESENTANTE', '', $resp);    
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', '', $resp);
    
    $resp=str_replace('vCOMISSAO_PRESTADORA', '', $resp);    
    $resp=str_replace('v_COMISSAO_PRESTADORA', '', $resp);   
    
    $resp=str_replace('vOPERADORA', '', $resp);    
    $resp=str_replace('v_OPERADORA', '', $resp);    

    $resp=str_replace('vvlrCONTRATO', '0,00', $resp);
    $resp=str_replace('vvlrADESAO', '0,00', $resp);        
    $resp=str_replace('vvlrTOTAL', '0,00', $resp);    
    $resp=str_replace('vvlrRECEBIDO', '0,00', $resp);    
    $resp=str_replace('vvlrPRODUCAO', '0,00', $resp);
    $resp=str_replace('vvlrPLANTAO', '0,00', $resp);
                                               
    $resp=str_replace('vASSINATURA', date("d/m/y"), $resp);    
    $resp=str_replace('vDATA_ENVIO', '-', $resp);
    $resp=str_replace('vDATA_REENVIO', '-', $resp);

    $resp=str_replace('@dataCADASTRO','', $resp);    
    $resp=str_replace('@infoCADASTRO', '' , $resp);

    $resp=str_replace('vNASCCONTRATANTE', '', $resp);
    $resp=str_replace('vSEXOC', '', $resp);
    $resp=str_replace('vENDERECO', '', $resp);
    $resp=str_replace('vEND_NUMERO', '', $resp);
    $resp=str_replace('vEND_COMPLEMENTO', '', $resp);

    $resp=str_replace('vBAIRRO', '', $resp);
    $resp=str_replace('v_BAIRRO', '', $resp);
    
    $resp=str_replace('vMUNICIPIO', '', $resp);
    $resp=str_replace('vUF', '', $resp);
    $resp=str_replace('vCEP', '', $resp);
    $resp=str_replace('vFONERES', '', $resp);
    $resp=str_replace('vFONECOM', '', $resp);
    $resp=str_replace('vCELULAR', '', $resp);
    $resp=str_replace('vEMAIL', '', $resp);
    $resp=str_replace('vFONE_RES', '', $resp);

    $resp=str_replace('checkedQTDEMENS_1', 'checked', $resp);
    $resp=str_replace('checkedQTDEMENS_2', '', $resp);
    $resp=str_replace('checkedQTDEMENS_3', '', $resp);

    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('@numregPropostaEntregueCaixa', '', $resp);
  }
  
  if ($acao!='incluirREG')  $resp .= '^' . $row->observacoes;
  else $resp .= '^';


  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $idUSUARIO = $infoUSUARIO[1]; 

  $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  $row = mysql_fetcH_object($resultado);
  $permissoes=$row->permissoes;

  // J = digita propostas
  if (strpos($permissoes, 'J')!==false) {
    $resp=str_replace('readonly ', '', $resp);
  }
  // U = ve proposta sem poder alterar
  if (strpos($permissoes, 'U')!==false) {
    $resp=str_replace('readonly_2', 'readonly', $resp);
    $resp = str_replace('v_PERMISSAO', '', $resp);
    $resp = str_replace('v_READONLY', 'readonly', $resp);
  }
  // Z = altera comissões
  if(strpos($permissoes, 'Z')) {
    $resp = str_replace('v_PERMISSAO', ", 'Z'", $resp);
    $resp = str_replace('v_READONLY', '', $resp);
  } else {
    $resp = str_replace('v_PERMISSAO', "", $resp);
    $resp = str_replace('v_READONLY', 'readonly', $resp);
  }
 

}


/*****************************************************************************************/
if ($acao=='calculoMENS') {
  $nascimento = $_REQUEST['nasc'];
  $plano = $_REQUEST['plano'];
  $assinatura = $_REQUEST['assinatura'];
  $tabela = $_REQUEST['tabela'];
  $qtdeUSUARIOS = $_REQUEST['qtdeUSUARIOS'];
  $debito = $_REQUEST['debito'];  
  
//  $assinatura = strtotime(date("Y-m-d", strtotime($assinatura)) . " +1 month");
  $assinatura = strtotime(date("Y-m-d", strtotime($assinatura)) );

  $idade = calculate_age($nascimento, date("Ymd", $assinatura), 0);
  
  $sql = "select precos, precos2, precos3, precos4, precos5, precos6, precos7, precos8 ".
        "  from precosplanos where numTABELA=$tabela and numPLANO=$plano";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp = '';
  
  if (mysql_num_rows($resultado)>0) { 
    $row = mysql_fetcH_object($resultado);
    
    if ( $idade<=18 ) $fx = 0;
    if ( $idade>=19 && $idade<=23 ) $fx = 1;    
    if ( $idade>=24 && $idade<=28 ) $fx = 2;
    if ( $idade>=29 && $idade<=33 ) $fx = 3;
    if ( $idade>=34 && $idade<=38 ) $fx = 4;
    if ( $idade>=39 && $idade<=43 ) $fx = 5;
    if ( $idade>=44 && $idade<=48 ) $fx = 6;
    if ( $idade>=49 && $idade<=53 ) $fx = 7;
    if ( $idade>=54 && $idade<=58 ) $fx = 8;
    if ( $idade>=59 ) $fx = 9;
    
    $pos = (7 * $fx);
    
    $qualPRECO=-1;
    if ($debito=='true') {
      if ($qtdeUSUARIOS==1) $qualPRECO=2;
      if ($qtdeUSUARIOS==2) $qualPRECO=4;
      if ($qtdeUSUARIOS==3) $qualPRECO=6;
      if ($qtdeUSUARIOS>=4) $qualPRECO=8;
    }
    else {
      if ($qtdeUSUARIOS==1) $qualPRECO=1;
      if ($qtdeUSUARIOS==2) $qualPRECO=3;
      if ($qtdeUSUARIOS==3) $qualPRECO=5;
      if ($qtdeUSUARIOS>=4) $qualPRECO=7;
    }
    if ($qualPRECO==1) $precos = $row->precos;
    if ($qualPRECO==2) $precos = $row->precos2;
    if ($qualPRECO==3) $precos = $row->precos3;        
    if ($qualPRECO==4) $precos = $row->precos4;    
    if ($qualPRECO==5) $precos = $row->precos5;    
    if ($qualPRECO==6) $precos = $row->precos6;    
    if ($qualPRECO==7) $precos = $row->precos7;    
    if ($qualPRECO==8) $precos = $row->precos8;    
 
    $vlrPAGAR = substr($precos, $pos, 7);
    
    /*
    if ($desconto>-1) {
      $desconto= substr($row->descontos, ($desconto * 7), 7);

      if (trim($desconto)!='')     $vlrPAGAR -= ( substr($precos, $pos, 7)* ($desconto/100) );
    }
    */
            
    $resp= $idade.'~'.str_replace('.', ',', number_format($vlrPAGAR, 2, ',', '')   );
  }                                    
}


/*****************************************************************************************/
IF ($acao=='verDUPLICIDADE') {
  $tipo = $_REQUEST['tipo'];
  $sql  = "select sequencia, numCONTRATO from propostas where numCONTRATO='$vlr' and idTipoContrato=$tipo";
  
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='ok';
  
  if (mysql_num_rows($resultado)>0)  {  
    $sequencia = $_REQUEST['sequencia'];

    if ($sequencia=='') $resp = 'jaCAD';
    else {
      $row = mysql_fetcH_object($resultado);      
      if ($row->sequencia != $sequencia) $resp = 'jaCAD';
    }
  }  
}

/*****************************************************************************************/
IF ($acao=='lerRepreEntregou') {

  $sql  = "select numPROP, ep.numREPRESENTANTE, rep.nome as nomeREPRESENTANTE ".
          "from propostasentregues pe ".
          "inner join entregaspropostas ep  ".
          "   on pe.nument = ep.numero " .
          "inner join representantes rep  ".
          "   on rep.numero = ep.numrepresentante " .          
          "where pe.numPROP=$vlr ";  
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $row = mysql_fetcH_object($resultado);
  $resp="$row->numREPRESENTANTE;$row->nomeREPRESENTANTE ($row->numREPRESENTANTE)"; 
}





/*****************************************************************************************/
IF ($acao=='futuras') {
  
  $sequencia = $_REQUEST['vlr'];     // ddmmyyyy
  
  $sql  = "select futuras.numREG as idFUTURA,ordem, date_format(dataVENCIMENTO, '%d/%m/%y') as dataVENCIMENTO, futuras.periodoAPURACAO, " . 
          "valor, situacaoPARCELA,  propostas.numCONTRATO, futuras.extrato, ifnull(pagaNoAdiantamento, 0) as pagaNoAdiantamento, " .
          " ifnull(marcadaParaPagarAdiantamento, 0) as marcadaParaPagarAdiantamento, " .
          "case situacaoPARCELA   " .
          "when '1' then concat('Pago em ', date_format(dataPGTOParcela, '%d/%m/%y'))  " .
          "when '2' then 'Em aberto'   " .
          "when '3' then concat('Não localizada em ', date_format(dataSituacaoParcela, '%d/%m/%y'))  " . 
          "when '4' then concat('Cancelada em ', date_format(dataSituacaoParcela, '%d/%m/%y'))  " .
          "else ''   " .
          "end as situacao, date_format(dataPgtoParcela, '%d/%m/%y') as dataPgtoParcela,  " .
          " baixas.nomearq, date_format(futuras.dataGeracaoRel, '%d/%m/%y') as dataGeracaoRel,   " .       
          "valorPagoParcela, comissaoREPRESENTANTE, futuras.opResponsavel " .        
          "from futuras ".
          "left join baixas " .
          "   on baixas.numreg = idArqBaixa ".
          "left join propostas " .
          "   on propostas.sequencia = futuras.sequencia ".
          " where futuras.sequencia=$sequencia ".
          "order by ordem " ;
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] ;
    
	$header = "$largura1 px,Mensalidades";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
    } else {    
      $largura1='';
    }
    $i++;
  
    $vlr = number_format($row->valor, 2, ',', '');
    $vlrPAGO='';
    if ($row->valorPagoParcela>0) $vlrPAGO = number_format($row->valorPagoParcela, 2, ',', '');
    
    $linkBAIXAR=$row->ordem;
    $funcaoBAIXAR='';

    if ($vlrPAGO=='' && $row->periodoAPURACAO=='' ) {
      $link='<font color=blue>BAIXAR</font>';
      $funcao='baixar(:idFUTURA, ":infoFUTURA", ":dataFUTURA")';

      if ($row->ordem==1) {
        if ($row->pagaNoAdiantamento==1) {
          $linkBAIXAR="$row->ordem";
          $funcaoBAIXAR='';
        }
        else {    
          if ($row->marcadaParaPagarAdiantamento==0 ) {
            $linkBAIXAR="$row->ordem<font color=blue><br>CLIQUE AQUI PARA ADIANTAR PGTO</font>";
            $funcaoBAIXAR='adiantar(:idFUTURA)';
          }else  {
            $linkBAIXAR="$row->ordem<font color=red><br>* MARCADA <br>PARA <br>ADTO *<br><b>CLIQUE AQUI PARA CANCELAR ADTO</b></font>";
            $funcaoBAIXAR='cancelar_adiantar(:idFUTURA)';
          }
        }
      }
    }
    else {
      $link='<font color=red>CANCELAR BAIXA</font>';
      $funcao='cancelar_baixa(:idFUTURA, ":infoFUTURA", ":dataFUTURA")';
    }
    $adto = $row->pagaNoAdiantamento==1 ? '(<font color=red>ADIANTAMENTO</font>)' : '';
    $dataRELATORIO = $row->dataGeracaoRel;
    $lin = "<tr title='Arquivo de baixa= $row->nomearq' onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" " . 
            "onmouseout=\"this.style.backgroundColor='#E6E8EE';\"   ><td><table width='100%'>" . 
            '<tr>'.
            " <td rowspan=2 width='10%' align=left onclick='$funcaoBAIXAR' $largura1>$linkBAIXAR</td>".
            " <td><table><tr><td>Vcto:</td><td width='80px' style='color:blue;'>$row->dataVENCIMENTO</td><td>Valor:</td><td width='80px' style='color:blue;'>$vlr</td>".
            " <td>Situação:</td><td width='130px' style='color:blue;'>$row->situacao</td><td>Valor pago:</td><td width='80px' style='color:blue;'>$vlrPAGO</td></tr></table></td>".
            " <td width='20%' align=center onclick='$funcao' rowspan=2>$link</td>".
            '</tr>'.
            '<tr>'.
            " <td><table><tr><td>Período apuração:</td><td width='180px' style='color:blue;'>$row->periodoAPURACAO</td>".
            " <td>Repasse:</td><td  width='100px' style='color:blue;'>$dataRELATORIO $adto</td><td>Extrato:</td>".
            " <td style='color:blue;'>$row->extrato</td></tr></table></td>".
            '</tr></table></td>'.
            "</tr>";

    $lin = str_replace(':idFUTURA', $row->idFUTURA, $lin);
    $lin = str_replace(':infoFUTURA', "Proposta: $row->numCONTRATO  Parcela: $row->ordem", $lin);
    $lin = str_replace(':dataFUTURA', $row->dataVENCIMENTO, $lin);

     $resp = $resp . ($lin);
  }
}

/*****************************************************************************************/
IF ($acao=='lerPARCELA') {
  
  $proposta = $_REQUEST['proposta'];     
  $parcela = $_REQUEST['parcela'];
  
  $sql  = "select date_format(dataVENCIMENTO, '%d/%m/%Y') as dataVENCIMENTO, " . 
          "valor, situacaoPARCELA,    " . 
          "case situacaoPARCELA   " .
          "when '1' then concat('Pago em ', date_format(dataPGTOParcela, '%d/%m/%y'))  " .
          "when '2' then 'Em aberto'   " .
          "when '3' then concat('Não localizada em ', date_format(dataSituacaoParcela, '%d/%m/%y'))  " . 
          "when '4' then concat('Cancelada em ', date_format(dataSituacaoParcela, '%d/%m/%y'))  " .
          "else ''   " .
          "end as situacao "  .
          "from futuras fut ".
          "inner join listadepropostas prop ".
          "   on prop.sequencia = fut.sequencia " .
          "where prop.numCONTRATO=$proposta and ordem = $parcela";

  //die($sql);          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp = 'INEXISTENTE';
  if (mysql_num_rows($resultado)>=1)  { 
    $row = mysql_fetcH_object($resultado);
    
    if ($row->situacaoPARCELA!='')  $resp=$row->situacao;
    else {
      $vlr = number_format($row->valor, 2, ',', '');
      $resp = "ok;$row->dataVENCIMENTO;$vlr";
    }  
  }  
}



/*****************************************************************************************/
IF ($acao=='gravarBAIXA') {
  
  $proposta = $_REQUEST['proposta'];     
  $parcela = $_REQUEST['parcela'];
  $dataPGTO = $_REQUEST['dataPGTO'];
  $valorPAGO = $_REQUEST['valorPAGO'];  
  $situacao = $_REQUEST['situacao'];
  
  $sql  = "select numREG  " . 
          "from futuras fut ".
          "inner join listadepropostas prop ".
          "   on prop.sequencia = fut.sequencia " .
          "where prop.numCONTRATO=$proposta and ordem = $parcela";

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  $resp = "PROPOSTA/PARCELA INEXISTENTE";
  if (mysql_num_rows($resultado)>=1)  { 
    $row = mysql_fetcH_object($resultado);
    
    $numREG = $row->numREG;    
  
    if ($situacao=='1')
      $sql = "update futuras set dataPGTOParcela='$dataPGTO', valorPAGOParcela=$valorPAGO, ".
                " opRESPONSAVEL=$logado, situacaoPARCELA='1', dataSituacaoParcela=now() where nuMREG = $numREG ";
    else
      $sql = "update futuras set situacaoPARCELA=$situacao, dataPGTOParcela=null, valorPAGOParcela=null, ".
                " opRESPONSAVEL=$logado, situacaoPARCELA='$situacao', dataSituacaoParcela=now() where nuMREG = $numREG ";
                     
    $resp = "ok";
    mysql_query($sql) or die(mysql_error());
  }  
}

      
/*****************************************************************************************/
IF ($acao=='pesquisa') {
  
  $cmp = $_REQUEST['cmp'];     
  $vlr = $_REQUEST['vlr'];  
  
  if ($cmp==1) 
    $sql = "select sequencia, dataCADASTRO, date_format(dataCADASTRO, '%d/%m/%Y') as dataMOSTRAR " .
            " from listadepropostas where sequencia=$vlr ";
            
  else if ($cmp==2) 
    $sql = "select sequencia, dataCADASTRO, date_format(dataCADASTRO, '%d/%m/%Y') as dataMOSTRAR " .
            " from listadepropostas where numCONTRATO=$vlr ";

  else if ($cmp==3) 
    $sql = "select lst.sequencia, dataCADASTRO, date_format(dataCADASTRO, '%d/%m/%Y') as dataMOSTRAR " .
            " from listadepropostas lst " .
            " inner join usuarios usu " .
            "    on lst.sequencia = usu.sequencia " .
            " where lst.contratante like '$vlr%' or usu.nome like '$vlr%' ";

  else if ($cmp==4) 
    $sql = "select lst.sequencia, dataCADASTRO, date_format(dataCADASTRO, '%d/%m/%Y') as dataMOSTRAR " .
              " from listadepropostas lst " .
            " inner join usuarios usu " .
            "    on lst.sequencia = usu.sequencia " .
            " where lst.contratante like '%$vlr%' or usu.nome like '%$vlr%' ";
             

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)>=1)  { 
    $row= mysql_fetcH_object($resultado);    
    $resp = "$row->dataMOSTRAR;$row->sequencia";
  } 
  else $resp='none';   
}

/*****************************************************************************************/
IF ($acao=='lerCEP') {
 $webservice_url     = 'http://webservice.kinghost.net/web_cep.php';
 $webservice_query    = array(
     'auth'    => '918f96a6f44de096e3bfe2da6745133d', //Chave de autenticação do WebService - Consultar seu painel de controle
     'formato' => 'query_string', //Valores possíveis: xml, query_string ou javascript
     'cep'     => $_REQUEST['vlr'] //CEP que será pesquisado
 );
 

 //Forma URL
 $webservice_url .= '?';
 foreach($webservice_query as $get_key => $get_value){
     $webservice_url .= $get_key.'='.urlencode($get_value).'&';
 }
 
  parse_str(file_get_contents($webservice_url), $resultado);

 switch($resultado['resultado']){  
   case '2':  
      $resp = 'ok;'.$resultado['cidade'].';'.$resultado['uf']; 
      break;  
     

   case '1':
     $bairro = $resultado['bairro'];
     $sql  = "select nome,numreg from bairro where ucase(nome) = ucase('$bairro')  ";
     $rsTMP = mysql_query($sql, $conexao) or die (mysql_error());
     
     if ( mysql_num_rows($rsTMP)>0 ) {   
        $regBAIRRO = mysql_fetcH_object($rsTMP);
        $bairro = $regBAIRRO->numreg;   
        $nomeBAIRRO = $regBAIRRO->nome;
     }
     // bairro nao existe na tabela 'bairro', insere 
     else {
       mysql_query("insert into bairro(nome) select '$bairro'") or  die (mysql_error());
          
       $nomeBAIRRO = $bairro;
       $bairro = mysql_insert_id();
     }  
                     
     $resp = 'ok'.$resultado['logradouro'].';'.$resultado['cidade'].';'.$resultado['uf'].';'.$bairro.';'.$nomeBAIRRO;
     mysql_free_result($rsTMP);
       
     break;  

   default:  
      $resp = "Falha ao buscar cep: ".$resultado['resultado']."\n\nProvavelmente este CEP está errado!";  
      break;  
  }
  
  die($resp);

}  


/*****************************************************************************************/
IF ($acao=='motivos') {
  
  $sequencia = $_REQUEST['vlr'];     // ddmmyyyy
  
  $sql  = "select numero, descricao ".  
          "from motivos_cancelamento where ativo='S' " .
          "order by descricao " ;
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.8;
    
	$header = "$largura1 px,Nº|$largura2 px,Descrição";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
    } else {    
      $largura1='';$largura2='';
    }
    $i++;
  
    $lin = "<tr onmousedown='gravarMOTIVO($row->numero);' onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" " . 
            "onmouseout=\"this.style.backgroundColor='#E6E8EE';\"   >" . 
            "<td align=\"left\" $largura1>&nbsp;$row->numero</td>".
            "<td align=\"left\" $largura2>$row->descricao</td>".
            "</tr>";
            
    $resp = $resp . ($lin);
  }
}
      



/*****************************************************************************************/
if ($acao=='gerarEMAILS') {

  $info = mysql_query("select email from propostas where email<>''", $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($info);

//  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".xls";
  $txt = "listaEMAILS.txt";
  $arqSAIDA = fopen("../ajax/txts/$txt", 'w');
  
  while ($row = mysql_fetcH_object($info)) {
    fwrite($arqSAIDA, "$row->email \n");
  }
 
  fclose($arqSAIDA);  
  mysql_free_result($info);

  die("ok;ajax/txts/$txt");
}

    
    


      


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();



?>


