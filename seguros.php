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
position: absolute; top: 200px;  width: 980px; height: 400px;	
margin-top: -300px; margin-left: -490px; display:block; z-index:3;}

.cssDIV_SINISTRO {
position: absolute; top: 200px;  width: 580px; height: 200px;	
margin-top: -120px; margin-left: -300px; display:block; z-index:3;}

.cssDIV_RENOVACAO {
position: absolute; top: 200px;  width: 580px; height: 200px;	
margin-top: -120px; margin-left: -300px; display:block; z-index:3;}



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
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Tornar ativo/inativo</a></li>  
</ul>

<form id="frmSEGUROS" name="frmSEGUROS" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divSINISTRO" class="cssDIV_ESCONDE"></div>
<div id="divRENOVACAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id=SELECAO_2 type=hidden value="" >
<input id="somenteATIVOS" type="hidden" value="S" />
<input id="tipoDATA" type="hidden" value="assinadas" />

<input id="dataTRAB" type="hidden" value="" >

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">

      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="60%">
          <table width="100%"><tr ><td>
            <span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span>
            <input type="text" id="txtDATATRAB" value="" 
                style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
                onchange="lerHOJE(1);lerREGS();" />        
          </td></tr>
          <tr>
            <td width="300px"><span class="lblTitJanela" id="lblFILTRO">&nbsp;&nbsp; </span></td>
            <td><input id=btnFILTRO style="cursor:pointer" type=button class=botaoMudarPlano value="" onclick="alternaTIPO()"; /></td>
          </td></tr>
          </table>
        </td>
        
        <td title="Alternar entre ativo/inativo" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >
          <img src="images/trocar.png" />
        </td>

      	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
      	  <img src="images/buscadata.png" />
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
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .65); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>

      <tr><td><table width="100%"><tr>
        <td>
          &nbsp;<span class="lblPADRAO">Cliente:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:300px;" maxlength="50" />
        </td>

        <td width="'30%" align="right">
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
var nQtdeCamposTextForm = 12;

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtTIPOCLIENTE;Identifique o tipo do cliente';
aCMPS[1]='txtNOME;Preencha o nome do cliente';
aCMPS[2]='txtNASC;Preencha a data de nascimento corretamente ou deixe em branco';
aCMPS[3]='txtTIPOSEGURO;Identifique o tipo do seguro';
aCMPS[4]='txtSEGURADORA;Identifique a seguradora';
aCMPS[5]='txtCORRETOR;Identifique o corretor';
aCMPS[6]='txtASSINATURA;Preencha a data de assinatura corretamente';
aCMPS[7]='txtAPOLICE;Preencha o número da apólice';
aCMPS[8]='txtVALOR;Preencha o valor da apólice';
aCMPS[9]='txtTIPOSINISTRO;';
aCMPS[10]='txtDATASINISTRO;';
aCMPS[11]='txtDATALIBERACAO;';
aCMPS[12]='txtRENOVACAO_CORRETOR;';

Calendar.setup({
inputField:    "txtDATATRAB",     
ifFormat  :     "%d/%m/%Y",     
button    :    "btnDATATRAB"    
});


var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanSINISTRO= document.getElementById('divSINISTRO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanRENOVACAO= document.getElementById('divRENOVACAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==27) {
  if (lJanAuxilio)   	fecharAUXILIO();        
  else if (lJanSINISTRO)   	fecharSINISTRO();
  else if (lJanRENOVACAO)   	fecharRENOVACAO();
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
 
if  (tecla==13 && ! lJanAuxilio && ! lJanRegistro && ! lJanSINISTRO && ! lJanRENOVACAO ) {
  lerREGS();
  return; 
}  

if  (tecla==13) {         
  if (lJanAuxilio)   	usouAUXILIO();
  if (lJanSINISTRO && cfoco=='txtTERCEIROS')   	{verSINISTRO();return;}
  if (lJanRENOVACAO && cfoco=='txtCOMISSAO')   	{verRENOVACAO();return;}
}

if  (tecla==40) {         
  if (lJanSINISTRO && cfoco=='txtTERCEIROS')   	{verSINISTRO();return;}
  if (lJanRENOVACAO && cfoco=='txtCOMISSAO')   	{verRENOVACAO();return;}
}

if  (tecla==45 && ! lJanRegistro)   	incluirREG();
if (lJanRegistro)  {
  if (cfoco!='txtOBS') eval("teclasNavegacao(e);");
}

var lF2=tecla==113;

if  ( tecla==119 && lJanRegistro )  AuxilioF7(cfoco);  

if  ( lF2 && lJanRegistro && ! lJanSINISTRO && ! lJanRENOVACAO  )  
  document.getElementById('btnGRAVAR').click();

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
function fecharSINISTRO()     {
document.getElementById("divSINISTRO").innerHTML='';
document.getElementById("divSINISTRO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function fecharRENOVACAO()     {
document.getElementById("divRENOVACAO").innerHTML='';
document.getElementById("divRENOVACAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}




/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanSINISTRO= document.getElementById('divSINISTRO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanRENOVACAO= document.getElementById('divRENOVACAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else if (lJanSINISTRO) 
	document.getElementById('txtTIPOSINISTRO').focus();
else if (lJanRENOVACAO) 
	document.getElementById('txtRENOVACAO_CORRETOR').focus();
else if (lJanRegistro ) 
	document.getElementById('txtTIPOCLIENTE').focus();
}	

/*******************************************************************************/
function lerREGS( avancarDATA  ) {

if ( typeof(avancarDATA)=='undefined' )  avancarDATA=0;
showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxSEGUROS.php?acao=lerREGS&ativos='+
  document.getElementById('somenteATIVOS').value+'&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2='+avancarDATA+
  '&tipo='+document.getElementById('tipoDATA').value+'&vlr3='+
    document.getElementById('txtPR').value, desenhaTabela);
}


/*******************************************************************************/
function  desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1];
  var anoMES = aRESP[1].split('^')[2]; 

  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  centerDiv( 'divSINISTRO' );
  centerDiv( 'divRENOVACAO' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Apólices de seguro</font>&nbsp;&nbsp;&nbsp;&nbsp;';

  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativas)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativas)';
  else  
    titulo.innerHTML += '(Todos)';

  if (anoMES.indexOf('FILTRO')==-1) {
    document.getElementById('lblFILTRO').innerHTML = '&nbsp;&nbsp;'+anoMES+ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    document.getElementById('btnFILTRO').value = "ALTERNAR";  
  }
  else {
    document.getElementById('lblFILTRO').innerHTML = '&nbsp;&nbsp;'+anoMES+ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ;
    document.getElementById('btnFILTRO').value = "CANCELAR FILTRO";
  }

  
  document.getElementById('dataTRAB').value = aRESP[1].split('^')[3] ;

  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxSEGUROS.php?acao=incluirREG', desenhaJanelaREG);
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
  ColocaFocoCmpInicial();
}
}  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
showAJAX(1);
ajax.criar('ajax/ajaxSEGUROS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

/* concatena */
dataNASC='null';   data = document.getElementById('txtNASC').value;
if (data.rtrim().ltrim()!='') 
  dataNASC = "20"+data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);
dataASSI='null';   data = document.getElementById('txtASSINATURA').value;
if (data.rtrim().ltrim()!='') 
  dataASSI = "20"+data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

var vlr=document.getElementById('txtVALOR').value.replace(',','.');
vlr=(vlr=='') ? 'null' : vlr;

var percentual=document.getElementById('txtPERCENTUAL').value.replace(',','.');
percentual=(percentual=='') ? 'null' : percentual;

cmps= document.getElementById('numREG').value+'|'+ 
      document.getElementById('txtTIPOCLIENTE').value+'|'+
      document.getElementById('txtNOME').value+'|'+
      dataNASC+'|'+
      document.getElementById('txtFONE').value+'|'+
      document.getElementById('txtEMAIL').value+'|'+
      document.getElementById('txtTIPOSEGURO').value+'|'+
      document.getElementById('txtSEGURADORA').value+'|'+
      document.getElementById('txtCORRETOR').value+'|'+
      dataASSI+'|'+
      document.getElementById('txtAPOLICE').value+'|'+
      vlr+'|'+
      percentual+'|'+
      escape( Encoder.htmlEncode(document.getElementById('txtOBS').value) );


var strSINISTROS=''; 
var tabSIN = document.getElementById('tabSINISTROS');   
for (var l=0; l<=tabSIN.rows.length-1; l++) {
    
  data = tabSIN.rows[l].cells[1].innerHTML;
  dataSINISTRO='null';
  if (data.rtrim().ltrim()!='') 
    dataSINISTRO = "20"+data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);
  data = tabSIN.rows[l].cells[2].innerHTML;
  dataLIBERACAO='null';
  if (data.rtrim().ltrim()!='') 
    dataLIBERACAO = "20"+data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  strSINISTROS += (strSINISTROS=='' ? '' : '|') ;
  strSINISTROS += tabSIN.rows[l].cells[5].innerHTML+';'+  
           dataSINISTRO+';'+    
           dataLIBERACAO+';'+
           tabSIN.rows[l].cells[3].innerHTML+';'+
           tabSIN.rows[l].cells[5].innerHTML;
}

var strRENOVACOES=''; 
var tabRENOVA = document.getElementById('tabRENOVACOES');   
for (var l=0; l<=tabRENOVA.rows.length-1; l++) {
    
  strRENOVACOES += (strRENOVACOES=='' ? '' : '|') ;
  strRENOVACOES += tabRENOVA.rows[l].cells[6].innerHTML+';'+  
           tabRENOVA.rows[l].cells[1].innerHTML+';'+
            tabRENOVA.rows[l].cells[2].innerHTML+';'+
          tabRENOVA.rows[l].cells[3].innerHTML.replace(',','.')+';'+
          tabRENOVA.rows[l].cells[4].innerHTML.replace(',','.');
}



if (nomeCMP!='todos')         {
	switch (nomeCMP) {
		case 'txtTIPOCLIENTE':		
		case 'txtTIPOSEGURO':
		case 'txtSEGURADORA':
		case 'txtCORRETOR':
		case 'txtTIPOSINISTRO':
		case 'txtRENOVACAO_CORRETOR':
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
    if (! document.getElementById(cCMP)) continue;
 
		cMSG = cmp[1];
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl'); 		
				
		erro=0;
		switch (cCMP)   {
			case 'txtNOME':
			case 'txtAPOLICE':
        if ( cVLR=='' ) erro=1;
        break;

			case 'txtASSINATURA':
        if ( cVLR=='' || ! verifica_data(cCMP) )   erro=1;
        break;

			case 'txtNASC':
        if ( cVLR!='' && ! verifica_data(cCMP) )   erro=1;
        break;

  		case 'txtTIPOCLIENTE':		
  		case 'txtTIPOSEGURO':
  		case 'txtSEGURADORA':
  		case 'txtCORRETOR':
        if ( document.getElementById(label).innerHTML=='' ||
            document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	ajax.criar('ajax/ajaxSEGUROS.php?acao=gravar&vlr=' + cmps+'&sin='+strSINISTROS+'&renova='+strRENOVACOES, gravou);
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
  	cID= cID.replace('INC_', '');
  	window.top.document.getElementById('infoTrab').value = 'frmSEGUROS:GRAVOU=' + cID
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

ajax.criar('ajax/ajaxSEGUROS.php?acao=mudarSITUACAO&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmSEGUROS:GRAVOU=' + id
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


/************************************/
function alternaTIPO()  {

if (document.getElementById('btnFILTRO').value == "CANCELAR FILTRO") cancelarFILTRO();
else {
  tipo = document.getElementById('tipoDATA').value;
  document.getElementById('tipoDATA').value = (tipo=='assinadas' ? 'vencendo em' : 'assinadas');
}


lerREGS();
}

/************************************/
function cancelarFILTRO()  {
document.getElementById('txtPR').value='';

lerREGS();
}

/*******************************************************************************/
function editarSINISTRO(id) {
	
showAJAX(1);

tipo=''; lbltipo='';
dataSIN=document.getElementById('txtDATATRAB').value;
dataLIB=document.getElementById('txtDATATRAB').value;
terceiros='';

var tab = document.getElementById('tabSINISTROS');
for (var f=0; f<=tab.rows.length-1; f++) {
  var idLIN = tab.rows[f].id;
  if (idLIN==id) { 
    tipo=tab.rows[f].cells[5].innerHTML;
    lbltipo=tab.rows[f].cells[7].innerHTML;
    dataSIN=tab.rows[f].cells[1].innerHTML;
    dataLIB=tab.rows[f].cells[2].innerHTML;
    terceiros=tab.rows[f].cells[3].innerHTML;
    break;
  }
}
ajax.criar('ajax/ajaxSEGUROS.php?acao=editarSINISTRO&tipo='+
   + tipo+'&lbltipo='+lbltipo+'&datasin='+dataSIN+'&datalib='+dataLIB+'&terceiros='+terceiros+'&idLIN='+id, '', 0);

var divSINISTRO = document.getElementById('divSINISTRO');
divSINISTRO.setAttribute(propCLASSE, 'cssDIV_SINISTRO');    divSINISTRO.innerHTML = ajax.ler().split('^')[0];
Muda_CSS();

  
showAJAX(0);
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function verSINISTRO() {

cVLR=document.getElementById('txtDATASINISTRO').value;
if (cVLR=='' || ! verifica_data('txtDATASINISTRO'))    {alert('Preencha a data do sinistro válida'); 
document.getElementById('txtDATASINISTRO').focus();return;}

cVLR=document.getElementById('txtDATALIBERACAO').value;
if ( cVLR!='' && ! verifica_data('txtDATALIBERACAO') )   {alert('Preencha a data da liberação válida'); 
document.getElementById('txtDATALIBERACAO').focus(); return;}

label='lblTIPOSINISTRO';
if ( document.getElementById(label).innerHTML=='' ||
    document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) {
  alert('Identifique o tipo do sinistro'); document.getElementById('txtTIPOSINISTRO').focus(); return;
}

var linEDITANDO=-1;
var maiorID = -1;
var tab = document.getElementById('tabSINISTROS');

for (var f=0; f<=tab.rows.length-1; f++) {
  var numLIN = tab.rows[f].id.replace('SINISTRO_','');
  var idLIN = tab.rows[f].id;
    
  if (parseInt(numLIN, 10) > maiorID) maiorID = parseInt(numLIN, 10);
  if (document.getElementById('idLINHA').value==idLIN) linEDITANDO=f;
}

if (linEDITANDO!=-1) { 
  tab.rows[linEDITANDO].cells[0].innerHTML = document.getElementById('lblTIPOSINISTRO').innerHTML+' ('+
          document.getElementById('txtTIPOSINISTRO').value+')';
  tab.rows[linEDITANDO].cells[1].innerHTML = document.getElementById('txtDATASINISTRO').value; 
  tab.rows[linEDITANDO].cells[2].innerHTML = document.getElementById('txtDATALIBERACAO').value;
  tab.rows[linEDITANDO].cells[3].innerHTML = document.getElementById('txtTERCEIROS').value;
  tab.rows[linEDITANDO].cells[5].innerHTML = document.getElementById('txtTIPOSINISTRO').value;
  tab.rows[linEDITANDO].cells[7].innerHTML = document.getElementById('lblTIPOSINISTRO').innerHTML;

}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'SINISTRO_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = document.getElementById('lblTIPOSINISTRO').innerHTML+' ('+
          document.getElementById('txtTIPOSINISTRO').value+')';  col.width = '45%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtDATASINISTRO').value; col.width = '10%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtDATALIBERACAO').value; col.width = '10%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtTERCEIROS').value; col.width = '10%'; col.align='left';

  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onmousedown = function() { removeSINISTRO(lin.id); }  	

  col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtTIPOSINISTRO').value; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = ''; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = document.getElementById('lblTIPOSINISTRO').innerHTML; col.style.display='none';
  
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onclick = function() {editarSINISTRO(this.id); }    
  lin.width='100%';
}

fecharSINISTRO()
}

/*******************************************************************************/
function removeSINISTRO(idLinExcluir)    {

var tab = document.getElementById('tabSINISTROS');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN == idLinExcluir) {
    tab.deleteRow( x );break;    
  }  
}
}




/*******************************************************************************/
function editarRENOVACAO(id) {
	
showAJAX(1);

corretor=''; lblcorretor='';
parcelas='';protecao='';premio='';comissao='';

var tab = document.getElementById('tabRENOVACOES');
for (var f=0; f<=tab.rows.length-1; f++) {
  var idLIN = tab.rows[f].id;
  if (idLIN==id) { 
    corretor=tab.rows[f].cells[6].innerHTML;
    lblcorretor=tab.rows[f].cells[7].innerHTML;
    protecao=tab.rows[f].cells[1].innerHTML;
    parcelas=tab.rows[f].cells[2].innerHTML;
    premio=tab.rows[f].cells[3].innerHTML;
    comissao=tab.rows[f].cells[4].innerHTML;
    break;
  }
}
ajax.criar('ajax/ajaxSEGUROS.php?acao=editarRENOVACAO&corretor='+
   + corretor+'&lblcorretor='+lblcorretor+'&protecao='+protecao+'&parcelas='+parcelas+'&premio='+premio+'&comissao='+
      comissao+'&idLIN='+id, '', 0);

var divRENOVACAO= document.getElementById('divRENOVACAO');
divRENOVACAO.setAttribute(propCLASSE, 'cssDIV_RENOVACAO');    divRENOVACAO.innerHTML = ajax.ler().split('^')[0];
Muda_CSS();

  
showAJAX(0);
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function verRENOVACAO() {

label='lblRENOVACAO_CORRETOR';
if ( document.getElementById(label).innerHTML=='' ||
    document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) {
  alert('Identifique o corretor da renovaçao'); document.getElementById('txtRENOVACAO_CORRETOR').focus(); return;
}

var linEDITANDO=-1;
var maiorID = -1;
var tab = document.getElementById('tabRENOVACOES');

for (var f=0; f<=tab.rows.length-1; f++) {
  var numLIN = tab.rows[f].id.replace('RENOVA_','');
  var idLIN = tab.rows[f].id;
    
  if (parseInt(numLIN, 10) > maiorID) maiorID = parseInt(numLIN, 10);
  if (document.getElementById('idLINHA').value==idLIN) linEDITANDO=f;
}

if (linEDITANDO!=-1) { 
  tab.rows[linEDITANDO].cells[0].innerHTML = document.getElementById('lblRENOVACAO_CORRETOR').innerHTML+' ('+
          document.getElementById('txtRENOVACAO_CORRETOR').value+')';
  tab.rows[linEDITANDO].cells[1].innerHTML = document.getElementById('txtPROTECAO').value; 
  tab.rows[linEDITANDO].cells[2].innerHTML = document.getElementById('txtPARCELAS').value;
  tab.rows[linEDITANDO].cells[3].innerHTML = document.getElementById('txtPREMIO').value;
  tab.rows[linEDITANDO].cells[4].innerHTML = document.getElementById('txtCOMISSAO').value;

}
else {
  var lin = tab.insertRow(-1);
  
  maiorID++;
  lin.id = 'RENOVA_' + maiorID.toString();
  
	var col = lin.insertCell(-1); col.innerHTML = document.getElementById('lblRENOVACAO_CORRETOR').innerHTML+' ('+
          document.getElementById('txtRENOVACAO_CORRETOR').value+')';  col.width = '35%'; col.align='left';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtPROTECAO').value; col.width = '15%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtPARCELAS').value; col.width = '15%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtPREMIO').value; col.width = '15%'; col.align='right';
	col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtCOMISSAO').value; col.width = '15%'; col.align='right';

  col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
  col.width = '5%'; col.onmousedown = function() { removeRENOVACAO(lin.id); }  	

  col = lin.insertCell(-1); col.innerHTML = document.getElementById('txtRENOVACAO_CORRETOR').value; col.style.display='none';
  col = lin.insertCell(-1); col.innerHTML = document.getElementById('lblRENOVACAO_CORRETOR').innerHTML; col.style.display='none';
  
  var corFORM = parent.window.document.getElementById('corFormJanela').value;
  lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
  lin.onmouseover = function() {
    this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
  lin.onclick = function() {editarRENOVACAO(this.id); }    
  lin.width='100%';
}

fecharRENOVACAO()
}

/*******************************************************************************/
function removeRENOVACAO(idLinExcluir)    {

var tab = document.getElementById('tabRENOVACOES');
for (var x=0; x<=tab.rows.length-1; x++) {
  var idLIN = tab.rows[x].id;
  
  if (idLIN == idLinExcluir) {
    tab.deleteRow( x );break;    
  }  
}
}







//]]></script>
  </body>
</html>
