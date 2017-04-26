<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

// a barra diretorios do linux e no windows é diferente
$_SESSION['barra'] = chr(47);

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );



$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];
if (isset( $_REQUEST['vlr2']))   $vlr2 = $_REQUEST['vlr2'];

if ($acao=='sairPROGRAMA') {
  session_destroy();
  die();
}

if ($acao=='salvarDimensoesIFRAME')     {
  $_SESSION['largIFRAME'] = $_REQUEST['largura'];
  $_SESSION['altIFRAME'] = $_REQUEST['altura'];
  
  echo( $_SESSION['cores'] );
  
  
  die();
}

/* se a resolucao passada via jscript é maior que 1000px (largura), indica que
devera usar arquivos HD-high resolution para barra tarefas */
if ($acao=='usarTipoIMAGEM')     {
  if ( $vlr > 1100 && $vlr2 > 770 ) 
    $_SESSION['usarTipoIMAGEM'] = '_HD';
  else  
    $_SESSION['usarTipoIMAGEM'] = '';
  
  die();
}


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$RESP = 'INEXISTENTE';


/*****************************************************************************************/
IF ($acao=='verMsgOperador') {
  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $sql  = "select ifnull(mensagem, 'none') as mensagem ".
          "from operadores where numero = $infoUSUARIO[1]  ";
   
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());

  $row = mysql_fetcH_object($resultado);
  $RESP = $row->mensagem;
}

/*****************************************************************************************/
IF ($acao=='excluirMsgOperador') {
  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $sql  = "update operadores set mensagem='none' where numero = $infoUSUARIO[1]  ";
   
  mysql_query($sql, $conexao) or die (mysql_error());
  
  mysql_close($conexao);
  die();
}




/*****************************************************************************************/
if ($acao=='mudarCORES')     {
  $idUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $sql = "update operadores set esquemaCORES = $vlr where numero = $idUSUARIO[1]";    
    
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = "erro:" . mysql_error();


  else            
    $resp = esquemaCORES( $vlr );     

 
  mysql_close($conexao);
  echo $resp; die();
}

/*****************************************************************************************/
if ($acao=='gravarComissaoEmpresa')     {
  $comissao  = explode(';', $vlr);
  
  $gravar='';
  for ($r=0; $r<count($comissao); $r++)   {
    $gravar .= str_pad($comissao[$r], 3, ' ', 0);        
  }  
  
  $sql = "update comissaoameg set comissaoSobreMensalidades = '$gravar' ";    
    
  mysql_query($sql) or die( mysql_error() );

  mysql_close($conexao);
  echo 'OK'; die();
}


/*****************************************************************************************/
if ($acao=='gravarInfoEmpresa')     {
  $info = explode(';', $vlr);
  $sql = "update info_empresa set nome= '$info[0]' ";    
    
  mysql_query($sql) or die( mysql_error() );

  mysql_close($conexao);
  echo 'OK'; die();
}

                                      



/*****************************************************************************************/
IF ($acao=='testarLOGIN') {
  $resultado = mysql_query("select nome, senha from operadores where numero=$vlr and ativo='S';", $conexao) or die (mysql_error()); 
  
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
     $RESP = $row[0] . ";" . $row[1];
  }
}

/*****************************************************************************************/
IF ($acao=='lerNomeEmpresa') {
  $resultado = mysql_query("select nome from info_empresa", $conexao) or die (mysql_error()); 
  
  $row = mysql_fetcH_object($resultado);
  
  $_SESSION['empresa']=$row->nome;
  
  $RESP =   $row->nome;
}


/*****************************************************************************************/
if ($acao=='lerComissaoEmpresa') {
  $resultado = mysql_query("select comissaoSobreMensalidades from comissaoameg", $conexao) or die (mysql_error()); 
  
  $RESP='';
  $row = mysql_fetcH_object($resultado);
  for ($r=1; $r<=9; $r++)   {
    $RESP .= $r==1 ? '' : ';'; 
    $RESP .= substr($row->comissaoSobreMensalidades , ($r-1)*3, 3);        
  }  
}

/*****************************************************************************************/
if ($acao=='lerInfoEmpresa') {
  $resultado = mysql_query("select nome from info_empresa", $conexao) or die (mysql_error()); 
  
  $row = mysql_fetcH_object($resultado);
  $RESP = $row->nome;        
}


/*****************************************************************************************/
IF ($acao=='logarUsuario') {
  $num = substr($vlr, 0,  strpos($vlr, ';') );   $senha = substr($vlr, strpos($vlr, ';')+1);
  $resultado = mysql_query("select nome, numero, senha, ifnull(esquemaCORES,0) as ".
    "esquemaCORES from operadores where numero=$num and senha='$senha' and ativo='S';", $conexao) or die (mysql_error());

  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    $_SESSION['idUSUARIO_LOGADO'] = $row[0] . ";" . $row[1];
    
    if ($senha=='123')
      mysql_query("update operadores set mensagem='Você esta usando a senha temporária 123, <br><br> ".
            " mude a sua senha o quanto antes por favor' where numero=$num ");
    else
      mysql_query("update operadores set mensagem=null where numero=$num ");  
  
    $RESP='ok';
    
    /* aciona esquema de cores baseado no esquema escolhido pelo usuario atual */
   //  esquemaCORES( $row[2] );
    esquemaCORES( 1 );
  }
}



/*****************************************************************************************/
IF ($acao=='montarMENU') {

  $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  $logado=$infoUSUARIO[1];
  
  // 1 = administrador
  if ($logado==1) {
    $RESP = '';
    $RESP .= "[null,'Arquivos',null,null,null, ";
  	$RESP .= " [null,'Operadoras',42,null,null], ";
  	$RESP .= " [null,'Tipos de contratos',43,null,null], ";	
  	$RESP .= " [null,'Grupos de venda',44,null,null], ";	
  	$RESP .= " [null,'Corretores',0,null,null], ";
    $RESP .= " [null,'Comissão sobre mensalidades',null,null,null, ";	
    $RESP .= "   [null,'Corretor',46,null,null], ";  
    $RESP .= "   [null,'Operadora',47,null,null] ], ";
    $RESP .= " [null,'Comissão sobre adesão',21,null,null], ";
  	$RESP .= " [null,'Operadores do sistema',2,null,null], ";
      $RESP .= " [null,'Plantão',null,null,null, ";
  	  $RESP .= "   [null,'Origens',29,null,null], ";
  	  $RESP .= "   [null,'Produtos',30,null,null], ";
  	  $RESP .= "   [null,'Resultados',32,null,null] ], ";


  	$RESP .= " [null,'Créditos e débitos',4,null,null], ";
//    $RESP .= " [null,'Escritórios',20,null,null], ";
  	$RESP .= " _cmSplit,  ";
  	
  	$RESP .= " [null,'Bancos',8,null,null], ";
  	$RESP .= " _cmSplit,		 ";
  	$RESP .= " [null,'Propostas',11,null,null] ] , ";
  	$RESP .= "[null,'Caixa',null,null,null, ";
  	$RESP .= " [null,'Plano de Contas',22,null,null], ";
	  $RESP .= " [null,'Agrupadores de operações de saída',19,null,null], ";
  	$RESP .= " [null,'Caixa',6,null,null] ], ";

  	$RESP .= "[null,'Seguros',null,null,null, ";
  	$RESP .= " [null,'Tipos de seguro',40,null,null], ";
	  $RESP .= " [null,'Seguradoras',41,null,null], ";
	  $RESP .= " [null,'Corretores',45,null,null], ";
  	$RESP .= " [null,'Apólices',38,null,null] ], ";

    $RESP .= "[null,'Configuração',null,null,null, ";
    $RESP .= "	[null,'Alterar senha',3,null,null], ";
    $RESP .= "	[null,'Informações da empresa',25,null,null] ], ";

    $RESP .= "[null,'Outros',null,null,null, ";
    $RESP .= "	[null,'Baixar software de comissões corretor',33,null,null], ";
    $RESP .= "	[null,'Baixar software de confirmações',37,null,null], ";
    $RESP .= "	[null,'Baixar software Foxit Reader',48,null,null] ] ";

  }
  else {
    $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $idUSUARIO = $infoUSUARIO[1]; 

    $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    $row = mysql_fetcH_object($resultado);
    $permissoes=$row->permissoes;

    $_SESSION['permissoes'] = $permissoes;

    $RESP = '';

    $RESP .= "[null,'Arquivos',null,null,null, ";

    if (strpos($permissoes, 'A')!==false) $RESP .= " [null,'Operadoras',42,null,null], "; 
    if (strpos($permissoes, 'B')!==false)	$RESP .= " [null,'Tipos de contratos',43,null,null], ";
    if (strpos($permissoes, 'C')!==false) $RESP .= " [null,'Grupos de venda',44,null,null], ";
    if (strpos($permissoes, 'D')!==false) $RESP .= " [null,'Corretores',0,null,null], ";
    if (strpos($permissoes, 'E')!==false) {
      $RESP .= " [null,'Comissão sobre mensalidades',null,null,null, ";	
      $RESP .= "   [null,'Corretor',46,null,null], ";  
      $RESP .= "   [null,'Operadora',47,null,null] ], ";
    }
    if (strpos($permissoes, 'P')!==false) {
      $RESP .= " [null,'Comissão sobre adesão',21,null,null], ";	
    }

    if (strpos($permissoes, 'K')!==false) $RESP .= " [null,'Operadores do sistema',2,null,null], ";
//  if (strpos($permissoes, 'F')!==false)	{
//    $RESP .= " [null,'Funcionários',45,null,null], ";
//    }
    if (strpos($permissoes, 'S')!==false)	{
      $RESP .= " [null,'Plantão',null,null,null, ";
  	  $RESP .= "   [null,'Origens',29,null,null], ";
  	  $RESP .= "   [null,'Produtos',30,null,null], ";
  	  $RESP .= "   [null,'Resultados',32,null,null] ], ";
    }

    if (strpos($permissoes, 'G')!==false) $RESP .= " [null,'Créditos e débitos',4,null,null], ";
//    if (strpos($permissoes, 'N')!==false) $RESP .= " [null,'Escritórios',20,null,null], ";



    if (strpos($permissoes, 'L')!==false) {
      $RESP .= " _cmSplit,  ";
      $RESP .= " [null,'Bancos',8,null,null], ";
      $RESP .= " _cmSplit,		 ";
    }

    if (strpos($permissoes, 'J')!==false || strpos($permissoes, 'U')!==false)	$RESP .= " [null,'Propostas',11,null,null] , ";

    $RESP .= " [null,'Sair',14,null,null], ";
    $RESP .= "  ], ";
  
    // cx geral/plano de contas
    if ( strpos($permissoes, 'H')!==false ) {
    	$RESP .= "[null,'Caixa',null,null,null, ";
    	$RESP .= " [null,'Plano de contas',22,null,null], ";
   	  $RESP .= " [null,'Agrupadores de operações de saída',19,null,null], ";
    	$RESP .= " [null,'Operações',6,null,null] ], ";
    }
    // cx interno
    else if ( strpos($permissoes, 'I')!==false ) { 
    	$RESP .= "[null,'Caixa',null,null,null, ";
    	$RESP .= " [null,'Operações',6,null,null] ], ";
    }

    if ( strpos($permissoes, 'X')!==false ) {
    	$RESP .= "[null,'Seguros',null,null,null, ";
    	$RESP .= " [null,'Tipos de seguros',40,null,null], ";
  	  $RESP .= " [null,'Seguradoras',41,null,null], ";
  	  $RESP .= " [null,'Corretores',45,null,null], ";

    	$RESP .= " [null,'Apólices',38,null,null] ], ";
    }

    $RESP .= "[null,'Configuração',null,null,null, ";
    $RESP .= "	[null,'Alterar senha',3,null,null] ], ";

    $RESP .= "[null,'Outros',null,null,null, ";
    $RESP .= "	[null,'Baixar software de comissões corretor',33,null,null], ";
    $RESP .= "	[null,'Baixar software de confirmações',37,null,null], ";
    $RESP .= "	[null,'Baixar software Foxit Reader',48,null,null] ] ";
}

  
}



/*****************************************************************************************/
if ($acao=='gravarSENHA')     {
  $info = explode('|', $_REQUEST['vlr']);
  
  $idUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
  
  $sql = "select ifnull(senha, '') as senha from operadores where numero = $idUSUARIO[1]";    
    
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $row = mysql_fetcH_object($resultado);
    
  if ( rtrim($row->senha) != rtrim($info[0]) ) 
    $RESP = 'incorreta';

  else {
    $sql = "update operadores set senha='$info[1]' where numero = $idUSUARIO[1]";
    
    mysql_query($sql);
  
    if (mysql_affected_rows()==-1) 
      $RESP = mysql_error();
    else            
      $RESP = "OK";
  }         
}
      


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo $RESP; die();


?>


