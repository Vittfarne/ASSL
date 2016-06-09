<?php

define("USER", "");
define("PASS", "");
define("SALT", "29d0be3d2492d99362169be3d4238cb35b4"); // IMPORTANT: Must enter a random string (min 16 chars) here which will be used to create hashes.

error_reporting(0);
if (strlen(SALT) < 16) {die("system error: Min 16 character random string as SALT is required.");}
chdir(dirname(__FILE__)) or die("system error: chdir");
@mkdir("data");
if ($argc) {process($argv);}
if ($_REQUEST['id']) {status();}
$c=$_REQUEST['_'];
if ($c=='verify') {verify();}

echo <<<S_HTML
<html>
<body bgcolor="#E6F2FF">
<br><br>
<form method="POST">
<div id="content" align="center" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1">
  <center>
<table border="0" cellpadding="3" cellspacing="0" style="border-collapse: collapse; font-family: Tahoma; font-size: 10pt; letter-spacing: 1" bordercolor="#111111">
  <tr>
    <td width="100%" colspan="2" dir="ltr" style="color: #FFFFFF" bgcolor="#808080">
    Paste your CSR below:</td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0" dir="ltr">&nbsp;</td>
    </tr>
  <tr>
    <td width="100%" colspan="2" dir="ltr" align="center">
    <textarea rows="13" name="csr" cols="90" style="font-family: Tahoma; font-size: 8pt"></textarea></td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0">&nbsp;</td>
    </tr>
  <tr>
    <td width="100%" colspan="2" style="color: #FFFFFF" bgcolor="#808080">
    Email Address:</td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0">&nbsp;</td>
    </tr>
  <tr>
    <td width="50%" align="right">
    <font size="1">(the certificate will be e-mailed here<br>
    so make sure you enter it a correctly)</font></td>
    <td width="50%">
    <input type="text" name="email" size="32" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1"></td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0">&nbsp;</td>
    </tr>
  <tr>
    <td width="100%" colspan="2" style="color: #FFFFFF" bgcolor="#808080">
    Optional: <font size="1">(leave them blank to use defaults)</font></td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0">&nbsp;</td>
  </tr>
  <tr>
    <td width="50%" align="right">
    <p dir="ltr">First Name:<br>
    <font size="1">(only alphabets. 3-20 chars)</font></td>
    <td width="50%">
    <input type="text" name="fname" size="32" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1"></td>
  </tr>
  <tr>
    <td width="50%" align="right">
    <p dir="ltr">Last Name:<br>
    <font size="1">(only alphabets. 3-20 chars)</font></td>
    <td width="50%">
    <input type="text" name="lname" size="32" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1"></td>
  </tr>
  <tr>
    <td width="50%" align="right">
    <p dir="ltr">Phone:<br>
    <font size="1">(only digits. 6-20 chars)</font></td>
    <td width="50%">
    <input type="text" name="phone" size="32" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1"></td>
  </tr>
  <tr>
    <td width="100%" colspan="2" style="padding: 0">&nbsp;</td>
    </tr>
  <tr>
    <td width="100%" colspan="2" align="center">
    <input name="next" onclick="check(this.form)" type="button" value="Verify &gt;" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1"></td>
  </tr>
  </table>
  </center>
</div>
<input type="hidden" name="_" value="verify">
</form>

<script>
function check(f) {ajax("?", function(res) {eval(res);}, "ajax=1&_=verify&csr="+encodeURIComponent(f.csr.value)+"&email="+encodeURIComponent(f.email.value));}
function ajax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else{if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("Content-type","application/x-www-form-urlencoded")}else{C.open("GET",B,true)}C.send(this.postBody)}};
</script>

</body>
</html>
S_HTML;


function verify()
{
$fname=preg_match('/^\s*([a-zA-Z]{3,20})\s*$/', $_REQUEST['fname'], $j) ? $j[1] : 'John';
$lname=preg_match('/^\s*([a-zA-Z]{3,20})\s*$/', $_REQUEST['lname'], $j) ? $j[1] : 'Doe';
$email=filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL);
$phone=preg_match('/^\s*([0-9]{6,20})\s*$/', $_REQUEST['phone'], $j) ? $j[1] : '9876543210';
$csr=preg_replace('!(\r\n|\r|\n)!', "\n", trim($_REQUEST['csr']));
$hash=md5($csr);

if (!$email) {$e='A valid email address is required. SSL certificate will be mailed to it.';}
elseif (($cn=getcn("$csr"))===false) {$e='CSR is invalid.';}
else
{
$fp=fopen("data/$hash.lock", 'w');
if(!flock($fp, LOCK_EX | LOCK_NB)) {$e='SSL issue is already under process for this CSR. Please wait few minutes before trying again.';}
else {flock($fp, LOCK_UN);}
fclose($fp);
}

if ($e) {die("alert('$e');");}

$md5=md5($fname.$lname.$email.$phone.$csr);
if ($_REQUEST["ajax"])
{
echo <<<S_HTML
if(confirm("The CSR you have provided belongs to the domain:\\n\\n$cn\\n\\nClick OK to proceed or cancel to change.")) {f.submit();}
S_HTML;
}
else
{
$uid=md5(time().rand().$fname.$lname.$email.$phone.$csr);
$id=md5($uid.SALT);
file_put_contents("data/$id.txt", serialize(array('fname'=>$fname, 'lname'=>$lname, 'email'=>$email, 'phone'=>$phone, 'csr'=>$csr, 'cn'=>$cn, 'status'=>'Please wait ...'))) or die("system error: write-id-file");
exec("php5 ".__FILE__." $id > /dev/null 2>&1 &");
header("Location: ?id=$uid");
}
exit;
}


function status()
{
$uid=$_REQUEST['id'];
$id=md5($uid.SALT);
if (!$id || !file_exists("data/$id.txt")) {$error="session: $uid not found.";}
else {$d=session($id); extract($d);}

$ve=$_REQUEST['vemail'];
if ($ve && !$vemail && count($emails) && in_array($ve, $emails) && !$error && !$done)
{
session($id, 'status', "<u>$ve</u> selected as validation email address.<br>Submitting order..");
extract(session($id, 'vemail', $ve));
exec("php5 ".__FILE__." $id > /dev/null 2>&1 &");
die(header("Location: ?id=$uid"));
}

if (!$done && !$error && !$vemail && count($emails))
{
$icon='info.png';
foreach ($emails as $i) $html.="<a href='?id=$uid&vemail=$i'>$i</a><br>";
$refresh='';
}
elseif ($error) {$icon='error.png'; $error="<font color='#FF0000'><h2>($error)</h2></font>";}
elseif ($done) {$icon='success.png';}
else {$icon='progress.gif'; $refresh=1; $info='<br>This page will refresh every 5 seconds. Sometimes it can take upto 2-3 minutes to show any status changes.';}

if ($refresh) {$refresh="<meta http-equiv='refresh' content='5;URL=?id=$uid'>";}

echo <<<S_HTML
<html>
<head>
$refresh
<style>
a {color: blue; text-decoration: none;}
a:visited {color: blue; text-decoration: none;}
a:hover {color: red;text-decoration: underline;}
</style>
</head>
<body bgcolor="#E6F2FF">
<div align="center" style="font-family: Tahoma; font-size: 10pt; letter-spacing: 1">
<br><img src="$icon"><br>$info<br><h3>$status</h3>
$error
$html
</div>
</body>
</html>
S_HTML;
exit;
}


function process($argv)
{
$id=$argv[1];
extract(session($id));
if (!$cn) {session($id, 'error', 1); die("session not found.");}
if ($done || $error) {die("process already completed.");}

$hash=md5($csr);

$fp=fopen("data/$hash.lock", 'w');
if(!flock($fp, LOCK_EX | LOCK_NB)) {session($id, 'error', 1); error($id, __LINE__, "Could not get acquire file lock.");}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
 
$cf="data/$id.cookies";
curl_setopt($ch, CURLOPT_COOKIEFILE, $cf);
curl_setopt($ch, CURLOPT_COOKIEJAR,  $cf);


if (!$csrf)
{
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/"); 
$d = curl_exec($ch);
if (!preg_match('/csrftoken=(.+?);/', $d, $csrf)) {error($id, __LINE__, "Failed to get initial CSRF token.", $d);}
$csrf=urldecode($csrf[1]);
session($id, 'status', 'Logging in..');
$post = array('csrftoken' => $csrf,'username' => USER,'password' => PASS);
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/user/actn/login/"); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$d = curl_exec($ch);
if (!preg_match('/csrftoken=(.+?);/', $d, $csrf)) {error($id, __LINE__, "Failed to get CSRF token.", $d);}
$csrf=urldecode($csrf[1]);
if (strpos($d, '<meta http-equiv="refresh" content="0;URL=\'/user/dashboard\'"')===false) {error($id, __LINE__, "Login failed.", $d);}
session($id, 'csrf', $csrf);
session($id, 'status', 'Logged in.. CSRF token retrieved.');
}

if (!count($emails))
{
session($id, 'status', 'Generating order..');
$post = array('csrftoken'  => $csrf,'domain' => $cn,'first' => $fname,'last' => $lname,'email' => $email,'phone' => $phone);
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/actn/ssl-create/"); 
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$d = curl_exec($ch);
if (!preg_match('~\Q<meta http-equiv="refresh" content="0;URL=\'/account/ssl-purchase/\E(.+?)/\'"~', $d, $order))  {error($id, __LINE__, "Could not generate order.", $d);}
$order=$order[1];
session($id, 'order', $order);
session($id, 'status', "Order: <u>$order</u> generated. Fetching email addresses..");
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/ssl-purchase/$order/"); 
curl_setopt($ch, CURLOPT_HTTPGET, 1);
$d = curl_exec($ch);
if (!preg_match_all('/<option value="(.+?)"/', $d, $e)) {error($id, __LINE__, "Could not retrieve validation email addressess.", $d);}
$e=$e[1];
session($id, 'emails', $e);
session($id, 'status', "Email addresses retrieved. Waiting for you to choose one:");
}

elseif($vemail)
{
$post=array('csrftoken'  => $csrf, 'email' => $vemail,'csr' => $csr);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/actn/ssl-purchase/$order/"); 
$d = curl_exec($ch);
if (strpos($d, '<meta http-equiv="refresh" content="0;URL=\'/account/ssl-finalize/\'" />')===false) {error($id, __LINE__, "Could not submit order.", $d);}
session($id, 'status', "Process completed successfully.<br><br>You will recieve a email with validation link on this address: <u>$vemail</u>.<br>It can take from few minutes to a couple of hours for the mail to arrive in your Inbox.<br>You must visit that link & click on the \"I Approve\" button for your certificate to be issued.<br>The SSL certificate will be emailed to: <u>$email</u><br><br>");
session($id, 'done', 1);
}

curl_close($ch);
flock($fp, LOCK_UN);
fclose($fp);
unlink("data/$hash.lock");

exit;
}

function session($id, $k="", $v="")
{
$d=unserialize(file_get_contents("data/$id.txt"));
if ($k) {$d[$k]=$v;}
file_put_contents("data/$id.txt", serialize($d));
return $d;
}

function error($id, $line, $msg, $log='')
{
session($id, 'log', $log);
session($id, 'error-line', $line);
session($id, 'error', $msg);
die();
}

function getcn($csr)
{
$f=tempnam(sys_get_temp_dir(), 'csr');
file_put_contents($f, $csr);
$cn=exec("openssl req -in $f -noout -subject 2>/dev/null");
unlink($f);
if(preg_match('~CN=([^/\s]+)~', $cn, $cn)) {return $cn[1];}
return false;
}

?>


