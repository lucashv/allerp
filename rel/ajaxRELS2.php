<?phpheader("Content-Type: text/html; charset=iso-8859-1");session_start();require_once( '../includes/definicoes.php'  );require_once( '../includes/funcoes.php'  );require_once( '../includes/senha.php'  );$acao = $_REQUEST['acao'];if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];/* conexao */$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());$resp = 'INEXISTENTE';$rsDATA = mysql_query("select date_format(now(), '%d/%m/%y') as hoje, TIME_FORMAT(now(), '%H:%I') as agora ", $conexao)     or die (mysql_error());$row = mysql_fetcH_object($rsDATA);$hoje = $row->hoje;$agora = $row->agora;mysql_free_result($rsDATA);$MESES = array('Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho',          'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');/*****************************************************************************************/if ($acao=='inadimplencia') {  $dataIniMostrar = $_REQUEST['dataIniMostrar'];  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  $tipobusca= $_REQUEST['tipobusca'];  $busca = $tipobusca==1 ? 'propostas com parcelas vencidas' : 'propostas com data de cadastro';   $titREL = "Inadimpl�ncia ($busca) entre: $dataIniMostrar e $dataFinMostrar ";  $dataINI = $_REQUEST['DATAINI'];  $dataFIN = $_REQUEST['DATAFIN'];      $produto = $_REQUEST['prod'];  $repre = $_REQUEST['repre'];    $parcela1 = $_REQUEST['par1'];    $parcela2 = $_REQUEST['par2'];    $headers =       "                 Mensalidades     Mensalidades                                                                     Valor                         Numero                    |".      "Produto          pagas            n�o pagas        Corretor                     Contratante                       proposta  Telefones            Proposta           |".      str_repeat('-', 155);//     xxxxxxxxxxxxxxx  9,9,9,9,9,9,9,9  9,9,9,9,9,9,9,9  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxzxxxxxxxxxxxxxxxxxxxxxx  999.999,99  xxxxxxxxxxxxxxxxxxx                                                                                                                               $sql = "select ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, tip.descricao as nomePRODUTO, " .         "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .         "   prop.contratante, prop.idREPRESENTANTE,  prop.foneRES, prop.foneCOM, prop.foneCEL, prop.vlrCONTRATO,  " .          " date_format(fut.dataVENCIMENTO, '%d/%m/%y') as dataVENCIMENTO, fut.ordem, prop.sequencia,  ".          " ifnull(fut.situacaoPARCELA, 0) as situacaoMOSTRAR, fut.valor as vlrPARCELA,  prop.numCONTRATO, ".          " ifnull(prop.cancelada,'N') as cancelada ".         "from propostas prop " .         "left join operadoras opa " .         "   on opa.numreg = prop.idOPERADORA " .         "left join representantes repre " .         "   on repre.numero = prop.idREPRESENTANTE " .         "left join tipos_contrato tip ".         "  on tip.numreg=prop.idTipoContrato ".         "left join futuras fut ".         "  on fut.sequencia = prop.sequencia ".         " where prop.sequencia in @criterioBUSCA and ifnull(cancelada,'N')<>'S' ".        "  and ordem between $parcela1 and $parcela2  @criterioREPRE @criterioPRODUTO ".         " order by sequencia";             // busca cotnratos com mensalidades que vencem no periodo e que nao foram pagas ainda  if ($tipobusca==1)    $sql = str_replace('@criterioBUSCA',       " (select sequencia from futuras where date_format(datavencimento, '%Y%m%d') between  '$dataINI' and '$dataFIN' ".      " and ifnull(situacaoparcela,0)<>1 ) " , $sql);  // busca cotnratos com data de cadastro no periodo e que nao foram pagas ainda  else    $sql = str_replace('@criterioBUSCA',       "(select distinct propostas.sequencia from propostas ".      "inner join futuras ".      "   on futuras.sequencia=propostas.sequencia  ".      "where date_format(datacadastro, '%Y%m%d') ".       " between  '$dataINI' and '$dataFIN' and ifnull(situacaoparcela,0)<>1 )  " , $sql);            //" and date_format(fut.datavencimento, '%Y%m%d') between  '$dataINI' and '$dataFIN' @criterioREPRE @criterioPRODUTO ".  if ($repre=='9999' || $repre=='')   $sql = str_replace('@criterioREPRE', '', $sql);  else  $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$repre " , $sql);  if ($produto=='9999') $sql = str_replace('@criterioPRODUTO', '', $sql);  else $sql = str_replace('@criterioPRODUTO', " and prop.idTipoContrato=$produto " , $sql);//die($sql);  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  $sequenciaATUAL=-1;  $totnaopagas=0;   $totpagas=0;  while ($row = mysql_fetch_object($resultado)) {    if ($row->cancelada=='S') continue;          if ($sequenciaATUAL!=$row->sequencia ) {      if ( $sequenciaATUAL!=-1 ) {        $pagas = (strlen($pagas)>15) ? (substr($pagas, 0, 12) . '...') : $pagas ;        $naopagas = (strlen($naopagas)>15) ? (substr($naopagas, 0, 12) . '...') : $naopagas;        if ($lin + 1 > 55)    cabecalho();        fwrite($Arq, $produto .'  ' .                        substr(str_pad($pagas, 15, ' ', 1), 0, 15) .'  ' .                        substr(str_pad($naopagas, 15, ' ', 1), 0, 15) .'  ' .                        $repreATUAL .'  ' .                        $contratante .'  ' .                          str_pad($valor, 10, ' ', 0) .'  ' .                        substr(str_pad($telefones, 19, ' ', 1), 0, 19) .'  '.                        $row->numCONTRATO ."\n" );      }      $pagas=''; $naopagas='';      $lin++;      $sequenciaATUAL=$row->sequencia;    }    if ($row->situacaoMOSTRAR==1) {       $pagas .= ($pagas=='' ? '' : ',') . $row->ordem;      $totpagas += $row->vlrPARCELA;    }    else {      $naopagas .= ($naopagas=='' ? '' : ',') . $row->ordem;      $totnaopagas += $row->vlrPARCELA;    }    $repreATUAL = substr(str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 27, ' ', 1), 0, 27);    $contratante = substr(str_pad($row->contratante, 30, ' ', 1), 0, 30);    $produto = substr(str_pad($row->nomePRODUTO, 15, ' ', 1), 0, 15);    $valor = number_format($row->vlrCONTRATO, 2, ',', '.');    $telefones='';    if ($row->foneRES!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneRES;    if ($row->foneCOM!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCOM;    if ($row->foneCEL!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCEL;      }  if ($lin + 2 > 55)    cabecalho();  fwrite($Arq, "\n");  fwrite($Arq, "Total pagas R$: ".number_format($totpagas, 2, ',', '.').'   '.                "Total nao pagas R$: ".number_format($totnaopagas, 2, ',', '.') );  fclose($Arq);}/*****************************************************************************************/if ($acao=='repre_CPFs') {  $titREL = "Relat�rio de corretores (CPFs)";  $headers =       "Corretor                                  RG                    CPF|".      str_repeat('-', 160);//      xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx xxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxx                                                                                                                                    $sql = "select nome , cpf, rg, numero " .         "from representantes rep " .         " where ifnull(ativo, '')='S' order by nome ";         $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 55)    cabecalho();    $repre = substr($row->nome, 0, 35). '('.$row->numero.')';    $CPF = rtrim(trim($row->cpf));    if ($CPF!='') {      $CPF=substr_replace($CPF, '-', 9, 0);$CPF = substr_replace($CPF, '.', 6, 0);$CPF = substr_replace($CPF, '.', 3, 0);    }    fwrite($Arq, str_pad($repre, 40, ' ', 1). '  '.                 str_pad($row->rg, 20, ' ', 1). '  '.                 $CPF ."\n");    $lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='clientes_mensalidades') {  $dataIniMostrar = $_REQUEST['dataIniMostrar'];  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  $titREL = "Clientes com mensalidades entre: $dataIniMostrar e $dataFinMostrar ";  $dataINI = $_REQUEST['DATAINI'];  $dataFIN = $_REQUEST['DATAFIN'];  $produto = $_REQUEST['prod'];  $repre = $_REQUEST['repre'];    $headers =       "Produto               Mensalidade     Corretor                             Contratante                 Valor proposta  Telefones                |".      str_repeat('-', 155);//     xxxxxxxxxxxxxxx       99/99/99 (99�)  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxzxxxxxxxxxxxxxxxxxxxxxx  999.999,99      xxxxxxxxxxxxxxxxxxx                                                                                                                           $sql = "select ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, tip.descricao as nomePRODUTO, " .         "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .         "   prop.contratante, prop.idREPRESENTANTE,  prop.foneRES, prop.foneCOM, prop.foneCEL, prop.vlrCONTRATO,  " .          " date_format(fut.dataVENCIMENTO, '%d/%m/%y') as dataVENCIMENTO, fut.ordem ".         "from propostas prop " .         "left join operadoras opa " .         "   on opa.numreg = prop.idOPERADORA " .         "left join representantes repre " .         "   on repre.numero = prop.idREPRESENTANTE " .         "left join tipos_contrato tip ".         "  on tip.numreg=prop.idTipoContrato ".         "left join futuras fut ".         "  on fut.sequencia = prop.sequencia ".         " where prop.sequencia in (select sequencia from futuras where date_format(datavencimento, '%Y%m%d') between  '$dataINI' and '$dataFIN') ".        " and date_format(fut.datavencimento, '%Y%m%d') between  '$dataINI' and '$dataFIN' @criterioREPRE @criterioPRODUTO ".         " order by contratante";         if ($repre=='9999' || $repre=='')   $sql = str_replace('@criterioREPRE', '', $sql);  else  $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$repre " , $sql);  if ($produto=='') $sql = str_replace('@criterioPRODUTO', '', $sql);  else $sql = str_replace('@criterioPRODUTO', " and prop.idTipoContrato=$produto " , $sql);  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 55)    cabecalho();    $valor = number_format($row->vlrCONTRATO, 2, ',', '.');    $telefones='';    if ($row->foneRES!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneRES;    if ($row->foneCOM!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCOM;    if ($row->foneCEL!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCEL;    $mens = $row->dataVENCIMENTO . " (".str_pad($row->ordem, 2, ' ', 0) .'�)';      fwrite($Arq, str_pad(                    substr(str_pad($row->nomePRODUTO, 20, ' ', 1), 0, 20) .'  ' .                    $mens .'  ' .                    substr(str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 35, ' ', 1), 0, 35) .'  ' .                    substr(str_pad($row->contratante, 30, ' ', 1), 0, 30) .'  ' .                      str_pad($valor, 10, ' ', 0) .'  ' .                    $telefones, 155, ' ', 1) ."\n");    $lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='resumoPgtoPorBanco') {  $titREL = "Relat�rio de pagamentos a corretores";  $infoRELAT = $_REQUEST['rel'];  $infoRELAT2 = $_REQUEST['rel2'];  $titulos= 'Relat�rio '.$infoRELAT.'|'.$infoRELAT2;  $headers =   "Corretor                   Banco                                Ag�ncia     Opera��o    N� conta    Favorecido                           Valor pagar  Pago?|".  str_repeat('-', 160);// xxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999.999,99                                                                                                                                    $sql = "select rep.nome as nomeREPRE, ban.nome as nomeBANCO, idBANCO, agencia, operacao, num_conta, favorecido, rep.numero as idREPRE, pag.valor, " .         " ifnull(pago,0) as jaPAGO ".         "from representantes rep " .         "left join bancos ban " .         "   on ban.numero = rep.idBANCO " .         " inner join pagamentos_corretor pag " .          "    on pag.idRELATORIO=$vlr and pag.idREPRESENTANTE=rep.numero ".          "where idBANCO is not null and pag.valor>0 " .         " order by idBANCO ";         $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  $bancoATUAL=''; $totBANCO=0; $totBANCO_GERAL;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 38)    cabecalho();    if ($bancoATUAL!=$row->idBANCO ) {      if ($bancoATUAL!='') {        if ($lin + 1 > 38)    cabecalho();        fwrite($Arq, str_pad('<negrito> TOTAL:', 145, ' ', 0). '  '.                     str_pad(number_format($totBANCO, 2, ',', '.'), 10, ' ', 0) . "\n");        $lin++;      }      $bancoATUAL=$row->idBANCO; $totBANCO=0;    }    $banco = substr($row->nomeBANCO, 0, 30). '('.$row->idBANCO.')';    $repre = substr($row->nomeREPRE, 0, 20). '('.$row->idREPRE.')';    $totBANCO += $row->valor;    $totBANCO_GERAL += $row->valor;    $jaPAGO = $row->jaPAGO==1 ? 'Sim' : 'N�o';    fwrite($Arq, str_pad($repre, 25, ' ', 1). '  '.                 str_pad($banco, 35, ' ', 1). '  '.                 str_pad($row->agencia, 10, ' ', 1) . '  '.                 str_pad($row->operacao, 10, ' ', 1) . '  '.                 str_pad($row->num_conta, 10, ' ', 1) . '  '.                 str_pad($row->favorecido, 36, ' ', 1) .'  '.                 str_pad(number_format($row->valor, 2, ',', '.'), 10, ' ', 0) . '  '.                  $jaPAGO . "\n");    $lin++;  }  if ($lin + 2 > 55)    cabecalho();  fwrite($Arq, str_pad('<negrito> TOTAL:', 145, ' ', 0). '  '.               str_pad(number_format($totBANCO, 2, ',', '.'), 10, ' ', 0) . "\n");  fwrite($Arq, str_pad('<negrito> TOTAL GERAL:', 145, ' ', 0). '  '.               str_pad(number_format($totBANCO_GERAL, 2, ',', '.'), 10, ' ', 0) . "\n");  fclose($Arq);}/*****************************************************************************************/IF ($acao=='recibo') {  $infoRELAT = $_REQUEST['rel'];  $infoRELAT2 = $_REQUEST['rel2'];  $sql  = "select idREPRESENTANTE, repre.nome as nomeREPRESENTANTE, valor " .          "from pagamentos_corretor pgto ".          "left join representantes repre " .          "   on repre.numero = pgto.idREPRESENTANTE ".          " where pgto.idRELATORIO = $vlr ".          "order by repre.nome " ;              $resultado = mysql_query($sql, $conexao) or die (mysql_error());    $titREL = "Resumo de pagamentos";  $titulos= 'Relat�rio '.$infoRELAT.'|'.$infoRELAT2;   $headers =       "Corretor                                            Comiss�o    |".      str_repeat('-', 80);//     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999.999,99                                                                                                                                        $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 55)    cabecalho();    fwrite($Arq, str_pad("$row->nomeREPRESENTANTE ($row->idREPRESENTANTE)", 50, ' ', 1). '  '.                 str_pad(number_format($row->valor, 2, ',', '.'), 10, ' ', 0) . "\n");    $lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='contas') {  $titREL = "Relat�rio de contas banc�rias";  $headers =       "Corretor                        Banco                                     Ag�ncia     Opera��o    N� conta    Favorecido               |".      str_repeat('-', 160);//     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx                                                                                                                                    $sql = "select rep.nome as nomeREPRE, ban.nome as nomeBANCO, idBANCO, agencia, operacao, num_conta, favorecido, rep.numero as idREPRE " .         "from representantes rep " .         "left join bancos ban " .         "   on ban.numero = rep.idBANCO " .         "where idBANCO is not null " .         " order by idBANCO ";         $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 55)    cabecalho();    $banco = substr($row->nomeBANCO, 0, 30). '('.$row->idBANCO.')';    $repre = substr($row->nomeREPRE, 0, 25). '('.$row->idREPRE.')';    fwrite($Arq, str_pad($repre, 30, ' ', 1). '  '.                 str_pad($banco, 40, ' ', 1). '  '.                 str_pad($row->agencia, 10, ' ', 1) . '  '.                 str_pad($row->operacao, 10, ' ', 1) . '  '.                 str_pad($row->num_conta, 10, ' ', 1) . '  '.                 str_pad($row->favorecido, 40, ' ', 1)  ."\n");    $lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='posvenda') {  $dataIniMostrar = $_REQUEST['dataIniMostrar'];  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  $titREL = "Propostas cadastradas entre: $dataIniMostrar e $dataFinMostrar ";  $dataINI = $_REQUEST['DATAINI'];  $dataFIN = $_REQUEST['DATAFIN'];  $produto = $_REQUEST['prod'];  $repre = $_REQUEST['repre'];    $headers =       "                 Data de                                                                                                         |".                "Produto          cadastro  Corretor                             Contratante                      Valor proposta  Telefones                |".      str_repeat('-', 155);//     xxxxxxxxxxxxxxx  99/99/99  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999.999,99      xxxxxxxxxxxxxxxxxxx                                                                                                                           $sql = "select ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, tip.descricao as nomePRODUTO, " .         "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .         "   prop.contratante, prop.idREPRESENTANTE,  prop.foneRES, prop.foneCOM, prop.foneCEL, prop.vlrCONTRATO  " .         "from propostas prop " .         "left join operadoras opa " .         "   on opa.numreg = prop.idOPERADORA " .         "left join representantes repre " .         "   on repre.numero = prop.idREPRESENTANTE " .         "left join tipos_contrato tip ".         "  on tip.numreg=prop.idTipoContrato ".         " where date_format(dataCADASTRO, '%Y%m%d') between  '$dataINI' and '$dataFIN' @criterioREPRE @criterioPRODUTO ".         " order by contratante";         if ($repre=='9999' || $repre=='')   $sql = str_replace('@criterioREPRE', '', $sql);  else  $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$repre " , $sql);  if ($produto=='') $sql = str_replace('@criterioPRODUTO', '', $sql);  else $sql = str_replace('@criterioPRODUTO', " and prop.idTipoContrato=$produto " , $sql);  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  while ($row = mysql_fetch_object($resultado)) {      if ($lin + 1 > 55)    cabecalho();    $valor = number_format($row->vlrCONTRATO, 2, ',', '.');    $telefones='';    if ($row->foneRES!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneRES;    if ($row->foneCOM!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCOM;    if ($row->foneCEL!='') $telefones .= ($telefones=='' ? '' : ', ') . $row->foneCEL;    fwrite($Arq, str_pad(                    substr(str_pad($row->nomePRODUTO, 15, ' ', 1), 0, 15) .'  ' .                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .                    substr(str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 35, ' ', 1), 0, 35) .'  ' .                    substr(str_pad($row->contratante, 35, ' ', 1), 0, 35) .'  ' .                      str_pad($valor, 10, ' ', 0) .'  ' .                    $telefones, 155, ' ', 1) ."\n");    $lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='lerPERIODOS') {  $operadora=$_REQUEST['op'];  $sql = "select numREG, date_format(mes_ano, '%m') as mes, date_format(mes_ano, '%Y') as ano, ".          " date_format(dataini_conf, '%d/%m/%y') as dataini_conf, date_format(datafin_conf, '%d/%m/%y') as datafin_conf, ".          " date_format(dataini_vales, '%d/%m/%y') as dataini_vales, date_format(datafin_vales, '%d/%m/%y') as datafin_vales, ".         " date_format(dataPGTO, '%d/%m/%y') as dataMOSTRAR ".          "from periodos_pgto ".          " where ifnull(dataPGTO, '')<>'' and idOPERADORA=$operadora  " .          'order by dataPGTO desc limit 10';  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  $resp = '<select style="width:450px;" id="lstPERIODOS" onchange="mudouPERIODO()">';        $i=0;  if (mysql_num_rows($resultado)>0) {     while ($row = mysql_fetcH_object($resultado)) {      if ($row->mes!='')        $info = $MESES[ ((int)$row->mes)-1 ] . '/' . $row->ano . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.                ' Pagamento em '.$row->dataMOSTRAR;      else        $info = "Semana de $row->dataini_conf a $row->datafin_conf &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".                ' Pagamento em '.$row->dataMOSTRAR;      $lin = "<option @mudaCOR value='$row->dataini_conf|$row->datafin_conf|$row->dataini_vales|$row->datafin_vales|$row->numREG'>".              "$info</option>";                      $i++;      if ($i % 2==0)        $lin= str_replace('@mudaCOR', 'style="color:blue;background-color:lightgrey;font-size:13px;padding-top:0px;height:15px;"  ', $lin);      else        $lin= str_replace('@mudaCOR', 'style="color:blue;background-color:white;font-size:13px;padding-top:0px;height:15px;"  ', $lin);      $resp .= $lin;    }  }  else $resp .= "<option value=9999>NENHUM PER�ODO DEFINIDO</option>";  $resp.='</select>';}  /*****************************************************************************************/if ($acao=='senhas') {    $sql  = "select numero, nome, senha  ".          "from representantes ".          "where ifnull(ativo,'N')='S'  ".          "order by nome";  $titREL = "Senha para software de comissoes";  $titulos="";    $headers=    '             |'.	  "N�     Nome                                 Senha|".    str_repeat('-', 100 );//   99999  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxx          $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');	$pagina = 0;    $lin = 200;  $soma=0;	  while ($row = mysql_fetcH_object($resultado)) {		$info=str_pad($row->numero, 5, ' ', 0) .' ' .					str_pad($row->nome, 35, ' ', 1) .'   ' .          $row->senha ." \n";		if ($lin + 1 > 55)   cabecalho();    fwrite($Arq, $info );		$lin++;  }  fclose($Arq);}/*****************************************************************************************/if ($acao=='nuncaPAGOS') {  $dataIniMostrar = $_REQUEST['dataIniMostrar'];  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  $idREPRE = $_REQUEST['repre'];  $dataINI = $_REQUEST['DATAINI'];  $dataFIN = $_REQUEST['DATAFIN'];      $idOPERADORA = $_REQUEST['ope'];  $nomeOPERADORA = $_REQUEST['nomeope'];  $excecoesOPERADORAS = $_REQUEST['excecoes']=='' ? '9999' : $_REQUEST['excecoes'];  $infoADD='';   if ($idOPERADORA!='') $infoADD = "  - OPERADORA: $nomeOPERADORA ";  $titREL = "Propostas (cadastradas entre: $dataIniMostrar e $dataFinMostrar) sem nenhuma confirma��o banc�ria $infoADD ";  $headers =       "                                    Data de                                                               Valor do   Valor     Valor      Valor   Data|".                "Operadora        Produto            cadastro  Corretor                    Segurado                        contrato  recebido  produ��o   plant�o  envio|".     str_repeat('-', 155);//    xxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxx  99/99/99  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxxx            99999,99  99999,99  99999,99  99999,99          // a subquery no interior da query principal le campo "sequencia" de propostas que ja tenham qq parcela paga  // excecao: se operadora do contrato tem a tal regra mostrada na tela de geracao do relat, nao considera como paga  // a 1a parcela quando qtde vidas do contrato < 100    $sql = "select ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, ifnull(contra.descricao, '* ERRO *') as tipoCONTRATO, " .         "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .         "   prop.contratante, prop.vlrCONTRATO, prop.vlrRECEBIDO, prop.vlrPRODUCAO, prop.idREPRESENTANTE, prop.vlrPLANTAO, " .         "   date_format(prop.dataEnvioOperadora, '%d/%m/%y') as dataEnvioOperadora " .         "from propostas  prop " .         "left join  ( ".         "  select distinct prop2.sequencia  ".         "  from propostas prop2  ".         "  inner join futuras fut ".         "    on fut.sequencia = prop2.sequencia ".         " where ifnull(fut.situacaoPARCELA,0)=1 and ".         "    (  (fut.ordem>1 and prop2.idOPERADORA in ($excecoesOPERADORAS) and prop2.qtdeVIDAS<100) or ".         "          (prop2.idOPERADORA not in ($excecoesOPERADORAS)) ) ".         "    and date_format(prop2.dataCADASTRO, '%Y%m%d') between '$dataINI' and '$dataFIN' ".          "     @criterioOPERADORA_1 @criterioREPRE_1 )".         "as propPAGAS ".         "on propPAGAS.sequencia=prop.sequencia ".         "left join operadoras opa " .         "   on opa.numreg = prop.idOPERADORA " .         "left join tipos_contrato contra " .         "   on contra.numreg = prop.idTipoContrato " .         "left join representantes repre " .         "   on repre.numero = prop.idREPRESENTANTE " .         "where (propPAGAS.sequencia is null or propPAGAS.sequencia = '')  @criterioOPERADORA_2 @criterioREPRE_2 ".         " and date_format(prop.dataCADASTRO, '%Y%m%d') between '$dataINI' and '$dataFIN'  ".                   " order by prop.idREPRESENTANTE, prop.contratante ";  if ($idOPERADORA!='') {    $sql = str_replace('@criterioOPERADORA_1', " and prop2.idOPERADORA=$idOPERADORA", $sql);    $sql = str_replace('@criterioOPERADORA_2', " and prop.idOPERADORA=$idOPERADORA", $sql);  }  else {    $sql = str_replace('@criterioOPERADORA_1', '', $sql);    $sql = str_replace('@criterioOPERADORA_2', '', $sql);  }  if ($idREPRE!='9999') {    $sql = str_replace('@criterioREPRE_1', " and prop2.idREPRESENTANTE=$idREPRE", $sql);    $sql = str_replace('@criterioREPRE_2', " and prop.idREPRESENTANTE=$idREPRE", $sql);  }  else {    $sql = str_replace('@criterioREPRE_1', '', $sql);    $sql = str_replace('@criterioREPRE_2', '', $sql);  }  $resultado = mysql_query($sql, $conexao) or die (mysql_error());  if (mysql_num_rows($resultado)==0) die('nada');   $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";  $Arq = fopen("../ajax/txts/$txt", 'w');  $pagina = 0;  $lin = 87;  $repreATUAL = 'none';  $totG_CONTRATO=0;     $totG_PRODUCAO=0;      $totG_RECEBIDO=0;      $totG_PLANTAO=0;      $qtdeG_CONTRATO=0;  while ($row = mysql_fetch_object($resultado)) {      if ($row->idREPRESENTANTE!=$repreATUAL) {           if ($lin + 2 > 55) cabecalho();      if ($repreATUAL!='none') {        $qtdeCONTRATO = str_pad($qtdeCONTRATO, 5, ' ', 0);          fwrite($Arq, '<negrito>'.str_pad("    TOTAL:     $qtdeCONTRATO   ", 106, ' ', 0) .                    str_pad(number_format($totCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .                    str_pad(number_format($totRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .                    str_pad(number_format($totPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .                    str_pad(number_format($totPLANTAO, 2, ',', ''), 8, ' ', 0) . "                                                 \n");        $lin++;       }      $totCONTRATO=0;     $totPRODUCAO=0;      $totRECEBIDO=0;      $totPLANTAO=0;      $qtdeCONTRATO=0;      $repreATUAL = $row->idREPRESENTANTE;    }            if ($lin + 1 > 55)    cabecalho();    $totCONTRATO += $row->vlrCONTRATO;    $totPRODUCAO += $row->vlrPRODUCAO;    $totPLANTAO += $row->vlrPLANTAO;    $totRECEBIDO += $row->vlrRECEBIDO;    $qtdeCONTRATO++;    $totG_CONTRATO += $row->vlrCONTRATO;    $totG_PRODUCAO += $row->vlrPRODUCAO;    $totG_PLANTAO += $row->vlrPLANTAO;    $totG_RECEBIDO += $row->vlrRECEBIDO;    $qtdeG_CONTRATO++;      fwrite($Arq, substr(str_pad($row->nomeOPERADORA, 15, ' ', 1), 0, 15) .'  ' .                  substr(str_pad($row->tipoCONTRATO, 17, ' ', 1), 0, 17) .'  ' .                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .                  str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 26, ' ', 1) .'  ' .                  substr(str_pad($row->contratante, 30, ' ', 1), 0, 30) .'  ' .                  str_pad(number_format($row->vlrCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .                  str_pad(number_format($row->vlrRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .                  str_pad(number_format($row->vlrPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .                  str_pad(number_format($row->vlrPLANTAO, 2, ',', ''), 8, ' ', 0) .'  ' .                  str_pad($row->dataEnvioOperadora, 8, ' ', 1) ."\n");    $lin++;  }  if ($lin + 2 > 55)    cabecalho();  $qtdeCONTRATO = str_pad($qtdeCONTRATO, 5, ' ', 0);  fwrite($Arq, '<negrito>'.str_pad(" TOTAL:     $qtdeCONTRATO   ", 106, ' ', 0) .              str_pad(number_format($totCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totPLANTAO, 2, ',', ''), 8, ' ', 0) . "                                                 \n");  if ($lin + 2 > 55)    cabecalho();  $qtdeCONTRATO = str_pad($qtdeG_CONTRATO, 5, ' ', 0);  fwrite($Arq, '<negrito>'.str_pad(" GERAL:     $qtdeCONTRATO   ", 106, ' ', 0) .              str_pad(number_format($totG_CONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totG_RECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totG_PRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .              str_pad(number_format($totG_PLANTAO, 2, ',', ''), 8, ' ', 0) . "                                                 \n");  fclose($Arq);}/*****************************************************************************************//* fecha conexao */if ( isset($resultado) )  mysql_free_result($resultado);mysql_close($conexao);echo ($resp); die();/*****************************************************************************************/function cabecalho() {global $pagina, $Arq, $titREL, $titulos, $row, $lin, $acao, $hoje, $agora, $headers;if ($pagina>0)   fwrite($Arq,  "FIM PAGINA \n" );  $pagina ++;fwrite($Arq,  $_SESSION['empresa'] ."\n"); fwrite($Arq,  "$titREL \n");$lin=2;if ($titulos!='') { $tit = explode('|', $titulos); for ($r=0; $r<count($tit); $r++) {   fwrite($Arq, "$tit[$r] \n");      $lin++;   }}        fwrite($Arq,  str_repeat('=', 80) . "\n");      fwrite($Arq,  "Relat�rio emitido em: $hoje as $agora          P�GINA: $pagina \n");fwrite($Arq,  str_repeat('=', 80) . "\n");$lin += 3;$header = explode('|', $headers);for ($e=0; $e<count($header); $e++) {  fwrite($Arq, "$header[$e] \n");    $lin++;  }}?>