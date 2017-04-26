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
position: absolute; top: 200px;  width: 700px; height: 80px;	
margin-top: -255px; margin-left: -325px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20;
}

.cssDIV_AJAX_EMAIL {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 400px; height: 100px;	margin-top: -90px; margin-left: -180px; display:block; 
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

var email_linAtual, email_timer, timer1, timer2;

//]]>
</script>
<ul id="CM1" class="SimpleContextMenu_MAIOR">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Tornar ativo/inativo</a></li>  
  <li><a href="javascript:emailSENHA();">Enviar e-mail com senha para este corretor</a></li>
  <li><a href="javascript:senhas();">Gerar lista de senhas</a></li>
  <li><a href="javascript:contas();">Relatório de contas bancárias</a></li>
  <li><a href="javascript:cpfs();">Listar nome, RG, CPF</a></li>
</ul>

<div id="divAJAX_EMAIL" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="100px" bgcolor="white" rules="rows"  border="1" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/enviando.png" alt="" /></td></tr>
    <tr valign="middle"><td>
      <span id=lblENVIADO style="font-family:verdana;color:black;font-size:12px;"></span></td>
    <tr id=trESPERANDO valign="middle"><td>
      <span id=lblESPERANDO style="font-family:verdana;color:#1D9101;font-size:12px;">nada</span></td>
    <tr  valign="middle" height="50px"><td align=center 
      style="font-family:verdana;font-size:13px;color:red;width:50px;height:30px;" onclick="cancelar_EMAIL();"><input id=btnCANCELA_EMAIL type=button value="&nbsp;&nbsp;Cancelar&nbsp;&nbsp;" /></td>
    </tr>    
  </table>
</div>

<form id="frmREPRESENTANTES" name="frmREPRESENTANTES" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id=SELECAO_2 type=hidden value="" >
<input id="somenteATIVOS" type="hidden" value="S" />


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="70%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>
        
        <td title="Muda comissão sobre mensalidade de vários corretores ao mesmo tempo" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="mudarCOMIMENS();" >
          <img src="images/alternar.png" />
        </td>    

        <td title="Alternar entre ativo/inativo" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >
          <img src="images/trocar.png" />
        </td>    
            
        <td title="Períodos de pagamento para corretor" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" 
          onclick="window.top.frames['framePRINCIPAL'].location.href='periodosPGTO.php';" >
          <img src="images/calendario.png" />
        </td>

        <td title="Períodos para relatório de vendas" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" 
          onclick="window.top.frames['framePRINCIPAL'].location.href='periodosVENDA.php';" >
          <img src="images/calendario2.png" />
        </td>

        <td title="Enviar e-mail com a senha para corretores que possuem e-mail" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="mandar_email();" >
          <img src="images/email_enviar.png" />
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
        <td>
          &nbsp;<span class="lblPADRAO">Pesquisa:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:200px;" onkeyup="PR();" maxlength="30" />
                        <input id="txtFOCADO" type="text" value="" 
          style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />

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
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 21;

var aEND=new Array();

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtNOME;Digite o nome do corretor';
aCMPS[1]='txtFONE;';
aCMPS[2]='txtCPF;';
aCMPS[3]='txtGRUPO;Identifique o grupo de vendas ou deixe em branco';
aCMPS[4]='txtCOMISSAO_REPRESENTANTE;Identifique a comissão sobre mensalidades ou deixe em branco';
aCMPS[5]='txtCOMISSAO_ADESAO;Identifique a comissão sobre adesão ou deixe em branco';
aCMPS[6]='txtOPERADOR;Identifique um operador (indicações) válido ou deixe em branco';
aCMPS[7]='txtNASC;Preencha data nascimento válida ou deixe em branco';
aCMPS[8]='txtBANCO;Identifique banco válido ou deixe em branco';

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

if  ( tecla==119 && lJanRegistro )  AuxilioF7(cfoco);

if (lJanRegistro)  eval("teclasNavegacao(e);");

var lF2=tecla==113;  


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
else if (lJanRegistro )   {
	document.getElementById('txtOPERADOR').focus();
  nQtdeCamposTextForm = 21;
}  	
else 
  document.getElementById('txtFOCADO').focus();  	
}	
	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=lerREGS&ativos='+
  document.getElementById('somenteATIVOS').value, desenhaTabela);
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
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Corretores/Funcionários</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativos)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativos)';
  else  
    titulo.innerHTML += '(Todos)';
    
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  //if (this.resposta.indexOf("ondblclick")!=-1) f_sort( document.all.tabREGs, 1, true, 3, 'h2' );  
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=incluirREG', desenhaJanelaREG);
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
ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

/* concatena */
var data2 = document.getElementById('txtNASC').value;
var nascimento='null';
if (data2.rtrim().ltrim()!='') 
  nascimento = data2.substring(6, 10)+'-'+data2.substring(3, 5)+'-'+data2.substring(0, 2);

cmps= document.getElementById('txtNOME').value+'|'+document.getElementById('txtFONE').value+'|'+
      document.getElementById('txtCPF').value+'|' +
      document.getElementById('numREG').value+'|'+
      document.getElementById('txtGRUPO').value+'|'+
      document.getElementById('txtCOMISSAO_REPRESENTANTE').value+'|'+
      document.getElementById('txtCOMISSAO_ADESAO').value+'|'+
      document.getElementById('txtEND').value+'|'+
      document.getElementById('txtBAIRRO').value+'|'+
      document.getElementById('txtCEP').value+'|'+
      document.getElementById('txtMUNICIPIO').value+'|'+
      document.getElementById('txtUF').value+'|'+
      document.getElementById('txtCONTA').value+'|'+
      document.getElementById('txtRG').value+'|'+
      document.getElementById('txtEMAIL').value+'|'+
      document.getElementById('txtOPERADOR').value+'|'+
      nascimento+'|'+
      document.getElementById('txtBANCO').value+'|'+
      document.getElementById('txtAGENCIA').value+'|'+
      document.getElementById('txtOPERACAO').value+'|'+
      document.getElementById('txtNUM_CONTA').value+'|'+
      document.getElementById('txtFAVORECIDO').value;

      
if (nomeCMP!='todos')         {

	switch (nomeCMP) {
		case 'txtGRUPO':
		case 'txtCOMISSAO_REPRESENTANTE':		
		case 'txtCOMISSAO_ADESAO':
		case 'txtOPERADOR':
		case 'txtBANCO':
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
    	  
      /* se identificou o grupo de vendas, preencha a comissao vinculada ao grupo (caso o campo comissao ainda esteja em branco) */
      if (nomeCMP=='txtGRUPO') {
        cVLR=ajax.ler();
        cINFO = cVLR.split('^')[1];

        cVLR = cVLR.split('^')[0];         
        idCOMISSAO = cINFO.split('|')[1];
        nomeCOMISSAO = cINFO.split('|')[0];

        if (document.getElementById('lblCOMISSAO_REPRESENTANTE').innerHTML=='') {
          document.getElementById('txtCOMISSAO_REPRESENTANTE').value=idCOMISSAO;
          document.getElementById('lblCOMISSAO_REPRESENTANTE').innerHTML=nomeCOMISSAO;
        }
        
        cVLR=ajax.ler().split('^')[0].split(';')[1];
        cmpLBL.innerHTML = cVLR;  
      }
      else
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
        
			case 'txtGRUPO':        			 
			case 'txtOPERADOR':
        if ( document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1 ) 
            erro=1;
        break;
        
			case 'txtCOMISSAO_REPRESENTANTE':			
        if ( document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;

			case 'txtBANCO':			
        if ( document.getElementById(label).innerHTML.toLowerCase().indexOf('erro')!=-1) 
            erro=1;
        break;

			case 'txtNASC':
        if ( cVLR.trim()!='' && ! verifica_data('txtNASC') )   erro=1;
        break;
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  rdBUTTON = document.forms['frmREPRESENTANTES'].elements['tipo'];
  var tipo='';                    
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipo = rdBUTTON[i].value;
  }

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
  
	showAJAX(1);
	ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=gravar&vlr=' + cmps+'&tipo='+tipo, gravou);
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
  	window.top.document.getElementById('infoTrab').value = 'frmREPRESENTANTES:GRAVOU=' + cID
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

ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=mudarSITUACAO&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmREPRESENTANTES:GRAVOU=' + id
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

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmREPRESENTANTES'].elements['tipo'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtGRUPO').focus();
}


/*******************************************************************************/
function mandar_email() {
var tab=document.getElementById('tabREGs');

cont=0;
if (tab) {
  for (d=0; d<tab.rows.length; d++) {
    if (tab.rows[d].cells[6].innerHTML.rtrim()!='') cont++ 
  }
}
if (cont==0) {
  alert('Nenhum corretor com e-mail cadastrado');
  return;
}
  
if (! confirm('Confirma o envio de e-mail com a senha para '+cont+' corretor(es) ?')) return;

if (! confirm('Tem certeza?')) return;

var tabela = document.getElementById("tabREGs");

document.getElementById("lblESPERANDO").innerHTML = 'Aguarde...';
document.getElementById("lblENVIADO").innerHTML = 'Preparando...';
showAJAX_EMAIL(1);


aEND.length=0; 
// cria lista enderecos enviar
for (r=0; r<tabela.rows.length; r++) {
  end = tabela.rows[r].cells[6].innerHTML.rtrim();
  nome = tabela.rows[r].cells[1].innerHTML.rtrim();
  senha = tabela.rows[r].cells[7].innerHTML.rtrim();
  numero = tabela.rows[r].id;
  if (end!='') aEND.push( [nome, end, senha, numero] );     
}

email_linAtual=0;
enviaEMAILS()
}

/*******************************************************************************/
function enviaEMAILS() {

document.getElementById("lblESPERANDO").innerHTML = "<font color=green>Enviando...</font>";

info = aEND[email_linAtual];

end = info[1];
nome = info[0];
senha = info[2];
numero = info[3];

ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=email&end='+end+'&nome='+nome+'&senha='+senha+'&numero='+numero , '', 0);

/* se mandou para todos destinatarios */  
if (email_linAtual==(aEND.length-1)) {
  showAJAX_EMAIL(0);
  clearTimeout(timer1); clearTimeout(timer2);
  return;
}

document.getElementById("lblENVIADO").innerHTML = 'Enviado para <font color=blue><b>'+end+'</font></b>'+
'  ('+(email_linAtual+1)+'/'+(aEND.length)+')';

email_linAtual++;
email_timer=10;

document.getElementById("lblESPERANDO").innerHTML = 'Aguarde... '+email_timer;



clearTimeout(timer1); clearTimeout(timer2);

timer1=setTimeout("timer()",1000);
timer2=setTimeout("enviaEMAILS()",10000);

}

/*******************************************************************************/
function timer() {
if (document.getElementById('btnCANCELA_EMAIL').value.indexOf('Cancelando')!=-1) {
  showAJAX_EMAIL(0);
  clearTimeout(timer1); clearTimeout(timer2);
  
  document.getElementById('btnCANCELA_EMAIL').value="    Cancelar    ";  
  return;
}

email_timer--;
document.getElementById("lblESPERANDO").innerHTML = 'Aguarde... '+email_timer;

if (email_timer==0) email_timer=10;
timer1=setTimeout("timer()",1000);
}

/*******************************************************************************/
function showAJAX_EMAIL(acao) {
dv= document.getElementById('divAJAX_EMAIL');
if (acao==1) {
	dv.setAttribute("className", "cssDIV_AJAX_EMAIL");
	dv.setAttribute("class", "cssDIV_AJAX_EMAIL");	
	
}
else   {
	dv.setAttribute("className", "cssDIV_ESCONDE");
	dv.setAttribute("class", "cssDIV_ESCONDE");	
}  	
}

/************************************/
function cancelar_EMAIL()  {
document.getElementById('btnCANCELA_EMAIL').value="Cancelando...";
}

/*******************************************************************************/
function emailSENHA() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

var tab=document.getElementById('tabREGs');

var end = ''; nome = ''; senha = '';

if (tab) {
  for (d=0; d<tab.rows.length; d++) {
    if (tab.rows[d].id==id && tab.rows[d].cells[6].innerHTML.rtrim()!='') {
      end = tab.rows[d].cells[6].innerHTML.rtrim();
      nome = tab.rows[d].cells[1].innerHTML.rtrim();
      senha = tab.rows[d].cells[7].innerHTML.rtrim();
      numero = tab.rows[d].id;
      break; 
    } 
  }
}
if (end=='') {
  alert('Corretor sem e-mail cadastrado');
  return;
}
  
if (! confirm('Confirma o envio de e-mail com a senha para este corretor ?')) return;

document.getElementById("lblESPERANDO").innerHTML = 'Aguarde...';
document.getElementById("lblENVIADO").innerHTML = 'Preparando...';
showAJAX_EMAIL(1);


aEND.length=0; 
aEND.push( [nome, end, senha, numero] );     

email_linAtual=0;
enviaEMAILS();

alert('E-mail enviado.');
}

/*******************************************************************************/
function senhas() {
showAJAX(1);
ajax.criar('rel/ajaxRELS2.php?acao=senhas', '', 0);
showAJAX(0);


if (ajax.ler().indexOf('nada')!=-1) 
  alert('Nenhum registro encontrado');
else
  window.open('pdf/rel.php', 'nome', 'width=10,height=10' );	
}

/*******************************************************************************/
function mudarCOMIMENS() {

if (! confirm('Use esta ferramenta se precisa que TODOS os corretores que usam determinada comissão SOBRE MENSALIDADES, \n\n'+
              'mudem para outra comissão SOBRE MENSALIDADES.\n\n'+
               'O sistema vai pedir a comissão SUBSTITUTA (NOVA), e a comissão que deve ser SUBSTITUÍDA (ANTIGA).\n\n'+
               'Lembrando que, isso não interfere na comissão dos contratos já cadastrados dos corretores.\n\nContinua?\n\n') )
  return;

resp = prompt('Digite o código da comissão que deve ser SUBSTITUÍDA (ANTIGA):\n\n', '');

if (resp==null) return;
if (resp.rtrim()=='') return;
if (isNaN(resp.rtrim())) return;

idSUBSTITUIDA=resp;

showAJAX(1);
ajax.criar('ajax/ajaxTIPOS_COMISSAO_REPRESENTANTE.php?acao=lerCOMISSAO&vlr=' + idSUBSTITUIDA, '', 0);
showAJAX(0);

if (ajax.ler()=='ERRO') {
  alert('Comissão não encontrada');
  return;
}
nomeSUBSTITUIDA=ajax.ler();




resp = prompt('Digite o código da comissão SUBSTITUTA (NOVA):\n\n', '');

if (resp==null) return;
if (resp.rtrim()=='') return;
if (isNaN(resp.rtrim())) return;

idSUBSTITUTA=resp;

showAJAX(1);
ajax.criar('ajax/ajaxTIPOS_COMISSAO_REPRESENTANTE.php?acao=lerCOMISSAO&vlr=' + idSUBSTITUTA, '', 0);
showAJAX(0);

if (ajax.ler()=='ERRO') {
  alert('Comissão não encontrada');
  return;
}
nomeSUBSTITUTA=ajax.ler();



if (! confirm('Confirma que todos os corretores que estão configurados com a comissão:\n\n'+
              nomeSUBSTITUIDA+' ('+idSUBSTITUIDA+')\n\n'+
              'passem a estar configurados com a comissão\n\n'+
              nomeSUBSTITUTA+' ('+idSUBSTITUTA+')\n\n')) 
  return;

if (! confirm('** Última chance **\n\nConfirma que todos os corretores que estão configurados com a comissão:\n\n'+
              nomeSUBSTITUIDA+' ('+idSUBSTITUIDA+')\n\n'+
              'passem a estar configurados com a comissão\n\n'+
              nomeSUBSTITUTA+' ('+idSUBSTITUTA+')\n\n')) 
  return;



showAJAX(1);
ajax.criar('ajax/ajaxREPRESENTANTES.php?acao=trocarCOMISSAO&antiga=' + idSUBSTITUIDA+'&nova='+idSUBSTITUTA, '', 0);
showAJAX(0);


resp=ajax.ler();
if (resp.indexOf('corretor(res)')!=-1)   {
  alert(resp);
	lerREGS();
}
else
	alert('Erro ao gravar: \n\n ' + resp);	
}


/*******************************************************************************/
function contas() {

if (!confirm('Será gerado um relatório de contas bancárias, mas somente de corretores cuja informação foi preenchida.\n\nContinua?')) return;
showAJAX(1);
ajax.criar('rel/ajaxRELS2.php?acao=contas', '', 0);
showAJAX(0);

window.open('pdf/rel.php', 'nome', 'width=10,height=10' );	
}

/*******************************************************************************/
function cpfs() {

if (!confirm('Será gerado relatório de CPFs. \n\nContinua?')) return;
showAJAX(1);
ajax.criar('rel/ajaxRELS2.php?acao=repre_CPFs', '', 0);
showAJAX(0);

window.open('pdf/rel.php', 'nome', 'width=10,height=10' );	

}





//]]></script>
  </body>
</html>
