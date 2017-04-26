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



/*****************************************************************************************/
if ($acao=='situacao') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $repre = $_REQUEST['repre'];  
  $buscar = $_REQUEST['buscar'];    // 1= recebidos   2= entregues     3= cadastrados     4= cancelados    5= todos  
  
  $titREL = 'Relatorio de situacao dos contratos';
  $titulos_temp="Período: $dataIniMostrar a $dataFinMostrar|".
                'Representante: @repre';  
  
  $headers=
    'Contrato  Situacao   |'. 
    str_repeat('-', 120);
//   9999999   xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxXXXXXXXXXXXXXXXXXXXXXXXXXXXxxxxxxxxxxxxxxxxX              
  
// recebido 
// entregue  
// cancelado na entrega, motivo:
// cancelado após cadastro, motivo:  

  // se é pra listar somente props recebidas ou todas as propostas
  // isola as propostas recebidas porque na tabela recebimentopropostas 
  // infelizmente ele nao isola (registro a registro) - isso foi um erro de projeto pode se dizer
  if ($buscar==1 || $buscar==5) { 
    mysql_query("create temporary table trab (proposta int, data date, numrepresentante smallint); ", $conexao) or die (mysql_error());
    
    // props recebidas
    $sql = "select priprop, ultprop, date_format(data, '%Y%m%d') as data, rp.numrepresentante ". 
           "from propostasrecebidas pr  ". 
           "inner join recebimentospropostas rp ".
           "	on pr.numrec = rp.numero ".
           "where rp.data between '$dataini'	and '$datafin' ".
           "group by priprop, ultprop, data, rp.numrepresentante";
           
    
    $rsRECEB = mysql_query($sql, $conexao) or die (mysql_error());
    
    $sqlInsercao='';
    $props = '';
    
    while ($regRECEB = mysql_fetch_object($rsRECEB)) {
      for ($prop = $regRECEB->priprop; $prop<=$regRECEB->ultprop; $prop++)  {
        $props .= ($props=='') ? '' : ', ';
        $props .= "($prop, '$regRECEB->data', $regRECEB->numrepresentante)";     
      }  
      $sqlInsercao .= ($sqlInsercao=='') ? '' : ', ';
      $sqlInsercao .= $props;
      
      if (strlen($sqlInsercao)>=100) { 
         $sqlInsercao='insert into trab values ' . $sqlInsercao;
         mysql_query($sqlInsercao, $conexao) or die (mysql_error());
         
         $sqlInsercao='';
         $props = '';
       }  
            
    }  
    if ($sqlInsercao!='') { 
       $sqlInsercao='insert into trab values ' . $sqlInsercao;
       mysql_query($sqlInsercao, $conexao) or die (mysql_error());
     }  
    

    mysql_free_result($rsRECEB);
    
    
    // exclui das recebidas, propostas que ja foram entregues ou cadastradas
    mysql_query('delete from trab where proposta in ( select numprop from propostasentregues )', $conexao) 
        or die (mysql_error());
    mysql_query('delete from trab where proposta in ( select numcontrato from listadepropostas )', $conexao) 
        or die (mysql_error());
  }     
            
  // le representantes cujo tipo=1 (repre base), 3 (repre tele) ou 5 (repre 3o)
  $sql = 'select numero,nome from representantes where (tipo=1 or tipo=3 or tipo=5) @criterio order by nome';
  $sql = str_replace('@criterio',  ( $repre!=9999 ? " and numero=$repre" : '' ), $sql);
    
//die($sql);
  $rsREPRE = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($rsREPRE)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;  
  $lin = 0;
  
  $nada=true;
  
  while ($regREPRE = mysql_fetch_object($rsREPRE)) {  
    
    // 1= recebidos   2= entregues     3= cadastrados     4= cancelados    5= todos
    
    if ($buscar==1)     // recebidos               
      $sql = "select proposta as vlr, concat('Recebida em ', date_format(data, '%d/%m/%y')) as situacao from trab where numrepresentante=$regREPRE->numero";
             
    else if ($buscar==2)     // entregues               
      $sql = "select numprop as vlr, " . 
             "	concat('Entregue em ',  date_format(data, '%d/%m/%y')) as situacao " . 
             "from propostasentregues pe " .
             "inner join entregaspropostas ep " .
             "		on ep.numero=pe.nument " .
             "where ifnull(idcancel, '')='' and ep.data between '$dataini'	and '$datafin' ".
             "and ep.numrepresentante=$regREPRE->numero";
             
    else if ($buscar==3)     // cadastrados               
      $sql = "select numcontrato as vlr, ". 
             "	concat('Cadastrada em ',  date_format(lst.datacadastro, '%d/%m/%y')) as situacao ". 
             "from listadepropostas lst ".
             "where ifnull(lst.idcancelamento, '')='' and lst.datacadastro between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero";             

    else if ($buscar==4)     // canceladas               
      $sql = "select numprop as vlr, ". 
             "	concat('Cancelada na entrega em ',  date_format(data, '%d/%m/%y'), ', motivo: ', mot.descricao) as situacao " . 
             "from propostasentregues pe " .
             "inner join entregaspropostas ep " .
             "		on ep.numero=pe.nument " .
             "left join motivos_cancelamento mot " .
             "		on mot.numero=pe.idcancel " .		
             "where ifnull(idcancel, '')<>'' and ep.data between '$dataini'	and '$datafin' " .
             "and ep.numrepresentante=$regREPRE->numero " .             
             "union " .
             "select numcontrato as vlr, ". 
             "	concat('Cancelada após cadastro em ',  date_format(prop.datacancelamento, '%d/%m/%y'), ', motivo: ', mot.descricao) as situacao ". 
             "from listadepropostas lst ".
             "inner join propostas prop ".
             "		on prop.sequencia=lst.sequencia ".
             "left join motivos_cancelamento mot ".
             "		on mot.numero=lst.idcancelamento		 ".
             "where ifnull(lst.idcancelamento, '')<>'' and prop.datacancelamento between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero";             

    // se gerar relatorio de todas as propostas lê (pertencentes ao representante) 
    // propostas somente recebidas      
    // + propostas canceladas na entrega 
    // + propostas canceladas no cadastro +
    // + propostas cadastradas normalmente
    else if ($buscar==5)      
      $sql = "select proposta as vlr, concat('Recebida em ', date_format(data, '%d/%m/%y')) as situacao ".
             "from trab where numrepresentante=$regREPRE->numero " .
             "union " .
             "select numprop as vlr, ". 
             "	concat('Cancelada na entrega em ',  date_format(data, '%d/%m/%y'), ', motivo: ', mot.descricao) as situacao " . 
             "from propostasentregues pe " .
             "inner join entregaspropostas ep " .
             "		on ep.numero=pe.nument " .
             "left join motivos_cancelamento mot " .
             "		on mot.numero=pe.idcancel " .		
             "where ifnull(idcancel, '')<>'' and ep.data between '$dataini'	and '$datafin' " .
             "and ep.numrepresentante=$regREPRE->numero " .             
             "union " .
             "select numcontrato as vlr, ". 
             "	concat('Cancelada após cadastro em ',  date_format(prop.datacancelamento, '%d/%m/%y'), ', motivo: ', mot.descricao) as situacao ". 
             "from listadepropostas lst ".
             "inner join propostas prop ".
             "		on prop.sequencia=lst.sequencia ".
             "left join motivos_cancelamento mot ".
             "		on mot.numero=lst.idcancelamento		 ".
             "where ifnull(lst.idcancelamento, '')<>'' and prop.datacancelamento between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero " .             
             "union " . 
             "select numcontrato as vlr, ". 
             "	concat('Cadastrada em ',  date_format(lst.datacadastro, '%d/%m/%y')) as situacao ". 
             "from listadepropostas lst ".
             "where ifnull(lst.idcancelamento, '')='' and lst.datacadastro between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero";             

    $sql .= " order by vlr ";
    
//    die($sql);
 

    $rsPROPS = mysql_query($sql, $conexao) or die (mysql_error());
    
    if ( mysql_num_rows($rsPROPS)<1 ) continue;
    

    $titulos = str_replace('@repre', "$regREPRE->nome ($regREPRE->numero)", $titulos_temp);
    cabecalho();
     
    
    while ($regPROP = mysql_fetch_object($rsPROPS)) { 
      if ($lin + 1 > 55)   cabecalho();
      
      $nada=false;
      fwrite($Arq, str_pad($regPROP->vlr, 8, ' ', 0) .'  ' .  
                    "$regPROP->situacao\n");
                    
      $lin++;
    }
    mysql_free_result($rsPROPS);
  }
  
  mysql_query("drop table trab", $conexao);
  mysql_free_result($rsREPRE);
  
  fclose($Arq);
  
  if ($nada) die('nada');
}



/*****************************************************************************************/
if ($acao=='mensalidades') {
  $opcao = $_REQUEST['opcao'];
  if ($opcao!=2) {
    $dataIniMostrar = $_REQUEST['dataIniMostrar'];
    $dataFinMostrar = $_REQUEST['dataFinMostrar'];
    
    $dataINI = $_REQUEST['DATAINI'];
    $dataFIN = $_REQUEST['DATAFIN'];  
    
  } 
  $idREPRE = '';
  if ( isset($_REQUEST['repre']) )    $idREPRE = $_REQUEST['repre'];
  
  $gerar2e4 = $_REQUEST['gerar2e4'];     
  
  if ($opcao==1) $titREL = "Mensalidades com data vencimento entre $dataIniMostrar e $dataFinMostrar ";
  if ($opcao==2) $titREL = "Mensalidades cuja proposta não foi localizada pela Clinipam";
  if ($opcao==3) $titREL = "Mensalidades não pagas com data vencimento entre $dataIniMostrar e $dataFinMostrar ";  
  if ($opcao==4) $titREL = "Mensalidades não pagas com data vencimento entre $dataIniMostrar e $dataFinMostrar ";
  if ($opcao==5) $titREL = "Mensalidades pagas entre $dataIniMostrar e $dataFinMostrar ";
    
  
  if ($opcao==5) 
      $headers = 
          "Nº da                                Parc.  Data de     Valor/Pago|".          
          "proposta  Contratante                       Venc.       (R$)           Situaçäo|".
         str_repeat('-', 80);
  else
    $headers = 
      "Nº da                                Parc.  Data de     Valor/comissäo|".          
      "proposta  Contratante                       Venc.       (R$)           Situaçäo|".
     str_repeat('-', 80);      


  
  // le comissoes da empresa    
  $resultado = mysql_query("select comissaoSobreMensalidades from comissaoameg", $conexao) or die (mysql_error()); 
  
  $comissao='';

  $row = mysql_fetcH_object($resultado);
  for ($r=1; $r<=9; $r++)   {
    $comissao .= $r==1 ? '' : ';'; 
    $comissao .= substr($row->comissaoSobreMensalidades , ($r-1)*3, 3);        
  }
  $comissoes = explode(';', $comissao);
  
  mysql_free_result($resultado);

  // le mensalidades
  $sql = "select lst.numCONTRATO, lst.contratante, fut.ordem, fut.valor, date_format(dataPGTOParcela, '%d/%m/%y') as dataPGTOParcela , fut.valorPagoParcela, " .
         "date_format(fut.dataVENCIMENTO, '%d/%m/%y') as dataVENCIMENTO, ".
         " ifnull(fut.situacaoPARCELA, '2') as situacao, " .
         "lst.numREPRESENTANTE, ifnull(repre.nome, '* erro *') as nomeREPRESENTANTE, ".
          "case ifnull(situacaoPARCELA, 2)   " .
          "when 1 then 'Paga'  " .
          "when 2 then 'Em aberto'   " .
          "when 3 then 'Não localizada' " . 
          "when 4 then 'Cancelada'  " .
          "end as descSITUACAO " .          
         "from futuras fut " .
         "inner join listadepropostas lst  " .
         "		on lst.sequencia = fut.sequencia ".
         "left join representantes repre " .
         "		on repre.numero = lst.numREPRESENTANTE  " ;
         		
  if ($opcao==1)   
    $sql .= "where dataVENCIMENTO between '$dataINI' and '$dataFIN' order by dataVENCIMENTO desc ";
    
  else if ($opcao==2) 
    $sql .= "where situacaoPARCELA=3 order by dataVENCIMENTO desc  ";
    
  else if ($opcao==3)   
    $sql .= "where dataVENCIMENTO between '$dataINI' and '$dataFIN' and " .
          "ifnull(situacaoPARCELA, 2)=2 order by dataVENCIMENTO desc ";
    
  else if ($opcao==4)   {
    $sql .= "where dataVENCIMENTO between '$dataINI' and '$dataFIN' and ifnull(situacaoPARCELA, 2)=2 " ;
    if ($idREPRE!=9999)  $sql .= " and lst.numREPRESENTANTE = $idREPRE";
    $sql .= " order by lst.numREPRESENTANTE asc, dataVENCIMENTO desc";
  }
  else if ($opcao==5)   {
    $sql .= "where dataPGTOParcela between '$dataINI' and '$dataFIN' and ifnull(situacaoPARCELA, 2)=1 " ;
    if ($gerar2e4=='true')  
      $sql .= " and (fut.ordem=2 or fut.ordem=4) ";
    if ($idREPRE!=9999)  $sql .= " and lst.numREPRESENTANTE = $idREPRE";
    $sql .= " order by lst.numREPRESENTANTE asc, dataVENCIMENTO desc";
  }
  
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');
  

  $pagina = 0;
  
  $lin = 87;
  $repreATUAL = 'none';
  
  $tot=0;
    
  while ($row = mysql_fetch_object($resultado)) {  

    if ($idREPRE!='' ) {     
        
      if ($lin + 1 > 55  || $repreATUAL != $row->numREPRESENTANTE)   {
        $titulos = "Representante: $row->nomeREPRESENTANTE ($row->numREPRESENTANTE)"; 

        $repreATUAL = $row->numREPRESENTANTE;
        cabecalho();
      }  
        
    }
    else {
      if ($lin + 1 > 55)    cabecalho();
    }  

    $valor = number_format($row->valor, 2, ',', '')  ;
    $tot += $row->valorPagoParcela;
    
    if ($opcao==5)  {
      $comissao =number_format($row->valorPagoParcela * .10, 2, ',', '')  ;
      $totCOMISSAO += $row->valorPagoParcela * 0.1;
    }                 
    else {
      $comissao = number_format($row->valorPagoParcela * ($comissoes[($row->ordem-1)]/100), 2, ',', '')  ;
      $totCOMISSAO += $row->valorPagoParcela * ($comissoes[($row->ordem-1)]/100);
    }               
    
    fwrite($Arq, str_pad($row->numCONTRATO, 8, ' ', 1) .'  ' .  
                  substr(str_pad($row->contratante, 25, ' ', 1), 0, 25) .'  ' .
                  str_pad("$row->ordem", 5, ' ', 1) .'  ' .
                  str_pad($row->dataVENCIMENTO, 8, ' ', 0) .'   ' .
                  str_pad($valor, 6, ' ', 0) .'/' .                  
                  str_pad($comissao, 6, ' ', 0) .'   ' .
                  str_pad($row->descSITUACAO, 8, ' ', 1)  ."\n");
    $lin++;
  }
  if ($lin + 6 > 55)    cabecalho();
  
  fwrite($Arq, "\n\n" . 'TOTAL PARCELAS PAGAS: ' .
                str_pad(number_format($tot, 2, ',', ''), 6, ' ', 0) .
                '          ,TOTAL COMISSAO DO REPRESENTANTE : ' .      
                str_pad(number_format($totCOMISSAO, 2, ',', ''), 6, ' ', 0)); 
    

  fclose($Arq);
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
        
fwrite($Arq,  str_repeat('=', 80) . "\n");      
fwrite($Arq,  "Relatório emitido em: $hoje as $agora          PÁGINA: $pagina \n");
fwrite($Arq,  str_repeat('=', 80) . "\n");
$lin += 3;

$header = explode('|', $headers);
for ($e=0; $e<count($header); $e++) {
  fwrite($Arq, "$header[$e] \n");
  
  $lin++;  
}
}



?>
