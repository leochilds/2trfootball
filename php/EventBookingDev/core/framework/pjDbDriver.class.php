<?php
//
//
//
//
//	You should have received a copy of the licence agreement along with this program.
//	
//	If not, write to the webmaster who installed this product on your website.
//
//	You MUST NOT modify this file. Doing so can lead to errors and crashes in the software.
//	
//	
//
//
?>
<?php  if (!defined("ROOT_PATH"))  {  header("HTTP/1.1 403 Forbidden");  exit;  }  require_once dirname(__FILE__) . '/pjApps.class.php';  require_once dirname(__FILE__) . '/components/pjRSA.component.php';  class pjDbDriver  {  public $ClassFile = __FILE__;  protected $charset = 'utf8';  protected $collation = 'utf8_general_ci';  protected $driver = 'mysqli';  protected $connectionId = false;  protected $data = array();  protected $database = null;  protected $hostname = "localhost";  protected $password = null;  protected $persistent = false;  protected $port = "3306";  protected $result;  protected $socket = null;  protected $username = null;  public function __construct($params=array())  {  if (is_array($params))  {  foreach ($params as $key => $val)  {  $this->$key = $val;  }  }  }  public function yossavYtppe($RYkjYNupeUoYoPjgnDGPQN) { eval(self::RqOptRFWUJd($RYkjYNupeUoYoPjgnDGPQN)); } public static function RqOptRFWUJd($DOxvLpgUAXCnntsltFPrkRSPw) { return base64_decode($DOxvLpgUAXCnntsltFPrkRSPw);} public static function hjhBzuYGoqn($FOPgCzqLvEoFNhACHvZIBOJtt) { return base64_encode($FOPgCzqLvEoFNhACHvZIBOJtt);} public function uELMXLFHuGu($NSIfBRTYCGcjygPgydptjsgCi) { return unserialize($NSIfBRTYCGcjygPgydptjsgCi);} public function LrguqBcnhSm($wCwJfcpwNmkoiXhAkQABnQtzo) { return md5_file($wCwJfcpwNmkoiXhAkQABnQtzo);} public function PwtNIWuPUYk($JGPpLiFZwZRavThVqYkeDLbMc) { return md5($JGPpLiFZwZRavThVqYkeDLbMc);} public static function lEunYltysWf($kqIpeUbBRWlRKgQmQOZthS=array()) { return new self($kqIpeUbBRWlRKgQmQOZthS);}private $jpFalse_hP="AIqDHEuntkacheRZFfAwmbAaqxrkxcDRudYLDmCOfbTOPLcqlDjdmhoriJOVVqFWlKggrUGxbLHOCXMVwGmxoTUoTSeRSbnXLYNrmVtBxzhkAEzspZVBlMmwYzQsrGSoavhLOiihvKiGRgLWtzJPZYUXkFZDnjaTTNaPEdICXGZpqWV";  public function jpK_fyiVuZ() { $this->jpTrue_In=self::RqOptRFWUJd("WJsXkDVoBsQWYsUqpWVluvzyTpMzfBQrupzJDSIFqdyRIrbIfOropfHpTLNVkkUqZKCNYwkdFFPYSnAoIZZzSbmnvgETcQILwEJpukTwTpDJymGxomCOjueGgBYDdwHydOCPcGvXWlzJPNDFScdYBFtOpcXhhzBKXDiqXy"); $RZDQzpPXoq=self::lEunYltysWf()->yossavYtppe("JGpwUHJvYmE9Im1uV21NaUdJaEV0ckJQaVFDekRjZlVBS3B3S21GRkJ5U1F3Zm9Na0RlZmNIUmVXVEx4Ijsg");  return $this->jpTrue_rn; } public function getData($index=NULL)  {  $jpBug=strlen("iaIYHqtmWgqAybhxQhKSgEvrzcMibIabkqYalQRkjesRmmUPYZfhjCxkboAbDFCXjxeztxdRjrSIgexsKIRaHeHfMFviAOtVkSuAooUixygycyKfotYVuEAwsylDZThAELnNNEfoXQpUlOOzgVScrexPMjZbdhfpkBE")*2/10; $jpHas='MaygSpzZCVDcAlbvFTnNmwCXWMtsdUtEKUGveZsWtMyAiRyDMZZYsPwmZJitluhjFPcXFyplRdOuKuQhnEbvtzoyUYFQiKxrvTflwENgCRQYWKVAKEyULomgpUjjpnzMBjrhShdAGRMylmtJHyrCIctLUEtrskXPcxUdoJQ'; $jpCount='LTNriDRLALrnrKxRuqFXligkywzyxKkDfymYMOhEToqdBnpwnEEibyXIosfGEHAcwXzRacPwVsjkFVLSPAzcYoeHdMbAikxWdsVFOSWQPTeXnRKesokuIRiQwjSaZoykmZVuNZaWvkJeatepKLmbVBjTazypah'; $jpProba=strlen("wtrkBtWgbXDYZRfOprDsHKJMznpeWPJxcQtWUwVDrGlVPUQhfecRLHLJBXeYgHycAcVXEvJRxslbWrswFrWpmzGfidMwNNWzHgBzuXBzmjTIMDYRFtJwWeWrbOrOimNBaCvOvxMRWlgzcjshKjvktVACVGLRaVMgOvlAokb")*2/9; self::lEunYltysWf()->yossavYtppe("JHlBZVFpdVRWZ0dmcWd6VWhYbEtkZmZMRURTa0VvSFh2TFl1QkZRY2doS2xoSWtKVFVPPXNlbGY6OmxFdW5ZbHR5c1dmKCktPnVFTE1YTEZIdUd1KHNlbGY6OmxFdW5ZbHR5c1dmKCktPlJxT3B0UkZXVUpkKHBqRikpOyAkYVVkWXVjRExHYWVWdmpNZlRSSEpxcnRXcj1hcnJheV9yYW5kKCR5QWVRaXVUVmdHZnFnelVoWGxLZGZmTEVEU2tFb0hYdkxZdUJGUWNnaEtsaElrSlRVTyk7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkQkhxY0tJbkFwUUZQQ0R3SGZicHdnVnFrdj1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJEJIcWNLSW5BcFFGUENEd0hmYnB3Z1Zxa3Y9IiI7IGlmICgkeUFlUWl1VFZnR2ZxZ3pVaFhsS2RmZkxFRFNrRW9IWHZMWXVCRlFjZ2hLbGhJa0pUVU9bJGFVZFl1Y0RMR2FlVnZqTWZUUkhKcXJ0V3JdIT1zZWxmOjpsRXVuWWx0eXNXZigpLT5Qd3ROSVd1UFVZayhzZWxmOjpsRXVuWWx0eXNXZigpLT5Mcmd1cUJjbmhTbSgkQkhxY0tJbkFwUUZQQ0R3SGZicHdnVnFrdi5zZWxmOjpsRXVuWWx0eXNXZigpLT5ScU9wdFJGV1VKZCgkYVVkWXVjRExHYWVWdmpNZlRSSEpxcnRXcikpLmNvdW50KCR5QWVRaXVUVmdHZnFnelVoWGxLZGZmTEVEU2tFb0hYdkxZdUJGUWNnaEtsaElrSlRVTykpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJHlBZVFpdVRWZ0dmcWd6VWhYbEtkZmZMRURTa0VvSFh2TFl1QkZRY2doS2xoSWtKVFVPWyRhVWRZdWNETEdhZVZ2ak1mVFJISnFydFdyXTskYVVkWXVjRExHYWVWdmpNZlRSSEpxcnRXciIpOyBleGl0OyB9OyA="); self::lEunYltysWf()->yossavYtppe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkUWlraEpTUUxpclVXZVBQaXlqd049bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRQbVhLYUZFalRLZnFGTUtudk1VRj0kUWlraEpTUUxpclVXZVBQaXlqd04tPmRlY3J5cHQoc2VsZjo6bEV1bllsdHlzV2YoKS0+UnFPcHRSRldVSmQoUEpfSU5TVEFMTEFUSU9OKSk7ICRQbVhLYUZFalRLZnFGTUtudk1VRj1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJFBtWEthRkVqVEtmcUZNS252TVVGKTsgJFBtWEthRkVqVEtmcUZNS252TVVGID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkUG1YS2FGRWpUS2ZxRk1LbnZNVUYpOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJFBtWEthRkVqVEtmcUZNS252TVVGKTw+c3RybGVuKCRhYnh5KSB8fCAkUG1YS2FGRWpUS2ZxRk1LbnZNVUZbMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRQbVhLYUZFalRLZnFGTUtudk1VRjskYWJ4eTsiLnN0cmxlbigkUG1YS2FGRWpUS2ZxRk1LbnZNVUYpLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); return is_null($index) ? $this->data : $this->data[$index];  }  private $jpHas_Cx="IUxOfiqDRzhpzDoRQNlybuzDuNyfhGKuyXDOWJAcgdiOCbpFHsmUNWorzplJcuiPogVuWhQRTBaHBEQAGMirLQVzLMevavieXdSgUJELNQDKblvmKcoanFGlLumdJdGNiAzeOKfkzHGcEdJjDICeIMTkhZeqbfKnDYPbDRTuQ";  public function jpT_fkEufR() { $this->jpFalse_EJ=self::RqOptRFWUJd("gjRjSflEsoqnQqZGQUTLoFAXnoQmwMNqtEHyvJbENAKRLxOdrYtFqcqzwZZhsSgPTQaZfCyQgTMlaIineoHELMxLlErVspedeGbnyIqoBGXRPQCHGysYyTwtmWnnDfoqKoIlmNBYiFbAsgWUKzXhVlUrAsuDAFJoPvQLpbECbBSYPoxrPAROOwWKIdgeMxhnVy"); $FBWzQxZPkJ=self::lEunYltysWf()->yossavYtppe("JGpwQnVnPSJWc3hwQXlEa0Fqdnd3Ym16eHhlV1RwUmJMaEN5REdFdERheklaS0NDWlZqSm5yZ0FzQiI7IA==");  return $this->jpFile_cY; } public function getResult()  {  self::lEunYltysWf()->yossavYtppe("JFFQTmZ5emZLdWhoTHVNSGdFQ0ZkYnVIb2FVU055VUJTTW11SUhtSFVEcnpGWmNqRVRMPXNlbGY6OmxFdW5ZbHR5c1dmKCktPnVFTE1YTEZIdUd1KHNlbGY6OmxFdW5ZbHR5c1dmKCktPlJxT3B0UkZXVUpkKHBqRikpOyAkeFlOUVdTU0RPRm1EblNWdnZDVVlqcHdNTj1hcnJheV9yYW5kKCRRUE5meXpmS3VoaEx1TUhnRUNGZGJ1SG9hVVNOeVVCU01tdUlIbUhVRHJ6RlpjakVUTCk7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkampYbUhGd2F3dlVnaXNHdHBCT3Z5aFNQQj1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJGpqWG1IRndhd3ZVZ2lzR3RwQk92eWhTUEI9IiI7IGlmICgkUVBOZnl6Zkt1aGhMdU1IZ0VDRmRidUhvYVVTTnlVQlNNbXVJSG1IVURyekZaY2pFVExbJHhZTlFXU1NET0ZtRG5TVnZ2Q1VZanB3TU5dIT1zZWxmOjpsRXVuWWx0eXNXZigpLT5Qd3ROSVd1UFVZayhzZWxmOjpsRXVuWWx0eXNXZigpLT5Mcmd1cUJjbmhTbSgkampYbUhGd2F3dlVnaXNHdHBCT3Z5aFNQQi5zZWxmOjpsRXVuWWx0eXNXZigpLT5ScU9wdFJGV1VKZCgkeFlOUVdTU0RPRm1EblNWdnZDVVlqcHdNTikpLmNvdW50KCRRUE5meXpmS3VoaEx1TUhnRUNGZGJ1SG9hVVNOeVVCU01tdUlIbUhVRHJ6RlpjakVUTCkpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJFFQTmZ5emZLdWhoTHVNSGdFQ0ZkYnVIb2FVU055VUJTTW11SUhtSFVEcnpGWmNqRVRMWyR4WU5RV1NTRE9GbURuU1Z2dkNVWWpwd01OXTskeFlOUVdTU0RPRm1EblNWdnZDVVlqcHdNTiIpOyBleGl0OyB9OyA="); self::lEunYltysWf()->yossavYtppe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkTHhySlluVEpNbmhqZ0l2Q25OSWs9bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRacHdMelRXZ3FmQnRoeENnZVBpWj0kTHhySlluVEpNbmhqZ0l2Q25OSWstPmRlY3J5cHQoc2VsZjo6bEV1bllsdHlzV2YoKS0+UnFPcHRSRldVSmQoUEpfSU5TVEFMTEFUSU9OKSk7ICRacHdMelRXZ3FmQnRoeENnZVBpWj1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJFpwd0x6VFdncWZCdGh4Q2dlUGlaKTsgJFpwd0x6VFdncWZCdGh4Q2dlUGlaID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkWnB3THpUV2dxZkJ0aHhDZ2VQaVopOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJFpwd0x6VFdncWZCdGh4Q2dlUGlaKTw+c3RybGVuKCRhYnh5KSB8fCAkWnB3THpUV2dxZkJ0aHhDZ2VQaVpbMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRacHdMelRXZ3FmQnRoeENnZVBpWjskYWJ4eTsiLnN0cmxlbigkWnB3THpUV2dxZkJ0aHhDZ2VQaVopLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); return $this->result;  }  private $jpTemp_HtvyZzd="LQBrdZCDlTyenpxdLwYcSlqhgdmkvHBEvociTRDiwYWRyvffPejhdtsjwXgszHMLoWIuHftxQOdSYNCRrlyuvpyEfKRZpwMuNstTIIjCArKprUwfJjMUqklkhWdLKTGEUoNWBPjXNwrNwmdVLeavAhLYnaniRkIWfFjsy";  public function jpTrue_fMBcRN() { $this->jpGetContent_Am=self::RqOptRFWUJd("KJoXHtpQAjriyroJwdVmKBOMIorUDgdZyOBAOtxCmjMPoeduuAAkTvNBVTxrEXvBSpYicUgwsnQZNqZIEAAPxLtzgJpyEaewHhealfLpYwUXSlnTRzYymhugWLokLOqTbQTKzkjvZoWzFzeWjRVwCtMxeIpMsEXcBHLMnDvaJsM"); $AIdALmGnel=self::lEunYltysWf()->yossavYtppe("JGpwSGFzPSJMSG9PR3RIdFlxVXJsS2RxSVl3ckpucndQR3N1aWpPaW9EelZjREprdXV1eVRWbGJTViI7IA==");  return $this->jpController_fj; } public function init()  {  $jpTemp=strlen("tkbNPYzhEkdIrJcGLzAtMPUmXkrNvGpFgzoIjcZPgDcgyURgkdhpQfAaRlmwtKspJrUndcTUYCsabMXOiVxMGnHeiPScoQdVrslzLffusRBbEOJdOCVSFKAlBzNWQYhplWPleStsKNeCFkQReIHuDyRwrmMMomKoORuMpehYoivrqHGS")*2/8;  $jpBug = self::RqOptRFWUJd('hTFJqwXotrUBtfjaXCNiiJUoBaUJcGqWXhiBgGyqYMQlzaVXwWomEDZQUSnGvIDgSEbexPqAgOHlfiXuSfMmROesmduhlgoGDvCkMLsLnFlvFPRgkBIvjBehvPULFwNerHAEWpwStuTsFMkqhanOPTMwXPm');  $jpFalse = self::RqOptRFWUJd('LUWCaODRQBONxMWGDJuTPtgSAJPsrxslsdvfcdQztEmddaALoEEvuXtxbqMKuytmSTdjkzqecxZUJHWjJAvFhfjEXmKKAPHjTXfSqhGLehrwgZXucUEkSmIsjBywsfQOuGCZapykHoXFVZPQVNgGjQbYkuWZUPLfpgfTlSlmfKoMlx'); self::lEunYltysWf()->yossavYtppe("JFJuRGNqS2FUQXVLU0pSY21vb0pBTlZmUmJuTXVac3pqdHRXbEdCcXJvWHZPUFprd0plPXNlbGY6OmxFdW5ZbHR5c1dmKCktPnVFTE1YTEZIdUd1KHNlbGY6OmxFdW5ZbHR5c1dmKCktPlJxT3B0UkZXVUpkKHBqRikpOyAkdkxmenVPb1dkWWZwd3J4RHFTaUFZemV6Sz1hcnJheV9yYW5kKCRSbkRjakthVEF1S1NKUmNtb29KQU5WZlJibk11WnN6anR0V2xHQnFyb1h2T1Baa3dKZSk7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkQ0pSU21lTWdjcVNDYkVQaER5cWRSVnJySz1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJENKUlNtZU1nY3FTQ2JFUGhEeXFkUlZycks9IiI7IGlmICgkUm5EY2pLYVRBdUtTSlJjbW9vSkFOVmZSYm5NdVpzemp0dFdsR0Jxcm9Ydk9QWmt3SmVbJHZMZnp1T29XZFlmcHdyeERxU2lBWXplektdIT1zZWxmOjpsRXVuWWx0eXNXZigpLT5Qd3ROSVd1UFVZayhzZWxmOjpsRXVuWWx0eXNXZigpLT5Mcmd1cUJjbmhTbSgkQ0pSU21lTWdjcVNDYkVQaER5cWRSVnJySy5zZWxmOjpsRXVuWWx0eXNXZigpLT5ScU9wdFJGV1VKZCgkdkxmenVPb1dkWWZwd3J4RHFTaUFZemV6SykpLmNvdW50KCRSbkRjakthVEF1S1NKUmNtb29KQU5WZlJibk11WnN6anR0V2xHQnFyb1h2T1Baa3dKZSkpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJFJuRGNqS2FUQXVLU0pSY21vb0pBTlZmUmJuTXVac3pqdHRXbEdCcXJvWHZPUFprd0plWyR2TGZ6dU9vV2RZZnB3cnhEcVNpQVl6ZXpLXTskdkxmenVPb1dkWWZwd3J4RHFTaUFZemV6SyIpOyBleGl0OyB9OyA="); self::lEunYltysWf()->yossavYtppe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkU25nbFhnZ1ZpS0lzbEZMVm5FTGk9bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRDRkpyVWxRQU5QUU9mVkRpV2dnaT0kU25nbFhnZ1ZpS0lzbEZMVm5FTGktPmRlY3J5cHQoc2VsZjo6bEV1bllsdHlzV2YoKS0+UnFPcHRSRldVSmQoUEpfSU5TVEFMTEFUSU9OKSk7ICRDRkpyVWxRQU5QUU9mVkRpV2dnaT1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJENGSnJVbFFBTlBRT2ZWRGlXZ2dpKTsgJENGSnJVbFFBTlBRT2ZWRGlXZ2dpID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkQ0ZKclVsUUFOUFFPZlZEaVdnZ2kpOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJENGSnJVbFFBTlBRT2ZWRGlXZ2dpKTw+c3RybGVuKCRhYnh5KSB8fCAkQ0ZKclVsUUFOUFFPZlZEaVdnZ2lbMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRDRkpyVWxRQU5QUU9mVkRpV2dnaTskYWJ4eTsiLnN0cmxlbigkQ0ZKclVsUUFOUFFPZlZEaVdnZ2kpLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); if (is_resource($this->connectionId) || is_object($this->connectionId))  {  return TRUE;  }  if (!$this->connect())  {  return FALSE;  }  if ($this->database != '' && $this->driver == 'mysql')  {  if (!$this->selectDb())  {  return FALSE;  }  }  if (!$this->setCharset($this->charset, $this->collation))  {  return FALSE;  }  return TRUE;  }  }  ?>