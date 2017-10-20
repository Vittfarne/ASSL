<?php

if (!defined("ASSL")) {
	die('Error: 403');
}

$LANG['lngkey']				=		'sv';
$LANG['langname']			=		'Swedish';
$LANG['pastecsr']			=		'Klistra in din CSR nedan:';
$LANG['email']				=		'E-postadress:';
$LANG['emaildesc']	   		=		'(Viktigt: Certifikatet kommer att skickas till adressen)';
$LANG['verify']		    	=		'Verifiera';
$LANG['optional']			=		'Valfritt';
$LANG['ldefault']			=		'(lämna tomt för att använda standard)';
$LANG['fname']				=		'Förnamn:';
$LANG['fdesc']				=		'(endast bokstäver. 3-20 tecken)';
$LANG['sname']				=		'Efternamn:';
$LANG['sdesc']				=		'(endast bokstäver. 3-20 tecken)';
$LANG['phone']				=		'Telefon:';
$LANG['pdesc']				=		'(endast siffror. 6-20 tecken)';
$LANG['e_validemail']    	=		'En giltig e-postadress krävs. SSL-certifikatet kommer skickas till den.';
$LANG['e_invalidcsr']    	=		'Ogiltig CSR';
$LANG['e_inprog']			=		'SSL processen är redan startad för denna CSR. Var god vänta någon minut innan du försöker igen.';
$LANG['i_csrcheck2']	    =		'Följande domän hittades i den angivna CSR:en';
$LANG['i_csrcheck3']	    =		'Klicka på OK för att fortsätta eller avbryt om du vill ändra!';
$LANG['i_csrcheck1']	    =		'Kontrollera begäran';
$LANG['i_csrcheckok']    	=		'Ok!';
$LANG['i_csrcheckcancel']	=		'Avbryt';
$LANG['pwait']			    =		'Var god vänta ...';
$LANG['ses']			    =		'session';
$LANG['notfound']		    =		'hittades inte';
$LANG['oops']			    =		'Hoppsan...';
$LANG['selval']            	=    	'vald som valideringsadress.<br>Skickar order..';
$LANG['refreshing']        	=    	'Sidan uppdateras var femte sekund. Ibland kan det ta upp emot 2-3 minuter innan statusen ändras.';
$LANG['procompl']          	=    	'processen redan klar.';
$LANG['compl1']            	=    	'Processen slutförd.<br><br>Ett meddelande med en bekräftelselänk kommer skickas till adressen:';
$LANG['compl2']            	=    	'Det kan ta allt från några minuter till några timmar innan e-postmeddelandet dyker upp i inkorgen.<br>Du måste följa länken och klicka på "I Approve"-knappen för att certifikatet ska utfärdas.<br>Certifikatet kommer skickas till:';
$LANG['notret']            	=    	'Det gick inte att hämta e-postadressen för valideringen.';
$LANG['chemail']           	=    	'E-postadresser hämtade. Väntar på att du väljer en:';
$LANG['order']             	=    	'Order:';
$LANG['fetch']             	=    	'genererad. Hämtar e-postadresser...';
$LANG['genorder']          	=    	'Genererar order...';
$LANG['gennew']            	=    	'Skapa nytt certifikat';