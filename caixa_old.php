<?php 
ob_start();
require("doctype.php"); 
session_start();

//echo "<pre>"; print_r($_SESSION); echo "</pre>";

list($nomeUsuario, $codUsuario) = explode(";", $_SESSION['idUSUARIO_LOGADO']);
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
<script type="text/javascript" src="js/jquery.maskedinput.js"></script>
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<script language=javascript>
Date.prototype.addDays = function(days) {
this.setDate(this.getDate()+days);
} 
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$(".real").maskMoney({symbol:"",decimal:".",thousands:"",allowNegative:true});
	});
</script>

<!-- Folha de estilos do calendário -->
<link rel="stylesheet" type="text/css" media="all" href="js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />
<link rel="stylesheet" type="text/css" media="all" href="css/jquery.fancybox.css" />
<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a função Calendar.setup, que ajuda a gerar um calendário em poucas linhas de código -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar-setup.js"></script> 


<?
$usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;

if ($usandoTelaMaior1024_768) {
?>
  <style type="text/css" xml:space="preserve">
  .cssDIV_EDICAO_ENT {
  position: absolute; top: 200px;  width: 980px; height: 400px;	
  margin-top: -360px; margin-left: -490px; display:block; z-index:3;}

  .cssDIV_EDICAO_CAIXA {
  position: absolute; top: 200px;  width: 980px; height: 400px;	
  margin-top: -300px; margin-left: -490px; display:block; z-index:3;}
  </style>
<?  
}
else  {
?>
  <style type="text/css" xml:space="preserve">
  .cssDIV_EDICAO_ENT {
  position: absolute; top: 200px;  width: 980px; height: 400px;	
  margin-top: -290px; margin-left: -490px; display:block; z-index:3;}

  .cssDIV_EDICAO_CAIXA {
  position: absolute; top: 200px;  width: 980px; height: 400px;	
  margin-top: -280px; margin-left: -490px; display:block; z-index:3;}
  </style>
<?  
}
?>

<style type="text/css" xml:space="preserve">


.cssDIV_PGTO {
position: absolute; top: 200px;  width: 780px; height: 100px;	
margin-top: -50px; margin-left: -400px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

.cssDIV_BAIXAR {
position: absolute; top: 200px;  width: 500px; height: 250px;	
margin-top: -180px; margin-left: -240px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}



</style>


</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerHOJE();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu_MAIOR">
  <li><a href="javascript:incluirREG(1);">Nova entrega de proposta &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Insert)</a></li>
  <li><a href="javascript:incluirREG(2);">Nova operação no caixa &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Ctrl+Insert)</a></li>  
  <li><a href="javascript:editarREG();"><b>Editar registro</b></a></li>
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>  
</ul>

<form id="frmCAIXA" name="frmCAIXA" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divPGTO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divRELATORIOS" class="cssDIV_ESCONDE"  >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="80%"><span class="lblTitJanela">&nbsp;&nbsp;&nbsp;Relatórios</td>
    <td align="right" onclick="fechaREL();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

  <?
    require_once( 'includes/definicoes.php'  );

    $conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
    mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

    $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $idUSUARIO = $infoUSUARIO[1]; 

    $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    $row = mysql_fetcH_object($resultado);
    $permissoes=$row->permissoes;

    // H= acesso cx geral, plano contas
    if (strpos($permissoes, 'H')!==false || $idUSUARIO==1) {
  ?>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="rel();"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Caixa de hoje</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="window.top.frames['framePRINCIPAL'].location.href='rel/caixa_agrupadores.php';";"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Movimento por agrupadores</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="window.top.frames['framePRINCIPAL'].location.href='rel/boletos.php';";"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Boletos</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="window.top.frames['framePRINCIPAL'].location.href='rel/cheques.php';";"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Cheques</span>
    </td>    
   </tr></table></td></tr>

  <?
  } 
  else {
  ?>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="rel();"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Caixa de hoje</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="window.top.frames['framePRINCIPAL'].location.href='rel/boletos.php';";"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Boletos</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="window.top.frames['framePRINCIPAL'].location.href='rel/cheques.php';";"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Cheques</span>
    </td>    
   </tr></table></td></tr>

  <?
  } 
  ?>


   
  </table>  
</div>


<div id="divPESQUISA" class="cssDIV_ESCONDE"  >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="80%"><span class="lblTitJanela">&nbsp;&nbsp;&nbsp;PESQUISAR</td>
    <td align="right" onclick="fechaPESQUISA();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(1);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">CPF/CNPJ</span>
    </td>    
   </tr></table></td></tr>
   
   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(2);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Nº VALE CRÉDITO</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(3);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Nº OPERAÇÃO DO CAIXA</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(4);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">Nº BOLETO</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(5);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">NOME OU CPF SACADO (BOLETO)</span>
    </td>    
   </tr></table></td></tr>

   
  </table>  
</div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor="red">
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >
<input id="hidESCRITORIO" type="hidden" value="" >
<input id="acessoLIMITADO" type="hidden" value="" >

<input id="forcarSELECAO" type="hidden" value="" >

<input id="dataTRAB" type="hidden" value="" />

<input id="propEDITANDO" type="hidden" value="" />
<input id="pgtoEDITANDO" type="hidden" value="" />
<input id="valeEDITANDO" type="hidden" value="" />
<input id="recarregarINC" type="hidden" value="" />

<input id="vendoEXCLUIDAS" type="hidden" value="" />

<input id="pesqPALAVRA" type="hidden" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
  <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">

  <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>

  <td width="55%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span><br>
  <table cellpadding=0 cellspacing=0><tr>
    <td><span class="lblTitJanela" style="padding-left:9px">Escritório:&nbsp;&nbsp; </span></td>
    <td height="25px"><span class="lblTitJanela" id=tdESCRITORIO style="color:blue;font-size:12px;"></span></td>
  </tr></table>
  </td>

  <td>
  <input type="text" id="txtDATATRAB" value="" 
      style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
      onchange="lerHOJE(1);lerREGS();";/> 
  </td>
	
  <?
  // H= acesso cx geral, plano contas - permite fazer auditoria, ver operacoes excluidas, alteradas
  if (strpos($permissoes, 'H')!==false || $idUSUARIO==1) {

    ?>
    	<td width="40px" title="Ver excluidas/alteradas (NAO VENDO)" align="center" onmouseout="this.style.backgroundColor='white'" 
    	onmouseover="this.style.backgroundColor='#A9B2CA'"  onclick="policia();" >
        <table bordercolor=red id=tablePOLICIA cellpadding=1 cellspacing=1 ><tr><td>
    	   <img src="images/policia.png" />
        </td></tr></table>
    	</td>
  <?
  }
  ?>

	<td title="Operaçoes pendentes (malote)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="pendentes();" >
	  <img src="images/malote.png" />
	</td>

	<td title="Muda escritório" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternarESCRITORIO();" >
	  <img src="images/alternar2.png" />
	</td>

	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
	  <img src="images/buscadata.png" />
	</td>

	<td title="Nova entrega de proposta" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="incluirREG(1);" >
	  <img src="images/novo.png" />
	</td>
 
	<td title="Nova operação no caixa" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="incluirREG(2);" >
	  <img src="images/novo2.png" />
	</td> 


	<td title="Relatórios" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="menuREL();" >
	  <img src="images/protocolo.png" />
	</td>

	<td id="btnPESQUISAR" title="Pesquisa (Ctrl+B)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="menuPSQ();" >
	  <img src="images/pesquisa.png" />
	</td>

	<td id="btnRETORNAR" title="Retornar 1 dia (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >
	  <img src="images/setaESQUERDA.png" />
	</td>
	
	<td id="btnAVANCAR" title="Avançar 1 dia (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >
	  <img src="images/setaDIREITA.png" />
	</td>    
	    
	<td style="cursor: pointer;text-align:right;"  
	  onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
	  class="lblTitJanela" >[ X ]</span>
	</td>      
      </tr></table></td></tr>

      <tr>
	<td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .55); ?> px" >
	  <div id="titTABELA">&nbsp;</div>
	  <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
	</td>
      </tr>

      <tr width="100%"><td><table  width="100%"  ><tr  >

        <td>
        <table cellspacing=0  cellpadding=0>
        <tr>        
          <td width="100px">&nbsp;</td>
          <td width="100px" align=right><span class="lblPADRAO">DINHEIRO</span></td>
          <td width="100px" align=right><span class="lblPADRAO">CHEQUE</span></td>
        </tr>
        <tr>        
          <td width="140px"><span class="lblPADRAO" id=lblTRANSPORTADO>&nbsp;Transportado:</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblTransDINHEIRO>&nbsp;</span></td>
          <td width="80px" align=right ><span class="lblPADRAO" id=lblTransCHEQUE>&nbsp;</span></td>
        </tr>
        <tr>        
          <td width="140px" ><span class="lblPADRAO">&nbsp;Entradas:</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblDINHEIRO>&nbsp;</span></td>
          <td width="80px" align=right ><span class="lblPADRAO" id=lblCHEQUE>&nbsp;</span></td>
        </tr>
        <tr>        
          <td width="140px"><span class="lblPADRAO">&nbsp;Saídas:</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblSAIDADINHEIRO >&nbsp;</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblSAIDACHEQUE >&nbsp;</span></td>
        </tr>
        <tr>        
          <td width="140px"><span class="lblPADRAO">&nbsp;Saldo:</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblSALDODINHEIRO>&nbsp;</span></td>
          <td width="80px" align=right><span class="lblPADRAO" id=lblSALDOCHEQUE>&nbsp;</span></td>
        </tr>
        
        <? if($codUsuario == 1 || $codUsuario == 2 || $codUsuario == 54) { ?>
        	<tr>
        		<td colspan="3">&nbsp;</td>
        	</tr>
        	<tr>
        		<td colspan="3"><span class="lblPADRAO">&nbsp;Alterar Valor Transportado</span></td>
        	</tr>
        	<tr>
        		<td align="right"><span class="lblPADRAO">&nbsp;Valor dinheiro:</span></td>
        		<td colspan="2">&nbsp;<input type="text" id="txtValorDinheiro" class="real" name="txtValorDinheiro" size="10" maxlength="10" /></td>
        	</tr>
        	<tr>
        		<td align="right"><span class="lblPADRAO">&nbsp;Valor cheque:</span></td>
        		<td>&nbsp;<input type="text" id="txtValorCheque" name="txtValorCheque" class="real" size="10" maxlength="10" /></td>
        		<td><input type="button" value="ALTERAR" onclick="return atualizaTranspostado();" /></td>
        	</tr>
		<? } ?>
		
        </table>
        </td>

        <td>
        <table cellspacing=0  cellpadding=0>
        <tr>        
        <td width="20px">&nbsp;</td>
        <td width="10px" ><span style="background-color:red">&nbsp;&nbsp;&nbsp;</span></td>
        <td width="80px"><span class="lblPADRAO">&nbsp;= saída</span></td>

        <td width="10px"><span style="background-color:blue">&nbsp;&nbsp;&nbsp;</span></td>
        <td width="80px"><span class="lblPADRAO">&nbsp;= entrada</span></td>

        <td width="10px"><span style="background-color:green">&nbsp;&nbsp;&nbsp;</span></td>
        <td width="80px"><span class="lblPADRAO">&nbsp;= verificar</span></td>
        </tr>
        </table>
        </td>




	<td align="right" width="120px" >

      <input id="txtFOCADO" type="text" value="" 
        style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" />
    		      
    	<span width="100px" class="lblUSUARIO" id="totREGS">&nbsp;&nbsp;<span>
    	</td>
	
      </tr></table></td></tr>
    </table>

  </td>

</tr>
</table>

</form>
<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[

/* qtde de campos text na tela de edição, necessario informar  */
var largPR = 0;
var escritorioANTERIOR='';
var propsINICIAL='';

var aCMPS;

Calendar.setup({
inputField:    "txtDATATRAB",     
ifFormat  :     "%d/%m/%Y",     
button    :    "btnDATATRAB"    
});


var ajax = new execAjax();

/*******************************************************************************/
function lerHOJE( buscarDataEscolhida )         {

if (typeof buscarDataEscolhida=='undefined')     {
  showAJAX(1);
  ajax.criar('ajax/ajaxCAIXA.php?acao=lerDataHoje', '', 0);  
  var hoje=ajax.ler();
  
  hoje = hoje.replace(/<br>/g, String.fromCharCode(13));
} 
else
  hoje = document.getElementById('txtDATATRAB').value;
   

/*var pridiaMES = '01'+ hoje.substring(2);*/

var pridiaMES = hoje;
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');

document.getElementById('dataTRAB').value = pridiaMES;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=verificada', '', 0);
showAJAX(0);

if (ajax.ler()=='semACESSO') 
  document.getElementById('acessoLIMITADO').value = 1;  
else
  document.getElementById('acessoLIMITADO').value = 0;

showAJAX(0);
return(hoje);
}

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

lCTRL = e.ctrlKey; 
lALT = e.altKey;

var lJanRegistro = document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanCHEQUE = document.getElementById('txtVLRCH');
var lJanBOLETO = document.getElementById('txtVLRBOLETO');
var lJanCARTAO = document.getElementById('txtVLRCARTAO');
var lJanDINHEIRO = document.getElementById('txtDINHEIRO');
var lJanVALE = document.getElementById('txtVLRVALE');
var lJanVALE_CREDITO = document.getElementById('txtVALE_PGTO');
var lJanCAIXA = document.getElementById('txtCONTA');
var lJanPSQ= document.getElementById('divPESQUISA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanREL= document.getElementById('divRELATORIOS').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (tecla==39 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnAVANCAR').click();
if (tecla==37 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnRETORNAR').click();
if (tecla==66 && lCTRL && ! lJanAuxilio && ! lJanRegistro) document.getElementById('btnPESQUISAR').click();

if (tecla==9 && lCTRL && ! lJanAuxilio && ! lJanCHEQUE && ! lJanBOLETO
  && ! lJanCARTAO && ! lJanVALE && (lJanRegistro || lJanCAIXA)) alternarINCLUSAO();

if  (tecla==27) {        
  if ( document.getElementById('tdESCRITORIO').innerHTML.indexOf('<select')!=-1) voltaESCRITORIO();
  else if (lJanREL)   	fechaREL();
  else if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanCHEQUE)   	{limparCmpsCheque(); fecharPGTO();}
  else if (lJanBOLETO)   	{limparCmpsBoleto(); fecharPGTO();}  
  else if (lJanCARTAO)   	{limparCmpsCartao(); fecharPGTO();}  
  else if (lJanDINHEIRO)   	{limparCmpsDinheiro(); fecharPGTO();}
  else if (lJanVALE)   	  {limparCmpsVale(); fecharPGTO();}  
  else if (lJanVALE_CREDITO)   	  {limparCmpsValeCredito(); fecharPGTO();} 
  else if (lJanRegistro)   	fecharEDICAO();
  else if (lJanPSQ)   	fechaPESQUISA();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}  
}  

if (lJanRegistro) {
  if  ( lALT && (tecla>=49 && tecla<=54) ) {
    e.stopPropagation();e.preventDefault();
  }
  if  (tecla==49 && lALT)   	addPGTO_VALE_CREDITO();  
  if  (tecla==50 && lALT)   	addPGTO_CHEQUE();
  if  (tecla==51 && lALT)   	addPGTO_BOLETO();
  if  (tecla==52 && lALT)   	addPGTO_CARTAO();
  if  (tecla==53 && lALT)   	addPGTO_VALE();
  if  (tecla==54 && lALT)   	addPGTO_DINHEIRO();
  if  (tecla==55 && lALT)   	addPGTO_INTERNET();  
}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();  
if  (lCTRL && tecla==45 && ! lJanRegistro)   	{incluirREG(2);return;}
if  (tecla==45 && ! lJanRegistro)   	{incluirREG(1);return;}

if  (tecla==13 && cfoco=='txtADESAO')   	{addPROPOSTA(); return;}
if  (tecla==13 && cfoco=='txtDESCRICAO_VALE')   	{addVALE_CREDITO(); return;}
if  (tecla==13 && (cfoco=='txtNOMECH' || cfoco=='txtCPFBOLETO' ||
     cfoco=='txtVLRCARTAO' || cfoco=='txtVLRVALE' || cfoco=='txtVALORVALECREDITO' || cfoco=='txtDINHEIRO' ))   	{addPGTO(); return;}


if (lJanRegistro || lJanCAIXA)  eval("teclasNavegacao(e);");

if  ( tecla==113 && lJanRegistro ) document.getElementById('btnGRAVAR').click();
if  ( tecla==119 && lJanRegistro ) AuxilioF7(cfoco);
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


/*******************************************************************************/
function fecharEDICAO()     {
document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function fecharCAIXA()     {
document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function fechaPESQUISA() {
var divPESQUISA = document.getElementById('divPESQUISA'); divPESQUISA.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
}

/*******************************************************************************/
function fechaREL() {
var div = document.getElementById('divRELATORIOS'); div.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
}


/*******************************************************************************/
function fecharPGTO()     {
document.getElementById("divPGTO").innerHTML='';
document.getElementById("divPGTO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
Muda_CSS();
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

var lJanENTREGA= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO_ENT';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanPGTO= document.getElementById('divPGTO').getAttribute(propCLASSE)=='cssDIV_PGTO';
var lJanCAIXA= document.getElementById('txtCONTA');

if (cmp!=null) 
	document.getElementById(cmp).focus();

else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();
  
else if (lJanPGTO) {
  if (document.getElementById('txtVLRBOLETO'))   {nQtdeCamposTextForm = 5;   document.getElementById('txtVLRBOLETO').focus();}  
  else if (document.getElementById('txtVLRCARTAO'))   {nQtdeCamposTextForm = 1;   document.getElementById('txtVLRCARTAO').focus();}  
  else if (document.getElementById('txtDINHEIRO'))   {nQtdeCamposTextForm = 1;   document.getElementById('txtDINHEIRO').focus();}
  else if (document.getElementById('txtVLRVALE'))   {nQtdeCamposTextForm = 2; document.getElementById('txtREL_REPRESENTANTE').focus();}  
  else if (document.getElementById('txtBANCO'))   {nQtdeCamposTextForm = 6;   document.getElementById('txtCHEQUE').focus();}
  else if (document.getElementById('txtVALE_PGTO'))   {nQtdeCamposTextForm = 2;   document.getElementById('txtVALE_PGTO').focus();}
}

else if (lJanCAIXA) {
  nQtdeCamposTextForm = 11;
  
  aCMPS=new Array();
  aCMPS[0]='txtDATA;Preencha uma data válida';
  aCMPS[1]='txtCONTA;Identifique uma conta válida';  
  aCMPS[2]='txtDESCRICAO;Preencha a descrição';
  aCMPS[3]='txtVALOR;Preencha o valor';
  aCMPS[4]='txtFUNCIONARIO;Identifique um funcionário';  
   
  document.getElementById('txtCONTA').focus();
  document.getElementById('txtFUNCIONARIO').focus();
  document.getElementById('txtCONTA').focus();
}  	

else if (lJanENTREGA) { 
  nQtdeCamposTextForm = 14;

  aCMPS=new Array();
  aCMPS[0]='txtDATA;Preencha uma data válida';
  document.getElementById('txtREPRESENTANTE').focus();
}
else 
  document.getElementById('txtFOCADO').focus();  	
}	

/*******************************************************************************/
function lerREGS( avancarDATA ) {

if ( typeof(avancarDATA)=='undefined' )  avancarDATA=0;
showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

data = document.getElementById('dataTRAB').value;
var data2 = new Date(parseInt(data.substring(4, 10),10), parseInt(data.substring(2, 4),10)-1, parseInt(data.substring(0, 2),10));

if (avancarDATA!=0) {data2.addDays(avancarDATA);}

var dia=data2.getDate(); if (dia.toString().length<2) dia = '0'+dia;
var mes=data2.getMonth()+1; if (mes.toString().length<2) mes = '0'+mes;

document.getElementById('dataTRAB').value = dia+''+mes+''+data2.getFullYear();
dataLER = data2.getFullYear()+''+mes+''+dia;

ajax.criar('ajax/ajaxCAIXA.php?acao=lerREGS&vlr='+
  dataLER+'&vlr2='+avancarDATA+'&esc='+document.getElementById('hidESCRITORIO').value+'&excluidas='+
    document.getElementById('vendoEXCLUIDAS').value, desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');

  if (ajax.ler()=='semAcesso') {
    alert('O usuário logado não tem permissão para acessar caixa de qualquer escritório');
    window.top.frames['framePRINCIPAL'].location.href='inicial.php'    
  }

  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 
  var cmpPESQUISA = aRESP[1].split('^')[2];
  var descCAIXA = aRESP[1].split('^')[3];
  var totEntDINHEIRO = aRESP[1].split('^')[4];
  var totEntCHEQUE = aRESP[1].split('^')[5];
  var totSaiDINHEIRO = aRESP[1].split('^')[8];
  var totSaiCHEQUE = aRESP[1].split('^')[9];
  var nomeESCRITORIO = aRESP[1].split('^')[6];
  var idESCRITORIO = aRESP[1].split('^')[7];
  var saldoDINHEIRO = aRESP[1].split('^')[10];
  var saldoCHEQUE = aRESP[1].split('^')[11];
  var saldoTRANSP_DINHEIRO = aRESP[1].split('^')[12];
  var saldoTRANSP_CHEQUE = aRESP[1].split('^')[13];
  var dataTRANSPORTADO = aRESP[1].split('^')[14];
  var diaSEMANA = aRESP[1].split('^')[15];

  showAJAX(0);
  
  centerDiv( 'divEDICAO' ); centerDiv( 'divPGTO' ); centerDiv( 'divPESQUISA' ); centerDiv( 'divRELATORIOS' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');

  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">'+descCAIXA+'</font>&nbsp;&nbsp;&nbsp;&nbsp;';      

  document.getElementById('lblDINHEIRO').innerHTML = '<font color=blue>'+totEntDINHEIRO+'</font>';
  document.getElementById('lblCHEQUE').innerHTML = '<font color=blue>'+totEntCHEQUE+'</font>'; 

  document.getElementById('lblSAIDADINHEIRO').innerHTML = '<font color=red>'+totSaiDINHEIRO+'</font>';
  document.getElementById('lblSAIDACHEQUE').innerHTML = '<font color=red>'+totSaiCHEQUE+'</font>';

  document.getElementById('lblSALDODINHEIRO').innerHTML = '<font color=blue>'+saldoDINHEIRO+'</font>';
  document.getElementById('lblSALDOCHEQUE').innerHTML = '<font color=blue>'+saldoCHEQUE+'</font>';

  document.getElementById('lblTransDINHEIRO').innerHTML = '<font color=blue>'+saldoTRANSP_DINHEIRO+'</font>';
  document.getElementById('lblTransCHEQUE').innerHTML = '<font color=blue>'+saldoTRANSP_CHEQUE+'</font>';

  document.getElementById('lblTRANSPORTADO').innerHTML = '&nbsp;Transportado <font color=blue>'+dataTRANSPORTADO+'</font>';

  
  data=document.getElementById("dataTRAB").value;
  dataLER = data.substring(0, 2)+'/'+data.substring(2, 4)+'/'+data.substring(6, 10);

  if (cmpPESQUISA=='') 
    titulo.innerHTML += dataLER+',&nbsp;&nbsp;&nbsp;'+diaSEMANA+'';    
  else
    titulo.innerHTML += cmpPESQUISA;

  document.getElementById('tdESCRITORIO').innerHTML = nomeESCRITORIO;
  document.getElementById('hidESCRITORIO').value = idESCRITORIO;
  
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';

  if (document.getElementById('forcarSELECAO').value!='') { 
    var tab = document.getElementById('tabREGs');
    for (var f=0; f<tab.rows.length; f++) {
  
      idLIN = tab.rows[f].id;
      idLIN = idLIN.substr(idLIN.indexOf('_')+1);

      if (idLIN==document.getElementById('forcarSELECAO').value) {
        Selecionar(tab.rows[f].id, 1);     
      }
    }
  }
  document.getElementById('forcarSELECAO').value='';


/*  
  if (document.getElementById('recarregarINC').value==1) {
    document.getElementById('recarregarINC').value=0;
    incluirREG(1);
  }
*/    
}
}


/*******************************************************************************/
function incluirREG(qual) {
showAJAX(1);

if (qual=='1')
  ajax.criar('ajax/ajaxCAIXA.php?acao=incluirENTREGA', desenhaJanelaREG);
else 
  ajax.criar('ajax/ajaxCAIXA.php?acao=incluirCAIXA', desenhaJanelaREG2);  

}

/*******************************************************************************/
function alternarINCLUSAO() {
showAJAX(1);

if (document.getElementById('txtCONTA')) 
  ajax.criar('ajax/ajaxCAIXA.php?acao=incluirENTREGA', desenhaJanelaREG);
else 
  ajax.criar('ajax/ajaxCAIXA.php?acao=incluirCAIXA', desenhaJanelaREG2);  
}


/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');

  var aRESP = ajax.ler().split('^');

  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO_ENT');    divEDICAO.innerHTML = aRESP[0];

  if (document.getElementById('numREG').value=='') 
    document.getElementById('btnVERIFICADA').style.display='none';
  else {
    if (document.getElementById('faltaVERIFICAR').value=='S')
      document.getElementById('btnVERIFICADA').style.display='block'; 
    else
      document.getElementById('btnVERIFICADA').style.display='none';
  }
  if (document.getElementById('acessoLIMITADO').value==1) {
    document.getElementById('btnBOLETO').style.display='none';
    document.getElementById('btnCARTAO').style.display='none';
  } else {
//    document.getElementById('btnVALECREDITO').style.display='inline';
//    document.getElementById('btnBOLETO').style.display='inline';
//    document.getElementById('btnCARTAO').style.display='inline';
  } 
  Muda_CSS();

  /* memoriza info comparativas das propostas */
  var tab = document.getElementById('tabPROPOSTAS');
  propsINICIAL='';
  for (var f=0; f<tab.rows.length; f++) {
    propsINICIAL += propsINICIAL == '' ? '' : '|'; 
    /* identificacao (cpf ou cnpj) e tipo contrato */
    propsINICIAL += tab.rows[f].cells[2].innerHTML+';'+tab.rows[f].cells[1].innerHTML;
  }
 
  
  showAJAX(0);  
  ColocaFocoCmpInicial();
  atlVLR_DEVIDO();
}
}

/*******************************************************************************/
function desenhaJanelaREG2()     {
if ( ajax.terminouLER() ) {

  var divEDICAO = document.getElementById('divEDICAO');

  var aRESP = ajax.ler().split('^');

  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO_CAIXA');    divEDICAO.innerHTML = aRESP[0];

  if (document.getElementById('numREG').value=='') 
    document.getElementById('btnVERIFICADA').style.display='none';
  else {
    if (document.getElementById('faltaVERIFICAR').value=='S')
      document.getElementById('btnVERIFICADA').style.display='block'; 
    else
      document.getElementById('btnVERIFICADA').style.display='none';
  }
  if (document.getElementById('acessoLIMITADO').value==1) {
    document.getElementById('btnBOLETO').style.display='none';
    document.getElementById('btnCARTAO').style.display='none';
  } else {
//    document.getElementById('btnBOLETO').style.display='inline';
//    document.getElementById('btnCARTAO').style.display='inline';
  } 

  
  Muda_CSS(); 
  
  showAJAX(0);  
  ColocaFocoCmpInicial();
}
}  
  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
var tab = document.getElementById('tabREGs');
for (var w=0; w<=tab.rows.length-1; w++) {
  if (document.getElementById('SELECAO').value == tab.rows[w].id) {
    id = id.substring(id.indexOf('_')+1);
    
    if ( tab.rows[w].cells[2].innerHTML!='-' ) {entrega=1;url='ajax/ajaxCAIXA.php?acao=editarENTREGA&vlr=' + id;}
    else {entrega=0;url='ajax/ajaxCAIXA.php?acao=editarCAIXA&vlr=' + id;}
    break; 
  } 
}  

showAJAX(1);
if (entrega==1)
  ajax.criar(url, desenhaJanelaREG);
else
   ajax.criar(url, desenhaJanelaREG2);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro1= document.getElementById('txtREPRESENTANTE');
lJanRegistro2= document.getElementById('txtCONTA');

if (! lJanRegistro1 && !lJanRegistro2 )  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

if (nomeCMP!='todos')         {
	switch (nomeCMP) {
		case 'txtREPRESENTANTE':
		case 'txtREL_REPRESENTANTE':		
		case 'txtREL_REPRESENTANTE2':
		case 'txtTIPO_CONTRATO':		
		case 'txtBANCO':		
		case 'txtOPERADORA':		
		case 'txtCONTA':		
		case 'txtFUNCIONARIO':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
			
      /* alterna a pesquisa para corretor ou funcionario dependendo do tipo de envolvido na operacao d caixa escolhida */
      if (cfoco=='txtFUNCIONARIO') {
/*      if (document.getElementById('tdENVOLVIDO').innerHTML=='Funcionário') nomeCMP='txtFUNCIONARIO';
        else nomeCMP='txtREPRESENTANTE'; */
      }

			showAJAX(1);
      cmpLBL.innerHTML = 'lendo...';
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');
      
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];

      if (nomeCMP=='txtTIPO_CONTRATO') {
        if (cVLR.indexOf('* ERRO *')==-1) {
          vlrADESAO= cVLR.split('!')[1];
          cpf_cnpj= cVLR.split('!')[2];

          txtCPF=document.getElementById('txtCPF');
          if (cpf_cnpj=='1') {
            document.getElementById('tdCPF').innerHTML='CPF';
            txtCPF.setAttribute('onKeyPress', "return sistema_formatar(event, this, '000.000.000-00');");
            txtCPF.setAttribute('maxlength', 14);
          }
          else {
            document.getElementById('tdCPF').innerHTML='CNPJ';
            txtCPF.setAttribute('onKeyPress', "return sistema_formatar(event, this, '00.000.000/0000-00');");
            txtCPF.setAttribute('maxlength', 18);
          }
  
          document.getElementById('txtADESAO').value = vlrADESAO.replace('.',',');
          cVLR = cVLR.split('!')[0];
        }   
      }
      if (nomeCMP=='txtCONTA') {
        if (cVLR.indexOf('* ERRO *')==-1) {
          var tipo=cVLR.split('|')[1];          
          var tipoENVOLVIDO=cVLR.split('|')[2].substring(0,1);

/*        if (tipoENVOLVIDO=='F')  document.getElementById('tdENVOLVIDO').innerHTML='Funcionário';      
          else document.getElementById('tdENVOLVIDO').innerHTML='Corretor'; */


          if (tipo.indexOf('E')!=-1) {
            document.getElementById('btnCHEQUES').style.display='none';
  
            cVLR= cVLR.split('|')[0] + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span style="color:blue;font-size:12px;font-weight:bold;">Entrada</span>)';
          }else {
            document.getElementById('btnCHEQUES').style.display='block';

            cVLR= cVLR.split('|')[0] + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span style="color:red;font-size:12px;font-weight:bold;">Saída</span>)';
          }
        }                           
      }

      cIDCMP=cIDCMP.rtrim().ltrim();
	
    	if (cVLR.indexOf('* ERRO *')!=-1) cmpLBL.style.color='red';
      else cmpLBL.style.color='blue';

      cVLR=cVLR.replace(/_/g, '&nbsp;');  	
      cmpLBL.innerHTML = cVLR;
			
			break;
			

    /* campo NUMERO DO VALE CREDITO SENDO USADO COMO PGTO */
		case 'txtVALE_PGTO':
      numVALE = document.getElementById('txtVALE_PGTO').value.trim();
      if (numVALE=='') {
        alert('Identifique o nº do vale crédito');
        document.getElementById('txtVALE_PGTO').focus();
        return;
      }
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=lerValeCredito&vlr='+numVALE+'&vlr2='+document.getElementById('numREG').value, '', 0);  
      showAJAX(0);
    
      var erroVALE=0;
      if (ajax.ler()=='ERR') {
        alert('Vale crédito não registrado'); erroVALE=1;
      }
      if (ajax.ler()=='INVALIDA') {
        alert('Você não pode gerar um vale crédito e pagar uma proposta com o mesmo '); erroVALE=1;
      }
      if (ajax.ler()=='PAGO') {
        alert('Vale crédito já pago'); erroVALE=1;
      }
      if (erroVALE==1) {
        document.getElementById('lblINICIALVALECREDITO').innerHTML='<font color=red>ERRO</font>';
        document.getElementById('lblUSADOVALECREDITO').innerHTML='<font color=red>ERRO</font>';
        document.getElementById('lblDISPONIVELVALECREDITO').innerHTML='<font color=red>ERRO</font>';
      }
      else {
        document.getElementById('lblINICIALVALECREDITO').innerHTML=ajax.ler().split('^')[0];
        document.getElementById('lblUSADOVALECREDITO').innerHTML=ajax.ler().split('^')[1];
        document.getElementById('lblDISPONIVELVALECREDITO').innerHTML=ajax.ler().split('^')[2];
      }
		case 'txtVALOR':			
			if (document.getElementById('txtRECEBIDO')) {
				if (document.getElementById('txtRECEBIDO').value=='') document.getElementById('txtRECEBIDO').value=document.getElementById('txtVALOR').value;
			}		
			break;
		case 'txtVALE_CREDITO':
      okVALE=document.getElementById('okVALE_CREDITO');      

      if (document.getElementById('txtVALE_CREDITO').value=='') {
        if ( document.getElementById('txtDESCRICAO_VALE').value.indexOf('VALE CRÉDITO Nº')!=-1)
            document.getElementById('txtDESCRICAO_VALE').value= '';
        if (document.getElementById('txtPAGAR_VALE').value=='')  
            document.getElementById('txtPAGAR_VALE').value= document.getElementById('txtDATA').value;
        okVALE.innerHTML='';
        return;
      }

      showAJAX(1);
			ajax.criar('ajax/ajaxCAIXA.php?acao=verValeCredito&vlr=' + document.getElementById('txtVALE_CREDITO').value + '&vlr2=' +
              document.getElementById('numREG').value, '', 0);
			showAJAX(0);

      if (ajax.ler()=='ERR') {
        alert('Vale já usado em outra operação de caixa');
        okVALE.innerHTML='ERRO'; okVALE.style.color='red'; okVALE.style.fontWeight='bold';
      } else if (ajax.ler()=='USADO') {
        alert('Vale já pago');
        okVALE.innerHTML='ERRO'; okVALE.style.color='red'; okVALE.style.fontWeight='bold';
      } else {
        okVALE.innerHTML='OK'; okVALE.style.color='blue'; okVALE.style.fontWeight='normal';

        if (document.getElementById('txtPAGAR_VALE').value=='')  
            document.getElementById('txtPAGAR_VALE').value= document.getElementById('txtDATA').value;

        if (document.getElementById('txtVALE_CREDITO').value!='' && document.getElementById('txtTIPO_VALE').value.toUpperCase()=='C' &&
              document.getElementById('txtDESCRICAO_VALE').value=='')
            document.getElementById('txtDESCRICAO_VALE').value= 'VALE CRÉDITO Nº '+document.getElementById('txtVALE_CREDITO').value;


      }
      break;
	}
	return;
}
  
else  {
	for (i=0;i<aCMPS.length;i++)   {
		cmp = aCMPS[i].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
		var label = cCMP.replace('txt', 'lbl'); 		
				
		erro=0;

    if (document.getElementById('txtCONTA')) {
  		switch (cCMP)   {
  			case 'txtDATA':
  				if ( cVLR=='' || ! verifica_data('txtDATA') )   erro=1;
  				break;
  				
      case 'txtFUNCIONARIO':		
  		case 'txtCONTA':
        if ( document.getElementById(label).innerHTML=='' ||
            document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;
  		}
    }
    else    { 
  		switch (cCMP)   {
  			case 'txtDATA':
  				if ( cVLR=='' || ! verifica_data('txtDATA') )   erro=1;
  				break;
  		}
    }		
		if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}
  data = document.getElementById('txtDATA').value;
  dataGRAVAR='null';
  if (data.rtrim().ltrim()!='') {
    /* se esta alterando entrega e nao mudou a data, sinaliza, porque o campo é "datetime", se mexermos nela vai alterar a data, baguncar tudo  */
    if (data==document.getElementById('txtDATA_SEG').value && document.getElementById('numREG').value!='') dataGRAVAR='NAOMUDAR';
    else dataGRAVAR = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

    dataOPERACAO = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);   
  }
  var totDINHEIRO=0; var totCHEQUE=0;
  var strPGTO='';                       
  var tab = document.getElementById('tabPGTO');
  var numBOLETO='';
  for (var r=0; r<=tab.rows.length-1; r++) {
    strPGTO += (strPGTO=='' ? '' : '|') ;
    tipo= tab.rows[r].cells[0].innerHTML;

    if (tipo=='CHEQUE') {
      data=tab.rows[r].cells[6].innerHTML;
      dataGRAVAR2 = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

      totCHEQUE += parseFloat(tab.rows[r].cells[7].innerHTML.replace(',','.'), 10); 
      // cheque=3, banco=4, data= 6, valor= 7
      strPGTO += 'CHEQUE;'+
                  tab.rows[r].cells[3].innerHTML+';'+
                  tab.rows[r].cells[4].innerHTML+';'+
                  dataGRAVAR2+';'+
                  tab.rows[r].cells[7].innerHTML.replace(',','.')+';'+
                  tab.rows[r].cells[8].innerHTML+';'+
                  tab.rows[r].cells[9].innerHTML;
    }
    else if (tipo=='BOLETO') {
      // valor boleto=3   data boleto=4       num boleto= 5
      var data=tab.rows[r].cells[4].innerHTML;
      var dataGRAVAR2 = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

      var vlrCORRETORA = parseFloat(document.getElementById('lblTOTAL').innerHTML.replace(',','.'), 10)+
                         parseFloat(document.getElementById('lblVLR_ADESAO').innerHTML.replace(',','.'), 10);
 
      strPGTO += 'BOLETO;'+
                  tab.rows[r].cells[3].innerHTML.replace(',','.')+';'+
                  dataGRAVAR2+';'+
                  tab.rows[r].cells[5].innerHTML+';'+
                  tab.rows[r].cells[6].innerHTML+';'+
                  tab.rows[r].cells[7].innerHTML+';'+
                  vlrCORRETORA.toFixed(2).toString();

      numBOLETO=tab.rows[r].cells[5].innerHTML;
    } 
    else if (tipo=='CARTÃO') {
      // banco=3   valor=5
      strPGTO += 'CARTÃO;'+
                  tab.rows[r].cells[3].innerHTML.replace(',','.');
    }
    else if (tipo=='DINHEIRO') {
      strPGTO += 'DINHEIRO;'+
                  tab.rows[r].cells[3].innerHTML.replace(',','.');
      totDINHEIRO += parseFloat(tab.rows[r].cells[3].innerHTML.replace(',','.'), 10);
    }
    else if (tipo=='INTERNET') {
      strPGTO += 'INTERNET;'+
                  tab.rows[r].cells[3].innerHTML.replace(',','.');
    }
    
    else if (tipo=='VALE') {
      // representante=3   valor=5
      strPGTO += 'VALE;'+
                  tab.rows[r].cells[3].innerHTML+';'+
                  tab.rows[r].cells[5].innerHTML.replace(',','.');
    }
    else if (tipo=='VALE CRÉDITO') {
      // vlr=3   num vale=4
      strPGTO += 'VALE CRÉDITO;'+
                  tab.rows[r].cells[4].innerHTML+';'+
                  tab.rows[r].cells[3].innerHTML.replace(',','.');
    }
  }
  /* verifica se ha conta: entrega de proposta definida */
	showAJAX(1);
	ajax.criar('ajax/ajaxCAIXA.php?acao=verificaCONTA', '' , 0);
  showAJAX(0);
  idCONTA=ajax.ler();
  

  /* concatena info dos vales INSERIDOS MANUALMENTE numa string */
  infoCRED_DEB='';
  var tabVALES = document.getElementById('tabVALES');
  for (var x=0; x<tabVALES.rows.length; x++) {
    /* alguns cred/debitos estao listados por exemplo, oriundos de um adto salarial,
      quem vai excluir/incluir novamente este tipo de cred/deb é a propria operacao d adto salarial,
      no momenhto que o sistema perceber que é uma operacao que suscita automaticamwente um debito, ele mesmo cuida disso
    -- as linhas abaixo concatenam cred/deb inseridos manualmente pelo usuario, ve-se se foram inseridos manualmente pela coluna 10
      (descricao do cred/deb - infelizmente devido a alteracoes no sistema, sei que é fragil, mas é a unica maneira
      sabermos quem é inserido manual, quem automatico */
      
    var desc=tabVALES.rows[x].cells[10].innerHTML;
    if (desc!='PROPOSTA(S) PAGA(S) COM BOLETO' && desc!='PROPOSTA(S) PAGA(S) COM VALE' &&
       desc!='ADIANTAMENTO SALARIAL' && desc!='ADIANTAMENTO DE COMISSÃO' ) {
      data = tabVALES.rows[x].cells[5].innerHTML;
      dataGRAVAR_CRED_DEB = '20'+data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);
 
      infoCRED_DEB += infoCRED_DEB=='' ? '' : '|'; 
      infoCRED_DEB += (tabVALES.rows[x].cells[0].innerHTML.indexOf('Débito')!=-1 ? 'D' : 'C')+';'+ 
                       tabVALES.rows[x].cells[1].innerHTML+';'+
                       tabVALES.rows[x].cells[7].innerHTML+';'+
                       tabVALES.rows[x].cells[3].innerHTML.replace(',','.')+';'+
                       tabVALES.rows[x].cells[4].innerHTML.replace(',','.')+';'+
                       dataGRAVAR_CRED_DEB+';'+
                       tabVALES.rows[x].cells[10].innerHTML;
    }
  }

  if (document.getElementById('txtCONTA')) {
    if (document.getElementById('lblCONTA').innerHTML.indexOf('Entrada')!=-1)
      tipo='E';
    else
      tipo='S';
      
    cmps=document.getElementById('numREG').value+'|'+
         dataGRAVAR+'|'+
         document.getElementById('txtFUNCIONARIO').value+'|'+
         document.getElementById('txtCONTA').value+'|'+
         document.getElementById('txtDESCRICAO').value+'|'+                   
         document.getElementById('txtVALOR').value.replace(',','.')+'|'+
         tipo+'|'+
        '|'+dataOPERACAO;

    url = 'ajax/ajaxCAIXA.php?acao=gravarCAIXA&vlr=' + cmps+ '&pgto='+strPGTO+'&din='+totDINHEIRO+'&ch='+totCHEQUE+
          '&bl='+numBOLETO+'&esc='+document.getElementById('hidESCRITORIO').value+'&cred='+infoCRED_DEB;
  }
  else {
    var strPROP='';
    var tab = document.getElementById('tabPROPOSTAS');
    for (var f=0; f<=tab.rows.length-1; f++) {
      strPROP += (strPROP=='' ? '' : '|') ;
      vlr= tab.rows[f].cells[3].innerHTML.replace(',','.');
      vlrRECEBIDO= tab.rows[f].cells[4].innerHTML.replace(',','.');
      vlrPRESTADORA= tab.rows[f].cells[5].innerHTML; vlrPRESTADORA=vlrPRESTADORA.substring(0, vlrPRESTADORA.indexOf('(')).replace(',','.');
      percPRESTADORA= tab.rows[f].cells[5].innerHTML; percPRESTADORA=percPRESTADORA.substring(percPRESTADORA.indexOf('(')+1);
      percPRESTADORA=percPRESTADORA.substring(0, percPRESTADORA.indexOf('%'));
      
      var vlrADESAO= tab.rows[f].cells[6].innerHTML.replace(',','.');
      var idUnicoEntregaProposta= tab.rows[f].cells[12].innerHTML;

      strPROP +=  tab.rows[f].cells[8].innerHTML+';'+
                  tab.rows[f].cells[9].innerHTML+';'+
                  tab.rows[f].cells[2].innerHTML+';'+
                  vlr+';'+
                  vlrRECEBIDO+';'+
                  vlrADESAO+';'+
                  vlrPRESTADORA+';'+
                  percPRESTADORA+';'+
                  idUnicoEntregaProposta;
    }
    
    if (strPROP=='') {
      alert('Preencha pelo menos 1 contrato');
      return;
    }

    /* verifica se ha conta: entrega de proposta definida */
  	showAJAX(1);
  	ajax.criar('ajax/ajaxCAIXA.php?acao=verificaCONTA', '' , 0);
    showAJAX(0);
    idCONTA=ajax.ler();
    
    if (ajax.ler().indexOf('NAO')!=-1) {
      alert('Não há conta ENTREGA DE PROPOSTA definida na tabela de contas'); return;
    }

    cmps= document.getElementById('numREG').value+'|'+
          dataGRAVAR+'^'+strPROP+'^'+
          ''+'^'+idCONTA+'^'+
          infoCRED_DEB+'^'+dataOPERACAO;

    /* verifica se houve alteracao da lista de propostas entregues, em 2 campos.. identificacao (cpf ou cnpj), e tipo de contrato
      se sim, marca no caixa ERRO! a vinculacao caixa/cadastro */ 
    var tab = document.getElementById('tabPROPOSTAS');
    var propsGRAVACAO='';
    for (var f=0; f<tab.rows.length; f++) {
      propsGRAVACAO += propsGRAVACAO == '' ? '' : '|'; 
      /* identificacao (cpf ou cnpj) e tipo contrato */
      propsGRAVACAO += tab.rows[f].cells[2].innerHTML+';'+tab.rows[f].cells[1].innerHTML;
    }

    erroCAIXA=0;
    if (propsINICIAL!=propsGRAVACAO && propsINICIAL!='') {
      erroCAIXA=1;
      if (! confirm('ATENÇÃO\n\nVocê alterou um dos campos: CPF, CNPJ ou TIPO DE CONTRATO\n\n'+
                'Estes campos definem a relação CAIXA -> CADASTRO \n\n'+
                'O cadastro de todos as propostas com estes cpfs/cnpjs sera desvinculado do caixa e deverá ser vinculado novamente\n\n'+
                'Continua?')) return;
    }
    if (propsINICIAL=='') erroCAIXA=1;
    url = 'ajax/ajaxCAIXA.php?acao=gravarENTREGA&vlr=' + cmps + '&pgto='+strPGTO+'&din='+totDINHEIRO+'&ch='+totCHEQUE+
            '&bl='+numBOLETO+'&esc='+document.getElementById('hidESCRITORIO').value+'&erroCAIXA='+erroCAIXA+'&cred='+infoCRED_DEB;;
  }

	showAJAX(1);
	ajax.criar(url, '' , 0);
  showAJAX(0);
  
  resp = ajax.ler();

  var titulo=document.getElementById('tituloEDICAO').innerHTML.toLowerCase();
  document.getElementById('recarregarINC').value = (titulo.indexOf('incluir')!=-1) ? 1 : 0;
  
  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    cID = document.getElementById('SELECAO').value;
		
		document.getElementById('SELECAO').value="";
  	
  	window.top.document.getElementById('infoTrab').value = 'frmCAIXA:GRAVOU=' + cID
  	lerREGS();
  	
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
}

/*******************************************************************************/
function excluirREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

if (! confirm('Excluir este registro?')) return;

id = id.substring(id.indexOf('_')+1);
showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=excluirREG&vlr=' + id, '', 0);
showAJAX(0);
  
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao excluir!!! \n\n' + ajax.ler());
  
  
lerREGS();  
}


/*******************************************************************************/
function addPROPOSTA() {
var tab = document.getElementById('tabPROPOSTAS');

cVLR=document.getElementById('txtCPF').value.trim();
lERRO=false;
if (cVLR!='')    {
  for (e=0; e<5; e++) {
    cVLR = cVLR.replace(".",  "");
    cVLR = cVLR.replace("-",  "");
  }
  if (document.getElementById('tdCPF').innerHTML=='CPF') {
    if ( cVLR.trim()!='' && ! validacpf(cVLR) ) {lERRO=true; erro='Preencha um CPF válido';}
  }
  else {
    if ( cVLR.trim()!='' && ! valida_cnpj(cVLR) ) {lERRO=true; erro='Preencha um CNPJ válido';}
  }
}

if (lERRO==true) {
  alert(erro);
  document.getElementById('txtCPF').focus();
  return;
}


linEDITANDO=-1;
var maiorID = -1;
for (var f=0; f<=tab.rows.length-1; f++) {
  var idLIN = tab.rows[f].id.replace('PROP_', '');
    
  if (parseInt(idLIN, 10) > maiorID) maiorID = parseInt(idLIN, 10);
  if (document.getElementById('propEDITANDO').value==tab.rows[f].id) linEDITANDO=f;
}

document.getElementById('propEDITANDO').value='';
var erro='';

idREPRE = document.getElementById('txtREPRESENTANTE').value.trim();
nomeREPRE=document.getElementById('lblREPRESENTANTE').innerHTML;
if (idREPRE=='' || nomeREPRE.indexOf('ERRO')!=-1) {
  alert('Identifique o corretor');
  document.getElementById('txtREPRESENTANTE').focus();
  return;
}

idTIPO = document.getElementById('txtTIPO_CONTRATO').value.trim();
nomeTIPO = document.getElementById('lblTIPO_CONTRATO').innerHTML;
if (idTIPO=='' || nomeTIPO.indexOf('ERRO')!=-1) {
  alert('Identifique o tipo de contrato');
  document.getElementById('txtTIPO_CONTRATO').focus();
  return;
}



cpf=document.getElementById('txtCPF').value;  

var vlrADESAO = document.getElementById('txtADESAO').value.rtrim().ltrim();
vlrADESAO = vlrADESAO=='' ? '0' : vlrADESAO ; vlrADESAO = parseFloat(vlrADESAO.replace(',','.'), 10);   
vlrADESAO = vlrADESAO.toFixed(2).toString().replace('.',',');

var vlr = document.getElementById('txtVALOR').value.rtrim().ltrim();
vlr = vlr=='' ? '0' : vlr ; vlr = parseFloat(vlr.replace(',','.'), 10);   
vlr = vlr.toFixed(2).toString().replace('.',',');

var vlrRECEBIDO = document.getElementById('txtRECEBIDO').value.rtrim().ltrim();
vlrRECEBIDO = vlrRECEBIDO=='' ? '0' : vlrRECEBIDO ; vlrRECEBIDO = parseFloat(vlrRECEBIDO.replace(',','.'), 10);   
vlrRECEBIDO = vlrRECEBIDO.toFixed(2).toString().replace('.',',');

cVLR=document.getElementById('txtRECEBIDO').value.trim();
if (cVLR=='')    {
  alert('Preencha o valor recebido');
  document.getElementById('txtRECEBIDO').focus();  
  return;
}  

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=lerPercAdeRepre&id='+idREPRE+'&idPROD='+idTIPO, '', 0);  
showAJAX(0);

comiREPRE_mostrar = parseFloat(ajax.ler(), 10).toFixed(0);
comiPRESTADORA_mostrar = (100-parseFloat(ajax.ler(), 10)).toFixed(0);  

comiREPRE = parseFloat(ajax.ler(), 10) / 100;
comiPRESTADORA = (100-parseFloat(ajax.ler(), 10)) / 100;

/*alert(ajax.ler()+'...'+comiPRESTADORA);*/
  
vlrCALC= parseFloat(document.getElementById('txtRECEBIDO').value.replace(',','.'), 10);
/*vlrCALC= parseFloat(document.getElementById('txtVALOR').value.replace(',','.'), 10);*/

vlrCORRETOR = (vlrCALC * comiREPRE).toFixed(2).toString().replace('.',',');  
vlrPRESTADORA = (vlrCALC * comiPRESTADORA).toFixed(2).toString().replace('.',','); 

if (linEDITANDO!=-1) { 
  tab.rows[linEDITANDO].cells[0].innerHTML = nomeREPRE+' ('+idREPRE+')';
  tab.rows[linEDITANDO].cells[1].innerHTML = nomeTIPO+' ('+idTIPO+')';
  tab.rows[linEDITANDO].cells[2].innerHTML = cpf;
  tab.rows[linEDITANDO].cells[3].innerHTML = vlr;              
  tab.rows[linEDITANDO].cells[4].innerHTML = vlrRECEBIDO;
  tab.rows[linEDITANDO].cells[5].innerHTML = vlrPRESTADORA + ' ('+comiPRESTADORA_mostrar+'%)';  
  tab.rows[linEDITANDO].cells[6].innerHTML = vlrADESAO;  
  
  tab.rows[linEDITANDO].cells[8].innerHTML = idREPRE;
  tab.rows[linEDITANDO].cells[9].innerHTML = idTIPO;
  tab.rows[linEDITANDO].cells[10].innerHTML = nomeREPRE;
  tab.rows[linEDITANDO].cells[11].innerHTML = nomeTIPO;    

  /* numREG da entrega d proposta  */
  tab.rows[linEDITANDO].cells[12].innerHTML = tab.rows[linEDITANDO].cells[12].innerHTML;
}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'PROP_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = nomeREPRE+' ('+idREPRE+')';  col.width = '20%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = nomeTIPO+' ('+idTIPO+')';  col.width = '20%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = cpf;  col.width = '10%'; col.align='left';	
	  	
	col = lin.insertCell(-1); col.innerHTML = vlr;  col.width = '10%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = vlrRECEBIDO;  col.width = '15%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = vlrPRESTADORA + ' ('+comiPRESTADORA_mostrar+'%)';  col.width = '15%'; col.align='right';
	/*col = lin.insertCell(-1); col.innerHTML = vlrCORRETOR + ' ('+comiREPRE_mostrar+'%)';  col.width = '15%'; col.align='right'; */
	col = lin.insertCell(-1); col.innerHTML = vlrADESAO;  col.width = '10%'; col.align='right';    
	   	
  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onclick = function() { removePROPOSTA(lin.id); }  	
  
  col = lin.insertCell(-1); col.innerHTML = idREPRE; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = idTIPO; col.style.display='none';
  
  col = lin.insertCell(-1); col.innerHTML = nomeREPRE; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = nomeTIPO; col.style.display='none'; 

  /* numREG da entrega d proposta, quando inserindo nao existe obvio, mas necessario haver a coluna 12 para evitar erros */
  col = lin.insertCell(-1); col.innerHTML = ''; col.style.display='none';
  
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onclick = function() {editarPROPOSTA(this.id); }    
  lin.width='100%';
}
atlVLR_DEVIDO();
limparCmpsPropEnt();    
}

/*******************************************************************************/
function limparCmpsPropEnt() {

document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
document.getElementById('txtTIPO_CONTRATO').value=''; document.getElementById('lblTIPO_CONTRATO').innerHTML='';
document.getElementById('txtCPF').value='';
document.getElementById('txtADESAO').value='';
document.getElementById('txtVALOR').value='';
document.getElementById('txtRECEBIDO').value='';

var tab = document.getElementById('tabPROPOSTAS');
for (var y=0; y<=tab.rows.length-1; y++) {
  tab.rows[y].style.color='black';
  tab.rows[y].style.fontWeight='normal';
}
document.getElementById('propEDITANDO').value='';
setTimeout("document.getElementById('txtREPRESENTANTE').focus()", 200);
}


/*******************************************************************************/
function limparCmpsCheque() {

document.getElementById('txtCHEQUE').value='';
document.getElementById('txtBANCO').value=''; document.getElementById('lblBANCO').innerHTML='';
document.getElementById('txtDATACH').value='';
document.getElementById('txtVLRCH').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtCHEQUE').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';

}

/*******************************************************************************/
function limparCmpsBoleto() {

document.getElementById('txtVLRBOLETO').value='';
document.getElementById('txtDATABOLETO').value='';
document.getElementById('txtNUMEROBOLETO').value='';
document.getElementById('txtSACADO').value='';
document.getElementById('txtCPFBOLETO').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtVLRBOLETO').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}

/*******************************************************************************/
function limparCmpsCartao() {

document.getElementById('txtVLRCARTAO').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtCARTAO').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}

/*******************************************************************************/
function limparCmpsDinheiro() {

document.getElementById('txtDINHEIRO').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtDINHEIRO').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}


/*******************************************************************************/
function limparCmpsVale() {

document.getElementById('txtREL_REPRESENTANTE').value=''; document.getElementById('lblREL_REPRESENTANTE').innerHTML='';
document.getElementById('txtVLRVALE').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtREL_REPRESENTANTE').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}

/*******************************************************************************/
function limparCmpsValeCredito() {

document.getElementById('txtVALE_PGTO').value='';
document.getElementById('txtVALORVALECREDITO').value='';
document.getElementById('lblINICIALVALECREDITO').innerHTML='';
document.getElementById('lblUSADOVALECREDITO').innerHTML='';
document.getElementById('lblDISPONIVELVALECREDITO').innerHTML='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtVALE_PGTO').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}

/*******************************************************************************/
function editarPROPOSTA(idLinEditar ) {

var tab = document.getElementById('tabPROPOSTAS');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  tab.rows[x].style.color='black';
  tab.rows[x].style.fontWeight='normal';
  
  if (idLIN == idLinEditar) {  
    tab.rows[x].style.color='blue';
    tab.rows[x].style.fontWeight='bold';    
    
    document.getElementById('txtREPRESENTANTE').value = tab.rows[x].cells[8].innerHTML; 
    document.getElementById('txtTIPO_CONTRATO').value = tab.rows[x].cells[9].innerHTML;    

    document.getElementById('lblREPRESENTANTE').innerHTML = tab.rows[x].cells[10].innerHTML;    
    document.getElementById('lblTIPO_CONTRATO').innerHTML = tab.rows[x].cells[11].innerHTML;    
    
    document.getElementById('txtCPF').value = tab.rows[x].cells[2].innerHTML;    
    document.getElementById('txtVALOR').value = tab.rows[x].cells[3].innerHTML;
    document.getElementById('txtRECEBIDO').value = tab.rows[x].cells[4].innerHTML;
    document.getElementById('txtADESAO').value = tab.rows[x].cells[6].innerHTML;            

    document.getElementById('propEDITANDO').value=idLinEditar;
    document.getElementById('txtTIPO_CONTRATO').focus();
  }  
}

setTimeout("document.getElementById('txtREPRESENTANTE').focus()", 200);
}

/*******************************************************************************/
function removePROPOSTA(idLinExcluir)    {

var tab = document.getElementById('tabPROPOSTAS');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN == idLinExcluir) {tab.deleteRow( x );break;}  
}
atlVLR_DEVIDO();
limparCmpsPropEnt();
}

/*******************************************************************************/
function removePGTO(idLinExcluir)    {

var lJanPGTO = document.getElementById('divPGTO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if (lJanPGTO) fecharPGTO();

document.getElementById('pgtoEDITANDO').value='-1';
var tab = document.getElementById('tabPGTO');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN == idLinExcluir) {
    /* se boleto ja pago, nao permite alterar */
    if ( tab.rows[x].cells[0].innerHTML.trim().indexOf('BOLETO')!=-1 && tab.rows[x].cells[1].innerHTML.trim().indexOf('por:')!=-1 ) {
      alert('Boleto já pago. Para alterar/excluir, primeiramente cancele seu pagamento'); 
      return;
    }
    tab.deleteRow( x );break;  
  }
}
atlVLR_DEVIDO();
}


/*******************************************************************************/
function atlVLR_DEVIDO() {
var tab = document.getElementById('tabPROPOSTAS');
if (! tab) return;
vlrPRESTADORA=0;
vlrADESAO=0; 
vlrTOTAL=0;
for (var f=0; f<=tab.rows.length-1; f++) {
  valor=tab.rows[f].cells[5].innerHTML;
  valor=valor.substring(0,valor.indexOf('('));
  vlrPRESTADORA += parseFloat(valor.replace(',','.'), 10);
  vlrTOTAL += parseFloat(valor.replace(',','.'), 10);
  
  valor=tab.rows[f].cells[6].innerHTML;
  vlrADESAO += parseFloat(valor.replace(',','.'), 10);
//  vlrTOTAL += parseFloat(valor.replace(',','.'), 10);
}
vlrJUSTIFICADO=0;
var tab = document.getElementById('tabPGTO');
for (var f=0; f<=tab.rows.length-1; f++) {

  if (tab.rows[f].cells[0].innerHTML.trim()=='CHEQUE') coluna=7;
  if (tab.rows[f].cells[0].innerHTML.trim()=='BOLETO') coluna=3;  
  if (tab.rows[f].cells[0].innerHTML.trim()=='CARTÃO') coluna=3;  
  if (tab.rows[f].cells[0].innerHTML.trim()=='DINHEIRO' || tab.rows[f].cells[0].innerHTML.trim()=='INTERNET') coluna=3;
  if (tab.rows[f].cells[0].innerHTML.trim()=='VALE') coluna=5;  
  if (tab.rows[f].cells[0].innerHTML.trim()=='VALE CRÉDITO') coluna=3;

  valor=tab.rows[f].cells[coluna].innerHTML;
  vlrJUSTIFICADO += parseFloat(valor.replace(',','.'), 10);
} 
 
vlrFALTANDO=parseFloat(((vlrTOTAL+vlrADESAO) - vlrJUSTIFICADO).toFixed(2), 10);

/* verifica na lista de cred/deb se ha vale(s) credito, se houver, leva em consideracao
para calculo do valor devido */
var valeCREDITO = 0;
var tabVALES = document.getElementById('tabVALES');
for (var x=0; x<tabVALES.rows.length; x++) {
  if (tabVALES.rows[x].cells[0].innerHTML.indexOf('Vale Crédito')!=-1) {
    vlr = tabVALES.rows[x].cells[3].innerHTML.replace(',','.');
    vlr = parseFloat(vlr, 10);
    valeCREDITO += vlr;
  }
}

vlrFALTANDO += valeCREDITO;

document.getElementById('lblVLR_PROP').innerHTML = vlrPRESTADORA.toFixed(2).toString().replace('.',',');
document.getElementById('lblVLR_ADESAO').innerHTML = vlrADESAO.toFixed(2).toString().replace('.',',');
document.getElementById('lblTOTAL').innerHTML = vlrTOTAL.toFixed(2).toString().replace('.',',');

document.getElementById('lblTOTAL_DEVE').innerHTML = (vlrTOTAL+vlrADESAO).toFixed(2).toString().replace('.',',');

document.getElementById('lblJUSTIFICADO').innerHTML = vlrJUSTIFICADO.toFixed(2).toString().replace('.',',');
if (isNaN(vlrFALTANDO))
  document.getElementById('lblFALTANDO').innerHTML = '0,00';
else {
  if (vlrFALTANDO<0)
    document.getElementById('lblFALTANDO').innerHTML = '(SOBRANDO)&nbsp;'+vlrFALTANDO.toFixed(2).toString().replace('.',',');
  else
    document.getElementById('lblFALTANDO').innerHTML = vlrFALTANDO.toFixed(2).toString().replace('.',',');
}

} 


/*******************************************************************************/
function validacpf(vlr) {
var CPF = vlr;

// Aqui começa a checagem do CPF
var POSICAO, I, SOMA, DV, DV_INFORMADO;
var DIGITO = new Array(10);
DV_INFORMADO = CPF.substr(9, 2); // Retira os dois últimos dígitos do número informado

// Desemembra o número do CPF na array DIGITO
for (I=0; I<=8; I++) {
  DIGITO[I] = CPF.substr( I, 1);
}

// Calcula o valor do 10º dígito da verificação
POSICAO = 10;
SOMA = 0;
   for (I=0; I<=8; I++) {
      SOMA = SOMA + DIGITO[I] * POSICAO;
      POSICAO = POSICAO - 1;
   }
DIGITO[9] = SOMA % 11;
   if (DIGITO[9] < 2) {
        DIGITO[9] = 0;
}
   else{
       DIGITO[9] = 11 - DIGITO[9];
}

// Calcula o valor do 11º dígito da verificação
POSICAO = 11;
SOMA = 0;
   for (I=0; I<=9; I++) {
      SOMA = SOMA + DIGITO[I] * POSICAO;
      POSICAO = POSICAO - 1;
   }
DIGITO[10] = SOMA % 11;
   if (DIGITO[10] < 2) {
        DIGITO[10] = 0;
   }
   else {
        DIGITO[10] = 11 - DIGITO[10];
   }


// Verifica se os valores dos dígitos verificadores conferem
DV = DIGITO[9] * 10 + DIGITO[10];
   if (DV != DV_INFORMADO) {
      return false;
   }

return true;
}
/*******************************************************************************/
function verificaCOMBINACAO(item) {
return true;

/* trecho abaixo desativado */
var tab = document.getElementById('tabPGTO');
if (tab.rows.length>0) {
  alert('Permitido somente 1 ítem como pagamento');
  return;
}
for (var y=0; y<=tab.rows.length-1; y++) {
  if (tab.rows[y].cells[0].innerHTML!=item) {
    alert('Não é possível combinar formas de pgto. Você já está usando o tipo '+tab.rows[y].cells[0].innerHTML);
    return false;  
  }
}
return true;
}
                  
/*******************************************************************************/
function addPGTO_VALE_CREDITO()     {
if (!verificaCOMBINACAO('VALE')) return;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_VALE_CREDITO', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS(); 
  
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function addPGTO_VALE()     {
if (!verificaCOMBINACAO('VALE')) return;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_VALE', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS(); 
  
ColocaFocoCmpInicial();
}




/*******************************************************************************/
function addPGTO_CHEQUE()     {
if (!verificaCOMBINACAO('CHEQUE')) return;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_CHEQUE', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS(); 
  
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function addPGTO_BOLETO()     {
if (!verificaCOMBINACAO('BOLETO')) return;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_BOLETO', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS();
if (document.getElementById('txtVLRBOLETO').value.trim()=='')
  document.getElementById('txtVLRBOLETO').value=document.getElementById('lblTOTAL_DEVE').innerHTML.trim();


ColocaFocoCmpInicial();
}

/*******************************************************************************/
function addPGTO_CARTAO()     {
if (!verificaCOMBINACAO('CARTÃO')) return;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_CARTAO', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS();
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function addPGTO_DINHEIRO()     {

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_DINHEIRO', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS();
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function addPGTO_INTERNET()     {

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_INTERNET', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS();
ColocaFocoCmpInicial();
}





/*******************************************************************************/
function addPGTO()     {
var tab = document.getElementById('tabPGTO');

var maiorID = -1;
linEDITANDO=-1;
for (var f=0; f<=tab.rows.length-1; f++) {
  var idLIN = tab.rows[f].id.replace('PGTO_', '');
    
  if (parseInt(idLIN, 10) > maiorID) maiorID = parseInt(idLIN, 10);
  if (document.getElementById('pgtoEDITANDO').value==tab.rows[f].id) linEDITANDO=f;
}

document.getElementById('pgtoEDITANDO').value='';

if (document.getElementById('txtVLRCH')) {
  idBANCO = document.getElementById('txtBANCO').value.trim();
  nomeBANCO=document.getElementById('lblBANCO').innerHTML;
  if (idBANCO=='' || nomeBANCO.indexOf('ERRO')!=-1) {
    alert('Identifique o banco');
    document.getElementById('txtBANCO').focus();
    return;
  }
  
  var vlrCH = document.getElementById('txtVLRCH').value.rtrim().ltrim();
  vlrCH = vlrCH=='' ? '0' : vlrCH ; vlrCH = parseFloat(vlrCH.replace(',','.'), 10);
  if (vlrCH==0) {
    alert('Preencha o valor do cheque'); document.getElementById('txtVLRCH').focus(); return;
  }   
  vlrCH = vlrCH.toFixed(2).toString().replace('.',',');
  
  var data=document.getElementById('txtDATACH').value.rtrim();
  if ( data=='' || ! verifica_data('txtDATACH') )   {
    alert('Preencha uma data válida'); document.getElementById('txtDATACH').focus(); return;    
  }

  modo='CHEQUE';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaNº: </font></td><td align=left  width="80px">_azul'+document.getElementById('txtCHEQUE').value+'</font></td>'+
            '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'+nomeBANCO+' ('+idBANCO+')</font></td>'+
            '<td>_cinzaData: </font></td><td align=left width="80px">_azul'+document.getElementById('txtDATACH').value+'</font></td>'+            
            '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'+vlrCH+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
}
if (document.getElementById('txtVLRBOLETO')) {
  var vlrBOLETO = document.getElementById('txtVLRBOLETO').value.rtrim().ltrim();
  vlrBOLETO = vlrBOLETO=='' ? '0' : vlrBOLETO ; vlrBOLETO = parseFloat(vlrBOLETO.replace(',','.'), 10);
  if (vlrBOLETO==0) {
    alert('Preencha o valor do boleto'); document.getElementById('txtVLRBOLETO').focus(); return;
  }   
  vlrBOLETO = vlrBOLETO.toFixed(2).toString().replace('.',',');

  var dataBOLETO=document.getElementById('txtDATABOLETO').value.rtrim();
  if ( dataBOLETO=='' || ! verifica_data('txtDATABOLETO') )   {
    alert('Preencha uma data válida'); document.getElementById('txtDATABOLETO').focus(); return;    
  }

  var numBOLETO=document.getElementById('txtNUMEROBOLETO').value.rtrim();
  if ( numBOLETO=='' )   {
    alert('Preencha o nº do boleto'); document.getElementById('txtNUMEROBOLETO').focus(); return;    
  }

  showAJAX(1);
	ajax.criar('ajax/ajaxCAIXA.php?acao=verBOLETO&vlr=' + document.getElementById('txtNUMEROBOLETO').value + '&vlr2=' +
          document.getElementById('numREG').value, '', 0);
	showAJAX(0);

  if (ajax.ler()=='ERR') {
    alert('Boleto já usado em outra operação de caixa');
    return;
  } 
  
  modo='BOLETO';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrBOLETO+'</font></td>'+            
            '<td>_cinza&nbsp;&nbsp;&nbsp;Vencimento: </font></td><td align=right width="60px">_azul'+dataBOLETO+'</font></td>'+
            '<td>_cinza&nbsp;&nbsp;&nbsp;Nº: </font></td><td align=right width="60px">_azul'+numBOLETO+'</font></td>'+
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
}

if (document.getElementById('txtVLRCARTAO')) {
  var vlrCARTAO = document.getElementById('txtVLRCARTAO').value.rtrim().ltrim();
  vlrCARTAO = vlrCARTAO=='' ? '0' : vlrCARTAO ; vlrCARTAO = parseFloat(vlrCARTAO.replace(',','.'), 10);
  if (vlrCARTAO==0) {
    alert('Preencha o valor do cartão'); document.getElementById('txtVLRCARTAO').focus(); return;
  }   
  vlrCARTAO = vlrCARTAO.toFixed(2).toString().replace('.',',');
  
  modo='CARTÃO';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrCARTAO+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
}

if (document.getElementById('txtDINHEIRO')) {

  if ( document.getElementById('tituloDIALOGO').innerHTML.indexOf('Internet')!=-1 )  
    modo='INTERNET';
  else
    modo='DINHEIRO';  

  var vlrDINHEIRO = document.getElementById('txtDINHEIRO').value.rtrim().ltrim();
  vlrDINHEIRO = vlrDINHEIRO=='' ? '0' : vlrDINHEIRO ; vlrDINHEIRO = parseFloat(vlrDINHEIRO.replace(',','.'), 10);
  if (vlrDINHEIRO==0) {
    alert('Preencha o valor em dinheiro'); document.getElementById('txtDINHEIRO').focus(); return;
  }   
  vlrDINHEIRO = vlrDINHEIRO.toFixed(2).toString().replace('.',',');
  
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrDINHEIRO+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
}


if (document.getElementById('txtVLRVALE')) {
  idREPRE = document.getElementById('txtREL_REPRESENTANTE').value.trim();
  nomeREPRE=document.getElementById('lblREL_REPRESENTANTE').innerHTML;
  if (idREPRE=='' || nomeREPRE.indexOf('ERRO')!=-1) {
    alert('Identifique o corretor');
    document.getElementById('txtREL_REPRESENTANTE').focus();
    return;
  }
  
  var vlrVALE = document.getElementById('txtVLRVALE').value.rtrim().ltrim();
  vlrVALE = vlrVALE=='' ? '0' : vlrVALE ; vlrVALE = parseFloat(vlrVALE.replace(',','.'), 10);
  if (vlrVALE==0) {
    alert('Preencha o valor do vale'); document.getElementById('txtVLRVALE').focus(); return;
  }   
  vlrVALE = vlrVALE.toFixed(2).toString().replace('.',',');
  
  modo='VALE';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaCorretor: </font></td><td align=left width="300px">_azul'+nomeREPRE+' ('+idREPRE+')</font></td>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrVALE+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
} 
if (document.getElementById('txtVALE_PGTO')) {
  var vlrVALE = document.getElementById('txtVALORVALECREDITO').value.rtrim().ltrim();
  vlrVALE = vlrVALE=='' ? '0' : vlrVALE ; vlrVALE = parseFloat(vlrVALE.replace(',','.'), 10);
  if (vlrVALE==0) {
    alert('Preencha o valor a ser debitado do vale crédito'); document.getElementById('txtVALORVALECREDITO').focus(); return;
  }   
  vlrDISPONIVEL = parseFloat(document.getElementById('lblDISPONIVELVALECREDITO').innerHTML.replace(',','.'), 10);

  var conta=parseFloat((vlrDISPONIVEL-vlrVALE).toFixed(2), 10);

  if ( conta < 0 ) {
    alert('Saldo insuficiente do vale crédito');
    return;
  }
  else if ( conta == 0 ) {
 /*
    if (! confirm('O vale crédito nº '+document.getElementById('txtVALE_PGTO').value+' está sendo quitado. Confirma?' )) return;
  */
  }

  vlrVALE = vlrVALE.toFixed(2).toString().replace('.',',');


  modo='VALE CRÉDITO';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaNº: </font></td><td align=left width="80px">_azul'+numVALE+'</font></td>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrVALE+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
  }
}


if (linEDITANDO!=-1) { 
  tab.rows[linEDITANDO].cells[0].innerHTML = modo;
  tab.rows[linEDITANDO].cells[1].innerHTML = detalhes;

  if (document.getElementById('txtVLRCH')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = document.getElementById('txtCHEQUE').value;   
	 tab.rows[linEDITANDO].cells[4].innerHTML = idBANCO;   	 
	 tab.rows[linEDITANDO].cells[5].innerHTML = nomeBANCO; 
	 tab.rows[linEDITANDO].cells[6].innerHTML = document.getElementById('txtDATACH').value;   
	 tab.rows[linEDITANDO].cells[7].innerHTML = vlrCH;      	 
	 tab.rows[linEDITANDO].cells[8].innerHTML = document.getElementById('txtINFOCH').value;
	 tab.rows[linEDITANDO].cells[9].innerHTML = document.getElementById('txtNOMECH').value;
  }
  if (document.getElementById('txtVLRBOLETO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = vlrBOLETO;      	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = dataBOLETO;
	 tab.rows[linEDITANDO].cells[5].innerHTML = numBOLETO;
	 tab.rows[linEDITANDO].cells[6].innerHTML = document.getElementById('txtSACADO').value;
	 tab.rows[linEDITANDO].cells[7].innerHTML = document.getElementById('txtCPFBOLETO').value;
  }
  if (document.getElementById('txtVLRCARTAO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = vlrCARTAO;      	 
  }
  if (document.getElementById('txtDINHEIRO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = vlrDINHEIRO;      	 
  }
  if (document.getElementById('txtVLRVALE')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = idREPRE;   	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = nomeREPRE; 
	 tab.rows[linEDITANDO].cells[5].innerHTML = vlrVALE;      	 
  }
  if (document.getElementById('txtVALE_PGTO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = vlrVALE.replace(',','.');   	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = numVALE;
  }   	
}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'PGTO_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = modo; col.align='left'; col.width = '15%';
	col = lin.insertCell(-1); col.innerHTML = detalhes; col.align='left'; col.width = '80%';
	
  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onclick = function() { removePGTO(lin.id); return;}  	

  if (document.getElementById('txtVLRCH')) {
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtCHEQUE').value;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = idBANCO;   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = nomeBANCO;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtDATACH').value;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = vlrCH;   col.style.display='none';      	 
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtINFOCH').value;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtNOMECH').value;   col.style.display='none';

  }
  if (document.getElementById('txtVLRBOLETO')) {
    col = lin.insertCell(-1); col.innerHTML = vlrBOLETO;   col.style.display='none';      	 
    col = lin.insertCell(-1); col.innerHTML = dataBOLETO;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = numBOLETO;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtSACADO').value;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtCPFBOLETO').value;   col.style.display='none';

  }
  if (document.getElementById('txtVLRCARTAO')) {
    col = lin.insertCell(-1); col.innerHTML = vlrCARTAO;   col.style.display='none';      	 
  }
  if (document.getElementById('txtDINHEIRO')) {
    col = lin.insertCell(-1); col.innerHTML = vlrDINHEIRO;   col.style.display='none';      	 
  }
  if (document.getElementById('txtVLRVALE')) {
    col = lin.insertCell(-1); col.innerHTML = idREPRE;   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = nomeREPRE;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = vlrVALE;   col.style.display='none';      	 
  }
  if (document.getElementById('txtVALE_PGTO')) {
    col = lin.insertCell(-1); col.innerHTML = vlrVALE.replace(',','.');   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = numVALE;   col.style.display='none';
  }   	
   	
     	
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onclick = function() {editarPGTO(this.id); }    
  lin.width='100%';
}
atlVLR_DEVIDO();
if (document.getElementById('txtVLRCH')) limparCmpsCheque();
if (document.getElementById('txtVLRBOLETO')) limparCmpsBoleto();
if (document.getElementById('txtVLRCARTAO')) limparCmpsCartao();
if (document.getElementById('txtDINHEIRO')) limparCmpsDinheiro();
if (document.getElementById('txtVLRVALE')) limparCmpsVale();
if (document.getElementById('txtVALE_PGTO')) limparCmpsValeCredito();
fecharPGTO();
}

/*******************************************************************************/
function editarPGTO(idLinEditar ) {

var tab = document.getElementById('tabPGTO');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  tab.rows[x].style.color='black';
  tab.rows[x].style.fontWeight='normal';
  
  if (idLIN == idLinEditar) {  
    if (tab.rows[x].cells[0].innerHTML.trim()=='CHEQUE') {
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_CHEQUE', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtCHEQUE').value = tab.rows[x].cells[3].innerHTML; 
      document.getElementById('txtBANCO').value = tab.rows[x].cells[4].innerHTML;
      document.getElementById('lblBANCO').innerHTML = tab.rows[x].cells[5].innerHTML;
      document.getElementById('txtDATACH').value = tab.rows[x].cells[6].innerHTML;
      document.getElementById('txtVLRCH').value = tab.rows[x].cells[7].innerHTML;
      document.getElementById('txtINFOCH').value = tab.rows[x].cells[8].innerHTML;
      document.getElementById('txtNOMECH').value = tab.rows[x].cells[9].innerHTML;
        
      setTimeout("document.getElementById('txtCHEQUE').focus()", 200);      
    }
    
    if (tab.rows[x].cells[0].innerHTML.trim()=='BOLETO') {
      /* se boleto ja pago, nao permite alterar */
      if ( tab.rows[x].cells[1].innerHTML.trim().indexOf('por:')!=-1) {
        alert('Boleto já pago. Para alterar/excluir, primeiramente cancele seu pagamento'); 
        return;
      }
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_BOLETO', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtVLRBOLETO').value = tab.rows[x].cells[3].innerHTML;
      document.getElementById('txtDATABOLETO').value = tab.rows[x].cells[4].innerHTML;
      document.getElementById('txtNUMEROBOLETO').value = tab.rows[x].cells[5].innerHTML;
      document.getElementById('txtSACADO').value = tab.rows[x].cells[6].innerHTML;
      document.getElementById('txtCPFBOLETO').value = tab.rows[x].cells[7].innerHTML;

        
      setTimeout("document.getElementById('txtVLRBOLETO').focus()", 200);      
    }
    
    if (tab.rows[x].cells[0].innerHTML.trim()=='CARTÃO') {
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_CARTAO', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtVLRCARTAO').value = tab.rows[x].cells[3].innerHTML;
        
      setTimeout("document.getElementById('txtVLRCARTAO').focus()", 200);      
    }

    if (tab.rows[x].cells[0].innerHTML.trim()=='DINHEIRO' || tab.rows[x].cells[0].innerHTML.trim()=='INTERNET') {
      showAJAX(1);
      if (  tab.rows[x].cells[0].innerHTML.trim()=='DINHEIRO') 
        ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_DINHEIRO', '', 0);
      else
        ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_INTERNET', '', 0);              
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtDINHEIRO').value = tab.rows[x].cells[3].innerHTML;
        
      setTimeout("document.getElementById('txtDINHEIRO').focus()", 200);      
    }
    

    if (tab.rows[x].cells[0].innerHTML.trim()=='VALE') {
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_VALE', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtREL_REPRESENTANTE').value = tab.rows[x].cells[3].innerHTML;
      document.getElementById('lblREL_REPRESENTANTE').innerHTML = tab.rows[x].cells[4].innerHTML;
      document.getElementById('txtVLRVALE').value = tab.rows[x].cells[5].innerHTML;
        
      setTimeout("document.getElementById('txtREL_REPRESENTANTE').focus()", 200);      
    }
    if (tab.rows[x].cells[0].innerHTML.trim()=='VALE CRÉDITO') {
      showAJAX(1);
      ajax.criar('ajax/ajaxCAIXA.php?acao=addPGTO_VALE_CREDITO', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtVALE_PGTO').value = tab.rows[x].cells[4].innerHTML;
      document.getElementById('txtVALORVALECREDITO').value = tab.rows[x].cells[3].innerHTML.replace('.',',');

      setTimeout("document.getElementById('txtVALE_PGTO').focus()", 200);      
    }
    tab.rows[x].style.color='blue';
    tab.rows[x].style.fontWeight='bold';
  }
}
document.getElementById('pgtoEDITANDO').value=idLinEditar;
}
/*******************************************************************************/
function rel() { 

dataTELA = document.getElementById('dataTRAB').value;
data2 = new Date(data.substring(4, 10), data.substring(2, 4), data.substring(0, 2));

dataLER = dataTELA.substring(4, 10)+dataTELA.substring(2, 4)+dataTELA.substring(0, 2);
dataMOSTRAR = dataTELA.substring(0, 2)+'/'+dataTELA.substring(2, 4)+'/'+dataTELA.substring(6, 10);

showAJAX(1);
ajax.criar('rel/ajaxRELS.php?acao=caixa_detalhado&DATAINI=' + dataLER+'&DATAFIN='+dataLER+
    '&dataIniMostrar='+dataMOSTRAR+'&dataFinMostrar='+dataMOSTRAR, '', 0);
showAJAX(0);

/*alert(ajax.ler());return;*/
if (ajax.ler().indexOf('nada')!=-1)
  alert('Nenhum registro encontrado');
else
  window.open('pdf/rel_PAISAGEM.php', 'nome', 'width=10,height=10' );	
}


/*******************************************************************************/
function menuPSQ() {
var divPESQUISA = document.getElementById('divPESQUISA'); divPESQUISA.setAttribute(propCLASSE, 'cssDIV_BAIXAR');    
}

/*******************************************************************************/
function menuREL() {
var divREL = document.getElementById('divRELATORIOS'); divREL.setAttribute(propCLASSE, 'cssDIV_BAIXAR');    
}



/*******************************************************************************/
function pesquisa(item) {

var cmp='';
if (item==1) cmp='CPF/CNPJ (PREENCHA SOMENTE OS NUMEROS, SEM PONTO, TRAÇO)';
if (item==2) cmp='Nº DO VALE CRÉDITO';
if (item==3) cmp='Nº OPERAÇÃO DO CAIXA';
if (item==4) cmp='Nº BOLETO';
if (item==5) cmp='CPF ou NOME DO SACADO BOLETO';

resp = prompt(cmp, '');

if (resp==null) return;
if (resp.trim()=='') return;

fechaPESQUISA();

if (item==5) {
  showAJAX(1);
  ajax.criar('ajax/ajaxCAIXA.php?acao=pesqCPF_SACADO&vlr='+resp, '', 0);  
  showAJAX(0);

  if (ajax.ler()=='nada') {
    alert('Informação não encontrada'); return;
  }
  document.getElementById('dataTRAB').value = ajax.ler().split(';')[0];
  document.getElementById('forcarSELECAO').value=ajax.ler().split(';')[1];
  lerREGS();
  return;
}
document.getElementById('SELECAO').value='';
document.getElementById('pesqPALAVRA').value=cmp;


showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=lerREGS&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2='+item+
  '&vlr3='+resp+'&excluidas='+
    document.getElementById('vendoEXCLUIDAS').value, desenhaTabela);
}

/*******************************************************************************/
function preparaVALECREDITO() {

var tabPROP = document.getElementById('tabPROPOSTAS');
if (tabPROP) {
  nomeREPRE='';
  for (var f=0; f<tabPROP.rows.length; f++) {
    var idREPRE=tabPROP.rows[f].cells[8].innerHTML;
    var nomeREPRE=tabPROP.rows[f].cells[10].innerHTML;
  }
}
var somaPAGO=0;
var tab = document.getElementById('tabPGTO');
for (var r=0; r<tab.rows.length; r++) {
  tipo= tab.rows[r].cells[0].innerHTML;

/*  if (tipo=='CHEQUE') somaCH += parseFloat(tab.rows[r].cells[7].innerHTML.replace(',','.'), 10);*/
  if (tipo=='CHEQUE') coluna=7;
  if (tipo=='BOLETO') coluna=3;  
  if (tipo=='CARTÃO') coluna=3;  
  if (tipo=='DINHEIRO') coluna=3;
  if (tipo=='VALE') coluna=5;  
  if (tipo=='VALE CRÉDITO') coluna=3;

  valor=tab.rows[r].cells[coluna].innerHTML.replace(',','.');
  somaPAGO += parseFloat(valor, 10);
}

/* verifica na lista de cred/deb se ha vale(s) credito, se houver, leva em consideracao
para calculo do valor do (proximo) vale credito */
var valeCREDITO = 0;
var tabVALES = document.getElementById('tabVALES');
for (var x=0; x<tabVALES.rows.length; x++) {
  if (tab.rows[x].cells[0].innerHTML.indexOf('Vale Crédito')!=-1) {
    vlr = tab.rows[x].cells[3].innerHTML.replace(',','.');
    vlr = parseFloat(vlr, 10);
    valeCREDITO += vlr;
  }
}
somaPAGO -= valeCREDITO;

if (document.getElementById('txtPAGAR_VALE').value=='') {
  var data = document.getElementById('txtDATA').value;
  var ano=data.substring(6, 8);
  data = data.substring(0, 2)+ data.substring(3, 5) + '20'+data.substring(6, 8);

  var data2 = new Date(data.substring(4, 10), parseInt(data.substring(2, 4),10)-1, data.substring(0, 2));

  data2.addDays(+1);
  var dia=data2.getDate(); if (dia.toString().length<2) dia = '0'+dia;
  var mes=data2.getMonth()+1; if (mes.toString().length<2) mes = '0'+mes;

  document.getElementById('txtPAGAR_VALE').value = dia+'/'+mes+'/'+ano;
}

if (tabPROP) {
  var recebido = parseFloat(document.getElementById('lblTOTAL_DEVE').innerHTML.replace(',','.'), 10);
  
  somaPAGO -= recebido;
  
  somaPAGO = somaPAGO.toFixed(2).toString().replace('.',',');
  
  if (document.getElementById('txtVALOR_VALE').value=='' || document.getElementById('txtVALOR_VALE').value=='0,00') 
      document.getElementById('txtVALOR_VALE').value=somaPAGO;
  if (document.getElementById('txtREL_REPRESENTANTE2').value=='' && nomeREPRE!='') {
      document.getElementById('lblREL_REPRESENTANTE2').innerHTML=nomeREPRE;
      document.getElementById('txtREL_REPRESENTANTE2').value=idREPRE;
  }
} 
document.getElementById('txtVALE_CREDITO').focus();
atlVLR_DEVIDO();
}


/********************************************************************************/
function pagarBOLETO(idBOLETO,operacao)  {
showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=verPodePagarBoleto', '', 0);  
showAJAX(0);

if (ajax.ler()=='nao') {
  alert('Você não tem autorização a pagar/cancelar pagamento de boletos');return;
}  
if (operacao==1) {
  if (!confirm('Confirma pagamento deste boleto?')) {
    return;
  }
  var sug=lerHOJE();
  sug = sug.replace('/20', '/');
  var data=prompt('Data de pagamento do boleto (formato dd/mm/yy):',sug);
  
  if (data==null) return;
  if (data.rtrim()=='') return;
  
  dataDIG=data.substring(0, 2)+'/'+data.substring(3, 5)+'/20'+data.substring(6, 9);
  data=data.substring(0, 2)+'/'+data.substring(3, 5)+'/20'+data.substring(6, 9);
  dataVER=data.substring(0, 2)+'/'+data.substring(3, 5)+'/20'+data.substring(6, 9);

  if (! verifica_data('ITSELF_'+data) || data.length<10)  {
    alert('Data inválida');
    return;
  }
  data=data.substring(6, 11)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);


  var one_day=1000*60*60*24

  var today=new Date();
  var dataVLR=new Date(parseInt(dataDIG.substr(6, 5),10), parseInt(dataDIG.substring(3, 5),10)-1, parseInt(dataDIG.substring(0, 2),10));

  var dif = Math.ceil((dataVLR.getTime()-today.getTime())/(one_day))
  if (dif>0) {
    alert('Digite uma data igual ou anterior a hoje'); return;
  }

  vlrREPRE=-1; idREPRE=-1;
  var gerar=prompt('Digite 1 para gerar CRÉDITO \n\nou 2 para NÃO GERAR\n\n',1);
  if (gerar=='1') {
    /* soma vlr respectivo do corretor, para gerar credito ou vale credito */
    var tab = document.getElementById('tabPROPOSTAS');
    var vlrRECEBIDO=0; vlrPRESTADORA=0; vlrADESAO=0; vlrREPRE=0;
    for (var f=0; f<tab.rows.length; f++) {
      vlrRECEBIDO += parseFloat(tab.rows[f].cells[4].innerHTML.replace(',','.'), 10);
      vlrPREST= tab.rows[f].cells[5].innerHTML; vlrPREST=vlrPREST.substring(0, vlrPREST.indexOf('(')).replace(',','.');
      vlrPRESTADORA += parseFloat(vlrPREST, 10);
      vlrADESAO += parseFloat(tab.rows[f].cells[6].innerHTML.replace(',','.'), 10);
  
      vlrREPRE += vlrRECEBIDO - (vlrPRESTADORA + vlrADESAO);
      idREPRE= tab.rows[f].cells[8].innerHTML;
    }
  }
  url='ajax/ajaxCAIXA.php?acao=pagarBOLETO&vlr='+idBOLETO+'&data='+data+'&vlr2='+vlrREPRE+'&idREPRE='+idREPRE;
}
else {
  if (!confirm('Cancelar o pagamento deste boleto?')) {
    return;
  }
  url='ajax/ajaxCAIXA.php?acao=cancelarPGTOBOLETO&vlr='+idBOLETO;
}
showAJAX(1);
ajax.criar(url, '', 0);  
showAJAX(0);
if (ajax.ler()=='ok') {fecharEDICAO();editarREG();} 
}

/********************************************************************************/
function alternarESCRITORIO() {
var esc = document.getElementById('tdESCRITORIO');
escritorioANTERIOR = esc.innerHTML;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=lerESCRITORIOS', '', 0);
showAJAX(0);

esc.innerHTML  = ajax.ler();
}

/********************************************************************************/
function voltaESCRITORIO() {
document.getElementById('tdESCRITORIO').innerHTML = escritorioANTERIOR;
}

/********************************************************************************/
function acessarESCRITORIO() {
var lstbox = document.getElementById('lstESCRITORIOS');
var idESCRITORIO = lstbox[lstbox.selectedIndex].value;
var letraESCRITORIO = lstbox[lstbox.selectedIndex].title;

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=setarESCRITORIO&vlr='+letraESCRITORIO, '', 0);
showAJAX(0);

document.getElementById('hidESCRITORIO').value = idESCRITORIO;


lerREGS();
}

/********************************************************************************/
function verificada() {

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=verEscritorioCentral', '', 0);
showAJAX(0);

if (ajax.ler()=='naoDEF') {
  alert('Erro - Escritório central não definido');
  return;
}

escCENTRAL = ajax.ler();

if (!confirm('Esta operação será validada e registrada na data de hoje, \nno escritório central ('+escCENTRAL+')\n\nContinua?\n\n')) return;
showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=verificada&vlr='+document.getElementById('numREG').value, '', 0);
showAJAX(0);

if (ajax.ler()=='semACESSO') {
  alert('Você não tem permissão para conferir caixa');
  return;
}
fecharEDICAO();
lerREGS();
}

/********************************************************************************/
function buscaCHEQUES() {

data = document.getElementById('dataTRAB').value;
var data2 = new Date(parseInt(data.substring(4, 10),10), parseInt(data.substring(2, 4),10)-1, parseInt(data.substring(0, 2),10));

var dia=data2.getDate(); if (dia.toString().length<2) dia = '0'+dia;
var mes=data2.getMonth()+1; if (mes.toString().length<2) mes = '0'+mes;

document.getElementById('dataTRAB').value = dia+''+mes+''+data2.getFullYear();
dataLER = data2.getFullYear()+''+mes+''+dia; 

showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=buscaCHEQUES&vlr='+dataLER, '', 0);
showAJAX(0);

if (ajax.ler()=='nada') 
  alert('Nenhum cheque recebido em operações de entrada hoje'); 
else{
  var divTRAB = document.getElementById('divTabPAGAMENTOS');
  divTRAB.innerHTML = ajax.ler();
}

document.getElementById('txtFUNCIONARIO').focus();
}

/*******************************************************************************/
function policia() {
document.getElementById('vendoEXCLUIDAS').value = 
  document.getElementById('vendoEXCLUIDAS').value==''? 'sim' : '';

document.getElementById('tablePOLICIA').border = 
  document.getElementById('vendoEXCLUIDAS').value=='' ? 0 : '3';

document.getElementById('tablePOLICIA').title = 
  document.getElementById('vendoEXCLUIDAS').value=='' ? 'Ver excluidas/alteradas (NAO VENDO)' : 'Ver excluidas/alteradas (VENDO)';

lerREGS(); 
}

/*******************************************************************************/
function editarCRED_DEB(idLinEditar ) {

var tab = document.getElementById('tabVALES');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN == idLinEditar) {  
    /* alguns cred/debitos estao listados por exemplo, oriundos de um adto salarial,
      quem vai excluir/incluir novamente este tipo de cred/deb é a propria operacao d adto salarial,
      no momenhto que o sistema perceber que é uma operacao que suscita automaticamwente um debito, ele mesmo cuida disso
    -- as linhas abaixo concatenam cred/deb inseridos manualmente pelo usuario, ve-se se foram inseridos manualmente pela coluna 10
      (descricao do cred/deb - infelizmente devido a alteracoes no sistema, sei que é fragil, mas é a unica maneira
      sabermos quem é inserido manual, quem automatico */
    var desc=tab.rows[x].cells[10].innerHTML;
    if (desc=='PROPOSTA(S) PAGA(S) COM BOLETO' || desc=='PROPOSTA(S) PAGA(S) COM VALE' ||
       desc=='ADIANTAMENTO SALARIAL' || desc=='ADIANTAMENTO DE COMISSÃO' ) {
      alert('Este crédito/débito foi gerado automaticamente, devido a uma operação do \n\ntipo ADIANTAMENTO SALARIAL, PAGAMENTO DE PROPOSTA COM VALE, etc\n\n'+
            'e só pode ser alterado/excluído se a operação que o gerou for alterada');
      return;
    }

    tab.rows[x].style.color='blue';
    tab.rows[x].style.fontWeight='bold';    
    
    if (tab.rows[x].cells[0].innerHTML.indexOf('Débito')!=-1)
      document.getElementById('txtTIPO_VALE').value = 'D';
    else
      document.getElementById('txtTIPO_VALE').value = 'C';

    document.getElementById('txtVALE_CREDITO').value = tab.rows[x].cells[1].innerHTML; 
    document.getElementById('txtREL_REPRESENTANTE2').value = tab.rows[x].cells[7].innerHTML;
    document.getElementById('lblREL_REPRESENTANTE2').innerHTML = tab.rows[x].cells[8].innerHTML;

    document.getElementById('txtVALOR_VALE').value = tab.rows[x].cells[3].innerHTML;
    document.getElementById('txtDESCONTO_VALE').value = tab.rows[x].cells[4].innerHTML;
    document.getElementById('txtPAGAR_VALE').value = tab.rows[x].cells[5].innerHTML;

    document.getElementById('txtDESCRICAO_VALE').value = tab.rows[x].cells[10].innerHTML;

    document.getElementById('valeEDITANDO').value=idLinEditar;
  }  
}
setTimeout("document.getElementById('txtTIPO_VALE').focus()", 200);
}

/*******************************************************************************/
function addVALE_CREDITO() {
var tab = document.getElementById('tabVALES');

linEDITANDO=-1;
var maiorID = -1;
for (var f=0; f<=tab.rows.length-1; f++) {
  var idLIN = tab.rows[f].id.replace('VALE_', '');
    
  if (parseInt(idLIN, 10) > maiorID) maiorID = parseInt(idLIN, 10);
  if (document.getElementById('valeEDITANDO').value==tab.rows[f].id) linEDITANDO=f;
}
document.getElementById('valeEDITANDO').value='';
var erro='';

tipo = document.getElementById('txtTIPO_VALE').value.toUpperCase();
if (tipo!='C' && tipo!='D') {
  alert('Use\n\nC= crédito\nD= débito\n\n');
  document.getElementById('txtTIPO_VALE').focus();
  return;
}

if (tipo=='D' && document.getElementById('txtVALE_CREDITO').value.rtrim().ltrim()!='') {
  alert('Não preencha o campo Nº, quando tipo= Débito');
  document.getElementById('okVALE_CREDITO').innerHTML=''; 
  document.getElementById('txtVALE_CREDITO').focus();
  return;
} 

idREPRE = document.getElementById('txtREL_REPRESENTANTE2').value.trim();
nomeREPRE=document.getElementById('lblREL_REPRESENTANTE2').innerHTML;
if (idREPRE=='' || nomeREPRE.indexOf('ERRO')!=-1) {
  alert('Identifique o corretor do crédito/débito');
  document.getElementById('txtREL_REPRESENTANTE2').focus();
  return;
}

var valor = document.getElementById('txtVALOR_VALE').value.rtrim().ltrim();
valor = valor=='' ? '0' : valor ; valor = parseFloat(valor.replace(',','.'), 10);   
valor = valor.toFixed(2).toString().replace('.',',');

var desconto = document.getElementById('txtDESCONTO_VALE').value.rtrim().ltrim();
desconto = desconto=='' ? '0' : desconto ; desconto = parseFloat(desconto.replace(',','.'), 10);   
desconto = desconto.toFixed(2).toString().replace('.',',');

if ( document.getElementById('txtPAGAR_VALE').value.trim()=='' || ! verifica_data('txtPAGAR_VALE') )   {
  alert('Data para pagar crédito/débito inválida');
  document.getElementById('txtPAGAR_VALE').focus();
  return;
}

cTIPO = document.getElementById('txtTIPO_VALE').value.toUpperCase();
if (cTIPO=='D') tipo='Débito';
else {
  if (document.getElementById('txtVALE_CREDITO').value.trim()=='') tipo='Crédito';
  else tipo='Vale Crédito';
}
if (linEDITANDO!=-1) { 
  tab.rows[linEDITANDO].cells[0].innerHTML = tipo;
  tab.rows[linEDITANDO].cells[1].innerHTML = document.getElementById('txtVALE_CREDITO').value.trim();
  tab.rows[linEDITANDO].cells[2].innerHTML = nomeREPRE+' ('+idREPRE+')';
  tab.rows[linEDITANDO].cells[3].innerHTML = valor;
  tab.rows[linEDITANDO].cells[4].innerHTML = desconto;
  tab.rows[linEDITANDO].cells[5].innerHTML = document.getElementById('txtPAGAR_VALE').value.trim();              
  tab.rows[linEDITANDO].cells[7].innerHTML = idREPRE;
  tab.rows[linEDITANDO].cells[8].innerHTML = nomeREPRE;  
  tab.rows[linEDITANDO].cells[10].innerHTML = document.getElementById('txtDESCRICAO_VALE').value.trim();
}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'VALE_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = tipo;  col.width = '20%'; col.align='left';
	var col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtVALE_CREDITO').value.trim();  col.width = '10%'; col.align='left';
	var col = lin.insertCell(-1); col.innerHTML = nomeREPRE+' ('+idREPRE+')';  col.width = '35%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = valor;  col.width = '10%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = desconto;  col.width = '10%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtPAGAR_VALE').value.trim();  col.width = '15%'; col.align='right';	
	   	
  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onclick = function() { removeCRED_DEB(lin.id); }  	
  
  col = lin.insertCell(-1); col.innerHTML = idREPRE; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = nomeREPRE; col.style.display='none';
  /* coluna INSERIDO MANUALMENTE */
  col = lin.insertCell(-1); col.innerHTML = '1'; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtDESCRICAO_VALE').value.trim(); col.style.display='none';
  
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onclick = function() {editarCRED_DEB(this.id); }    
  lin.width='100%';
}
atlVLR_DEVIDO();
limparCmpsCreditoDebito();    
}

/*******************************************************************************/
function limparCmpsCreditoDebito() {

document.getElementById('txtTIPO_VALE').value = '';
document.getElementById('txtVALE_CREDITO').value = ''; 
document.getElementById('txtREL_REPRESENTANTE2').value = '';
document.getElementById('lblREL_REPRESENTANTE2').innerHTML = '';

document.getElementById('txtVALOR_VALE').value = '';
document.getElementById('txtDESCONTO_VALE').value = '';
document.getElementById('txtPAGAR_VALE').value = '';
document.getElementById('txtDESCRICAO_VALE').value = '';
document.getElementById('okVALE_CREDITO').innerHTML='';


var tab = document.getElementById('tabVALES');
for (var y=0; y<=tab.rows.length-1; y++) {
  if (tab.rows[y].style.color!='grey') {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
}
document.getElementById('valeEDITANDO').value='';
setTimeout("document.getElementById('txtREPRESENTANTE').focus()", 200);
}


/*******************************************************************************/
function limparCmpsPropEnt() {

document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
document.getElementById('txtTIPO_CONTRATO').value=''; document.getElementById('lblTIPO_CONTRATO').innerHTML='';
document.getElementById('txtCPF').value='';
document.getElementById('txtADESAO').value='';
document.getElementById('txtVALOR').value='';
document.getElementById('txtRECEBIDO').value='';

var tab = document.getElementById('tabPROPOSTAS');
for (var y=0; y<=tab.rows.length-1; y++) {
  tab.rows[y].style.color='black';
  tab.rows[y].style.fontWeight='normal';
}
document.getElementById('propEDITANDO').value='';
setTimeout("document.getElementById('txtREPRESENTANTE').focus()", 200);
}

/*******************************************************************************/
function removeCRED_DEB(idLinExcluir)    {


var tab = document.getElementById('tabVALES');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN==idLinExcluir) {
    /* alguns cred/debitos estao listados por exemplo, oriundos de um adto salarial,
      quem vai excluir/incluir novamente este tipo de cred/deb é a propria operacao d adto salarial,
      no momenhto que o sistema perceber que é uma operacao que suscita automaticamwente um debito, ele mesmo cuida disso
    -- as linhas abaixo concatenam cred/deb inseridos manualmente pelo usuario, ve-se se foram inseridos manualmente pela coluna 10
      (descricao do cred/deb - infelizmente devido a alteracoes no sistema, sei que é fragil, mas é a unica maneira
      sabermos quem é inserido manual, quem automatico */
    var desc=tab.rows[x].cells[10].innerHTML;
    if (desc=='PROPOSTA(S) PAGA(S) COM BOLETO' || desc=='PROPOSTA(S) PAGA(S) COM VALE' ||
       desc=='ADIANTAMENTO SALARIAL' || desc=='ADIANTAMENTO DE COMISSÃO' ) {
      alert('Este crédito/débito foi gerado automaticamente, devido a uma operação do \n\ntipo ADIANTAMENTO SALARIAL, PAGAMENTO DE PROPOSTA COM VALE, etc\n\n'+
            'e só pode ser alterado/excluído se a operação que o gerou for alterada');
      return;
    }


    tab.deleteRow( x );break;
  }
}
atlVLR_DEVIDO();
limparCmpsCreditoDebito();
}

/*******************************************************************************/
function valida_cnpj(cnpj)       {
var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
digitos_iguais = 1;

cnpj = cnpj.replace('.', '');
cnpj = cnpj.replace('.', '');
cnpj = cnpj.replace('.', '');
cnpj = cnpj.replace('.', '');

cnpj = cnpj.replace('-', '');
cnpj = cnpj.replace('/', '');


if (cnpj.length < 14 && cnpj.length < 15)
      return false;
for (i = 0; i < cnpj.length - 1; i++)
      if (cnpj.charAt(i) != cnpj.charAt(i + 1))
            {
            digitos_iguais = 0;
            break;
            }
if (!digitos_iguais)
      {
      tamanho = cnpj.length - 2
      numeros = cnpj.substring(0,tamanho);
      digitos = cnpj.substring(tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (i = tamanho; i >= 1; i--)
            {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                  pos = 9;
            }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(0))
            return false;
      tamanho = tamanho + 1;
      numeros = cnpj.substring(0,tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (i = tamanho; i >= 1; i--)
            {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                  pos = 9;
            }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(1))
            return false;
      return true;
      }
else
      return false;
}

/*******************************************************************************/
function pendentes()       {
showAJAX(1);
ajax.criar('ajax/ajaxCAIXA.php?acao=lerREGS&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2=6&excluidas='+
    document.getElementById('vendoEXCLUIDAS').value+'&vlr3=nada', desenhaTabela);
}

//]]>



function atualizaTranspostado()
{
	var txtValorDinheiro = $("#txtValorDinheiro").val();
	var txtValorCheque = $("#txtValorCheque").val();

	$.ajax({
		type: 'get',
		url: 'ajax/ajaxCAIXA.php?acao=atualizaTranspostado&txtValorDinheiro=' + txtValorDinheiro + '&txtValorCheque=' + txtValorCheque,
		cache: false,
		dataType: 'html',
		data: $("").serialize(),
		success: function(data) {
			alert(data);
			//window.frames['framePRINCIPAL'].location.href='caixa.php';
			lerHOJE();
			lerREGS();
			Avisa('');
			Muda_CSS();
			ColocaFocoCmpInicial();
		},
		beforeSend: function(data) {
			$.fancybox.showLoading();
		},
		complete: function(data){
			$.fancybox.hideLoading();
		},
		error: function(data){
			alert(data.responseText);
		},
	});
	
	return false;
}

</script>
</body>
</html>