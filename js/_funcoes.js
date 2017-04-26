// JScript File


/* cria 3 metodos para string */
 
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}

/* adiciona o evento .click nos controles HTML - o firefox nao o possui nativamente */ 
HTMLElement.prototype.click = function() {
var evt = this.ownerDocument.createEvent('MouseEvents');
evt.initMouseEvent('click', true, true, this.ownerDocument.defaultView, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
this.dispatchEvent(evt);
}


/* proprieda que muda conforme o browser */
browser=navigator.appName; 
if (browser.indexOf("Microsoft")!=-1)
  propCLASSE="className";
else  
  propCLASSE="class";







//**********************************************************************************************
function AbrirJanelaMaximizado( aURL, aWinName )
{
   var wOpen;
   var sOptions;

   sOptions = 'status=no,menubar=no,scrollbars=no,resizable=no,toolbar=no';
   sOptions = sOptions + ',width=' + (screen.availWidth - 10).toString();
   sOptions = sOptions + ',height=' + (screen.availHeight - 122).toString();
   sOptions = sOptions + ',screenX=0,screenY=0,left=0,top=0';

   wOpen = window.open( '', aWinName, sOptions );
   wOpen.location = aURL;
   wOpen.focus();
   //wOpen.moveTo( 0, 0 );
   wOpen.resizeTo( screen.availWidth, screen.availHeight );
   return wOpen;
}

//**********************************************************************************************
function AbrirJanelaCentro(aURL, Largura, Altura)
{
/*
AbrirJanelaMaximizado( aURL, 'tst' );
return;
*/

if (document.all || document.layers) {
   var w = screen.availWidth;
   var h = screen.availHeight;
}

var params = "toolbar=0,";
params += "location=no,";
params += "directories=0,";
params += "status=0,";
params += "menubar=0,";
params += "titlebar=no,";
params += "scrollbars=0,";
params += "resizable=0;channelmode = yes";

var popW = Largura, popH = Altura;

var leftPos = (w-popW)/2; topPos = (h-popH)/2;

/*window.open(aURL, 'popup','width=' + popW + ',height=' + popH + ',top=' + topPos + ',left=' + leftPos + 'status=no,menubar=no,scrollbars=yes,resizable=no,toolbar=no;titlebar=0'); */
window.open(aURL, 'popup','width=' + popW + ',height=' + popH + ',top=' + topPos + ',left=' + leftPos + params);
}

//**********************************************************************************************
function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
	if (query != '') {
  	var vars = query.split("&");
  	for (var i=0;i<vars.length;i++) {
    	var pair = vars[i].split("=");
    	if (pair[0] == variable) {
      	return pair[1];
    	}
  	}
	}   else  {
		return('');
	}
}

//**********************************************************************************************

function trimString (str) {
  str = this != window? this : str;
  return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

//**********************************************************************************************
function findPosY(obj)
{
  var curtop = 0;
  if(obj.offsetParent)
      while(1)
      {
        curtop += obj.offsetTop;
        if( ! obj.offsetParent)
          break;
        obj = obj.offsetParent;
      }
  else if(obj.y)
      curtop += obj.y;
  return curtop;
}

//**********************************************************************************************
function findPosX(obj)
{
  var curleft = 0;
  if(obj.offsetParent)
      while(1)
      {
        curleft += obj.offsetLeft;
        if( ! obj.offsetParent)
          break;
        obj = obj.offsetParent;
      }
  else if(obj.y)
      curleft += obj.x;
  return curleft;
}


/************************************************************************************/
function MouseSobre(obj)  {
var id=document.getElementById(obj).id;

/* se estamos trabalhando em janela pop up - usado por exemplo qdo vamos incluir um novo reg
de medico, abrimos pop up so com janela medicos ---- esquecemos esquema de cores e usamos 
qq cor para destacar registro */
lPopUp=false;
try {
  a = window.top.document.getElementById('corMouseOverAuxilio').value;
} catch(e) {
  lPopUp=true;
}  
  
if (id.indexOf('at_')!=-1 || (id.indexOf('aux_')!=-1) )    {
  document.getElementById(id).style.backgroundColor =    
    lPopUp ? 'white' : window.top.document.getElementById('corMouseOverAuxilio').value;
}
    
else    {
  document.getElementById(id).style.backgroundColor =    
    lPopUp ? 'grey' : window.top.document.getElementById('corMouseOver').value;

}
 
 
}


/************************************************************************************/
function MouseFora(obj)  {
var id=document.getElementById(obj).id;

/* se estamos trabalhando em janela pop up - usado por exemplo qdo vamos incluir um novo reg
de medico, abrimos pop up so com janela medicos ---- esquecemos esquema de cores e usamos 
qq cor para destacar registro */
lPopUp=false;
try {
  a = window.top.document.getElementById('corMouseOverAuxilio').value;
} catch(e) {
  lPopUp=true;
}  

/* tela de auxilio */
if  (id.indexOf('aux_')!=-1)     {
  LinSelecionada = document.forms[0].SELECAO_2.value;

  if (LinSelecionada != id)
    document.getElementById(id).style.backgroundColor = 
        lPopUp ? 'grey' : window.top.document.getElementById('corFormAuxilio').value;    	
  else
    document.getElementById(id).style.backgroundColor =
        lPopUp ? 'yellow' : window.top.document.getElementById('corMouseDownAuxilio').value; 
}

else if  ( id.indexOf('pla_')!=-1 )     {
  LinSelecionada = document.forms[0].SELECAO_2.value;

  if (LinSelecionada != id)
    document.getElementById(id).style.backgroundColor = '#E6E8EE';  
  else
    document.getElementById(id).style.backgroundColor = '#9BA8D1';
    //document.getElementById(id).style.backgroundColor = window.top.document.getElementById('corMouseDown').value; 
}

else if  ( id.indexOf('cx')!=-1 )     {
  LinSelecionada = document.forms[0].SELECAO.value;
  corANTERIOR = document.getElementById(id).title=='.' ? '#EDEDED' : 'white';
  
  if (LinSelecionada != id)
    document.getElementById(id).style.backgroundColor = corANTERIOR;  
  else
    document.getElementById(id).style.backgroundColor = '#C0FFC0';
}

/* demais telas */
else {
  LinSelecionada = document.forms[0].SELECAO.value;

  if (LinSelecionada != id)
    document.getElementById(id).style.backgroundColor = 'white'; 
      	
  else
    document.getElementById(id).style.backgroundColor = 
      lPopUp ? 'yellow' :  window.top.document.getElementById('corMouseDown').value; 
}
   
/* coloca o tempo todo foco no cmp de pesquisa rapida */
var PR2 = document.getElementById('txtPR2');
if (null != PR2) 
  PR2.focus();  

else  {
  lFOCAR=1;
  div_=document.getElementById('divEDICAO');
  
  if (div_!=null) { if (div_.getAttribute(propCLASSE) != "cssDIV_ESCONDE") lFOCAR=0;}
  
  div_=document.getElementById('divAUXILIO');
  if (div_!=null) {if (div_.getAttribute(propCLASSE) != "cssDIV_ESCONDE") lFOCAR=0;}
  
  
  if (lFOCAR==1) {
  	var PR = document.getElementById('txtPR');
  	if (null != PR) {PR.focus(); }  
}	
}

}


/************************************************************************************/
function Selecionar(ID_obj, lRolarAteLinha)     {

if (typeof lRolarAteLinha == 'undefined' ) {lRolarAteLinha = 0;}
if (typeof nUsandoSelecao1ou2 == 'undefined' ) {nUsandoSelecao1ou2 = 1;}

/* se estamos trabalhando em janela pop up - usado por exemplo qdo vamos incluir um novo reg
de medico, abrimos pop up so com janela medicos ---- esquecemos esquema de cores e usamos 
qq cor para destacar registro */
lPopUp=false;
try {
  a = window.top.document.getElementById('corMouseOverAuxilio').value;
} catch(e) {
  lPopUp=true;
}  


if (ID_obj.indexOf('aux_')!=-1)      {     
  	selecao = 'SELECAO_2';
  	corDESTAQUE = lPopUp ? 'white' : window.top.document.getElementById('corMouseDownAuxilio').value;
  	corTABELA = lPopUp ? 'grey' : window.top.document.getElementById('corFormAuxilio').value; 
}

else if ( ID_obj.indexOf('pla_')!=-1  )      {     
  	selecao = 'SELECAO_2';
  	corDESTAQUE = lPopUp ? 'yellow' : '#9BA8D1'; 
  	corTABELA = '#E6E8EE'; 
}


else if ( ID_obj.indexOf('pp_')!=-1  )      {    
  	selecao = 'SELECAO';
  	corDESTAQUE = lPopUp ? 'yellow' : '#a9b2ca';
  	corTABELA = '#D4E2F2'; 
}

else if (ID_obj.indexOf('at_')!=-1)      {     
  	selecao = 'SELECAO_2';
  	corDESTAQUE = lPopUp ? 'yellow' : window.top.document.getElementById('corMouseDownAuxilio').value;
  	corTABELA = lPopUp ? 'white' :  'white'; 
}
else if (ID_obj.indexOf('cx')!=-1)      {     
  	selecao = 'SELECAO';
  	corDESTAQUE = '#C0FFC0';
  	
    if (document.getElementById('SELECAO').value!='') {
      linAnt=document.getElementById('SELECAO').value;
      //corTABELA = document.getElementById(linAnt).title=='.' ? '#EDEDED' : 'white';
      corTABELA = document.getElementById(linAnt).title=='.' ? '#EDEDED' : 'white';
    }
}
else     {
	selecao = 'SELECAO';
	corDESTAQUE = lPopUp ? 'grey' : window.top.document.getElementById('corMouseDown').value;
	corTABELA = 'white'; 
}

	
LinSelecionada = document.getElementById(selecao).value;

// tira destaque lin atual
if  (LinSelecionada!='')
	 document.getElementById(LinSelecionada).style.backgroundColor = corTABELA; 
  

// assegura lin indicada para por em destaque existe na tabela
var LinExiste = document.getElementById(ID_obj);

if (LinExiste==null) return;

/* memoriza qual lin foi selecionada */
document.getElementById(selecao).value = ID_obj;


// poe destaque lin selecionada
document.getElementById(ID_obj).style.backgroundColor = corDESTAQUE;

// rola at� lin se solicitado

if (lRolarAteLinha==1) {
	// 235= altura da div
	// 20= tamanho m�dio de cada linha
	if ( ID_obj.indexOf('aux_')!=-1 )   {
		var divTABELA = document.getElementById('divLISTA_AUXILIO');			
		divTABELA.scrollTop = findPosY(document.getElementById(ID_obj)) - (divTABELA.clientHeight) - findPosY(divTABELA) + 20;
  }
  else {
		var divTABELA = document.getElementById('divTABELA');
		divTABELA.scrollTop = findPosY(document.getElementById(ID_obj)) - (divTABELA.clientHeight) - findPosY(divTABELA) + 20;
	}
}
//window.top.document.getElementById('lblUSUARIO').innerHTML= document.getElementById('SELECAO').value;
//window.top.document.getElementById('lblUSUARIO').innerHTML= selecao+','+ID_obj;

/* qdo temos mais de uma pesquisa rapida por formulario, uma pesquisa rapida dentro de uma DIV, por exemplo,
recorremos a um segundo campo txtPR2  */
var PR2 = document.getElementById('txtPR2');
if (PR2 != null) 
  {PR2.focus();    PR2.focus();}
else {
  var PR = document.getElementById('txtPR');
  if (PR != null)  {PR.focus(); PR.focus();}
}  


}


/************************************************************************************/
function VerificaAcaoInicial()     {

var AcaoInicial = window.top.document.getElementById('infoTrab').value;

// verifica se indicado algo para o form fazer => exemplo: selecionar alguma linha da tabela
if ( AcaoInicial.indexOf(document.forms[0].name) != -1 )  {
	var UltRegGravado = AcaoInicial.substring(AcaoInicial.indexOf('GRAVOU=')+7);

	if (UltRegGravado != '') 	 	{Selecionar(UltRegGravado, 1);}	

	window.top.document.getElementById('infoTrab').value = '';
}

var lTemPR = document.getElementById('txtPR');
if (null != lTemPR)   {
  if (lTemPR.disabled==false)  document.forms[0].txtPR.focus();   }


if (typeof(cCmpInicialFoco) != 'undefined')        document.getElementById(cCmpInicialFoco).focus();
}

/********************************************************************************
 adiciona eventos e propriedades (css) a cada textbox
********************************************************************************/
function Muda_CSS()     {

if (! document.getElementsByTagName)    return; 
var allfields = document.getElementsByTagName("input");

var nCMP = 0;

/*  loop atraves das tags input text, input password, e adiciona estilos e eventos */

for (var i=0; i<allfields.length; i++)   {

  var field = allfields[i];
  if (   ((field.getAttribute("type") == "text") || (field.getAttribute("type") == "password")) && (field.tag!=-1)  
    && (! field.readonly) )  {
  
//    if (field.id.indexOf('txtFOCADO')!=-1) {
//      field.setAttribute('className', "CorTxtBoxSeguraFoco");
//      field.setAttribute('class', "CorTxtBoxSeguraFoco");
//    }
//    else { 
      field.setAttribute('className', "CorTxtBoxInativo");
      field.setAttribute('class', "CorTxtBoxInativo");
//    }  

    // se vari�vel "aOrdemCmps" existe, caracteriza formul�rio de edi��o dados
    if (typeof(aOrdemCmps) != 'undefined')          
			field.onfocus = function () { FOCO(this.id); }

    aOrdemCmps[ field.getAttribute('tabIndex') ] = field.id; 
          
		
		/**************************************************************************************
		jogamos no evento onblur o padrao de todo sistema:  VerCmp(this.id)
		ou seja, todo formulario que tiver input textbox, input password obrigatoriamente deve conter
		uma funcao javascript VerCmp( ID DO CAMPO )
		***************************************************************************************/
		field.onblur = new Function(" VerCmp(this.id)");

		
		/************************************************
		proximo campo
		************************************************/
    nCMP++;
  }

  if (field.getAttribute("type") == "button")    {
    field.setAttribute('className', "btnSUBMIT");
    field.setAttribute('class', "btnSUBMIT");
  }  
}


}



/**************************************************************************************
 classe AJAX 
**************************************************************************************/ 
var execAjax = function()      {


var ajaxRequest;
var urlCHAMAR;


/*********************************************************************************/
this.criar = function(urlChamar, funcaoExecutarQdoPronto, assincrono)    {

if (typeof assincrono == 'undefined') {assincrono = 1;}

this.ajaxRequest = null;
this.urlCHAMAR = null;
 

ok=0;
var aVersions = [ "MSXML2.XMLHttp.5.0",
    "MSXML2.XMLHttp.4.0","MSXML2.XMLHttp.3.0",
    "MSXML2.XMLHttp","Microsoft.XMLHttp"
];
          
for (var i = 0; i < aVersions.length; i++) {
  try {
      this.ajaxRequest = new ActiveXObject(aVersions[i]);
      ok=1;
      break;
  } catch (oError) { /* faz nada */ }
}
      
if (ok==0) {
  try{
    /* Opera 8.0+, Firefox, Safari */
    this.ajaxRequest = new XMLHttpRequest();
  }
  catch (e) {
    alert("Your browser broke!");
    return false;
  }
} 

/* executa o programa asp indicado em urlChamar */
if (assincrono==0)  {
  this.ajaxRequest.open("GET", urlChamar, false );
  
}  
else     { 
  this.ajaxRequest.open("GET", urlChamar, true );
  this.ajaxRequest.onreadystatechange = funcaoExecutarQdoPronto;
}

this.ajaxRequest.send(null);



return;
}


/*********************************************************************************/
this.terminouLER = function()     {
if (this.ajaxRequest.readyState==4) return true;
else return false;
}  

/*********************************************************************************/
this.ler = function()      {
		return this.ajaxRequest.responseText;
}

}


/*

 funcoes para centralizacao de DIVs  
 
*/

function gebi(id){
var i, d = document;
for(i in {getElementById: 0, all: 0, layers: 0})
if(i in d) break;
return d[i] ? d[i][id] || d[i](id) : null;
}

function centerDiv(id){
var o = gebi(id) || {}, w = window, b = document.body;

/* esse +30 no style.top foi necessario por causa da barra rapida e tamb�m porque n�o tenho mais
tempo para achar a fun��o perfeita de centraliza��o da DIV */
o.style.left = (o.pageX = (w.innerWidth || b.clientWidth || 0) -  (o.offsetWidth || o.width || 0)>>1) + "px";
if (id=='divGED')
	o.style.top = (o.pageY = (w.innerHeight || b.clientHeight || 0) - (o.offsetHeight || o.height || 0)>>1) -5 + "px";
else
	o.style.top = (o.pageY = (w.innerHeight || b.clientHeight || 0) - (o.offsetHeight || o.height || 0)>>1) + 30 + "px";	
}


/* captura span lblACAO do formulario principal e joga a mensagem (parametro) para ele */
function Avisa(msg) {
var lblACAO = window.top.document.getElementById('lblACAO');
if (lblACAO!=null) window.top.document.getElementById('lblACAO').innerHTML=msg;
}


/* cria uma matriz multidimensional */
function CriaArrayMultiDimensional(iRows,iCols)
{
var i;
var j;
   var a = new Array(iRows);
   for (i=0; i < iRows; i++)
   {
       a[i] = new Array(iCols);
       for (j=0; j < iCols; j++)
       {
           a[i][j] = "";
       }
   }
   return(a);
} 


/*********************************************************************************/

/* preenche string com espa�os � esquerda */
function padL(string, qtde) {
var str = string;
while (str.length < qtde) str = ' ' + str;
return(str);
}


/*********************************************************************************/
function verifica_data(cmp) {

/* se come�ar com ITSELF_, avalia o proprio vlr passado em 'cmp'  */
if (cmp.substring(0,7)=='ITSELF_')
  vlr = cmp.substring( cmp.indexOf('ITSELF_')+7, 100 );
else  
  vlr=document.getElementById(cmp).value;

if (vlr=='') return true; 

dia = parseInt(vlr.substring(0,2), 10); 
mes = parseInt(vlr.substring(3,5), 10);  
ano = vlr.substring(6,10); 

situacao = "";
if ( isNaN(dia) || isNaN(mes)  || isNaN(ano) ) return false;

 
// verifica o dia valido para cada mes 
if ((dia < 1)||(dia < 1 || dia > 30) && (  mes == 4 || mes == 6 || mes == 9 || mes == 11 ) || dia > 31) { 
    situacao = "falsa"; 
} 

// verifica se o mes e valido 
if (mes < 1 || mes > 12 ) { 
    situacao = "falsa"; 
} 

// verifica se e ano bissexto 
if (mes == 2 && ( dia < 1 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) { 
    situacao = "falsa"; 
} 

if (situacao == "falsa") 
  return false; 
else
  return true;
}


/*******************************************************************************/
function AuxilioF7(oQueAuxiliar)  {	      

if ( oQueAuxiliar!='txtREPRESENTANTE' && oQueAuxiliar.indexOf('txtGRUPO')==-1 && 
  oQueAuxiliar!='txtBANCO' && oQueAuxiliar.indexOf('txtPLANO')==-1 && oQueAuxiliar.indexOf('txtOPERADORA')==-1
  && oQueAuxiliar!='txtREL_REPRESENTANTE' && oQueAuxiliar!='txtREL_SUPERVISOR' 
  && oQueAuxiliar!='txtREL_REPRESENTANTE2'  && oQueAuxiliar!='txtTIPO_CONTRATO'  && oQueAuxiliar!='txtCOMISSAO_REPRESENTANTE' 
  && oQueAuxiliar!='txtCOMISSAO_PRESTADORA' && oQueAuxiliar!='txtCONTA' && oQueAuxiliar!='txtFUNCIONARIO' 
 && oQueAuxiliar!='txtAGRUPADOR' && oQueAuxiliar!='txtREL_GRUPO' && oQueAuxiliar!='txtCOMISSAO_ADESAO'  
  && oQueAuxiliar!='txtINDICACAO' && oQueAuxiliar!='txtATENDIMENTO_PRODUTO' && oQueAuxiliar!='txtREPRESENTANTE_OCORRENCIA'
    && oQueAuxiliar!='txtSITUACAO' && oQueAuxiliar.indexOf('txtOPERADOR')==-1  
    && oQueAuxiliar!='txtTIPOSEGURO' && oQueAuxiliar.indexOf('txtSEGURADORA')==-1 && oQueAuxiliar.indexOf('txtCORRETOR')==-1)
    return true;
     
aux = 'ajax/ajaxAUXILIO.php?oQueAuxiliar=' + oQueAuxiliar;

retornoAUXILIO='';      /* prepara para receber um retorno */

showAJAX(1);
 ajax.criar(aux, '', 0);

if (ajax.ler().indexOf('was not found')!=-1 || ajax.ler().indexOf('o encontrada')!=-1 ) {
  aux = 'ajax/ajaxAUXILIO.php?oQueAuxiliar=' + oQueAuxiliar;
  ajax.criar(aux, '', 0);
}
if (ajax.ler().indexOf('was not found')!=-1 || ajax.ler().indexOf('o encontrada')!=-1 ) {
  aux = '../ajax/ajaxAUXILIO.php?oQueAuxiliar=' + oQueAuxiliar;
  ajax.criar(aux, '', 0);
}



showAJAX(0); Avisa('');

var divAUXILIO = document.getElementById('divAUXILIO');
divAUXILIO.innerHTML = ajax.ler();
 
divAUXILIO.setAttribute("className", "cssDIV_AUXILIO");
divAUXILIO.setAttribute("class", "cssDIV_AUXILIO");
divAUXILIO.style.zIndex=100;
Muda_CSS(); 

ColocaFocoCmpInicial();
}


/*******************************************************************************/
function fecharAUXILIO(infoESCOLHIDA)     {
/* limpa PR para q caixa de pesq rapida seja fechada */
document.getElementById('txtPR2').value='';
document.getElementById('SELECAO_2').value='';

cmpTEXTO=document.getElementById('infoAUXILIO').value;
if (cmpTEXTO=='txtREPRESENTANTE' && ! document.getElementById('txtREPRESENTANTE'))
  cmpTEXTO='txtFUNCIONARIO';
/* coloca info escolhida no campo destino */
if (infoESCOLHIDA!=null)   {  
  var escolhido = infoESCOLHIDA.split("|");
  
  cmpLABEL = cmpTEXTO.replace('txt', 'lbl');

  
  
  
  /* "textbox" � usado qdo preenchendo um c�digo */
  if ( document.getElementById(cmpTEXTO).getAttribute("type") == "text" ) {
    document.getElementById(cmpTEXTO).value = escolhido[1];
    document.getElementById(cmpLABEL).innerHTML = escolhido[0];
    document.getElementById(cmpLABEL).style.color='blue';
    
  }
  
  /* nao usamos texbox quando o auxilio foi usado para preencher um span, ou algo assim
    no caso do filtro de exames por exemplo */
  else     {
    document.getElementById(cmpTEXTO).innerHTML = escolhido[0] + ' ('+escolhido[2]+')';
    
    document.getElementById(  cmpTEXTO.replace('lbl', 'hid')  ).value = escolhido[2];
    
    if ( document.getElementById( 'sujouFILTRO' ) ) 
      document.getElementById( 'sujouFILTRO' ).value = 'SIM';
      
    if ( cmpTEXTO=='lblTabUsada' )    atlVALORES();      
    
    
  }    
  
}
document.getElementById("divAUXILIO").innerHTML='';
document.getElementById("divAUXILIO").setAttribute("className", "cssDIV_ESCONDE");
document.getElementById("divAUXILIO").setAttribute("class", "cssDIV_ESCONDE");

ColocaFocoCmpInicial(cmpTEXTO); 	
}

/*******************************************************************************/
function usouAUXILIO(){
/* le dados da linha selecionada */	
var SELECAO=document.getElementById("SELECAO_2").value.rtrim().ltrim();

/* se selecionou alguma coisa */
if (SELECAO!='') {        
	/* retorna dados da info selecionada */
	var tabela=document.getElementById("tabAUXILIO");
	for(var lin=0;lin < tabela.rows.length; lin++) {
		if ( tabela.rows[lin].id == SELECAO)
			/* o padrao constru��o das tabelas auxilio � DESCRICAO DA INFO;CODIGO DA INFO */
			resp = tabela.rows[lin].cells[0].innerHTML + "|" + tabela.rows[lin].cells[1].innerHTML + '|' +
        tabela.rows[lin].id.replace('aux_', '');
	}
}

fecharAUXILIO(resp);
}


/************************************************************************/
function PR_AUXILIO(cmpBUSCAR)  {

var tabela = document.getElementById("tabAUXILIO");
var PR = document.getElementById('txtPR2').value.toUpperCase();    larg = PR.length;

if (larg < largPR) {largPR = larg; return; }

largPR = larg;

if (PR.rtrim().ltrim() != '')   {
	for(var lin=0;lin < tabela.rows.length; lin++) {
		if ( tabela.rows[lin].cells[cmpBUSCAR].innerHTML.toUpperCase().substring(0, larg) == PR ) 
      {Selecionar(tabela.rows[lin].id, 1, 2); break;}
	}
}

return;
}


/************************************************************************/
function novoREG(idCMP)    {

fecharAUXILIO();

if (idCMP=='txtCLIENTE') url='pacientes.php';
if (idCMP=='txtMEDICO') url='medicos.php';

sOptions = 'status=no,menubar=no,scrollbars=no,resizable=no,toolbar=no';
sOptions = sOptions + ',width=' + (screen.availWidth - 100).toString();
sOptions = sOptions + ',height=' + (screen.availHeight - 200).toString();
sOptions = sOptions + ',screenX=0,screenY=0,left=0,top=0';

wOpen = window.open( '', '', sOptions );
wOpen.location = url;
wOpen.focus();
wOpen.moveTo( 0, 0 );
//wOpen.resizeTo( screen.availWidth, screen.availHeight );
}

 function padright(val, ch, num){
            var re = new RegExp("^.{" + num + "}");
            var pad = "";
            if (!ch) ch = " ";
            do {
                pad += ch;
            } while (pad.length < num);
            //return re.exec(val + pad)[0];
            return val + pad;
        }


/*******************************************************************************/
function showAJAX(acao) {
dv= document.getElementById('divAJAX');
if (acao==1) {
	dv.setAttribute("className", "cssDIV_AJAX");
	dv.setAttribute("class", "cssDIV_AJAX");	
	
}
else   {
	dv.setAttribute("className", "cssDIV_ESCONDE");
	dv.setAttribute("class", "cssDIV_ESCONDE");	
}  	
}

/*******************************************************************************/
function isFloat(val) {
if(!val || (typeof val != "string" || val.constructor != String)) {
return(false);
}
var isNumber = !isNaN(new Number(val));
if(isNumber) {
if(val.indexOf('.') != -1) {
return(true);
} else {
return(false);
}
} else {
return(false);
}
}
