
var foco;
var cfoco = '';
var aOrdemCmps = Array();




/********************************************************************************
 função onkeydown - gerencia teclas pressionadas no form de edição de dados
********************************************************************************/
function teclasNavegacao(e)  {
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;


// nao permite ;(59),'(39),"(34)Š(179)  */
if ( (tecla==59) || (tecla==39) || (tecla==34) || (tecla==179)   )         void(0);


// seta para baixo ou Enter foi press
if ( (tecla==40)   ||  (tecla==13)   )         {
  var cmp = aOrdemCmps[foco];
  
  if (foco < nQtdeCamposTextForm)  {                 //aCmps.length-1  
    var cProx = document.getElementById( aOrdemCmps[foco+1] );
    cProx.focus();      
  }
    
  
}

// seta para cima foi press
if ( (tecla==38)  )           {
  if (foco != 1)  {
    var cProx = document.getElementById(aOrdemCmps[foco-1]);
    cProx.focus();      
  }
}
}

/********************************************************************************
 funcao chamada qdo campo TEXTBOX recebe o foco
********************************************************************************/
function FOCO(idCAMPO)   {

// HAbilita textboxes do form
var allfields = document.getElementsByTagName("input");

for(i=0; i<allfields.length; i++)  {
  var field = allfields[i];
  if ((field.getAttribute("type") == "text") || (field.getAttribute("type") == "password") ) {
	  field.disabled=false;
  }
}

foco = document.getElementById(idCAMPO).tabIndex;
cfoco = document.getElementById(idCAMPO).id;


document.getElementById(idCAMPO).select();
document.getElementById(idCAMPO).focus();
  

/* pesquisa mnemonica, indicamos campos com essa pesquisa mudando seu TITLE para qq coisa menos vazio  */
if (document.getElementById(idCAMPO).title!='') {
  auxiliar=idCAMPO.replace('txt', 'pesq');
  
  eval(auxiliar+"='" + document.getElementById(idCAMPO).value+"'");
  
  createSelection(document.getElementById(idCAMPO), 0, document.getElementById(idCAMPO).value.length);
   
}
if  (idCAMPO.toUpperCase().indexOf('TXTPR') == -1) {
  /* se nao temos a informacao sobre cores, utilizamos qq uma */
  lPopUp=false;
  try {
    a = window.top.document.getElementById('corMouseOverAuxilio').value;
  } catch(e) {
    lPopUp=true;
  }  

  document.getElementById(idCAMPO).style.backgroundColor= 
    lPopUp ? 'lightblue' : window.top.document.getElementById('corTextBox').value;
}    
      
/*  document.getElementById(idCAMPO).setAttribute(propCLASSE, "CorTxtBoxAtivo"); 
"#CDDCFF"; */
  


document.getElementById(idCAMPO).value = document.getElementById(idCAMPO).value ; 



}

/********************************************************************************
 Função que verifica se a pessoa digitou no formato correto dependendo da
 máscara passada
********************************************************************************/
function sistema_formatar( e, src, mask, funcaoEnter ) {
	var i = src.value.length;
	var saida = mask.substring(i,i+1);
	var ascii = (navigator.appName == "Netscape") ? e.which : e.keyCode;
	var erro = false;
	var letra = String.fromCharCode(ascii);
	
	if ( ascii == 0 || ascii == 8 )
		return true;
	else if ( ascii == 13 ) {
		if ( typeof funcaoEnter != 'undefined' ) {
			eval(funcaoEnter);
			return false;
		}
	}
	/* campo do tipo: Credito ou Desconto */
	else if ( saida == 'C' ) {
		if ( ascii == 99 || ascii == 100 || ascii == 67 || ascii == 68 )     
			return true;
		else
			return false;
	} 
   
	else if ( saida == 'A' ) {
		if ( (ascii >=97) && (ascii <= 122) )
			return true;
		else
			return false;
	} 
	/* campo do tipo: sim ou nao */
	else if ( saida == 'R' ) {		
		if (   (letra.toUpperCase() == 'S') || (letra.toUpperCase() == 'N')  )
			return true;
		else
			return false;
	}	
	/* campo do tipo: sexo */
	else if ( saida == 'S' ) {		    	
		if (   (letra.toUpperCase() == 'F') || (letra.toUpperCase() == 'M')  )
			return true;
		else
			return false;
	}		
	/* campo do tipo: estado civil */
	else if ( saida == 'E' ) {		    
		var letra = letra.toUpperCase();
		if (  (letra == 'C') || (letra == 'D') || (letra == 'J') || 
					(letra == 'M') || (letra == 'R') || (letra == 'S') ||
					(letra == 'C')					 )
			return true;
		else
			return false;
	}			
	else if (saida == '0') {
		if ( ascii >= 48 && ascii <= 57 )  
			return true;
		else
			return false;
	} else if ( saida == '#' )
		return true;
	else {
		src.value += saida;
		i += 1;
		saida = mask.substring(i,i+1);

		if ( saida == 'A' ) {
			if ( (ascii >=97) && (ascii <= 122) )
				return true;
			else { return false; }
		} else if (saida == '0') {
			if ( (ascii >= 48) && (ascii <= 57) )
				return true;
			else
				return false;
		} else
			return true;
	}
}


/********************************************************************************
 coloca data no formato dd/mm/yy
********************************************************************************/
function mascara_data(campo)        {

var mydata = '';
var data = campo.value;


var UltDig = data.charCodeAt(data.length-1);

if (UltDig >= 48 && UltDig <= 57)     {
if (data.length == 2){
    campo.value = data + '/';         }

if (data.length == 5){
    campo.value = data + '/';         }

}     else    {

campo.value = data.substring(0, data.length-1);
}


}



/********************************************************************************
 controla edição de campos currency
********************************************************************************/
function FormatMoney(vlr_cmp, e)    	{


if (window.event) iKeyCode=e.keyCode;
else iKeyCode=e.which;


var texto=vlr_cmp;

if ( ( iKeyCode >= 48 && iKeyCode <= 57 ) || ( iKeyCode == 8 || iKeyCode == 0  ) )
   var a=1; 

else if (iKeyCode == 44 || iKeyCode == 46)    	{
  if ( texto.indexOf(",") != -1  || texto.indexOf(".") != -1  )    	 return false; 
}
else	    
   return false;

}


/****************************************************
captura a posição de uma div, textbox, ou qq componente HTML - util para 
quando criando tooltip em relação a determinado campo, pois precisa saber sua
localização na tela 
**********************************************************/
function getAbsolutePosition(element){
    var ret = new Point();
    for(; 
        element && element != document.body;
        ret.translate(element.offsetLeft, element.offsetTop), element = element.offsetParent
        );
        
    return ret;
}

		/*  FUNCAO AUXILIAR DA FUNCAO ACIMA */

		function Point(x,y){
		        this.x = x || 0;
		        this.y = y || 0;
		        this.toString = function(){
		            return '('+this.x+', '+this.y+')';
		        };
		        this.translate = function(dx, dy){
		            this.x += dx || 0;
		            this.y += dy || 0;
		        };
		        this.getX = function(){ return this.x; }
		        this.getY = function(){ return this.y; }
		        this.equals = function(anotherpoint){
		            return anotherpoint.x == this.x && anotherpoint.y == this.y;
		        };
		}


/* seleciona parte de um textbox - equivale selStart, selEnd do visual basic */		
function createSelection(field, start, end) {

    if( field.createTextRange ) {
        var selRange = field.createTextRange();
        selRange.collapse(true);
        selRange.moveStart('character', start);
        selRange.moveEnd('character', end-start);
        selRange.select();
    } else if( field.setSelectionRange ) {
        field.setSelectionRange(start, end);
        field.selectionStart=0; 
        field.selectionEnd=end;
//        field.select();
//        field.setSelectionRange(field.value.length, field.value.length);        
    } else if( field.selectionStart ) {
        field.selectionStart = start;
        field.selectionEnd = end;
    }
    field.focus();
}