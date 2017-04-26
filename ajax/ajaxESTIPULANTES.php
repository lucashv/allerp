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

/*****************************************************************************************/
IF ($acao=='futuras') {
  
  $empresa = $_REQUEST['vlr'];     // ddmmyyyy
  
  $sql  = "select ordem, date_format(vencimento, '%d/%m/%y') as vencimento, " . 
          "valor, ". 
          "case situacao   " .
          "when '1' then concat('Pago em ', date_format(datapgto, '%d/%m/%y'))  " .
          "when '2' then 'Em aberto'   " .
          "when '3' then concat('Não localizada em ', date_format(datasituacao, '%d/%m/%y'))  " . 
          "when '4' then concat('Cancelada em ', date_format(datasituacao, '%d/%m/%y'))  " .
          "else ''   " .
          "end as situacao, date_format(datapgto, '%d/%m/%y') as datapgto,  " .
          "valorpago " .        
          "from pj_futuras where idempresa=$empresa ".
          "order by ordem " ;
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.2;
  $largura3 = $_SESSION['largIFRAME'] * 0.2;  
  $largura4 = $_SESSION['largIFRAME'] * 0.4;
  $largura5 = $_SESSION['largIFRAME'] * 0.1;    
    
	$header = "$largura1 px,Parcela|$largura2 px,Vencimento|$largura3 px,Valor|$largura4 px,Situação|$largura5 px,Pagamento";
   
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
    } else {    
      $largura1='';$largura2='';$largura3=''; $largura4=''; $largura5='';
    }
    $i++;
  
    $vlr = number_format($row->valor, 2, ',', '');
    $vlrPAGO='';
    if ($row->valorpago>0) $vlrPAGO = number_format($row->valorpago, 2, ',', '');
    
    
    $lin = "<tr onmouseover=\"this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';\" " . 
            "onmouseout=\"this.style.backgroundColor='#E6E8EE';\"   >" . 
            "<td align=\"left\" $largura1>&nbsp;$row->ordem</td>".
            "<td align=\"center\" $largura2>$row->vencimento</td>".
            "<td align=\"right\" $largura3>$vlr&nbsp;&nbsp;&nbsp;</td>".
            "<td align=\"left\" $largura4>$row->situacao</td>".
            "<td align=\"right\" $largura5>$vlrPAGO</td>".                        
            "</tr>";
            
    $resp = $resp . ($lin);
  }
}



/*****************************************************************************************/
if ($acao=='resumo') {
  $id = $_REQUEST['empresa'] ;

  $rstEMP = mysql_query("select nome, date_format(vigencia, '%Y%m%d') as vigencia from pj where numero=$id", $conexao) or die (mysql_error());
  $regEMP = mysql_fetcH_object($rstEMP);

//  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".xls";
  $txt = "$regEMP->nome.xls";
  $arqSAIDA = fopen("../ajax/planilhasPJ/$txt", 'w');
  
  if (trim($regEMP->vigencia)=='') die('Data de vigência do contrato em branco');

  $dataVIGENCIA =  $regEMP->vigencia;
  mysql_free_result($rstEMP);

  $sql='select pl.plano, pl.numero as idPLANO, fx.faixainicial, fx.faixafinal, vl.preco ' . 
      'from pj_planos pl ' .
      'left join pj_faixas_etarias fx ' .
      "	on fx.idEMPRESA=$id " .
      'left join pj_precos vl '.
      '	on vl.idEMPRESA=fx.idEMPRESA and '. 
      '		 vl.idPLANO=pl.numREG and vl.idFAIXAETARIA=fx.numREG ';
  

  $planos = mysql_query($sql, $conexao) or die (mysql_error());
       
  $info = mysql_query("select date_format(datanasc, '%Y%m%d') as datanasc, tipodependente, nome, 1 as num, plano, ".
                "case tipodependente " .
                "when 7 then 'TIT'  " .
                "else 'DEPEND'   " .
                "end as tipo, ".
                "plano from pj_usuarios where idempresa=$id", $conexao) or die (mysql_error());
  fwrite($arqSAIDA, '<table>');

  fwrite($arqSAIDA, '<tr style="color:black;font-size:14px;font-weight:bold;background-color:#00FF00;"><td>Nº</td><td>T/D</td><td>NOME</td><td>PLANO</td><td>IDADE</td><td>VALOR</td></tr>');

  $cont=0;
  $soma=0;
  while ($row = mysql_fetcH_object($info)) {
    
    $idade = calculate_age($row->datanasc, $dataVIGENCIA, 0);
    
    $cont++;

    $precoMOSTRAR='  -  ';
    mysql_data_seek($planos, 0);
    while ($plano=mysql_fetcH_object($planos)) {
//die( " $row->plano        $plano->idPLANO          $idade      $plano->faixainicial        $plano->faixafinal ");
 
        if ( strpos($idade, 'M')>-1 ) 
          $idadeCALC='0';
        else {
          $idadeCALC=str_replace(' A', '', $idade); $idadeCALC=str_replace(' M', '', $idadeCALC);
        }  

      if ( trim($row->plano)==trim($plano->idPLANO) && $idadeCALC>=$plano->faixainicial && $idadeCALC<=$plano->faixafinal) {
        $soma += $plano->preco; 
  
        $precoMOSTRAR="&nbsp;".number_format($plano->preco, 2, ',', '');  $nomePLANO=$plano->plano;   break;
      } 
    }

    $numero="$row->num";
    $idadeMOSTRAR="&nbsp; $idade";

//die($idade);

    fwrite($arqSAIDA, "<tr><td >$cont</td><td width='200px'>$row->tipo</td><td width='400px'>$row->nome</td>".
              "<td width='400px'>$nomePLANO</td><td align=right width='200px'>$idadeMOSTRAR</td><td align=right width='200px'>$precoMOSTRAR</td></tr>");
  }
 
  $soma="&nbsp;  ".number_format($soma, 2, ',', '');
  fwrite($arqSAIDA, "<tr><td colspan=5>&nbsp;</td><td>TOTAL</td></tr>");
  fwrite($arqSAIDA, "<tr><td colspan=5>&nbsp;</td><td>$soma</td></tr>");

  fwrite($arqSAIDA, '</table>');
  fwrite($arqSAIDA, '<br><br><br><br>');

  fclose($arqSAIDA);  
  mysql_free_result($info);
  mysql_free_result($planos);

  die("ok;ajax/planilhasPJ/$txt");
}


/*****************************************************************************************/
if ($acao=='editarPRECO') {
  $faixa = $_REQUEST['faixa'] ;
  $plano = $_REQUEST['plano'] ;
  $empresa = $_REQUEST['empresa'] ;
  $vlr = $_REQUEST['vlr'] ;

  $info = mysql_query("select * from pj_precos where idempresa=$empresa and idplano=$plano and idfaixaetaria=$faixa ", $conexao) or die (mysql_error());
  if ( mysql_num_rows($info)==0 )
    mysql_query("insert into pj_precos(idempresa, idplano, idfaixaetaria, preco) values($empresa, $plano, $faixa, $vlr)") or  die (mysql_error());
  else
    mysql_query("update pj_precos set preco=$vlr where idempresa=$empresa and idplano=$plano and idfaixaetaria=$faixa") or  die (mysql_error()); 
 
  mysql_free_result($info);

    
  die();
}


/*****************************************************************************************/
if ($acao=='excluirFAIXA') {
  
  mysql_query("delete from pj_faixas_etarias where numreg=$vlr") or  die (mysql_error());
    
  die();
}

/*****************************************************************************************/
if ($acao=='mudarFAIXA') {
  $fxinicial = $_REQUEST['inicial'] ;
  $fxfinal = $_REQUEST['final'] ;
  $idempresa = $_REQUEST['empresa'] ;

  if ($vlr=='')
    mysql_query("insert into pj_faixas_etarias(idempresa, faixainicial,faixafinal) values($idempresa,$fxinicial, $fxfinal)") or  die (mysql_error());
  else  
    mysql_query("update pj_faixas_etarias set faixainicial=$fxinicial, faixafinal=$fxfinal where numreg=$vlr") or  die (mysql_error());
    
  die();
}




/*****************************************************************************************/
if ($acao=='lerFAIXA') {
  
  $info = mysql_query("select faixainicial, faixafinal from pj_faixas_etarias where numreg=$vlr", $conexao) or die (mysql_error());

  $row = mysql_fetcH_object($info);
 
  $resp="$row->faixainicial|$row->faixafinal";  
  mysql_free_result($info);

  die( $resp );
}




/*****************************************************************************************/
if ($acao=='novaFAIXA') {
  $idEMPRESA = $_REQUEST['empresa'] ;
  $num = $_REQUEST['num'] ;
  $num2 = $_REQUEST['num2'];
  
  mysql_query("insert into pj_faixas_etarias(idEMPRESA, faixainicial, faixafinal) values($idEMPRESA,  $num, $num2)") or  die (mysql_error());

    
  die();
}



/*****************************************************************************************/
if ($acao=='gravarNovoPlano') {
  $nome = strtoupper( $_REQUEST['nome'] );
  $num = $_REQUEST['num'];
  
  mysql_query("insert into pj_planos(numero,plano) values($num, '$nome')") or  die (mysql_error());
    
  die();
}

/*****************************************************************************************/
if ($acao=='mudarNomePlano') {
  $nome = strtoupper( $_REQUEST['nome'] );
  $numreg = $_REQUEST['numreg'];
  $num = $_REQUEST['num'];
  
  mysql_query("update pj_planos set plano='$nome', numero=$num where numreg=$numreg") or  die (mysql_error());
    
  die();
}


/*****************************************************************************************/
if ($acao=='excluirPLANO') {
  $numreg = $_REQUEST['vlr'];
  
  mysql_query("delete from pj_planos where numreg=$numreg") or  die (mysql_error());
    
  die();
}    
    
    
/*****************************************************************************************/
if ($acao=='verDuplicidadePlano') {
  
  $numero = $_REQUEST['vlr'];     
  $operacao = $_REQUEST['op'];
  if (isset( $_REQUEST['numreg']))   $numreg = $_REQUEST['numreg'];

  $jaEXISTE='0';
  if ($operacao=='inc') {
    $sql  = 'select numero '.
            'from pj_planos '.
            "where numero=$numero";
            
    $info = mysql_query($sql, $conexao) or die (mysql_error());
    if (mysql_num_rows($info)>0)  $jaEXISTE='1';
  } 
  else {
     $sql = 'select numero, numreg '.
            'from pj_planos '.
            "where numero=$numero ";
            
    $info = mysql_query($sql, $conexao) or die (mysql_error());
    while ($row = mysql_fetcH_object($info)) {
      if ($row->numreg!=$numreg) {$jaEXISTE='1';break;}
    }
  } 
  mysql_free_result($info);

  //die( $jaEXISTE );
die( $sql );
}




/*****************************************************************************************/
IF ($acao=='planos') {
  
  $idEMPRESA = $_REQUEST['vlr'];     // ddmmyyyy

  // le faixas etarias da empresa para montar titulo da table
  $sql  = 'select faixainicial, faixafinal '.
          'from pj_faixas_etarias '.
          "where idEMPRESA=$idEMPRESA";
            
  $faixas = mysql_query($sql, $conexao) or die (mysql_error());
  $qtdeFAIXAS=mysql_num_rows($faixas);

  $larguras=array('100','200');  // 30%= tamanho para coluna NUMERO, NOME PLANO

	$header = "<tr style='background-color: #333C83;color:white;font-size:12px;FONT-WEIGHT: bold;'><td width='100px'>Nº</td><td width='200px'>Plano</td>";

  $i=3;
  while ($row = mysql_fetcH_object($faixas)) {  
    // restante, 380 dividido pela qtde colunas para faixas etarias
    $larguras=array_pad( $larguras, $i, intval((380/$qtdeFAIXAS)));
    $j=$i-1; 
  	$header .= "<td align=right width='$larguras[$j]px'>$row->faixainicial..$row->faixafinal</td>";
    $i++;
  }

  mysql_free_result($faixas);

  // le valores dos plano/faixas etarias da empresa 
  $sql  = 'select pl.plano, vl.preco, pl.numreg, numero, fx.numreg as idFAIXA ' . 
          'from pj_planos pl ' .
          'left join pj_faixas_etarias fx ' .
          " 	on fx.idEMPRESA=$idEMPRESA " .
          'left join pj_precos vl ' .
          ' 	on vl.idEMPRESA=fx.idEMPRESA and ' . 
          '	  	 vl.idPLANO=pl.numREG and vl.idFAIXAETARIA=fx.numREG '.
          ' order by plano, faixainicial';
            
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

//  $resp = tabelaPADRAO('width="680px"   ', $header );
//  $resp .= '</table>|<table width="680px" id="tabPLANOS2" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  $resp = "<table width='720px' id='tabPLANOS2' cellpadding='3'  cellspacing='0' style='font-family:verdana;font-size:10px;color:black;'>$header ";
  
  
  $i=2;
  $planoATUAL='none';
  $contlin=0;
  while ($row = mysql_fetcH_object($resultado)) {

    if ($planoATUAL!=$row->plano) {
      if ($i>2) $lin .= '</tr>';
    
      $contlin++;

      $lin = "<tr onmousedown=\"Selecionar(this.id, null, 2);\" id=\"pla_$row->numreg\" " . 
            "onmouseover=\"this.style.cursor='default';MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">". 
              "<td align=\"left\" width='80px'>&nbsp;$row->numero</td>".
              "<td align=\"left\" width='160px' >&nbsp;$row->plano</td>";

      $planoATUAL=$row->plano;
      $i=2;
    } else     $lin='';

    //if ($i < $qtdeFAIXAS) {
      $vlr = $row->preco>0 ? number_format($row->preco, 2, ',', '') : '-';

      $j=$i-1;
      $largura=$larguras[$i]-10;  
      $lin .= "<td align=\"right\" width='$largura px' id='$row->numreg".'_'."$row->idFAIXA' onmousedown='selecionaVLR(this.id);'>&nbsp;$vlr</td>";
      $i++;
//    }
            
    $resp .= ($lin);
  }


}



 
/*****************************************************************************************/
if ($acao=='excluirREG') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("delete from pj where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[32];
  
  // datas  
  $cmps[17]= $cmps[17]=='null' ? 'null' : "'$cmps[17]'";    
  $cmps[19]= $cmps[19]=='null' ? 'null' : "'$cmps[19]'";
  $cmps[20]= $cmps[20]=='null' ? 'null' : "'$cmps[20]'";
  $cmps[29]= $cmps[29]=='' ? 'null' : $cmps[29];

  if ($id=='') 
    $sql = 'insert into pj(nome, cnpj, inscricaoEstadual, endereco, bairro, cep, municipio, uf, endereco2,  bairro2, cep2, municipio2, uf2,  telefone, fax, email, '. 
            'responsavel, DataNascResponsavel, contatoRH, DataNascContatoRH, vigencia, dataVencimento, numFuncionarios, totalDependentesOptantes, '. 
            'compraCarencia, abatimentoNivel, tabelaValores, cartaLiberalidade, risco, idREPRESENTANTE, ramoATIVIDADE, observacoes, numclinipam)  ' .
            " values('$cmps[0]', '$cmps[1]', '$cmps[2]', '$cmps[3]', '$cmps[4]', '$cmps[5]', '$cmps[6]', ".
            "       '$cmps[7]', '$cmps[8]', '$cmps[9]', '$cmps[10]', '$cmps[11]', '$cmps[12]', '$cmps[13]', '$cmps[14]', ". 
            "       '$cmps[15]', '$cmps[16]', $cmps[17], '$cmps[18]', $cmps[19], $cmps[20], '$cmps[21]', '$cmps[22]', ".
            "       '$cmps[23]', '$cmps[24]', '$cmps[25]', '$cmps[26]', '$cmps[27]', '$cmps[28]', $cmps[29], '$cmps[30]', ".
            "       '$cmps[31]', '$cmps[33]' ) ";
  
  else  
    $sql = "update pj set nome='$cmps[0]', cnpj='$cmps[1]', inscricaoEstadual='$cmps[2]', endereco='$cmps[3]', bairro='$cmps[4]', cep='$cmps[5]', ".
            " municipio='$cmps[6]', uf='$cmps[7]', endereco2='$cmps[8]',  bairro2='$cmps[9]', cep2='$cmps[10]', municipio2='$cmps[11]', uf2='$cmps[12]',  ".
            " telefone='$cmps[13]', fax='$cmps[14]', email='$cmps[15]', responsavel='$cmps[16]', DataNascResponsavel=$cmps[17], contatoRH='$cmps[18]', ".
            "  DataNascContatoRH=$cmps[19], vigencia=$cmps[20], dataVencimento='$cmps[21]', numFuncionarios='$cmps[22]', ".
            "totalDependentesOptantes='$cmps[23]', compraCarencia='$cmps[24]', abatimentoNivel='$cmps[25]', tabelaValores='$cmps[26]', ".
            "cartaLiberalidade='$cmps[27]', risco='$cmps[28]', idREPRESENTANTE=$cmps[29], ramoATIVIDADE='$cmps[30]', observacoes='$cmps[31]',  " .
            " numclinipam='$cmps[33]'  where numero=$id ";

  
  mysql_query($sql);
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
  
  $sql  = "select numero, nome ".
          " from pj " .
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.8;
    
	$header = "$largura1 px,Número|$largura2 px,Nome";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
    } else {    
      $largura1='';$largura2='';
    }
    $i++;
  
    $lin = "<tr ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "</tr>";
            

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('estipulante.txt', 'r');
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
      break;      
    case 'editarREG':
      $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  if ($acao!='incluirREG')   {
    $sql  = "select pj.nome, cnpj, inscricaoEstadual, endereco, bairro, municipio, uf, cep, endereco2, bairro2, municipio2, uf2, cep2, " .
            " telefone, fax, email, responsavel, DATE_FORMAT(DataNascResponsavel, '%d/%m/%Y') as DataNascResponsavel, contatoRH, ".
             " DATE_FORMAT(DataNascContatoRH, '%d/%m/%Y') as DataNascContatoRH, dataVencimento, " . 
            ' totalDependentesOptantes, compraCarencia, tabelaValores, cartaLiberalidade, abatimentoNivel, observacoes, numFuncionarios,  '.
            " idREPRESENTANTE, ramoATIVIDADE, DATE_FORMAT(vigencia, '%d/%m/%Y') as vigencia, risco, ".
            " ifnull(rep.nome, '') as nomeREPRESENTANTE, numclinipam ".
            ' from pj ' .
            ' left join representantes rep '.
            '     on rep.numero=pj.idREPRESENTANTE '.  
            " where pj.numero=$vlr ";

//die($sql);    
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('vCNPJ', $row->cnpj, $resp);
    $resp=str_replace('vINSCRICAO', $row->inscricaoEstadual, $resp);
    $resp=str_replace('vEND2', $row->endereco2, $resp);
    $resp=str_replace('vBAIRRO2', $row->bairro2, $resp);
    $resp=str_replace('vCEP2', $row->cep2, $resp);
    $resp=str_replace('vMUNICIPIO2', $row->municipio2, $resp);
    $resp=str_replace('vUF2', $row->uf2, $resp);

    $resp=str_replace('vEND', $row->endereco, $resp);
    $resp=str_replace('vBAIRRO', $row->bairro, $resp);
    $resp=str_replace('vCEP', $row->cep, $resp);
    $resp=str_replace('vMUNICIPIO', $row->municipio, $resp);
    $resp=str_replace('vUF', $row->uf, $resp);

    $resp=str_replace('vFONE', $row->telefone, $resp);
    $resp=str_replace('vFAX', $row->fax, $resp);
    $resp=str_replace('vEMAIL', $row->email, $resp);
    $resp=str_replace('vRESPONSAVEL', $row->responsavel, $resp);
    $resp=str_replace('vNASC_RESPONSAVEL', $row->DataNascResponsavel, $resp);
    $resp=str_replace('vRH', $row->contatoRH, $resp);
    $resp=str_replace('vNASC_RH', $row->DataNascContatoRH, $resp);
    $resp=str_replace('vVIGENCIA', $row->vigencia, $resp);
    $resp=str_replace('vVENCTO', $row->dataVencimento, $resp);
    $resp=str_replace('vFUNCIONARIOS', $row->numFuncionarios, $resp);
    $resp=str_replace('vOPTANTES', $row->totalDependentesOptantes, $resp);
    $resp=str_replace('vCARENCIA', $row->compraCarencia, $resp);
    $resp=str_replace('vABATIMENTO', $row->abatimentoNivel, $resp);
    $resp=str_replace('vTABELA', $row->tabelaValores, $resp);
    $resp=str_replace('vCARTA', $row->cartaLiberalidade, $resp);
    $resp=str_replace('vTABELA', $row->tabelaValores, $resp);
    $resp=str_replace('vRISCO', $row->risco, $resp);
    $resp=str_replace('vRAMO', $row->ramoATIVIDADE, $resp);                       
    $resp=str_replace('vREPRESENTANTE', $row->idREPRESENTANTE, $resp);
    $resp=str_replace('v_REPRESENTANTE', $row->nomeREPRESENTANTE, $resp);  
    $resp=str_replace('vOBSERVACOES', $row->observacoes, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);    
    $resp=str_replace('vNUMCLINIPAM', $row->numclinipam, $resp);
        
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vCNPJ', '', $resp);
    $resp=str_replace('vINSCRICAO', '', $resp);
    $resp=str_replace('vEND2', '', $resp);
    $resp=str_replace('vBAIRRO2', '', $resp);
    $resp=str_replace('vCEP2', '', $resp);
    $resp=str_replace('vMUNICIPIO2', '', $resp);
    $resp=str_replace('vUF2', '', $resp);
    $resp=str_replace('vEND', '', $resp);
    $resp=str_replace('vBAIRRO', '', $resp);
    $resp=str_replace('vCEP', '', $resp);
    $resp=str_replace('vMUNICIPIO', '', $resp);
    $resp=str_replace('vUF', '', $resp);

    $resp=str_replace('vFONE', '', $resp);
    $resp=str_replace('vFAX', '', $resp);
    $resp=str_replace('vEMAIL', '', $resp);
    $resp=str_replace('vRESPONSAVEL', '', $resp);
    $resp=str_replace('vNASC_RESPONSAVEL', '', $resp);
    $resp=str_replace('vRH', '', $resp);
    $resp=str_replace('vNASC_RH', '', $resp);
    $resp=str_replace('vVIGENCIA', '', $resp);
    $resp=str_replace('vVENCTO', '', $resp);
    $resp=str_replace('vFUNCIONARIOS', '', $resp);
    $resp=str_replace('vOPTANTES', '', $resp);
    $resp=str_replace('vCARENCIA', '', $resp);
    $resp=str_replace('vABATIMENTO', '', $resp);
    $resp=str_replace('vTABELA', '', $resp);
    $resp=str_replace('vCARTA', '', $resp);
    $resp=str_replace('vTABELA', '', $resp);
    $resp=str_replace('vRISCO', '', $resp);
    $resp=str_replace('vRAMO', '', $resp);                       
    $resp=str_replace('vREPRESENTANTE', '', $resp);
    $resp=str_replace('v_REPRESENTANTE', '', $resp);  
    $resp=str_replace('vOBSERVACOES', '', $resp);
    $resp=str_replace('@numREG', '', $resp);
    $resp=str_replace('vNUMCLINIPAM', '', $resp);
  }
}



/*****************************************************************************************/
/* calcula idade em anos, meses e dias */
/*****************************************************************************************/

function date_delta($ts_start_date, $ts_end_date) {
  $secs_in_day = 86400;

  $i_years = gmdate('Y', $ts_end_date) - gmdate('Y', $ts_start_date);
  $i_months = gmdate('m', $ts_end_date) - gmdate('m', $ts_start_date);
  $i_days = gmdate('d', $ts_end_date) - gmdate('d', $ts_start_date);
//  if ($i_days < 0)
//    $i_months--;
  if ($i_months < 0) {
    $i_years--;
    $i_months += 12;
  }
  if ($i_days < 0) {
    $i_days = gmdate('d', gmmktime(0, 0, 0,
      gmdate('m', $ts_start_date)+1,
      0,
      gmdate('Y', $ts_start_date))) -
      gmdate('d', $ts_start_date);
    $i_days += gmdate('d', $ts_end_date);
  }

  # calculate HMS delta
  $f_delta = $ts_end_date - $ts_start_date;
  $f_secs = $f_delta % $secs_in_day;
  $f_secs -= ($i_secs = $f_secs % 60);
  $i_mins = intval($f_secs/60)%60;
  $f_secs -= $i_mins * 60;
  $i_hours = intval($f_secs/3600);

  return array($i_years, $i_months, $i_days,
               $i_hours, $i_mins, $i_secs);
}

function calculate_age($s_start_date,
    $s_end_date = '',
    $b_show_all = 0) {
  $b_show_time = strlen($s_start_date > 8);
  $ts_start_date =
    mktime(substr($s_start_date, 8, 2),
      substr($s_start_date, 10, 2),
      substr($s_start_date, 12, 2),
      substr($s_start_date, 4, 2),
      substr($s_start_date, 6, 2),
      substr($s_start_date, 0, 4));
  if ($s_end_date) {
    $ts_end_date =
      mktime(substr($s_end_date, 8, 2),
        substr($s_end_date, 10, 2),
        substr($s_end_date, 12, 2),
        substr($s_end_date, 4, 2),
        substr($s_end_date, 6, 2),
        substr($s_end_date, 0, 4));
  } else {
    $ts_end_date = time();
  }

  list ($i_age_years, $i_age_months, $i_age_days,
        $i_age_hours, $i_age_mins, $i_age_secs) =
       date_delta($ts_start_date, $ts_end_date);

  # output
  $s_age = '';
  
  
  if ($i_age_years)
    //$s_age .= "$i_age_years ano".
      //(abs($i_age_years)>1?'s':'');
    $s_age = "$i_age_years A";
    
  else if ( $i_age_months ) {
  $s_age .= ($s_age?',':'').
      "$i_age_months  ".
      (abs($i_age_months)>1?'':'');

      $s_age = "$i_age_months M ";
  }



//    $s_age .= ($s_age?',':'').
//      "$i_age_months mes".
//      (abs($i_age_months)>1?'es':'');

/*  
  if ($b_show_all && $i_age_days )
    $s_age .= ($s_age?',':'').
      "$i_age_days dia".
      (abs($i_age_days)>1?'s':'');

/* nao mostra horas, min, segs
  
  if ($b_show_time && $i_age_hours)
    $s_age .= ($s_age?', ':'').
      "$i_age_hours hora".
      (abs($i_age_hours)>1?'s':'');
      
  if ($b_show_time && $i_age_mins)
    $s_age .= ($s_age?', ':'').
      "$i_age_mins minuto".
      (abs($i_age_mins)>1?'s':'');
  if ($b_show_time && $i_age_secs)
    $s_age .= ($s_age?', ':'').
      "$i_age_secs segundo".
      (abs($i_age_secs)>1?'s':'');
      
*/

      
  return $s_age;
}



/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


