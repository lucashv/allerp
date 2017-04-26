<?php
header("Content-Type: text/html; charset=iso-8859-1");


session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );
require_once( '../includes/senha.php'  );
require_once( '../includes/funcoesDATA.php'  );

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

die('334');


/*****************************************************************************************/
if ($acao=='midia') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $titREL = 'Retorno de mídia';
  $titulos="Período: $dataIniMostrar a $dataFinMostrar   Agrupado por: ";
  
  if (isset( $_REQUEST['tipobusca']))   $tipobusca = $_REQUEST['tipobusca'];
  $info = $tipobusca==1 ? 'cadastradas' : 'assinadas';
  $cmpfiltro = $tipobusca==1 ? 'datacadastro' : 'dataassinatura';
  $titulos = "Propostas $info entre $dataIniMostrar e $dataFinMostrar";    
  
  $headers=
    'Mídia                          Qtde usuários (%) |'.         
    str_repeat('-', 80);
  

  $sql = 'select count(*) as qtde, prop.numMIDIA, mid.nome '.  
         'from propostas prop '.
         'inner join usuarios usu '.
         '	on usu.sequencia=prop.sequencia '.
         'left join midias mid '.
         '	on prop.nummidia = mid.numero  '.           
         "where ifnull(prop.nummidia,0)<>0  and prop.$cmpfiltro between '$dataini' and '$datafin'   " .
         'group by nummidia ';
         

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;  
  $lin = 87;
  
  $tot=0;
  while ($row = mysql_fetch_object($resultado)) {
    $tot += $row->qtde;  
  }
  mysql_data_seek($resultado, 0);  
  
  while ($row = mysql_fetch_object($resultado)) {
    if ($lin + 1 > 55)  cabecalho();
    
    fwrite($Arq, str_pad($row->nome, 30, ' ', 1) .'  ' .  
                  str_pad(number_format($row->qtde, 0, ',', '.'), 7, ' ', 0) .                  
                  ' ('.str_pad(number_format($row->qtde * 100 / $tot, 0, ',', '.'), 3, ' ', 0) . '%)' ."\n" );                  
  }
  fwrite($Arq, "\n");
    

  fclose($Arq);
}







/*****************************************************************************************/
if ($acao=='operadoras') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $titREL = 'Operadoras anteriores';
  $titulos="Período: $dataIniMostrar a $dataFinMostrar   Agrupado por: ";
  
  if (isset( $_REQUEST['tipobusca']))   $tipobusca = $_REQUEST['tipobusca'];
  $info = $tipobusca==1 ? 'cadastradas' : 'assinadas';
  $cmpfiltro = $tipobusca==1 ? 'datacadastro' : 'dataassinatura';
  $titulos = "Propostas $info entre $dataIniMostrar e $dataFinMostrar";    
  
  $headers=
    'Operadora                      Qtde usuários (%) |'.         
    str_repeat('-', 80);
  

  $sql = 'select count(*) as qtde '.  
         'from propostas prop '.
         'inner join usuarios usu '.
         '	on usu.sequencia=prop.sequencia '.
         "where prop.$cmpfiltro between '$dataini' and '$datafin'   " ;
         
  $totMES=0;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetch_object($resultado) ;
  $totMES=$row->qtde;
  mysql_free_result($resultado); 

  $sql = 'select count(*) as qtde, prop.operadoraANTERIOR, ope.nome '.  
         'from propostas prop '.
         'inner join usuarios usu '.
         '	on usu.sequencia=prop.sequencia '.
         'left join operadoras ope '.
         '	on prop.operadoraANTERIOR = ope.numero  '.           
         "where operadoraanterior<>0 and prop.$cmpfiltro between '$dataini' and '$datafin'   " .
         'group by operadoraanterior ';
         

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');

  $pagina = 0;  
  $lin = 87;
  
  $tot=0;
  while ($row = mysql_fetch_object($resultado)) {
    $tot += $row->qtde;  
  }
  mysql_data_seek($resultado, 0);  
  
  while ($row = mysql_fetch_object($resultado)) {
    if ($lin + 1 > 55)  cabecalho();
    
    fwrite($Arq, str_pad($row->nome, 30, ' ', 1) .'  ' .  
                  str_pad(number_format($row->qtde, 0, ',', '.'), 7, ' ', 0) .                  
                  ' ('.str_pad(number_format($row->qtde * 100 / $tot, 0, ',', '.'), 3, ' ', 0) . '%)' ."\n" );                  
  }
  fwrite($Arq, "\n");
  fwrite($Arq, str_pad('Total compra carência:', 30, ' ', 1) .'  ' .  
                  str_pad(number_format($tot, 0, ',', '.'), 7, ' ', 0) .
                  ' ('.str_pad(number_format($tot * 100 / $totMES, 0, ',', '.'), 3, ' ', 0) . '%) ' ."\n" );
  fwrite($Arq, str_pad('Total sem compra carência:', 30, ' ', 1) .'  ' .  
                  str_pad(number_format($totMES - $tot, 0, ',', '.'), 7, ' ', 0) .
                  ' ('.str_pad(number_format(($totMES-$tot) * 100 / $totMES, 0, ',', '.'), 3, ' ', 0) . '%) ' ."\n" );
  fwrite($Arq, str_pad('Total usuários período:', 30, ' ', 1) .'  ' .  
                  str_pad(number_format($totMES, 0, ',', '.'), 7, ' ', 0) );
                                                       
    

  fclose($Arq);
}




/*****************************************************************************************/
if ($acao=='usuarios') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];
  
  $busca = $_REQUEST['buscar'];
  $cmpBUSCA = $busca==1 ? 'dataCADASTRO' : 'dataASSINATURA';  
  $busca = $busca==1 ? 'Data de cadastro' : 'Data de assinatura';
  
  $relaciona = $_REQUEST['relacionar'];

  $titREL = 'Relatorio de usuários por fx etária/plano';
  $titulos="Período ($busca): $dataIniMostrar a $dataFinMostrar";
  

  // se relacionar por plano
  if ($relaciona==1) { 
    $headers=
        '                                                     1ª mens.  Vlr médio |'.
        'Plano                                  Usuários (%)  (R$)      (R$)|'.
        '--------------------------------------------------------------------------------';
//       xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  9999 (999)    9.999,99  9.999,99
    
    $sql = "select count(*) as totCANCELADOS ".
           "from listadepropostas lst " . 
           "inner join usuarios usu " .
           "	on usu.sequencia=lst.sequencia " .
           "inner join planos pl  " .
           "	on pl.numero = usu.plano " .	
           "where $cmpBUSCA between '$dataini' and '$datafin' and ifnull(idCANCELAMENTO,0)<>0";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    
    $totCANCELADOS=0;
    if (mysql_num_rows($resultado)>0) {
      $row = mysql_fetch_object($resultado); 
      $totCANCELADOS=$row->totCANCELADOS;
    }

    $sql = "select plano, sum(remocao) as vlrREMOCAO, pl.nome, count(*) as totUSUARIOS, sum(vlr1aMensalidade) as vlrMens, ".
            "sum(vlr1aMensalidade)/count(*) as vlrMEDIO, ".
            "sum(case when remocao>0 then 1 else 0 end) as totREMOCAO, sum(remocao) as vlrREMOCAO ". 
           "from listadepropostas lst " . 
           "inner join usuarios usu " .
           "	on usu.sequencia=lst.sequencia " .
           "inner join planos pl  " .
           "	on pl.numero = usu.plano " .	
           "where $cmpBUSCA between '$dataini' and '$datafin' and ifnull(lst.idCANCELAMENTO,0)=0 ". 
           "group by plano " .
           "order by plano";
            
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    
    if (mysql_num_rows($resultado)==0) die('nada'); 
  
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  
    $pagina = 0;  
    $lin = 87;
     
    $totUSUARIOS=0; $vlrREMOCAO=0; $totREMOCAO=0; 
    while ($row = mysql_fetch_object($resultado)) {

      if ($row->idCANCELAMENTO==0)   { 
        $totUSUARIOS += $row->totUSUARIOS;
        $vlrREMOCAO += $row->vlrREMOCAO;
        $totREMOCAO += $row->totREMOCAO;
      }
    }
    mysql_data_seek($resultado, 0);
    
    while ($row = mysql_fetch_object($resultado)) {  
      
      if ($lin + 1 > 55)   cabecalho();
  
      if ($relaciona==1) {
        $percUSUARIO = $row->totUSUARIOS * 100 /$totUSUARIOS;
          
        fwrite($Arq,  str_pad($row->nome, 37, ' ', 1) .'  ' .
                      str_pad($row->totUSUARIOS, 4, ' ', 0) .'  (' .                  
                      str_pad(number_format($percUSUARIO, 0, ',', '.'), 3, ' ', 0)  .')    ' .
                      str_pad(number_format($row->vlrMens, 2, ',', '.'), 8, ' ', 0) .'  ' .                                    
                      str_pad(number_format($row->vlrMEDIO, 2, ',', '.'), 8, ' ', 0) ."\n" );
      }                  
      $lin++;
    }
    
    
    if ($totREMOCAO>0)
      $mediaREMOCAO = number_format($vlrREMOCAO / $totREMOCAO, 2, ',', '.');
    else 
      $mediaREMOCAO = 0;  
    $vlrREMOCAO = number_format($vlrREMOCAO, 2, ',', '.');
    
    fwrite($Arq, " \n");
    fwrite($Arq, "Total de remoções: $totREMOCAO , R$: $vlrREMOCAO     Média: $mediaREMOCAO \n");
    fwrite($Arq, "TOTAL DE USUÁRIOS: $totUSUARIOS   \n"); 
    fwrite($Arq, "                   (+ $totCANCELADOS usuários cancelados não calculados na estatística acima)");
  }
  
  else {
    $headers='';
    
    $sql = "select lst.sequencia, usu.parentesco, date_format(usu.datanasc, '%Y%m%d') as dataNASC, " .
           " date_format(lst.dataassinatura, '%Y%m%d') as dataASSINATURA, ucase(sexo) as sexo, vlr1aMensalidade " .
           "from listadepropostas lst  " .
           "inner join usuarios usu  " .
           "	on usu.sequencia=lst.sequencia " .
           "where $cmpBUSCA between '$dataini' and '$datafin' " .
           " and ifnull(lst.idCANCELAMENTO, 0)=0";

    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    
    if (mysql_num_rows($resultado)==0) die('nada'); 
  
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  
    $pagina = 0;  

    cabecalho();
    
    // zera matriz 
    $valores = array();
    
    // matriz que conterá a somatorio por genero, parentesco, vlr medio
    for ($a=1; $a<13; $a++) {    
      array_push($valores, array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0));
    } 
    
//    die( print_r($valores) );

    while ($row = mysql_fetch_object($resultado)) {
    
      $idade = str_replace('A','',calculate_age($row->dataNASC, $row->dataASSINATURA, 0));
      if (strpos($idade, 'M')!==false ) $idade=0;
  
      if ( $idade<=2 ) $x = 0;
      if ( $idade>=3 && $idade<=18 ) $x = 1;
      if ( $idade>=19 && $idade<=23 ) $x = 2;    
      if ( $idade>=24 && $idade<=28 ) $x = 3;
      if ( $idade>=29 && $idade<=33 ) $x = 4;
      if ( $idade>=34 && $idade<=38 ) $x = 5;
      if ( $idade>=39 && $idade<=43 ) $x = 6;
      if ( $idade>=44 && $idade<=48 ) $x = 7;
      if ( $idade>=49 && $idade<=53 ) $x = 8;
      if ( $idade>=54 && $idade<=58 ) $x = 9;
      if ( $idade>=59 ) $x = 10;
      
      // qtde usuarios
      $valores[$x][0]++;
      
      // genero
      if ($row->sexo=='M') $valores[$x][1]++;
      else   $valores[$x][2]++;
      
      // parentescos
      $valores[$x][ $row->parentesco+2 ]++;
      
      // vlr adesao
      $valores[$x][16] += $row->vlr1aMensalidade;
    }

    $txt = 
     array("      |Usuários|   H  |   M  |  PAI |  MAE |  FLO |  FLA |  ESO |  ESA |  IMO |  IMA |  COO |  COA |  OUT |  TIT |  TUT |VLR MEDIO|",
           " 0..2 |   9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           " 3..18|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "19..23|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "24..28|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "29..33|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "34..38|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "39..43|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "44..48|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "49..53|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "54..58|        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           "  +59 |        | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |", 
           " ",
           "TOTAL:         | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999 | 9999,99 |",
           " ",
           "Legenda: H= homem    M= mulher  FLO= filho       FLA= filha       ESO= esposo  ESA= esposa",
           "         IMO= irmão  IMA=irmã   COO=companheiro  COA=companheira  OUT=outros   TIT= titular",
           "         TUT= tutelado");

//print_r($valores);die();
    // joga valores para o texto de saida
    for ($x=0; $x<17; $x++) {    

      $linha = $txt[$x];

      // faixas etarias e suas qtdes sao preenchidas/substituidas no array $txt 
      if ($x>0 && $x<12)  {
        
        for ($y=0; $y<17; $y++) {
          
          $pos = 10 + 7*$y;
          
          // obtem valor da coluna
          $vlr = $valores[$x-1][$y];
          
          // soma ao total da coluna
          $valores[11][$y] += $vlr;
          
          // decide qual vlr mostrar  - se o valor puro....ou calcular vlr medio  
          if ($y==16)  $vlr = $valores[$x-1][16] / $valores[$x-1][0];
          else $vlr = $valores[$x-1][$y];
          
          if ($vlr==0) {
            if ($y<16) $linha = substr_replace($linha, "    ", $pos, 4);  
            else $linha = substr_replace($linha, '       ', $pos, 7);
          }
          else {
            if ($y<16) $linha = substr_replace($linha, str_pad($vlr, 4, ' ', 0), $pos, 4);  
            else $linha = substr_replace($linha, str_pad(number_format($vlr, 2, ',', ''), 7, ' ', 0), $pos, 7);          
          }
        }
      }

      // preenche/substitui linha do total
      else if ($x==13) {

        for ($y=0; $y<17; $y++) {
          
          $pos = 10 + 7*$y;
          
          // calcula vlr medio ou obtem valor da coluna
          if ($y==16)  $vlr = $valores[11][16] / $valores[11][0];
          else $vlr = $valores[11][$y];
          
          if ($vlr==0) {
            if ($y<16) $linha = substr_replace($linha, '    ', $pos, 4);  
            else $linha = substr_replace($linha, '       ', $pos, 7);
          }
          else {
            if ($y<16) $linha = substr_replace($linha, str_pad($vlr, 4, ' ', 0), $pos, 4);  
            else $linha = substr_replace($linha, str_pad(number_format($vlr, 2, ',', ''), 7, ' ', 0), $pos, 7);          
          }
        }

      
      }
      
            
      fwrite($Arq, str_pad("$linha", 140, ' ', 1)."\n"); 
    }
    
         
  }

  fclose($Arq);
}



/*****************************************************************************************/
if ($acao=='caixa') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $titREL = 'Relatorio de caixa';
  $titulos_temp="Período: $dataIniMostrar a $dataFinMostrar".
  
  $headers=
    '                                                                                                      +----------------------- Cheques ----------------------+                                                                                    |'. 
    '                                                                                                      !                                                      !           Valor     Percent            Saídas                                      |'. 		
	  'Data         Corretor/Funcionário   Tipo contrato          Operação                   CPF              Nº      Valor    Data      Banco                         Valor    recebido  AllCross   Saídas  Cheques   Cartão  Cheques     Vale  Dinheiro|'. 
    str_repeat('-', 245);
//   99/99 99:99  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxx   999999  9999,99  99/99/99  xxxxxxxxxxxxxxxxxxxxx (999)   9999,99  9999,99       999%  9999,99  9999,99  9999,99  9999,99  9999,99   9999,99 
  
  $sql  = "select numero, nome  " .
          "from representantes ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $repres=array();
  while ($row = mysql_fetcH_object($resultado)) {    
    $repres[$row->numero]=$row->nome;
  }
  
  $sql  = "select numero, nome  " .
          "from funcionarios ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $funcionarios=array();
  while ($row = mysql_fetcH_object($resultado)) {    
    $funcionarios[$row->numero]=$row->nome;
  }  

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  //$Arq = fopen("../ajax/txts/$txt", 'w');
  $Arq = fopen("../ajax/txts/tst.txt", 'w');

  $dataTRAB = $dataini;
  
  $sql  = "select cx.numreg as idOP,  date_format(cx.dataOP, '%d/%m %H:%i') as dataOPERACAO, " .
          "ifnull(plano.nome, '* erro *') as descCONTA, cx.descOPERACAO, ep.vlrADESAO,  plano.entOUsai, " .            
          "ifnull(tipoprop.descricao, '* erro *') as descTIPO_CONTRATO,  ep.idTIPO_CONTRATO, ep.percentualPRESTADORA, " .
          " ep.vlrRECEBIDO, ep.cpf, ep.valor, ep.numreg as idENTREGA, ep.idREPRESENTANTE, cx.idFUNCIONARIO, cx.idOPERACAO, cx.valor as vlrCAIXA ".
          "from caixa cx " .
          "left join entregaspropostas ep  " .
          " 	on cx.numreg = ep.idCAIXA  " .
          "left join contas plano  " .
          "	  on plano.numero = cx.idOPERACAO " .
          "left join tipos_contrato tipoprop " .
          "	  on tipoprop.numreg = ep.idTIPO_CONTRATO " .
          " where cx.dataop between '$dataTRAB 00:00:00' and '$dataTRAB 23:59:00'  and  ifnull(ep.alterada1_excluida2,0)=0  order by cx.dataOP desc ";

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

	$pagina = 0;  
  $lin = 200;
	
	$totENT_CHEQUE=0;
	$totENT_DINHEIRO=0;
	$totENT_VALE=0;

	$idCAIXA_ATUAL=-1;
  while ($row = mysql_fetcH_object($resultado)) {   
		// idENTREGA=='', significa que nao ha entrega vinculada, o registro é uma operacao de cx simples
    $data=$row->dataOPERACAO;
  	$vlrRECEBIDO = number_format($row->vlrRECEBIDO, 2, ',', '');
    $valor = number_format($row->vlrCAIXA, 2, ',', '');
    $percPRESTADORA = '-';    
    if ($row->percentualPRESTADORA>0)   $percPRESTADORA = number_format($row->percentualPRESTADORA, 0, ',', '').'%';
  
    if ($row->idENTREGA!='') {      
			$envolvido = substr($repres[$row->idREPRESENTANTE], 0, 15) . ' ('.$row->idREPRESENTANTE.')';
			$cpf = $row->cpf ;
			$contrato = "$row->descTIPO_CONTRATO ($row->idTIPO_CONTRATO)";
			$descricao= "entrega  prop";
    }
		// operacao caixa diferente de entrega proposta
		else {
			$envolvido = substr($funcionarios[$row->idFUNCIONARIO], 0,15) . ' ('.$row->idFUNCIONARIO.')';
			$cpf='-';
			$contrato = '-';
			$descricao= substr($row->descCONTA, 0, 19) . ' ('.$row->idOPERACAO.')';
			
			$info=str_pad($data, 11, ' ', 1) .'  ' .
						str_pad($envolvido, 21, ' ', 1) .'  ' .
						str_pad($contrato, 21, ' ', 1) .'  ' .		
						str_pad($descricao, 25, ' ', 1) .'  ' .
						str_pad($cpf, 14, ' ', 1) . '  '.
						str_pad($strPAG, 56, ' ', 1) . '  '.
						str_pad('', 7, ' ', 0).'   '.
						str_pad('', 7, ' ', 0).'     '.
						str_pad($percPRESTADORA, 4, ' ', 0) . '   ';
						
			if ($row->entOUsai=='S')  {
				$info .= str_pad($valor, 7, ' ', 0);
			}	
			else	 {
				$info .= str_pad('', 50, ' ', 0) . str_pad($valor, 7, ' ', 0);			
			}	
			$info .= "\n";

			if ($lin + 1 > 55)   cabecalho();
			fwrite($Arq, $info);
			$lin++;   		

			continue;
    }

		// trecho a seguir aplica se a operacoes de caixa que sao entregas de propostas
    $sql = "select pg.numREG, pg.tipoPGTO, pg.idBANCO, pg.idOPERADORA, pg.idREPRESENTANTE, cheque, ".
           "date_format(pg.dataCHEQUE, '%d/%m/%y') as dataCHEQUE, pg.valor, ". 
           " ifnull(ban.nome, '* ERRO *') as nomeBANCO, ifnull(opera.nome, '* ERRO *') as nomeOPERADORA, ".
           " ifnull(repre.nome, '* ERRO *') as nomeREPRE ".
           "from pagamentos pg ".
           "left join representantes repre ".
           "    on repre.numero=pg.idREPRESENTANTE ".
           "left join operadoras opera " .
           "	  on opera.numreg = pg.idOPERADORA " .
           "left join bancos ban " .
           "	  on ban.numero = pg.idBANCO " .
           "where idCAIXA=$row->idOP";

    $pags = mysql_query($sql) or die (mysql_error());
    $strPAG=' ';  
		
		$valorPAGO = 0;
		$valorPAGO_2 = number_format(0, 2, ',', '')  ; 
		
    while ($pag = mysql_fetcH_object($pags) )  {
			$tipo=$pag->tipoPGTO;
			
			$valorPAGO = $pag->valor;
			$valorPAGO_2 = number_format($pag->valor, 2, ',', '')  ; 

      if ($tipo=='CHEQUE') {
        $strPAG = str_pad($pag->cheque, 6, ' ', 1) .'  ' .
  								str_pad($valorPAGO_2, 7, ' ', 0) .'  ' .
									str_pad($pag->dataCHEQUE, 8, ' ', 0) .'  ' .
	  							str_pad(substr($pag->nomeBANCO, 0,24) . "($pag->idBANCO)", 28, ' ', 1) ;
      }
    }
        
		// se nenhum tipo pgto especificado, sistema parte principio que foi pago em especie
		if ( mysql_num_rows($pags)<1 )  $tipo='DINHEIRO';
		
		if ($idCAIXA_ATUAL!=$row->idOP) $idCAIXA_ATUAL=$row->idOP;
		else $strPAG='';

		// se pgto= cheque, mostra entreg de proposta de forma especifica
		if ($tipo=='CHEQUE') $infoTIPO='Cheques';
		if ($tipo=='DINHEIRO') $infoTIPO='Dinheiro';		
		if ($tipo=='VALE') $infoTIPO='Adiantamento Prop.(vale)';				
		
		$info=str_pad($data, 11, ' ', 1) .'  ' .
					str_pad($envolvido, 21, ' ', 1) .'  ' .
					str_pad($contrato, 21, ' ', 1) .'  ' .		
					str_pad($infoTIPO, 25, ' ', 1) .'  ' .
					str_pad($cpf, 14, ' ', 1) . '  '.
					str_pad($strPAG, 56, ' ', 1) . '  '.
					str_pad($valor, 7, ' ', 0).'   '.
					str_pad($vlrRECEBIDO, 7, ' ', 0).'     '.
					str_pad($percPRESTADORA, 4, ' ', 0) .  '    '.
					str_pad('', 26, ' ', 0);

		// se pgto em dinheiro, posiciona na coluna dinheiro, senao, na coluna cheques				
		if ($tipo=='DINHEIRO')  {
			$aMAIS=str_pad('', 19, ' ', 0);
			$totENT_DINHEIRO += $row->vlrRECEBIDO * ($row->percentualPRESTADORA/100);
			$valorPAGO_2 = number_format($row->vlrRECEBIDO * ($row->percentualPRESTADORA/100), 2, ',', '')  ; 
			//$totENT_DINHEIRO += $valorPAGO;
		} 
		else if ($tipo=='CHEQUE')  {
			$aMAIS='';
			//$totENT_CHEQUE += $row->vlrRECEBIDO * ($row->percentualPRESTADORA/100);
			$totENT_CHEQUE += $valorPAGO;
		}
		else if ($tipo=='VALE')  {
			$aMAIS=str_pad('', 9, ' ', 0);
			//$totENT_VALE += $row->vlrRECEBIDO * ($row->percentualPRESTADORA/100);
			$totENT_VALE += $valorPAGO;
		}		

		$info .= $aMAIS . str_pad( $valorPAGO_2, 7, ' ', 0) ;
		$info .= "\n";

		if ($lin + 1 > 55)   cabecalho();
		fwrite($Arq, $info);
    $lin++;   		
		
		// se pgto= cheque e ha um vlr de adesao digitado, cria mais uma linha para especificar adesao
		if ( ($tipo=='CHEQUE' || $tipo=='DINHEIRO') && $row->vlrADESAO>0) {
			$info=str_pad($data, 11, ' ', 1) .'  ' .
						str_pad($envolvido, 21, ' ', 1) .'  ' .
						str_pad($contrato, 21, ' ', 1) .'  ' .		
						str_pad("Adesão $infoTIPO", 25, ' ', 1) .'  ' .
						str_pad('', 14, ' ', 1) . '  '.
						str_pad('', 56, ' ', 1) . '  '.
						str_pad('-', 7, ' ', 0).'   '.
						str_pad('-', 7, ' ', 0).'     '.
						str_pad('-', 4, ' ', 0) . '    '.
						str_pad('', 26, ' ', 0) ;

			if ($tipo=='DINHEIRO')  {
				$aMAIS=str_pad('', 19, ' ', 0); 
				$totENT_DINHEIRO += $row->vlrADESAO;
			} else {
				$aMAIS='';
				$totENT_CHEQUE += $row->vlrADESAO;
			}
			$info .= $aMAIS . str_pad( number_format($row->vlrADESAO, 2, ',', ''), 7, ' ', 0) ;
						
			$info .= "\n";

			if ($lin + 1 > 55)   cabecalho();
 			fwrite($Arq, $info);
	    $lin++;   
		}		
		
		
/*
'                                                                                                      +----------------------- Cheques ----------------------+                                                                                    |'. 
'                                                                                                      !                                                      !           Valor     Percent            Saídas                                      |'. 		
'Data         Corretor/Funcionário   Tipo contrato          Operação                   CPF              Nº      Valor    Data      Banco                         Valor    recebido  AllCross   Saídas  Cheques   Cartão  Cheques     Vale  Dinheiro|'. 
 99/99 99:99  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxx (999)  xxxxxxxxxxxxxxxxxxxxxxxxx  xxxxxxxxxxxxxx   999999  9999,99  99/99/99  xxxxxxxxxxxxxxxxxxxxx (999)   9999,99  9999,99       999%  9999,99  9999,99  9999,99  9999,99  9999,99   9999,99 

*/                    
      $lin++;
  }
  
  fclose($Arq);
}




/*****************************************************************************************/
IF ($acao=='removeENVIO') {
  mysql_query("delete from ultimo_envio_clinipam where proposta=$vlr") or die(mysql_error());  
}

/*****************************************************************************************/
IF ($acao=='novoEnvioClinipam') {
  $vlr2 = $_REQUEST['vlr2'];
  mysql_query("insert into ultimo_envio_clinipam(proposta, contratante) select '$vlr', '$vlr2';") or die(mysql_error());  
}
   
/*****************************************************************************************/
IF ($acao=='lerUltimoEnvioClinipam') {
  $sql  = "select proposta, contratante ".
          "from ultimo_envio_clinipam  order by numreg ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $resp='';
  while ($row = mysql_fetcH_object($resultado)) {
    if ($resp!='') $resp.='|'; 
    $resp .= "$row->proposta;$row->contratante";
  }
  if ($resp=='') $resp='nada';
   
}




/*****************************************************************************************/
if ($acao=='bordero3') {
  
  $headers = 
    "           Nº da     Data de     Data de                                     Plano   1ª          Data da  Débito|".
    "Seqüência  proposta  assinatura  vencimento  Usuário                         optado  mens. (R$)  entrega  automático|".
    str_repeat('-', 120);      

  $gerarTXT = $_REQUEST['gerarTXT'];
  $propostas = $_REQUEST['prop'];  

  $titulos = "";   
  $titREL = 'BORDERÔ DE REMESSA DE CONTRATO';
    
  
  $sql = "select usu.numreg as idUSUARIO, lst.sequencia, lst.numCONTRATO, prop.cpfContratante as cpfContratante, date_format(lst.dataASSINATURA, '%d/%m/%Y') as dataASSINATURA, usu.cpf as cpfUSUARIO, ". 
         "date_format(prop.data1aMens, '%d/%m/%y') as data1aMens, usu.nome, usu.plano, usu.vlr1aMensalidade, ".
         "date_format(usu.dataNasc, '%d/%m/%Y') as dataNascimento, " .          
         "date_format(ent.data, '%d/%m/%y') as dataENTREGA ," .
         "ifnull(debitoAUTOMATICO, 0) as debitoAUTOMATICO , date_format(lst.dataCadastro, '%d/%m/%Y') as dataCadastro, ".
         "usu.cpf, usu.parentesco, prop.email, prop.endereco, prop.municipio, prop.bairro, prop.cep, ifnull(prop.end_numero, '') as end_numero, ".
         " prop.numREPRESENTANTE, prop.uf , ".
         " ifnull(parent.codigo, 'O') as codPARENTESCO, date_format(prop.datacancelamento, '%d/%m/%Y') as datacancelamento, usu.sexo, ".
         "  date_format(prop.DataNascContratante, '%d/%m/%Y') as DataNascContratante, ".
         " lst.contratante, prop.SexoContratante, ifnull(prop.foneres, '') as foneres, ifnull(prop.fonecom, '') as fonecom, " .
         " ifnull(prop.fonecel, '') as fonecel, prop.operadoraANTERIOR  " .                           
         "from listadepropostas lst ".
         "inner join propostas  prop ".
         "		on prop.sequencia = lst.sequencia ".
         "inner join representantes repre ".
         "		on repre.numero = lst.numREPRESENTANTE ".
         "inner join usuarios usu ".
         "		on usu.sequencia = lst.sequencia ".
         "inner join propostasentregues propentr ".
         "		on propentr.numPROP = lst.numCONTRATO ".
         "inner join entregaspropostas ent ".
         "		on ent.numero = propentr.numENT ".
         "left join parentescos parent ".
         "		on parent.numero = usu.parentesco ".
         "where lst.numCONTRATO in ($propostas)   and  ifnull(ent.alterada1_excluida2,0)=0   " .
         "order by sequencia, parent.titular " ;
         
  
//  $sql = str_replace('@criterioREPRE', $criterio, $sql);
  //  die($sql);    

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($resultado)==0) die('nada'); 

  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $Arq = fopen("../ajax/txts/$txt", 'w');
  
  if ($gerarTXT=='true') {
    $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);

    mysql_query("insert into transferencias(data, idOPERADOR) select now(), $logado[1];", $conexao) or die(mysql_error());
  
    $nomeARQ = str_pad(mysql_insert_id(), 7, '0', 0) . '.txt';
    $ArqTXT = fopen("../ajax/transfere/$nomeARQ", 'w');

    mysql_query("update transferencias set nomearq='$nomeARQ' where numreg=".mysql_insert_id()) or die(mysql_error());

    fwrite($ArqTXT, date('d/m/Y') . "\n");
  }

  $pagina = 0;
  
  $lin = 87;
  $seqATUAL = -1;
  
  $totCONTRATOS=0;
  $totUSUARIOS=0;
  while ($row = mysql_fetch_object($resultado)) {  

    $cpfContratante=str_replace('.', '' , $row->cpfContratante);     $cpfContratante=str_replace('-', '' , $cpfContratante);
    $cpf=str_replace('.', '' , $row->cpf);     $cpf=str_replace('-', '' , $cpf);

    $totUSUARIOS++;
    
    if ($lin + 1 > 55)   cabecalho();
      
    $mostrarTUDO = false;
    if ($seqATUAL != $row->sequencia )   {
      $mostrarTUDO = true;
      $seqATUAL = $row->sequencia;
      
      $totCONTRATOS++;      
    }  
      
    
//    $totCONVENIO += $row->valorEXAME;
     
    $vlr1a = number_format($row->vlr1aMensalidade, 2, ',', '')  ;
    
    if ($mostrarTUDO)
     fwrite($Arq, str_pad($row->sequencia, 10, ' ', 1) .' ' .  
                   str_pad($row->numCONTRATO, 9, ' ', 1) .' ' .
                   str_pad($row->dataASSINATURA, 10, ' ', 1) .'  ' .
                   str_pad($row->data1aMens, 10, ' ', 1) .'  ' .
                   str_pad(substr($row->nome, 0, 29), 30, ' ', 1) .'  ' .
                   str_pad($row->plano, 6, ' ', 1) .'  ' .
                   str_pad($vlr1a, 8, ' ', 0) .'   ' .
                   str_pad($row->dataENTREGA, 10, ' ', 1)  . '  ' .
                   ($row->debitoAUTOMATICO ? 'SIM' : '-').  "\n");
    else
     fwrite($Arq, str_pad('', 45, ' ', 1)  .  
                   str_pad(substr($row->nome,0,29), 30, ' ', 1) .'  ' .
                   str_pad($row->plano, 6, ' ', 1) .'  ' .
                   str_pad($vlr1a, 8, ' ', 0) .'   ' .
                   str_pad($row->dataENTREGA, 10, ' ', 1)  ."\n");


    $lin++;
    
    // TXT de transferencia
    if ($gerarTXT!='true') continue;

    //$codTITULAR = ($row->sequencia!=$codTITULAR) ? $row->sequencia : $codTITULAR;

    // ao encontrar o titular da proposta, avalia se ele é titular e contratante= sua categoria= T 
    // ou se ele é somente titular= sua categoria= D e gera um registro do contratante como sendo categoria= F    

    // marca como enviada para CLINIPAM
    mysql_query("update listadepropostas set enviadaCLINIPAM=1, dataEnvioClinipam=now() where sequencia=$row->sequencia", $conexao) or die (mysql_error());
    
    if ($row->codPARENTESCO=='X') {

      if ( ($row->cpfUSUARIO==$row->cpfContratante) && ($row->cpfUSUARIO!='' && $row->cpfContratante!='') && 
           ($row->dataNascimento==$row->DataNascContratante) && ($row->dataNascimento!='' && $row->DataNascContratante!='') ) {  
        $codTITULAR = $row->idUSUARIO;

        fwrite($ArqTXT, 'I' .  
                       str_pad($row->idUSUARIO, 16, ' ', 1) .
                       str_pad($row->nome, 60, ' ', 1) .
                      ($row->dataNascimento=='' ? '          ' : $row->dataNascimento) .
                      ($row->dataASSINATURA=='' ? '          ' : $row->dataASSINATURA) .
                       str_pad('T', 1, ' ', 1) .
                       str_pad($cpf, 14, ' ', 1) .
                       str_pad('       ', 14, ' ', 1) .
                       $row->codPARENTESCO .
                      str_pad($row->sexo, 1, ' ', 1) . 
                      ($row->datacancelamento=='' ? '          ' : $row->datacancelamento) .
                      str_pad($codTITULAR, 16, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat('0', 11).
                      str_pad($row->email, 60, ' ', 1) .
                      str_pad($row->endereco, 60, ' ', 1) .
                      strtoupper( str_pad($row->bairro, 60, ' ', 1) ) .
                      str_pad($row->municipio, 60, ' ', 1) .
                      str_pad($row->uf, 2, ' ', 1) .
                      str_pad($row->cep, 8, '0', 0) .
                      str_pad($row->foneres, 14, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat(' ', 2).
                      str_repeat(' ', 4).
                      str_pad("310-$row->plano", 6, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 10).
                      str_repeat(' ', 1).
                      str_repeat(' ', 10).
                      str_repeat(' ', 15).
                      str_pad($row->end_numero, 10, ' ', 1) .
                      str_repeat(' ', 5).
                      '1'.
                      str_repeat(' ', 15).
                      str_pad('3', 15, ' ', 1) .
                      str_repeat(' ', 87).                                            
                      str_pad($row->numCONTRATO, 30, ' ', 1) .                       
                      str_pad($row->fonecom, 13, ' ', 1) .
                      str_pad($row->fonecel, 13, ' ', 1) . 
                      str_pad($row->end_complemento, 60, ' ', 1) . 
                      str_pad($row->operadoraANTERIOR, 4, ' ', 1) . "\n");                      

      }    
      else        {
        // gera registro do titular financeiro (contratante)
        $codTITULAR = $row->sequencia;

        fwrite($ArqTXT, 'I' .  
                       str_pad($row->sequencia, 16, ' ', 1) .
                       str_pad($row->contratante, 60, ' ', 1) .                        
                      ($row->DataNascContratante=='' ? '          ' : $row->DataNascContratante) .
                      ($row->dataASSINATURA=='' ? '          ' : $row->dataASSINATURA) .
                       str_pad('F', 1, ' ', 1) .
                       str_pad($cpfContratante, 14, ' ', 1) . 
                       str_pad('       ', 14, ' ', 1) .
                       'X'.
                      str_pad($row->SexoContratante, 1, ' ', 1) .  
                      ($row->datacancelamento=='' ? '          ' : $row->datacancelamento) .
                      str_pad($codTITULAR, 16, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat('0', 11).
                      str_pad($row->email, 60, ' ', 1) .
                      str_pad($row->endereco, 60, ' ', 1) .
                      strtoupper( str_pad($row->bairro, 60, ' ', 1) ) .
                      str_pad($row->municipio, 60, ' ', 1) .
                      str_pad($row->uf, 2, ' ', 1) .
                      str_pad($row->cep, 8, '0', 0) .
                      str_pad($row->foneres, 14, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat(' ', 2).
                      str_repeat(' ', 4).
                      str_pad("310-$row->plano", 6, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 10).
                      str_repeat(' ', 1).
                      str_repeat(' ', 10).
                      str_repeat(' ', 15).
                      str_pad($row->end_numero, 10, ' ', 1) .
                      str_repeat(' ', 5).
                      '1'.
                      str_repeat(' ', 15).
                      str_pad('3', 15, ' ', 1) .
                      str_repeat(' ', 87).                                            
                      str_pad($row->numCONTRATO, 30, ' ', 1) .                       
                      str_pad($row->fonecom, 13, ' ', 1) .
                      str_pad($row->fonecel, 13, ' ', 1) . 
                      str_pad($row->end_complemento, 60, ' ', 1) . 
                      str_pad($row->operadoraANTERIOR, 4, ' ', 1) . "\n");                      

        // gera registro informando que o titular sua categoria= D
        fwrite($ArqTXT, 'I' .  
                       str_pad($row->idUSUARIO, 16, ' ', 1) .
                       str_pad($row->nome, 60, ' ', 1).                       
                      ($row->dataNascimento=='' ? '          ' : $row->dataNascimento) .
                      ($row->dataASSINATURA=='' ? '          ' : $row->dataASSINATURA) .
                       str_pad('D', 1, ' ', 1) .
                       str_pad($cpf, 14, ' ', 1) .
                       str_pad('       ', 14, ' ', 1) .
                       $row->codPARENTESCO .
                      str_pad($row->sexo, 1, ' ', 1) . 
                      ($row->datacancelamento=='' ? '          ' : $row->datacancelamento) .
                      str_pad($codTITULAR, 16, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat('0', 11).
                      str_pad($row->email, 60, ' ', 1) .
                      str_pad($row->endereco, 60, ' ', 1) .
                      strtoupper( str_pad($row->bairro, 60, ' ', 1) ) .
                      str_pad($row->municipio, 60, ' ', 1) .
                      str_pad($row->uf, 2, ' ', 1) .
                      str_pad($row->cep, 8, '0', 0) .
                      str_pad($row->foneres, 14, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 60).
                      str_repeat(' ', 2).
                      str_repeat(' ', 4). 
                      str_pad("310-$row->plano", 6, ' ', 1) .
                      str_repeat(' ', 10).
                      str_repeat(' ', 10).
                      str_repeat(' ', 1).
                      str_repeat(' ', 10).
                      str_repeat(' ', 15).
                      str_pad($row->end_numero, 10, ' ', 1) .
                      str_repeat(' ', 5) .
                      '1'.
                      str_repeat(' ', 15).
                      str_pad('3', 15, ' ', 1) .
                      str_repeat(' ', 87).                                            
                      str_pad($row->numCONTRATO, 30, ' ', 1) .                       
                      str_pad($row->fonecom, 13, ' ', 1) .
                      str_pad($row->fonecel, 13, ' ', 1) . 
                      str_pad($row->end_complemento, 60, ' ', 1) . 
                      str_pad($row->operadoraANTERIOR, 4, ' ', 1) . "\n");                      
      }                      

    
    }
    // se parentesco <> 12, nao é titular, simplesmente gera o registro do usuario
    else 
      fwrite($ArqTXT, 'I' .  
                     str_pad($row->idUSUARIO, 16, ' ', 1) .
                     str_pad($row->nome, 60, ' ', 1).
                    ($row->dataNascimento=='' ? '          ' : $row->dataNascimento) .
                    ($row->dataASSINATURA=='' ? '          ' : $row->dataASSINATURA) .
                     str_pad('D', 1, ' ', 1) .
                     str_pad($cpf, 14, ' ', 1) .
                     str_pad('       ', 14, ' ', 1) .
                     $row->codPARENTESCO .
                    str_pad($row->sexo, 1, ' ', 1) . 
                    ($row->datacancelamento=='' ? '          ' : $row->datacancelamento) .
                    str_pad($codTITULAR, 16, ' ', 1) .
                    str_repeat(' ', 10).
                    str_repeat(' ', 60).
                    str_repeat('0', 11).
                    str_pad($row->email, 60, ' ', 1) .
                    str_pad($row->endereco, 60, ' ', 1) .
                    strtoupper( str_pad($row->bairro, 60, ' ', 1) ) .
                    str_pad($row->municipio, 60, ' ', 1) .
                    str_pad($row->uf, 2, ' ', 1) .
                    str_pad($row->cep, 8, '0', 0) .
                    str_pad($row->foneres, 14, ' ', 1) .
                    str_repeat(' ', 10).
                    str_repeat(' ', 60).
                    str_repeat(' ', 2).
                    str_repeat(' ', 4).
                    str_pad("310-$row->plano", 6, ' ', 1) .
                    str_repeat(' ', 10).
                    str_repeat(' ', 10).
                    ' ' .
                    str_repeat(' ', 10).
                    str_repeat(' ', 15).
                    str_pad($row->end_numero, 10, ' ', 1) .
                    str_repeat(' ', 5).
                    '1'.
                    str_repeat(' ', 15).
                    str_pad('3', 15, ' ', 1) .
                    str_repeat(' ', 87).                                            
                    str_pad($row->numCONTRATO, 30, ' ', 1) .                       
                    str_pad($row->fonecom, 13, ' ', 1) .
                    str_pad($row->fonecel, 13, ' ', 1) . 
                    str_pad($row->end_complemento, 60, ' ', 1) . 
                      str_pad($row->operadoraANTERIOR, 4, ' ', 1) . "\n");                    
  }
  if ($gerarTXT=='true') fclose($ArqTXT);
    
  $obs = explode(';', $_REQUEST['obs']);
  if ($lin + count($obs)+1 >55)   cabecalho();
  fwrite($Arq,  " \n") ;
  for ($w=0; $w<count($obs); $w++) {
    fwrite($Arq,  "$obs[$w] \n") ;
    $lin++;  
  }
  
  if ($lin + 11 >55)   cabecalho();            
      
  fwrite($Arq,  "\n") ;
  fwrite($Arq,  "Total de contratos: $totCONTRATOS \n") ;  
  fwrite($Arq,  "Total de usuários : $totUSUARIOS \n") ;
  fwrite($Arq,  "\n") ; fwrite($Arq,  "\n") ;
  fwrite($Arq,  "-------------------------------------------------------------------------------- \n") ;  
  fwrite($Arq,  $_SESSION['empresa']. "\n") ;
  fwrite($Arq,  "\n") ; fwrite($Arq,  "\n") ;  
  fwrite($Arq,  "-------------------------------------------------------------------------------- \n") ;      
  fwrite($Arq,  "Clinipam Assistência Médica. \n") ;
  fwrite($Arq,  "\n") ; fwrite($Arq,  "\n") ;  
  fwrite($Arq,  "Recebido em: ____/____/________ ") ;  

  fclose($Arq);
}




/*****************************************************************************************/
if ($acao=='produtos') {
  $dataIniMostrar = $_REQUEST['dataIniMostrar'];
  $dataFinMostrar = $_REQUEST['dataFinMostrar'];  
  
  $dataini = $_REQUEST['DATAINI'];
  $datafin = $_REQUEST['DATAFIN'];

  $gerarXLS = $_REQUEST['xls'];

  $repre = $_REQUEST['repre'];  
  
  $titREL = 'Vendedores / produtos';
  
  $gerarLISTA = $_REQUEST['listar'];  

  $tipobusca = $_REQUEST['tipobusca'];
  $infoCMP = $tipobusca==1 ? 'Data de cadastro' : 'Data de assinatura';
  $cmpBUSCAR = $tipobusca==1 ? 'datacadastro' : 'dataassinatura';  
  
  $titulos="Período ($infoCMP): $dataIniMostrar a $dataFinMostrar";  
  
  $headers=
    '                                                    +-------------------------------  Produtos / nº de vidas ---------------------------------------+                            |'.
    '                        Qtde       Qtde   Qtde prop !                                           Mater                      Mater                    ! Pessoas                    |'.       
    'Representante           propostas  vidas  débitos    Hospitalar    Mater         Especial       Especial      Genial       Perfeito     Perfeito      protegidas  Valor     C.P. |'.    
    str_repeat('-', 190);
   //                        Qtde       Qtde   Qtde prop                                           Mater                       Mater                       Pessoas |'.       
   //Representante           propostas  vidas  débitos    Hospitalar    Mater        Especial      Especial      Genial        Perfeito      Perfeito      protegidas  Valor     C.P. |'.    
   //xxxxxxxxxxxxxxxxxxxxxx  aaaaa      bbbbb  ccccc      ddddd (999%)  eeeee (999%) fffff (999%)  ggggg (999%)  hhhhh (999%)  iiiii (999%)  jjjjj (999%)  kkkkk       lllll,ll  mmmm

  

  if ($gerarXLS=='false' ) {
    $sql = 'select rep.nome as nomeREPRE, rep.numero as numREPRE, count(*) as qtdePROPS, rep.numero as representante , '.
           'SUM( CASE '.
           '      WHEN debitoautomatico = 1 THEN 1 '.
           '       ELSE 0 '.
           '       END ) as qtdeDEB      '.
           ' from propostas prop '.
           ' inner join representantes rep '.
           ' 	on rep.numero = prop.numrepresentante     '.
           " where $cmpBUSCAR  between '$dataini' and '$datafin' @criterioREPRESENTANTE ".
           ' group by rep.nome, rep.numero '.	
           ' order by rep.nome ';
              
    if ($repre!=9999)
      $sql = str_replace('@criterioREPRESENTANTE', " and rep.numero=$repre ", $sql);
    else
      $sql = str_replace('@criterioREPRESENTANTE', "", $sql);
    
                
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
    
    if (mysql_num_rows($resultado)==0) die('nada'); 
  
    $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
    $Arq = fopen("../ajax/txts/$txt", 'w');
  
    $pagina = 0;  
    $lin = 87;
    
    $vlrtotHOSP = 0;
    $vlrtotESPECIAL = 0;
    $vlrtotMATER  = 0;
    $vlrtotGENIAL = 0;        
    $vlrtotMATPERFEITO = 0;
    $vlrtotPERFEITO = 0;        
    
    while ($row = mysql_fetch_object($resultado)) {  
      
      if ($lin + 1 > 55)  cabecalho();
            
      $sql = 'select count(*) as qtdeVIDAS, ' . 
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 2 THEN 1 ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdeHOSP, ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 2 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrHOSP, ' .           
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 4 THEN 1 ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdeESPECIAL, ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 4 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrESPECIAL, ' .          
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 5 THEN 1 ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdeMATER, ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 5 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrMATER, ' .           
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 31 THEN 1 ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdeGENIAL, ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 31 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrGENIAL, ' .           
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 39 THEN 1   ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdeMATPERFEITO,  ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 39 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrMATPERFEITO, ' .           
             'SUM( CASE  ' . 
             ' 	WHEN usu.plano = 38 THEN 1  ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as qtdePERFEITO,  ' .
             'SUM( CASE ' . 
             ' 	WHEN usu.plano = 38 THEN usu.vlrADESAO ' . 
             ' 	ELSE 0 ' . 
             ' 	END ) as vlrPERFEITO, ' .           
             ' SUM( CASE '. 
             ' 	WHEN usu.remocao>0 THEN 1 '. 
             ' ELSE 0 '. 
             ' END ) as qtdeREMOCAO, '. 
  	         ' sum(vlr1aMensalidade) as valor '.             
             'from propostas prop   ' .
             'inner join usuarios usu ' .
             '	on usu.sequencia = prop.sequencia ' .
             " where prop.numrepresentante=$row->numREPRE  "  .
             " and $cmpBUSCAR between '$dataini' and '$datafin'  "; 
                
      $prods = mysql_query($sql, $conexao) or die (mysql_error());
      $prod = mysql_fetch_object($prods);
      
  
      $lin = 'xxxxxxxxxxxxxxxxxxxxxx  aaaaa      bbbbb  ccccc      ddddd              fffff     ggggg     hhhhh   iiiii     jjjjj     kkkkk     llllllll      ';
      
      $lin = 'xxxxxxxxxxxxxxxxxxxxxx  aaaaa      bbbbb  ccccc      ddddd (ddd%)               fffff (fff%)  ggggg (ggg%)  hhhhh (hhh%)  iiiii (iii%)  jjjjj (jjj%)  kkkkk       llllllll      ';    
      
     //Representante           propostas  vidas  débitos    Hospitalar    Mater        Especial      Especial      Genial        Perfeito      Perfeito      protegidas  Valor     C.P. |'.    
     //xxxxxxxxxxxxxxxxxxxxxx  aaaaa      bbbbb  ccccc      ddddd (999%)  eeeee (999%) fffff (999%)  ggggg (999%)  hhhhh (999%)  iiiii (999%)  jjjjj (999%)  kkkkk       lllll,ll  mmmm
      
      $vlrtotHOSP += $prod->vlrHOSP;
      $vlrtotESPECIAL += $prod->vlrESPECIAL;
      $vlrtotMATER += $prod->vlrMATER;
      $vlrtotGENIAL += $prod->vlrGENIAL;        
      $vlrtotMATPERFEITO += $prod->vlrMATPERFEITO;
      $vlrtotPERFEITO += $prod->vlrPERFEITO;        
      
      $lin = str_replace('xxxxxxxxxxxxxxxxxxxxxx', str_pad(substr($row->nomeREPRE, 0, 21), 22, ' ', 1), $lin); 
      $lin = str_replace('aaaaa', str_pad($row->qtdePROPS, 5, ' ', 0), $lin);
      $lin = str_replace('ccccc', str_pad($row->qtdeDEB, 5, ' ', 0), $lin);    
      
      $valor=str_pad(number_format($prod->valor, 2, ',', ''), 8, ' ', 0);          
  
      $lin = str_replace('bbbbb', str_pad($prod->qtdeVIDAS, 5, ' ', 0), $lin);
      $lin = str_replace('ddddd', str_pad($prod->qtdeHOSP, 5, ' ', 0), $lin);
  		if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('ddd%', str_pad(intval($prod->qtdeHOSP * 100 / $prod->qtdeVIDAS).'%', 4, ' ', 0), $lin);
  		else	
  			$lin = str_replace('ddd%', str_pad('0%', 4, ' ', 0), $lin);			
          
      $lin = str_replace('fffff', str_pad($prod->qtdeESPECIAL, 5, ' ', 0), $lin);
  		if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('fff%', str_pad(intval($prod->qtdeESPECIAL * 100 / $prod->qtdeVIDAS).'%', 4, ' ', 0), $lin);                
  		else
  			$lin = str_replace('fff%', str_pad('0%',4, ' ', 0), $lin);                		
      
      $lin = str_replace('ggggg', str_pad($prod->qtdeMATER, 5, ' ', 0), $lin);
      if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('ggg%', str_pad(intval($prod->qtdeMATER * 100 / $prod->qtdeVIDAS).'%', 4, ' ', 0), $lin);    
  		else
  			$lin = str_replace('ggg%', str_pad('0%', 4, ' ', 0), $lin);                		
  			
      
      $lin = str_replace('hhhhh', str_pad($prod->qtdeGENIAL, 5, ' ', 0), $lin);
      if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('hhh%', str_pad(intval($prod->qtdeGENIAL * 100 / $prod->qtdeVIDAS).'%', 4, ' ', 0), $lin);
  		else
  			$lin = str_replace('hhh%', str_pad('0%', 4, ' ', 0), $lin);                		
  			
          
      $lin = str_replace('iiiii', str_pad($prod->qtdeMATPERFEITO, 5, ' ', 0), $lin);
      if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('iii%', str_pad(intval($prod->qtdeMATPERFEITO * 100 / $prod->qtdeVIDAS). '%', 4, ' ', 0), $lin);
  		else
  			$lin = str_replace('iii%', str_pad('0%', 4, ' ', 0), $lin);                		
          
      $lin = str_replace('jjjjj', str_pad($prod->qtdePERFEITO, 5, ' ', 0), $lin);
      if ($prod->qtdeVIDAS>0)
  			$lin = str_replace('jjj%', str_pad(intval($prod->qtdePERFEITO * 100 / $prod->qtdeVIDAS).'%', 4, ' ', 0), $lin);
  		else
  			$lin = str_replace('jjj%', str_pad('0%', 4, ' ', 0), $lin);                		
  			
                          
      $lin = str_replace('kkkkk', str_pad($prod->qtdeREMOCAO, 5, ' ', 0), $lin);    
      $lin = str_replace('llllllll', $valor, $lin);
      
      $tot1 += $row->qtdePROPS;
      $tot3 += $row->qtdeDEB;
      $tot2 += $prod->qtdeVIDAS;    
      $tot4 += $prod->qtdeHOSP;
      $tot5 += $prod->qtdeESPECIAL;
      $tot6 += $prod->qtdeMATER;
      $tot7 += $prod->qtdeGENIAL;
      $tot8 += $prod->qtdeMATPERFEITO;
      $tot9 += $prod->qtdePERFEITO;                        
      $tot10 += $prod->valor;
      $tot11 += $prod->qtdeREMOCAO;        
      
      $lin = str_replace('0 (  0%)', '   -    ', $lin);
      fwrite($Arq,  "$lin   \n");
    }  
  
    if ($lin + 3 > 55)  {
      cabecalho();
      $lin += 2;
    }  
    
    $valor=str_pad(number_format($tot10, 2, ',', ''), 8, ' ', 0);
    $lin = '                        aaaaa      bbbbb  ccccc      ddddd              fffff     ggggg     hhhhh   iiiii     jjjjj     kkkkk     llllllll      ';
  
    $lin = '                        aaaaa      bbbbb  ccccc      ddddd (ddd%)               fffff (fff%)  ggggg (ggg%)  hhhhh (hhh%)  iiiii (iii%)  jjjjj (jjj%)  kkkkk       llllllll      ';  
    $lin = str_replace('aaaaa', str_pad($tot1, 5, ' ', 0), $lin);
    $lin = str_replace('bbbbb', str_pad($tot2, 5, ' ', 0), $lin);
    $lin = str_replace('ccccc', str_pad($tot3, 5, ' ', 0), $lin);    
  
    $lin = str_replace('ddddd', str_pad($tot4, 5, ' ', 0), $lin);
    $lin = str_replace('ddd%', str_pad(intval($tot4 * 100 / $tot2).'%', 4, ' ', 0), $lin);
        
    $lin = str_replace('fffff', str_pad($tot5, 5, ' ', 0), $lin);
    $lin = str_replace('fff%', str_pad(intval($tot5 * 100 / $tot2).'%', 4, ' ', 0), $lin);
      
    $lin = str_replace('ggggg', str_pad($tot6, 5, ' ', 0), $lin);
    $lin = str_replace('ggg%', str_pad(intval($tot6 * 100 / $tot2).'%', 4, ' ', 0), $lin);
      
    $lin = str_replace('hhhhh', str_pad($tot7, 5, ' ', 0), $lin);
    $lin = str_replace('hhh%', str_pad(intval($tot7 * 100 / $tot2).'%', 4, ' ', 0), $lin);
      
    $lin = str_replace('iiiii', str_pad($tot8, 5, ' ', 0), $lin);
    $lin = str_replace('iii%', str_pad(intval($tot8 * 100 / $tot2).'%', 4, ' ', 0), $lin);  
    
    $lin = str_replace('jjjjj', str_pad($tot9, 5, ' ', 0), $lin);
    $lin = str_replace('jjj%', str_pad(intval($tot9 * 100 / $tot2).'%', 4, ' ', 0), $lin);
      
    $lin = str_replace('kkkkk', str_pad($tot11, 5, ' ', 0), $lin);  
    $lin = str_replace('llllllll', str_pad($valor, 5, ' ', 0), $lin);          
  
    $lin = str_replace('0 (  0%)', '   -    ', $lin);
    fwrite($Arq,  "   \n");          
    fwrite($Arq,  "$lin \n"); 
    fwrite($Arq,  "   \n");
    
  //    $lin = '                                                   ddddddd                    fffffff       ggggggg       hhhhhhh       iiiiiii       jjjjjjj                                   ';
      $lin = '                                                          ddddddd                fffffff       ggggggg        hhhhhhh       iiiiiii       jjjjjjj                                   ';        
      
     //Representante           propostas  vidas  débitos    Hospitalar    Mater        Especial      Especial      Genial        Perfeito      Perfeito      protegidas  Valor     C.P. |'.    
     //xxxxxxxxxxxxxxxxxxxxxx  aaaaa      bbbbb  ccccc      ddddd (999%)  eeeee (999%) fffff (999%)  ggggg (999%)  hhhhh (999%)  iiiii (999%)  jjjjj (999%)  kkkkk       lllll,ll  mmmm
      
    $lin = str_replace('ddddddd', str_pad($vlrtotHOSP==0 ? '-' : number_format($vlrtotHOSP, 2, ',', ''), 7, ' ', 0), $lin);
    $lin = str_replace('fffffff', str_pad($vlrtotESPECIAL==0 ? '-' : number_format($vlrtotESPECIAL, 2, ',', ''), 7, ' ', 0), $lin);
    $lin = str_replace('ggggggg', str_pad($vlrtotMATER==0 ? '-' : number_format($vlrtotMATER, 2, ',', ''), 7, ' ', 0), $lin);
    $lin = str_replace('hhhhhhh', str_pad($vlrtotGENIAL==0 ? '-' : number_format($vlrtotGENIAL, 2, ',', ''), 7, ' ', 0), $lin);
    $lin = str_replace('iiiiiii', str_pad($vlrtotMATPERFEITO==0 ? '-' : number_format($vlrtotMATPEREFEITO, 2, ',', ''), 7, ' ', 0), $lin);
    $lin = str_replace('jjjjjjj', str_pad($vlrtotPERFEITO==0 ? '-' : number_format($vlrtotPERFEITO, 2, ',', ''), 7, ' ', 0), $lin);
    
    fwrite($Arq,  "$lin   \n");
  }  

  if ($gerarLISTA=='true') {
    if ($gerarXLS=='true') {
      $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".xls";
      $Arq = fopen("../ajax/txts/$txt", 'w');

      fwrite($Arq, '<html><table><tr>'.
                   '<td>Representante</td>'.
                   '<td>Proposta</td>'.
                   '<td>Data entrega</td>'.
                   '<td>Data cadastro</td>'.
                   '<td>Data assinatura</td>'.
                   '<td>Qtde vidas</td>'.
                   '<td>Vlr 1a mens</td>'.
                   '</tr>');          
    }
   
    $headers=
      "                                          Data      Data      Data       |".
      "Representante                   Proposta  entrega   cadastro  Assinatura |".
      str_repeat('-', 80);
  //   xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  999999    99/99/99  99/99/99  99/99/99  
  
    $sql = "select rep.numero as idREPRE, rep.nome as nomeREPRE, pe.numprop, ".   			
           "date_format(ep.data, '%d/%m/%y') as dataENTREGA, date_format(lst.datacadastro, '%d/%m/%y') as dataCADASTRO,  ".
           "  date_format(lst.dataassinatura, '%d/%m/%y') as dataASSINATURA, prop.qtdeusuarios, prop.vlr1aMens " .
           "from listadepropostas lst ".
           "inner join propostasentregues pe ".  		
           "	on pe.numprop = lst.numcontrato  ".
           "inner join propostas prop ".  		
           "	on prop.sequencia=lst.sequencia ".  
           "inner join entregaspropostas ep ".  		
           "on ep.numero = pe.nument ".    
           "inner join representantes rep   		".
           "   on rep.numero = ep.numrepresentante ".
           " where lst.$cmpBUSCAR  between '$dataini' and '$datafin' and  ifnull(ep.alterada1_excluida2,0)=0 ".           
           " order by rep.nome, pe.numprop ";
           
//           die($sql);
   	  
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
    $tot=0;
    if (mysql_num_rows($resultado)>0) {
      $lin = 87;
      while ($row = mysql_fetch_object($resultado)) {

        $tot++;
  
        if ($gerarXLS=='true') {
          fwrite($Arq, '<tr>'.
                       "<td>$row->nomeREPRE</td>".
                       "<td>$row->numprop</td>".
                       "<td>$row->dataENTREGA</td>".
                       "<td>$row->dataCADASTRO</td>".
                       "<td>$row->dataASSINATURA</td>".
                       "<td>$row->qtdeusuarios</td>".
                       "<td align=right>$row->vlr1aMens</td>".
                       '</tr>');
          }
          else {            
            if ($lin + 2 > 55)   cabecalho();
            fwrite($Arq, str_pad(substr($row->nomeREPRE, 0, 29), 30, ' ', 1) .'  ' .
                         str_pad($row->numprop, 6, ' ', 1) .'    ' .
                         str_pad($row->dataENTREGA, 10, ' ', 1)  . '' .
                         str_pad($row->dataCADASTRO, 10, ' ', 1)  . '  ' .
                         str_pad($row->dataASSINATURA, 10, ' ', 1) .  "\n");
      
            $lin++;
         }
      }
    }
    if ($gerarXLS!='true') {
      if ($lin + 2 > 55)   cabecalho();
      
      fwrite($Arq, str_repeat('-', 80)."\n");    
      fwrite($Arq, 'TOTAL DE PROPOSTAS: '.$tot);
    }
    else  {
      fwrite($Arq, '<tr>'.
                   "<td colspan=7>&nbsp;</td>".
                   "<td align=left colspan=6>TOTAL DE PROPOSTA: $tot</td>".
                   '</tr></table>');
    }

    
  }

  fclose($Arq);

  $resp= "../ajax/txts/$txt";
}





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
    'Contrato  Situacao          |'. 
    str_repeat('-', 80);
//   9999999   xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxXXXXXXXXXXXXXXXXXXXXXXXXXXXxxxxxxxxxxxxxxxxX              
  
// recebido 
// entregue  
// cancelado na entrega, motivo:
// cancelado após cadastro, motivo:  

  // se é pra listar somente props recebidas ou todas as propostas
  // isola as propostas recebidas porque na tabela recebimentopropostas 
  // infelizmente ele nao isola (registro a registro) - isso foi um erro de projeto pode se dizer
  if ($buscar==1 || $buscar==5) { 
    mysql_query("create temporary table trab (proposta int, data date, numrepresentante smallint, priprop int); ", $conexao) or die (mysql_error());
     
    // props recebidas
    $sql = "select priprop, ultprop, date_format(data, '%Y%m%d') as data, rp.numrepresentante ". 
           "from propostasrecebidas pr  ". 
           "inner join recebimentospropostas rp ".
           "	on pr.numrec = rp.numero ".
           "where rp.data between '$dataini'	and '$datafin' @criterioREPRE and ifnull(rp.alterada1_excluida2,0)=0 ".
           "group by pr.priprop, pr.ultprop, rp.data, rp.numrepresentante order by priprop ";

    $sql = str_replace('@criterioREPRE',  ( $repre!='9999' ? " and rp.numrepresentante=$repre" : '' ), $sql);

    $rsRECEB = mysql_query($sql, $conexao) or die (mysql_error());
    
    $props = '';
    
    while ($regRECEB = mysql_fetch_object($rsRECEB)) {
      
      if ($regRECEB->priprop==$regRECEB->ultprop) {
        $props .= ($props=='') ? '' : ', ';
        $props = "($regRECEB->priprop, '$regRECEB->data', $regRECEB->numrepresentante, $regRECEB->priprop)";
      }
      else {
//        $props='';
        for ($prop = $regRECEB->priprop; $prop<=$regRECEB->ultprop; $prop++)  {
          $props .= ($props=='') ? '' : ', ';
          $props .= "($prop, '$regRECEB->data', $regRECEB->numrepresentante, $regRECEB->priprop)";
        }
      }

      if (strlen($props)>=100) {
         $sqlInsercao='insert into trab values ' . $props;
         mysql_query($sqlInsercao, $conexao) or die (mysql_error()."ERRO 1<br><br>$sqlInsercao");
         
         $props = '';
      }  
    }

    if ($props!='') {
      $sqlInsercao='insert into trab values ' . $props;
      mysql_query($sqlInsercao, $conexao) or die (mysql_error()."ERRO 2<br><br>$sqlInsercao");
    }  
  

    mysql_free_result($rsRECEB);

  
    
    // exclui das recebidas, propostas que ja foram entregues ou cadastradas
    mysql_query('delete from trab where proposta in ( select numprop from propostasentregues )', $conexao) 
        or die (mysql_error());
    mysql_query('delete from trab where proposta in ( select numcontrato from listadepropostas )', $conexao) 
        or die (mysql_error());
  }     
            
  $sql = 'select numero,nome from representantes @criterio order by nome';
  $sql = str_replace('@criterio',  ( $repre!='9999' ? " where numero=$repre" : '' ), $sql);
    
//die($sql);
  $rsREPRE = mysql_query($sql, $conexao) or die (mysql_error());
  
  if (mysql_num_rows($rsREPRE)==0) die('nadaREPRESENTANTE'); 

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
             "where ifnull(idcancel, '')='' and ep.data between '$dataini'	and '$datafin' and  ifnull(ep.alterada1_excluida2,0)=0 ".
             "and ep.numrepresentante=$regREPRE->numero";
             
    else if ($buscar==3)     // cadastrados               
      $sql = "select numcontrato as vlr, ". 
             "	concat('Cadastrada em ',  date_format(lst.datacadastro, '%d/%m/%y')) as situacao ". 
             "from listadepropostas lst ".
             "where ifnull(lst.idcancelamento, '')='' and lst.datacadastro between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero";             

    else if ($buscar==4)     // canceladas               
      $sql = "select numprop as vlr, ". 
             "	concat('Cancelada na entrega em ',  date_format(data, '%d/%m/%y'), ', motivo: ', mot.descricao, '                       ') as situacao " . 
             "from propostasentregues pe " .
             "inner join entregaspropostas ep " .
             "		on ep.numero=pe.nument " .
             "left join motivos_cancelamento mot " .
             "		on mot.numero=pe.idcancel " .		
             "where ifnull(idcancel, '')<>'' and ep.data between '$dataini'	and '$datafin' " .
            "  and  ifnull(ep.alterada1_excluida2,0)=0 ".
             "and ep.numrepresentante=$regREPRE->numero " .             
             "union " .
             "select lst.numcontrato as vlr, ". 
             "	concat('Cancelada após cadastro em ',  ".
              "     date_format(prop.datacancelamento, '%d/%m/%y'), ', motivo: ', mot.descricao, ', Refeita em: ',ifnull(listapropREFEITAS.numCONTRATO,'????')) as situacao ". 
             "from listadepropostas lst ".
             "inner join propostas prop ".
             "		on prop.sequencia=lst.sequencia ".
             "left join propostas propREFEITAS ".
             "		on propREFEITAS.refeitaDaProposta = lst.numCONTRATO ".
             "left join listadepropostas listapropREFEITAS ".
             "		on listapropREFEITAS.sequencia = propREFEITAS.sequencia ".
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
            "  and  ifnull(ep.alterada1_excluida2,0)=0 ".
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
            "  and  ifnull(ep.alterada1_excluida2,0)=0 ".
             "and lst.numrepresentante=$regREPRE->numero";
             
    else if ($buscar==7)     // cadastrados mas pendentes               
      $sql = "select numcontrato as vlr, ". 
             "	concat('Cadastrada em ',  date_format(lst.datacadastro, '%d/%m/%y'), ' mas pendente') as situacao ". 
             "from listadepropostas lst ".
             "where lst.datacadastro between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero and ifnull(pendente,0)=1 and ifnull(lst.idCANCELAMENTO,0)=0 and ifnull(enviadaCLINIPAM,0)=0 ";
             
    else if ($buscar==6)     // cadastrados e nao enviada               
      $sql = "select numcontrato as vlr, ". 
             "	concat('Cadastrada em ',  date_format(lst.datacadastro, '%d/%m/%y'), ', não cancelada e não enviada') as situacao ". 
             "from listadepropostas lst ".
             "where lst.datacadastro between '$dataini'	and '$datafin' ".
             "and lst.numrepresentante=$regREPRE->numero and ifnull(lst.idCANCELAMENTO,0)=0 and ifnull(enviadaCLINIPAM,0)=0 ";             
                          
                          

    $sql .= " order by vlr ";
    
    //die($sql);
 

    $rsPROPS = mysql_query($sql, $conexao) or die (mysql_error());
    
    if ( mysql_num_rows($rsPROPS)<1 ) continue;
    

    $titulos = str_replace('@repre', "$regREPRE->nome ($regREPRE->numero)", $titulos_temp);
    cabecalho();
     
    
    $tot1=0; $tot2=0;
    while ($regPROP = mysql_fetch_object($rsPROPS)) { 
      if ($lin + 1 > 55)   cabecalho();
      
      // olha o xunxo terrivel.........teriamos que pegar o campo modeloPROPOSTA, mas como estou sem tempo,
      // vou considerar que propostas numeracao abaixo de 100.000 sao odonto
      // xunxo feito em 09/03/2012 14:32
      // nao preciso me preocupar tanto com esse xunxo, porque a chance proposta odontopam chegar em 100 mil... nao sei..
      if ( $regPROP->vlr < 100000 )  $tot2++;
      else  $tot1++;
 
      $nada=false;
      fwrite($Arq, str_pad($regPROP->vlr, 8, ' ', 0) .'  ' .  
                    "$regPROP->situacao\n");
                    
      $lin++;
    }
    mysql_free_result($rsPROPS);
    if ($lin + 3 > 55)   cabecalho();
    fwrite($Arq, "\n");
    fwrite($Arq, "Total Medicina: $tot1\n");
    fwrite($Arq, "Total Odontologia: $tot2\n");
  }

  
  mysql_query("drop table trab", $conexao);
  mysql_free_result($rsREPRE);
  
  fclose($Arq);
  
  if ($nada) die('nadaFINAL');
}


  
/*****************************************************************************************/
IF ($acao=='verPROP') {
  $sql  = "select ifnull(pendente,0) as pendente, ifnull(idcancelamento,0) as idCANCEL, contratante, ifnull(enviadaCLINIPAM,0) as enviadaCLINIPAM ".
          "from listadepropostas lst ".
          "where numCONTRATO=$vlr ";  
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $row = mysql_fetcH_object($resultado);
  if ( mysql_num_rows($resultado)==0 ) $resp='erro;Proposta não cadastrada';
  else {
    $resp=$row->contratante;
    
    if ($row->idCANCEL<>0) $resp='erro;Proposta cancelada';
    else if ($row->pendente==1) $resp='erro;Proposta com pendência';
    else if ($row->enviadaCLINIPAM<>0) $resp="pergunta;$row->contratante";
  }      
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
          "Nº da                                Parc.  Data de     Pago /comissao|".          
          "proposta  Contratante                       Venc.       (R$)           Situaçäo|".
         str_repeat('-', 80);
  else
    $headers = 
      "Nº da                                Parc.  Data de     Pago /comissao|".          
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
  
//die($sql);
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
                  str_pad(number_format($row->valorPagoParcela, 2, ',', ''), 7, ' ', 1) .'/' .
                  str_pad($comissao, 6, ' ', 0) .'   ' .
                  str_pad($row->descSITUACAO, 8, ' ', 1) ."\n");
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
