<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script><script
type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script><script
type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 500px; height: 80px;	
margin-top: -130px; margin-left: -275px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

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
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>  
</ul>

<form id="frmCIDADES" name="frmCIDADES" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id=SELECAO_2 type=hidden value="" >


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="75%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="80%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;Munic�pios</span></td>
        
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
        <td>
          &nbsp;<span class="lblPADRAO">Pesquisa:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:200px;" onkeyup="PR();" maxlength="30" />
        </td>

        <td width="'30%" align="right">              
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
/* qtde de campos text na tela de edi��o, necessario informar  */
var nQtdeCamposTextForm = 2;

var largPR = 0;

var aCMPS=new Array(2);
aCMPS[0]='txtNOME;Digite o nome do munic�pio';
aCMPS[1]='txtUF;Identifique a UF do munic�pio';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==45 && ! lJanRegistro)   	incluirREG();
if  (tecla==27) {        
  if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
  
if  (tecla==13)        
  if (lJanAuxilio)   	usouAUXILIO();  

if (lJanRegistro)  eval("teclasNavegacao(e);");

var lF2=tecla==113;  

if  ( tecla==119 && lJanRegistro )  AuxilioF7(cfoco);

if  ( lF2 && lJanRegistro )   document.getElementById('btnGRAVAR').click();
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
else if (lJanRegistro )
	document.getElementById('txtUF').focus();
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxCIDADES.php?acao=lerREGS', desenhaTabela);
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
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Munic�pios</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxCIDADES.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');
  
  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();
  
  /* muda estilo de campos text, span para o padr�o do site */
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
ajax.criar('ajax/ajaxCIDADES.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

/* concatena */
cmps= document.getElementById('txtNOME').value+'|'+
      document.getElementById('txtUF').value+ '|' +
      document.getElementById('numREG').value; 

if (nomeCMP!='todos')         {

	switch (nomeCMP) {
		case 'txtUF':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
			
			cmpLBL.innerHTML = 'lendo...';
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			
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
			case 'txtNOME':
        if ( cVLR=='' ) erro=1;
        break;
        
			case 'txtUF':        			 
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 || cVLR=='' ) erro=1;
        break;
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	ajax.criar('ajax/ajaxCIDADES.php?acao=gravar&vlr=' + cmps, gravou);
}

}

/*******************************************************************************/
function gravou() {

if ( ajax.terminouLER() ) {
  showAJAX(0);
  
  resp = ajax.ler();
  
  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
  
  	cID = resp.substring(resp.indexOf(';')+1);
  	window.top.document.getElementById('infoTrab').value = 'frmCIDADES:GRAVOU=' + cID
  	lerREGS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
  
}


/************************************/
function PR()  {
var tabela = document.getElementById("tabREGs");
var PR = document.getElementById('txtPR').value.toUpperCase();    larg = PR.length;

if (larg < largPR) {largPR = larg; return; }

largPR = larg;

if (PR.rtrim().ltrim() != '')   {
	for(var lin=0;lin < tabela.rows.length; lin++) {
		if ( tabela.rows[lin].cells[1].innerHTML.substring(0, larg).toUpperCase() == PR ) {Selecionar(tabela.rows[lin].id, 1);break;}
	}
}
}

/*******************************************************************************/
function excluirREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

if ( ! confirm('Confirma exclus�o deste registro?') ) return;

ajax.criar('ajax/ajaxCIDADES.php?acao=excluirREG&vlr=' + id, '', 0);

if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao excluir!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmCIDADES:GRAVOU=' + id
lerREGS();  
}







//]]></script>
  </body>
</html>
