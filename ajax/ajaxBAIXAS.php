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
if ($acao=='baixar') {

  $logado = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $nomearq = $_REQUEST['arq'];
  
  mysql_query("insert into baixas(data, idOPERADOR,nomearq) select now(), $logado[1], '$nomearq';", $conexao) or die(mysql_error());

  $id = mysql_insert_id();
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $arq = fopen("../ajax/txts/$txt", 'r');

  $sql = "insert into detalhes_baixa(numBAIXA, proposta, parcela, valor, dataPGTO) values ";
  $atl = '';

  while(true)       {
	  $lin = fgets($arq);
	  if ($lin == null)  break;
	  
	  $info = explode('|', $lin);
	  
    $numPROPOSTA = $info[0];
    $parcela = $info[1];
    $dataBAIXA =  $info[2];
    $vlrPAGO =   str_replace("\n", "",  $info[3]);    
    
    $sql .= (strlen($sql)<=80 ? '' : ', ');
    $sql .= " ($id, $numPROPOSTA, $parcela, $vlrPAGO, '$dataBAIXA') ";
    
  }

  mysql_query($sql) or die(mysql_error());
  
  // soma as parcelas por contrato
  $sql = "insert into detalhes_baixa(numbaixa, proposta, parcela, valor, datapgto) " .
         "select -1, max(proposta), max(parcela), sum(round(valor,2)), max(datapgto) " .
         "from detalhes_baixa " .
         "where numbaixa=$id " .
         "group by proposta, parcela, numbaixa " ;
  mysql_query($sql) or die(mysql_error());
  
  // atualiza as futuras com base em tudo que foi feito aqui pra cima
  $sql = "update futuras fut, listadepropostas lst, detalhes_baixa det " . 
         "set situacaoPARCELA=1, dataPgtoParcela=det.datapgto, valorPagoParcela=det.valor, fut.idArqBaixa=$id, " .
         "opRESPONSAVEL = $logado[1]  " .
         "where fut.ordem=det.parcela and fut.sequencia = lst.sequencia " . 
         "and lst.numCONTRATO=det.proposta and det.numbaixa=-1  ";
  mysql_query($sql) or die(mysql_error());
         
  // limpa a sujeira  
  mysql_query("delete from detalhes_baixa where numbaixa=$id;") or die(mysql_error());
  mysql_query("update detalhes_baixa set numbaixa=$id where numbaixa=-1;") or die(mysql_error());
  
  mysql_close($conexao);
  die( "ok;$id" );
}  
           
           
/*****************************************************************************************/
if ($acao=='cancelarBAIXA') {

  $sql = "update futuras fut, listadepropostas lst, detalhes_baixa det " . 
         "set fut.dataGeracaoRel=null,fut.situacaoPARCELA=null, fut.dataSITUACAOPARCELA=null, fut.dataPgtoParcela=null, fut.valorpagoParcela=null, ". 
          " fut.periodoAPURACAO=null,fut.opResponsavel=null ".
         "where fut.ordem=det.parcela and fut.sequencia = lst.sequencia " . 
         "and lst.numCONTRATO=det.proposta and det.numbaixa=$vlr  ";

  mysql_query("update futuras set dataGeracaoRel=null,situacaoPARCELA=null, dataSITUACAOPARCELA=null, dataPgtoParcela=null, valorpagoParcela=null, ". 
              " periodoAPURACAO=null,opResponsavel=null where numreg=$vlr") or  die (mysql_error());

  mysql_query($sql) or die(mysql_error());
  
  mysql_query("delete from baixas where numreg=$vlr") or die(mysql_error());
         
  die( "ok" );
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $sql  = "select DATE_FORMAT(data, '%d/%m/%y %H:%i') as dataMOSTRAR, ifnull(op.nome, '* erro *') as nomeOPERADOR," .           
          " idOPERADOR, bai.numREG, nomearq, idoperadora, oper.nome as nomeOPERADORA, qtdeERROS, qtdeBAIXAS, qtdeESTORNOS, periodoAPURACAO " .
          " from baixas bai " .
          " left join operadores op ".
          "   on op.numero = bai.idOPERADOR " .
          " left join operadoras oper ".
          "   on oper.numreg = bai.idOPERADORA " .          
          "order by data desc " .
          " limit 800 " ;

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'];
	$header = "$largura1 px,Baixas";
   
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
  
    $qtdeERROS=$row->qtdeERROS==0 ? '-' : $row->qtdeERROS;
    $qtdeBAIXAS=$row->qtdeBAIXAS==0 ? '-' : $row->qtdeBAIXAS;
    $qtdeESTORNOS=$row->qtdeESTORNOS==0 ? '-' : $row->qtdeESTORNOS;
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row->numREG\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" .
            "<td>".
            "<table>".
            "<tr><td><table><tr>". 
            " <td>Operadora:</td><td style='color:blue;width:120px' align=left >$row->nomeOPERADORA ($row->idoperadora)</td>".
            " <td>Data/hora:</td><td style='color:blue;width:120px'>$row->dataMOSTRAR</td>".
            " <td>Operador:</td><td style='color:blue;width:140px'>$row->nomeOPERADOR ($row->idOPERADOR)</td>".
            " <td>Erros:</td><td style='color:blue;width:30px'>$qtdeERROS</td>".
            " <td>Baixas:</td><td style='color:blue;width:30px'>$qtdeBAIXAS</td>".
            " <td>Estornos:</td><td style='color:blue;width:30px'>$qtdeESTORNOS</td>".
            '</tr></table></td></tr>'.
            '<tr><td><table><tr>'.
            " <td>Arquivo:</td><td style='color:blue;width:400px'>$row->nomearq</td>".
            " <td>Período de apuração:</td><td style='color:blue;width:170px'>$row->periodoAPURACAO</td>".
            "</tr></table></td></tr>".
            '</table></td></tr>';
            
    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}




/*****************************************************************************************/
IF ($acao=='verMENSALIDADES') {
  
  $sql  = "select replace(proposta,'SUL','') as proposta, parcela, valor, datapgto, sucesso " .           
          " from detalhes_baixa " .
          " where numbaixa=$vlr ".
          "order by numreg " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $arq = fopen("../ajax/txts/$txt", 'w');

  fwrite($arq, "proposta, parcela, valor, data pgto, situacao \n");
  fwrite($arq, "\n");
  
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    $sucesso = $row[4]=='1' ? 'baixa efetivada' : 'nao encontrou proposta/parcela'; 
    fwrite($arq, "$row[0], $row[1], $row[2], $row[3]          $sucesso \n");
  }
  fclose($arq);
  $resp = "ajax/txts/$txt"; 
}


/*****************************************************************************************/
IF ($acao=='verERROS') {
  
  $sql  = "select erro  " .           
          " from erros_baixa " .
          " where idCONFIRMACAO=$vlr ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $arq = fopen("../ajax/txts/$txt", 'w');

  fwrite($arq, "descricao do erro\n");
  fwrite($arq, "\n");
  
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    fwrite($arq, "$row[0] \n");
  }
  fclose($arq);
  $resp = "ajax/txts/$txt"; 
}


/*****************************************************************************************/
IF ($acao=='verESTORNOS') {
  
  $sql  = "select erro  " .           
          " from estornos_baixa " .
          " where idCONFIRMACAO=$vlr ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
  $arq = fopen("../ajax/txts/$txt", 'w');

  fwrite($arq, "descricao do estorno\n");
  fwrite($arq, "\n");
  
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    fwrite($arq, "$row[0] \n");
  }
  fclose($arq);
  $resp = "ajax/txts/$txt"; 
}






/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


