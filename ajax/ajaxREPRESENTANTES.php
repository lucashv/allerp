<?
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


/*****************************************************************************************/
if ($acao=='trocarCOMISSAO') {
  $antiga = $_REQUEST['antiga'];
  $nova = $_REQUEST['nova'];
  
  mysql_query("update representantes set idTIPO_COMISSAO=$nova where idTIPO_COMISSAO=$antiga;") or  die (mysql_error());
    
  $resp = mysql_affected_rows() . ' corretor(res) teve(tiveram) comissao sobre mensalidade reconfigurada';  
}    




/*****************************************************************************************/
if ($acao=='lerCOMISSAO') {

  $sql  = "select ifnull(idTIPO_COMISSAO,1) as idCOMISSAO, ifnull(comi.nome, '* SEM COMISSAO DEFINIDA *') as nomeCOMISSAO ".
          "from representantes repre ".
          "left join tipos_comissao comi ". 
          "     on comi.numreg=repre.idTIPO_COMISSAO ".    
          " where numero=$vlr  ";   
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $resp="$row->idCOMISSAO|$row->nomeCOMISSAO";
} 
/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update representantes set ativo=case ativo when 'S' then 'N' else 'S' end where numero=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  $tipo = $_REQUEST['tipo'];  
  
  $id = $cmps[3];
  $idGRUPO = trim($cmps[4]=='') ? 'null' : $cmps[4];  
  $idTIPO_COMISSAO = trim($cmps[5]=='') ? 'null' : $cmps[5];  
  $comiADESAO = trim($cmps[6]=='') ? 'null' : $cmps[6];
  $operadorPREVENDA = trim($cmps[15]=='') ? 'null' : $cmps[15];
  $nascimento = trim($cmps[16]=='null') ? 'null' : "'$cmps[16]'";
  $idBANCO = trim(trim($cmps[17])=='') ? 'null' : $cmps[17];
  
  // alguns campos precisam ser preparados para gravação
  $cpf=$cmps[2];
  $cpf = str_replace('.', '', $cpf);   $cpf = str_replace('-', '', $cpf);

  $senha = getUniqueCode(5);
  
  if ($id=='') 
    $sql = "insert into representantes(nome,fone,cpf,ativo,idGRUPO, idTIPO_COMISSAO, interno_externo, comiADESAO, ".
            " endereco,bairro,cep,municipio,uf,conta,rg,emailCORRETOR, operadorPREVENDA, senha, nascimento, idBANCO, agencia, operacao, ".
            ' num_conta, favorecido) '.  
            " values('$cmps[0]', '$cmps[1]','$cpf','S',$idGRUPO,$idTIPO_COMISSAO,$tipo,$comiADESAO, ".
            " '$cmps[7]', '$cmps[8]', '$cmps[9]', '$cmps[10]', '$cmps[11]', '$cmps[12]', '$cmps[13]', '$cmps[14]', $operadorPREVENDA, ".
            "'$senha', $nascimento, $idBANCO, '$cmps[18]', '$cmps[19]', '$cmps[20]', '$cmps[21]');";
  
  else  
    $sql = "update representantes set nome='$cmps[0]', fone='$cmps[1]', cpf='$cpf', idGRUPO=$idGRUPO, idTIPO_COMISSAO=$idTIPO_COMISSAO, ".
            " comiADESAO=$comiADESAO, interno_externo=$tipo, endereco='$cmps[7]', bairro='$cmps[8]', cep='$cmps[9]', municipio='$cmps[10]' ,".
            " nascimento=$nascimento, uf='$cmps[11]', conta='$cmps[12]', rg='$cmps[13]', emailCORRETOR='$cmps[14]', ".
            " operadorPREVENDA=$operadorPREVENDA, idBANCO=$idBANCO, agencia='$cmps[18]', operacao='$cmps[19]', ".
            " num_conta='$cmps[20]', favorecido='$cmps[21]' where numero=$id"; 
  
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
           


//fatima_lazarotto@hotmail.com


/*****************************************************************************************/
IF ($acao=='lerREGS') {
  $ativos = $_REQUEST['ativos'];
  
  $sql  = "select rep.numero, rep.nome,  rep.ativo, rep.idGRUPO, ifnull(grp.nome,'') as nomeGRUPO, ".
          "  rep.idTIPO_COMISSAO, ifnull(tipcom.nome,'') as nomeTIPO_COMISSAO, ifnull(interno_externo, 1) as interno_externo, ".
          "  rep.comiADESAO, ifnull(comiade.nome, '-') as nomecomiADESAO, emailCORRETOR, senha ".
          " from representantes rep ".
          " left join grupos_venda grp ".
          "     on grp.numreg=idGRUPO ".
          " left join tipos_comissao tipcom ".
          "     on tipcom.numreg=idTIPO_COMISSAO ".
          " left join tipos_comissao_adesao comiade ".
          "     on comiade.numreg=rep.comiADESAO ".          
          ($ativos=='S' ? " where ifnull(rep.ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(rep.ativo, '')<>'S' " : "" ) .          
          "order by rep.nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.1;
  $largura2 = $_SESSION['largIFRAME'] * 0.2;
  $largura5 = $_SESSION['largIFRAME'] * 0.1;  
  $largura3 = $_SESSION['largIFRAME'] * 0.3;  
  $largura4 = $_SESSION['largIFRAME'] * 0.3;  
    
	$header = "$largura1 px,Nº|$largura2 px,Nome|$largura5 px,Tipo|$largura2 px,Grupo de vendas|$largura2 px,Comissão sobre mensalidades|".
            "$largura2 px,Comissão sobre adesão|1%,&nbsp;|1%,&nbsp;";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
      $largura3="width=\"$largura3 px\"";
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
    } else {    
      $largura1='';$largura2=''; $largura3='';  $largura4=''; $largura5='';
    }
    $i++;
    
    $tipo=$row[7]==1 ? 'Interno' : 'Externo';
    $comiADESAO=($row[8]!='' && $row[8]!='0') ? "$row[9] ($row[8])" : '-';
    $grupoVENDAS = $row[3]=='' ? '-' : "$row[4] ($row[3])";

//    if (trim($row[6])=='') $comiREPRE='<font color=red>* usa comissão do grupo vendas *</font>'; else $comiREPRE = "$row[6] ($row[5])";
    if (trim($row[6])=='') $comiREPRE='-'; else $comiREPRE = "$row[6] ($row[5])";
 
    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>&nbsp;&nbsp;$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1]</td>".
            "<td align=\"left\" $largura5>$tipo</td>".            
            "<td align=\"left\" $largura2>$grupoVENDAS</td>".            
            "<td align=\"left\" $largura2>$comiREPRE</td>".            
            "<td align=\"left\" $largura2>$comiADESAO</td>".
            "<td style='display:none'>$row[10]</td>".
            "<td style='display:none'>$row[11]</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('representante.txt', 'r');
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
//      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarREG':
      $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
//      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  if ($acao!='incluirREG')   {
    $sql  = "select rep.numero, rep.nome, rep.cpf, rep.fone, rep.idGRUPO, ifnull(grp.nome, '') as nomeGRUPO, comiADESAO, ".
            " rep.idTIPO_COMISSAO, ifnull(tipcom.nome, '') as nomeTIPO_COMISSAO, ifnull(interno_externo, 1) as interno_externo, ".
            " tipade.nome as nomecomiADESAO, endereco, bairro, cep, municipio,  uf, emailCORRETOR, conta, rg, rep.operadorPREVENDA, ".
            " op.nome as nomeOPERADOR, rep.senha, date_format(rep.nascimento, '%d/%m/%y') as nascimento, ".
            ' idBANCO, bancos.nome as nomeBANCO, agencia, num_conta, favorecido, operacao '.
            "from representantes rep ".
            "left join grupos_venda grp ".
            "   on grp.numreg=rep.idGRUPO " .
            "left join tipos_comissao tipcom ".
            "   on tipcom.numreg=rep.idTIPO_COMISSAO " .
            "left join operadores op ".
            "   on op.numero=rep.operadorPREVENDA " .
            "left join tipos_comissao_adesao tipade ".
            "   on tipade.numreg=rep.comiADESAO " .
            "left join bancos ".
            "   on rep.idBANCO = bancos.numero " .           
            "where rep.numero=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    // formata cpf 
    $CPF = rtrim(trim($row->cpf));
    if ($CPF!='') {
      $CPF=substr_replace($CPF, '-', 9, 0);$CPF = substr_replace($CPF, '.', 6, 0);$CPF = substr_replace($CPF, '.', 3, 0);
    }
    $resp=str_replace('v_SENHA', $row->senha, $resp);

    // verifica permissão de alteração de comissões
    if (strpos($_SESSION['permissoes'], 'Z')) {
      $resp = str_replace('v_PERMISSAO', ", 'Z'", $resp);
    $resp = str_replace('v_READONLY', '', $resp);
    } else {
      $resp = str_replace('v_PERMISSAO', '', $resp);
    $resp = str_replace('v_READONLY', 'readonly', $resp);
    }

    $resp=str_replace('vCOMISSAO_ADESAO', $row->comiADESAO, $resp);
    $resp=str_replace('v_COMISSAO_ADESAO', $row->nomecomiADESAO, $resp);

    $resp=str_replace('vOPERADOR', $row->operadorPREVENDA, $resp);
    $resp=str_replace('v_OPERADOR', $row->nomeOPERADOR, $resp);

    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('vFONE', $row->fone, $resp);

    $resp=str_replace('vNASC', $row->nascimento, $resp);    

    $resp=str_replace('vRG', $row->rg, $resp);
    $resp=str_replace('vEND', $row->endereco, $resp);
    $resp=str_replace('vBAIRRO', $row->bairro, $resp);
    $resp=str_replace('vMUNICIPIO', $row->municipio, $resp);
    $resp=str_replace('vUF', $row->uf, $resp);
    $resp=str_replace('vCEP', $row->cep, $resp);
    $resp=str_replace('vEMAIL', $row->emailCORRETOR, $resp);
    $resp=str_replace('vCONTA', $row->conta, $resp);

    $resp=str_replace('vBANCO', $row->idBANCO, $resp);
    $resp=str_replace('v_BANCO', $row->nomeBANCO, $resp);
    $resp=str_replace('vAGENCIA', $row->agencia, $resp);
    $resp=str_replace('vOPERACAO', $row->operacao, $resp);
    $resp=str_replace('vNUM_CONTA', $row->num_conta, $resp);
    $resp=str_replace('vFAVORECIDO', $row->favorecido, $resp);

    
    $resp=str_replace('vGRUPO', $row->idGRUPO, $resp);
    $resp=str_replace('v_GRUPO', $row->nomeGRUPO, $resp);
    
    if ($row->interno_externo==1) { 
      $resp=str_replace('checked_1', 'checked', $resp);
      $resp=str_replace('checked_2', '', $resp);
    }
    else {
      $resp=str_replace('checked_2', 'checked', $resp);
      $resp=str_replace('checked_1', '', $resp);
    }        
    $resp=str_replace('vCOMISSAO_REPRESENTANTE', $row->idTIPO_COMISSAO, $resp);
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', $row->nomeTIPO_COMISSAO, $resp);    
    
    $resp=str_replace('vCPF', $CPF, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);
  }    
  else {
    $resp = str_replace('v_READONLY', '', $resp);
    $resp = str_replace('v_PERMISSAO', '', $resp);

    $resp=str_replace('v_SENHA', '-', $resp);

    $resp=str_replace('vCOMISSAO_ADESAO', '', $resp);
    $resp=str_replace('v_COMISSAO_ADESAO', '', $resp);

    $resp=str_replace('vOPERADOR', '', $resp);
    $resp=str_replace('v_OPERADOR', '', $resp);

    $resp=str_replace('vRG', '', $resp);
    $resp=str_replace('vEND', '', $resp);
    $resp=str_replace('vBAIRRO', '', $resp);
    $resp=str_replace('vMUNICIPIO', '', $resp);
    $resp=str_replace('vUF', '', $resp);
    $resp=str_replace('vCEP', '', $resp);
    $resp=str_replace('vEMAIL', '', $resp);
    $resp=str_replace('vCONTA', '', $resp);

    $resp=str_replace('vBANCO', '', $resp);
    $resp=str_replace('v_BANCO', '', $resp);
    $resp=str_replace('vAGENCIA', '', $resp);
    $resp=str_replace('vOPERACAO', '', $resp);
    $resp=str_replace('vNUM_CONTA', '', $resp);
    $resp=str_replace('vFAVORECIDO', '', $resp);


    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('vNASC', '', $resp);
    $resp=str_replace('vFONE', '', $resp);    
    $resp=str_replace('vCPF', '', $resp);
    $resp=str_replace('@numREG', '', $resp);
    
    $resp=str_replace('vGRUPO', '' , $resp);
    $resp=str_replace('v_GRUPO', '', $resp);

    $resp=str_replace('vCOMISSAO_REPRESENTANTE', '', $resp);
    $resp=str_replace('v_COMISSAO_REPRESENTANTE', '', $resp);
    
    $resp=str_replace('checked_1', 'checked', $resp);
    $resp=str_replace('checked_2', '', $resp);
  }
}


/*****************************************************************************************/
IF ( $acao=='email' )  {

  $email=$_REQUEST['end'];
  $nome=$_REQUEST['nome'];
  $senha=$_REQUEST['senha'];
  $numero=$_REQUEST['numero'];
  
  $resultado = mysql_query("select nome, email, responsavel from info_empresa", $conexao) 
        or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  
  $to      = $email;
  $subject = "Senha para ver comissões - $row->nome";
  $message = "<br>".
            "<b>$nome </b><br><br><br>"  .
            "Seu Nº e senha para usar o software de confirmações, créditos e débitos é:  <br>".
            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nº: <font color=blue size='+2'>$numero</font>, Senha: ".
            " <font color=blue size='+2'>$senha</font>  <br><br><br>".
            "Para baixar (download) o software de comissões, clique ". 
                '<a href="http://'.$_SERVER['SERVER_NAME'].'/comissoes.exe">&nbsp;&nbsp;AQUI&nbsp;&nbsp;</a>   <br><br><br>'.
            '<font size="+1" color=red>Procedimento</font><br><br>'.
            '<font size="+1" color=black>Se estiver usando o Internet Explorer, você verá uma tela parecida com esta<br>'.
            '<img src="http://allcross.kinghost.net/IE.JPG" /><br>'.
            'Clique em FAZER DOWNLOAD DO ARQUIVO, e na tela seguinte que irá aparecer, clique em SALVAR<br>'.
            'Salve o arquivo em alguma pasta conhecida sua.<br>'.
            'Se o Internet Explorer fizer outras perguntas do tipo: executar este programa?, escolha sempre as respostas positivas: (SIM, EXECUTAR, CONTINUAR), este arquivo não contém vírus.<br>'.            
            'Em seguida, acesse a pasta onde salvou o arquivo e execute (clicar 2x sobre) comissoes.exe<br><br><br>'.
            '<font size="+1" color=black>Se estiver usando o Firefox, você verá uma tela parecida com esta<br>'.
            '<img src="http://allcross.kinghost.net/FF.JPG" /><br>'.
            'Clique no botão DOWNLOAD, salve o arquivo em alguma pasta conhecida sua.<br>'.
            'Se o Firefox fizer outras perguntas do tipo: executar este programa?, escolha sempre as respostas positivas: (SIM, EXECUTAR, CONTINUAR), este arquivo não contém vírus.<br>'.            
            'Em seguida, acesse a pasta onde salvou o arquivo e execute (clicar 2x sobre) comissoes.exe<br><br><br>'.
            '<font color=red><b>Aviso:<br></b></font>Se ao executar o arquivo comissoes.exe (soft comissões), '.
            'você receber um aviso que há uma versão atualizada disponível, use o mesmo link acima '.
            'para baixá-lo (atualizá-lo) novamente - ou seja, não exclua esta mensagem.  <br><br><br>'.
            'Por favor, não responda este e-mail.'.
            '<br><br><br><br>'.
            'Atenciosamente.' . '<br><br><br><br><br>'.
            "<b>$row->nome".  
            "<br>" .             
            "<br><br><br>" ;                
              
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= "From: $row->responsavel <$row->email>" ;
    
  $resp = mail($to, $subject, $message, $headers);
  $resp = ($resp) ? 3 : -1;
}



/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>
