<?php
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );
require_once( '../includes/senha.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$resp = 'INEXISTENTE';

$rsDATA = mysql_query("select date_format(now(), '%d/%m/%y') as hoje, TIME_FORMAT(now(), '%H:%I') as agora ", $conexao) 
    or die (mysql_error());
$row = mysql_fetcH_object($rsDATA);

$hoje = $row->hoje;
$agora = $row->agora;
mysql_free_result($rsDATA);

// verifica de antemao qual e a conta de entrega de proposta
$sql = "select numero   ".
       "from contas  ".
       "where ifnull(contaENTREGA_PROPOSTA,0)=1 ";
$contaENTREGA=0;
$resultado = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($resultado)>0) {
  $row = mysql_fetcH_object($resultado);
  $contaENTREGA = $row->numero;
}


/*****************************************************************************************/
if ($acao=='senhaREPASSE') {
  $sql = 'select nome  '   . 
         "from operadores ". 
         "where (senha='$vlr' and numero=1) or (senha='$vlr' and instr(permissoes, 'H')<>0); ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) $resp='nao';
  else $resp='ok';
}



/*****************************************************************************************/
if ($acao=='ligacoes') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  

  $idSITUACAO = $_REQUEST['situ'];
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $nomeREPRE = $_REQUEST['nomeREPRE'];
  $repre = $_REQUEST['repre'];
  $tipoREL = $_REQUEST['tipoREL'];

  $formatoREL = $_REQUEST['formato'];
  $porNOME = $_REQUEST['porNOME']=='true' ? 1 : 0;
  $nome = $_REQUEST['nome'];

  $operadora = $_REQUEST['operadora'];  
  $nomeOPERADORA = $_REQUEST['nomeOPERADORA'];  

  $relacionou = '';
  // se formato do relat = listar registros
  if ($formatoREL==1) {
    // todos os regs
    if ($tipoREL==1) {  
      $sql  = "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, indicacaoEXCEL, ".
              " lig.obs, 'Lig Medicina' as tabela, '' as situacao " .
              "from ligacoes lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE  @criterioOPERADORA ".
              ' union all '.
              "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, indicacaoEXCEL, ".
              " lig.obs, 'Lig Odonto' as tabela, '' as situacao " .
              "from ligacoes_ODONTO lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  ".
              ' union all '.
              "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, '' as indicacaoEXCEL, ".
              " lig.obs, 'Indica��o' as tabela, sit.descricao as situacao  " .
              "from indicacoes_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              "left join resultados_indicacoes sit ".
              "    on sit.numreg = idRESULTADO ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              ' union all '.
              "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, '' as indicacaoEXCEL, ".
              " lig.obs, 'Presencial' as tabela, '' as situacao " .
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE    @criterioOPERADORA ".
              "  order by nomeCORRETOR ";
    }
    // ligacoes telefonicas
    else if ($tipoREL==2) 
      $sql  = "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, indicacaoEXCEL, lig.obs, 'Lig Medicina' as tabela " .
              "from ligacoes lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              ' union all '.
              "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.corretorEXCEL, lig.telefones, ".
              "lig.operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, indicacaoEXCEL, lig.obs, 'Lig Odonto' as tabela " .
              "from ligacoes_ODONTO lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  ".
              "  order by nomeCORRETOR ";
  
    // indicacoes
    else if ($tipoREL==3) 
      $sql  =  "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, '' as indicacaoEXCEL, ".
              " lig.obs, 'Indica��o' as tabela, sit.descricao as situacao " .
              "from indicacoes_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              "left join resultados_indicacoes sit ".
              "    on sit.numreg = idRESULTADO ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE @criterioSITUACAO   @criterioOPERADORA ".
              "  order by nomeCORRETOR ";
  
    // atendimento presencial
    else if ($tipoREL==4)
       $sql  = "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, '' as indicacaoEXCEL, lig.obs, ".
              " case ifnull(lig.tipo,1)   " .
              "   when 1 then 'Liga��o'  " .
              "   else 'Presencial'   " .
              "   end as tabela "  .
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where ifnull(lig.tipo,1)<>1 and date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              'order by nomeCORRETOR';
//              "  order by ifnull(lig.tipo, 1),data desc ";

    // ligacoes extra plantao
    else if ($tipoREL==5)
       $sql  = "select lig.data, lig.numreg, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, '' as corretorEXCEL, lig.telefones, ".
              "'' as operadoraEXCEL, repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idOPERADOR, usu.nome as nomeOPERADOR, lig.idINDICACAO, ind.nome as nomeINDICACAO, '' as indicacaoEXCEL, lig.obs, ".
              " case ifnull(lig.tipo,1)   " .
              "   when 1 then 'Liga��o'  " .
              "   else 'Presencial'   " .
              "   end as tabela "  .
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              "left join operadores usu ".
              "    on usu.numero=idOPERADOR ".
              " where ifnull(lig.tipo,1)=1 and date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              ' order by nomeCORRETOR ';
//              "  order by ifnull(lig.tipo, 1),data desc ";


    
    $headers=
  	  "                          Tipo do                                                                                                     Origem do   |".
  	  "Data      Situa��o        contato        Corretor                  Nome                      Telefone(s)        Produto               Contato     |".
      str_repeat('-', 160 );
  //   99/99/99  xxxxxxxxxxxxxx  xxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxx                

  }

  //*****************************************************************************************************************************
  // se formato do relat = extrair email telefone
  //*****************************************************************************************************************************
  else if ($formatoREL==4) {
      $sql  = "select lig.data, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.telefones, lig.email, ".
              " repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idINDICACAO, ind.nome as nomeINDICACAO, 'Indica��o' as tabela " .
              "from indicacoes_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              ' union all '.
              "select lig.data, date_format(lig.data, '%d/%m/%y') as dataMOSTRAR, lig.telefones, lig.email, ".
              " repre.nome as nomeCORRETOR, ope.nome as nomeOPERADORA, lig.nome, lig.idCORRETOR, lig.idOPERADORA, ".
              " lig.idINDICACAO, ind.nome as nomeINDICACAO, 'Presencial' as tabela " .
              "from presencial_atendimento lig ".
              "left join representantes repre ".
              "    on repre.numero=idCORRETOR ".
              "left join produto_atendimento ope ".
              "    on ope.numero=idOPERADORA ".
              "left join origens_atendimento ind ".
              "    on ind.numero=idINDICACAO ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA ".
              "  order by nomeCORRETOR ";
  }



  //*****************************************************************************************************************************
  // se formato do relat = somatoria por origem atendimento ou por corretor ou por situacao (no caso das indicacoes)
  //*****************************************************************************************************************************
  else {
    // se formato rel= somatoria por origem atendimento
    if ($formatoREL==2) {
      $cmpID='idINDICACAO';
      $tabelaAPOIO='origens_atendimento';
    }
    // se formato rel= somatoria por situacao (indicacoes)
    else if ($formatoREL==5) {
      $cmpID='idRESULTADO';
      $tabelaAPOIO='resultados_indicacoes';
    }
    // se formato rel= somatoria por corretor
    else {
      $cmpID='idCORRETOR';
      $tabelaAPOIO='representantes';
    }
    // todos os regs
    if ($tipoREL==1) 
      $sql  = "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome ".
              "from ligacoes lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE @criterioPERDIDAS   @criterioOPERADORA group by $cmpID ".
              ' union all '.
              "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
              "from ligacoes_ODONTO lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  @criterioPERDIDAS  group by $cmpID ".
              ' union all '.
              "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
              "from indicacoes_atendimento lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA   group by $cmpID ".
              ' union all '.
              "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
              "from presencial_atendimento lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA   group by $cmpID ";
  
    // ligacoes telefonicas
    else if ($tipoREL==2) 
      $sql  = "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome ".
              "from ligacoes lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  @criterioPERDIDAS group by $cmpID ".
              ' union all '.
              "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
              "from ligacoes_ODONTO lig ".
              "left  join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  @criterioPERDIDAS  group by $cmpID ";
  
    // indicacoes
    else if ($tipoREL==3)  {
      if ($formatoREL==5) 
        $sql  =  "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.descricao as nome   ".
                "from indicacoes_atendimento lig ".
                "left  join $tabelaAPOIO tabela ".
                "    on tabela.numreg = $cmpID ".
                " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  @criterioSITUACAO group by $cmpID ";
      else
        $sql  =  "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
                  "from indicacoes_atendimento lig ".
                  "left  join $tabelaAPOIO tabela ".
                  "    on tabela.numero = $cmpID ".
                  " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA  @criterioSITUACAO  group by $cmpID ";
    }
  
    // atendimento presencial
    else if ($tipoREL==4)
       $sql  = "select count(*) as qtde, ifnull(lig.$cmpID, 0) as numero, tabela.nome   ".
              "from presencial_atendimento lig ".
              "left join $tabelaAPOIO tabela ".
              "    on tabela.numero = $cmpID ".
              " where date_format(lig.data, '%Y%m%d') between '$dataini' and '$datafin' @criterioREPRE   @criterioOPERADORA   group by $cmpID ";

    $titulos="";
    $headers='';

    $sql = str_replace('@criterioPERDIDAS', " and instr(lig.nome, 'PERDEU')=0 ", $sql);
  }
  if ($tipoREL==1) $relacionou='Plant�o';
  else if ($tipoREL==2) $relacionou='Liga��es telef�nicas (plant�o)';
  else if ($tipoREL==3) $relacionou='Indica��es';
  else if ($tipoREL==4) $relacionou='Atendimento Presencial';
  else if ($tipoREL==5) $relacionou='Liga��es extra plant�o';


  if ($operadora!='') $titulos="Produto: $nomeOPERADORA ($operadora)";
  if ($repre=='9999') {
    $titREL = "$relacionou no per�odo: $dataIniMostrar a $dataFinMostrar   ";
    $sql = str_replace('@criterioREPRE', '', $sql);
  }
  else if ($repre=='') {
    $titREL = "$relacionou no per�odo: $dataIniMostrar a $dataFinMostrar  Corretor: $nome";
    $sql = str_replace('@criterioREPRE', " and corretorEXCEL like '%$nome%' " , $sql);
  }
  else {
    $titREL = "$relacionou no per�odo: $dataIniMostrar a $dataFinMostrar  Corretor: $nomeREPRE";
    $sql = str_replace('@criterioREPRE', " and lig.idCORRETOR=$repre " , $sql);
  }

  if ($idSITUACAO!='') $sql = str_replace('@criterioSITUACAO', " and ifnull(lig.idRESULTADO,0)=$idSITUACAO ", $sql);
  else $sql = str_replace('@criterioSITUACAO', '', $sql);
  
  if ($operadora!='') $sql = str_replace('@criterioOPERADORA', " and ifnull(idOPERADORA,0)=$operadora ", $sql);
  else $sql = str_replace('@criterioOPERADORA', '', $sql);
  
  
   

  // extrar email,telefones para XLS
  if ($formatoREL==4) {
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".xls";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  }
  else {
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  }

  
  // se formato rel= somatoria por origem atendimento ou formato rel= somatoria por corretor ou formato rel= somatoria por situacao
  // cria  matriz para preenchimento das qtdes usando como indice o ID da origem ou ID do corretor 
  if ($formatoREL==2 || $formatoREL==3 || $formatoREL==5) {
    // se formato rel= somatoria por origem atendimento
    if ( $formatoREL==2 )
      $resultado = mysql_query("select numero, nome from origens_atendimento ", $conexao) or die (mysql_error());
    else if ( $formatoREL==5 )
      $resultado = mysql_query("select numreg as numero, descricao as nome from resultados_indicacoes ", $conexao) or die (mysql_error());
    else
      $resultado = mysql_query("select numero, nome from representantes ", $conexao) or die (mysql_error());

    $tabelaAPOIO_CONT=array();
    $tabelaAPOIO_NOME=array();
    while ($row = mysql_fetcH_object($resultado)) {    
      $tabelaAPOIO_NOME[$row->numero]=$row->nome;
      $tabelaAPOIO_CONT[$row->numero]=0;
    }
    if ($formatoREL==2) $tabelaAPOIO_NOME[0]='SEM ORIGEM';
    else if ($formatoREL==5) $tabelaAPOIO_NOME[0]='SEM SITUA�AO DEFINIDA';
    else $tabelaAPOIO_NOME[0]='SEM CORRETOR DEFINIDO';
    $tabelaAPOIO_CONT[0]=0;
  }

//die($sql);
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  if (mysql_num_rows($resultado)==0) die('nada');

	$pagina = 0;  
  $lin = 200;

  $total=0;
  $total_2=0;
	
  // se formato rel= somatoria por origem atendimento ou formato rel= somatoria por corretor ou somatoria por situacao
  // preenche matriz para preenchimento das qtdes usando como indice o ID da origem ou ID do corretor
  if ($formatoREL==2 || $formatoREL==3 || $formatoREL==5) {
    $totREGS=0;
    while ($row = mysql_fetcH_object($resultado)) {
      $totREGS += $row->qtde;

      if ($row->numero=='0' || $row->numero=='') $tabelaAPOIO_CONT[0] += $row->qtde;
      else $tabelaAPOIO_CONT[$row->numero] += $row->qtde;
    }

    foreach( $tabelaAPOIO_CONT as $numero => $qtde)     {
  		if ($lin + 1 > 55)   cabecalho();

      if ($tabelaAPOIO_CONT[$numero]>0) {
     		$info=str_pad($numero, 6, ' ', 0) .'  ' .
              str_pad($tabelaAPOIO_NOME[$numero], 50, ' ', 1) .'  ' .
              str_pad($qtde, 6, ' ', 0) . '   ('.
              str_pad( number_format(($qtde*100/$totREGS), 2, ',', '.').'%', 8, ' ', 0) .")\n";
  
        $total += $qtde; 
        fwrite($Arq, $info );
    		$lin++;
      }
    }
  }
  // se formato rel= listar registros
  else if ($formatoREL==1) {
    while ($row = mysql_fetcH_object($resultado)) { 
    
      $repre='';
      if ( $row->idCORRETOR=='' && $row->corretorEXCEL!='') 
        $repre = substr("$row->corretorEXCEL", 0, 24);
      else if ($row->idCORRETOR!='')     
        $repre = substr("$row->nomeCORRETOR ($row->idCORRETOR)", 0, 24);
  
      $indicacao = '';
      if ( $row->idINDICACAO=='' && $row->indicacaoEXCEL!='')
        $indicacao = substr($row->indicacaoEXCEL, 0, 20);
      else if ($row->idINDICACAO!='')  
        $indicacao = substr("$row->nomeINDICACAO ($row->idINDICACAO)", 0, 20);
  
      $operadora = '';
      if ( $row->idOPERADORA=='' && $row->operadoraEXCEL!='')
        $operadora = substr($row->operadoraEXCEL, 0, 20);
      else if ($row->idOPERADORA!='')
        $operadora = substr("$row->nomeOPERADORA ($row->idOPERADORA)", 0, 20);
  
      $fones = substr($row->telefones, 0, 17);
      $nome = substr($row->nome, 0, 24);
      $tipo = substr($row->tabela, 0, 13);
      $situacao = ($tipoREL==3 || $tipoREL==1) ? substr($row->situacao, 0, 14) : '-';
  
  		$info=$row->dataMOSTRAR . '  '.
            str_pad($situacao, 14, ' ', 1) .'  ' .
            str_pad($tipo, 13, ' ', 1) .'  ' .
            str_pad($repre, 24, ' ', 1) .'  ' .
            str_pad($nome, 24, ' ', 1) .'  ' .
            str_pad($fones, 17, ' ', 1) .'  ' .
            str_pad($operadora, 20, ' ', 1) .'  ' .
            str_pad($indicacao, 20, ' ', 1) ." \n";

      if ( strpos($row->nome, 'PERDEU')!==false ) {
        //
      } else {
        if ($tipoREL==4) {
          // atend presencial/demais ligacoes totaliza separado
          if ($row->tabela=='Presencial') $total_2++;
          else $total++;     
        }
        else
          $total++; 
      }  

  		if ($lin + 1 > 55)   cabecalho();
      fwrite($Arq, $info );
  		$lin++;
    }
  }

  // se formato rel= extrair email, telefones
  else if ($formatoREL==4) {
    $info = '<table><tr>'.  
            '<td><font color=blue size="=1">Data</font></td>'.
            '<td><font color=blue size="=1">Tipo contato</td>'.
            '<td><font color=blue size="=1">Produto</td>'.
            '<td><font color=blue size="=1">Origem</td>'.
            '<td><font color=blue size="=1">Corretor</td>'.
            '<td><font color=blue size="=1">Nome</td>'.
            '<td><font color=blue size="=1">Telefones</td>'.
            '<td><font color=blue size="=1">E-mail</td>'.
            '</tr>';

    fwrite($Arq, $info );

    while ($row = mysql_fetcH_object($resultado)) { 
    
      $repre = "$row->nomeCORRETOR ($row->idCORRETOR)";
      $indicacao = '';
      if ($row->idINDICACAO!='')  
        $indicacao = "$row->nomeINDICACAO ($row->idINDICACAO)";
  
      $operadora = '';
      if ($row->idOPERADORA!='')
        $operadora = "$row->nomeOPERADORA ($row->idOPERADORA)";
  
  		$info='<tr>'.
          "<td align=left>$row->dataMOSTRAR</td>".
          "<td align=left>$row->tabela</td>".
          "<td align=left>$operadora</td>".
          "<td align=left>$indicacao</td>".
          "<td align=left>$repre</td>".
          "<td align=left>$row->nome</td>".
          "<td align=left>$row->telefones</td>".
          "<td align=left>$row->email</td>".
          '</tr>';

      fwrite($Arq, $info );
    }
  }

  if ($formatoREL!=4) { 
  	if ($lin + 3 > 55)   cabecalho();
  
    if ($tipoREL==4) {
    	$info=str_pad(' TOTAL liga��es (liga��es perdidas n�o s�o consideradas): ', 60, ' ', 0) .'  ' .
            str_pad($total, 6, ' ', 0) . "\n";
    	$info2=str_pad(' TOTAL presen�as : ', 60, ' ', 0) .'  ' .
            str_pad($total_2, 6, ' ', 0) . "\n";
      fwrite($Arq, "\n"  );
      fwrite($Arq, $info );
      fwrite($Arq, $info2 );

    } 
    else {
    	$info=str_pad(' TOTAL (liga��es perdidas n�o s�o consideradas): ', 60, ' ', 0) .'  ' .
            str_pad($total, 6, ' ', 0) . "\n";
      fwrite($Arq, "\n"  );
      fwrite($Arq, $info );
    }

    
  }
  // gerado XLS
  else
    $resp = "../ajax/txts/$txt";
  
  fclose($Arq);
}




/*****************************************************************************************/
if ($acao=='cheques') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];
  $tipoREL = $_REQUEST['tipoREL'];

  $sql  = "select date_format(cx.dataop, '%d/%m/%y') as dataCAIXA, date_format(pag.datacheque, '%d/%m/%y') as dataCHEQUE, ".
          "         pag.valor, pag.cheque, pag.idBANCO, ban.nome as nomeBANCO, con.entOUsai, pag.infoCHEQUE as cpf, pag.nomeCHEQUE ".
          "from caixa cx ".
          "inner join pagamentos pag ".
          "  on cx.numreg=pag.idCAIXA ".
          "inner join bancos ban ".
          "  on ban.numero=pag.idBANCO ".
          "inner join contas con ".
          "  on con.numero=cx.idOPERACAO ".
          "where ifnull(cx.alterada2_excluida1,0)=0 and date_format(cx.dataop, '%Y%m%d') between '$dataini' and '$datafin' and tipoPGTO='CHEQUE' ".
          "@criterioCHEQUE ";

  if ( $tipoREL=='2' ) {
    $sql = str_replace('@criterioCHEQUE', " and con.entOUsai='E'  " , $sql);
    $palavra='RECEBIDOS';    
  }
  else if ( $tipoREL=='1' ) {
    $sql = str_replace('@criterioCHEQUE', " and con.entOUsai='S'   " , $sql);
    $palavra='ENVIADOS';     
  }
  else  {
    $sql = str_replace('@criterioCHEQUE', '', $sql);
    $palavra='ENVIADOS E RECEBIDOS';
  }

  $titREL = "Cheques $palavra no per�odo: $dataIniMostrar a $dataFinMostrar";
  $titulos="";
  
  $headers=
    '                                                                 Data do   Data da                                                            |'.
	  "Banco                                     N� cheque       Valor  cheque    opera��o  CPF/CNPJ            Nome                       Opera��o|".
    str_repeat('-', 150 );
//   xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxx  99.999,99  99/99/99  99/99/99  xxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxx  envio    
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');




//die($sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  if (mysql_num_rows($resultado)==0) die('nada');

	$pagina = 0;  
  $lin = 200;
  $soma=0;
	
  while ($row = mysql_fetcH_object($resultado)) {

    $operacao= $row->entOUsai=='S' ? 'Envio' : 'Recebimento';
    $banco = substr($row->nomeBANCO, 0,30).' ('.$row->idBANCO.')';

    $soma += $row->valor;
		$info=str_pad($banco, 40, ' ', 1) .' ' .
					str_pad($row->cheque, 10, ' ', 0) .'   ' .
					str_pad(number_format($row->valor, 2, ',', '.'), 9, ' ', 0) . '  '.
          str_pad($row->dataCHEQUE, 8, ' ', 0).'  '.
          str_pad($row->dataCAIXA, 8, ' ', 0).'  '.
          str_pad($row->cpf, 18, ' ', 1).'  '.
          str_pad($row->nomeCHEQUE, 25, ' ', 1).'  '.
          "$operacao \n";

		if ($lin + 1 > 55)   cabecalho();
    fwrite($Arq, $info );
		$lin++;
  }
  if ($lin + 2 > 55)   cabecalho();
  fwrite($Arq, "\n");
  fwrite($Arq, 'TOTAL: R$ '. number_format($soma, 2, ',', '.'));


  fclose($Arq);
}


/*****************************************************************************************/
if ($acao=='caixa_agrupadores') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $tipoREL = $_REQUEST['tipoREL'];

  $titREL = 'Relatorio de caixa por agrupadores';
  $titulos="per�odo: $dataIniMostrar a $dataFinMostrar";
  
  $headers=
    '                                                                                                                                                                                                   |'. 
    '                                                    Saldo                                 |'. 		
	  'Data   Opera��o                                    anterior       Sa�da     Entrada         |'.
    str_repeat('-', 90);
//   99/99  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999.999,99  99.999,99  99.999,99
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $sql  = "select cx.numreg as idOP,  date_format(cx.dataOP, '%d/%m') as dataOPERACAO, plano.tipoCAIXA, " .
          "ifnull(plano.nome, '* erro *') as descCONTA, cx.descOPERACAO, plano.entOUsai, " .            
          " cx.idOPERACAO, cx.valor as vlrCAIXA ".
          "from caixa cx " .
          "left join contas plano  " .
          "	  on plano.numero = cx.idOPERACAO " .
          " where ifnull(cx.alterada2_excluida1,0)=0 and date_format(cx.dataop, '%Y%m%d') between '$dataini' and '$datafin' @criterioCAIXA order by cx.dataOP desc ";

  if ($tipoREL=='1') $sql = str_replace('@criterioCAIXA', " and plano.tipoCAIXA='E' ", $sql); 
  else $sql = str_replace('@criterioCAIXA', '', $sql);
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

	$pagina = 0;  
  $lin = 200;
	
	$totSAIDA=0;
  $totENTRADA=0;

	$idCAIXA_ATUAL=-1; 
  
  while ($row = mysql_fetcH_object($resultado)) {
    $mostrarPGTOS=false;
    if ($idCAIXA_ATUAL==-1) $idCAIXA_ATUAL=$row->idOP;
    else { 
		  if ($idCAIXA_ATUAL!=$row->idOP) $mostrarPGTOS=true;  
    }

    if ($mostrarPGTOS) {
      $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO, pg.idOPERADORA, pg.idREPRESENTANTE, cheque, pg.infoCHEQUE, ".
             "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
             " ifnull(ban.nome, '* ERRO *') as nomeBANCO,  idPagouBoleto, ".
            " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
             " ifnull(repre.nome, '* ERRO *') as nomeREPRE ".
             "from pagamentos pg ".
             "left join representantes repre ".
             "    on repre.numero=pg.idREPRESENTANTE ".
             "left join operadores ope " .
             "	  on ope.numero = pg.idPagouBoleto " .
             "left join bancos ban " .
             "	  on ban.numero = pg.idBANCO " .
             "where idCAIXA=$idCAIXA_ATUAL";

      $pags = mysql_query($sql) or die (mysql_error());
      $strPAG=' ';  
  		
      while ($pag = mysql_fetcH_object($pags) )  {
   			$tipo=$pag->tipoPGTO;
 			
        if ($tipo=='CHEQUE') {
          $strPAG = '     CHEQUE N�: '.
                    str_pad($pag->cheque, 6, ' ', 0) .'   Data: ' .
  									str_pad($pag->dataCHEQUE, 8, ' ', 0) .'   Banco: ' .
  	  							str_pad(trim($pag->nomeBANCO) . " ($pag->idBANCO)", 40, ' ', 1);

        }
        else if ($tipo=='BOLETO') {
          if ($pag->nomeBOLETOPAGO!='')  
            $boletoPAGO= "PAGO: $pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
          else 
            $boletoPAGO= "PAGO: N+O";

          $strPAG = '     BOLETO: '.
  	  							" N� $pag->infoCHEQUE   Valor: $valorPAGO_2   Vencimento: $pag->dataCHEQUE   $boletoPAGO"; 
        }
        else if ($tipo=='VALE CR�DITO') {
          $strPAG = '     VALE CR�DITO N�: '.
                    str_pad($pag->infoCHEQUE, 6, ' ', 0); 
        }
        else if ($tipo=='VALE') {
          $strPAG = '     ADIANTAMENTO DE PROPOSTA (VALE)  Corretor: '.
  	  							str_pad(trim($pag->nomeREPRE) . " ($pag->idREPRESENTANTE)", 30, ' ', 1);
        }
        else if ($tipo=='CART+O') {
          $strPAG = '     CART+O D+BITO/CR+DITO  ';
        }
        else if ($tipo=='DINHEIRO') {
          $strPAG = '     DINHEIRO'; 
        }
    		if ($lin + 1 > 55)   cabecalho();
		    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
    		$lin++;
      }
      if ($valeGERADO!=-1) {
        $strPAG = '     CR+DITO CHEQUE CORRETOR N�: '.
                  str_pad($valeGERADO, 6, ' ', 0).'  '.
                  ' Corretor: '.$reprevaleGERADO; 
          
    		if ($lin + 1 > 55)   cabecalho();
		    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
    		$lin++;
      }
    }
    $idCAIXA_ATUAL=$row->idOP;

    $entrada='';    $saida='';
	  if ($row->entOUsai=='E') {$entrada= number_format($row->vlrCAIXA , 2, ',', '.'); $totENTRADA+=$row->vlrCAIXA;}
	  else {$saida= number_format($row->vlrCAIXA , 2, ',', '.'); $totSAIDA+=$row->vlrCAIXA;}

    $descricao= substr(substr($row->descCONTA, 0, 19) . ' ('.$row->idOPERACAO.') ', 0, 40);
  	$info= str_pad($row->dataOPERACAO, 5, ' ', 1) .'  ' .
					str_pad($descricao, 40, ' ', 1) .'  ' .
					str_pad('', 10, ' ', 0).'  '.
					str_pad($saida, 10, ' ', 0).'  '.
					str_pad($entrada, 10, ' ', 0).'         ';

		if ($lin + 1 > 55)   cabecalho();
    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad('<negrito>'.$info, 80, ' ', 1) . "\n");
		$lin++;
  }


  if ($lin + 2 > 55)   cabecalho();

  fwrite($Arq, (($lin % 2==0) ? '<cinza>' : ''). "\n");
  $lin++;
  $total = str_pad('TOTAL:      ', 61, ' ', 0) .
           str_pad(number_format($totSAIDA, 2, ',', '.'), 10, ' ', 0). '  '.  
           str_pad(number_format($totENTRADA, 2, ',', '.'), 10, ' ', 0).  "              \n";
  fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').$total . "\n");



  // soma agrupadores
  $sql  = "select agr.nome, sum(cx.valor) as total ".  
          "from caixa cx ".
          "left join contas plano ".
          "  on plano.numero = cx.idOPERACAO ".
          "left join agrupadores agr ".
          "  on agr.numero = plano.idAGRUPADOR ".
          "where ifnull(cx.alterada2_excluida1,0)=0 and date_format(cx.dataop, '%Y%m%d') between '$dataini' and '$datafin' @criterioCAIXA ".
          "and agr.nome is not null ".
          "group by agr.nome ".
          "order by agr.nome ";

  if ($tipoREL=='1') $sql = str_replace('@criterioCAIXA', " and plano.tipoCAIXA='E' ", $sql); 
  else $sql = str_replace('@criterioCAIXA', '', $sql);
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  $lin = 200;
	
  $headers=
	  'Agrupador                                                Valor |'.
//   xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999.999,99
    str_repeat('-', 80);


  while ($row = mysql_fetcH_object($resultado)) {
    if ($lin + 2 > 55)   cabecalho();
  
    $info = str_pad($row->nome, 50, ' ', 1) . '  '.
           str_pad(number_format($row->total, 2, ',', '.'), 10, ' ', 0);
    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').$info . "\n");

    $lin++;
  }
  fclose($Arq);
}




/*****************************************************************************************/
if ($acao=='boletos') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];
  $tipoREL = $_REQUEST['tipoREL'];

  $titREL = "Boletos com vencimento per�odo: $dataIniMostrar a $dataFinMostrar";
  $titulos="";
  
  $headers=
	  'N� boleto             Data vencimento  Valor R$  N� opera��o de caixa  Pago?                                         |'.
    str_repeat('-', 130   );
//   xxxxxxxxxxxxxxxxxxxx  99/99/99         9999,99   999999                xxx      
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $dataTRAB = $dataini;
  
  $sql  = "select infoCHEQUE as numBOLETO, date_format(pg.dataCHEQUE, '%d/%m/%y') as dataVENC, pg.valor, ". 
           " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, pg.idPagouBoleto, pg.idCAIXA ".            
          "from pagamentos pg " .
          "left join operadores ope " .
          "	  on ope.numero = pg.idPagouBoleto " .
          "  where date_format(pg.dataCHEQUE, '%Y%m%d') between '$dataini' and '$datafin' and pg.tipoPGTO='BOLETO'  ".
         " @criterioBOLETO order by pg.infoCHEQUE ";


  if ( $tipoREL=='2' ) $sql = str_replace('@criterioBOLETO', " and ifnull(pg.idPagouBoleto,'')='' " , $sql);
  else if ( $tipoREL=='3' ) $sql = str_replace('@criterioBOLETO', " and ifnull(pg.idPagouBoleto,'')<>'' " , $sql);
  else $sql = str_replace('@criterioBOLETO', '', $sql);

//die($sql);

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  if (mysql_num_rows($resultado)==0) die('nada');

	$pagina = 0;  
  $lin = 200;
	
  while ($row = mysql_fetcH_object($resultado)) {

    $pago= "N�o";
    if ($row->nomeBOLETOPAGO!='')  $pago= "$row->dataBOLETOPAGO - por: $row->nomeBOLETOPAGO ($row->idPagouBoleto)";

		$info=str_pad($row->numBOLETO, 20, ' ', 1) .'  ' .
					str_pad($row->dataVENC, 8, ' ', 1) .'         ' .
					str_pad(number_format($row->valor, 2, ',', ''), 7, ' ', 0) . '   '.
          str_pad($row->idCAIXA, 6, ' ', 0).'                '.
          $pago .  "                            \n";

		if ($lin + 1 > 55)   cabecalho();
    fwrite($Arq, $info );
		$lin++;
  }

  fclose($Arq);
}






/*****************************************************************************************/
if ($acao=='caixa_detalhado') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $titREL = 'Relatorio de caixa detalhado';
  $titulos_temp="per�odo: $dataIniMostrar a $dataFinMostrar".
  
  $headers=
    '                                                                                                                                                                                                   |'. 
    '                                                                                    Valor         Percent            Sa�das                                     |'. 		
	  'Data   Corretor/Funcion�rio   Opera��o                                    Valor  recebido        AllCross   Sa�das  Cheques   Cart�o  Cheques     Vale  Dinheiro|'.
    str_repeat('-', 160);
//   99/99  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999,99   9999,99  9999,99 (999%)  e999,99  t999,99  f999,99  X999,99  3999,99   d999,99
//  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  
  $sql  = "select numero, nome  " .
          "from representantes ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $repres=array();
  while ($row = mysql_fetcH_object($resultado)) {    
    $repres[$row->numero]=$row->nome;
  }
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $dataTRAB = $dataini;
  
  $sql  = "select cx.numreg as idOP,  date_format(cx.dataOP, '%d/%m') as dataOPERACAO, plano.tipoCAIXA, plano.tipoENVOLVIDO, " .
          "ifnull(plano.nome, '* erro *') as descCONTA, cx.descOPERACAO, ep.vlrADESAO,  plano.entOUsai, plano.saidaCHEQUE, tipoprop.cpf_cnpj, " .            
          "ifnull(tipoprop.descricao, '* erro *') as descTIPO_CONTRATO,  ep.idTIPO_CONTRATO, ep.percentualPRESTADORA, cx.temFormasPgto, " .
          " ep.vlrRECEBIDO, ep.cpf, ep.valor, ep.numreg as idENTREGA, ep.idREPRESENTANTE, cx.idFUNCIONARIO, cx.idOPERACAO, cx.valor as vlrCAIXA ".
          "from caixa cx " .
          "left join entregaspropostas ep  " .
          " 	on cx.numreg = ep.idCAIXA  " .
          "left join contas plano  " .
          "	  on plano.numero = cx.idOPERACAO " .
          "left join tipos_contrato tipoprop " .
          "	  on tipoprop.numreg = ep.idTIPO_CONTRATO " .
          " where ifnull(cx.alterada2_excluida1,0)=0 and date_format(cx.dataop, '%Y%m%d') between '$dataini' and '$datafin' @criterioTIPOCAIXA order by cx.dataOP desc ";

// verifica se usuario pode ver todo caixa ou so cx interno
  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $idUSUARIO = $infoUSUARIO[1]; 

  $resultado = mysql_query("select permissoes from operadores where numero = $idUSUARIO ", $conexao) or die (mysql_error());

  $row = mysql_fetcH_object($resultado);
  $permissoes=$row->permissoes;

  // usuario tem acesso cx geral/todo plano de contas
  if ( strpos($permissoes, 'H')!==false || $idUSUARIO==1) 
	   $sql = str_replace('@criterioTIPOCAIXA', '' , $sql);
  else
	   $sql = str_replace('@criterioTIPOCAIXA', " and plano.tipoCAIXA='I' ", $sql);
 

//die($sql);
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

	$pagina = 0;  
  $lin = 200;
	
	$totSAIDA=0;
  $totSAIDA_CHEQUE=0;
  $totCARTAO=0;
  $totCHEQUE=0;
  $totVALE=0;
  $totDINHEIRO=0;

	$idCAIXA_ATUAL=-1; $opENTREGA=false;
  $percPRESTADORA_CALC=0;  $ultSOMATORIAPROPOSTAS=0;
  
  while ($regCAIXA = mysql_fetcH_object($resultado)) {
    $mostrarPGTOS=false;
    if ($idCAIXA_ATUAL==-1) $idCAIXA_ATUAL=$regCAIXA->idOP;
    else { 
		  if ($idCAIXA_ATUAL!=$regCAIXA->idOP) {
        $mostrarPGTOS=true;
      }  
    }
    

    // os pagamentos sao mostrados qdo o looping encontra uma operacao de caixa diferente,
    // ou seja.......imprime a linha com a operacao caixa, verifica operacao caixa mudou ($idCAIXA_ATUAL), le/imprime os pagamentos da
    // ultima operacao impressa

    // quando a operacao caixa for uma entrega ($opENTREGA==true), alem de listar os pgtos, posiciona os valores dos pagamentos
    // nas colunas respectivas: cartao, cheque, etc, E soma os valores cartao, cheque, etc 

    // quando a operacao caixa NAO for uma entrega ($opENTREGA==false), lista os pgtos, mas nao mostra/soma valores dos pgtos,
    // quando imprimindo operacao deste tipo...valores sao relacionados/somados somente na linha indicativa da operacao do caixa  
    if ($mostrarPGTOS) {
      // verifica se a operacao de caixa tem vale credito gerado
      $sql="select cred.representante, cred.numVALE_CREDITO, rep.nome, cred.valor  ".
            "from creditos_descontos cred ".  
            "left join representantes rep ".
            "on   rep.numero=cred.representante ".
            "where idCAIXA=$idCAIXA_ATUAL and tipo='C' ";
      $rsVALE = mysql_query($sql) or die (mysql_error());
      $valeGERADO=-1;
      if ( mysql_num_rows($rsVALE)>0 )  {
        $regVALE = mysql_fetcH_object($rsVALE); 
        $valeGERADO=$regVALE->numVALE_CREDITO; $reprevaleGERADO = str_pad(trim($regVALE->nome) . " ($regVALE->representante)", 30, ' ', 1);
        $vlrvaleGERADO=$regVALE->valor;
      }
      mysql_free_result($rsVALE);  

      $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO, pg.idOPERADORA, pg.idREPRESENTANTE, cheque, pg.infoCHEQUE, ".
             "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
             " ifnull(ban.nome, '* ERRO *') as nomeBANCO,  idPagouBoleto, ".
            " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
             " ifnull(repre.nome, '* ERRO *') as nomeREPRE ".
             "from pagamentos pg ".
             "left join representantes repre ".
             "    on repre.numero=pg.idREPRESENTANTE ".
             "left join operadores ope " .
             "	  on ope.numero = pg.idPagouBoleto " .
             "left join bancos ban " .
             "	  on ban.numero = pg.idBANCO " .
             "where idCAIXA=$idCAIXA_ATUAL";

      $pags = mysql_query($sql) or die (mysql_error());
      
      $qtdeCHEQUE=0;
      while ($pag = mysql_fetcH_object($pags) )  {
   			$tipo=$pag->tipoPGTO;
        if ($tipo=='CHEQUE') {
          $qtdeCHEQUE++;
        }
      }        
      mysql_data_seek($pags, 0);  
                

      $strPAG=' ';  
  		
  		$valorPAGO = 0;
  		$valorPAGO_2 = number_format(0, 2, ',', '')  ;
      $contCHEQUE=0; 
      while ($pag = mysql_fetcH_object($pags) )  {
   			$tipo=$pag->tipoPGTO;
 			
  			$valorPAGO = $pag->valor;
  			$valorPAGO_2 = number_format($pag->valor, 2, ',', '')  ; 
  
        if ($tipo=='CHEQUE') {
          $strPAG = 'CHEQUE N�: '.
                    str_pad($pag->cheque, 6, ' ', 0) .'   Data: ' .
  									str_pad($pag->dataCHEQUE, 8, ' ', 0) .'   Banco: ' .
  	  							str_pad(trim($pag->nomeBANCO) . " ($pag->idBANCO)", 40, ' ', 1);

          $contCHEQUE++;
          if ($opENTREGA) {
            if ($qtdeCHEQUE<=1) {
              if ($pag->valor>=$percPRESTADORA_CALC) {
                $valorPAGO_2 = number_format($percPRESTADORA_CALC, 2, ',', '')  ;
                $totCHEQUE += $percPRESTADORA_CALC;
              } 
              else {
                $valorPAGO_2 = number_format($pag->valor, 2, ',', '')  ;
                $totCHEQUE += $pag->valor;
              }
              $strPAG = str_repeat(' ',5) . 
                        str_pad($strPAG, 129, ' ', 1) .
                        str_pad($valorPAGO_2, 7, ' ', 0) ;
            }
            else {
              if ($contCHEQUE==$qtdeCHEQUE) {
                $totCHEQUE += $ultSOMATORIAPROPOSTAS;              
              
                $strPAG = str_repeat(' ',5) . 
                          str_pad($strPAG, 129, ' ', 1) .
                          str_pad(number_format($ultSOMATORIAPROPOSTAS, 2, ',', ''), 7, ' ', 0) ;
              }                          
              else
                $strPAG = str_repeat(' ',5) . 
                          str_pad($strPAG, 129, ' ', 1) ;
            }                        
          }
          else {
            if ($entOUSai=='E' ) {
              $strPAG = str_repeat(' ',5) . 
                        str_pad($strPAG, 129, ' ', 1) .
                        str_pad($valorPAGO_2, 7, ' ', 0) ;
              $totCHEQUE += $pag->valor;
            }
            else {
              $strPAG = str_repeat(' ',5) . 
                        str_pad($strPAG, 111, ' ', 1) .
                        str_pad($valorPAGO_2, 7, ' ', 0) ;
              $totSAIDA_CHEQUE += $pag->valor;
            }
          }
        }
        else if ($tipo=='BOLETO') {
          if ($pag->nomeBOLETOPAGO!='')  
            $boletoPAGO= "PAGO: $pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
          else 
            $boletoPAGO= "PAGO: N�O";

          $strPAG = 'BOLETO: '.
  	  							" N� $pag->infoCHEQUE   Valor: $valorPAGO_2   Vencimento: $pag->dataCHEQUE   $boletoPAGO"; 

          if ($opENTREGA) {
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 138, ' ', 1) .
                      str_pad($valorPAGO_2, 7, ' ', 0);
            $totVALE += $pag->valor;
          }
          else
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 138, ' ', 1) ;

        }
        else if ($tipo=='VALE CR�DITO') {
          $strPAG = 'VALE CR�DITO N�: '.
                    str_pad($pag->infoCHEQUE, 6, ' ', 0); 
            
          if ($opENTREGA) {
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 129, ' ', 1) .
                      str_pad($valorPAGO_2, 7, ' ', 0) ;
            $totCHEQUE += $pag->valor;
          }
          else
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 129, ' ', 1) ;

        }
        else if ($tipo=='VALE') {
          $strPAG = 'ADIANTAMENTO DE PROPOSTA (VALE)  Corretor: '.
  	  							str_pad(trim($pag->nomeREPRE) . " ($pag->idREPRESENTANTE)", 30, ' ', 1);
            
          if ($opENTREGA) {
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 138, ' ', 1) .
                      str_pad($valorPAGO_2, 7, ' ', 0) ;
            $totVALE += $pag->valor;
          }
          else
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 138, ' ', 1) ;

        }
        else if ($tipo=='CART�O') {
          $strPAG = 'CART�O D�BITO/CR�DITO  ';
            
          if ($opENTREGA) {
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 120, ' ', 1) .
                      str_pad($valorPAGO_2, 7, ' ', 0) ;
            $totCARTAO += $pag->valor;
          }
          else
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 120, ' ', 1) ;

        }
        else if ($tipo=='DINHEIRO') {
          $strPAG = 'DINHEIRO'; 
            
          if ($opENTREGA) {
            $strPAG = str_pad('', 5, ' ', 0) . 
                      str_pad($strPAG, 148, ' ', 1) .
                      str_pad($valorPAGO_2, 7, ' ', 0) ;
            $totDINHEIRO += $pag->valor;
          }
          else {
            if ($entOUSai=='E' ) {
              $strPAG = str_repeat(' ',5) . 
                        str_pad($strPAG, 148, ' ', 1) .
                        str_pad($valorPAGO_2, 7, ' ', 0) ;
              $totDINHEIRO += $pag->valor;
            }
            else {
              $strPAG = str_repeat(' ',5) . 
                        str_pad($strPAG, 102, ' ', 1) .
                        str_pad($valorPAGO_2, 7, ' ', 0) ;
              $totSAIDA += $pag->valor; 
            }
          }
        }

    		if ($lin + 1 > 40)   cabecalho();
		    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
    		$lin++;
      }
      if ($valeGERADO!=-1) {
        $strPAG = '     CR�DITO CHEQUE CORRETOR N�: '.
                  str_pad($valeGERADO, 6, ' ', 0).'  '.
                  ' Corretor: '.$reprevaleGERADO; 
          
        if ($opENTREGA) {
          $strPAG = str_pad($strPAG, 134, ' ', 1) .
                    str_pad(number_format($vlrvaleGERADO, 2, ',', ''), 7, ' ', 0) ;
  
          $totCHEQUE += $vlrvaleGERADO;
        }

    		if ($lin + 1 > 40)   cabecalho();
		    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
    		$lin++;
      }
      $percPRESTADORA_CALC=0;
      $ultSOMATORIAPROPOSTAS=0;      
    }
    $idCAIXA_ATUAL=$regCAIXA->idOP;
    $entOUSai=$regCAIXA->entOUsai;
    
   
		// idENTREGA=='', significa que nao ha entrega vinculada, o registro � uma operacao de cx simples
    $data=$regCAIXA->dataOPERACAO;
  	$vlrRECEBIDO = number_format($regCAIXA->vlrRECEBIDO, 2, ',', '');
    $valor = number_format($regCAIXA->vlrCAIXA, 2, ',', '');
    $percPRESTADORA = '';    

    $infoCOMPLEMENTAR='';
    if ($regCAIXA->idENTREGA!='') {
      $valor = number_format($regCAIXA->vlrCAIXA, 2, ',', '');
      $valorCAIXA = $regCAIXA->vlrCAIXA;

      if ($regCAIXA->percentualPRESTADORA>0) {   
        $percPRESTADORA = str_pad(number_format($regCAIXA->vlrADESAO + ($regCAIXA->vlrRECEBIDO) * ($regCAIXA->percentualPRESTADORA/100), 2, ',', ''), 7, ' ', 0).' ('.
                        str_pad(number_format($regCAIXA->percentualPRESTADORA, 0, ',', ''), 3, ' ', 0).'%)';
        $percPRESTADORA_CALC += $regCAIXA->vlrRECEBIDO * ($regCAIXA->percentualPRESTADORA/100);
        $percPRESTADORA_CALC+=$regCAIXA->vlrADESAO;
        
        $ultSOMATORIAPROPOSTAS += $regCAIXA->vlrRECEBIDO * ($regCAIXA->percentualPRESTADORA/100);
        $ultSOMATORIAPROPOSTAS +=$regCAIXA->vlrADESAO;                      
      }

			$envolvido = substr($repres[$regCAIXA->idREPRESENTANTE], 0, 15) . ' ('.$regCAIXA->idREPRESENTANTE.')';
			$cpf = $regCAIXA->cpf ;
			$contrato = "$regCAIXA->descTIPO_CONTRATO";
      $operacao = $regCAIXA->descOPERACAO=='' ? '' : " - $regCAIXA->descOPERACAO"; 
      $descricao= substr(substr($regCAIXA->descCONTA, 0, 19) . ' ('.$regCAIXA->idOPERACAO.') '.$operacao, 0, 40);

      $opENTREGA=true;

  		$info= str_pad($data, 5, ' ', 1) .'  ' .
  					str_pad($envolvido, 21, ' ', 1) .'  ' .
  					str_pad($descricao, 40, ' ', 1) .'  ' .
  					str_pad($valor, 7, ' ', 0).'   '.
  					str_pad($vlrRECEBIDO, 7, ' ', 0).'  '.
  					str_pad($percPRESTADORA, 13, ' ', 0);

  		$infoCOMPLEMENTAR= ($regCAIXA->cpf_cnpj==1 ? 'CPF: ' : 'CNPJ: ')."$cpf      Tipo de contrato: $contrato ($regCAIXA->idTIPO_CONTRATO)";

/*
      if ($row->temFormasPgto=='0') { 
        $info = str_pad($info, 182, ' ', 1) .
         str_pad($vlrRECEBIDO, 7, ' ', 0) ;
      }
*/
  
    }
		// operacao caixa diferente NAO + entrega proposta
		else {
      // se operacao caixa envolve uma conta cujo envovlido � um CORRETOR, considera que o numero � de um CORRETOR obviamente
      // caso contrario, considera como numero de um funcionario
//    if ($regCAIXA->tipoENVOLVIDO=='C') 
//	  $envolvido = substr($repres[$regCAIXA->idFUNCIONARIO], 0, 15) . ' ('.$regCAIXA->idFUNCIONARIO.')';
//    else
//	  $envolvido = substr($funcionarios[$regCAIXA->idFUNCIONARIO], 0,15) . ' ('.$regCAIXA->idFUNCIONARIO.')';
  	  $envolvido = substr($repres[$regCAIXA->idFUNCIONARIO], 0, 15) . ' ('.$regCAIXA->idFUNCIONARIO.')';
			$cpf='';
			$contrato = '';
//			$descricao= substr($row->descCONTA, 0, 19) . ' ('.$row->idOPERACAO.')';

      $operacao = $regCAIXA->descOPERACAO=='' ? '' : " - $regCAIXA->descOPERACAO"; 
      $descricao= "$regCAIXA->descCONTA ($regCAIXA->idOPERACAO) $operacao";

//			$descricao= substr($row->idOPERACAO, 0, 19);
				
      $opENTREGA=false;

  		$info= str_pad($data, 5, ' ', 1) .'  ' .
  					str_pad($envolvido, 21, ' ', 1) .'  ' .
  					str_pad($descricao, 40, ' ', 1) ;

/*
      if ($row->entOUsai=='S') {
        if ($row->saidaCHEQUE!=1) { 
          $info = str_pad($info, 107, ' ', 1) .
                   str_pad($valor, 7, ' ', 0) ;
          $totSAIDA += $row->vlrCAIXA;
        } 
        else {
          $info = str_pad($info, 116, ' ', 1) .
                  str_pad($valor , 7, ' ', 0) ;
          $totSAIDA_CHEQUE += $row->vlrCAIXA ;
        }
      }
      else {
         $info = str_pad($info, 153, ' ', 1) .
                   str_pad($valor, 7, ' ', 0) ;
         $totDINHEIRO += $row->vlrCAIXA;
      }
*/


//Data   Corretor/Funcion�rio   Opera��o                                    Valor  recebido        AllCross   Sa�das  Cheques   Cart�o  Cheques     Vale  Dinheiro|'.
//99/99  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999,99   9999,99  9999,99 (999%)  e999,99  t999,99  f999,99  X999,99  3999,99   d999,99


    }
		if ($lin + 1 > 40)   cabecalho();
    fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad('<negrito>'.$info, 160, ' ', 1) . "\n");
		$lin++;

    if ($infoCOMPLEMENTAR!='') {
  		if ($lin + 1 > 40)   cabecalho();
      fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').'<negrito>'.$infoCOMPLEMENTAR . "\n");
  		$lin++;
    }

  }



  // verifica se a operacao de caixa tem vale credito gerado
  $sql="select cred.representante, cred.numVALE_CREDITO, rep.nome, cred.valor  ".
        "from creditos_descontos cred ".  
        "left join representantes rep ".
        "on   rep.numero=cred.representante ".
        "where idCAIXA=$idCAIXA_ATUAL and tipo='C';";
  $rsVALE = mysql_query($sql) or die (mysql_error());
  $valeGERADO=-1;
  if ( mysql_num_rows($rsVALE)>0 )  {
    $regVALE = mysql_fetcH_object($rsVALE); 
    $valeGERADO=$regVALE->numVALE_CREDITO; $reprevaleGERADO = str_pad(trim($regVALE->nome) . " ($regVALE->representante)", 30, ' ', 1);
    $vlrvaleGERADO=$regVALE->valor;
  }
  mysql_free_result($rsVALE);  

	// trecho a seguir aplica se a operacoes de caixa que sao entregas de propostas
  $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO, pg.idOPERADORA, pg.idREPRESENTANTE, cheque, pg.infoCHEQUE, ".
         "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
         " ifnull(ban.nome, '* ERRO *') as nomeBANCO,  idPagouBoleto, ".
            " date_format(pg.dataBOLETOPAGO, '%d/%m/%y') as dataBOLETOPAGO, ope.nome as nomeBOLETOPAGO, ".
         " ifnull(repre.nome, '* ERRO *') as nomeREPRE ".
         "from pagamentos pg ".
         "left join representantes repre ".
         "    on repre.numero=pg.idREPRESENTANTE ".
         "left join operadores ope " .
         "	  on ope.numero = pg.idPagouBoleto " .
         "left join bancos ban " .
         "	  on ban.numero = pg.idBANCO " .
         "where idCAIXA=$idCAIXA_ATUAL";

  $pags = mysql_query($sql) or die (mysql_error());
  $strPAG=' ';  
	
	$valorPAGO = 0;
	$valorPAGO_2 = number_format(0, 2, ',', '')  ;
  while ($pag = mysql_fetcH_object($pags) )  {
		$tipo=$pag->tipoPGTO;
	
		$valorPAGO = $pag->valor;
		$valorPAGO_2 = number_format($pag->valor, 2, ',', '')  ; 

    if ($tipo=='CHEQUE') {
      $strPAG = 'CHEQUE N�: '.
                str_pad($pag->cheque, 6, ' ', 0) .'   Data: ' .
								str_pad($pag->dataCHEQUE, 8, ' ', 0) .'   Banco: ' .
  							str_pad(trim($pag->nomeBANCO) . " ($pag->idBANCO)", 40, ' ', 1);

      if ($opENTREGA) {
        if ($pag->valor>=$percPRESTADORA_CALC) {
          $valorPAGO_2 = number_format($percPRESTADORA_CALC, 2, ',', '')  ;
          $totCHEQUE += $percPRESTADORA_CALC;
        } 
        else {
          $valorPAGO_2 = number_format($pag->valor, 2, ',', '')  ;
          $totCHEQUE += $pag->valor;
        }
        $strPAG = str_repeat(' ',5) . 
                  str_pad($strPAG, 129, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0) ;
      }
      else
        $strPAG = str_repeat(' ',5) . 
                  str_pad($strPAG, 129, ' ', 1) ;

    }
    else if ($tipo=='BOLETO') {
      if ($pag->nomeBOLETOPAGO!='')  
        $boletoPAGO= "PAGO: $pag->dataBOLETOPAGO - por: $pag->nomeBOLETOPAGO ($pag->idPagouBoleto)";
      else 
        $boletoPAGO= "PAGO: N+O";

      $strPAG = 'BOLETO: '.
  	  							" N� $pag->infoCHEQUE   Valor: $valorPAGO_2   Vencimento: $pag->dataCHEQUE   $boletoPAGO"; 

      if ($opENTREGA) {
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 138, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0);
        $totVALE += $pag->valor;
      }
      else
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 102, ' ', 1) ;

    }
    else if ($tipo=='VALE CR�DITO') {
      $strPAG = 'VALE CR�DITO N�: '.
                str_pad($pag->infoCHEQUE, 6, ' ', 0); 
        
      if ($opENTREGA) {
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 129, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0) ;
        $totCHEQUE += $pag->valor;
      }
      else
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 129, ' ', 1) ;

    }
    else if ($tipo=='VALE') {
      $strPAG = 'ADIANTAMENTO DE PROPOSTA (VALE)  Corretor: '.
  							str_pad(trim($pag->nomeREPRE) . " ($pag->idREPRESENTANTE)", 30, ' ', 1);
        
      if ($opENTREGA) {
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 138, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0) ;
        $totVALE += $pag->valor;
      }
      else
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 138, ' ', 1) ;

    }
    else if ($tipo=='CART�O') {
      $strPAG = 'CART�O D�BITO/CR�DITO  ';
        
      if ($opENTREGA) {
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 120, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0) ;
        $totCARTAO += $pag->valor;
      }
      else
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 120, ' ', 1) ;

    }
    else if ($tipo=='DINHEIRO') {
      $strPAG = 'DINHEIRO'; 
        
      if ($opENTREGA) {
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 148, ' ', 1) .
                  str_pad($valorPAGO_2, 7, ' ', 0) ;
        $totDINHEIRO += $pag->valor;
      }
      else
        $strPAG = str_pad('', 5, ' ', 0) . 
                  str_pad($strPAG, 148, ' ', 1) ;
    }

		if ($lin + 1 > 40)   cabecalho();
		fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
		$lin++;
  }
  if ($valeGERADO!=-1) {
    $strPAG = '     CR�DITO CHEQUE CORRETOR N�: '.
              str_pad($valeGERADO, 6, ' ', 0).'  '.
              ' Corretor: '.$reprevaleGERADO; 
      
    if ($opENTREGA) {
      $strPAG = str_pad($strPAG, 134, ' ', 1) .
                str_pad(number_format($vlrvaleGERADO, 2, ',', ''), 7, ' ', 0) ;

      $totCHEQUE += $vlrvaleGERADO;
    }

		if ($lin + 1 > 40)   cabecalho();
		fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').str_pad($strPAG, 160, ' ', 1) . "\n");
//		fwrite($Arq, str_pad($strPAG, 160, ' ', 1) . "\n");
		$lin++;
  }


  if ($lin + 2 > 40)   cabecalho();

  fwrite($Arq, (($lin % 2==0) ? '<cinza>' : ''). "\n");
  $lin++;
  $total = str_pad('TOTAL:      ', 107, ' ', 0) .
           str_pad(number_format($totSAIDA, 2, ',', ''), 7, ' ', 0). '  '.  
           str_pad(number_format($totSAIDA_CHEQUE, 2, ',', ''), 7, ' ', 0). '  '.
           str_pad(number_format($totCARTAO, 2, ',', ''), 7, ' ', 0). '  '.
           str_pad(number_format($totCHEQUE, 2, ',', ''), 7, ' ', 0). '  '.
           str_pad(number_format($totVALE, 2, ',', ''), 7, ' ', 0). '   '.
           str_pad(number_format($totDINHEIRO, 2, ',', ''), 7, ' ', 0) . "\n";
  fwrite($Arq, (($lin % 2==0) ? '<cinza>' : '').$total . "\n");
	
	

  fclose($Arq);
}







/*****************************************************************************************/
if ($acao=='pj') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];

  $titREL = "Vendas de PJ cadastradas entre: $dataIniMostrar e $dataFinMostrar ";
  $dataINI = $_REQUEST['DATAINI'];
  $dataFIN = $_REQUEST['DATAFIN'];
  

  $headers = 
      "                                    Data de                                                                                            |".          
      "Operadora        Grupo              cadastro  Corretor                     Segurado                        CNPJ                Telefone|".
      str_repeat('-', 155);
//     xxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxx  99/99/99  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  00.000.000/0000-00  xxxxxxxxxxxxxxxxxxx     
                                                                                                                  
  
  $sql = "select ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, ifnull(grupo.nome, '* ERRO *') as nomeGRUPO, " .
         "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .
         "   prop.contratante, prop.idREPRESENTANTE,  repre.idGRUPO, prop.foneRES, prop.cpfCONTRATANTE " .
         "from propostas prop " .
         "left join operadoras opa " .
         "   on opa.numreg = prop.idOPERADORA " .
         "left join representantes repre " .
         "   on repre.numero = prop.idREPRESENTANTE " .
         "left join grupos_venda grupo " .
         "   on grupo.numreg = repre.idGRUPO " .
         "left join tipos_contrato tip ".
         "  on tip.numreg=prop.idTipoContrato ".
         " where date_format(dataCADASTRO, '%Y%m%d') between  '$dataINI' and '$dataFIN' and ifnull(prop.cancelada, 'N')<>'S' and cpf_cnpj=2 ".
         " order by contratante";       

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;

  $lin = 87;

  while ($row = mysql_fetch_object($resultado)) {  
    if ($lin + 1 > 55)    cabecalho();

    fwrite($Arq, str_pad(
                    substr(str_pad($row->nomeOPERADORA, 15, ' ', 1), 0, 15) .'  ' .
                    substr(str_pad($row->nomeGRUPO, 17, ' ', 1), 0, 17) .'  ' .  
                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .
                    str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 26, ' ', 1) .'  ' .
                    substr(str_pad($row->contratante, 30, ' ', 1), 0, 30) .'  ' .  
                    str_pad($row->cpfCONTRATANTE, 19, ' ', 1) . '  '.
                    $row->foneRES, 155, ' ', 1) ."\n");

    $lin++;
  }
  fclose($Arq);
}

/*****************************************************************************************/
if ($acao=='creditos') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];
    
  $dataINI = $_REQUEST['DATAINI'];
  $dataFIN = $_REQUEST['DATAFIN'];

  $idREPRE = $_REQUEST['repre'];
  $tipoREL = $_REQUEST['tipoREL'];
  $tipoBUSCA = $_REQUEST['tipobusca'];

  $marcarCOMOPAGO = $_REQUEST['baixar'];
    
  $idREPRE = '';
  $idREPRE = $_REQUEST['repre'];

  if ($tipoREL=='2') $filtro='Todos os vale cr�ditos ';
  else if ($tipoREL=='1') $filtro='Cr�ditos/descontos ';
  else if ($tipoREL=='3') $filtro='Vale cr�ditos n�o pagos';
  else if ($tipoREL=='4') $filtro='Vale cr�ditos pagos';

  if ($tipoBUSCA=='2') {$cmp='data';$titCMP='Data de registro';}
  else  {$cmp='pagarVALE';$titCMP='Data para pagar';} 

  $titREL = "$filtro $titCMP no per�odo: $dataIniMostrar e $dataFinMostrar ";

  // tipoREL=1 , creditos/descontos
  if ($tipoREL=='1') { 
    $headers = 
        "                                     Registrado                                                  Valor                                    |".          
        "Corretor                             em:         Tipo  Descri��o                                 R$       Contratante(s)                  |".
       str_repeat('-', 130);
  //     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  99/99/99    X     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999,99  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

    
  }
  else {
    $headers = 
        "                                     Registrado  Data                                                      Pago     Valor    Valor com   |".          
        "Corretor                             em:         pagar     Tipo  Descri��o                                 R$       R$       Desconto (%)|".
       str_repeat('-', 140);
  //     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  99/99/99    99/99/99  X     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999,99  9999,99  9999,99 (999%)
  }        

  $sql  = "select cx.numreg as idCAIXA, cx.idOPERACAO, cre.numero, cre.descricao, ifnull(rep.nome, '') as nomeREPRESENTANTE, " .
          " cre.representante as idREPRESENTANTE, date_format(data, '%d/%m/%y') as dataREGISTRO, ifnull(pagoVALE_CREDITO,0) as pagoVALE_CREDITO, ".
          " cre.valor, ucase(cre.tipo) as tipo, ifnull(cre.descontoVALE, 0) as descontoVALE, cre.numVALE_CREDITO , " .
          " date_format(pagarVALE, '%d/%m/%y') as pagarVALE_MOSTRAR  ".
          " from creditos_descontos cre ".
          "left join representantes rep ".
          " on rep.numero = cre.representante " .
          "left join caixa cx ".
          " on cx.numreg = cre.idCAIXA ".
          " where date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioTIPO   and ifnull(cre.excluido,0)=0 " .          
          "order by nomeREPRESENTANTE,data desc " ;

  if ($idREPRE=='9999')
    $sql = str_replace('@criterioREPRE', '', $sql); 
  else
    $sql = str_replace('@criterioREPRE', " and cre.representante=$idREPRE ", $sql);

  if ($tipoREL=='4') $sql = str_replace('@criterioTIPO', "  and ifnull(cre.numVALE_CREDITO, '')<>'' and ifnull(pagoVALE_CREDITO,0)<>0 "  , $sql);
  else if ($tipoREL=='3') $sql = str_replace('@criterioTIPO', "  and ifnull(cre.numVALE_CREDITO, '')<>'' and ifnull(pagoVALE_CREDITO,0)=0 "  , $sql); 
  else if ($tipoREL=='2') $sql = str_replace('@criterioTIPO', "  and ifnull(cre.numVALE_CREDITO, '')<>''  "  , $sql);
  else $sql = str_replace('@criterioTIPO', "  and ifnull(cre.numVALE_CREDITO, '')=''  ", $sql);


  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;

  $lin = 87;
  $repreATUAL = 'none';

  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado = $infoUSUARIO[1];

  $totVALOR=0;  $totDESCONTO=0;
  $totVALOR_G=0;  $totDESCONTO_G=0;
  while ($row = mysql_fetch_object($resultado)) {  

    if ($row->idREPRESENTANTE!=$repreATUAL) {     
      if ($lin + 2 > 55) cabecalho();

      if ($repreATUAL!='none') {

        if ($tipoREL=='1') {
          fwrite($Arq, '<negrito>'.str_pad(" TOTAL:      ", 97, ' ', 0) . str_pad(number_format($totVALOR, 2, ',', ''), 7, ' ', 0) . 
              "                            \n");
        } 
        else {
          fwrite($Arq, '<negrito>'.str_pad(" TOTAL:      ", 116, ' ', 0) .
              str_pad(number_format($totVALOR, 2, ',', ''), 7, ' ', 0) . '  '.
              str_pad(number_format($totDESCONTO, 2, ',', ''), 7, ' ', 0) . "                       \n");
        }

        $lin++; 
      }

      $totVALOR=0;$totDESCONTO=0;
      $repreATUAL = $row->idREPRESENTANTE;
    }  
    if ($lin + 1 > 55)    cabecalho();

    $repre=substr($row->nomeREPRESENTANTE, 0, 30);

    $desconto= $row->descontoVALE==0 ? '' : ' ('.str_pad($row->descontoVALE, 3, ' ', 0) .'%)';
//    $pago= $row->pagoVALE_CREDITO==1 ? 'Sim' : 'N�o';
    $vlrCOMDESCONTO=$row->valor - $row->valor*($row->descontoVALE/100);

    // tipoREL=1 , creditos/descontos
    if ($tipoREL=='1') {

      if ($row->tipo=='C') $totVALOR += $row->valor; else $totVALOR -= $row->valor; 
      if ($row->tipo=='C') $totDESCONTO += $row->valor - ($row->valor*($row->descontoVALE/100)); 
      else $totDESCONTO -= $row->valor-($row->valor*($row->descontoVALE/100));
  
      if ($row->tipo=='C') $totVALOR_G += $row->valor; else $totVALOR_G -= $row->valor; 
      if ($row->tipo=='C') $totDESCONTO_G += $row->valor-($row->valor*($row->descontoVALE/100)); 
      else $totDESCONTO_G -= $row->valor-($row->valor*($row->descontoVALE/100));


      // se for um credito/debito relacionado com uma entrega de proposta, tenta conseguir o nome do(s) contratante(s) envolvidos
      $contratantes='';
      if ($row->idOPERACAO==$contaENTREGA) {

        $sql= "select prop.contratante from caixa cx ".
              "inner join entregaspropostas ent ".
              "  on ent.idcaixa=cx.numreg ".
              "inner join propostas prop ".
              "  on prop.numregPropostaEntregueCaixa = ent.numreg ".
              "where cx.numreg=$row->idCAIXA ; ";
  
        $rsCONTRAT = mysql_query($sql) or die (mysql_error());
        if (mysql_num_rows($rsCONTRAT)>0) {
          while ($regCONTRAT = mysql_fetcH_object($rsCONTRAT)) {
            $contratantes .= $contratantes=='' ? '' : ',' ;
            $contratantes .= $regCONTRAT->contratante;
          }
        }
        mysql_free_result($rsCONTRAT);
      }
      if (strlen($contratantes)>45) $contratantes=substr($contratantes, 0, 42) . '....';

      $valor=number_format($row->valor, 2, ',', '');
      $valor = $row->tipo=='D' ? '-' . number_format($row->valor, 2, ',', '') : number_format($row->valor, 2, ',', ''); 
      $info = str_pad("$repre ($row->idREPRESENTANTE)", 35, ' ', 1) .'  ' .
                    str_pad($row->dataREGISTRO, 8, ' ', 1).'    ' .
                    str_pad($row->tipo, 1, ' ', 0) .'     ' .
                    substr(str_pad($row->descricao, 40, ' ', 1), 0, 40) .'  ' .
                    str_pad($valor, 7, ' ', 0) . '  '.
                    substr($contratantes, 0, 45);
      $info = str_pad($info, 150, ' ', 1);       
      fwrite($Arq, $info . " \n");
    }
    else {
      if ($row->pagoVALE_CREDITO==1) $pago=$vlrCOMDESCONTO;
      else {
        // vale credito nao pago, verifica os pagamentos ja efetuados dele
        $sql = "select sum(pag.valor) as vlrPAGO  ".
               "from pagamentos pag ".
               'left join caixa cx '.
               '    on cx.numreg=idCAIXA '.                 
               "where infoCHEQUE=$row->numVALE_CREDITO and tipoPGTO='VALE CR�DITO' and ifnull(cx.alterada2_excluida1,0)=0 ";

        $rsPAGOS = mysql_query($sql) or die (mysql_error());
        $pago=0;
        if (mysql_num_rows($rsPAGOS)>0) {
          $regPAGO = mysql_fetcH_object($rsPAGOS);
          $pago += $regPAGO->vlrPAGO;
        }
        mysql_free_result($rsPAGOS);
      } 
      if (  ($tipoREL=='4' && $pago==$vlrCOMDESCONTO) ||   ($tipoREL=='3' && $pago<$vlrCOMDESCONTO) || ($tipoREL=='2') ) {

        if ($row->tipo=='C') $totVALOR += $row->valor; else $totVALOR -= $row->valor; 
        if ($row->tipo=='C') $totDESCONTO += $row->valor - ($row->valor*($row->descontoVALE/100)); 
        else $totDESCONTO -= $row->valor-($row->valor*($row->descontoVALE/100));
    
        if ($row->tipo=='C') $totVALOR_G += $row->valor; else $totVALOR_G -= $row->valor; 
        if ($row->tipo=='C') $totDESCONTO_G += $row->valor-($row->valor*($row->descontoVALE/100)); 
        else $totDESCONTO_G -= $row->valor-($row->valor*($row->descontoVALE/100));

        fwrite($Arq, str_pad("$repre ($row->idREPRESENTANTE)", 35, ' ', 1) .'  ' .
                      str_pad($row->dataREGISTRO, 8, ' ', 1).'    ' .
                      str_pad($row->pagarVALE_MOSTRAR, 8, ' ', 0) .'  ' .
                      str_pad($row->tipo, 1, ' ', 0) .'     ' .
                      substr(str_pad($row->descricao, 40, ' ', 1), 0, 40) .'  ' .
                      str_pad(number_format($pago, 2, ',', ''), 7, ' ', 0) .'  ' .
                      str_pad(number_format($row->valor, 2, ',', ''), 7, ' ', 0) . '  '.
                      str_pad(number_format($vlrCOMDESCONTO, 2, ',', ''), 7, ' ', 0) .$desconto ."           \n");
      }  
    }

    // registra pagamento do vale credito caso requisitado pelo usuario
    if ($marcarCOMOPAGO=='true') {
      if ($row->pagoVALE_CREDITO=='0' ) {
        mysql_query("update creditos_descontos set pagoVALE_CREDITO=1, ".
                    " datapagoVALE_CREDITO=now(), oppagoVALE_CREDITO=$logado where numero=$row->numero ", $conexao) or die (mysql_error());
      }
    }

    $lin++;
  }
  if ($lin + 1 > 55)    cabecalho();
  if ($tipoREL=='1') {
    fwrite($Arq, '<negrito>'.str_pad(" TOTAL:      ", 97, ' ', 0) . str_pad(number_format($totVALOR, 2, ',', ''), 7, ' ', 0) . 
              "                                \n");
  } else {
    fwrite($Arq, '<negrito>'.str_pad(" TOTAL:      ", 116, ' ', 0) .
        str_pad(number_format($totVALOR, 2, ',', ''), 7, ' ', 0) . '  '.
        str_pad(number_format($totDESCONTO, 2, ',', ''), 7, ' ', 0) . "                       \n");
  }
  $lin++;

  if ($lin + 1 > 55)    cabecalho();
  if ($tipoREL=='1') {
    fwrite($Arq, '<negrito>'.str_pad(" TOTAL GERAL:      ", 97, ' ', 0) .str_pad(number_format($totVALOR_G, 2, ',', ''), 7, ' ', 0)."                           \n");
  } else {
    fwrite($Arq, '<negrito>'.str_pad(" TOTAL GERAL:      ", 116, ' ', 0) .
        str_pad(number_format($totVALOR_G, 2, ',', ''), 7, ' ', 0) . '  '.
        str_pad(number_format($totDESCONTO_G, 2, ',', ''), 7, ' ', 0) . "                     \n");
  }


  fclose($Arq);
}




/*****************************************************************************************/
if ($acao=='protocolo') {
  $marcar = $_REQUEST['marcarENVIADAS']=='true' ? 1 : 0;

  $resultado = mysql_query(' select nome from info_empresa', $conexao) or die (mysql_error());

  $row = mysql_fetch_object($resultado);
  $nomeEMPRESA=trim($row->nome);
    
  $sql  = "select prop.idOPERADORA, ope.nome as nomeOPERADORA, prop.numCONTRATO, prop.contratante, ".
          " prop.idTipoContrato as idPRODUTO, tip.descricao as nomePRODUTO, prop.sequencia, prop.vlrCONTRATO, ".
          " date_format(now(), '%d/%m/%y  %H:%i') as dataENVIO, qtdeVIDAS,repre.nome as nomeREPRE, env.reenvio, prop.dataEnvioOperadora ".
          "from ultimo_protocolo_envio env ".
          " inner join propostas prop ".
          "   on prop.sequencia=env.sequencia ".
          " left join operadoras ope ".
          "   on ope.numreg = prop.idOPERADORA ".
          " left join tipos_contrato tip ".
          "   on tip.numreg = prop.idTipoContrato ".
          " left join representantes repre ".
          "   on repre.numero = prop.idREPRESENTANTE ".
          " order by env.numreg  ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $contratos=0; $xxxxvidas=0;    $vlrTOT=0;
  $operacao='envio'; $pagina=0;
  while ($row = mysql_fetch_object($resultado)) {
    $contratos++;    
    $xxxxvidas += $row->qtdeVIDAS;
    $vlrTOT += $row->vlrCONTRATO;

    $data=$row->dataENVIO;
    $xxxxxxnomeOPERADORA=substr(str_pad($row->nomeOPERADORA, 20, ' ', 1), 0, 20) ;
    if ($row->reenvio=='S') $operacao='Reenvio';
  }
  $contratos= str_pad($contratos, 10, ' ', 0);
  $vlrTOT= str_pad(number_format($vlrTOT, 2, ',', '.'), 10, ' ', 0);
  $xxxxvidas= str_pad($xxxxvidas, 10, ' ', 0);

  $headers= 
  "Protocolo de envio de propostas (P�gina YY)                $nomeEMPRESA ||".         
  "Data do envio ....: $data |".
  "Valor total ......: R$ $vlrTOT   |".
  "Total de contratos: R$ $contratos                          --------------------   ------------------------------------------ |".
  "Total de vidas....: R$ $xxxxvidas                          $xxxxxxnomeOPERADORA   $nomeEMPRESA ||||".
  "N�  Operadora             Tipo contrato         N� da proposta  Corretor              Segurado                        Qtde vidas   Valor da fatura  Opera��o   |".
  "--------------------------------------------------------------------------------------------------------------------------------------------------------------";
// 99  xxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999         999,999,99       reenvio 

  mysql_data_seek($resultado, 0);
  $i=1;
  $lin=90;  
  while ($row = mysql_fetch_object($resultado)) {

    if ($lin + 1 > 40) cabecalho2();

    $operacao = $row->reenvio=='S' ? 'REENVIO' : 'ENVIO';
    fwrite($Arq, str_pad($i, 2, ' ', 0).'  ' .  
                 substr(str_pad($row->nomeOPERADORA, 20, ' ', 1), 0, 20) .'  ' .
                 substr(str_pad($row->nomePRODUTO, 20, ' ', 1), 0, 20) .'  ' .
                 str_pad($row->numCONTRATO, 14, ' ', 1) .'  ' .
                 substr(str_pad($row->nomeREPRE, 20, ' ', 1), 0, 20) .'  ' .
                 substr(str_pad($row->contratante, 30, ' ', 1), 0, 30) .'  ' .
                 str_pad($row->qtdeVIDAS, 4, ' ', 0) .'         ' .
                str_pad(number_format($row->vlrCONTRATO, 2, ',', '.'), 10, ' ', 0) . '       '.
                $operacao . "\n");

    if ($marcar==1) {
      // se ja houve uma data de envio inicial, nao muda, somente altera como data de REENVIO
      if ($row->dataEnvioOperadora=='') $cmpENVIO = " dataEnvioOperadora=now(), ";  else  $cmpENVIO = '';

      if ($row->reenvio=='S')
        $sql="update propostas set $cmpENVIO jaENVIADA=1, pendente=0, dataReenvioOperadora=now() where sequencia=$row->sequencia";        
      else
        $sql="update propostas set $cmpENVIO jaENVIADA=1, pendente=0   where sequencia=$row->sequencia";

      mysql_query($sql , $conexao) or die (mysql_error());
    }
    $i++;
    $lin++;  
  }

  fclose($Arq);
}



/*****************************************************************************************/
if ($acao=='novoEnvioProtocolo') {
  $reenvio = $_REQUEST['reenvio']=='true' ? 'S' : 'N';
  mysql_query("insert into ultimo_protocolo_envio(sequencia,reenvio) select $vlr,'$reenvio';") or die(mysql_error());  
}

/*****************************************************************************************/
IF ($acao=='novoProtocoloEnvio') {
  mysql_query("delete from ultimo_protocolo_envio;") or die(mysql_error());  
}

/*****************************************************************************************/
IF ($acao=='verPROP') {
  $op = $_REQUEST['op'];
  $sql  = "select pendente, cancelada, contratante, jaENVIADA, propostas.sequencia, ifnull(ult.sequencia, 'nao') as jaNaLista ".
          "from propostas  ".
          "left join ultimo_protocolo_envio ult ".
          "   on ult.sequencia=propostas.sequencia " .
          "where idOPERADORA=$op and numCONTRATO='$vlr' ";
          
  $resultado = mysql_query($sql, $conexao) or die (mysql_error() . '...'. $sql);
  
  $row = mysql_fetcH_object($resultado);

  if ( mysql_num_rows($resultado)==0 ) $resp='erro;Proposta n�o cadastrada';
  else {
    $resp=$row->sequencia;

    if ($row->jaNaLista!='nao') $resp='erro;Proposta j� adicionada � lista';
    else if ($row->cancelada=='S') $resp='erro;Proposta cancelada';
    else if ($row->pendente=='S') $resp='erro;Proposta com pend�ncia';
    else if ($row->jaENVIADA<>0) $resp="pergunta;$row->sequencia";
//    else if ($row->jaENVIADA<>0) $resp="erro;Proposta j� enviada";

  }      
}

/*****************************************************************************************/
IF ($acao=='verSEQUENCIA') {
  $sql  = "select pendente, cancelada, contratante, jaENVIADA, propostas.sequencia, ifnull(ult.sequencia, 'nao') as jaNaLista ".
          "from propostas  ".
          "left join ultimo_protocolo_envio ult ".
          "   on ult.sequencia=propostas.sequencia " .
          "where propostas.sequencia=$vlr ";
  
  $resultado = mysql_query($sql, $conexao) or die (mysql_error() . '...'. $sql);
  
  $row = mysql_fetcH_object($resultado);

  if ( mysql_num_rows($resultado)==0 ) $resp='erro;Proposta n�o cadastrada';
  else {
    $resp=$row->sequencia;

    if ($row->jaNaLista!='nao') $resp='erro;Proposta j� adicionada � lista';
    else if ($row->cancelada=='S') $resp='erro;Proposta cancelada';
    else if ($row->pendente=='S') $resp='erro;Proposta com pend�ncia';
    else if ($row->jaENVIADA<>0) $resp="erro;Proposta j� enviada";
//    else if ($row->jaENVIADA<>0) $resp="pergunta;$row->sequencia";
  }      
}



/*****************************************************************************************/
if ($acao=='lerUltimoProtocoloEnvio') {
  $sql  = "select prop.idOPERADORA, ope.nome as nomeOPERADORA, prop.numCONTRATO, prop.contratante, env.reenvio, ".
          " prop.idTipoContrato as idPRODUTO, tip.descricao as nomePRODUTO, prop.sequencia  ".
          "from ultimo_protocolo_envio env ".
          " inner join propostas prop ".
          "   on prop.sequencia=env.sequencia ".
          " inner join operadoras ope ".
          "   on ope.numreg = prop.idOPERADORA ".
          " inner join tipos_contrato tip ".
          "   on tip.numreg = prop.idTipoContrato ".
          " order by env.numreg ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='';
  while ($row = mysql_fetcH_object($resultado)) {
    if ($resp!='') $resp.='|';
    $reenvio = $row->reenvio=='S' ? ' (<font color=red size="1">REENVIO</font>)' : ''; 
    $resp .= "$row->nomeOPERADORA ($row->idOPERADORA);$row->nomePRODUTO ($row->idPRODUTO);$row->numCONTRATO    $reenvio;$row->contratante;".
              "$row->sequencia";
  }
  if ($resp=='') $resp='nada';
}

/*****************************************************************************************/
IF ($acao=='removeENVIO') {
  mysql_query("delete from ultimo_protocolo_envio where sequencia=$vlr") or die(mysql_error());  
}


/*****************************************************************************************/
if ($acao=='confirmacoes') {
  $idRELATORIO = $_REQUEST['idRELATORIO'];

  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];
  
  $dataIniMostrar_VALES = $_REQUEST['dataIniMostrar_VALES'];
  $dataFinMostrar_VALES = $_REQUEST['dataFinMostrar_VALES'];

  $dataINI = $_REQUEST['DATAINI'];
  $dataFIN = $_REQUEST['DATAFIN'];
  
  $dataINI_VALES = $_REQUEST['DATAINI_VALES'];
  $dataFIN_VALES = $_REQUEST['DATAFIN_VALES'];
  
  $dataREPASSE = $_REQUEST['repasse'];

  $idREPRE = $_REQUEST['repre'];
  $nomeOPERADORA=''; $idOPERADORA=''; 

  $operadora =  $_REQUEST['operadora'];  
  if ($operadora!='') {
    $operadora = explode(';', $_REQUEST['operadora']);
    $nomeOPERADORA = $operadora[1]; 
    $idOPERADORA = $operadora[0];
  } 

  $relGERAL = $_REQUEST['geral']=='true' ? 1 : 0;
  $marcarDATAPGTO = $_REQUEST['gravarDATA']=='true' ? 1 : 0;

  $repasseDENOVO = $_REQUEST['denovo']=='true' ? 1 : 0;

  $cmp=$_REQUEST['tipobusca']=='1' ? 'DataPgtoParcela' : 'dataGeracaoRel';
  $cmp2=$_REQUEST['tipobusca']=='1' ? 'DATA PGTO' : 'DATA REPASSE';
    
  $idREPRE = '';
  $idREPRE = $_REQUEST['repre'];

  $relCREDITOS = $_REQUEST['creditos']=='true' ? 1 : 0;  
  $adiantamento= $_REQUEST['adiantamento']=='true' ? 1 : 0;

  $infoADTO ='';
  if ($adiantamento) $infoADTO = '(ADIANTAMENTO)';

  $titREL = "Confirma��es $infoADTO no per�odo ($cmp2): $dataIniMostrar e $dataFinMostrar  " .
    ($relCREDITOS ? " - Cr�d/d�b per�odo $dataIniMostrar_VALES e $dataFinMostrar_VALES" : ''); 
  if ($nomeOPERADORA!='') $titulos = "Operadora: $nomeOPERADORA ($idOPERADORA)";

  $headers = 
      "                  Data de   Data do                                                           Base de    Parcela   Comiss�o|".          
      "Produto           cadastro  extrato   Corretor                   Segurado                     c�lculo      (%)       (R$)  |".
     str_repeat('-', 127);
//    xxxxxxxxxxxxxxxxx  99/99/99  99/99/99  xxxxxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxsxxxxxx  9999999,99  99 (999%)   9999,99       
//      xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx9999999,99hhhhhhhhhhhh9999999,99
  
    
  // le mensalidades 
  $sql = "select prop.qtdeMENS, date_format(fut.DataPgtoParcela, '%d/%m/%y') as DataPgtoParcela, fut.numREG as idFUTURA, prop.idTipoContrato as idPRODUTO, ifnull(prod.descricao, '* erro *') as nomePRODUTO, ".
         "prop.idREPRESENTANTE, ifnull(repre.nome, '* erro *') as nomeREPRE, prop.contratante, fut.dataGeracaoRel as dataREPASSE, ".
         "fut.valor, fut.valorPagoParcela, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, fut.ordem,  ".
         "  concat('Banco: ',ban.nome,' (',repre.idBANCO, ')    Ag�ncia: ', repre.agencia, ".
          " '    Opera��o:', repre.operacao, '    Conta: ', repre.num_conta, '    Favorecido: ', repre.favorecido) as dadosCONTA, ". 
         "date_format(fut.dataSituacaoParcela, '%d/%m/%y') as dataRECEBIDO, 1aMensIgualVigencia as priMensIgualVigencia, ".
          "  ifnull(fut.marcadaParaPagarAdiantamento,0)=1  as marcadaADIANTAMENTO, ifnull(prod.pagarPRIMEIRA, 'N') as pagarPRIMEIRA, ".
         "ifnull(comi1.p1a, 0) as p1a_1, ifnull(comi1.p2a, 0) as p2a_1, ifnull(comi1.p3a, 0) as p3a_1, ifnull(comi1.p4a, 0) as p4a_1, ". 
         " ifnull(comi1.p5a, 0) as p5a_1, ifnull(comi1.adesao, 0) as adesao_1, ifnull(comi1.pVITALICIA, 0) as pVITALICIA_1, ".
         "ifnull(comi2.p1a, 0) as p1a_2, ifnull(comi2.p2a, 0) as p2a_2, ifnull(comi2.p3a, 0) as p3a_2, ifnull(comi2.p4a, 0) as p4a_2, ". 
         " ifnull(comi2.p5a, 0) as p5a_2, ifnull(comi2.adesao, 0) as adesao_2, ifnull(comi2.pVITALICIA, 0) as pVITALICIA_2, ".
         "ifnull(comi3.p1a, 0) as p1a_3, ifnull(comi3.p2a, 0) as p2a_3, ifnull(comi3.p3a, 0) as p3a_3, ifnull(comi3.p4a, 0) as p4a_3, ". 
         " ifnull(comi3.p5a, 0) as p5a_3, ifnull(comi3.adesao, 0) as adesao_3, ifnull(comi3.pVITALICIA, 0) as pVITALICIA_3, ".
         "ifnull(comi4.p1a, 0) as p1a_4, ifnull(comi4.p2a, 0) as p2a_4, ifnull(comi4.p3a, 0) as p3a_4, ifnull(comi4.p4a, 0) as p4a_4, ". 
         " ifnull(comi4.p5a, 0) as p5a_4, ifnull(comi4.adesao, 0) as adesao_4, ifnull(comi4.pVITALICIA, 0) as pVITALICIA_4, ".
         "ifnull(comi5.p1a, 0) as p1a_5, ifnull(comi5.p2a, 0) as p2a_5, ifnull(comi5.p3a, 0) as p3a_5, ifnull(comi5.p4a, 0) as p4a_5, ". 
         " ifnull(comi5.p5a, 0) as p5a_5, ifnull(comi5.adesao, 0) as adesao_5, ifnull(comi5.pVITALICIA, 0) as pVITALICIA_5, ".
         "ifnull(comi6.p1a, 0) as p1a_6, ifnull(comi6.p2a, 0) as p2a_6, ifnull(comi6.p3a, 0) as p3a_6, ifnull(comi6.p4a, 0) as p4a_6, ". 
         " ifnull(comi6.p5a, 0) as p5a_6, ifnull(comi6.adesao, 0) as adesao_6, ifnull(comi6.pVITALICIA, 0) as pVITALICIA_6, ".
         "ifnull(comi7.p1a, 0) as p1a_7, ifnull(comi7.p2a, 0) as p2a_7, ifnull(comi7.p3a, 0) as p3a_7, ifnull(comi7.p4a, 0) as p4a_7, ". 
         " ifnull(comi7.p5a, 0) as p5a_7, ifnull(comi7.adesao, 0) as adesao_7, ifnull(comi7.pVITALICIA, 0) as pVITALICIA_7, ".
         "ifnull(comi8.p1a, 0) as p1a_8, ifnull(comi8.p2a, 0) as p2a_8, ifnull(comi8.p3a, 0) as p3a_8, ifnull(comi8.p4a, 0) as p4a_8, ". 
         " ifnull(comi8.p5a, 0) as p5a_8, ifnull(comi8.adesao, 0) as adesao_8, ifnull(comi8.pVITALICIA, 0) as pVITALICIA_8, ".
         "ifnull(comi9.p1a, 0) as p1a_9, ifnull(comi9.p2a, 0) as p2a_9, ifnull(comi9.p3a, 0) as p3a_9, ifnull(comi9.p4a, 0) as p4a_9, ". 
         " ifnull(comi9.p5a, 0) as p5a_9, ifnull(comi9.adesao, 0) as adesao_9, ifnull(comi9.pVITALICIA, 0) as pVITALICIA_9, ".
         "ifnull(comi10.p1a, 0) as p1a_10, ifnull(comi10.p2a, 0) as p2a_10, ifnull(comi10.p3a, 0) as p3a_10, ifnull(comi10.p4a, 0) as p4a_10, ". 
         " ifnull(comi10.p5a, 0) as p5a_10, ifnull(comi10.adesao, 0) as adesao_10, ifnull(comi10.pVITALICIA, 0) as pVITALICIA_10, ".
         " prod.vidas1, prod.vidas2, prod.vidas3, prod.vidas4, prod.vidas5, prod.vidas6, prod.vidas7, prod.vidas8, prod.vidas9, prop.cancelada, ".
         " prod.vidas10, prop.idComiRepresentante as comiNaProposta , prop.qtdeVIDAS, prod.idOPERADORA, opa.1aMensIgualVigencia as priMensIgualVigencia ".  
         "from futuras fut ".
         "left join propostas prop ".
         "  on prop.sequencia=fut.sequencia ".
         "left join tipos_contrato prod ".
         "  on prod.numreg=prop.idTipoContrato ".
         "left join operadoras opa ".
         "  on opa.numreg=prod.idOPERADORA ".
         "left join representantes repre ".
         "    on repre.numero=prop.idREPRESENTANTE ".
         "left join bancos ban ".
         "    on ban.numero=repre.idBANCO ".
          "left join comissoes_representante comi1 " .
          "    on  comi1.idCOMISSAO=repre.idTIPO_COMISSAO and comi1.idPRODUTO=prod.numreg and comi1.interno_externo=repre.interno_externo and comi1.comissionamentoPorVidas=1 ".
          "left join comissoes_representante comi2 " .
          "    on  comi2.idCOMISSAO=repre.idTIPO_COMISSAO and comi2.idPRODUTO=prod.numreg and comi2.interno_externo=repre.interno_externo and comi2.comissionamentoPorVidas=2  ".
          "left join comissoes_representante comi3 " .
          "    on  comi3.idCOMISSAO=repre.idTIPO_COMISSAO and comi3.idPRODUTO=prod.numreg and comi3.interno_externo=repre.interno_externo and comi3.comissionamentoPorVidas=3  ".
          "left join comissoes_representante comi4 " .
          "    on  comi4.idCOMISSAO=repre.idTIPO_COMISSAO and comi4.idPRODUTO=prod.numreg and comi4.interno_externo=repre.interno_externo and comi4.comissionamentoPorVidas=4  ".
          "left join comissoes_representante comi5 " .
          "    on  comi5.idCOMISSAO=repre.idTIPO_COMISSAO and comi5.idPRODUTO=prod.numreg and comi5.interno_externo=repre.interno_externo and comi5.comissionamentoPorVidas=5  ".
          "left join comissoes_representante comi6 " .
          "    on  comi6.idCOMISSAO=prop.idComiRepresentante and comi6.idPRODUTO=prod.numreg and comi6.interno_externo=repre.interno_externo and comi6.comissionamentoPorVidas=1 ".
          "left join comissoes_representante comi7 " .
          "    on  comi7.idCOMISSAO=prop.idComiRepresentante and comi7.idPRODUTO=prod.numreg and comi7.interno_externo=repre.interno_externo and comi7.comissionamentoPorVidas=2 ".
          "left join comissoes_representante comi8 " .
          "    on  comi8.idCOMISSAO=prop.idComiRepresentante and comi8.idPRODUTO=prod.numreg and comi8.interno_externo=repre.interno_externo and comi8.comissionamentoPorVidas=3 ".
          "left join comissoes_representante comi9 " .
          "    on  comi9.idCOMISSAO=prop.idComiRepresentante and comi9.idPRODUTO=prod.numreg and comi9.interno_externo=repre.interno_externo and comi9.comissionamentoPorVidas=4 ".
          "left join comissoes_representante comi10 " .
          "    on  comi10.idCOMISSAO=prop.idComiRepresentante and comi10.idPRODUTO=prod.numreg and comi10.interno_externo=repre.interno_externo and comi10.comissionamentoPorVidas=5 ".
          "where @criterioREPASSE and ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1  @criterioPAGAS_OU_NAO @criterioREPRE @criterioOPERADORA ".
          "  @criterioADIANTAMENTO ".
          "order by prop.idREPRESENTANTE, prop.contratante ";
          
          //          " and dataGeracaoRel is null @criterioADIANTAMENTO ".
          //"order by prop.idREPRESENTANTE, prop.contratante ";
         
  if ( ! $repasseDENOVO )
    $sql = str_replace('@criterioREPASSE', ' dataGeracaoRel is null  ', $sql);
  else
    $sql = str_replace('@criterioREPASSE', ' 1=1 ', $sql);
  
 
  if (! $adiantamento) {
    $sql = str_replace('@criterioPAGAS_OU_NAO', 
              " and ifnull(fut.situacaoParcela,0)=1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN'" , $sql);

    $sql = str_replace('@criterioADIANTAMENTO', ' and ifnull(fut.marcadaParaPagarAdiantamento,0)=0 ', $sql);
  }
  else {
    $sql = str_replace('@criterioPAGAS_OU_NAO',  " and ifnull(fut.situacaoParcela,0)<>1 ", $sql);
    $sql = str_replace('@criterioADIANTAMENTO', " and fut.ordem=1 and ifnull(fut.marcadaParaPagarAdiantamento,0)=1 ", $sql);
  } 
    

  if ($idREPRE=='9999')
    $sql = str_replace('@criterioREPRE', '', $sql); 
  else
    $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);

  if ($idOPERADORA=='')
    $sql = str_replace('@criterioOPERADORA', '', $sql); 
  else
    $sql = str_replace('@criterioOPERADORA', " and prod.idoperadora=$idOPERADORA ", $sql);


//die($sql);
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;

  $lin = 87;
  $repreATUAL = 'none';

  $forcarTOTALIZAR=false;
  $totG_COMISSAO=0;
  $totG_COMISSAO_2=0;

  $qtdeCorretoresPagar = 0;
  while ($row = mysql_fetch_object($resultado)) {  

    if ($row->idREPRESENTANTE!=$repreATUAL) {
      
      $txtCREDITOS='nada';
      if ($relCREDITOS && $repreATUAL!='none') {  
          $infoCREDITOS = explode('^', 
            creditos($repreATUAL, $dataINI_VALES, $dataFIN_VALES, $dataIniMostrar_VALES, $dataFinMostrar_VALES, $contaENTREGA, $totCOMISSAO_2));
          $txtCREDITOS = $infoCREDITOS[0]; 
          $vlrCREDITOS = floatval($infoCREDITOS[1]); 
      }
      if ( ($repreATUAL!='none' && ($txtCREDITOS!='nada' || $totCOMISSAO_2>0)) || $relGERAL || $forcarTOTALIZAR) $temALGO=true;
      else $temALGO=false;

      if ($temALGO) {
        if ($lin + 2 > 55  ) cabecalho();
  
  //      if ($repreATUAL!='none' && $totCOMISSAO>0) {
        if ($repreATUAL!='none' ) {
            fwrite($Arq, '<negrito>'.str_pad(" TOTAL:    ", 92, ' ', 0) .
                str_pad(number_format($totCOMISSAO, 2, ',', ''), 10, ' ', 0) . '            '. 
                str_pad(number_format($totCOMISSAO_2, 2, ',', ''), 10, ' ', 0) . "\n");
          $lin++;
  
          if ($relCREDITOS && $txtCREDITOS!='nada' ) fwrite($Arq, $txtCREDITOS);

          $jaPAGO=0;
          if ($lin + 4 > 55  ) cabecalho();
            fwrite($Arq, "\n"); 
//          if ($vlrCREDITOS<0)      
            fwrite($Arq, '<negrito>'.str_pad("TOTAL A RECEBER:      ", 20, ' ', 0) . 
                    str_pad(number_format($totCOMISSAO_2 + $vlrCREDITOS, 2, ',', ''), 10, ' ', 0) .    "   \n") ;

            // se algum valor a ser pago para o corretor atual, soma 1 ao total de corretores pagar,
            // e se nao existe ainda, cria um registro de acompanhamento de pagamento,
            // para saber se o pagamento deste corretor foi feito
//            if ($totCOMISSAO_2 + $vlrCREDITOS<>0 && $idREPRE=='9999')     {
            if ($idREPRE=='9999' )     {
              // idRELATORIO='', pessoa escolheu periodo manualmente (nao foi um relatorio especifico, cujo perido previamente estipulado)
              if (trim($idRELATORIO)!='') {        
                $sql = 'select numreg, ifnull(pago, 0) as jaPAGO '. 
                       'from pagamentos_corretor '.
                       "where idRELATORIO = '$idRELATORIO' and idREPRESENTANTE=$repreATUAL ";
  
                $rsPGTOS = mysql_query($sql, $conexao) or die ($sql.'<br><br>'.mysql_error()); 
                $pagar = $totCOMISSAO_2 + $vlrCREDITOS;
                $pagar = $pagar<0 ? 0 : $pagar;
  
                $qtdeCorretoresPagar = ($pagar<=0) ? $qtdeCorretoresPagar : $qtdeCorretoresPagar+1;
                if (mysql_num_rows($rsPGTOS)==0) {
                  mysql_query("insert into pagamentos_corretor(idRELATORIO,idREPRESENTANTE, valor) select $idRELATORIO, $repreATUAL, $pagar", $conexao) or 
                            die (mysql_error());
                } 
                else {
                  mysql_query("update pagamentos_corretor set valor=$pagar where idRELATORIO=$idRELATORIO and idREPRESENTANTE=$repreATUAL", $conexao) or 
                            die (mysql_error());
                  $regPGTO = mysql_fetch_object($rsPGTOS);
                  $jaPAGO=$regPGTO->jaPAGO;
                }
              }  
            }
            else {
              $sql = 'select numreg, ifnull(pago, 0) as jaPAGO '. 
                     'from pagamentos_corretor '.
                     "where idRELATORIO = $idRELATORIO and idREPRESENTANTE=$idREPRE ";
          
              $rsPGTOS = mysql_query($sql, $conexao) or die (mysql_error()); 
              if (mysql_num_rows($rsPGTOS)>=0) {
                $regPGTO = mysql_fetch_object($rsPGTOS);
                $jaPAGO=$regPGTO->jaPAGO;
              }
            }
            mysql_free_result($rsPGTOS);
          
            $jaPAGO = $jaPAGO==1 ? 'SIM' : 'N�O';
            fwrite($Arq, "<negrito>Dados da conta:        \n") ;
            fwrite($Arq, '<negrito>'.$dadosCONTA."   \n") ;
            fwrite($Arq, "<negrito>Depositado ...: $jaPAGO \n") ;


//          else
//            fwrite($Arq, '<negrito>'.str_pad(" TOTAL A RECEBER:      ", 20, ' ', 0) . 
  //                  str_pad(number_format($totCOMISSAO_2 - $vlrCREDITOS, 2, ',', ''), 10, ' ', 0) .    "   \n") ;

            $lin++; $lin++;
  
          // se nao for relat geral, pula pagina entre representantes
          if (! $relGERAL)   cabecalho();
        }
      } 
      $totCOMISSAO=0;       $totCOMISSAO_2=0;
      $repreATUAL = $row->idREPRESENTANTE;
      $forcarTOTALIZAR=false;

//      if (! $temALGO) continue;
    }  
    if ($lin + 1 > 55)    cabecalho();

    // se especificado (NO CONTRATO) qual comissao usar  
    $perc=0;
    if ($row->comiNaProposta!='' && $row->comiNaProposta!='0') {
      if ($row->qtdeVIDAS >= $row->vidas1 && $row->qtdeVIDAS <= $row->vidas2) {
        if ($row->ordem=='1') $perc=$row->p1a_6; if ($row->ordem=='2') $perc=$row->p2a_6;  if ($row->ordem=='3') $perc=$row->p3a_6;
        if ($row->ordem=='4') $perc=$row->p4a_6; if ($row->ordem=='5') $perc=$row->p5a_6;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_6;
      }
      if ($row->qtdeVIDAS >= $row->vidas3 && $row->qtdeVIDAS <= $row->vidas4) {
        if ($row->ordem=='1') $perc=$row->p1a_7; if ($row->ordem=='2') $perc=$row->p2a_7;  if ($row->ordem=='3') $perc=$row->p3a_7;
        if ($row->ordem=='4') $perc=$row->p4a_7; if ($row->ordem=='5') $perc=$row->p5a_7;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_7;
      }
      if ($row->qtdeVIDAS >= $row->vidas5 && $row->qtdeVIDAS <= $row->vidas6) {
        if ($row->ordem=='1') $perc=$row->p1a_8; if ($row->ordem=='2') $perc=$row->p2a_8;  if ($row->ordem=='3') $perc=$row->p3a_8;
        if ($row->ordem=='4') $perc=$row->p4a_8; if ($row->ordem=='5') $perc=$row->p5a_8;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_8;
      }
      if ($row->qtdeVIDAS >= $row->vidas7 && $row->qtdeVIDAS <= $row->vidas8) {
        if ($row->ordem=='1') $perc=$row->p1a_9; if ($row->ordem=='2') $perc=$row->p2a_9;  if ($row->ordem=='3') $perc=$row->p3a_9;
        if ($row->ordem=='4') $perc=$row->p4a_9; if ($row->ordem=='5') $perc=$row->p5a_9;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_9;
      }
      if ($row->qtdeVIDAS >= $row->vidas9 && $row->qtdeVIDAS <= $row->vidas10) {
        if ($row->ordem=='1') $perc=$row->p1a_10; if ($row->ordem=='2') $perc=$row->p2a_10;  if ($row->ordem=='3') $perc=$row->p3a_10;
        if ($row->ordem=='4') $perc=$row->p4a_10; if ($row->ordem=='5') $perc=$row->p5a_10;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_10;
      }          
    }
    // se nao especificado (NO CONTRATO) qual comissao de representante usar, usa a comissao especificada no reg do representante
    else {
      if ($row->qtdeVIDAS >= $row->vidas1 && $row->qtdeVIDAS <= $row->vidas2) {
        if ($row->ordem=='1') $perc=$row->p1a_1; if ($row->ordem=='2') $perc=$row->p2a_1;  if ($row->ordem=='3') $perc=$row->p3a_1;
        if ($row->ordem=='4') $perc=$row->p4a_1; if ($row->ordem=='5') $perc=$row->p5a_1;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_1;
      }
      if ($row->qtdeVIDAS >= $row->vidas3 && $row->qtdeVIDAS <= $row->vidas4) {
        if ($row->ordem=='1') $perc=$row->p1a_2; if ($row->ordem=='2') $perc=$row->p2a_2;  if ($row->ordem=='3') $perc=$row->p3a_2;
        if ($row->ordem=='4') $perc=$row->p4a_2; if ($row->ordem=='5') $perc=$row->p5a_2;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_2;
      }
      if ($row->qtdeVIDAS >= $row->vidas5 && $row->qtdeVIDAS <= $row->vidas6) {
        if ($row->ordem=='1') $perc=$row->p1a_3; if ($row->ordem=='2') $perc=$row->p2a_3;  if ($row->ordem=='3') $perc=$row->p3a_3;
        if ($row->ordem=='4') $perc=$row->p4a_3; if ($row->ordem=='5') $perc=$row->p5a_3;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_3;
      }
      if ($row->qtdeVIDAS >= $row->vidas7 && $row->qtdeVIDAS <= $row->vidas8) {
        if ($row->ordem=='1') $perc=$row->p1a_4; if ($row->ordem=='2') $perc=$row->p2a_4;  if ($row->ordem=='3') $perc=$row->p3a_4;
        if ($row->ordem=='4') $perc=$row->p4a_4; if ($row->ordem=='5') $perc=$row->p5a_4;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_4;
      }
      if ($row->qtdeVIDAS >= $row->vidas9 && $row->qtdeVIDAS <= $row->vidas10) {
        if ($row->ordem=='1') $perc=$row->p1a_5; if ($row->ordem=='2') $perc=$row->p2a_5;  if ($row->ordem=='3') $perc=$row->p3a_5;
        if ($row->ordem=='4') $perc=$row->p4a_5; if ($row->ordem=='5') $perc=$row->p5a_5;
        if ($row->ordem>5 || $row->qtdeMENS=='3' && $perc==0) $perc=$row->pVITALICIA_5;
      }          
    }
    $listar=true;
    // se nao for relat geral, e percentual =0, nao mostra
    if (! $relGERAL && $perc==0 )   $listar=false;

    // mensalidades de contratos cuja operadora tem data 1a mens = data assinatura (vigencia), nao paga comissao sobre a 1a
    // uma vez que a 1a mensalidade � paga/rateada no ato da entrega da proposta 
    if ($row->priMensIgualVigencia=='S' && $row->ordem==1 && $row->pagarPRIMEIRA!='S')  {
      // se operadora do contrato entra na regra d repassar 1a caso qtde vidas >= 100, lista
      if ($row->qtdeVIDAS>=100) 
        $listar=true;
      else
        $listar=false;
    }

    if ($row->priMensIgualVigencia=='O') $listar=true;
    if ($row->cancelada=='S') $listar=false;

    if (! $relGERAL && $perc==0 )   $listar=false;

    if ($listar) {
      $forcarTOTALIZAR=true;

      // se for adto, desconta 10%
      $baseCALCULO = $row->valor;
      $baseCALCULO = $row->valorPagoParcela;

      if ($row->marcadaADIANTAMENTO==1) $perc -= 10; 
      
      $comissao = $row->valorPagoParcela * ($perc/100); 

      fwrite($Arq, substr(str_pad($row->nomePRODUTO, 17, ' ', 1), 0, 17) .' ' .  
                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .
                    str_pad($row->DataPgtoParcela, 8, ' ', 1) .'  ' .
                    substr(str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 25, ' ', 1), 0, 25) .'  ' .
                    substr(str_pad($row->contratante, 25, ' ', 1), 0, 25) .'  ' .
                    str_pad(number_format($baseCALCULO, 2, ',', ''), 10, ' ', 0) .'   ' .
                    str_pad($row->ordem, 2, ' ', 0) .' (' .
                    str_pad($perc, 3, ' ', 0) .'%)   ' .
                    str_pad(number_format($comissao, 2, ',', ''), 7, ' ', 0) ."\n");

      $dadosCONTA = $row->dadosCONTA;

      $totCOMISSAO += $row->valorPagoParcela;
      $totG_COMISSAO += $row->valorPagoParcela;

      $totCOMISSAO_2 += $row->valorPagoParcela * ($perc/100);
      $totG_COMISSAO_2 += $row->valorPagoParcela * ($perc/100);

      if ($marcarDATAPGTO && $row->dataREPASSE=='') {
        if ($adiantamento)
          mysql_query("update futuras set dataGeracaoRel='$dataREPASSE', pagaNoAdiantamento=1 where numreg=$row->idFUTURA");
        else 
          mysql_query("update futuras set dataGeracaoRel='$dataREPASSE' where numreg=$row->idFUTURA");
      }

  
      $lin++;
    }
  }
  if ($lin + 1 > 55)    cabecalho();
  fwrite($Arq, '<negrito>'.str_pad(" TOTAL:    ", 92, ' ', 0) .
      str_pad(number_format($totCOMISSAO, 2, ',', ''), 10, ' ', 0) . '            '. 
      str_pad(number_format($totCOMISSAO_2, 2, ',', ''), 10, ' ', 0) . "\n");

  $lin++;;

  $txtCREDITOS='nada';
  if ($relCREDITOS) {
        $infoCREDITOS = explode('^', 
          creditos($repreATUAL, $dataINI_VALES, $dataFIN_VALES, $dataIniMostrar_VALES, $dataFinMostrar_VALES, $contaENTREGA, $totCOMISSAO_2));
        $txtCREDITOS = $infoCREDITOS[0]; 
        $vlrCREDITOS = floatval($infoCREDITOS[1]); 
  } 
  if ($txtCREDITOS!='nada') fwrite($Arq, $txtCREDITOS);  
  if ($lin + 4 > 55  ) cabecalho();

  // se algum valor a ser pago para o corretor atual, soma 1 ao total de corretores pagar,
  // e se nao existe ainda, cria um registro de acompanhamento de pagamento,
  // para saber se o pagamento deste corretor foi feito

  $jaPAGO=0;
  if ($idREPRE=='9999')     {
    $sql = 'select numreg, ifnull(pago, 0) as jaPAGO '. 
           'from pagamentos_corretor '.
           "where idRELATORIO = $idRELATORIO and idREPRESENTANTE=$repreATUAL ";

    $rsPGTOS = mysql_query($sql, $conexao) or die (mysql_error()); 
    $pagar = $totCOMISSAO_2 + $vlrCREDITOS;
    $pagar = $pagar<0 ? 0 : $pagar;

    $qtdeCorretoresPagar = ($pagar<=0) ? $qtdeCorretoresPagar : $qtdeCorretoresPagar+1; 
    if (mysql_num_rows($rsPGTOS)==0) {
      mysql_query("insert into pagamentos_corretor(idRELATORIO,idREPRESENTANTE, valor) select $idRELATORIO, $repreATUAL, $pagar", $conexao) or 
                die (mysql_error());
    } 
    else {
      mysql_query("update pagamentos_corretor set valor=$pagar where idRELATORIO=$idRELATORIO and idREPRESENTANTE=$repreATUAL", $conexao) or 
                die (mysql_error());
      $regPGTO = mysql_fetch_object($rsPGTOS);
      $jaPAGO=$regPGTO->jaPAGO;
    }
  } 
  else {
    $sql = 'select numreg, ifnull(pago, 0) as jaPAGO '. 
           'from pagamentos_corretor '.
           "where idRELATORIO = $idRELATORIO and idREPRESENTANTE=$idREPRE ";

    $rsPGTOS = mysql_query($sql, $conexao) or die (mysql_error()); 
    if (mysql_num_rows($rsPGTOS)>=0) {
      $regPGTO = mysql_fetch_object($rsPGTOS);
      $jaPAGO=$regPGTO->jaPAGO;
    }
  }
  mysql_free_result($rsPGTOS);

  fwrite($Arq, "\n"); 
  fwrite($Arq, '<negrito>'.str_pad("TOTAL A RECEBER:      ", 20, ' ', 0) . 
          str_pad(number_format($totCOMISSAO_2 + $vlrCREDITOS, 2, ',', ''), 10, ' ', 0) .    "   \n") ;

  $jaPAGO = $jaPAGO==1 ? 'SIM' : 'N+O';
  fwrite($Arq, "<negrito>Dados da conta:        \n") ;
  fwrite($Arq, '<negrito>'.$dadosCONTA."   \n") ;
  fwrite($Arq, "<negrito>Depositado ...: $jaPAGO  \n") ;

  // registra qtos corretores tem valores a receber neste relatorio
  if ($idREPRE=='9999') {
    mysql_query("update periodos_pgto set qtdeCorretoresPagar=$qtdeCorretoresPagar where numreg=$idRELATORIO", $conexao) or 
              die (mysql_error());
  }



  if ($lin + 2 > 55)    cabecalho();
  fwrite($Arq, "\n");
  fwrite($Arq, '<negrito>'.str_pad(" GERAL CORRETORES: ", 92, ' ', 0) .
              str_pad(number_format($totG_COMISSAO, 2, ',', ''), 10, ' ', 0) . '            '. 
              str_pad(number_format($totG_COMISSAO_2, 2, ',', ''), 10, ' ', 0) . "\n");

  fclose($Arq);
}







/*****************************************************************************************/
if ($acao=='vendas') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];
    
  $dataINI = $_REQUEST['DATAINI'];
  $dataFIN = $_REQUEST['DATAFIN'];

  $tipoREL = $_REQUEST['tipoREL'];
  $idREPRE = $_REQUEST['repre'];
    
  $idREPRE = '';
  $idREPRE = $_REQUEST['repre'];
  $tipoBUSCA = $_REQUEST['tipobusca'];

  $idGRUPO = $_REQUEST['gr'];
  $nomeGRUPO = $_REQUEST['nomeGR'];

  $idOPERADORA = $_REQUEST['ope'];
  $nomeOPERADORA = $_REQUEST['nomeOPE'];

  $idPRODUTO = $_REQUEST['prod'];
  $nomePRODUTO = $_REQUEST['nomePROD'];



  if ($tipoBUSCA=='1') {$data=' DATA DE CADASTRO';$cmp='dataCADASTRO';}
  if ($tipoBUSCA=='2') {$data=' DATA DE VIG-NCIA';$cmp='dataASSINATURA';}
  if ($tipoBUSCA=='3') {$data=' DATA DE ENVIO';$cmp='dataEnvioOperadora';}

  $infoADD=''; 
  if ($idGRUPO!='') $infoADD = "  - GRUPO DE VENDAS: $nomeGRUPO "; 
  if ($idOPERADORA!='') $infoADD = "  - OPERADORA: $nomeOPERADORA ";
  if ($idPRODUTO!='') $infoADD = "  - TIPO DE CONTRATO: $nomePRODUTO ";

  //************************************************************************************************
  //************************************************************************************************
  // tipo de relatorio SOMATORIA POR CORRETOR
  //************************************************************************************************
  //************************************************************************************************
  if ($tipoREL=='1') {
    $titREL = "Vendas no per�odo: ($data) $dataIniMostrar e $dataFinMostrar $infoADD ";

    $headers = 
        "                                    Data de                                           Valor do   Valor     Valor      Valor    Data de   Qtde   Num| ".          
        "Operadora        Produto            cadastro  Corretor               Segurado         contrato  recebido  produ��o   plant�o  envio    Vidas  prop|".
       str_repeat('-', 155);
  //    xxxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxx  99/99/99  xxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxxx  99999,99  99999,99  99999,99  99999,99  99/99/99  9999     

  
                     
    // le propostas
    $sql = "select ifnull(prop.qtdeVIDAS,0) as qtdeVIDAS, ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, ifnull(contra.descricao, '* ERRO *') as tipoCONTRATO, " .
           "   substring(ifnull(repre.nome, '* ERRO *'),1,15) as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .
           "   prop.contratante, prop.vlrCONTRATO, prop.vlrRECEBIDO, prop.vlrPRODUCAO, prop.idREPRESENTANTE, prop.vlrPLANTAO, " .
           "   date_format(prop.dataEnvioOperadora, '%d/%m/%y') as dataEnvioOperadora, prop.numCONTRATO " .
           "from propostas prop " .
           "left join operadoras opa " .
           "   on opa.numreg = prop.idOPERADORA " .
           "left join tipos_contrato contra " .
           "   on contra.numreg = prop.idTipoContrato " .
           "left join representantes repre " .
           "   on repre.numero = prop.idREPRESENTANTE " .
           " where ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and ifnull(prop.cancelada, 'N')<>'S' ".
           " order by repre.nome, contratante";      // prop.idOPERADORA, prop.idTipoContrato 

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);

    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);    

//die($sql);
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
    if (mysql_num_rows($resultado)==0) die('nada'); 

    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');

    $pagina = 0;
  
    $lin = 87;
    $repreATUAL = 'none';
  
    $totG_CONTRATO=0;     $totG_PRODUCAO=0;      $totG_RECEBIDO=0;      $totG_PLANTAO=0;      $qtdeG_CONTRATO=0; $totG_VIDAS=0;

    while ($row = mysql_fetch_object($resultado)) {  

      if ($row->idREPRESENTANTE!=$repreATUAL) {     
        if ($lin + 2 > 55) cabecalho();

        if ($repreATUAL!='none') {
          $qtdeCONTRATO = str_pad($qtdeCONTRATO, 5, ' ', 0);
            fwrite($Arq, '<negrito>'.str_pad("    TOTAL:     $qtdeCONTRATO   ", 85, ' ', 0) .
                      str_pad(number_format($totCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .
                      str_pad(number_format($totRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .
                      str_pad(number_format($totPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .
                      str_pad(number_format($totPLANTAO, 2, ',', ''), 8, ' ', 0) .'          '.
                      str_pad($totVIDAS, 4, ' ', 0) ."                                     \n");


          $lin++; 
        }

        $totCONTRATO=0;     $totPRODUCAO=0;      $totRECEBIDO=0;      $totPLANTAO=0;      $qtdeCONTRATO=0; $totVIDAS=0;
        $repreATUAL = $row->idREPRESENTANTE;
      }  
        
      if ($lin + 1 > 55)    cabecalho();

      $totCONTRATO += $row->vlrCONTRATO;
      $totPRODUCAO += $row->vlrPRODUCAO;
      $totPLANTAO += $row->vlrPLANTAO;
      $totRECEBIDO += $row->vlrRECEBIDO;
      $totVIDAS += $row->qtdeVIDAS;
      $qtdeCONTRATO++;

      $totG_CONTRATO += $row->vlrCONTRATO;
      $totG_PRODUCAO += $row->vlrPRODUCAO;
      $totG_PLANTAO += $row->vlrPLANTAO;
      $totG_RECEBIDO += $row->vlrRECEBIDO;
      $totG_VIDAS += $row->qtdeVIDAS;      
      $qtdeG_CONTRATO++;

    
      fwrite($Arq, substr(str_pad($row->nomeOPERADORA, 15, ' ', 1), 0, 15) .'  ' .
                    substr(str_pad($row->tipoCONTRATO, 17, ' ', 1), 0, 17) .'  ' .  
                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .
                    str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 21, ' ', 1) .'  ' .
                    substr(str_pad($row->contratante, 15, ' ', 1), 0, 15) .'  ' .
                    str_pad(number_format($row->vlrCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrPLANTAO, 2, ',', ''), 8, ' ', 0) .'  ' .
                    str_pad($row->dataEnvioOperadora, 8, ' ', 1) . '  '.
                    str_pad($row->qtdeVIDAS, 4, ' ', 1) . '  '.
                    $row->numCONTRATO ."\n");                    

      $lin++;
    }
    if ($lin + 2 > 55)    cabecalho();
  
    $qtdeCONTRATO = str_pad($qtdeCONTRATO, 5, ' ', 0);
    fwrite($Arq, '<negrito>'.str_pad(" TOTAL:     $qtdeCONTRATO   ", 101, ' ', 0) .
                str_pad(number_format($totCONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totRECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totPRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totPLANTAO, 2, ',', ''), 8, ' ', 0) .'          '.
                str_pad($totVIDAS, 4, ' ', 0) ."                                     \n");

    if ($lin + 2 > 55)    cabecalho();
  
    $qtdeCONTRATO = str_pad($qtdeG_CONTRATO, 5, ' ', 0);
    fwrite($Arq, '<negrito>'.str_pad(" GERAL:     $qtdeCONTRATO   ", 101, ' ', 0) .
                str_pad(number_format($totG_CONTRATO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totG_RECEBIDO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totG_PRODUCAO, 2, ',', ''), 8, ' ', 0) .'  ' .
                str_pad(number_format($totG_PLANTAO, 2, ',', ''), 8, ' ', 0) .'          '.
                str_pad($totG_VIDAS, 4, ' ', 0) ."                                     \n");


    fclose($Arq);
  }



  //************************************************************************************************
  //************************************************************************************************
  // tipo de relatorio CANCELADOS
  //************************************************************************************************
  //************************************************************************************************
  if ($tipoREL=='2') {
    $titREL = "Propostas canceladas no per�odo: ($data) $dataIniMostrar e $dataFinMostrar $infoADD ";

    $headers = 
        "                                    Data de                                                               Valor do  Qtde |".          
        "Operadora        Produto            cadastro  Corretor                    Segurado                        contrato  vidas|".
       str_repeat('-', 120);
  //     xxxxxxxxxxxxxx  xxxxxxxxxxxxxxxxx  99/99/99  xxxxxxxxxxxxxxxxxxxx (9999)  xxxxxxxxxxxxxxxxxxxx            99999,99        

    // le mensalidades
    $sql = "select ifnull(prop.qtdeVIDAS,0) as qtdeVIDAS, ifnull(opa.nome, '* ERRO *') as nomeOPERADORA, ifnull(contra.descricao, '* ERRO *') as tipoCONTRATO, " .
           "   ifnull(repre.nome, '* ERRO *') as nomeREPRE, date_format(prop.dataCADASTRO, '%d/%m/%y') as dataPROPOSTA, " .
           "   prop.contratante, prop.vlrCONTRATO, prop.vlrRECEBIDO, prop.vlrPRODUCAO, prop.idREPRESENTANTE, prop.vlrPLANTAO, " .
           "   date_format(prop.dataEnvioOperadora, '%d/%m/%y') as dataEnvioOperadora " .
           "from propostas prop " .
           "left join operadoras opa " .
           "   on opa.numreg = prop.idOPERADORA " .
           "left join tipos_contrato contra " .
           "   on contra.numreg = prop.idTipoContrato " .
           "left join representantes repre " .
           "   on repre.numero = prop.idREPRESENTANTE " .
           " where date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and prop.cancelada='S' ".
           " order by repre.nome, contratante";      // prop.idOPERADORA, prop.idTipoContrato 

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);

    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);        

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
    if (mysql_num_rows($resultado)==0) die('nada'); 

    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');

    $pagina = 0;
  
    $lin = 87;
    $totVIDAS=0;
    while ($row = mysql_fetch_object($resultado)) {  
      if ($lin + 1 > 55)    cabecalho();

      fwrite($Arq, substr(str_pad($row->nomeOPERADORA, 15, ' ', 1), 0, 15) .'  ' .
                    substr(str_pad($row->tipoCONTRATO, 17, ' ', 1), 0, 17) .'  ' .  
                    str_pad($row->dataPROPOSTA, 8, ' ', 1) .'  ' .
                    str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 26, ' ', 1) .'  ' .
                    substr(str_pad($row->contratante, 30, ' ', 1), 0, 30) .'  ' .
                    str_pad(number_format($row->vlrCONTRATO, 2, ',', ''), 8, ' ', 0) . '  '.
                    str_pad($row->qtdeVIDAS, 4, ' ', 1) ."\n");
      $totVIDAS+=$row->qtdeVIDAS;                    
      $lin++;
    }

    if ($lin + 1 > 55)    cabecalho();

    fwrite($Arq, str_pad('TOTAL:          ', 116, ' ', 0) .
                  str_pad($totVIDAS, 4, ' ', 1) ."\n");
    
    fclose($Arq);
  }


  //************************************************************************************************
  //************************************************************************************************
  // tipo de relatorio CLASSIFICACAO
  //************************************************************************************************
  //************************************************************************************************
  if ($tipoREL=='3') {
    $titREL = "Vendas no per�odo: ($data) $dataIniMostrar e $dataFinMostrar $infoADD ";

    $headers = 
        "                                                              Valor       Valor       Valor        Valor|".          
        "Coloca��o  Corretor                             Contratos    produ��o    contrato    recebido     plant�o|".
       str_repeat('-', 110);
  //     99          xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx (999)   99999    999.999,99  999.999,99  999.999,99  999.999,99                  

  
    
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
                      
    // le totais vendas por grupo de venda 
    $sql = "select count(*) as qtdeCONTRATOS, sum(prop.vlrPRODUCAO) as vlrPRODUCAO, sum(prop.vlrCONTRATO) as vlrCONTRATO, ".
           "  sum(prop.vlrRECEBIDO) as vlrRECEBIDO, sum(prop.vlrPLANTAO) as vlrPLANTAO, ".
           "     repre.idGRUPO, ifnull(grupo.nome, '* GRUPO N+O DEFINIDO *') as nomeGRUPO ".
           "from propostas prop ".
           "left join representantes repre ".
           "  on repre.numero = prop.idREPRESENTANTE ".
           "left join grupos_venda grupo ".
           "  on grupo.numreg = repre.idGRUPO ".
           "where ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and ifnull(prop.cancelada, 'N')<>'S' ".
           " group by repre.idGRUPO, grupo.nome  ".
           " order by sum(prop.vlrPRODUCAO) desc ";

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);

    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);    

    $totBRUTO=0;   $totVENDAS=0;   $totCONTRATOS=0;
    $equipeCAMPEA=''; $equipeCAMPEA2=''; $maiorqtdeCONTRATOS=0;
    $rsGRUPOS = mysql_query($sql, $conexao) or die (mysql_error());
    $totvlrPRODUCAO=0; $totvlrCONTRATO=0; $totvlrRECEBIDO=0; $totvlrPLANTAO=0; $totCONTRATOS=0;
    while ($regGRUPO = mysql_fetch_object($rsGRUPOS)) {
      $totBRUTO += $regGRUPO->vlrCONTRATO;
      $totVENDAS += $regGRUPO->vlrPRODUCAO;
      $totCONTRATOS += $regGRUPO->qtdeCONTRATOS;

      $equipeCAMPEA= $equipeCAMPEA=='' ? $regGRUPO->nomeGRUPO: $equipeCAMPEA;
      $equipeCAMPEA2= $regGRUPO->qtdeCONTRATOS > $maiorqtdeCONTRATOS ? $regGRUPO->nomeGRUPO: $equipeCAMPEA2;
      $maiorqtdeCONTRATOS = $regGRUPO->qtdeCONTRATOS > $maiorqtdeCONTRATOS ? $regGRUPO->qtdeCONTRATOS : $maiorqtdeCONTRATOS;

      $totvlrPRODUCAO+=$regGRUPO->vlrPRODUCAO;
      $totvlrCONTRATO+=$regGRUPO->vlrCONTRATO;
      $totvlrRECEBIDO+=$regGRUPO->vlrRECEBIDO;
      $totvlrPLANTAO+=$regGRUPO->vlrPLANTAO;
      $totCONTRATOS+=$regGRUPO->qtdeCONTRATOS;
    }
    mysql_data_seek($rsGRUPOS, 0);


    $pagina = 0;
    cabecalho();

    fwrite($Arq, "\n");
    fwrite($Arq, str_pad('TOTAL BRUTO: ', 35, ' ', 1) .   
                  str_pad(number_format($totBRUTO, 2, ',', '.'), 10, ' ', 0) . "\n");
    fwrite($Arq, str_pad('TOTAL EM VENDAS: ', 35, ' ', 1) .   
                  str_pad(number_format($totVENDAS, 2, ',', '.'), 10, ' ', 0) . "\n");
    fwrite($Arq, str_pad('EQUIPE CAMPE+ EM VLR PLANT+O: ', 35, ' ', 1) .   
                  str_pad($equipeCAMPEA, 40, ' ', 1) . "\n");
    fwrite($Arq, str_pad('EQUIPE CAMPE+ EM CONTRATOS: ', 35, ' ', 1) .   
                  str_pad($equipeCAMPEA2, 40, ' ', 1) . "\n");
    fwrite($Arq, "\n");
    $lin += 7;


    // le totais vendas por representante 
    $sql = "select count(*) as qtdeCONTRATOS, sum(prop.vlrPRODUCAO) as vlrPRODUCAO, sum(prop.vlrCONTRATO) as vlrCONTRATO, ".
           "  sum(prop.vlrRECEBIDO) as vlrRECEBIDO, sum(prop.vlrPLANTAO) as vlrPLANTAO, ".
           "     repre.idGRUPO, ifnull(grupo.nome, '* GRUPO N+O DEFINIDO *') as nomeGRUPO, ".
            " prop.idREPRESENTANTE, ifnull(repre.nome, '* erro *') as nomeREPRESENTANTE ".
           "from propostas prop ".
           "left join representantes repre ".
           "  on repre.numero = prop.idREPRESENTANTE ".
           "left join grupos_venda grupo ".
           "  on grupo.numreg = repre.idGRUPO ".
           "where ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and ifnull(prop.cancelada, 'N')<>'S' ".
            " group by prop.idREPRESENTANTE, repre.nome, repre.idGRUPO, grupo.nome ".
           " order by sum(prop.vlrPRODUCAO) desc ";

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);

    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);    
    $rsVENDAS = mysql_query($sql, $conexao) or die (mysql_error());


    $posicaoGRUPO=1;
    while ($regGRUPO = mysql_fetch_object($rsGRUPOS)) {  

      if ($lin + 1 > 55)    cabecalho();

      // mostra grupo e sua colocacao
      fwrite($Arq, '<negrito>'. 
                str_pad($posicaoGRUPO, 2, ' ', 0) . '          '.
                str_pad("$regGRUPO->nomeGRUPO ($regGRUPO->idGRUPO)", 36, ' ', 1) .'  ' .
                str_pad($regGRUPO->qtdeCONTRATOS, 5, ' ', 0) .'    ' .
                str_pad(number_format($regGRUPO->vlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regGRUPO->vlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regGRUPO->vlrRECEBIDO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regGRUPO->vlrPLANTAO, 2, ',', '.'), 10, ' ', 0) . "\n");
      $lin++;
 
      // lista representantes do grupo atual e suas colocacoes
      mysql_data_seek($rsVENDAS, 0);
      $posicaoREPRE=1;
      while ($regVENDA = mysql_fetch_object($rsVENDAS)) {

        if ($regVENDA->idGRUPO==$regGRUPO->idGRUPO) {
          if ($lin + 1 > 55)    cabecalho();
          fwrite($Arq, 
                    str_pad($posicaoREPRE, 2, ' ', 0) . '          '.
                    str_pad("$regVENDA->nomeREPRESENTANTE ($regVENDA->idREPRESENTANTE)", 36, ' ', 1) .'  ' .
                    str_pad($regVENDA->qtdeCONTRATOS, 5, ' ', 0) .'    ' .
                    str_pad(number_format($regVENDA->vlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                    str_pad(number_format($regVENDA->vlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                    str_pad(number_format($regVENDA->vlrRECEBIDO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                    str_pad(number_format($regVENDA->vlrPLANTAO, 2, ',', '.'), 10, ' ', 0) . "\n");
  
          $lin++; $posicaoREPRE++;
        }
      }
      fwrite($Arq, "\n");
      $lin++;
 
      $posicaoGRUPO++;
    }
    if ($lin + 2 > 55)    cabecalho();
    fwrite($Arq, "\n");
    fwrite($Arq, '<negrito>'. 
              str_pad(' TOTAL GERAL:               ', 50, ' ', 0) . 
              str_pad($totCONTRATOS, 5, ' ', 0) .'    ' .
              str_pad(number_format($totvlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrRECEBIDO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrPLANTAO, 2, ',', '.'), 10, ' ', 0) . "\n");

    fclose($Arq);
    mysql_free_result($rsGRUPOS);
    mysql_free_result($rsVENDAS);
  }


  //************************************************************************************************
  //************************************************************************************************
  // tipo de relatorio SOMATORIA POR PRODUTO (TIPO DE CONTRATO)
  //************************************************************************************************
  //************************************************************************************************
  if ($tipoREL=='4') {
    $titREL = "Vendas no per�odo: ($data) $dataIniMostrar e $dataFinMostrar $infoADD  ";

    $headers = 
        "Tipo de                                             Valor       Valor       Valor        Valor|".          
        "contrato                             Contratos    produ��o    contrato    recebido     plant�o|".
       str_repeat('-', 80);
  //     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx (999)   99999    999.999,99  999.999,99  999.999,99  999.999,99                  

  
    
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');

    // le totais vendas por grupo de venda 
    $sql = "select count(*) as qtdeCONTRATOS, sum(prop.vlrPRODUCAO) as vlrPRODUCAO, sum(prop.vlrCONTRATO) as ".
           "vlrCONTRATO,   sum(prop.vlrRECEBIDO) as vlrRECEBIDO, sum(prop.vlrPLANTAO) as vlrPLANTAO,  ".
           "prop.idTipoContrato as idPRODUTO, ifnull(tip.descricao, '* TIPO N+O DEFINIDO *') as nomePRODUTO ".
           "from propostas prop  ".
           "left join tipos_contrato tip ".
           "   on tip.numreg = prop.idTipoContrato ".
           "where ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and ifnull(prop.cancelada, 'N')<>'S'  ".
           " group by prop.idTipoContrato, tip.descricao ".
           " order by tip.descricao ";

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);
    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);    

    $pagina = 0;
    cabecalho();

    $rsPRODUTOS = mysql_query($sql, $conexao) or die (mysql_error());
    while ($regPRODUTO = mysql_fetch_object($rsPRODUTOS)) {  
      if ($lin + 1 > 55)    cabecalho();

      // mostra grupo e sua colocacao
      fwrite($Arq,  
                str_pad("$regPRODUTO->nomePRODUTO ($regPRODUTO->idPRODUTO)", 36, ' ', 1) .'  ' .
                str_pad($regPRODUTO->qtdeCONTRATOS, 5, ' ', 0) .'    ' .
                str_pad(number_format($regPRODUTO->vlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regPRODUTO->vlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regPRODUTO->vlrRECEBIDO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                str_pad(number_format($regPRODUTO->vlrPLANTAO, 2, ',', '.'), 10, ' ', 0) . "\n");

      $totvlrPRODUCAO+=$regPRODUTO->vlrPRODUCAO;
      $totvlrCONTRATO+=$regPRODUTO->vlrCONTRATO;
      $totvlrRECEBIDO+=$regPRODUTO->vlrRECEBIDO;
      $totvlrPLANTAO+=$regPRODUTO->vlrPLANTAO;
      $totCONTRATOS+=$regPRODUTO->qtdeCONTRATOS;

      $lin++;
    }
    if ($lin + 2 > 55)    cabecalho();
    fwrite($Arq, "\n");
    fwrite($Arq, '<negrito>'. 
              str_pad(' TOTAL GERAL:               ', 38, ' ', 0) . 
              str_pad($totCONTRATOS, 5, ' ', 0) .'    ' .
              str_pad(number_format($totvlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrRECEBIDO, 2, ',', '.'), 10, ' ', 0) .'  ' .
              str_pad(number_format($totvlrPLANTAO, 2, ',', '.'), 10, ' ', 0) . "\n");

    fclose($Arq);
    mysql_free_result($rsPRODUTOS);
  }



  //************************************************************************************************
  //************************************************************************************************
  // tipo de relatorio SOMATORIA POR PRODUTOR 
  //************************************************************************************************
  //************************************************************************************************
  if ($tipoREL=='5') {
    $titREL = "Vendas no per�odo: ($data) $dataIniMostrar e $dataFinMostrar $infoADD "; 
    $sql = "select count(*) as qtdeCONTRATOS, ifnull(repre.nome, '* ERRO *') as nomeREPRE, sum(prop.vlrCONTRATO) as vlrCONTRATO, ".
            " sum(prop.vlrRECEBIDO) as vlrRECEBIDO, sum(prop.vlrPRODUCAO) as vlrPRODUCAO, prop.idREPRESENTANTE " .
           "from propostas prop " .
           "left join representantes repre " .
           "   on repre.numero = prop.idREPRESENTANTE " .
           " where ifnull(prop.qtdeMENS,0)<>2 and ifnull(prop.pendente,0)<>1 and date_format($cmp, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioOPERADORA @criterioTIPO and ifnull(prop.cancelada, 'N')<>'S' ".
           " group by nomeREPRE ". 
           " order by sum(prop.vlrPRODUCAO) desc";       

    if ($idGRUPO!='')
      $sql = str_replace('@criterioREPRE', " and repre.idGRUPO=$idGRUPO ", $sql);
    else {
      if ($idREPRE=='9999' || $idREPRE=='')
        $sql = str_replace('@criterioREPRE', '', $sql); 
      else
        $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);
    }
    if ($idOPERADORA!='')  $sql = str_replace('@criterioOPERADORA', " and prop.idOPERADORA=$idOPERADORA ", $sql);
    if ($idPRODUTO!='')   $sql = str_replace('@criterioTIPO', " and prop.idTipoCONTRATO=$idPRODUTO ", $sql);
    $sql = str_replace('@criterioTIPO', '', $sql);
    $sql = str_replace('@criterioOPERADORA', '', $sql);    
    if ($idREPRE=='9999' || $idREPRE=='')
      $sql = str_replace('@criterioREPRE', '', $sql); 
    else
      $sql = str_replace('@criterioREPRE', " and prop.idREPRESENTANTE=$idREPRE ", $sql);

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    if (mysql_num_rows($resultado)==0) die('nada'); 
  
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  
    $pagina = 0;
  
    $lin = 87;
  
    $headers = 
        "                                                                 Valor de    Valor do     Valor|".          
        "Corretor                                            Contratos    produ��o    contrato    recebido|".
       str_repeat('-', 120);
  //     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx      99999  999.999,99  999.999,99  999.999,99

    while ($row = mysql_fetch_object($resultado)) {

      if ($lin + 1 > 55)    cabecalho();

      fwrite($Arq, str_pad("$row->nomeREPRE ($row->idREPRESENTANTE)", 50, ' ', 1) .'      ' .
                    str_pad($row->qtdeCONTRATOS, 5, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrPRODUCAO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrCONTRATO, 2, ',', '.'), 10, ' ', 0) .'  ' .
                    str_pad(number_format($row->vlrRECEBIDO, 2, ',', '.'), 10, ' ', 0)  ."\n");
      $lin++;
    } 

    fclose($Arq);
  }

}


/*****************************************************************************************/
if ($acao=='contratos_erros') {
  $titREL = "Contratos sem operadora/tipo de contrato definido ";
  
  $headers = 
      "Sequ�ncia Contratante                                         Problema                               |".
     str_repeat('-', 120);
//    999999     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  xxx      

  // le mensalidades
  $sql = "select opa.numreg as numOPERADORA, contra.numreg as numTIPOCONTRATO, prop.contratante, prop.sequencia, " .
        " prop.operadoraEXCEL, prop.tipoContratoEXCEL ".
         "from propostas prop " .
         "left join operadoras opa " .
         "   on opa.numreg = prop.idOPERADORA " .
         "left join tipos_contrato contra " .
         "   on contra.numreg = prop.idTipoContrato " .
          " where contra.numreg is null or opa.numreg is null ".
          " order by contratante";


  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;

  $lin = 87;
  while ($row = mysql_fetch_object($resultado)) {  

    if ($lin + 1 > 55)    cabecalho();

    $operadora = $row->operadoraEXCEL!='' ? $row->operadoraEXCEL : 'em branco'; 
    $contrato = $row->tipoContratoEXCEL!='' ? $row->tipoContratoEXCEL: 'em branco';

    $problema='';
    if ($row->numTIPOCONTRATO=='') $problema="tipo contrato indefinido ($contrato)";
    if ($row->numOPERADORA=='')  $problema = $problema=='' ? "operadora indefinida ($operadora)" : 
                    "tipo contrato ($contrato)/operadora ($operadora) indefinidos";

    fwrite($Arq, str_pad($row->sequencia, 6, ' ', 0) .'    ' .
                  str_pad($row->contratante, 50, ' ', 1) .'  ' .  
                  $problema  ."\n");
    $lin++;
  }

  fclose($Arq);
}


/*****************************************************************************************/
if ($acao=='lerDATAS') {

  $offset = strtotime(date('d-m-Y'));
  
  if(date('w',$offset) == 1)
    $segunda = date('d-m-y',$offset);
  else
    $segunda = date('d-m-y',strtotime("last Monday",$offset));
  
  if(date('w',$offset) == 5)
    $domingo = date('d-m-y',$offset);
  else
    $domingo = date('d-m-y',strtotime("next Friday",$offset));
    
  $resp = $segunda . ';' . $domingo . ';' .
      date('d/m/y', mktime(00, 00, 00, date('m'), 01)) . ";" . 
      date('d/m/y', mktime(23, 59, 59, date('m')+1, 00)) .';'.
      date('d/m/y');
}


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();



/*****************************************************************************************/
function cabecalho() {
global $pagina, $Arq, $titREL, $titulos, $row, $lin, $acao, $hoje, $agora, $headers;

if ($pagina>0)   fwrite($Arq,  "FIM PAGINA \n" );
  
$pagina ++;

fwrite($Arq,  $_SESSION['empresa'] ."\n"); 
fwrite($Arq,  "$titREL \n");
$lin=2;

if ($titulos!='') {
 $tit = explode('|', $titulos);
 for ($r=0; $r<count($tit); $r++) {
   fwrite($Arq, "$tit[$r] \n");
   
   $lin++;  
 }
}
        
$adicional = explode(';', $_SESSION['idUSUARIO_LOGADO']);
fwrite($Arq,  str_repeat('=', 80) . "\n");      
fwrite($Arq,  "Relat�rio emitido em: $hoje as $agora  por: $adicional[0] ($adicional[1])        P�GINA: $pagina \n");
fwrite($Arq,  str_repeat('=', 80) . "\n");
$lin += 3;

$header = explode('|', $headers);
for ($e=0; $e<count($header); $e++) {
  fwrite($Arq, "$header[$e] \n");
  
  $lin++;  
}
}

/*****************************************************************************************/
function cabecalho2() {
global $pagina,$Arq, $titREL, $titulos, $row, $lin, $acao, $hoje, $agora, $headers;

if ($pagina>0)   fwrite($Arq,  "FIM PAGINA \n" );
  
$pagina ++;

$lin=1;
$header=$headers;
$header=str_replace('YY', str_pad($pagina, 2, ' ', 0), $header);
$header = explode('|', $header);
for ($e=0; $e<count($header); $e++) {
  fwrite($Arq, "$header[$e] \n");
  
  $lin++;  
}
}



/*****************************************************************************************/
function creditos($idREPREATUAL, $dataINI, $dataFIN, $dataIniMostrar, $dataFinMostrar, $contaENTREGA, $totCOMISSAO_2) {
global $pagina, $Arq, $hoje, $agora, $conexao, $lin;
  
$titRELCRED = "Cr�ditos/d�bitos registrados no per�odo: $dataIniMostrar e $dataFinMostrar ";

$headersCRED = 
    "                                     Registrado                                                  Valor                                    |".          
    "Corretor                             em:         Tipo  Descri��o                                 R$       Contratante(s)                  |".
   str_repeat('-', 130);
//     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  99/99/99    X     xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999,99  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

  
$sql  = "select cx.numreg as idCAIXA, cx.idOPERACAO, cre.numero, cre.descricao, ifnull(rep.nome, '') as nomeREPRESENTANTE, " .
        " cre.representante as idREPRESENTANTE, date_format(data, '%d/%m/%y') as dataREGISTRO, ifnull(pagoVALE_CREDITO,0) as pagoVALE_CREDITO, ".
        " cre.valor, ucase(cre.tipo) as tipo, ifnull(cre.descontoVALE, 0) as descontoVALE, cre.numVALE_CREDITO , " .
        " date_format(pagarVALE, '%d/%m/%y') as pagarVALE_MOSTRAR  ".
        " from creditos_descontos cre ".
        "left join representantes rep ".
        " on rep.numero = cre.representante " .
        "left join caixa cx ".
        " on cx.numreg = cre.idCAIXA ".
        " where date_format(pagarVALE, '%Y%m%d') between '$dataINI' and '$dataFIN' @criterioREPRE @criterioTIPO   and ifnull(cre.excluido,0)=0 " .          
        "order by idREPRESENTANTE,data desc " ;

$sql = str_replace('@criterioREPRE', " and cre.representante=$idREPREATUAL ", $sql);
$sql = str_replace('@criterioTIPO', "  and ifnull(cre.numVALE_CREDITO, '')=''  ", $sql);

$rsCRED = mysql_query($sql, $conexao) or die (mysql_error());

if (mysql_num_rows($rsCRED)==0) return('nada');

//die($sql); 

$linCRED = $lin;

$txtCRED='';
if ($linCRED+6>55) {
  if ($pagina>0)   $txtCRED .=  "FIM PAGINA \n" ;
      
  $pagina ++;

  $txtCRED .= $_SESSION['empresa'] ."\n" ; 
  $txtCRED .= "$titRELCRED \n";
  $linCRED=2;
    
  $adicional = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $txtCRED .=  str_repeat('=', 80) . "\n";      
  $txtCRED .=  "Relat�rio emitido em: $hoje as $agora, por: $adicional[0] ($adicional[1])       P�GINA: $pagina \n";
  $txtCRED .=  str_repeat('=', 80) . "\n";
  $linCRED += 3;
}
    
$txtCRED .=  " \n";

$header = explode('|', $headersCRED);
for ($e=0; $e<count($header); $e++) {
  $txtCRED .= "$header[$e] \n";
    
  $linCRED++;  
}


$totVALOR=0;  $totDESCONTO=0;
while ($regCRED = mysql_fetch_object($rsCRED)) {  

  if ($linCRED + 1 > 55)    {
    if ($pagina>0)   $txtCRED .=  "FIM PAGINA \n" ;
      
    $pagina ++;

    $txtCRED .=  $_SESSION['empresa'] ."\n"; 
    $txtCRED .=  "$titRELCRED \n";
    $linCRED=2;
    
    $adicional = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $txtCRED .=  str_repeat('=', 80) . "\n";      
    $txtCRED .=  "Relat�rio emitido em: $hoje as $agora, por: $adicional[0] ($adicional[1])          P�GINA: $pagina \n";
    $txtCRED .=  str_repeat('=', 80) . "\n";
    $linCRED += 3;
    
    $header = explode('|', $headersCRED);
    for ($e=0; $e<count($header); $e++) {
      $txtCRED .= "$header[$e] \n";
      
      $linCRED++;  
    }
  }

  if ($regCRED->tipo=='C') $totVALOR += $regCRED->valor; else $totVALOR -= $regCRED->valor; 
  
  // se for um credito/debito relacionado com uma entrega de proposta, tenta conseguir o nome do(s) contratante(s) envolvidos
  $contratantes='';
  if ($regCRED->idOPERACAO==$contaENTREGA) {

    $sql= "select prop.contratante from caixa cx ".
          "inner join entregaspropostas ent ".
          "  on ent.idcaixa=cx.numreg ".
          "inner join propostas prop ".
          "  on prop.numregPropostaEntregueCaixa = ent.numreg ".
          "where cx.numreg=$regCRED->idCAIXA ; ";

    $rsCONTRAT = mysql_query($sql) or die (mysql_error());
    if (mysql_num_rows($rsCONTRAT)>0) {
      while ($regCONTRAT = mysql_fetcH_object($rsCONTRAT)) {
        $contratantes .= $contratantes=='' ? '' : ',' ;
        $contratantes .= $regCONTRAT->contratante;
      }
    }
    mysql_free_result($rsCONTRAT);
  }

  if (strlen($contratantes)>45) $contratantes=substr($contratantes, 0, 42) . '....';

  $valor = $regCRED->tipo=='D' ? '-' . number_format($regCRED->valor, 2, ',', '') : number_format($regCRED->valor, 2, ',', '');
  $info = str_pad("$regCRED->nomeREPRESENTANTE ($regCRED->idREPRESENTANTE)", 35, ' ', 1) .'  ' .
                str_pad($regCRED->dataREGISTRO, 8, ' ', 1).'    ' .
                str_pad($regCRED->tipo, 1, ' ', 0) .'     ' .
                substr(str_pad($regCRED->descricao, 40, ' ', 1), 0, 40) .'  ' .
                str_pad($valor, 7, ' ', 0) . '  '.
                substr($contratantes, 0, 45);
  $info = str_pad($info, 150, ' ', 1);       
  $txtCRED .= $info . " \n";

  $linCRED++;
}
$txtCRED .= '<negrito>'.str_pad(" TOTAL:      ", 97, ' ', 0) . str_pad(number_format($totVALOR, 2, ',', ''), 7, ' ', 0) . 
              "                            \n" ;
$txtCRED .= "^$totVALOR"; 

return($txtCRED);
}



?>
