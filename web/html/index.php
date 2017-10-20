<?php
session_start();
define("USER", "");
define("PASS", "");
define("SALT", "362169249cb35ba2d9942329d0be3d48"); // IMPORTANT: Must enter a random string (min 16 chars) here which will be used to create hashes.
define("DATADIR", "../data"); //DataDir
define("ASSL", "");

$allowedlangs = ['en','sv', 'nl'];

if (isset($_GET["lang"])){
  if (in_array($_GET["lang"], $allowedlangs)){
    $_SESSION["lang"] = $_GET["lang"];
  } else {
    $_SESSION["lang"] = "en";
  }
} elseif (!isset($_SESSION["lang"])){
$_SESSION["lang"] = "en";
} 

$lng = $_SESSION["lang"];

if (file_exists("lang/".$lng.".php")){
  include ("lang/".$lng.".php");
} else {
  die("Language not supported. Go to <a href='?lang=en'>?lang=en</a> for default");
}

error_reporting(E_ALL);
if (strlen(SALT) < 16) {die("system error: Min 16 character random string as SALT is required.");}
chdir(dirname(__FILE__)) or die("system error: chdir");
@mkdir(DATADIR);
if ($argc) {process($argv);}
if ($_REQUEST['id']) {status($lng);}
$c=$_REQUEST['_'];
if ($c=='verify') {verify($lng);}

echo <<<S_HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Loovit AutoSSL</title>
  <link rel="stylesheet" href="main.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <script src="dist/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="dist/sweetalert.css">
</head>
<body>
<div id="wrapper">
<h1 class="rubrik centext">Loovit AutoSSL</h1>
  <div id="con1" class="onecont">
    <form method="POST">
    <table border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td width="100%" colspan="2" dir="ltr">
        <h2 class="rubrik">{$LANG['pastecsr']}</h2></td>
      </tr>
      <tr>
        <td width="100%" colspan="2" dir="ltr" align="center">
        <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" rows="18" name="csr" class="input" cols="90"></textarea></td>
      </tr>
      <tr>
        <td width="25%">
        <p class="text">{$LANG['email']}<br>
        <font size="1">{$LANG['emaildesc']}</font></p></td>
        <td width="75%">
        <input type="text" name="email" class="input" placeholder="{$LANG['email']}" size="32"></td>
      </tr>
      <tr>
        <td width="100%" colspan="2" align="center">
        <input name="next" onclick="check(this.form)" class="submit" type="button" onsubmit="check(this.form)" value="{$LANG['verify']} &gt;"></td>
      </tr>
        </table>
  </div>
         <div id="con2" class="onecont">
      <table border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td width="100%" colspan="2">
        <p class="text">{$LANG['optional']}: <font size="1">{$LANG['ldefault']}</font></p></td>
      </tr>
      <tr>
        <td width="25%" align="right">
        <p dir="ltr" class="text">{$LANG['fname']}<br>
        <font size="1">{$LANG['fdesc']}</font></p></td>
        <td width="75%">
        <input type="text" class="input" placeholder="{$LANG['fname']}" name="fname" size="32"></td>
      </tr>
      <tr>
        <td width="25%" align="right">
        <p dir="ltr" class="text">{$LANG['sname']}<br>
        <font size="1">{$LANG['sdesc']}</font></p></td>
        <td width="75%">
        <input type="text" name="lname" class="input" placeholder="{$LANG['sname']}" size="32"></td>
      </tr>
      <tr>
        <td width="25%" align="right">
        <p dir="ltr" class="text">{$LANG['phone']}<br>
        <font size="1">{$LANG['pdesc']}</font></p></td>
        <td width="75%">
        <input type="text" name="phone" class="input" placeholder="{$LANG['phone']}" size="32"></td>
      </tr>

      </table>
    <input type="hidden" name="_" value="verify">
    </form>

  </div>
 
    <script>
    function check(f) {ajax("?", function(res) {eval(res);}, "ajax=1&_=verify&csr="+encodeURIComponent(f.csr.value)+"&email="+encodeURIComponent(f.email.value));}
    function ajax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else{if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("Content-type","application/x-www-form-urlencoded")}else{C.open("GET",B,true)}C.send(this.postBody)}};
    </script>
</div>
</body>
</html>
S_HTML;

function verify($lng)
{
global $LANG;
$fname=preg_match('/^\s*([a-zA-Z]{3,20})\s*$/', $_REQUEST['fname'], $j) ? $j[1] : 'John';
$lname=preg_match('/^\s*([a-zA-Z]{3,20})\s*$/', $_REQUEST['lname'], $j) ? $j[1] : 'Doe';
$email=filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL);
$phone=preg_match('/^\s*([0-9]{6,20})\s*$/', $_REQUEST['phone'], $j) ? $j[1] : '9876543210';
$csr=preg_replace('!(\r\n|\r|\n)!', "\n", trim($_REQUEST['csr']));
$hash=md5($csr);
;

if (!$email) {$e=$LANG['e_validemail'];}
elseif (($cn=getcn("$csr"))===false) {$e=$LANG['e_invalidcsr'];}
else
{
$fp=fopen(DATADIR."/$hash.lock", 'w');
if(!flock($fp, LOCK_EX | LOCK_NB)) {$e=$LANG['e_inprog'];}
else {flock($fp, LOCK_UN);}
fclose($fp);
}

if ($e) {die('sweetAlert("'.$LANG['oops'].'", "'.$e.'","error");');}

$md5=md5($fname.$lname.$email.$phone.$csr);
if ($_REQUEST["ajax"])
{
echo <<<S_HTML
swal({
  title: "{$LANG['i_csrcheck1']}",
  text: "{$LANG['i_csrcheck2']} \\n\\n{$cn}\\n\\n {$LANG['i_csrcheck3']}",
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#6beb93",
  confirmButtonText: "{$LANG['i_csrcheckok']}",
  cancelButtonText: "{$LANG['i_csrcheckcancel']}",
  closeOnConfirm: false
},
function(){
  f.submit();
});
S_HTML;
}
else
{
$uid=md5(time().rand().$fname.$lname.$email.$phone.$csr);
$id=md5($uid.SALT);
file_put_contents(DATADIR."/$id.txt", serialize(array('fname'=>$fname, 'lname'=>$lname, 'email'=>$email, 'phone'=>$phone, 'csr'=>$csr, 'cn'=>$cn, 'sal'=>$lng, 'status'=>$LANG['pwait']))) or die("system error: write-id-file");
exec("php ".__FILE__." $id > /dev/null 2>&1 &");
header("Location: ?id=$uid");
}
exit;
}


function status($lng)
{
global $LANG;
$uid=$_REQUEST['id'];
$id=md5($uid.SALT);
if (!$id || !file_exists(DATADIR."/$id.txt")) {$error="{$LANG['ses']} $uid {$LANG['notfound']}.";}
else {$d=session($id); extract($d);}


$ve=$_REQUEST['vemail'];
if ($ve && !$vemail && count($emails) && in_array($ve, $emails) && !$error && !$done)
{
//session($id, 'status', "<u>$ve</u> Select email");
session($id, 'status', "<u>$ve</u> {$LANG['selval']}");
extract(session($id, 'vemail', $ve));
exec("php ".__FILE__." $id > /dev/null 2>&1 &");
die(header("Location: ?id=$uid"));
}

if (!$done && !$error && !$vemail && count($emails))
{
$icon='<img src="info.png"><br>';
foreach ($emails as $i) $html.="<p class='text centext'><a class='emaillink' href='?id=$uid&vemail=$i'>$i</a></p>";
$refresh='';
}
elseif ($error) {$icon='<img src="error.png"><br>'; $error="<p class=\"text centext\"><h2 class='rubrik'>($error)</h2>";}
elseif ($done) {$icon='<img src="success.png"><br>';}
else {$icon=''; $refresh=1; $info='
<div class="sk-circle">
  <div class="sk-circle1 sk-child"></div>
  <div class="sk-circle2 sk-child"></div>
  <div class="sk-circle3 sk-child"></div>
  <div class="sk-circle4 sk-child"></div>
  <div class="sk-circle5 sk-child"></div>
  <div class="sk-circle6 sk-child"></div>
  <div class="sk-circle7 sk-child"></div>
  <div class="sk-circle8 sk-child"></div>
  <div class="sk-circle9 sk-child"></div>
  <div class="sk-circle10 sk-child"></div>
  <div class="sk-circle11 sk-child"></div>
  <div class="sk-circle12 sk-child"></div>
</div>
<p class="text centext">'.$LANG['refreshing'].'</p>';}

if ($refresh) {$refresh="<meta http-equiv='refresh' content='5;URL=?id=$uid'>";}
//$refresh
echo <<<S_HTML
<!DOCTYPE html>
<html lang="{$LANG['lngkey']}">
<head>
  <meta charset="UTF-8">
  <title>$status | Loovit AutoSSL</title>
  <link rel="stylesheet" href="main.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  $refresh
  <script src="dist/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="dist/sweetalert.css">
</head>
<body>
<div id="wrapper">
<h1 class="rubrik centext">Loovit AutoSSL</h1>
<div id="con1" class="onecont">
<p class="text centext">
$icon
$info
</p>
<h2 class="rubrik">$status</h2>
$error
$html
</div>
</div>
</body>
</html>
S_HTML;
exit;
}


function process($argv)
{
global $LANG;
$id=$argv[1];
extract(session($id));
include_once("lang/".$sal.".php");
if (!$cn) {session($id, 'error', 1); die($error=$LANG['ses'] . $LANG['notfound']);}
if ($done || $error) {die($LANG['procompl']);}

$hash=md5($csr);


$fp=fopen(DATADIR."/$hash.lock", 'w');
if(!flock($fp, LOCK_EX | LOCK_NB)) {session($id, 'error', 1); error($id, __LINE__, "Could not get acquire file lock.");}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
 
$cf=DATADIR."/$id.cookies";
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
session($id, 'status', $LANG['genorder']);
$post = array('csrftoken'  => $csrf,'domain' => $cn,'first' => $fname,'last' => $lname,'email' => $email,'phone' => $phone);
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/actn/ssl-create/"); 
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$d = curl_exec($ch);
if (!preg_match('~\Q<meta http-equiv="refresh" content="0;URL=\'/account/ssl-purchase/\E(.+?)/\'"~', $d, $order))  {error($id, __LINE__, "Could not generate order.", $d);}
$order=$order[1];
session($id, 'order', $order);
session($id, 'status', $LANG['order']." <u>$order</u> ".$LANG['fetch']);
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/ssl-purchase/$order/"); 
curl_setopt($ch, CURLOPT_HTTPGET, 1);
$d = curl_exec($ch);
if (!preg_match_all('/<option value="(.+?)"/', $d, $e)) {error($id, __LINE__, $LANG['notret'], $d);}
$e=$e[1];
session($id, 'emails', $e);
session($id, 'status', $LANG['chemail']);
}

elseif($vemail)
{
$post=array('csrftoken'  => $csrf, 'email' => $vemail,'csr' => $csr);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_URL, "https://leap.singlehop.com/account/actn/ssl-purchase/$order/"); 
$d = curl_exec($ch);
if (strpos($d, '<meta http-equiv="refresh" content="0;URL=\'/account/ssl-finalize/\'" />')===false) {error($id, __LINE__, "Could not submit order.", $d);}
session($id, 'status', $LANG['compl1']." <u>$vemail</u><br>".$LANG['compl2']." <u>$email</u><br><br><a class='emaillink' href='.'>".$LANG['gennew']."</a><br>");
session($id, 'done', 1);
}

curl_close($ch);
flock($fp, LOCK_UN);
fclose($fp);
unlink(DATADIR."/$hash.lock");

exit;
}

function session($id, $k="", $v="")
{
$d=unserialize(file_get_contents(DATADIR."/$id.txt"));
if ($k) {$d[$k]=$v;}
file_put_contents(DATADIR."/$id.txt", serialize($d));
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
