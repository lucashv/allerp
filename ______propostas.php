<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/encoder.js" xml:space="preserve"></script>

<!-- Folha de estilos do calendário -->
<link rel="stylesheet" type="text/css" media="all" href="js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a função Calendar.setup, que ajuda a gerar um calendário em poucas linhas de código -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar-setup.js"></script> 

<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 950px; height: 300px;	
margin-top: -295px; margin-left: -480px; display:block; z-index:3;}
 
.cssDIV_FUTURAS  {
position: absolute; top: 200px;  width: 980px; height: 420px;	
margin-top: -230px; margin-left: -490px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

.cssDIV_BAIXAR {
position: absolute; top: 200px;  width: 600px; height: 290px;	
margin-top: -200px; margin-left: -300px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}

</style>


</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerOPERADORAS();lerHOJE();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu_MAIOR">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>  
  <li><a href="javascript:futuras();">Futuras</a></li>  
  <li><a href="javascript:menuPSQ();">Pesquisa</a></li>  
  <li><a href="javascript:cancelar();">Cancelar</a></li>  
  <li><a href="javascript:cadastro();">Alterar data de cadastro</a></li>
  <li><a href="javascript:pendente(0);">Marcar como pendente</a></li>
  <li><a href="javascript:pendentes();">Lista propostas pendentes</a></li>
</ul>

<form id="frmPROPOSTAS" name="frmPROPOSTAS" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>

<div id="divPROPENTREGUE" style="display:none">
  <img src='images/entregueCAIXA.png' />
</div>

<div id="divFUTURAS" class="cssDIV_ESCONDE" >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="90%"><span class="lblTitJanela" id="infoFUTURAS">&nbsp;&nbsp;&nbsp;FUTURAS</td>
    <td onclick="fechaFUTURAS();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

  <tr><td colspan="2" valign="top" height="20px" id="titFUTURAS"></td></tr>
  <tr><td colspan="2" valign="top" height="370px">
    <div id="tabFUTURAS" style="overflow:auto;min-height:95%;height:95%">
  </td></tr>

  </table>  
</div>

<div id="divESCOLHEPROPENTREGUE" class="cssDIV_ESCONDE" >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="90%"><span class="lblTitJanela" id="infoPROPENTREGUES">&nbsp;&nbsp;&nbsp;</td>
    <td onclick="fechaESCOLHEPROP();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

  <tr><td colspan="2" valign="top" height="20px" id="titPROPENTREGUES"></td></tr>
  <tr><td colspan="2" valign="top" height="210px">
    <div id="tabPROPENTREGUES" style="overflow:auto;min-height:95%;height:95%">
  </td></tr>

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
      <span class="lblTitJanela">1= SEQUÊNCIA</span>
    </td>    
   </tr></table></td></tr>
   
   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(2);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">2= NÚMERO CONTRATO</span>
    </td>    
   </tr></table></td></tr>
   
   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(3);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">3= CONTRATANTE (início do nome)</span>
    </td>    
   </tr></table></td></tr>
   
   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(4);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">4= CONTRATANTE (parte do nome)</span>
    </td>    
   </tr></table></td></tr>
   
   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(5);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">5= CPF/CNPJ CONTRATANTE</span>
    </td>    
   </tr></table></td></tr>

   <tr><td colspan="2"><table width="100%" height="30px"><tr>
    <td align="center" onclick="pesquisa(6);"  
      onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
      onmouseout="this.style.backgroundColor='#E6E8EE';">
      <span class="lblTitJanela">6= PROPOSTAS COM NUMERAÇÃO DUPLICADA E DA MESMA OPERADORA</span>
    </td>    
   </tr></table></td></tr>

   
  </table>  
</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >
<input id="recarregarINC" type="hidden" value="" />


<input id="operadoraATUAL" type="hidden" value="" />

<input id="pesqPALAVRA" type="hidden" value="" />

<input id="dataTRAB" type="hidden" value="" >


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">
      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
      
        <td width="55%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span>
          <input type="text" id="txtDATATRAB" value="" 
              style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
              onchange="lerHOJE(1);lerREGS();";/>        
        </td>
        
      	<td id="btnREL" title="Protocolo" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="rel();" >
      	  <img src="images/protocolo.png" />
      	</td>

      	<td title="Marcar como pendente" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="pendente(0);" >
      	  <img src="images/pendente.png" />
      	</td>

      	<td title="Listar propostas pendentes" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="pendentes();" >
      	  <img src="images/pendentes.png" />
      	</td>

      	<td title="Cancelar pesquisa" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="reler();" >
      	  <img src="images/cancelafiltro.png" />
      	</td>

      	<td title="Alterar data cadastro" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="cadastro();" >
      	  <img src="images/datacadastro.png" />
      	</td>

      	<td title="Mensalidades futuras" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="futuras();" >
      	  <img src="images/futuras.png" />
      	</td>

      	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
      	  <img src="images/buscadata.png" />
      	</td>

      	<td title="Cancelar proposta" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="cancelar();" >
      	  <img src="images/cancelar.png" />
      	</td>
        
        <td id="btnPESQUISAR" title="Pesquisa (F8)"  align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="menuPSQ();" >
          <img src="images/pesquisa.png" />
        </td>

        <td  id="btnRETORNAR" title="Retornar 1 mês  (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >
          <img src="images/setaESQUERDA.png" />
        </td>
        
        <td id="btnAVANCAR" title="Avançar 1 mês (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >
          <img src="images/setaDIREITA.png" />
        </td>    
            
        <td style="cursor: pointer;text-align:right;"  
          onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
          class="lblTitJanela" >[ X ]</span>
        </td>      
      </tr></table></td></tr>

      <tr>
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .60); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>

      <td width="30%"><div id=divOPERADORAS></div></td>

      <tr><td><table width="100%"><tr>
        <td align="left">
          &nbsp;<span class="lblPADRAO">Proposta:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:100px;"  maxlength="30" />
        </td>
        
        <td width="10px"><span style="background-color:red">&nbsp;&nbsp;&nbsp;</span></td>
        <td><span class="lblPADRAO">= cancelada</span></td>

        <td width="10px"><span style="background-color:blue">&nbsp;&nbsp;&nbsp;</span></td>
        <td><span class="lblPADRAO">= pendente</span></td>

        <td width="10px"><span style="background-color:green">&nbsp;&nbsp;&nbsp;</span></td>
        <td><span class="lblPADRAO">= já enviada para operadora</span></td>
        
        <td> 
        <input id="txtFOCADO" type="text" value="" 
          style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />
          
                      
        <span class="lblUSUARIO" id="totREGS">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;<span>
        </td>
      </tr></table></td></tr>
      
    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 29;

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtASSINATURA;Digite data de assinatura válida';
aCMPS[1]='txtNUMPROPOSTA;Identifique uma proposta válida';
aCMPS[2]='txtCONTRATANTE;Preencha o nome do contratante';
aCMPS[3]='txtCPF;CPF do contratante inválido';
aCMPS[4]='txtREPRESENTANTE;Identifique o corretor';
aCMPS[5]='txtOPERADORA;Identifique a operadora';
aCMPS[6]='txtTIPO_CONTRATO;Identifique o tipo do contrato';
aCMPS[7]='txtUSUARIOS;Digite a qtde de vidas';
aCMPS[8]='txtCOMISSAO_REPRESENTANTE;Identifique a comissao do corretor';
aCMPS[9]='txtCOMISSAO_PRESTADORA;Identifique a comissao da prestadora';
aCMPS[10]='txtvlrTOTAL;Digite o valor total';
aCMPS[11]='txtvlrCONTRATO;Digite o valor do contrato  ';
aCMPS[12]='txtvlrADESAO;Digite o valor do contrato  ';


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

var pridiaMES = '01'+ hoje.substring(2);
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');


document.getElementById('dataTRAB').value = pridiaMES;

showAJAX(0);
}

/*******************************************************************************/
function lerOPERADORAS()         {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerOPERADORAS', '', 0);  
showAJAX(0);

document.getElementById('divOPERADORAS').innerHTML = ajax.ler().split('^')[0];
document.getElementById('operadoraATUAL').value = ajax.ler().split('^')[1];
}
/*******************************************************************************/
function tiraFocoOperadora(id) {
                                                                               
if (document.getElementById('operadoraATUAL').value==id.replace('operadora','')) 
  document.getElementById(id).style.backgroundColor='lightgrey';
else
  document.getElementById(id).style.backgroundColor='white';
}


/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

lCTRL = e.ctrlKey;

var lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanFUTURAS= document.getElementById('divFUTURAS').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanPSQ= document.getElementById('divPESQUISA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanPROPS= document.getElementById('divESCOLHEPROPENTREGUE').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (tecla==45 && ! lJanRegistro)   	incluirREG();
if (tecla==39 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnAVANCAR').click();
if (tecla==37 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnRETORNAR').click();
if (tecla==119 && ! lJanAuxilio && ! lJanRegistro && ! lJanFUTURAS ) 
  document.getElementById('btnPESQUISAR').click();

if (tecla==113 && lJanRegistro) document.getElementById('btnGRAVAR').click();   

if ( (tecla>=49 && tecla<=54) && lJanPSQ) pesquisa(tecla-48);

if  (tecla==27) {        
  if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanPROPS)   	{fechaavisoENTREGUECAIXA();fechaESCOLHEPROP();}
  else if (lJanRegistro)   	fecharEDICAO();
  else if (lJanFUTURAS)   	fechaFUTURAS();
  else if (lJanPSQ)   	fechaPESQUISA();  
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}

if  (tecla==13 && ! lJanAuxilio && ! lJanRegistro && ! lJanFUTURAS && ! lJanPSQ  ) {
 showAJAX(1);
 ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerREGS&vlr='+
   document.getElementById('dataTRAB').value+'&vlr2=0'+
   '&vlr3='+document.getElementById('txtPR').value+'&cmp='+2, desenhaTabela);
 return; 
}  
  
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();  
if (lJanRegistro)  {
  if (cfoco!='txtOBS') eval("teclasNavegacao(e);");
}

if  ( tecla==119 && lJanRegistro && ! document.getElementById('txtNUMPROPOSTA').readOnly)  AuxilioF7(cfoco); 
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


/*******************************************************************************/
function fecharEDICAO()     {
fechaavisoENTREGUECAIXA();
document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else if (lJanRegistro )   {
	document.getElementById('txtTIPO_CONTRATO').focus();
  nQtdeCamposTextForm = 29;
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

ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerREGS&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2='+avancarDATA+'&operadora='+
  document.getElementById('operadoraATUAL').value, desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 
  var anoMES = aRESP[1].split('^')[2];
  
  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  centerDiv( 'divFUTURAS' );
  centerDiv( 'divPESQUISA' );
  centerDiv( 'divESCOLHEPROPENTREGUE' );
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Propostas</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  titulo.innerHTML += anoMES;
  
  document.getElementById('dataTRAB').value = aRESP[1].split('^')[3];
    
  document.getElementById('pesqPALAVRA').value='';
  document.getElementById('SELECAO').value='';   
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  
  VerificaAcaoInicial();      

  if (document.getElementById('recarregarINC').value==1) {
    document.getElementById('recarregarINC').value=0;
    incluirREG();
  }
  
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');
  
  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler().split('^')[0];
  
  document.getElementById('txtOBS').value = Encoder.htmlDecode( ajax.ler().split('^')[1] );
  
  /* muda estilo de campos text, span para o padrão do site */
  Muda_CSS(); 
  
  showAJAX(0);

  /* se ha um vinculo caixa-cadastro, ja exibe que ha registro de caixa */
  if ( document.getElementById('numregPropostaEntregueCaixa').value!='' ) 
    avisaENTREGUECAIXA(document.getElementById('numregPropostaEntregueCaixa').value,0);

  if (document.getElementById('txtTIPO_CONTRATO').value.trim()!='') {
    document.getElementById('txtTIPO_CONTRATO').focus(); 
    document.getElementById('txtSEXOC').focus();
  }
  ColocaFocoCmpInicial();
}
}  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')         {
  document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

  if (document.getElementById('txtNUMPROPOSTA').readOnly) return;

	switch (nomeCMP) {
		case 'txtREPRESENTANTE':
		case 'txtTIPO_CONTRATO':		
		case 'txtCOMISSAO_REPRESENTANTE':		
		case 'txtCOMISSAO_PRESTADORA':		
/*
		case 'txtOPERADORA':
      if (nomeCMP=='txtTIPO_CONTRATO') {
        if ( document.getElementById('lblOPERADORA').innerHTML=='' ||
            document.getElementById('lblOPERADORA').innerHTML.toLowerCase().indexOf('erro')!=-1) {
            alert('Identifique a operadora'); return;
        }         
      }
*/		
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
      
			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
/*
			if (nomeCMP!='txtTIPO_CONTRATO') 
			   ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			else
          ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr+
              '&operadora='+document.getElementById('txtOPERADORA').value, '', 0);

*/
  	  ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);

      aRESP = ajax.ler().split(';');  
    	
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();

      if (nomeCMP=='txtTIPO_CONTRATO') {
        if (cVLR.indexOf('* ERRO *')==-1) {
          vlrADESAO= cVLR.split('!')[1];
          cpf_cnpj= cVLR.split('!')[2];
          idOPERADORA=cVLR.split('!')[3];
          nomeOPERADORA=cVLR.split('!')[4];
          qtdeMENS=cVLR.split('!')[5];

          rdBUTTON = document.forms['frmPROPOSTAS'].elements['qtdeMENS'];
          rdBUTTON[qtdeMENS-1].checked = true;

          document.getElementById('lblOPERADORA').innerHTML=nomeOPERADORA;
          document.getElementById('txtOPERADORA').value=idOPERADORA;

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
          cVLR = cVLR.split('!')[0];

          atlVlrPlantao();
        }   
      }
      if (nomeCMP=='txtREPRESENTANTE') {
        if (cVLR.indexOf('* ERRO *')==-1) {

          showAJAX(1);
          ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=lerCOMISSAO&vlr=' +vlr, '', 0);
          showAJAX(0);

          document.getElementById('txtCOMISSAO_REPRESENTANTE').value=ajax.ler().split('|')[0];
          document.getElementById('lblCOMISSAO_REPRESENTANTE').innerHTML=ajax.ler().split('|')[1];

        }   
      }

    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';
        
    	cmpLBL.innerHTML = cVLR;
    	
			break;

		case 'txtCPF':
      /* se pressionou seta p cima nao verifica, se nao usuario nao consegue trabalhar direito */
      if (tecla==38) return;
      /* se a proposta esat sendo alterada, nao permite redefinir qual a respectiva entrega de proposta do caixa,
      esta ligacao caixa-cadastro é feita somente na inclusao da proposta, para redefinir a ligacao caixa-cadastro,
      somente se excluir o cadastro e incluir d novo 

      -- ATUALIZACAO: se nao ha vinculacao cadastro - caixa feita ainda (divPROPENTREGUE nao visivel), permite a pesquisa
      mesmo sendo alteracao de cadastro 
      */
/*      if (document.getElementById('numREG').value!='' && document.getElementById("divPROPENTREGUE").style.display!='none')  return; */
//      if (document.getElementById('numREG').value!='')  return;
  
      vlr=document.getElementById('txtCPF').value;

      showAJAX(1);
			ajax.criar('ajax/ajaxPROPOSTAS.php?acao=verENTPROP&tipo='+document.getElementById('txtTIPO_CONTRATO').value+
                  '&id='+vlr+'&sequencia='+document.getElementById('numREG').value, '', 0);
			showAJAX(0);

      entregues=ajax.ler();
      if (entregues=='nenhuma') {
        alert('Não há registro de proposta com este cpf/cnpj entregue no caixa nos últimos 200 dias');
        fechaavisoENTREGUECAIXA();
        return;
      }
      else {
        if (entregues.indexOf('somenteUMA')!=-1) {
          numREG=entregues.split(';')[0];
          document.getElementById('txtREPRESENTANTE').value=entregues.split(';')[1];
          document.getElementById('lblREPRESENTANTE').innerHTML=entregues.split(';')[2];
          document.getElementById('txtvlrCONTRATO').value=entregues.split(';')[3];
          document.getElementById('txtvlrADESAO').value=entregues.split(';')[4];
          document.getElementById('txtvlrRECEBIDO').value=entregues.split(';')[5];
          document.getElementById('txtvlrTOTAL').value=entregues.split(';')[6];          

          avisaENTREGUECAIXA( numREG.replace('somenteUMA',''),0 );
        }
        else {
          var cpf=document.getElementById('txtCPF').value.trim()=='' ? '< NÃO INFORMADO >' : document.getElementById('txtCPF').value;
          propENTREGUES('&nbsp;Propostas registradas no caixa - tipo '+document.getElementById('lblTIPO_CONTRATO').innerHTML+' ('+
                        document.getElementById('txtTIPO_CONTRATO').value+')&nbsp;&nbsp;&nbsp;&nbsp;'+
                        document.getElementById('tdCPF').innerHTML+': '+cpf, entregues);
        }
      }
      
      break;      

		case 'txtNUMPROPOSTA':
      var sequencia = document.getElementById('numREG').value;
      var proposta = document.getElementById('txtNUMPROPOSTA').value.trim();
      
      if (proposta=='') { 
      //  document.getElementById('okPROPOSTA').innerHTML = 'erro';
      }
      else {  
        showAJAX(1);
  			ajax.criar('ajax/ajaxPROPOSTAS.php?acao=verDUPLICIDADE&vlr='+proposta+'&sequencia='+sequencia+
              '&tipo='+document.getElementById('txtTIPO_CONTRATO').value, '', 0);
  			showAJAX(0);
  
  			document.getElementById('okPROPOSTA').innerHTML = '';
        if (ajax.ler().indexOf('jaCAD')!=-1) {        
          alert('Proposta já cadastrada');
          document.getElementById('okPROPOSTA').innerHTML = 'erro';
        }
      }
      break;

    case 'txtUSUARIOS':
    case 'txtvlrCONTRATO':
      atlVlrPlantao();
      atlVlrTotal();
      break;

    case 'txtvlrADESAO':
      atlVlrTotal();
      break;

		case 'txtCEP':
      showAJAX(1);
			ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerCEP&vlr='+document.getElementById('txtCEP').value, '', 0);
			showAJAX(0);
			
			if (ajax.ler().indexOf('ok')==-1)
        alert("Erro \n\n"+ajax.ler());
      else {
        var info = ajax.ler().replace('ok', '');
        if (typeof(info.split(';')[3])=='undefined')
          document.getElementById('txtENDERECO').value = info.split(';')[0];
        else {             
          document.getElementById('txtENDERECO').value = info.split(';')[0];
          document.getElementById('txtBAIRRO').value = info.split(';')[3];
          document.getElementById('lblBAIRRO').innerHTML = info.split(';')[4];
        }
        document.getElementById('txtMUNICIPIO').value = info.split(';')[1];
        document.getElementById('txtUF').value = info.split(';')[2];
      }  
  
      break;    

	}
	return;
}

else  {
  if (document.getElementById('txtNUMPROPOSTA').readOnly) return;

	for (i=0;i<aCMPS.length;i++)   {
		cmp = aCMPS[i].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl');
    
		erro=0;
		switch (cCMP)   {
			case 'txtNUMPROPOSTA':
/*        if ( cVLR=='' || document.getElementById('okPROPOSTA').innerHTML.toLowerCase().indexOf('erro')!=-1)   erro=1;*/
        if ( document.getElementById('okPROPOSTA').innerHTML.toLowerCase().indexOf('erro')!=-1)   erro=1;
        break;
		
			case 'txtASSINATURA':
        if ( cVLR=='' || ! verifica_data('txtASSINATURA') )   erro=1;
        break;
        
  		case 'txtCPF':		
        cVLR=document.getElementById('txtCPF').value.trim();
        lERRO=false;
        if (cVLR!='')    {
          for (e=0; e<5; e++) {
            cVLR = cVLR.replace(".",  "");
            cVLR = cVLR.replace("-",  "");
          }
          if (document.getElementById('tdCPF').innerHTML=='CPF') {
            if ( validacpf(cVLR) ) lERRO=false;
          }
          else
            lERRO=false;
        }
  
        if (lERRO==true) {
          alert('Preencha um CPF válido');
          document.getElementById('txtCPF').focus();
          return;
        }
  
        break;  

			case 'txtCONTRATANTE':        			 
			case 'txtUSUARIOS':			
			case 'txtvlrTOTAL':			
        if ( cVLR=='' ) erro=1;
        break;

/*        
  		case 'txtCOMISSAO_REPRESENTANTE':		
  		case 'txtCOMISSAO_PRESTADORA':		
*/
  		case 'txtREPRESENTANTE':
  		case 'txtTIPO_CONTRATO':		
  		case 'txtOPERADORA':
        if ( document.getElementById(label).innerHTML=='' ||
            document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;
                
 		}
 		if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}
 	}	
		

  var data = document.getElementById('txtASSINATURA').value;
  var assinatura='null';
  if (data.rtrim().ltrim()!='') 
    assinatura = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  var data2 = document.getElementById('txtNASCCONTRATANTE').value;
  var nascimento='null';
  if (data2.rtrim().ltrim()!='') 
    nascimento = data2.substring(6, 10)+'-'+data2.substring(3, 5)+'-'+data2.substring(0, 2);

  var vlrADESAO = document.getElementById('txtvlrADESAO').value.replace(',','.').trim();
  vlrADESAO = vlrADESAO=='' ? 'null' : vlrADESAO;  
  var vlrCONTRATO = document.getElementById('txtvlrCONTRATO').value.replace(',','.').trim();
  vlrCONTRATO = vlrCONTRATO=='' ? 'null' : vlrCONTRATO;
  var vlrTOTAL = document.getElementById('txtvlrTOTAL').value.replace(',','.').trim();
  vlrTOTAL = vlrTOTAL=='' ? 'null' : vlrTOTAL;
  var vlrRECEBIDO = document.getElementById('txtvlrRECEBIDO').value.replace(',','.').trim();
  vlrRECEBIDO = vlrRECEBIDO=='' ? 'null' : vlrRECEBIDO;
  var vlrPRODUCAO = document.getElementById('txtvlrPRODUCAO').value.replace(',','.').trim();
  vlrPRODUCAO = vlrPRODUCAO=='' ? 'null' : vlrPRODUCAO;
  var vlrPLANTAO = document.getElementById('txtvlrPLANTAO').value.replace(',','.').trim();
  vlrPLANTAO = vlrPLANTAO=='' ? 'null' : vlrPLANTAO;

  comiREPRE=document.getElementById('txtCOMISSAO_REPRESENTANTE').value;
  if (document.getElementById('lblCOMISSAO_REPRESENTANTE').innerHTML.indexOf('erro')!=-1 || comiREPRE=='') comiREPRE='null';

  comiPRESTADORA=document.getElementById('txtCOMISSAO_PRESTADORA').value;
  if (document.getElementById('lblCOMISSAO_PRESTADORA').innerHTML.indexOf('erro')!=-1 || comiPRESTADORA=='') comiPRESTADORA='null';   


  cmps= document.getElementById('numREG').value + '|'+ 
        document.getElementById('txtNUMPROPOSTA').value + '|'+
        document.getElementById('txtFONERES').value + '|'+                        
        vlrADESAO + '|'+
        vlrCONTRATO+ '|'+
        vlrTOTAL+ '|'+
        vlrRECEBIDO+ '|'+
        vlrPRODUCAO+ '|'+
        vlrPLANTAO+ '|'+                                
        assinatura + '|'+
        escape( Encoder.htmlEncode(document.getElementById('txtOBS').value) )+'|'+
        document.getElementById('txtUSUARIOS').value + '|'+
        document.getElementById('txtCONTRATANTE').value.toUpperCase()+'|'+
        document.getElementById('txtCPF').value+'|'+
        document.getElementById('txtTIPO_CONTRATO').value+'|'+
        comiREPRE+'|'+        
        comiPRESTADORA+'|'+
        '|'+
        document.getElementById('txtOPERADORA').value+'|'+
        document.getElementById('txtREPRESENTANTE').value+'|'+                                
        nascimento+'|'+
        document.getElementById('txtSEXOC').value+'|'+
        document.getElementById('txtENDERECO').value+'|'+
        document.getElementById('txtEND_NUMERO').value+'|'+
        document.getElementById('txtEND_COMPLEMENTO').value+'|'+
        document.getElementById('txtBAIRRO').value+'|'+
        document.getElementById('txtMUNICIPIO').value+'|'+
        document.getElementById('txtUF').value+'|'+
        document.getElementById('txtCEP').value+'|'+
        document.getElementById('txtFONECOM').value+'|'+
        document.getElementById('txtCELULAR').value+'|'+
        document.getElementById('txtEMAIL').value+'|'+
        document.getElementById('txtCONTRATO').value;
                
  rdBUTTON = document.forms['frmPROPOSTAS'].elements['qtdeMENS'];
  var qtdeMENS='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     qtdeMENS = rdBUTTON[i].value;
  }

	showAJAX(1);
	ajax.criar('ajax/ajaxPROPOSTAS.php?acao=gravar&vlr=' + cmps+
          '&qtdeMENS='+qtdeMENS+'&idENTREGA='+document.getElementById('numregPropostaEntregueCaixa').value, '', 0);
	showAJAX(0);
	
  resp = ajax.ler();



  
  var novoREG=document.getElementById('numREG').value.trim();

  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
    
    document.getElementById('recarregarINC').value = (novoREG=='' ? 1 : 0);

  	cID = resp.substring(resp.indexOf(';')+1);
    
    if (novoREG=='') alert('Sequência gravada: '+cID);
        
  	window.top.document.getElementById('infoTrab').value = 'frmPROPOSTAS:GRAVOU=' + cID
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

ajax.criar('ajax/ajaxPROPOSTAS.php?acao=excluir&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao excluir!!! \n\n' + ajax.ler());
  
lerREGS();  
}



/*******************************************************************************/
function cancelar() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

lCANCELADA=false;
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {
 if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    if (tab.rows[t].style.color=='red') {lCANCELADA=true;break; }
/*    if (tab.rows[t].style.color=='green') {alert('Proposta já enviada.');return;} */
    break;
  }  
}

if (lCANCELADA) { 
  if (! confirm("Proposta já cancelada. \n\nAnular o cancelamento?")) return;
}
else {
  if (! confirm("Cancelar esta proposta?")) return;
}

ajax.criar('ajax/ajaxPROPOSTAS.php?acao=cancelar&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao cancelar!!! \n\n' + ajax.ler());
  
lerREGS();  
}





/*******************************************************************************/
function buscar() {

var palavra=prompt('Digite uma palavra para procurar:','');

if (palavra==null) return;
if (palavra.rtrim()=='') return;

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerREGS&vlr=palavra'+palavra, desenhaTabela); 
}
  
/*******************************************************************************/
function futuras() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=futuras&vlr='+document.getElementById('SELECAO').value, '' ,0);
showAJAX(0);

var prop='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    prop = tab.rows[t].cells[2].innerHTML + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Operadora: '+ tab.rows[t].cells[0].innerHTML ;
    break;
  }  
}
var divFUTURAS = document.getElementById('divFUTURAS'); divFUTURAS.setAttribute(propCLASSE, 'cssDIV_FUTURAS');    
var titFUTURAS = document.getElementById('titFUTURAS'); titFUTURAS.innerHTML = ajax.ler().split('|')[0];
var tabFUTURAS = document.getElementById('tabFUTURAS'); tabFUTURAS.innerHTML = ajax.ler().split('|')[1];

document.getElementById('infoFUTURAS').innerHTML = "&nbsp;&nbsp;&nbsp;FUTURAS&nbsp;&nbsp;&nbsp; - Proposta:"+prop;

showAJAX(0);
}

/*******************************************************************************/
function fechaFUTURAS() {

var divFUTURAS = document.getElementById('divFUTURAS'); divFUTURAS.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
var tabFUTURAS = document.getElementById('tabFUTURAS'); tabFUTURAS.innerHTML = '';
}

/*******************************************************************************/
function fechaPESQUISA() {
var divPESQUISA = document.getElementById('divPESQUISA'); divPESQUISA.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
}

/*******************************************************************************/
function menuPSQ() {
var divPESQUISA = document.getElementById('divPESQUISA'); divPESQUISA.setAttribute(propCLASSE, 'cssDIV_BAIXAR');    
}

/*******************************************************************************/
function pesquisa(item) {

var cmp='';
if (item==1) cmp='SEQUÊNCIA';
if (item==2) cmp='Nº DO CONTRATO';
if (item==3) cmp='CONTRATANTE (INÍCIO DO NOME)'; 
if (item==4) cmp='CONTRATANTE (PARTE DO NOME)';
if (item==6) {cmp='PROPOSTAS COM NUMERAÇÃO DUPLICADA'; item=7;}
if (item==5) {cmp='CPF/CNPJ CONTRATANTE (DIGITE SOMENTE OS NUMEROS)'; item=6;}

resp='propostas com numeração duplicada';
if (item!=7) {
  resp = prompt(cmp, '');
  
  if (resp==null) return;
  if (resp.trim()=='') return;
  
  fechaPESQUISA();
  
  document.getElementById('SELECAO').value='';
  document.getElementById('pesqPALAVRA').value=cmp;
} 
else 
  fechaPESQUISA();

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerREGS&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2=0'+
  '&vlr3='+resp+'&cmp='+item, desenhaTabela);

 
}

/*******************************************************************************/
function fixaOPERADORA(id) {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=fixaOPERADORA&vlr='+id, '', 0);
showAJAX(0);

lerOPERADORAS();
lerREGS(); 
}

/*******************************************************************************/
function atlVlrPlantao() {

document.getElementById('lblPERC').innerHTML='(???%)';

var idPRODUTO=document.getElementById('txtTIPO_CONTRATO').value.trim();
var nomePRODUTO=document.getElementById('lblTIPO_CONTRATO').innerHTML;
var vlrCONTRATO = document.getElementById('txtvlrCONTRATO').value.trim();
var qtdeVIDAS = document.getElementById('txtUSUARIOS').value.trim();
qtdeVIDAS=(qtdeVIDAS=='') ? '0' : qtdeVIDAS;

if (nomePRODUTO.indexOf('* ERRO *')!=-1) return;
if ( (idPRODUTO=='') || (vlrCONTRATO=='') ) return

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=calcVlrPlantao&vlr='+idPRODUTO+'&vlr2='+vlrCONTRATO+'&vlr3='+qtdeVIDAS, '', 0);
showAJAX(0);

document.getElementById('txtvlrPLANTAO').value=ajax.ler().split('|')[0];
if ( ajax.ler().split('|')[2]=='S' )
  document.getElementById('txtvlrPRODUCAO').value=ajax.ler().split('|')[0];
else
  document.getElementById('txtvlrPRODUCAO').value='0,00';

document.getElementById('lblPERC').innerHTML=ajax.ler().split('|')[1]; 
}



/*******************************************************************************/
function cadastro() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

var prop=''; var data='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {
  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    prop = tab.rows[t].cells[2].innerHTML.replace('&nbsp;','') + ' - '+tab.rows[t].cells[0].innerHTML.replace('&nbsp;','');
    data = tab.rows[t].cells[5].innerHTML;
    break;
  }  
}
  
var data=prompt('Proposta '+prop+'\n\nNova data de cadastro (formato dd/mm/yy):',data);

if (data==null) return;
if (data.rtrim()=='') return;

data=data.substring(0, 2)+'/'+data.substring(3, 5)+'/20'+data.substring(6, 9);
if (! verifica_data('ITSELF_'+data) || data.length<10)  {
  alert('Data inválida');
  return;
}
data=data.substring(6, 11)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=alteraDataCadastro&vlr='+id+'&data='+data, '', 0);

if (ajax.ler().indexOf('ok')!=-1) {  	
  window.top.document.getElementById('infoTrab').value = 'frmPROPOSTAS:GRAVOU=' + id;
 	lerREGS();
} 	
else 
  alert('Houve erro ao alterar\n\n'+ajax.ler());
}

/*******************************************************************************/
function reler() {
lerHOJE();
lerREGS();
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
function pendente() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

var jaPENDENTE=false;
var jaENVIADA=false;
var prop='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {

    if (tab.rows[t].style.color=='blue') jaPENDENTE=true;
    //if (tab.rows[t].style.color=='rgb(159, 159, 159)') jaENVIADA=true;     
    if (tab.rows[t].style.color=='green') jaENVIADA=true;
    if (tab.rows[t].style.color=='red') {
      alert('Esta proposta já foi cancelada'); return;
    }
    break;
  }  
}

if (jaPENDENTE) {
 if (!confirm('Anula pendência desta proposta?'))  return;
 
 showAJAX(1);
 ajax.criar('ajax/ajaxPROPOSTAS.php?acao=anulaPENDENCIA&vlr='+document.getElementById('SELECAO').value, '' ,0);
 showAJAX(0);
 
 window.top.document.getElementById('infoTrab').value = 'frmPROPOSTAS:GRAVOU=' + document.getElementById('SELECAO').value;
 
 document.getElementById('SELECAO').value="";
 lerREGS();
 return; 
}

if (jaENVIADA) {
 if (!confirm('Proposta já enviada para operadora\n\nConfirma devolução e colocar em pendência?\n\n'))  return;
 
 showAJAX(1);
 ajax.criar('ajax/ajaxPROPOSTAS.php?acao=devolucao&vlr='+document.getElementById('SELECAO').value, '' ,0);
 showAJAX(0);
 
 window.top.document.getElementById('infoTrab').value = 'frmPROPOSTAS:GRAVOU=' + document.getElementById('SELECAO').value;
 
 document.getElementById('SELECAO').value="";
 lerREGS();
 return; 
}


showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=marcarPENDENTE&vlr='+document.getElementById('SELECAO').value, '', 0);
showAJAX(0);


if (ajax.ler().indexOf('ok')!=-1) {  	
  window.top.document.getElementById('infoTrab').value = 'frmPROPOSTAS:GRAVOU=' + id;
 	lerREGS();
}
}

/*******************************************************************************/
function pendentes() {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerREGS&cmp=5&vlr3=1&vlr='+
   document.getElementById('dataTRAB').value+'&vlr2=0', desenhaTabela);
showAJAX(0);
}
/*******************************************************************************/
function rel() {
window.top.frames['framePRINCIPAL'].location.href='rel/protocolo.php';
}
/*******************************************************************************/
function atlVlrTotal() {
var vlr1=parseFloat(document.getElementById('txtvlrCONTRATO').value.replace(',','.').trim(), 10);
var vlr2=parseFloat(document.getElementById('txtvlrADESAO').value.replace(',','.').trim(), 10);

var soma=(vlr1+vlr2).toFixed(2).toString().replace('.',',');

document.getElementById('txtvlrTOTAL').value = soma;
document.getElementById('txtvlrRECEBIDO').value = vlr1.toFixed(2).toString().replace('.',',');
}


/********************************************************************************/
function baixar(idFUTURA, infoFUTURA, dataVENC)  {

showAJAX(1);
ajax.criar('rel/ajaxRELS.php?acao=lerDATAS', '', 0);  
showAJAX(0);

var resp = ajax.ler().replace(/-/g, '/');
hoje= resp.split(';')[4];


var data=prompt(infoFUTURA+"\n\n"+
                'Data da baixa (formato dd/mm/yy):'+"\n\n",dataVENC);

if (data==null) return;
if (data.rtrim()=='') return;

data=data.substring(0, 2)+'/'+data.substring(3, 5)+'/20'+data.substring(6, 9);
if (! verifica_data('ITSELF_'+data) || data.length<8)  {
  alert('Data inválida');
  return;
}
data=data.substring(6, 11)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);



var repasse=prompt(infoFUTURA+"\n\n"+
                'Data do repasse (formato dd/mm/yy):'+"\n\n",hoje);

if (repasse==null) return;
if (repasse.rtrim()=='') return;

repasse=repasse.substring(0, 2)+'/'+repasse.substring(3, 5)+'/20'+repasse.substring(6, 9);
if (! verifica_data('ITSELF_'+repasse) || repasse.length<8)  {
  alert('Data inválida');
  return;
}
repasse=repasse.substring(6, 11)+'-'+repasse.substring(3, 5)+'-'+repasse.substring(0, 2);



var valor=prompt(infoFUTURA+"\n\n"+
                'Valor pago :'+"\n\n",'');


if (valor==null) return;
if (valor.rtrim()=='') return;

if (isNaN(valor)) { 
  alert('Valor inválido');
  return;
}

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=baixarMENS&vlr='+idFUTURA+'&valor='+valor+'&data='+data+'&repasse='+repasse, '', 0);  
showAJAX(0);

futuras();
}



/********************************************************************************/
function cancelar_baixa(idFUTURA, infoFUTURA, dataVENC)  {

if (! confirm(infoFUTURA+"\n\n"+'Tem certeza cancelar esta baixa?')) return;

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=cancelarMENS&vlr='+idFUTURA, '', 0);  
showAJAX(0);

futuras();
}

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmPROPOSTAS'].elements['qtdeMENS'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtREPRESENTANTE').focus();
}
/********************************************************************************/
function avisaENTREGUECAIXA(numregPropostaEntregueCaixa,lerDetalhes) { 
posY=findPosY( document.getElementById("txtNUMPROPOSTA") )-15;
posX=findPosX( document.getElementById("txtNUMPROPOSTA") )+220;

document.getElementById("divPROPENTREGUE").style.top=posY+'px';
document.getElementById("divPROPENTREGUE").style.left=posX+'px';

document.getElementById("divPROPENTREGUE").style.position='absolute';
document.getElementById("divPROPENTREGUE").style.display='block';
document.getElementById("divPROPENTREGUE").style.zIndex=3;

document.getElementById("numregPropostaEntregueCaixa").value = numregPropostaEntregueCaixa;
if (lerDetalhes==1) {
  var tab=document.getElementById('lstPROPENTREGUES');
  for (var t=0; t<tab.rows.length; t++) {
    if (tab.rows[t].id==numregPropostaEntregueCaixa) {
      document.getElementById('txtREPRESENTANTE').value=tab.rows[t].cells[5].innerHTML;
      document.getElementById('lblREPRESENTANTE').innerHTML=tab.rows[t].cells[6].innerHTML;
      document.getElementById('txtvlrCONTRATO').value=tab.rows[t].cells[7].innerHTML;
      document.getElementById('txtvlrADESAO').value=tab.rows[t].cells[8].innerHTML;
      document.getElementById('txtvlrRECEBIDO').value=tab.rows[t].cells[9].innerHTML;
      document.getElementById('txtvlrTOTAL').value=tab.rows[t].cells[10].innerHTML;      
    }
  }  
}

}

/********************************************************************************/
function fechaavisoENTREGUECAIXA() {
document.getElementById("divPROPENTREGUE").style.display='none';
document.getElementById("numregPropostaEntregueCaixa").style.zIndex = '';
document.getElementById("txtCPF").focus();
}

/*******************************************************************************/
function propENTREGUES(infoOBJETIVO, props) {

var divESCOLHEPROPENTREGUE = document.getElementById('divESCOLHEPROPENTREGUE'); 
divESCOLHEPROPENTREGUE.setAttribute(propCLASSE, 'cssDIV_FUTURAS');    
var tit = document.getElementById('titPROPENTREGUES'); tit.innerHTML = props.split('|')[0];
var tab = document.getElementById('tabPROPENTREGUES'); tab.innerHTML = props.split('|')[1];

document.getElementById('infoPROPENTREGUES').innerHTML = infoOBJETIVO;

showAJAX(0);
}

/*******************************************************************************/
function fechaESCOLHEPROP() {
var divESCOLHA = document.getElementById('divESCOLHEPROPENTREGUE'); divESCOLHA.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
var tabPROPENTREGUES = document.getElementById('tabPROPENTREGUES'); tabPROPENTREGUES.innerHTML = '';
}

/********************************************************************************/
function adiantar(idFUTURA)  {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=adiantarMENS&vlr='+idFUTURA, '', 0);  
showAJAX(0);

futuras();
}

/********************************************************************************/
function cancelar_adiantar(idFUTURA)  {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=cancelar_adiantarMENS&vlr='+idFUTURA, '', 0);  
showAJAX(0);

futuras();
}




//]]></script>
  </body>
</html>
