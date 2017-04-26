<?php 
ob_start();
require("../doctype.php"); 
session_start();

?>

<head>
<link href="../<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/funcoes.js" xml:space="preserve"></script>


<title>
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');showAJAX(1);showAJAX(0);document.getElementById('txt1').focus();Muda_CSS(); ">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -90px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" >
  <table height="50px" bgcolor="#a9b2ca" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="../images/database.png" alt="" /></td></tr>
  </table>
</div>


<form id="frmREL" name="frmREL" action="">

<input type="text" id="xunxo" style="display:none" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:30%;width:60%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Contratos sem operadora/tipo de contrato definido</td>
          <td style="width:30%" id="btnGERAR" align="center" onmouseout="this.style.backgroundColor='#fffff0'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="gerar();" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gerar ]</span>
          </td>
          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='../inicial.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>
        
      <tr valign="top"  ><td style="height:95%;width:100%" ><table width="100%">

    		<tr><td>&nbsp;</td></tr>  					
    		<tr><td>&nbsp;</td></tr>
    		<tr><td><input type="text" readonly style="border-width:0px" value="Clique em gerar..." id="txt1"/></td></tr>
    		<tr><td>&nbsp;</td></tr>
    		<tr><td>&nbsp;</td></tr>                            		
        
      </td></tr>
        

      </td></tr>					
    
   
    </table>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */

var ajax = new execAjax();

  


/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

if  (tecla==113) document.getElementById('btnGERAR').click();

if  (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='../relatorios.php';}

eval("rel_teclasNavegacao(e);");

}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);




/*******************************************************************************/
function gerar()      {

/* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
showAJAX(1);

ajax.criar('ajaxRELS.php?acao=contratos_erros', '', 0);

showAJAX(0);

if (ajax.ler().indexOf('nada')!=-1) 
  alert('Nenhum registro encontrado');
else
  window.open('../pdf/rel.php', 'nome', 'width=10,height=10' );	
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


//]]>
</script>
</body>
</html>
