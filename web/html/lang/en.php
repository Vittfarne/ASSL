<?php

if (!defined("ASSL")) {
	die('Error: 403');
}

$LANG['lngkey']				=		'en';
$LANG['langname']			=		'English';
$LANG['pastecsr']			=		'Paste your CSR below:';
$LANG['email']				=		'Email Address:';
$LANG['emaildesc']	   		=		'(Important: The certificate will be e-mailed here)';
$LANG['verify']		    	=		'Verify';
$LANG['optional']			=		'Optional';
$LANG['ldefault']			=		'(leave them blank to use defaults)';
$LANG['fname']				=		'First Name:';
$LANG['fdesc']				=		'(only alphabets. 3-20 chars)';
$LANG['sname']				=		'Last Name:';
$LANG['sdesc']				=		'(only alphabets. 3-20 chars)';
$LANG['phone']				=		'Phone:';
$LANG['pdesc']				=		'(only digits. 6-20 chars)';
$LANG['e_validemail']    	=		'A valid email address is required. SSL certificate will be mailed to it.';
$LANG['e_invalidcsr']    	=		'CSR is invalid.';
$LANG['e_inprog']			=		'SSL issue is already under process for this CSR. Please wait few minutes before trying again.';
$LANG['i_csrcheck2']	    =		'The CSR you have provided belongs to the domain:';
$LANG['i_csrcheck3']	    =		'Click OK to proceed or cancel to change!';
$LANG['i_csrcheck1']	    =		'Validate request';
$LANG['i_csrcheckok']    	=		'Ok!';
$LANG['i_csrcheckcancel']	=		'Cancel';
$LANG['pwait']			    =		'Please wait ...';
$LANG['ses']			    =		'session';
$LANG['notfound']		    =		'not found';
$LANG['oops']			    =		'Oops...';
$LANG['selval']            	=    	'selected as validation email address.<br>Submitting order..';
$LANG['refreshing']        	=    	'This page will refresh every 5 seconds. Sometimes it can take upto 2-3 minutes to show any status changes.';
$LANG['procompl']          	=    	'process already completed.';
$LANG['compl1']            	=    	'Process completed successfully.<br><br>You will recieve a email with validation link on this address:';
$LANG['compl2']            	=    	'It can take from few minutes to a couple of hours for the mail to arrive in your inbox.<br>You must visit that link & click on the "I Approve" button for your certificate to be issued.<br>The SSL certificate will be emailed to:';
$LANG['notret']            	=    	'Could not retrieve validation email addressess.';
$LANG['chemail']           	=    	'Email addresses retrieved. Waiting for you to choose one:';
$LANG['order']             	=    	'Order:';
$LANG['fetch']             	=    	'generated. Fetching email addresses..';
$LANG['genorder']          	=    	'Generating order..';
$LANG['gennew']            	=    	'Generate a new certifiacte';