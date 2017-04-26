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
<style type="text/css" xml:space="preserve">
.cssDIV_PLANOS {
position: absolute; top: 200px;  width: 750px; height: 420px;	
margin-top: -238px; margin-left: -370px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}

.cssUPLOAD  {
position: absolute; top: 200px;  width: 570px; height: 230px;	
margin-top: -120px; margin-left: -280px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}

.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 780px; height: 80px;	
margin-top: -250px; margin-left: -400px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

.cssDIV_FUTURAS  {
position: absolute; top: 200px;  width: 500px; height: 280px;	
margin-top: -160px; margin-left: -240px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}


</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerREGS();Avisa('');Muda_CSS();centerDiv('divUPLOAD');centerDiv('divPLANOS');centerDiv('divFUTURAS');ColocaFocoCmpInicial();">

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
  <li><a href="javascript:planos();">Planos/faixas etárias/valores</a></li>
  <li><a href="javascript:abreUPLOAD_XLS();">Ler planilha de Usuarios</a></li>
  <li><a href="javascript:resumo();">Gerar planilha Resumo de Usuarios</a></li>
  <li><a href="javascript:futuras();">Mensalidades</a></li>
</ul>



<form id="frmESTIPULANTES" name="frmESTIPULANTES" autocomplete="off" action="" >

<div id="divUPLOAD" class="cssDIV_ESCONDE">
  <iframe src="" style="height:100%;width:100%;" scrolling="no" id="fraUPLOAD" frameborder="0" ></iframe>
</div>
<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<div id="divFUTURAS" class="cssDIV_ESCONDE" >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="90%"><span class="lblTitJanela" id="infoFUTURAS">&nbsp;&nbsp;&nbsp;FUTURAS</td>
    <td onclick="fechaFUTURAS();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

  <tr><td colspan="2" valign="top" height="20px" id="titFUTURAS"></td></tr>
  <tr><td colspan="2" valign="top" height="210px" id="tabFUTURAS"></td></tr>
  
  </table>  
</div>


<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" />

<input id="SELECAO_3" type="hidden" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="75%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="90%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;Empresas</span></td>
        
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

<div id="divPLANOS"  class="cssDIV_ESCONDE">
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

  <tr height="30px;">
    <td width="90%"><span class="lblTitJanela" id="infoPLANOS">&nbsp;&nbsp;&nbsp;Planos</td>
    <td onclick="fechaPLANOS();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
  </tr>

  <tr><td colspan="2" valign="top" height="260x" id="tabPLANOS" ></td></tr>

  <tr><td colspan="2"><hr></td></tr>

  <tr><td colspan="2" valign="center" height="20px" width="100%">
    <table width="100%">
      <tr><td><table width="100%"><tr>
        <td width="33%" align="center"><input type="button" value=" Novo plano " style="color:blue;font-size:17px;font-weigth:bold;" onclick="novoPLANO();"/></td>
        <td width="33%" align="center"><input type="button" value=" Editar plano " style="color:blue;font-size:17px;font-weigth:bold;" onclick="editarPLANO();"/></td>
        <td width="33%" align="center"><input type="button" value=" Excluir plano " style="color:blue;font-size:17px;font-weigth:bold;" onclick="excluirPLANO();"/></td>
      </tr></table></td></tr>
      <tr><td><table width="100%"><tr>
        <td width="25%" align="center"><input type="button" value=" Nova faixa etária " style="color:blue;font-size:17px;font-weigth:bold;" onclick="novaFAIXA();" /></td>
        <td width="25%" align="center"><input type="button" value=" Editar faixa etária " style="color:blue;font-size:17px;font-weigth:bold;" onclick="editarFAIXA();" /></td>
        <td width="25%" align="center"><input type="button" value=" Excluir faixa etária " style="color:blue;font-size:17px;font-weigth:bold;" onclick="excluirFAIXA();"/></td>
      </tr></table></td></tr>
      <tr><td><table width="100%"><tr>
        <td width="100%" align="center"><input type="button" value=" Editar preço " style="color:blue;font-size:17px;font-weigth:bold;" onclick="editarPRECO();"/></td>
      </tr></table></td></tr>

    </table> 
  </td></tr>
  
  </table>  
</div>


</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 32;

var largPR = 0;

var aCMPS=new Array(7);
aCMPS[0]='txtNOME;Digite o nome da empresa';
aCMPS[1]='txtNASC_RESPONSAVEL;Preencha data nascimento responsavel valida ou deixe em branco';
aCMPS[2]='txtNASC_RH;Preencha data nascimento contato RH valida ou deixe em branco';
aCMPS[3]='txtVIGENCIA;Preencha data vigencia valida ou deixe em branco';
aCMPS[4]='txtVENCTO;Preencha dia vencimento valida ou deixe em branco';
aCMPS[5]='txtREPRESENTANTE;Identifique representante valido ou deixe em branco';
aCMPS[6]='txtNUMCLINIPAM;Identifique o nº CLINIPAM';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanUPLOAD= document.getElementById('divUPLOAD').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanPLANOS= document.getElementById('divPLANOS').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanFUTURAS= document.getElementById('divFUTURAS').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==27) {        
  if (lJanUPLOAD)   	fechaUPLOAD();
  else if (lJanFUTURAS)   	fechaFUTURAS();
  else if (lJanPLANOS)   	fechaPLANOS();
  else if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
  
if  (tecla==45 && ! lJanRegistro)   	incluirREG();
if (lJanRegistro && ! lJanAuxilio)  {if (cfoco!='txtOBSERVACOES') eval("teclasNavegacao(e);");}

if  ( tecla==113 && lJanRegistro && ! lJanAuxilio && ! lJanPLANOS && ! lJanFUTURAS && ! lJanUPLOAD) document.getElementById('btnGRAVAR').click();
if  ( tecla==119 && lJanRegistro && ! lJanAuxilio && ! lJanPLANOS && ! lJanFUTURAS && ! lJanUPLOAD)  AuxilioF7(cfoco); 
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
lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)=='cssDIV_AUXILIO';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else if (lJanRegistro )
	document.getElementById('txtNOME').focus();
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxESTIPULANTES.php?acao=lerREGS', desenhaTabela);
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
  
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');
  
  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();
  
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
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

if (nomeCMP!='todos')         {
	switch (nomeCMP) {
		case 'txtREPRESENTANTE':
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
			case 'txtNUMCLINIPAM':
        if ( cVLR=='' ) erro=1;
        break;

		case 'txtREPRESENTANTE':
        if ( document.getElementById('lblREPRESENTANTE').innerHTML.indexOf('ERRO')>-1 )   erro=1;
        break;
   
   
			case 'txtNASC_RESPONSAVEL':
			case 'txtNASC_RH':
      case 'txtVIGENCIA':
        if ( ! verifica_data(cCMP) )   erro=1;
        break;
        
        
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  /* concatena */
  data = document.getElementById('txtVIGENCIA').value;
  dataVIGENCIA='null'; if (data.trim()!='')  dataVIGENCIA = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  data = document.getElementById('txtNASC_RESPONSAVEL').value;
  nascRESPONSAVEL='null'; if (data.trim()!='')  nascRESPONSAVEL = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  data = document.getElementById('txtNASC_RH').value;
  nascRH='null'; if (data.trim()!='')  nascRH = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);
  
  cmps= document.getElementById('txtNOME').value+'|'+
        document.getElementById('txtCNPJ').value+'|'+
        document.getElementById('txtINSCRICAO').value+'|'+
        document.getElementById('txtEND').value+'|'+
        document.getElementById('txtBAIRRO').value+'|'+
        document.getElementById('txtCEP').value+'|'+
        document.getElementById('txtMUNICIPIO').value+'|'+
        document.getElementById('txtUF').value+'|'+
        document.getElementById('txtEND2').value+'|'+
        document.getElementById('txtBAIRRO2').value+'|'+
        document.getElementById('txtCEP2').value+'|'+
        document.getElementById('txtMUNICIPIO2').value+'|'+
        document.getElementById('txtUF2').value+'|'+
        document.getElementById('txtFONE').value+'|'+
        document.getElementById('txtFAX').value+'|'+
        document.getElementById('txtEMAIL').value+'|'+
        document.getElementById('txtRESPONSAVEL').value+'|'+
        nascRESPONSAVEL+'|'+
        document.getElementById('txtRH').value+'|'+
        nascRH+'|'+
        dataVIGENCIA+'|'+
        document.getElementById('txtVENCTO').value+'|'+
        document.getElementById('txtFUNCIONARIOS').value+'|'+
        document.getElementById('txtOPTANTES').value+'|'+
        document.getElementById('txtCARENCIA').value+'|'+
        document.getElementById('txtABATIMENTO').value+'|'+
        document.getElementById('txtTABELA').value+'|'+
        document.getElementById('txtCARTA').value+'|'+
        document.getElementById('txtRISCO').value+'|'+
        document.getElementById('txtREPRESENTANTE').value+'|'+
        document.getElementById('txtRAMO').value+'|'+
        escape( Encoder.htmlEncode(document.getElementById('txtOBSERVACOES').value) ) + '|'+
        document.getElementById('numREG').value+'|'+
        document.getElementById('txtNUMCLINIPAM').value;
        
  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	ajax.criar('ajax/ajaxESTIPULANTES.php?acao=gravar&vlr=' + cmps, gravou);
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
  	window.top.document.getElementById('infoTrab').value = 'frmESTIPULANTES:GRAVOU=' + cID
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

if (!confirm('Excluir este estipulante?')) return;

ajax.criar('ajax/ajaxESTIPULANTES.php?acao=excluirREG&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmESTIPULANTES:GRAVOU=' + id
lerREGS();  
}

/*******************************************************************************/
function abreUPLOAD_XLS()     {
var empresa='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    empresa = tab.rows[t].cells[1].innerHTML;
    break;
  }  
}

document.getElementById('divUPLOAD').setAttribute('class', "cssUPLOAD");
document.getElementById('fraUPLOAD').src = "planilhaPJ.php?idempresa="+document.getElementById('SELECAO').value+
        "&nomeempresa="+empresa;
}  

/*******************************************************************************/
function fechaUPLOAD()   {
document.getElementById('fraUPLOAD').src="";
document.getElementById('divUPLOAD').setAttribute('class', "cssDIV_ESCONDE");
}

/*******************************************************************************/
function fechaPLANOS() {
var divPLANOS = document.getElementById('divPLANOS'); divPLANOS.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
var tabPLANOS = document.getElementById('tabPLANOS'); tabPLANOS.innerHTML = '';
}

/*******************************************************************************/
function planos() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

document.getElementById('SELECAO_2').value='';
document.getElementById('SELECAO_3').value='';

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=planos&vlr='+document.getElementById('SELECAO').value, '' ,0);
showAJAX(0);


var empresa='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    empresa = tab.rows[t].cells[1].innerHTML;
    break;
  }  
}

var divPLANOS = document.getElementById('divPLANOS'); divPLANOS.setAttribute(propCLASSE, 'cssDIV_PLANOS');    
var tabPLANOS = document.getElementById('tabPLANOS'); tabPLANOS.innerHTML = ajax.ler().split('|')[0];


document.getElementById('infoPLANOS').innerHTML = "&nbsp;PLANOS&nbsp; - Empresa:&nbsp;"+empresa;

showAJAX(0);
}

/*******************************************************************************/
function novoPLANO() {
var num=prompt('Qual o número do novo plano?','');

if (num==null) return;
if (num.rtrim()=='') return;
if (isNaN(num)) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=verDuplicidadePlano&vlr='+num+'&op=inc', '', 0);
showAJAX(0);

if (ajax.ler().indexOf('1')>-1) {
  alert('Número de plano já utilizado'); return
}

var nome=prompt('Qual o nome do novo plano?','');

if (nome==null) return;
if (nome.rtrim()=='') return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=gravarNovoPlano&num='+num+'&nome='+nome, '', 0);
showAJAX(0);

planos();
}

/*******************************************************************************/
function editarPLANO() {
id = document.getElementById('SELECAO_2').value 
if (id=='') { alert('Selecione um registro');return;}

var numreg=document.getElementById('SELECAO_2').value.replace('pla_', '');

var num='';
var tab = document.getElementById('tabPLANOS2');
for (var t=1; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO_2').value) {
    num = tab.rows[t].cells[0].innerHTML.replace('&nbsp;', '');
    nome= tab.rows[t].cells[1].innerHTML.replace('&nbsp;', '');
    break;
  }  
}

var num=prompt('Mudar este plano para qual número?   \n\n(Se for mudar somente o nome, mantenha o mesmo número)', num);

if (num==null) return;
if (num.rtrim()=='') return;
if (isNaN(num)) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=verDuplicidadePlano&vlr='+num+'&op=alt&numreg='+numreg, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('1')>-1) {
  alert('Número de plano já utilizado'); return
}

var nome=prompt('Mudar o nome deste plano para... ?',nome);

if (nome==null) return;
if (nome.rtrim()=='') return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=mudarNomePlano&nome='+nome+'&num='+num+'&numreg='+numreg, '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function excluirPLANO() {
id = document.getElementById('SELECAO_2').value 
if (id=='') { alert('Selecione um registro');return;}

var numreg=document.getElementById('SELECAO_2').value.replace('pla_', '');

var num='';
var tab = document.getElementById('tabPLANOS2');
for (var t=1; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO_2').value) {
    num = tab.rows[t].cells[0].innerHTML.replace('&nbsp;', '');
    nome= tab.rows[t].cells[1].innerHTML.replace('&nbsp;', '');
    break;
  }  
}

if (! confirm('Confirma exclusão do plano '+ num+' - '+nome) ) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=excluirPLANO&vlr='+numreg, '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function novaFAIXA() {

var num=prompt('Qual a faixa etária inicial?','');

if (num==null) return;
if (num.rtrim()=='') return;
if (isNaN(num)) return;

var num2=prompt('Qual a faixa etária final?','');

if (num2==null) return;
if (num2.rtrim()=='') return;
if (isNaN(num2)) return;

var numreg=document.getElementById('SELECAO_2').value.replace('pla_', '');

var empresa='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    empresa = tab.rows[t].cells[0].innerHTML.toLowerCase().replace('&nbsp;&nbsp;', '');
    break;
  }  
}

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=novaFAIXA&num='+num+'&num2='+num2+'&empresa='+empresa, '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function selecionaVLR(idCOL) {

anterior=document.getElementById('SELECAO_3').value;
if (anterior!='') {
  document.getElementById(anterior).style.color='black';
  document.getElementById(anterior).style.fontWeight='normal';
  document.getElementById(anterior).style.fontSize='9px';
}
document.getElementById(idCOL).style.color='red';
document.getElementById(idCOL).style.fontSize='17px';
document.getElementById(idCOL).style.fontWeight='bold';

document.getElementById('SELECAO_3').value=idCOL;

}


/*******************************************************************************/
function editarFAIXA() {
id = document.getElementById('SELECAO_3').value 
if (id=='') { alert('Selecione uma faixa');return;}

info=id.split('_');   /* 1o num plano, 2o num faixa etaria */

if (info[1]!='') {
  showAJAX(1);
  ajax.criar('ajax/ajaxESTIPULANTES.php?acao=lerFAIXA&vlr='+info[1], '', 0);
  showAJAX(0);
  
  faixas=ajax.ler().split('|');
  
  fxinicial=faixas[0];  fxfinal=faixas[1];
}
else {
  fxinicial='';  fxfinal='';
}

var fxinicial=prompt('Mudar faixa inicial para? ', fxinicial);

if (fxinicial==null) return;
if (fxinicial.rtrim()=='') return;
if (isNaN(fxinicial)) return;

var fxfinal=prompt('Mudar faixa inicial para? ', fxfinal);

if (fxfinal==null) return;
if (fxfinal.rtrim()=='') return;
if (isNaN(fxfinal)) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=mudarFAIXA&vlr='+info[1]+'&inicial='+fxinicial+'&final='+fxfinal+
      '&empresa='+document.getElementById('SELECAO').value, '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function excluirFAIXA() {
id = document.getElementById('SELECAO_3').value 
if (id=='') { alert('Selecione uma faixa');return;}

info=id.split('_');   /* 1o num plano, 2o num faixa etaria */

if (info[1]=='') {
  alert('Nenhuma faixa etária para excluir');
  return;
}

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=lerFAIXA&vlr='+info[1], '', 0);
showAJAX(0);

faixas=ajax.ler().split('|');

fxinicial=faixas[0];  fxfinal=faixas[1];


if (!confirm('Confirma exclusão da faixa '+fxinicial+'..'+fxfinal+' ?')) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=excluirFAIXA&vlr='+info[1], '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function editarPRECO() {
id = document.getElementById('SELECAO_3').value 
if (id=='') { alert('Selecione uma preço');return;}

idempresa=document.getElementById('SELECAO').value;
info=id.split('_');   /* 1o num plano, 2o num faixa etaria */

if (info[1]=='') {
  alert('Nenhuma faixa etária selecionada');
  return;
}


vlr=document.getElementById(id).innerHTML.replace('&nbsp;', '');
if (vlr=='-') vlr='';
var vlr=prompt('Editar preço: ', vlr);

vlr=vlr.replace(',','.');
if (vlr==null) return;
if (vlr.rtrim()=='') return;
if (isNaN(vlr)) return;

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=editarPRECO&faixa='+info[1]+'&plano='+info[0]+'&vlr='+vlr+'&empresa='+idempresa, '', 0);
showAJAX(0);

planos();
}


/*******************************************************************************/
function resumo() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione uma empresa');return;}

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=resumo&empresa='+id, '', 0);
showAJAX(0);

var resp=ajax.ler();

if (resp.indexOf('ok')==-1) 
  alert("Erro \n\n"+resp); 
else
  window.open(resp.replace('ok;', '') );
}

/*******************************************************************************/
function futuras() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

showAJAX(1);
ajax.criar('ajax/ajaxESTIPULANTES.php?acao=futuras&vlr='+document.getElementById('SELECAO').value, '' ,0);
showAJAX(0);

var empresa='';
var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++) {
  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    empresa = tab.rows[t].cells[1].innerHTML;
    break;
  }  
}

var divFUTURAS = document.getElementById('divFUTURAS'); divFUTURAS.setAttribute(propCLASSE, 'cssDIV_FUTURAS');    
var titFUTURAS = document.getElementById('titFUTURAS'); titFUTURAS.innerHTML = ajax.ler().split('|')[0];
var tabFUTURAS = document.getElementById('tabFUTURAS'); tabFUTURAS.innerHTML = ajax.ler().split('|')[1];

document.getElementById('infoFUTURAS').innerHTML = "&nbsp;&nbsp;&nbsp;FUTURAS&nbsp;&nbsp;&nbsp; - Empresa:"+empresa;

showAJAX(0);
}

/*******************************************************************************/
function fechaFUTURAS() {
var divFUTURAS = document.getElementById('divFUTURAS'); divFUTURAS.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
var tabFUTURAS = document.getElementById('tabFUTURAS'); tabFUTURAS.innerHTML = '';
}


 


 




//]]></script>
  </body>
</html>
