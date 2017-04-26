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

<!-- Folha de estilos do calendário -->
<link rel="stylesheet" type="text/css" media="all" href="js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a função Calendar.setup, que ajuda a gerar um calendário em poucas linhas de código -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar-setup.js"></script> 


<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO_ENT {
position: absolute; top: 200px;  width: 980px; height: 400px;	
margin-top: -300px; margin-left: -490px; display:block; z-index:3;}

.cssDIV_EDICAO_CAIXA {
position: absolute; top: 200px;  width: 790px; height: 300px;	
margin-top: -150px; margin-left: -395px; display:block; z-index:3;}


.cssDIV_PGTO {
position: absolute; top: 200px;  width: 780px; height: 100px;	
margin-top: -50px; margin-left: -400px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
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
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();"><b>Editar registro</b></a></li>
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>  
</ul>

<form id="frmRECEBIMENTOS" name="frmRECEBIMENTOS" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divPGTO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor="red">
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >

<input id="dataTRAB" type="hidden" value="" />

<input id="propEDITANDO" type="hidden" value="" />
<input id="pgtoEDITANDO" type="hidden" value="" />
<input id="recarregarINC" type="hidden" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
  <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">

  <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>

  <td width="60%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span>
  <input type="text" id="txtDATATRAB" value="" 
      style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
      onchange="lerHOJE(1);lerREGS();";/> 
  </td>
	
	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
	  <img src="images/buscadata.png" />
	</td>

<!--
	<td id="btnALTERNAR" title="Alternar recebimento/proposta  (Ctrl+TAB)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >
	  <img src="images/alternar.png" />
	</td>
-->	
	
	<td id="btnPESQUISAR" title="Pesquisa proposta  (Ctrl+B)" align="center" onmouseout="this.style.backgroundColor='white'" 
	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alert('em construcao');" >
	  <img src="images/pesquisa.png" />
	</td>

	<td id="btnRETORNAR" title="Retornar 1 mês  (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'" 
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
	<td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .75); ?> px" >
	  <div id="titTABELA">&nbsp;</div>
	  <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
	</td>
      </tr>

      <tr><td><table width="100%"><tr>

	<td align="right">

<input id="txtFOCADO" type="text" value="" 
  style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" />
		      
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
var largPR = 0;

var aCHEQUES;

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
  ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=lerDataHoje', '', 0);  
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

showAJAX(0);
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
var lJanVALE = document.getElementById('txtVLRVALE');
var lJanCAIXA = document.getElementById('txtCONTA');

if (tecla==39 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnAVANCAR').click();
if (tecla==37 && ! lJanAuxilio && !lJanRegistro) document.getElementById('btnRETORNAR').click();
if (tecla==66 && lCTRL && ! lJanAuxilio && ! lJanRegistro) document.getElementById('btnPESQUISAR').click();

if (tecla==9 && lCTRL && ! lJanAuxilio && ! lJanCHEQUE && ! lJanBOLETO
  && ! lJanCARTAO && ! lJanVALE && (lJanRegistro || lJanCAIXA)) alternar();

/*if (tecla==9 && lCTRL && ! lJanAuxilio && ! lJanRegistro) document.getElementById('btnALTERNAR').click();*/

if  (tecla==27) {        
  if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanCAIXA)   	fecharCAIXA(); 
  else if (lJanCHEQUE)   	{limparCmpsCheque(); fecharPGTO();}
  else if (lJanBOLETO)   	{limparCmpsBoleto(); fecharPGTO();}  
  else if (lJanCARTAO)   	{limparCmpsCartao(); fecharPGTO();}  
  else if (lJanVALE)   	  {limparCmpsVale(); fecharPGTO();}  
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}  
}  

if (lJanRegistro) {  
  if  (tecla==49 && lALT)   	addDINHEIRO();  
  if  (tecla==50 && lALT)   	addCHEQUE();
  if  (tecla==51 && lALT)   	addBOLETO();
  if  (tecla==52 && lALT)   	addCARTAO();
  if  (tecla==53 && lALT)   	addVALE();
}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();  
if  (tecla==45 && ! lJanRegistro)   	incluirREG();

if  (tecla==13 && cfoco=='txtADESAO')   	{addPROPOSTA(); return;}
if  (tecla==13 && (cfoco=='txtVLRCH' || cfoco=='txtVLRBOLETO' || cfoco=='txtVLRCARTAO' || cfoco=='txtVLRVALE'))   	{addPGTO(); return;}  


if (lJanRegistro || lJanCAIXA)  eval("teclasNavegacao(e);");

if  ( tecla==113 && lJanRegistro ) document.getElementById('btnGRAVAR').click();
if  ( tecla==119 && lJanRegistro )  AuxilioF7(cfoco); 
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
  
else if (lJanCAIXA) {
  nQtdeCamposTextForm = 8;
  
  var aCMPS=new Array();
  aCMPS[0]='txtDATA;Preencha uma data válida';
  aCMPS[1]='txtCONTA;Identifique uma conta válida';
  aCMPS[2]='txtDESCRICAO;Preencha a descrição';
  aCMPS[3]='txtVALOR;Preencha o valor';
   
  document.getElementById('txtCONTA').focus();
}  	
else if (lJanPGTO) {
  if (document.getElementById('txtVLRBOLETO'))   {nQtdeCamposTextForm = 2;   document.getElementById('txtOPERADORA').focus();}  
  else if (document.getElementById('txtVLRCARTAO'))   {nQtdeCamposTextForm = 2;   document.getElementById('txtBANCO').focus();}  
  else if (document.getElementById('txtVLRVALE'))   {nQtdeCamposTextForm = 2; document.getElementById('txtREL_REPRESENTANTE').focus();}  
  else if (document.getElementById('txtBANCO'))   {nQtdeCamposTextForm = 4;   document.getElementById('txtCHEQUE').focus();}
}

else if (lJanENTREGA) { 
  nQtdeCamposTextForm = 7;

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
dataLER = data.substring(4, 8)+data.substring(2, 4)+data.substring(0, 2);

ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=lerENTREGAS&vlr='+
  dataLER+'&vlr2='+avancarDATA, desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 
  
  showAJAX(0);
  
  centerDiv( 'divEDICAO' ); centerDiv( 'divPGTO' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Entregas</font>&nbsp;&nbsp;&nbsp;&nbsp;';      
  
  data=document.getElementById("dataTRAB").value;;
  dataLER = data.substring(0, 2)+'/'+data.substring(2, 4)+'/'+data.substring(6, 10);
  titulo.innerHTML += dataLER;  
  
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  
  if (document.getElementById('recarregarINC').value==1) {
    document.getElementById('recarregarINC').value=0;
    incluirREG();
  }    
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);

ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function alternar() {
showAJAX(1);

if (document.getElementById('txtCONTA')) 
  ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=incluirREG', desenhaJanelaREG);
else 
  ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=incluirCAIXA', desenhaJanelaREG2);  
}


/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {

  var divEDICAO = document.getElementById('divEDICAO');
  
  var aRESP = ajax.ler().split('^');

  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO_ENT');    divEDICAO.innerHTML = aRESP[0];
  
  aCHEQUES = aRESP[1].split('|');
  
  Muda_CSS(); 
  
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
  
  Muda_CSS(); 
  
  showAJAX(0);  
  ColocaFocoCmpInicial();
}
}  
  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
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
		case 'txtTIPO_CONTRATO':		
		case 'txtBANCO':		
		case 'txtOPERADORA':		
		case 'txtCONTA':		
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
			
			showAJAX(1);
      cmpLBL.innerHTML = 'lendo...';
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');
      
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];

      if (nomeCMP=='txtTIPO_CONTRATO') {
        if (cVLR.indexOf('* ERRO *')==-1) {
          vlrADESAO= cVLR.split('!')[1];
          document.getElementById('txtADESAO').value = vlrADESAO.replace('.',',');
          cVLR = cVLR.split('!')[0];
        }   
      }
      if (nomeCMP=='txtCONTA') {
        if (cVLR.indexOf('* ERRO *')==-1) cVLR= cVLR.split('^')[0];
      }

      cIDCMP=cIDCMP.rtrim().ltrim();
	
    	if (cVLR.indexOf('* ERRO *')!=-1) cmpLBL.style.color='red';
      else cmpLBL.style.color='blue';

      cVLR=cVLR.replace(/_/g, '&nbsp;');  	
      cmpLBL.innerHTML = cVLR;
			
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
  if (data.rtrim().ltrim()!='') 
    dataGRAVAR = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  if (document.getElementById('txtCONTA')) {
  }
  else {
    var strPROP='';
    var tab = document.getElementById('tabPROPOSTAS');
    for (var f=0; f<=tab.rows.length-1; f++) {
      strPROP += (strPROP=='' ? '' : '|') ;
      vlr= tab.rows[f].cells[3].innerHTML.replace(',','.');
      vlrRECEBIDO= tab.rows[f].cells[4].innerHTML.replace(',','.');
      vlrPRESTADORA= tab.rows[f].cells[5].innerHTML; vlrPRESTADORA=vlrPRESTADORA.substring(0, vlrPRESTADORA.indexOf('(')).replace(',','.');
      vlrADESAO= tab.rows[f].cells[6].innerHTML.replace(',','.');

      strPROP +=  tab.rows[f].cells[8].innerHTML+';'+
                  tab.rows[f].cells[9].innerHTML+';'+
                  tab.rows[f].cells[2].innerHTML+';'+
                  vlr+';'+
                  vlrRECEBIDO+';'+
                  vlrPRESTADORA+';'+
                  vlrADESAO;
    }
    
    if (strPROP=='') {
      alert('Preencha pelo menos 1 contrato');
      return;
    }

    var strPGTO='';                       
    var tab = document.getElementById('tabPGTO');
    for (var f=0; f<=tab.rows.length-1; f++) {
      strPGTO += (strPGTO=='' ? '' : '|') ;
      tipo= tab.rows[f].cells[0].innerHTML;

      if (tipo=='CHEQUE') {
        data=tab.rows[f].cells[6].innerHTML;
        dataGRAVAR2 = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

        // cheque=3, banco=4, data= 6, valor= 7
        strPGTO += 'CHEQUE;'+
                    tab.rows[f].cells[3].innerHTML+';'+
                    tab.rows[f].cells[4].innerHTML+';'+
                    dataGRAVAR2+';'+
                    tab.rows[f].cells[7].innerHTML.replace(',','.');
      }
      else if (tipo=='BOLETO') {
        // operadora=3   valor=5
        strPGTO += 'BOLETO;'+
                    tab.rows[f].cells[3].innerHTML+';'+
                    tab.rows[f].cells[5].innerHTML.replace(',','.');
      }
      else if (tipo=='CARTÃO') {
        // banco=3   valor=5
        strPGTO += 'CARTÃO;'+
                    tab.rows[f].cells[3].innerHTML+';'+
                    tab.rows[f].cells[5].innerHTML.replace(',','.');
      }
      else if (tipo=='VALE') {
        // representante=3   valor=5
        strPGTO += 'VALE;'+
                    tab.rows[f].cells[3].innerHTML+';'+
                    tab.rows[f].cells[5].innerHTML.replace(',','.');
      }
    }
    cmps= document.getElementById('numREG').value+'|'+
          dataGRAVAR+'^'+strCMPS+'^'+
          strPGTO;
  }        
alert(cmps);return;
	showAJAX(1);
	ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=gravarRECEBIMENTO&vlr=' + cmps, '' , 0);
  showAJAX(0);
  
  resp = ajax.ler();
  
  var titulo=document.getElementById('tituloEDICAO').innerHTML.toLowerCase();
  document.getElementById('recarregarINC').value = (titulo.indexOf('incluir')!=-1) ? 1 : 0;
  
  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
  
  	cID = resp.substring(resp.indexOf(';')+1);
  	window.top.document.getElementById('infoTrab').value = 'frmRECEBIMENTOS:GRAVOU=' + cID
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

showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=excluirENT&vlr=' + id, '', 0);
showAJAX(0);
  
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao excluir!!! \n\n' + ajax.ler());
  
  
lerREGS();  
}


/*******************************************************************************/
function addPROPOSTA() {
var tab = document.getElementById('tabPROPOSTAS');

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


cVLR=document.getElementById('txtCPF').value.trim();
lERRO=true;
if (cVLR!='')    {
  for (e=0; e<5; e++) {
    cVLR = cVLR.replace(".",  "");
    cVLR = cVLR.replace("-",  "");
  }
  if ( validacpf(cVLR) ) lERRO=false;
}

/*
if (lERRO==true) {
  alert('Preencha um CPF correto');
  document.getElementById('txtCPF').focus();
  return;
}
*/
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
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=lerPercAdeRepre&id='+idREPRE+'&idPROD='+idTIPO, '', 0);  
showAJAX(0);

comiREPRE_mostrar = parseFloat(ajax.ler(), 10).toFixed(0);
comiPRESTADORA_mostrar = (100-parseFloat(ajax.ler(), 10)).toFixed(0);  

comiREPRE = parseFloat(ajax.ler(), 10) / 100;
comiPRESTADORA = (100-parseFloat(ajax.ler(), 10)) / 100;  
vlrCALC= parseFloat(document.getElementById('txtRECEBIDO').value.replace(',','.'), 10);

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
}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'PROP_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = nomeREPRE+' ('+idREPRE+')';  col.width = '20%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = nomeTIPO+' ('+idTIPO+')';  col.width = '25%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = cpf;  col.width = '10%'; col.align='left';	
	  	
	col = lin.insertCell(-1); col.innerHTML = vlr;  col.width = '10%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = vlrRECEBIDO;  col.width = '10%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = vlrPRESTADORA + ' ('+comiPRESTADORA_mostrar+'%)';  col.width = '15%'; col.align='right';
	/*col = lin.insertCell(-1); col.innerHTML = vlrCORRETOR + ' ('+comiREPRE_mostrar+'%)';  col.width = '15%'; col.align='right'; */
	col = lin.insertCell(-1); col.innerHTML = vlrADESAO;  col.width = '10%'; col.align='right';    
	   	
  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onclick = function() { removePROPOSTA(lin.id); }  	
  
  col = lin.insertCell(-1); col.innerHTML = idREPRE; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = idTIPO; col.style.display='none';
  
  col = lin.insertCell(-1); col.innerHTML = nomeREPRE; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = nomeTIPO; col.style.display='none'; 
  
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onmousedown = function() {editarPROPOSTA(this.id); }    
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
//lstCHEQUES(-1);
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

document.getElementById('txtOPERADORA').value=''; document.getElementById('lblOPERADORA').innerHTML='';
document.getElementById('txtVLRBOLETO').value='';

var tab = document.getElementById('tabPGTO');
if (tab) {
  for (var y=0; y<=tab.rows.length-1; y++) {
    tab.rows[y].style.color='black';
    tab.rows[y].style.fontWeight='normal';
  }
  setTimeout("document.getElementById('txtOPERADORA').focus()", 200);
}
document.getElementById('pgtoEDITANDO').value='';
}

/*******************************************************************************/
function limparCmpsCartao() {

document.getElementById('txtBANCO').value=''; document.getElementById('lblBANCO').innerHTML='';
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
  
  if (idLIN == idLinExcluir) {tab.deleteRow( x );break;}  
}
atlVLR_DEVIDO();
}


/*******************************************************************************/
function atlVLR_DEVIDO() {
var tab = document.getElementById('tabPROPOSTAS');
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
  vlrTOTAL += parseFloat(valor.replace(',','.'), 10);
}

vlrJUSTIFICADO=0;
var tab = document.getElementById('tabPGTO');
for (var f=0; f<=tab.rows.length-1; f++) {

  if (tab.rows[f].cells[0].innerHTML.trim()=='CHEQUE') coluna=7;
  if (tab.rows[f].cells[0].innerHTML.trim()=='BOLETO') coluna=5;  
  if (tab.rows[f].cells[0].innerHTML.trim()=='CARTÃO') coluna=5;  
  if (tab.rows[f].cells[0].innerHTML.trim()=='VALE') coluna=5;  

  valor=tab.rows[f].cells[coluna].innerHTML;
  vlrJUSTIFICADO += parseFloat(valor.replace(',','.'), 10);
} 
 
vlrFALTANDO=vlrTOTAL - vlrJUSTIFICADO;
document.getElementById('lblVLR_PROP').innerHTML = vlrPRESTADORA.toFixed(2).toString().replace('.',',');
document.getElementById('lblVLR_ADESAO').innerHTML = vlrADESAO.toFixed(2).toString().replace('.',',');
document.getElementById('lblTOTAL').innerHTML = vlrTOTAL.toFixed(2).toString().replace('.',',');
document.getElementById('lblJUSTIFICADO').innerHTML = vlrJUSTIFICADO.toFixed(2).toString().replace('.',',');
document.getElementById('lblFALTANDO').innerHTML = vlrFALTANDO.toFixed(2).toString().replace('.',',');

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
var tab = document.getElementById('tabPGTO');
for (var y=0; y<=tab.rows.length-1; y++) {
  if (tab.rows[y].cells[0].innerHTML!=item) {
    alert('Não é possível combinar formas de pgto. Você já está usando o tipo '+tab.rows[y].cells[0].innerHTML);
    return false;  
  }
}
return true;
}
                  
/*******************************************************************************/
function addVALE()     {
if (!verificaCOMBINACAO('vale')) return;


showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addVALE', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS(); 
  
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function addDINHEIRO()     {
alert('nao disponivel ainda');
}


/*******************************************************************************/
function addCHEQUE()     {
showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addCHEQUE', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS(); 
  
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function addBOLETO()     {
showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addBOLETO', '', 0);
showAJAX(0);  

var divTRAB = document.getElementById('divPGTO');
divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
divTRAB.innerHTML = ajax.ler();

Muda_CSS();
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function addCARTAO()     {
showAJAX(1);
ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addCARTAO', '', 0);
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
            '<td>_cinzaBanco: </font></td><td align=left width="350px">_azul'+nomeBANCO+' ('+idBANCO+')</font></td>'+
            '<td>_cinzaData: </font></td><td align=left width="80px">_azul'+document.getElementById('txtDATACH').value+'</font></td>'+            
            '<td>_cinzaValor: </font></td><td align=right width="80px">_azul'+vlrCH+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
    detalhes = detalhes.replace('/_cinza', '</font>');    
  }
}
if (document.getElementById('txtVLRBOLETO')) {
  idOPERADORA = document.getElementById('txtOPERADORA').value.trim();
  nomeOPERADORA=document.getElementById('lblOPERADORA').innerHTML;
  if (idOPERADORA=='' || nomeOPERADORA.indexOf('ERRO')!=-1) {
    alert('Identifique a operadora');
    document.getElementById('txtOPERADORA').focus();
    return;
  }
  
  var vlrBOLETO = document.getElementById('txtVLRBOLETO').value.rtrim().ltrim();
  vlrBOLETO = vlrBOLETO=='' ? '0' : vlrBOLETO ; vlrBOLETO = parseFloat(vlrBOLETO.replace(',','.'), 10);
  if (vlrBOLETO==0) {
    alert('Preencha o valor do boleto'); document.getElementById('txtVLRBOLETO').focus(); return;
  }   
  vlrBOLETO = vlrBOLETO.toFixed(2).toString().replace('.',',');
  
  modo='BOLETO';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaOperadora: </font></td><td align=left width="200px">_azul'+nomeOPERADORA+' ('+idOPERADORA+')</font></td>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrBOLETO+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
    detalhes = detalhes.replace('/_cinza', '</font>');    
  }
}

if (document.getElementById('txtVLRCARTAO')) {
  idBANCO = document.getElementById('txtBANCO').value.trim();
  nomeBANCO=document.getElementById('lblBANCO').innerHTML;
  if (idBANCO=='' || nomeBANCO.indexOf('ERRO')!=-1) {
    alert('Identifique o banco');
    document.getElementById('txtBANCO').focus();
    return;
  }
  
  var vlrCARTAO = document.getElementById('txtVLRCARTAO').value.rtrim().ltrim();
  vlrCARTAO = vlrCARTAO=='' ? '0' : vlrCARTAO ; vlrCARTAO = parseFloat(vlrCARTAO.replace(',','.'), 10);
  if (vlrCARTAO==0) {
    alert('Preencha o valor do cartão'); document.getElementById('txtVLRCARTAO').focus(); return;
  }   
  vlrCARTAO = vlrCARTAO.toFixed(2).toString().replace('.',',');
  
  modo='CARTÃO';
  detalhes='<table  border=0><tr>'+
            '<td>_cinzaBanco: </font></td><td align=left width="300px">_azul'+nomeBANCO+' ('+idBANCO+')</font></td>'+
            '<td>_cinzaValor: </font></td><td align=right width="60px">_azul'+vlrCARTAO+'</font></td>'+            
            '</tr></td>'+
            '</table>';            

  for (i=0; i<10; i++) {
    detalhes = detalhes.replace('_cinza', '<font color=gray>');
    detalhes = detalhes.replace('_azul', '<font style="color:blue;font-size:12px;">');    
    detalhes = detalhes.replace('/_cinza', '</font>');    
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
    detalhes = detalhes.replace('/_cinza', '</font>');    
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
  }
  if (document.getElementById('txtVLRBOLETO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = idOPERADORA;   	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = nomeOPERADORA; 
	 tab.rows[linEDITANDO].cells[5].innerHTML = vlrBOLETO;      	 
  }
  if (document.getElementById('txtVLRCARTAO')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = idBANCO;   	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = nomeBANCO; 
	 tab.rows[linEDITANDO].cells[5].innerHTML = vlrCARTAO;      	 
  }
  if (document.getElementById('txtVLRVALE')) { 
	 tab.rows[linEDITANDO].cells[3].innerHTML = idREPRE;   	 
	 tab.rows[linEDITANDO].cells[4].innerHTML = nomeREPRE; 
	 tab.rows[linEDITANDO].cells[5].innerHTML = vlrVALE;      	 
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
  }
  if (document.getElementById('txtVLRBOLETO')) {
    col = lin.insertCell(-1); col.innerHTML = idOPERADORA;   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = nomeOPERADORA;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = vlrBOLETO;   col.style.display='none';      	 
  }
  if (document.getElementById('txtVLRCARTAO')) {
    col = lin.insertCell(-1); col.innerHTML = idBANCO;   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = nomeBANCO;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = vlrCARTAO;   col.style.display='none';      	 
  }
  if (document.getElementById('txtVLRVALE')) {
    col = lin.insertCell(-1); col.innerHTML = idREPRE;   col.style.display='none';	 
    col = lin.insertCell(-1); col.innerHTML = nomeREPRE;   col.style.display='none';
    col = lin.insertCell(-1); col.innerHTML = vlrVALE;   col.style.display='none';      	 
  }   	
     	
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onmousedown = function() {editarPGTO(this.id); }    
  lin.width='100%';
}
atlVLR_DEVIDO();
if (document.getElementById('txtVLRCH')) limparCmpsCheque();
if (document.getElementById('txtVLRBOLETO')) limparCmpsBoleto();
if (document.getElementById('txtVLRCARTAO')) limparCmpsCartao();
if (document.getElementById('txtVLRVALE')) limparCmpsVale();
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
    tab.rows[x].style.color='blue';
    tab.rows[x].style.fontWeight='bold';
    
    if (tab.rows[x].cells[0].innerHTML.trim()=='CHEQUE') {
      showAJAX(1);
      ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addCHEQUE', '', 0);
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
        
      setTimeout("document.getElementById('txtCHEQUE').focus()", 200);      
    }
    
    if (tab.rows[x].cells[0].innerHTML.trim()=='BOLETO') {
      showAJAX(1);
      ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addBOLETO', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtOPERADORA').value = tab.rows[x].cells[3].innerHTML;
      document.getElementById('lblOPERADORA').innerHTML = tab.rows[x].cells[4].innerHTML;
      document.getElementById('txtVLRBOLETO').value = tab.rows[x].cells[5].innerHTML;
        
      setTimeout("document.getElementById('txtOPERADORA').focus()", 200);      
    }
    
    if (tab.rows[x].cells[0].innerHTML.trim()=='CARTÃO') {
      showAJAX(1);
      ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addCARTAO', '', 0);
      showAJAX(0);  

      var divTRAB = document.getElementById('divPGTO');
      divTRAB.setAttribute(propCLASSE, 'cssDIV_PGTO');    
      divTRAB.innerHTML = ajax.ler();

      Muda_CSS(); 
    
      tab.rows[x].style.color='blue';
      tab.rows[x].style.fontWeight='bold';    
      
      document.getElementById('txtBANCO').value = tab.rows[x].cells[3].innerHTML;
      document.getElementById('lblBANCO').innerHTML = tab.rows[x].cells[4].innerHTML;
      document.getElementById('txtVLRCARTAO').value = tab.rows[x].cells[5].innerHTML;
        
      setTimeout("document.getElementById('txtBANCO').focus()", 200);      
    }
    if (tab.rows[x].cells[0].innerHTML.trim()=='VALE') {
      showAJAX(1);
      ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=addVALE', '', 0);
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
  }
}
document.getElementById('pgtoEDITANDO').value=idLinEditar;
}



//]]>
</script>
</body>
</html>
