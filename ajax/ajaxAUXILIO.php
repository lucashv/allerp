<? 
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );


$oQueAuxiliar = $_REQUEST['oQueAuxiliar'];
if (isset($_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];

/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$resp = 'INEXISTENTE';


/* buscas rapidas */

if (strpos($oQueAuxiliar, 'pesquisa_')!==false) {
  switch ($oQueAuxiliar) {
    case 'pesquisa_txtCONTA':
      $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
      $idUSUARIO = $infoUSUARIO[1]; 
  
      $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
      $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
      $row = mysql_fetcH_object($resultado);
      $permissoes=$row->permissoes;

      // usuario tem acesso cx geral/todo plano de contas
      if ( strpos($permissoes, 'H')!==false  || $idUSUARIO==1) 
        $resultado = mysql_query("select numero, concat(nome, '|', entOUsai, '|', tipoENVOLVIDO) as nome  ".
                                  "from contas where numero =$vlr and ativo='S';") or die (mysql_error());
      // usuario tem acesso somente cx interno/contas cx interno
      else
        $resultado = mysql_query("select numero, concat(nome, '|', entOUsai, '|', tipoENVOLVIDO) as nome  ".
                                  "from contas where numero =$vlr and ativo='S' and tipoCAIXA='I';") or die (mysql_error());

      break;
      
//  case 'pesquisa_txtFUNCIONARIO':
//    $resultado = mysql_query("select numero, nome ".
//                              "from funcionarios where numero =$vlr and ativo='S';") or die (mysql_error());
//    break;

    case 'pesquisa_txtAGRUPADOR':
      $resultado = mysql_query("select numero, nome ".
                                "from agrupadores where numero =$vlr and ativo='S' ;") or die (mysql_error());
      break;


    case 'pesquisa_txtTIPOCLIENTE':
      $resultado = mysql_query("select numreg as numero, nome ".
                                "from seguros_tiposcliente where numreg =$vlr and ativo='S' ;") or die (mysql_error());
      break;

    case 'pesquisa_txtTIPOSINISTRO':
      $resultado = mysql_query("select numreg as numero, nome ".
                                "from seguros_tipos_sinistros where numreg =$vlr and ativo='S' ;") or die (mysql_error());
      break;


    case 'pesquisa_txtTIPOSEGURO':
      $resultado = mysql_query("select numreg as numero, nome ".
                                "from seguros_tipos where numreg =$vlr and ativo='S' ;") or die (mysql_error());
      break;

    case 'pesquisa_txtSEGURADORA':
      $resultado = mysql_query("select numreg as numero, nome ".
                                "from seguros_seguradoras where numreg =$vlr and ativo='S' ;") or die (mysql_error());
      break;

    case 'pesquisa_txtCORRETOR':
    case 'pesquisa_txtRENOVACAO_CORRETOR':
      $resultado = mysql_query("select numreg as numero, nome ".
                                "from seguros_corretores where numreg =$vlr and ativo='S' ;") or die (mysql_error());
      break;

    case 'pesquisa_txtOPERADORA':
      $resultado = mysql_query("select numreg as numero, nome from operadoras where ativo='S' and numreg=$vlr") or die (mysql_error());
      break;

    case 'pesquisa_txtOPERADOR':
      $resultado = mysql_query("select numero, nome from operadores where ativo='S' and numero=$vlr") or die (mysql_error());
      break;


    case 'pesquisa_txtATENDIMENTO_PRODUTO':
      $resultado = mysql_query("select numero, nome from produto_atendimento where ativo='S' and numero=$vlr") or die (mysql_error());
      break;



    case 'pesquisa_txtINDICACAO':
      $resultado = mysql_query("select numero, nome from origens_atendimento where ativo='S' and numero=$vlr") or die (mysql_error());
      break;

      
    case 'pesquisa_txtGRUPO':
    case 'pesquisa_txtREL_GRUPO':
      $sql="select grp.numreg as numero, concat(grp.nome, '&nbsp;<font color=grey>', ' (COMISSÃO= ', tipcom.nome, ' (', grp.idCOMISSAO, '))^',tipcom.nome,'|',grp.idCOMISSAO) as nome ".
          "from grupos_venda grp ".
          "left join tipos_comissao tipcom ".
          "   on tipcom.numreg=grp.idCOMISSAO " .
          " where grp.ativo='S' and grp.numreg=$vlr";
      $resultado = mysql_query($sql) or die (mysql_error());
      break;
      
    case 'pesquisa_txtCOMISSAO_REPRESENTANTE':
      $resultado = mysql_query("select numreg as numero, nome from tipos_comissao where ativo='S' and numreg=$vlr") or die (mysql_error());
      break;

    case 'pesquisa_txtCOMISSAO_ADESAO':
      $resultado = mysql_query("select numreg as numero, nome from tipos_comissao_adesao where ativo='S' and numreg=$vlr") or die (mysql_error());
      break;

      
    case 'pesquisa_txtCOMISSAO_PRESTADORA':
      $resultado = mysql_query("select numreg as numero, nome from tipos_comissao_prestadora where ativo='S' and numreg=$vlr") or die (mysql_error());
      break;

    case 'pesquisa_txtSITUACAO':
      $resultado = mysql_query("select numreg as numero, descricao as nome from resultados_indicacoes where ativo='S' and numreg=$vlr") or die (mysql_error());
      break;

      
  
    case 'pesquisa_txtTIPO_CONTRATO':
      if ($vlr=='9999') {
        $resultado = mysql_query("select 9999 as numero, 'TODOS' as nome ") or die (mysql_error());
        break;
      } 
      else {
        if (isset($_REQUEST['operadora']))   {
          $operadora = $_REQUEST['operadora'];
          $resultado = mysql_query("select numreg as numero, concat(descricao,'!',vlrADESAO,'!',cpf_cnpj) as nome from tipos_contrato where numreg=$vlr and ativo='S' and idOPERADORA=$operadora") or die (mysql_error());
        }
        else
          $resultado = mysql_query("select tipos_contrato.numreg as numero, concat(descricao,'!',vlrADESAO,'!',cpf_cnpj,'!',idOPERADORA,'!',".
                                  "operadoras.nome,'!',ifnull(operadoras.qtdeMENS,1)) as nome ".
                                   "from tipos_contrato ".
                                   "inner join operadoras ".
                                   "    on operadoras.numreg = tipos_contrato.idOPERADORA  ".
                                   "where tipos_contrato.numreg=$vlr and tipos_contrato.ativo='S'") or die (mysql_error());
      }
      
      break;

    case 'pesquisa_txtREPRESENTANTE':
    case 'pesquisa_txtREPRESENTANTE_OCORRENCIA':
    case 'pesquisa_txtFUNCIONARIO':
      $resultado = mysql_query("select numero, nome from representantes where ativo='S'  and numero =$vlr;") or die (mysql_error());
      break;
      
    case 'pesquisa_txtREPRESENTANTE_2':
      $resultado = mysql_query("select representantes.numero, nome, ifnull(tip.descricao, 'ERRO') as tipo from representantes ".
                              " where ativo='S'  and representantes.numero =$vlr;") or die (mysql_error());
      break;
      

    case 'pesquisa_txtREL_REPRESENTANTE':
    case 'pesquisa_txtREL_REPRESENTANTE2':
      if ($vlr=='9999') {
        $resultado = mysql_query("select 9999 as numero, 'TODOS' as nome ") or die (mysql_error());
        break;
      }
      else { 
        $resultado = mysql_query("select numero, nome from representantes where ativo='S' and numero =$vlr;") or die (mysql_error());
        break;
      }
      
    case 'pesquisa_txtMOTIVO':
      $resultado = mysql_query("select numero, descricao as nome from motivos_cancelamento where ativo='S' and numero =$vlr;") or die (mysql_error());
      break;

    case 'pesquisa_txtBANCO':
      $resultado = mysql_query("select numero, nome from bancos where numero =$vlr;") or die (mysql_error());
      break;
      
    case 'pesquisa_txtPLANO':
      $resultado = mysql_query("select numero, nome from planos where ativo='S' and numero =$vlr;") or die (mysql_error());
      break;

  }    
    
  if (mysql_num_rows($resultado)!=0) {  
    $row = mysql_fetcH_object($resultado);
    
    if ($oQueAuxiliar=='pesquisa_txtCONTA')
      $resp = str_replace('pesquisa_', '', $oQueAuxiliar) . ';' . $row->nome . '^' . $row->nivel;      
    else if ( strpos($oQueAuxiliar, 'pesquisa_txtPARENTESCO')!==false ) 
      $resp = str_replace('pesquisa_', '', $oQueAuxiliar) . ';' . $row->nome  ;
    else if ($oQueAuxiliar=='pesquisa_txtREPRESENTANTE_2')
      $resp = str_replace('pesquisa_', '', $oQueAuxiliar) . ';' . " $row->nome __________TIPO: <font color=red><b>$row->tipo</b></font>" ;          
    else    
      $resp = str_replace('pesquisa_', '', $oQueAuxiliar) . ';' . $row->nome;      

  }
  
  else     
    $resp = str_replace('pesquisa_', '', $oQueAuxiliar) . ';* ERRO *';
    
  /*****************************************************************************************/
  /* fecha conexao */
  mysql_free_result($resultado);
  mysql_close($conexao);
  
  echo $resp; die();
}



// monta table de auxilio

switch ($oQueAuxiliar) {
  case 'txtCONTA':
    $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $idUSUARIO = $infoUSUARIO[1]; 

    $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    $row = mysql_fetcH_object($resultado);
    $permissoes=$row->permissoes;

    // usuario tem acesso cx geral/todo plano de contas
    if ( strpos($permissoes, 'H')!==false || $idUSUARIO==1) 
  	   $sql = "select numero,concat(nome, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(', entOUsai, ')') as nome from contas where ativo='S' order by numero";
    // usuario tem acesso somente cx interno/contas cx interno
    else
  	   $sql = "select numero,concat(nome, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(', entOUsai, ')') as nome from contas where ativo='S' and tipoCAIXA='I' order by numero";

  	$titulo= ' Contas';
  	$cmpBUSCAR=0;
  	break;
  	
//case 'txtFUNCIONARIO':
//	$sql = "select numero,nome from funcionarios where ativo='S' order by numero";
//	$titulo= ' Funcionários';
//	$cmpBUSCAR=0;
//	break;

  case 'txtINDICACAO':
  	$sql = "select numero,nome from origens_atendimento where ativo='S' order by numero";
  	$titulo= ' Indicações';
  	$cmpBUSCAR=0;
  	break;


  case 'txtAGRUPADOR':
  	$sql = "select numero,nome from agrupadores where ativo='S'  order by numero";
  	$titulo= ' Agrupadores';
  	$cmpBUSCAR=0;
  	break;


  case 'txtREPRESENTANTE':
  case 'txtREPRESENTANTE_OCORRENCIA':
  case 'txtFUNCIONARIO':
  case 'txtREL_REPRESENTANTE':
  case 'txtREL_REPRESENTANTE2':
  	$sql = "select repre.numero, concat(nome, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=\"blue\">') as nome ".
            "from representantes repre  ".
            "where ativo='S'  " . 
            "order by repre.nome";
  	$titulo= ' Corretores/Funcionários';
  	$cmpBUSCAR=0;
  	break;
  	
  	
  case 'txtMOTIVO':
  	$sql = 'select numero, descricao as nome from motivos_cancelamento where ativo=\'S\' order by descricao';
  	$titulo= ' Motivos para cancelamento';
  	$cmpBUSCAR=0;
  	break;
  	
  case 'txtBANCO':
  	$sql = 'select numero, nome from bancos order by nome';
  	$titulo= ' Bancos';
  	$cmpBUSCAR=0;
  	break;
  	
  case 'txtMIDIA':
  	$sql = 'select numero, nome from midias where ativo=\'S\'  order by nome';
  	$titulo= ' Mídias';
  	$cmpBUSCAR=0;
  	break;
  	
  case 'txtPLANO':
  	$sql = 'select numero, nome from planos where ativo=\'S\' order by nome';
  	$titulo= ' Planos';
  	$cmpBUSCAR=0;
  	break;

  case 'txtTIPOCLIENTE':
  	$sql = "select numreg as numero, nome from seguros_tiposcliente where ativo='S' order by nome" ;
  	$titulo= ' Tipos de cliente';
  	$cmpBUSCAR=0;
  	break;

  case 'txtTIPOSINISTRO':
  	$sql = "select numreg as numero, nome from seguros_tipos_sinistros where ativo='S' order by nome" ;
  	$titulo= ' Tipos de sinistros';
  	$cmpBUSCAR=0;
  	break;

  case 'txtTIPOSEGURO':
  	$sql = "select numreg as numero, nome from seguros_tipos where ativo='S' order by nome" ;
  	$titulo= ' Tipos de seguros';
  	$cmpBUSCAR=0;
  	break;

  case 'txtSEGURADORA':
  	$sql = "select numreg as numero, nome from seguros_seguradoras where ativo='S' order by nome" ;
  	$titulo= ' Seguradoras';
  	$cmpBUSCAR=0;
  	break;

  case 'txtCORRETOR':
  case 'txtRENOVACAO_CORRETOR':
  	$sql = "select numreg as numero, nome from seguros_corretores where ativo='S' order by nome" ;
  	$titulo= ' Corretores de seguros';
  	$cmpBUSCAR=0;
  	break;
  	
  case 'txtTIPO_CONTRATO':
    if (isset($_REQUEST['operadora']))   {
      $operadora= $_REQUEST['operadora'];  
      $nomeOPERADORA= $_REQUEST['nomeOPERADORA'];
      
    	$sql = "select numreg as numero, descricao as nome from tipos_contrato where ativo='S' and idOPERADORA=$operadora order by descricao";
    	$titulo= " Tipos de contratos $nomeOPERADORA" ;      
    } else {
    	$sql = "select numreg as numero, descricao as nome from tipos_contrato where ativo='S' order by descricao";
    	$titulo= " Tipos de contratos" ;      
    }  
      
  	$cmpBUSCAR=0;
  	break;

  case 'txtCOMISSAO_REPRESENTANTE':
  	$sql = 'select numreg as numero, nome from tipos_comissao where ativo=\'S\' order by nome';
  	$titulo= ' Comissões sobre mensalidades (corretor)';
  	$cmpBUSCAR=0;
  	break;

  case 'txtCOMISSAO_ADESAO':
  	$sql = 'select numreg as numero, nome from tipos_comissao_adesao where ativo=\'S\' order by nome';
  	$titulo= ' Comissões sobre adesão (corretor)';
  	$cmpBUSCAR=0;
  	break;

  	
  case 'txtCOMISSAO_PRESTADORA':
  	$sql = 'select numreg as numero, nome from tipos_comissao_prestadora where ativo=\'S\' order by nome';
  	$titulo= ' Tipos de comissões da prestadora';
  	$cmpBUSCAR=0;
  	break;

  case 'txtSITUACAO':
  	$sql = 'select numreg as numero, descricao nome from resultados_indicacoes where ativo=\'S\' order by descricao';
  	$titulo= ' Situações no atendimento';
  	$cmpBUSCAR=0;
  	break;

  	
  	
  case 'txtOPERADORA':
  	$sql = 'select numreg as numero, nome from operadoras where ativo=\'S\' order by nome';
  	$titulo= ' Operadoras';
  	$cmpBUSCAR=0;
  	break;

  case 'txtOPERADOR':
  	$sql = 'select numero, nome from operadores where ativo=\'S\' order by nome';
  	$titulo= ' Operadores do sistema';
  	$cmpBUSCAR=0;
  	break;


  case 'txtATENDIMENTO_PRODUTO':
  	$sql = 'select numero, nome from produto_atendimento where ativo=\'S\' order by nome';
  	$titulo= ' Produtos do atendimento';
  	$cmpBUSCAR=0;
  	break;

  	
  case 'txtGRUPO':
  case 'txtREL_GRUPO':
  	$sql = 'select numreg as numero, nome from grupos_venda where ativo=\'S\' order by nome';
  	$titulo= ' Grupos de venda';
  	$cmpBUSCAR=0;
  	break;
  	
  	
  	
  	
}  	



$resp = '<table class="frmAUXILIO" border=1 width="100%" cellpadding=3 >';
   
$TITULO = '<table width="100%"><tr>' .
          '		<td style="width:90%" style="cursor: move;"><span class="lblTitJanela" id="titAUXILIO">TITULO</span></td>' .
          '		<td><span onclick="fecharAUXILIO()" style="cursor: pointer;" class="lblTitJanela">[ X ]</span></td>' .
          ' </tr></table>' ;


$resp = $resp .   
          '<tr><td> ' .
            $TITULO .
          '</td></tr>' .					
          '<tr><td height="250px">' .
          ' <div valign=top>titTABELA</div>' .
          ' <div id="divLISTA_AUXILIO" valign=top style="OVERFLOW: auto;height:95%;width:100%">LISTAGEM</div>' .
          '</td></tr> ' .
          '<tr><td>' .
          ' <input type="hidden" id="infoAUXILIO" value="' . $oQueAuxiliar . '" >' .
          '	&nbsp;<span class="lblPADRAO">Pesquisa:</span>&nbsp;&nbsp;' .
          '	<input type="text" id="txtPR2" style="width:400px;" MaxLength="30" onkeyup="PR_AUXILIO('.$cmpBUSCAR.');">' .
          '</td></tr>'; 

//$resp = $resp . (($oQueAuxiliar=='txtHONORARIOS') ? '</table><iframe width="100%" height="100%"></iframe></div>' : '</table>') ;          
$resp = $resp . '</table>' ;

$titTAB = tabelaPADRAO(' width="97%" ', '400px,Nome|100px,Nº' ) . '</table>';
$tab = '<table id="tabAUXILIO" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
$resultado = mysql_query($sql) or die (mysql_error());
$i=1;  
while ($row = mysql_fetcH_object($resultado)) {
  if ($i==1) {
    $largura1='width="400 px"';
    $largura2='width="100 px"';
  } else {    
    $largura1='';
    $largura2='';
  }
  $i++;

  if ( strpos($oQueAuxiliar, 'txtPARENTESCO')!==false )
    $nome=$row->nome;
  else
    $nome=$row->nome;  
  
	$lin = "<tr ondblclick=\"usouAUXILIO()\" onmousedown=\"Selecionar(this.id, null, 2);\" id=\"aux_$row->numero\" " . 
  "onmouseover=\"this.style.cursor='default'; " .
   "MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">".
   "<td align=\"left\" $largura1>$nome</td><td align=\"center\" $largura2>$row->numero</td></tr>"; 

	$tab = $tab . $lin;
} 
$tab = $tab . '</table>';


if (isset($titulo))   $resp = str_replace('TITULO', $titulo, $resp );
if (isset($titTAB))   $resp = str_replace('titTABELA', $titTAB, $resp );
if (isset($tab))   $resp = str_replace('LISTAGEM', $tab, $resp );


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);

echo $resp; die();

?>