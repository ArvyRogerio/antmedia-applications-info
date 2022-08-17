<?
$Servidor='http://127.0.0.1:5080';
$Email='seuemail@example.com';
$Senha='Su@S3nh@d3@dm1n';
$API='applications-info';

$ch=curl_init();

curl_setopt($ch,CURLOPT_TIMEOUT,15);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($ch,CURLOPT_IPRESOLVE,CURL_IPRESOLVE_V4);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

function NovoSession() {

   global $ch,$Session,$Servidor,$Email,$Senha;

   curl_setopt($ch,CURLOPT_HEADER,true);
   curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   curl_setopt($ch,CURLOPT_URL,$Servidor.'/rest/v2/users/authenticate');
   curl_setopt($ch,CURLOPT_POST,1);
   curl_setopt($ch,CURLOPT_POSTFIELDS,'{"email":"'.$Email.'","password":"'.md5($Senha).'"}');

   $server_response=@curl_exec($ch); $eSession='';

   foreach (explode("\n",$server_response) as $d) if (preg_match('/JSESSIONID=(.*?);/',$d,$match)==1) $eSession=$match[1];

   if ($eSession!='') {
      $Session=$eSession;
      file_put_contents('session.txt',$Session);
   }

}

$Session=@file_get_contents('session.txt');

//Não existe sessão, gera
if ($Session=='') NovoSession();

//Não conseguiu gerar, avisa e sai
if ($Session=='') {
   echo 'ERR';
   exit(1);
}

//Tenta duas vezes
for ($a=1;$a<3;$a++) {

   curl_setopt($ch,CURLOPT_HEADER,false);
   curl_setopt($ch,CURLOPT_POST,0);
   curl_setopt($ch,CURLOPT_HTTPHEADER,array('Cookie: JSESSIONID='.$Session));
   curl_setopt($ch,CURLOPT_URL,$Servidor.'/rest/v2/'.$API);

   $server_response=@curl_exec($ch);

   //Se voltou erro, provavelmente a sessão expirou
   if ($server_response=='') {
      NovoSession();
   } else {
      break;
   }

}

//Não conseguiu obter os dados
if ($server_response=='') {
   echo 'ERR';
   exit(1);
}

////////////////
// Específico //
////////////////

//Daqui pra baixo é específico para /rest/v2/applications-info
$SomaStreams=0;

//O applications-info vem em Json
$server_response=json_decode($server_response,true);

//Erro ao ler o json
if ($server_response=='') {
   echo 'ERR';
   exit(1);
}

//Se chegou até aqui, recebeu o json corretamente, então soma cada app
foreach ($server_response as $a) $SomaStreams+=$a['liveStreamCount'];

curl_close($ch);

echo $SomaStreams;
exit(0);
?>
