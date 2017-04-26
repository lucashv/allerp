<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = trim($_REQUEST['vlr']);

/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
$logado = $logado[1];

// le qual ult plantao manipulado pelo usuario
$resultado = mysql_query("select ifnull(ultPlantaoManipulado,'Medicina') as info from operadores ". 
                          " where numero=$logado", $conexao) or die (mysql_error());
$row = mysql_fetcH_object($resultado);
$lendoATUAL=$row->info;

if ($lendoATUAL=='Medicina') {
  $cmpCONFIG='corretorDaVezPlantao';
  $tabPLANTAO = 'plantao';
  $tabLIGACOES = 'ligacoes';
  $cmpUltOrdemPlantao = 'ultOrdemPlantao';
}
else if ($lendoATUAL=='Odontologia') {
  $cmpCONFIG='corretorDaVezPlantao_ODONTO';
  $tabPLANTAO = 'plantao_ODONTO';
  $tabLIGACOES = 'ligacoes_ODONTO';
  $cmpUltOrdemPlantao = 'ultOrdemPlantao_ODONTO';
}
else if ($lendoATUAL=='Indicações') {
  $cmpCONFIG='';
  $tabPLANTAO = '';
  $tabLIGACOES = 'indicacoes_atendimento';
}
else if ($lendoATUAL=='Atendimento Presencial/demais ligações') {
  $cmpCONFIG='';
  $tabPLANTAO = '';
  $tabLIGACOES = 'presencial_atendimento';
}
else if ($lendoATUAL=='Clinipam') {
  $cmpCONFIG='corretorDaVezPlantao_CLINIPAM';
  $tabPLANTAO = 'plantao_CLINIPAM';
  $tabLIGACOES = 'ligacoes_OPCLINIPAM';
  $cmpUltOrdemPlantao = 'ultOrdemPlantao_CLINIPAM';
}
$resp = 'INEXISTENTE';


/*****************************************************************************************/
if ($acao=='verOPERADOR_ALTERA_RESP') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  if ($logado==1) $resp='sim';
  else {
    $sql = 'select permissoes '   . 
           "from operadores ". 
           "where numero=$logado";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    
    $row = mysql_fetcH_object($resultado);
    $resp='nao';
    if (strpos($row->permissoes, 'S')!==false) $resp='sim';
  }  
}

/*****************************************************************************************/
if ($acao=='operadoresINDICACOES') {
  $sql = 'select nome,numero,permissoes '   . 
         "from operadores ". 
         "order by nome";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  // S, T     pessoas manipulam indicacoes
  $resp='';
  while ($row = mysql_fetcH_object($resultado)) {
    if (strpos($row->permissoes, 'T')!==false) {
      $resp.= $resp=='' ?  '' : "\n";
      $resp .= "$row->nome ($row->numero)";
    }
  }
  $resp .= "\n";  
}



/*****************************************************************************************/
if ($acao=='verOPERADOR') {
  $sql = 'select nome, numero  '   . 
         "from operadores ". 
         "where numero=$vlr";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) $resp='nao';
  else {
    $row = mysql_fetcH_object($resultado);
    $resp="$row->nome ($row->numero)";
  }
}


/*****************************************************************************************/
if ($acao=='mudarRESPONSAVEL') {
  $idNOVO_OPERADOR = $_REQUEST['novo'];

  $sql = "update indicacoes_atendimento set idOPERADOR=$idNOVO_OPERADOR ".
          " where numreg=$vlr";

  mysql_query($sql) or die($sql . '<br><br>'.mysql_error());
  
  mysql_close($conexao);
  echo $resp; die('ok' );
}  
           


/*****************************************************************************************/
if ($acao=='mostrarPLANTAO') {
  $sql = 'select pl.numREG, pl.idREPRESENTANTE, repre.nome, pl.numreg, pl.ramal, pl.bloqueado, '   .
         'hour(timediff(ultatendimento,now())) as horas, minute(timediff(ultatendimento,now())) as minutos, '.
          ' second(timediff(ultatendimento,now())) as segundos '. 
         "from $tabPLANTAO pl ". 
         'left join representantes repre '.
         '    on  repre.numero=pl.idREPRESENTANTE  '.
         'order by numreg ';

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $tab="<table cellpadding=3 cellspacing=0 bgcolor=lightgrey border=1 style='font-family:ms sans serif;font-size:16px;' width='750px' height='200px' ><tr bgcolor=white>".
        "<td width='50%'>Corretor</td><td>Última ligação</td></tr>";  
  
  $ordem=1;
  while ($row = mysql_fetcH_object($resultado)) {

    $bloqueado=$row->bloqueado==1 ? ' - <font color=red><b>BLOQUEADO</b></font>' : '';

    $ultima ='';
    if ($row->horas>0) $ultima=$row->horas . ($row->horas==1 ? ' hora' : ' horas');
    if ($row->minutos>0) { 
      $ultima .= $ultima=='' ? '' : ', ';
      $ultima .= $row->minutos . ($row->minutos==1 ? ' minuto' : ' minutos');
    }
    if ($row->segundos>0) {
      $ultima .= $ultima=='' ? '' : ', ';
      $ultima .= $row->segundos . ($row->segundos==1 ? ' segundo' : ' segundos');
    } 
    $lin = "<tr id='$row->numREG' onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
            ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
            " onclick='setarATUAL($row->numREG);' > ".
            "<td>$ordem - $row->nome ($row->idREPRESENTANTE)$bloqueado</td><td>$ultima</td></tr>";
    $tab .= $lin;
    
    $ordem++;
  }
  $tab .= '<tr><td colspan=2>&nbsp;</td></tr>'.
           "<tr><td colspan=2>&nbsp;</td></tr>".
          '<tr><td colspan=2 align=center bgcolor=white onmouseout="escondePLANTAO();">FECHAR</td></tr></table></td></tr></table>';
  $resp = $tab;
}


/*****************************************************************************************/
if ($acao=='senhaEXCLUIR') {
  $sql = 'select nome  '   . 
         "from operadores ". 
         "where senha='$vlr' and (instr(permissoes,'S')>0 or numero=1)";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) $resp='nao';
  else $resp='ok';
}



/*****************************************************************************************/
if ($acao=='gravarOCORRENCIA') {
  $cmps = explode('|', $_REQUEST['vlr']);

  $numreg = $cmps[1];
  $idINDICACAO = $cmps[0];
  $dataGRAVAR = "'$cmps[2]'"; 
  $dataGRAVAR2 = $cmps[3]=='' ? 'null' : "'$cmps[3]'";
  $idCORRETOR = $cmps[4]=='' ? 'null' : $cmps[4];
  $ocorrencia = $cmps[5];
  
  if ($numreg=='')  
    $sql = "insert into indicacoes_acompanhamento(data,idCORRETOR,dataPROXIMO,ocorrencia,idINDICACAO) ".
            " values(concat($dataGRAVAR, ' ',curtime()), $idCORRETOR, $dataGRAVAR2, '$ocorrencia', $idINDICACAO)";
  else  
    $sql = "update indicacoes_acompanhamento set data=concat($dataGRAVAR, ' ', curtime()),idCORRETOR=$idCORRETOR,".
              "  dataPROXIMO=$dataGRAVAR2 ,ocorrencia='$ocorrencia' ". 
            " where numreg=$numreg";

  mysql_query($sql) or die($sql . '<br><br>'.mysql_error());
  
  mysql_close($conexao);
  echo $resp; die('ok' );
}  
           



/*****************************************************************************************/
IF ( $acao=='incluirOCORRENCIA' || $acao=='editarOCORRENCIA'  ) {

  $arq = fopen('ocorrencia.txt', 'r');
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
    case 'incluirOCORRENCIA':
      $resp=str_replace('TITULO_JANELA', 'Nova Ocorrência',$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarOCORRENCIA':
      $resp=str_replace('TITULO_JANELA', "Editar Ocorrência Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);

      break;
  }        
  
  if ($acao!='incluirOCORRENCIA') {    
    $sql = "select acomp.numREG, date_format(acomp.data, '%d/%m/%y') as dataMOSTRAR,date_format(acomp.dataPROXIMO, '%d/%m/%y') as dataPROXIMO, ".
           " acomp.ocorrencia, acomp.idCORRETOR, repre.nome as nomeCORRETOR ".
           'from indicacoes_acompanhamento acomp '.
           'left join representantes repre  '.
           '    on repre.numero = acomp.idCORRETOR '.
           "where numreg=$vlr ";

    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vDATA_PROXIMO', $row->dataPROXIMO, $resp);

    $resp=str_replace('vDATA', $row->dataMOSTRAR, $resp);
    $resp=str_replace('vOCORRENCIA', $row->ocorrencia, $resp);

    $resp=str_replace('vREPRESENTANTE', $row->idCORRETOR, $resp);
    $resp=str_replace('vnomeREPRESENTANTE', $row->nomeCORRETOR, $resp);

    $resp=str_replace('@numREG', $row->numREG, $resp);
  }
  else {
    $resp=str_replace('vREPRESENTANTE', '', $resp);
    $resp=str_replace('vnomeREPRESENTANTE', '', $resp);

    $resp=str_replace('vOCORRENCIA', '', $resp);

    $resp=str_replace('vDATA_PROXIMO', '', $resp);
    $resp=str_replace('vDATA', date("d/m/y"), $resp);

    $resp=str_replace('@numREG', '', $resp);
  }

}



/*****************************************************************************************/
if ($acao=='verTELEFONE') {

  $sql  = "select lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
          "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
          " lig.idOPERADORA, lig.email, 0 as tipo, 'Indicações' as tabela ".
          "from indicacoes_atendimento lig ".
          "left join representantes repre ".
          "    on repre.numero=idCORRETOR ".
          "left join produto_atendimento ope ".
          "    on ope.numero=idOPERADORA ".          
          " where @criterioBUSCA  " .
          " union " .
          "select lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
          "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
          " lig.idOPERADORA, lig.email, 0 as tipo, 'Atendimento Presencial/demais ligações' as tabela ".
          "from presencial_atendimento lig ".
          "left join representantes repre ".
          "    on repre.numero=idCORRETOR ".
          "left join produto_atendimento ope ".
          "    on ope.numero=idOPERADORA ".          
          " where @criterioBUSCA  " .
          " union " .
          "select lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
          "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
          " lig.idOPERADORA, '' as email, tipo, 'Medicina' as tabela ".
          "from ligacoes lig ".
          "left join representantes repre ".
          "    on repre.numero=idCORRETOR ".
          "left join produto_atendimento ope ".
          "    on ope.numero=idOPERADORA ".          
          " where @criterioBUSCA  " .
          " union " .
          "select lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
          "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
          " lig.idOPERADORA, '' as email, tipo, 'Clinipam' as tabela ".
          "from ligacoes_OPCLINIPAM lig ".
          "left join representantes repre ".
          "    on repre.numero=idCORRETOR ".
          "left join produto_atendimento ope ".
          "    on ope.numero=idOPERADORA ".          
          " where @criterioBUSCA  " .
          " union " .
          "select lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
          "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
          " lig.idOPERADORA, '' as email, tipo, 'Odontologia' as tabela ".
          "from ligacoes_ODONTO lig ".
          "left join representantes repre ".
          "    on repre.numero=idCORRETOR ".
          "left join produto_atendimento ope ".
          "    on ope.numero=idOPERADORA ".          
          " where @criterioBUSCA  " ;
  $sql=str_replace('@criterioBUSCA', " telefones like trim('%$vlr%') ",$sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  if ( mysql_num_rows($resultado)>0 ) $resp='existe';
}


/*****************************************************************************************/
if ($acao=='qualUltimoPlantao') {
  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  $resultado = mysql_query("select permissoes, ifnull(ultPlantaoManipulado,'Medicina') as info from operadores ". 
                            " where numero=$logado", $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  // se nao tiver privilegio de ver tudo (ligacoes, etc), 
  if (strpos($row->permissoes, 'S')!==false || $logado==1)   
    $resp=$row->info;
  //e somente tiver privilegio ver suas proprias indicacoes
  else
    $resp='SOMENTE AS PROPRIAS INDICACOES!!!';
}


/*****************************************************************************************/
if ($acao=='proximoCORRETOR') {
  // le representante do plantao que esta sem atender a mais tempo
  /*
  $sql = 'select pl.ultATENDIMENTO  '   . 
         'from plantao pl '. 
         "where pl.idREPRESENTANTE=$vlr ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  
  $dataCORRETORATUAL=$row->ultATENDIMENTO;
*/
  $resultado = mysql_query("select ifnull($cmpCONFIG,-1) as info from configuracao", $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);

  if ($row->info==-1) die('erroCONEXAO');
  
  mysql_query("update $tabPLANTAO set bloqueado=0 where idREPRESENTANTE=$vlr") or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());

  
  $resultado = mysql_query("select ordem from $tabPLANTAO where ifnull(bloqueado,0)=0 and ordem>$row->info ".
              " order by ordem limit 1 ", $conexao) or die (mysql_error());
  if (mysql_num_rows($resultado)==0) {
    $resultado = mysql_query("select ordem from $tabPLANTAO where ifnull(bloqueado,0)=0 and ordem>=1 ".
                " order by ordem limit 1 ", $conexao) or die (mysql_error());
                
    if (mysql_num_rows($resultado)==0) {                
      mysql_free_result($resultado);    
      die('nada');
    }        
  }
  $row = mysql_fetcH_object($resultado);  
    
  $novoATUAL=$row->ordem;

  mysql_query("update configuracao set $cmpCONFIG=$novoATUAL ") or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());  

  $sql = 'select pl.idREPRESENTANTE, repre.nome, pl.numreg, pl.ramal, pl.ordem '   . 
         "from $tabPLANTAO pl ". 
         'left join representantes repre '.
         '    on  repre.numero=pl.idREPRESENTANTE  '.
         "where pl.ordem=$novoATUAL "; 
         
  $rsPLANTAO = mysql_query($sql, $conexao) or die (mysql_error());
  $regPLANTAO = mysql_fetcH_object($rsPLANTAO);


  // retorna info do repre encontrado (proximo)
  $resp=" $regPLANTAO->ordem - $regPLANTAO->nome ($regPLANTAO->idREPRESENTANTE)&nbsp;&nbsp;&nbsp;&nbsp;RAMAL: $regPLANTAO->ramal|".
         "$regPLANTAO->idREPRESENTANTE";
}



/*****************************************************************************************/
if ($acao=='desbloquearTODOS') {
  mysql_query("update $tabPLANTAO set bloqueado=0 ") or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());  
  echo('ok'); die();
}


/*****************************************************************************************/
if ($acao=='desbloquearCORRETOR') {
  mysql_query("update $tabPLANTAO set bloqueado=0 where idREPRESENTANTE=$vlr") or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());  
  echo('ok'); die();
}



/*****************************************************************************************/
if ($acao=='verPLANTAO') {
  $ramal = $_REQUEST['ramal'];
  $sql = 'select pl.idREPRESENTANTE, pl.ramal '   . 
         "from $tabPLANTAO pl ". 
         "where idREPRESENTANTE=$vlr or ramal='$ramal' ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='';
  while ($row = mysql_fetcH_object($resultado)) {
    if ($row->idREPRESENTANTE==$vlr) {$resp='jaCORRETOR';break;}
    if ($row->ramal==$ramal) {$resp='jaRAMAL';break;}
  }
}


/*****************************************************************************************/
if ($acao=='lerPLANTAO') {
  $sql = 'select pl.idREPRESENTANTE, repre.nome, pl.numreg, pl.ramal, pl.bloqueado '   . 
         "from $tabPLANTAO pl ". 
         'left join representantes repre '.
         '    on  repre.numero=pl.idREPRESENTANTE  '.
         'order by numreg ';

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='';
  $ordem=1;
  while ($row = mysql_fetcH_object($resultado)) {
    if ($resp!='') $resp.='|';
    $bloqueado=$row->bloqueado==1 ? ' - <font color=red><b>BLOQUEADO</b></font>' : '';
    $resp .= "$ordem;$row->nome ($row->idREPRESENTANTE)$bloqueado ;$row->numreg;$row->ramal";
    
    mysql_query("update $tabPLANTAO set ordem=$ordem where numreg=$row->numreg")  or  die (mysql_error());
    mysql_query("commit;", $conexao) or die (mysql_error());
    
    $ordem++;
  }
  $ordem--;
  mysql_query("update configuracao set $cmpUltOrdemPlantao=$ordem")  or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());
  if ($resp=='') $resp='nada';

  $resp .= "^$lendoATUAL";
}

/*****************************************************************************************/
if ($acao=='adicionaCORRETOR') {
  $ramal = $_REQUEST['ramal'];
  mysql_query("insert into $tabPLANTAO(idREPRESENTANTE, ultATENDIMENTO, ramal) values($vlr, now(), '$ramal')")  or  die (mysql_error());
  
  mysql_query("update $tabPLANTAO set ultATENDIMENTO=now()+numreg+100")  or  die (mysql_error());
  mysql_query("commit;", $conexao) or die (mysql_error());  
  echo('ok'); die();
}

/*****************************************************************************************/
if ($acao=='removeCORRETOR') {
  mysql_query("delete from $tabPLANTAO where numreg=$vlr") or  die (mysql_error());
  echo('ok'); die();
}




/*****************************************************************************************/
if ($acao=='novoPLANTAO') {
  mysql_query("delete from $tabPLANTAO") or  die (mysql_error());
  echo('ok'); die();
}




/*****************************************************************************************/
if ($acao=='excluir') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("delete from $tabLIGACOES where numreg=$id") or  die (mysql_error());
    
  echo('ok'); die();
}



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);

  $tipo = $_REQUEST['tipo'];
  $repreNOVO='';

  if ($lendoATUAL!='Indicações' && $lendoATUAL!='Atendimento Presencial/demais ligações') {
    
    $numreg = $cmps[5];
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = $logado[1];
    
    $idCORRETOR=$cmps[4]=='' ? 'null' : $cmps[4];
    $idOPERADORA=$cmps[3]=='' ? 'null' : $cmps[3];
    $idINDICACAO=$cmps[9]=='' ? 'null' : $cmps[9];
  
    if ($numreg=='') { 
      $sql = "insert into $tabLIGACOES(data,idCORRETOR,idOPERADOR,idOPERADORA,nome,telefones,corretorEXCEL,operadoraEXCEL,".
            "indicacaoEXCEL,idINDICACAO,obs,tipo) ".
              " values(concat('$cmps[0] ', curtime()), $idCORRETOR,$logado,$idOPERADORA,upper('$cmps[1]'),'$cmps[2]','$cmps[6]','$cmps[7]',".
              "'$cmps[8]', $idINDICACAO, '$cmps[10]',$tipo)";
  
      // desbloqueia corretor
      mysql_query("update $tabPLANTAO set bloqueado=0, ultATENDIMENTO=now() where idREPRESENTANTE=$idCORRETOR", $conexao) or die (mysql_error());
      mysql_query("commit;", $conexao) or die (mysql_error());
  

      // pula para proximo
      mysql_query("update configuracao set $cmpCONFIG=case when $cmpCONFIG>=$cmpUltOrdemPlantao then 1 else $cmpCONFIG+1 end;",
                 $conexao) or die (mysql_error());
      mysql_query("commit;", $conexao) or die (mysql_error());

      $sqlTMP= "select $cmpCONFIG as codigo, repre.nome, repre.numero ".
            "from configuracao ".
            "left  join plantao pl  ".
            "   on pl.ordem=$cmpCONFIG ".
            "inner join representantes repre  ".
            "   on repre.numero=pl.idREPRESENTANTE ";
      $resultado = mysql_query($sqlTMP, $conexao) or die (mysql_error());

      $row = mysql_fetcH_object($resultado);
      $repreNOVO = "$row->codigo   ($row->numero - $row->nome)";      

    }
    else  
      $sql = "update $tabLIGACOES set data=concat('$cmps[0] ', curtime()), idCORRETOR=$idCORRETOR, idOPERADOR=$logado, idOPERADORA=$idOPERADORA, ".
            " nome=upper('$cmps[1]'), indicacaoEXCEL='$cmps[8]', idINDICACAO=$idINDICACAO, obs='$cmps[10]', ".
            " telefones='$cmps[2]', corretorEXCEL='$cmps[6]', operadoraEXCEL='$cmps[7]', tipo=$tipo ". 
              " where numreg=$numreg";

  }
  else {
    $numreg = $cmps[5];
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = $logado[1];
    
    $idCORRETOR=$cmps[4]=='' ? 'null' : $cmps[4];
    $idOPERADORA=$cmps[3]=='' ? 'null' : $cmps[3];
    $idINDICACAO=$cmps[6]=='' ? 'null' : $cmps[6];

    // qdo manipulando INDICACOES, ha um campo a mais, RESULTADO, STATUS
    $e_indicacao=false; 
    if ( isset($cmps[9]) ) {  
      $e_indicacao=true;
      $cmpAMAIS = " , idRESULTADO";
      $vlrAMAIS = $cmps[9]=='' ? ', null ' : ", $cmps[9] ";
      $updateAMAIS = ", idRESULTADO = " . ($cmps[9]=='' ? ' null ' : $cmps[9]) ;
    }
  
          
    if ($numreg=='')  
      if (! $e_indicacao)  
        $sql = "insert into $tabLIGACOES(data,idCORRETOR,idOPERADOR,idOPERADORA,nome,telefones,idINDICACAO,obs,email,tipo) ".
                " values(concat('$cmps[0] ', curtime()), $idCORRETOR,$logado,$idOPERADORA,upper('$cmps[1]'),'$cmps[2]',$idINDICACAO,".
                  "'$cmps[7]','$cmps[8]', $tipo)";
      else
        $sql = "insert into $tabLIGACOES(idOPERADOR,data,idCORRETOR,idOPERADORA,nome,telefones,idINDICACAO,obs,email,tipo $cmpAMAIS) ".
                " values($logado,concat('$cmps[0] ', curtime()), $idCORRETOR,$idOPERADORA,upper('$cmps[1]'),'$cmps[2]',$idINDICACAO,".
                  "'$cmps[7]','$cmps[8]', $tipo $vlrAMAIS)";

    else
      if (! $e_indicacao)  
        $sql = "update $tabLIGACOES set idCORRETOR=$idCORRETOR, idOPERADOR=$logado, idOPERADORA=$idOPERADORA, ".
              " nome=upper('$cmps[1]'), idINDICACAO=$idINDICACAO, obs='$cmps[7]', ".
              " telefones='$cmps[2]', email='$cmps[8]', tipo=$tipo where numreg=$numreg";
      else
      $sql = "update $tabLIGACOES set idCORRETOR=$idCORRETOR, idOPERADORA=$idOPERADORA, ".
              " nome=upper('$cmps[1]'), idINDICACAO=$idINDICACAO, obs='$cmps[7]', ".
              " telefones='$cmps[2]', email='$cmps[8]', tipo=$tipo $updateAMAIS where numreg=$numreg";

  }

  mysql_query($sql) or die($sql.'<br><br>' .mysql_error());
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();
  else   {
    if ($numreg=='')    $numreg = mysql_insert_id();
    
    $resp = 'OK;' . $numreg .';'.$repreNOVO;
  }
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  $dataTRAB = $_REQUEST['vlr'];
  $filtro='';

  $lendoATUAL = $_REQUEST['lendo'];
  
  

  // aqui é o seguinte,   nao sei onde, nao sei qual arquivo esta sujando a variavel lendoATUAL
  // colocando caracteres do nada exemplo: Indica^&@*@$@&*(           
  // como nao descobri onde esta o erro, eu conserto aqui
  if (strpos($lendoATUAL, 'Indica')!==false) $lendoATUAL='Indicações';
//  die($lendoATUAL);

  //die($lendoATUAL);

  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $logado[1];

  mysql_query("update operadores set ultPlantaoManipulado='$lendoATUAL' where numero=$logado;", $conexao) or die (mysql_error());

    $tabLIGACOES = 'ligacoes_OPCLINIPAM';
  if ($lendoATUAL=='Medicina') {
    $cmpCONFIG='corretorDaVezPlantao';
    $tabPLANTAO = 'plantao';
    $tabLIGACOES = 'ligacoes';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao';
  }
  else if ($lendoATUAL=='Odontologia') {
    $cmpCONFIG='corretorDaVezPlantao_ODONTO';
    $tabPLANTAO = 'plantao_ODONTO';
    $tabLIGACOES = 'ligacoes_ODONTO';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao_ODONTO';
  }
  else if ($lendoATUAL=='Clinipam') {
    $cmpCONFIG='corretorDaVezPlantao_CLINIPAM';
    $tabPLANTAO = 'plantao_CLINIPAM';
    $tabLIGACOES = 'ligacoes_OPCLINIPAM';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao_CLINIPAM';
  }
  else if ($lendoATUAL=='Indicações') {
    $cmpCONFIG='';
    $tabPLANTAO = '';
    $tabLIGACOES = 'indicacoes_atendimento';
  }
  else if ($lendoATUAL=='Atendimento Presencial/demais ligações') {
    $cmpCONFIG='';
    $tabPLANTAO = '';
    $tabLIGACOES = 'presencial_atendimento';
  }
  

       
  // vlr2=   busca por telefone ou email
  if ( isset($_REQUEST['vlr2']) ) {
    $vlr2=$_REQUEST['vlr2'];

    // se nao e numerica a busca, procuro por email somente
    if (! is_numeric($vlr2) ) 
      $sql  = "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, lig.email, 0 as tipo, 'Indicações' as tabela ".
              "from indicacoes_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA    " .
              " union ".
              "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, lig.email, 0 as tipo, 'Atendimento Presencial/demais ligações' as tabela ".
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              "order by dataORG desc " ;
    // busca numerica, procura por telefones
    else
      $sql  = "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, lig.email, 0 as tipo, 'Indicações' as tabela ".
              "from indicacoes_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              " union " .
              "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, lig.email, 0 as tipo, 'Atendimento Presencial/demais ligações' as tabela ".
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              " union " .
              "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, '' as email, tipo, 'Medicina' as tabela ".
              "from ligacoes lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              " union " .
              "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, '' as email, tipo, 'Clinipam' as tabela ".
              "from ligacoes_OPCLINIPAM lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              " union " .
              "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
              " lig.idOPERADORA, '' as email, tipo, 'Odontologia' as tabela ".
              "from ligacoes_ODONTO lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where @criterioBUSCA   " .
              "order by dataORG desc " ;

    if (! is_numeric($vlr2) ) {
      $sql=str_replace('@criterioBUSCA', " lig.email like '%$vlr2%' ",$sql);
      $filtro="somente email= $vlr2";
    }
    if ( is_numeric($vlr2) ) {
      $sql=str_replace('@criterioBUSCA', " telefones like trim('%$vlr2%') ",$sql);
      $filtro="somente telefone= $vlr2";
    }
  } 
  // vlr3=   busca por nome
  else if ( isset($_REQUEST['vlr3']) ) {
    $vlr3=$_REQUEST['vlr3'];
    $sql  = "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
            "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
            " lig.idOPERADORA, lig.email, 0 as tipo, 'Indicações' as tabela ".
            "from indicacoes_atendimento lig ".
            "left join representantes repre ".
            "    on repre.numero=idCORRETOR ".
            "left join produto_atendimento ope ".
            "    on ope.numero=idOPERADORA ".          
            " where @criterioBUSCA   " .
            " union " .
            "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
            "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
            " lig.idOPERADORA, lig.email, 0 as tipo, 'Atendimento Presencial/demais ligações' as tabela ".
            "from presencial_atendimento lig ".
            "left join representantes repre ".
            "    on repre.numero=idCORRETOR ".
            "left join produto_atendimento ope ".
            "    on ope.numero=idOPERADORA ".          
            " where @criterioBUSCA  " .
            " union " .
            "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
            "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
            " lig.idOPERADORA, '' as email, tipo, 'Medicina' as tabela ".
            "from ligacoes lig ".
            "left join representantes repre ".
            "    on repre.numero=idCORRETOR ".
            "left join produto_atendimento ope ".
            "    on ope.numero=idOPERADORA ".          
            " where @criterioBUSCA  " .
            " union " .
            "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
            "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
            " lig.idOPERADORA, '' as email, tipo, 'Clinipam' as tabela ".
            "from ligacoes_OPCLINIPAM lig ".
            "left join representantes repre ".
            "    on repre.numero=idCORRETOR ".
            "left join produto_atendimento ope ".
            "    on ope.numero=idOPERADORA ".          
            " where @criterioBUSCA  " .
            " union " .
            "select lig.idOPERADOR, lig.data as dataORG, lig.numreg, date_format(lig.data, '%d/%m/%y  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
            "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, ".
            " lig.idOPERADORA, '' as email, tipo, 'Odontologia' as tabela ".
            "from ligacoes_ODONTO lig ".
            "left join representantes repre ".
            "    on repre.numero=idCORRETOR ".
            "left join produto_atendimento ope ".
            "    on ope.numero=idOPERADORA ".          
            " where @criterioBUSCA  " .
            "order by dataORG desc " ;

    $sql=str_replace('@criterioBUSCA', " lig.nome like '%$vlr3%' ",$sql);
    $filtro="nome cliente= $vlr3";
  }

  else {
    if ($lendoATUAL=='Indicações' || $lendoATUAL=='Atendimento Presencial/demais ligações')
      $sql  = "select lig.idOPERADOR, lig.numreg, date_format(lig.data, '%d/%m  %H:%i') as dataMOSTRAR, lig.telefones, ifnull(lig.tipo,1) as tipo,".
              "repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA,  ".
              " '$lendoATUAL' as tabela ".
              "from $tabLIGACOES lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where date_format(lig.data, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' @criterioQUEM  " .
              "order by lig.data desc " ;
    else
      $sql  = "select lig.idOPERADOR, lig.numreg, date_format(lig.data, '%d/%m  %H:%i') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ifnull(lig.tipo,1) as tipo, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " '$lendoATUAL' as tabela ".
              "from $tabLIGACOES lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".          
              " where date_format(lig.data, '%Y%m%d') between '$dataTRAB' and '$dataTRAB' @criterioQUEM  " .
              "order by lig.data desc " ;
              
  }

  $verDIFERENTE='';  
  if ($_REQUEST['proprias']=='sim') {
    // le qual é ou quais sao os corretores pelos quais o operador logado tem responsabilidade em cuidar das indicacoes
    $rsTMP = mysql_query("select numero  from representantes ". 
                              " where operadorPREVENDA=$logado", $conexao) or die (mysql_error());
    if ( mysql_num_rows($rsTMP)==0 ) $corretoresRESPONSABILIDADE='999999' ;
    else {
      $corretoresRESPONSABILIDADE='';
      while ($regTMP = mysql_fetcH_object($rsTMP)) {
        $corretoresRESPONSABILIDADE .= $corretoresRESPONSABILIDADE=='' ? '' : ',';
        $corretoresRESPONSABILIDADE .= $regTMP->numero;

        $verDIFERENTE .= "|$regTMP->numero|";
      }
    }
    mysql_free_result($rsTMP);
 
    $sql=str_replace('@criterioQUEM', " and lig.idCORRETOR in ($corretoresRESPONSABILIDADE) ",$sql);
  }
  else
      $sql=str_replace('@criterioQUEM', '',$sql);

//  if ($logado==5) die($sql);

//echo $sql;
  $resultado = mysql_query($sql, $conexao) or die ($sql.'<br><br>'.mysql_error().'<br><br>'.$lendoATUAL);
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.225;
  $largura3 = $_SESSION['largIFRAME'] * 0.165;
  $largura4 = $_SESSION['largIFRAME'] * 0.235;
  $largura5 = $_SESSION['largIFRAME'] * 0.15;
    
	$header = "$largura3 px,Data|$largura4 px,Nome|$largura2 px,Corretor|$largura2 px,Produto|$largura5 px,Telefones|1%,&nbsp;|1%,&nbsp;";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
//  $qtdeLIGACOES = mysql_num_rows($resultado);
  $qtdeVALIDAS = 0; $qtdeLIGACOES = 0; $qtdePRESENCIAL = 0; $qtdeDEMAISTIPOS = 0;
  
  $i=1;
  $regsJaListados='';
  while ($row = mysql_fetcH_object($resultado)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";
      $largura4="width=\"$largura4 px\"";
      $largura5="width=\"$largura5 px\"";
    } else {    
      $largura1='';$largura2=''; $largura4='';$largura3='';$largura5='';
    }
    $i++;

    if ($lendoATUAL=='Indicações' || $lendoATUAL=='Atendimento Presencial/demais ligações') {
      $corretor=$row->idCORRETOR!='' ? "$row->nomeCORRETOR ($row->idCORRETOR)" : '';
      $operadora=$row->idOPERADORA!='' ? "$row->nomeOPERADORA ($row->idOPERADORA)" : '';
    }
    else {
      $corretor=$row->idCORRETOR=='' ? $row->corretorEXCEL : "$row->nomeCORRETOR ($row->idCORRETOR)";
      $operadora=$row->idOPERADORA=='' ? $row->operadoraEXCEL : "$row->nomeOPERADORA ($row->idOPERADORA)";
    }

    $podeEDITAR='sim';
    $telefones=$row->telefones;
    if ($_REQUEST['proprias']=='sim') {
      if ( strpos($verDIFERENTE, "|$row->idCORRETOR|")!==false )  $podeEDITAR='sim'; else $podeEDITAR='nao';
      if ( strpos($verDIFERENTE, "|$row->idCORRETOR|")!==false )  $telefones=$row->telefones; else $telefones='************';
    }   
    $lin = "<tr @cor ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numreg\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura3>&nbsp;$row->dataMOSTRAR</td>".
            "<td align=\"left\" $largura4>$row->nome</td>".
            "<td align=\"left\" $largura2>$corretor</td>".            
            "<td align=\"left\" $largura2>$operadora</td>".            
            "<td align=\"left\" $largura5>$telefones</td>".            
            "<td style='display:none;'>$row->tabela</td>".
            "<td style='display:none;'>$podeEDITAR</td>".
            "</tr>";

    $regsJaListados .= "|$row->numreg|" ;
    if ($row->nome!='* PERDEU LIGAÇÃO *' && $row->tipo==1) $qtdeVALIDAS++;
    if ($row->tipo==1) $qtdeLIGACOES++;
//    if ($row->tipo==0) $qtdeDEMAISTIPOS++;

    if ( $lendoATUAL=='Atendimento Presencial/demais ligações' || $lendoATUAL=='Indicações') 
        $qtdeDEMAISTIPOS++;

    if ( $lendoATUAL=='Atendimento Presencial/demais ligações') 
      $lin = str_replace('@cor', $row->tipo==1 ? 'style="color:blue;"' : '',$lin);
    else
      $lin = str_replace('@cor', '',$lin);

            
    $resp = $resp . ($lin);
  }

  // le eventuais indicacoes que possuam alguma ocorrencia agendada para hoje
  if ( $lendoATUAL=='Indicações') {
    $sql = "select lig.idOPERADOR, lig.numreg, date_format(lig.data, '%d/%m  %H:%i') as dataMOSTRAR, lig.telefones, ifnull(lig.tipo,1) as tipo,".
           "repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA  ".
           "from indicacoes_atendimento lig ".
           "left join representantes repre ".
           "    on repre.numero=idCORRETOR ".
           "left join produto_atendimento ope ".
           "    on ope.numero=idOPERADORA ".          
           "inner join  indicacoes_acompanhamento hist ".
           "  on lig.numreg = hist.idINDICACAO ".
           "where ( date_format(hist.dataPROXIMO, '%Y%m%d') between  '$dataTRAB' and '$dataTRAB') @criterioQUEM  " .
           " group by lig.numreg ";

    if ($_REQUEST['proprias']=='sim') 
      $sql=str_replace('@criterioQUEM', " and lig.idCORRETOR in ($corretoresRESPONSABILIDADE) ",$sql);
    else
      $sql=str_replace('@criterioQUEM', '',$sql);

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    while ($row = mysql_fetcH_object($resultado)) {
      if ($i==1) {
        $largura1="width=\"$largura1 px\"";
        $largura2="width=\"$largura2 px\"";
        $largura3="width=\"$largura3 px\"";
        $largura4="width=\"$largura4 px\"";
        $largura5="width=\"$largura5 px\"";
      } else {    
        $largura1='';$largura2=''; $largura4='';$largura3='';$largura5='';
      }
      $i++;
  
      if ( strpos($regsJaListados, "|$row->numreg|")!==false )  {
        // registro ja listado no select acima, nao lista de novo - isso pode acontecer
      }
      else {
        $corretor=$row->idCORRETOR!='' ? "$row->nomeCORRETOR ($row->idCORRETOR)" : '';
        $operadora=$row->idOPERADORA!='' ? "$row->nomeOPERADORA ($row->idOPERADORA)" : '';
    
        $lin = "<tr @cor ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numreg\" onmouseover=\"this.style.cursor='default';" .  
      	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
                "<td align=\"left\" $largura3>&nbsp;$row->dataMOSTRAR</td>".
                "<td align=\"left\" $largura4>$row->nome</td>".
                "<td align=\"left\" $largura2>$corretor</td>".            
                "<td align=\"left\" $largura2>$operadora</td>".            
                "<td align=\"left\" $largura5>$row->telefones</td>".            
                "<td style='display:none;'>Indicações</td>".
                "<td style='display:none;'>sim</td>".
                "</tr>";
   
        $lin = str_replace('@cor', 'style="color:blue;"' ,$lin);
  
        $qtdeDEMAISTIPOS++;
        $resp .= $lin;
      }
    }
  }

  $resp .= '</table>';
  $diaSEMANA=diasemana(date($dataTRAB));
//$diaSEMANA=date(substr($dataTRAB,0,4).='-';
  $resp .= '^'.$qtdeLIGACOES.'^'.$filtro.'^'.$qtdeVALIDAS.'^'.$diaSEMANA.'^'.$qtdePRESENCIAL.'^'.$qtdeDEMAISTIPOS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {


  $lendoESPECIFICO=$lendoATUAL;
  if (isset( $_REQUEST['lendo']))   $lendoESPECIFICO = $_REQUEST['lendo'];

  $somenteLEITURA=false;  
  if ($lendoATUAL!=$lendoESPECIFICO) {
    $lendoATUAL=$lendoESPECIFICO;
    $somenteLEITURA=true;
  }


  if ($lendoATUAL=='Medicina') {
    $cmpCONFIG='corretorDaVezPlantao';
    $tabPLANTAO = 'plantao';
    $tabLIGACOES = 'ligacoes';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao';  
  }
  else if ($lendoATUAL=='Odontologia') {
    $cmpCONFIG='corretorDaVezPlantao_ODONTO';
    $tabPLANTAO = 'plantao_ODONTO';
    $tabLIGACOES = 'ligacoes_ODONTO';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao_ODONTO';
  }
  else if ($lendoATUAL=='Clinipam') {
    $cmpCONFIG='corretorDaVezPlantao_CLINIPAM';
    $tabPLANTAO = 'plantao_CLINIPAM';
    $tabLIGACOES = 'ligacoes_OPCLINIPAM';
    $cmpUltOrdemPlantao = 'ultOrdemPlantao_CLINIPAM';
  }
  else if ($lendoATUAL=='Indicações') {
    $cmpCONFIG='';
    $tabPLANTAO = '';
    $tabLIGACOES = 'indicacoes_atendimento';
  }
  else if ($lendoATUAL=='Atendimento Presencial/demais ligações') {
    $cmpCONFIG='';
    $tabPLANTAO = '';
    $tabLIGACOES = 'presencial_atendimento';
  }
  

  if ($lendoATUAL=='Indicações')
    $arq = fopen('indicacao.txt', 'r');
  else if ($lendoATUAL=='Atendimento Presencial/demais ligações')
    $arq = fopen('presencial.txt', 'r');
  else
    $arq = fopen('ligacao.txt', 'r');
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
      $resp=str_replace('@SO_LEITURA', 'nao', $resp);
      break;      
    case 'editarREG':
      $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      if ($somenteLEITURA) {
        $resp=str_replace('texto_botao', "<font color=red><b>[ TABELA: $lendoESPECIFICO - SÓ LEITURA ]</font>",$resp);
        $resp=str_replace('@SO_LEITURA', 'sim',$resp);
      }
      else {
        $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
        $resp=str_replace('@SO_LEITURA', 'nao',$resp);
        $resp=str_replace('readonly', '',$resp);
      }

      break;
  }        
  
  // acompanhamento, historico - so é usado em INDICACOES
  $resp = str_replace('@titHISTORICO',
          tabelaPADRAO('width="97%" style="text-align:left;"', 
              "15%,Data registro|45%,Ocorrência|20%,Registrado por:|15%,Próximo contato|5%,&nbsp;" ).'</table>', $resp);
  $resp=str_replace('@altDivHISTORICO', '165px', $resp);

  $tabHIST = '<table width="99%" id="tabHISTORICO" width="99%" cellpadding="3"  cellspacing="0" ' .
        'style="font-family:verdana;font-size:10px;color:black;">';

  if ($acao!='incluirREG')   { 
    if ($lendoATUAL=='Atendimento Presencial/demais ligações')     
      $sql  = "select lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.telefones, ifnull(lig.tipo, 1) as tipo,  ".
              "repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, lig.obs, lig.email " .
              "from $tabLIGACOES lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".          
              " where lig.numreg=$vlr " ;

    else if ($lendoATUAL=='Indicações')     
      $sql  = "select lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.telefones, ifnull(lig.tipo, 1) as tipo,  ".
              "repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, lig.idRESULTADO, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, lig.obs, lig.email, " .
              " sit.descricao as nomeSITUACAO ".
              "from $tabLIGACOES lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join resultados_indicacoes sit ".
              "    on sit.numreg=idRESULTADO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".          
              " where lig.numreg=$vlr " ;

    else
      $sql  = "select lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ifnull(lig.tipo, 1) as tipo, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, indicacaoEXCEL, lig.obs " .
              "from $tabLIGACOES lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".          
              " where lig.numreg=$vlr " ;
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('vFONES', $row->telefones, $resp);


    if ($lendoATUAL!='Indicações' && $lendoATUAL!='Atendimento Presencial/demais ligações') {
      $resp=str_replace('vREPRESENTANTE', ($row->idCORRETOR=='' && $row->corretorEXCEL!='') ? 
          "$row->corretorEXCEL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font color=grey>** sem código - importado sistema antigo EXCEL</font></b>": 
          "$row->nomeCORRETOR ($row->idCORRETOR)", $resp);
      $resp=str_replace('@exibirBTNPROXIMO', ($row->idCORRETOR=='' && $row->corretorEXCEL!='') ? 'style="display:none;"' : '', $resp);
  
      $resp=str_replace('@estiloCORRETOR', ($row->idCORRETOR=='' && $row->corretorEXCEL!='') ?
                'style="font-size:10px;font-weight:bold;"'  : 'style="font-size:14px;font-weight:bold;"', $resp); 
  
      $resp=str_replace('@idREPRESENTANTE', $row->idCORRETOR, $resp);

      $resp=str_replace('vATENDIMENTO_PRODUTO', $row->idOPERADORA, $resp);
      $resp=str_replace('vnomeATENDIMENTO_PRODUTO', ($row->idOPERADORA=='' && $row->operadoraEXCEL!='') ?   
            "$row->operadoraEXCEL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b><font color=grey>** sem código - importado sistema antigo EXCEL</font></b>" : $row->nomeOPERADORA, $resp);    
  
      $resp=str_replace('vINDICACAO', $row->idINDICACAO, $resp);
      $resp=str_replace('vnomeINDICACAO', ($row->idINDICACAO=='' && $row->indicacaoEXCEL!='') ?   
            "$row->indicacaoEXCEL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b><font color=grey>** sem código - importado sistema antigo EXCEL</font></b>" :
             $row->nomeINDICACAO, $resp);

    }
    else {
      $resp=str_replace('vREPRESENTANTE', $row->idCORRETOR, $resp);
      $resp=str_replace('vnomeREPRESENTANTE', $row->nomeCORRETOR, $resp);
      $resp=str_replace('@idREPRESENTANTE', $row->idCORRETOR, $resp);

      $resp=str_replace('vEMAIL', $row->email, $resp);

      if ($lendoATUAL=='Indicações') {
        $resp=str_replace('vSITUACAO', $row->idRESULTADO, $resp);
        $resp=str_replace('vnomeSITUACAO', $row->nomeSITUACAO, $resp);
      }

      $resp=str_replace('vATENDIMENTO_PRODUTO', $row->idOPERADORA, $resp);
      $resp=str_replace('vnomeATENDIMENTO_PRODUTO', $row->nomeOPERADORA, $resp);    
  
      $resp=str_replace('vINDICACAO', $row->idINDICACAO, $resp);
      $resp=str_replace('vnomeINDICACAO', $row->nomeINDICACAO, $resp);

      // le acompanhamento, historico no caso de lendo determinada indicacao
      if ($lendoATUAL=='Indicações' ) {
        $sql = "select acomp.numREG, date_format(acomp.data, '%d/%m/%y %H:%i') as dataMOSTRAR,date_format(acomp.dataPROXIMO, '%d/%m/%y') as dataPROXIMO, ".
               " acomp.ocorrencia, acomp.idCORRETOR, repre.nome as nomeCORRETOR ".
               'from indicacoes_acompanhamento acomp '.
               'left join representantes repre  '.
               '    on repre.numero = acomp.idCORRETOR '.
               "where idINDICACAO=$row->numreg ".
               "  order by acomp.data desc ";      
  
        $rsACOMP = mysql_query($sql) or die (mysql_error());  
        while ( $regACOMP = mysql_fetcH_object($rsACOMP) ) {
  
          $ocorrencia = substr($regACOMP->ocorrencia,0, 60);
          $lin = "<tr id=\"HIST_$regACOMP->numREG\" onmouseout=\"this.style.backgroundColor='#F6F7F7';\" " . 
                  ' onmouseover="this.style.backgroundColor=\'#A9B2CA\';this.style.cursor=\'pointer\';" '  .
                  " onclick='editarOCORRENCIA($regACOMP->numREG);' > ".
                 "<td align=\"center\" width=\"15%\">$regACOMP->dataMOSTRAR</td>". 
                 "<td align=\"left\" width=\"45%\">$ocorrencia</td>".
                 "<td align=\"left\" width=\"20%\">$regACOMP->nomeCORRETOR ($regACOMP->idCORRETOR)</td>".
                 "<td align=\"center\" width=\"15%\">$regACOMP->dataPROXIMO</td>".
                 "<td onmousedown=\"removerOCORRENCIA($regACOMP->numREG)\"  width=\"5%\" align=\"center\" >".
                                '<font color="red" style="font-size:14px;font-weight:bold;">X</font></td>'.
                 "</tr>";                            
                              
          $tabHIST .= $lin;       
        }
        mysql_free_result($rsACOMP);        
      }

    }

      $resp=str_replace('checkedTIPO2', $row->tipo==2 ? 'checked' : '', $resp);
      $resp=str_replace('checkedTIPO1', $row->tipo==1 ? 'checked' : '', $resp);

    $resp=str_replace('vOBS', $row->obs, $resp);

    $resp=str_replace('vDATA', $row->dataMOSTRAR, $resp);
    $resp=str_replace('vOPERADOR', "$row->nomeOPERADOR ($row->idOPERADOR)", $resp);    
    $resp=str_replace('@numREG', $vlr, $resp);
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vFONES', '', $resp);

    // obtem corretor que esta na vez no plantao (so para atendimentos ligacoes medicina e ligacoes odonto)
    // qdo manipulando tabela INDICACOES, nao usa plantao (um a um), e sim indicacao manual do corretor
    if ($lendoATUAL!='Indicações' && $lendoATUAL!='Atendimento Presencial/demais ligações') {
      $resultado = mysql_query("select ifnull($cmpCONFIG,-1) as info, $cmpUltOrdemPlantao as ultOrdemPlantao from configuracao", $conexao) or die (mysql_error());
      $row = mysql_fetcH_object($resultado);

      if ($row->info==-1) die('erroCONEXAO');

      $repreDaVez = $row->info;
      $repreDaVez = $repreDaVez>=$row->ultOrdemPlantao ? 1 : $repreDaVez+1;

      $sql = 'select pl.idREPRESENTANTE, repre.nome, pl.numreg, pl.ramal, pl.ordem '   . 
           "from $tabPLANTAO pl ". 
           'left join representantes repre '.
           '    on  repre.numero=pl.idREPRESENTANTE  '.
           "where pl.ordem>=$repreDaVez and ifnull(pl.bloqueado,0)=0"; 
    
      $rsPLANTAO = mysql_query($sql, $conexao) or die (mysql_error());
      if (mysql_num_rows($rsPLANTAO)==0) {
        mysql_free_result($rsPLANTAO);
        
        $sql = 'select pl.idREPRESENTANTE, repre.nome, pl.numreg, pl.ramal, pl.ordem '   . 
             "from $tabPLANTAO pl ". 
             'left join representantes repre '.
             '    on  repre.numero=pl.idREPRESENTANTE  '.
             "where pl.ordem>=1 "; 
      
        $rsPLANTAO = mysql_query($sql, $conexao) or die (mysql_error());
        if (mysql_num_rows($rsPLANTAO)==0) {
          mysql_free_result($rsPLANTAO);
          die('nada');
        }
      }
      $regPLANTAO = mysql_fetcH_object($rsPLANTAO);
      
      mysql_query("update $tabPLANTAO set bloqueado=1 where idREPRESENTANTE=$regPLANTAO->idREPRESENTANTE", $conexao) or die (mysql_error());
      mysql_query("commit;", $conexao) or die (mysql_error());    
    
      $resp=str_replace('vREPRESENTANTE', " $regPLANTAO->ordem - $regPLANTAO->nome ($regPLANTAO->idREPRESENTANTE)&nbsp;&nbsp;&nbsp;&nbsp;RAMAL: $regPLANTAO->ramal", $resp);
      $resp=str_replace('@idREPRESENTANTE', $regPLANTAO->idREPRESENTANTE, $resp);

      $resp=str_replace('@estiloCORRETOR', 'style="font-size:14px;font-weight:bold;"', $resp);
      mysql_free_result($rsPLANTAO);

      $resp=str_replace('checkedTIPO1', 'checked', $resp);
      $resp=str_replace('checkedTIPO2', '', $resp);
    }
    else {
      $resp=str_replace('vREPRESENTANTE', '', $resp);
      $resp=str_replace('vnomeREPRESENTANTE', '', $resp);
      $resp=str_replace('@idREPRESENTANTE', '', $resp);

      $resp=str_replace('vEMAIL', '', $resp);

      $resp=str_replace('vSITUACAO', '', $resp);
      $resp=str_replace('vnomeSITUACAO', '', $resp);

      $resp=str_replace('checkedTIPO2', 'checked', $resp);
      $resp=str_replace('checkedTIPO1', '', $resp);

    }

    $resp=str_replace('vATENDIMENTO_PRODUTO', '', $resp);
    $resp=str_replace('vnomeATENDIMENTO_PRODUTO', '', $resp);

    $resp=str_replace('vINDICACAO', '', $resp);
    $resp=str_replace('vnomeINDICACAO', '', $resp);

    $resp=str_replace('vOBS', '', $resp);

    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $logado = "$logado[0] ($logado[1])";

    $resp=str_replace('vOPERADOR', $logado, $resp);    
    
    $resp=str_replace('vDATA', date("d/m/y"), $resp);
    $resp=str_replace('@numREG', '', $resp);
  }
  $tabHIST .= '</table>';
  $resp=str_replace('@tabHISTORICO', $tabHIST, $resp);  

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
