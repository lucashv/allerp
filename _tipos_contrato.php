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

<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 600px; height: 50px;	
margin-top: -290px; margin-left: -290px; display:block; z-index:3;}
 
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>


</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerOPERADORAS();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Tornar ativo/inativo</a></li>  
</ul>

<form id="frmTIPOS_CONTRATO" name="frmTIPOS_CONTRATO" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >
<input id="somenteATIVOS" type="hidden" value="S" />

<input id="operadoraATUAL" type="hidden" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">
      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
      
        <td width="80%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>

        <td title="Alternar entre ativo/inativo" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >
          <img src="images/trocar.png" />
        </td>    
        
        
      </tr></table></td></tr>

      <tr>
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .65); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>

      <td width="30%"><div id=divOPERADORAS></div></td>
      
              <input id="txtFOCADO" type="text" value="" 
          style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />


      
    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 22;

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtOPERADORA;Identifique a operadora';
aCMPS[1]='txtDESCRICAO;Digite descrição do tipo';

var ajax = new execAjax();

/*******************************************************************************/
function lerOPERADORAS()         {

showAJAX(1);
ajax.criar('ajax/ajaxPROPOSTAS.php?acao=lerOPERADORAS', '', 0);  
showAJAX(0);

document.getElementById('divOPERADORAS').innerHTML = ajax.ler().split('^')[0];
document.getElementById('operadoraATUAL').value = ajax.ler().split('^')[1];
}

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

lCTRL = e.ctrlKey;

var lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (tecla==45 && ! lJanRegistro)   	incluirREG();
if (tecla==113 && lJanRegistro) document.getElementById('btnGRAVAR').click();   

if  (tecla==27) {
  if (lJanAuxilio)   	fecharAUXILIO();        
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if (lJanRegistro)  eval("teclasNavegacao(e);");

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
function ColocaFocoCmpInicial(cmp)   {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else if (lJanRegistro )   {
	document.getElementById('txtOPERADORA').focus();
  nQtdeCamposTextForm = 22;
}  	
else 
  document.getElementById('txtFOCADO').focus();  	
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);
document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';
             
ajax.criar('ajax/ajaxTIPOS_CONTRATO.php?acao=lerREGS&ativos='+
  document.getElementById('somenteATIVOS').value+'&operadora='+
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
  
  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Tipos de contratos</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativos)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativos)';
  else  
    titulo.innerHTML += '(Todos)';
  
  
  document.getElementById('SELECAO').value='';   
  
  VerificaAcaoInicial();      
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxTIPOS_CONTRATO.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');
  
  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler().split('^')[0];
  
  /* muda estilo de campos text, span para o padrão do site */
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
ajax.criar('ajax/ajaxTIPOS_CONTRATO.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')         {
  document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";
  
	switch (nomeCMP) {
		case 'txtOPERADORA':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
      
			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');  
    	
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();
    	
    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';
        
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
		switch (cCMP)   {
			case 'txtDESCRICAO':
        if ( cVLR=='' )   erro=1;
        break;
        
			case 'txtOPERADORA':        			 
        if ( document.getElementById(label).innerHTML=='' ||
            document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;
        
 		}
 		if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}
 	}	
		
  var vlrADESAO = document.getElementById('txtADESAO').value.rtrim().ltrim();
  vlrADESAO = vlrADESAO=='' ? '0' : vlrADESAO.replace(',','.');   

  if (document.getElementById('txtQTDE1').value.trim()=='') document.getElementById('txtQTDE1').value='null';
  if (document.getElementById('txtQTDE2').value.trim()=='') document.getElementById('txtQTDE2').value='null';
  if (document.getElementById('txtQTDE3').value.trim()=='') document.getElementById('txtQTDE3').value='null';
  if (document.getElementById('txtQTDE4').value.trim()=='') document.getElementById('txtQTDE4').value='null';
  if (document.getElementById('txtQTDE5').value.trim()=='') document.getElementById('txtQTDE5').value='null';
  if (document.getElementById('txtQTDE6').value.trim()=='') document.getElementById('txtQTDE6').value='null';
  if (document.getElementById('txtPERC1').value.trim()=='') document.getElementById('txtPERC1').value='null';
  if (document.getElementById('txtPERC2').value.trim()=='') document.getElementById('txtPERC2').value='null';
  if (document.getElementById('txtPERC3').value.trim()=='') document.getElementById('txtPERC3').value='null';
  
  if (document.getElementById('txtVIDAS1').value.trim()=='') document.getElementById('txtVIDAS1').value='null';
  if (document.getElementById('txtVIDAS2').value.trim()=='') document.getElementById('txtVIDAS2').value='null';  
  if (document.getElementById('txtVIDAS3').value.trim()=='') document.getElementById('txtVIDAS3').value='null';
  if (document.getElementById('txtVIDAS4').value.trim()=='') document.getElementById('txtVIDAS4').value='null';    
  if (document.getElementById('txtVIDAS5').value.trim()=='') document.getElementById('txtVIDAS5').value='null';
  if (document.getElementById('txtVIDAS6').value.trim()=='') document.getElementById('txtVIDAS6').value='null';
  if (document.getElementById('txtVIDAS7').value.trim()=='') document.getElementById('txtVIDAS7').value='null';
  if (document.getElementById('txtVIDAS8').value.trim()=='') document.getElementById('txtVIDAS8').value='null';        
  if (document.getElementById('txtVIDAS9').value.trim()=='') document.getElementById('txtVIDAS9').value='null';
  if (document.getElementById('txtVIDAS10').value.trim()=='') document.getElementById('txtVIDAS10').value='null';    
  

  cmps= document.getElementById('numREG').value + '|'+ 
        document.getElementById('txtDESCRICAO').value+'|'+                        
        document.getElementById('txtOPERADORA').value+'|'+
        vlrADESAO+'|'+
        document.getElementById('txtQTDE1').value+'|'+document.getElementById('txtQTDE2').value+'|'+document.getElementById('txtPERC1').value+'|'+
        document.getElementById('txtQTDE3').value+'|'+document.getElementById('txtQTDE4').value+'|'+document.getElementById('txtPERC2').value+'|'+
        document.getElementById('txtQTDE5').value+'|'+document.getElementById('txtQTDE6').value+'|'+document.getElementById('txtPERC3').value+'|'+
        document.getElementById('txtVIDAS1').value+'|'+document.getElementById('txtVIDAS2').value+'|'+
        document.getElementById('txtVIDAS3').value+'|'+document.getElementById('txtVIDAS4').value+'|'+
        document.getElementById('txtVIDAS5').value+'|'+document.getElementById('txtVIDAS6').value+'|'+
        document.getElementById('txtVIDAS7').value+'|'+document.getElementById('txtVIDAS8').value+'|'+
        document.getElementById('txtVIDAS9').value+'|'+document.getElementById('txtVIDAS10').value;                
        
                
  rdBUTTON = document.forms['frmTIPOS_CONTRATO'].elements['tipo'];
  var tipo='';                    
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipo = rdBUTTON[i].value;
  }

	showAJAX(1);
	ajax.criar('ajax/ajaxTIPOS_CONTRATO.php?acao=gravar&vlr=' + cmps+'&tipo='+tipo+
              '&prod='+document.forms[0].vlrPRODUCAO.checked, '', 0);
	showAJAX(0);
	
  resp = ajax.ler();

  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
  	cID = resp.substring(resp.indexOf(';')+1);
  	window.top.document.getElementById('infoTrab').value = 'frmTIPOS_CONTRATO:GRAVOU=' + cID  ;
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

ajax.criar('ajax/ajaxTIPOS_CONTRATO.php?acao=mudarSITUACAO&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmTIPOS_CONTRATO:GRAVOU=' + id
lerREGS();  
}

/************************************/
function alternar()  {
ativos = document.getElementById('somenteATIVOS').value;
 
if (ativos=='S') var agora='N';
else if (ativos=='N') var agora='';
else var agora='S';

document.getElementById('somenteATIVOS').value = agora;
setTimeout('void(0)', 200);

lerREGS();
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
function tiraFocoOperadora(id) {
                                                                               
if (document.getElementById('operadoraATUAL').value==id.replace('operadora','')) 
  document.getElementById(id).style.backgroundColor='lightgrey';
else
  document.getElementById(id).style.backgroundColor='white';
}

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmTIPOS_CONTRATO'].elements['tipo'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtGRUPO').focus();
}


/********************************************************************************/
function checar()  {

rdBUTTON = document.forms['frmTIPOS_CONTRATO'].vlrPRODUCAO;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtOPERADORA').focus();
}







//]]></script>
  </body>
</html>
