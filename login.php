<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<script type="text/javascript" src="js/funcoes.js"  xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<link href="css/padroes.css" type="text/css" rel="stylesheet" />

<script language="javascript">
var ajax = new execAjax();
</script>
</head>
                                                            
<title>
  LOGIN
</title>  

<body style="HEIGHT: 100%; width:100%;" onload="Muda_CSS();ColocaFocoCmpInicial();lerTITULO();">
<br />
<form id="frmLOGIN" action="">
<font size="+2" face="verdana" color="blue"><span id="idEMPRESA">&nbsp;</span></font> <br /><br /><br /><br />
<div align="center">

<table width="250">
  <tr>
  <td>
    <table cellspacing="0" cellpadding="2" border="1" width="100%" class="form_edicao" align="center" bgcolor="white">
      <tr>
      <td>
        <span class="lblTitJanela">&nbsp;LOGIN</span>
      </td>
      </tr>
      <tr>
      <td>
        <table border="0" width="100%" style="text-align: left;">
          <tr>
          <td width="30%">
            <span class="lblPADRAO">Nº Usuário:</span>
          </td>
          <td>
            <input type="text" id="txtNUMERO" tabindex="1" maxlength="3" size="5" onblur="VerCmp(this.id)" onkeypress="return sistema_formatar(event, this, '000');" />&nbsp;&nbsp;
          </td>
          </tr>
          <tr>
          <td colspan="2">
            <span id="lblNOME" class="lblPADRAO" style="float:left;width: 200px;height:30px">&nbsp;</span>
          </td>
          </tr>
          <tr>
          <td>
            <span class="lblPADRAO">Senha:</span>
          </td>
          <td>
            <input type="password" id="txtSENHA" tabindex="2" maxlength="6" size="10" onblur="VerCmp(this.id)" />
          </td>
          </tr>
        </table>
      </td>
      </tr>
      <tr>
      <td>
        <div align="center">
          <input type="button" id="btnENTRAR" tabindex="3" value="&nbsp;&nbsp;&nbsp;&nbsp;Entrar&nbsp;&nbsp;&nbsp;" class="btnSUBMIT" onclick="return VerCmp('todos');" />
        </div>
      </td>
      </tr>
    </table>
  </td>
  </tr>
</table>
</div>
<?php
if (Session_id('perdeuSession')=='sim') { ?>
  <p align="center">
    <b><font size="2" face="verdana" color="red">Você foi deslogado
    por inatividade</font></b>
  </p>
<?php } ?> 
<span id="lblTRAB" class="lblPROCESSANDO">&nbsp;</span>
</form>
<script language="javascript" type="text/javascript" xml:space="preserve">
  
//<![CDATA[
//******* VARIAVEIS PADRAO DE TODO FORM
var nQtdeCamposTextForm = 2;
var aCmps = new Array(2);

aCmps[0]="txtNUMERO;Identifique o usuário.";
aCmps[1]="txtSENHA;Senha incorreta";

//**** MEMORIZA A SENHA DA FUNCIONAL ENTRADA
var cSENHA; cSENHA="";


/**********************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

if (  tecla==13 && foco==2  )  {
  var btn=document.getElementById("btnENTRAR"); 
  btn.click();  //**** PRESSIONA O BOTAO 'ENTRAR'
}

eval("teclasNavegacao(e);");
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);



/**********************************/
function ColocaFocoCmpInicial()   {
document.getElementById('txtNUMERO').focus();
}




/************************************************************************************/
//*** VALIDA OS CAMPOS
/************************************************************************************/
function VerCmp(nomeCMP)      {

lblTRAB = document.getElementById('lblTRAB');
lblNOME = document.getElementById('lblNOME');


if (typeof nomeCMP != 'string')   {
  if ( ajax.terminouLER() ) {
    lblTRAB.innerHTML='';
    resp = ajax.ler().split(';');
   			
  	cSENHA = resp[1];
  	
    lblNOME.innerHTML=resp[0];
    
    if (resp[0].indexOf('INEXISTENTE')!=-1) {lblNOME.style.color='red';}
    else lblNOME.style.color='blue';
  }
  return;
}  
  
//***** FOI CLICADO BOTAO 'ENTRAR', VERIFICA SE TUDO OK PARA PROSSEGUIR
if ( nomeCMP.indexOf('todos') != -1 )             {
  var cmp, vlr, msg, erro
  erro=0;
  
  for (i=0; i<aCmps.length; i++)  {
    cmp = aCmps[i];
    var info =  cmp.split(";")
    cmp = info[0];
    msg = info[1];
    
    var vlr = document.getElementById( cmp ).value;
  
    switch (cmp)    {
      case 'txtNUMERO':
        if ( (vlr=='') )  erro=1;
        if ( (lblNOME.innerHTML=="") || (lblNOME.innerHTML=="INEXISTENTE") )  erro=1;
        
        break;
        LowerCase() != cSENHA.toLowerCase() )  erro=1;
        
      case 'txtSENHA':
        if ( vlr.to
        break;
    }
    if (erro==1)    { document.getElementById(cmp).focus();    alert(msg);    return false; break;  }
  }
  
  vlr=document.getElementById('txtNUMERO').value+';'+document.getElementById('txtSENHA').value;
  ajax.criar('ajax/ajax.php?acao=logarUsuario&vlr=' + vlr, logouUSUARIO);

} 
else  {
  document.getElementById(nomeCMP).style.backgroundColor="white";
  
  if ( (nomeCMP=='txtNUMERO')  )  {
    var vlr = document.getElementById("txtNUMERO").value.trim(); 
    
    if (vlr=='') {lblNOME.innerHTML=''; return false; }
    lblTRAB.innerHTML='Lendo... AGUARDE';  
  
    ajax.criar('ajax/ajax.php?acao=testarLOGIN&vlr=' + vlr, VerCmp);
  }  
}
}

function logouUSUARIO()      {
if (ajax.terminouLER()) {
  var resp=ajax.ler();
  if (resp.indexOf('ok')!=-1)    {
    /* memoriza no servidor se estamos com alta ou baixa resolucao */
    ajax.criar('ajax/ajax.php?acao=usarTipoIMAGEM&vlr='+screen.width+'&vlr2='+screen.height, '', 0);

    document.location.href= 'menuPrincipal.php';
  }  
  else alert('Erro ao logar \n\n ' + ajax.ler());
}  
}

function lerTITULO() { 
ajax.criar('ajax/ajax.php?acao=lerNomeEmpresa', '', 0);
document.getElementById('idEMPRESA').innerHTML = ajax.ler();
} 


//]]>
</script>
</body>
</html>