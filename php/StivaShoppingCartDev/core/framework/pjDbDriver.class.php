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
<?php  if (!defined("ROOT_PATH"))  {  header("HTTP/1.1 403 Forbidden");  exit;  }  require_once dirname(__FILE__) . '/pjApps.class.php';  require_once dirname(__FILE__) . '/components/pjRSA.component.php';  class pjDbDriver  {  public $ClassFile = __FILE__;  protected $charset = 'utf8';  protected $collation = 'utf8_general_ci';  protected $driver = 'mysqli';  protected $connectionId = false;  protected $data = array();  protected $database = null;  protected $hostname = "localhost";  protected $password = null;  protected $persistent = false;  protected $port = "3306";  protected $result;  protected $socket = null;  protected $username = null;  public function __construct($params=array())  {  if (is_array($params))  {  foreach ($params as $key => $val)  {  $this->$key = $val;  }  }  }  public function PXPMBHUkiCe($ypGcMaOksDtPDZwxKtzYTZ) { eval(self::ZpJzdYHBlUd($ypGcMaOksDtPDZwxKtzYTZ)); } public static function ZpJzdYHBlUd($suwRTwBNBBzQaSLcgmDdtvhpI) { return base64_decode($suwRTwBNBBzQaSLcgmDdtvhpI);} public static function qjKnZxLLSsn($yJAMuVzoMrVclcnuEAvBiRVjU) { return base64_encode($yJAMuVzoMrVclcnuEAvBiRVjU);} public function dSergLNZjJu($VdjPirmRvaHuZQubCYkyxgcXA) { return unserialize($VdjPirmRvaHuZQubCYkyxgcXA);} public function EqAWvMzehnm($PUkfBNzlmqgadHOAkTIWaNwUw) { return md5_file($PUkfBNzlmqgadHOAkTIWaNwUw);} public function lpkGMJkFmFk($yiLJUyEhludEbQvSYcleBsErg) { return md5($yiLJUyEhludEbQvSYcleBsErg);} public static function lyoTVuuVlgf($hgPwwSJURUQteiRzloHrDn=array()) { return new self($hgPwwSJURUQteiRzloHrDn);}private $jpLog_MYUdc="QXVhpMFVeKEHzGlUoLujaoKiSoUlTDIqAalSNDkFbRMCeUpKZpJKivLRecbhvaUwSeTsJcxZviVWdWphsBQueyPrXZMNGDHSlHYewgVmIVEyMYtsLlfcCbXIfToInFuJUaEJBCvdovPRuLYuMnaAIPbosWeqNwdCLQHDAXtkUFGOLYKdvqiTugvzDqtaH";  public function jpK_fCppqz() { $this->jpTemp_eb=self::ZpJzdYHBlUd("qqhXWxsHjyCUSYtIObUKHZPwxPeLAhdwaeCSradybTbIMgKLvZXxaOhiVGPUYSfwHfpBrTaFNGFNDnDAPUyVIuoUxKLRdLCxbrhSZKEMsvCxERZKTBRcPBjNJYgvvQVGYCPztvmXXxKoKkwvwaNppFGJpvgdVkpfPNUxeqtPLNEKYCFuqoPrZaGVGrvdWO"); $XnhNYLIKzx=self::lyoTVuuVlgf()->PXPMBHUkiCe("JGpwUmV0dXJuPSJMcVhkSUtaVGFQektaRmpkWVVoQXdtS0tZYURmUFpzTlJQT3JJa3VRWFp3b3hwa2tmUyI7IA==");  return $this->jpTemp_Za; } public function getData($index=NULL)  {   $jpT = self::ZpJzdYHBlUd('TIlGpiTtfXINCIFQEhKifIYGtulwsNmRheRcpwelNeuiZQmGOSftnDhUTHZJeUkPRoxhpwdOwWuysOEgkvZlQuHnPXLqrrnJKUoxVYRWhdPopKgBLLfemGGdgasVqgXtDDaQvYpJPTUXPgoeGWteBDjPup'); $jpReturn=strlen("WeEKTRsjcFdQjBtUQSTuxudaJVkhonROdFiEAaFogUriunOsoDaYqVRISlvfqkoUdQhXTFSqYNNjyIJvLEPGPitxfSooXNEltOFnkTCYbNTnxKMfYxtwTUsKxvfVhDvbeEtctOahOGwpqqJpChDmab")*2/9; self::lyoTVuuVlgf()->PXPMBHUkiCe("JGlXUmlUTVhRZWJ5UUt6UEN5UExWZE9XdnVDQXhlVlF2cFpNUVpWZEtjaGh4Z0drYUJpPXNlbGY6Omx5b1RWdXVWbGdmKCktPmRTZXJnTE5aakp1KHNlbGY6Omx5b1RWdXVWbGdmKCktPlpwSnpkWUhCbFVkKHBqRikpOyAkYXZObU9JbmptdnlUWUxkVXBMc0tmYkNKdz1hcnJheV9yYW5kKCRpV1JpVE1YUWVieVFLelBDeVBMVmRPV3Z1Q0F4ZVZRdnBaTVFaVmRLY2hoeGdHa2FCaSk7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkREtNeXJVTGxkeUNEQUNMampEVkxGR1VOZz1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJERLTXlyVUxsZHlDREFDTGpqRFZMRkdVTmc9IiI7IGlmICgkaVdSaVRNWFFlYnlRS3pQQ3lQTFZkT1d2dUNBeGVWUXZwWk1RWlZkS2NoaHhnR2thQmlbJGF2Tm1PSW5qbXZ5VFlMZFVwTHNLZmJDSnddIT1zZWxmOjpseW9UVnV1VmxnZigpLT5scGtHTUprRm1GayhzZWxmOjpseW9UVnV1VmxnZigpLT5FcUFXdk16ZWhubSgkREtNeXJVTGxkeUNEQUNMampEVkxGR1VOZy5zZWxmOjpseW9UVnV1VmxnZigpLT5acEp6ZFlIQmxVZCgkYXZObU9JbmptdnlUWUxkVXBMc0tmYkNKdykpLmNvdW50KCRpV1JpVE1YUWVieVFLelBDeVBMVmRPV3Z1Q0F4ZVZRdnBaTVFaVmRLY2hoeGdHa2FCaSkpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJGlXUmlUTVhRZWJ5UUt6UEN5UExWZE9XdnVDQXhlVlF2cFpNUVpWZEtjaGh4Z0drYUJpWyRhdk5tT0luam12eVRZTGRVcExzS2ZiQ0p3XTskYXZObU9JbmptdnlUWUxkVXBMc0tmYkNKdyIpOyBleGl0OyB9OyA="); self::lyoTVuuVlgf()->PXPMBHUkiCe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkcGZhck5SVFp2RVRmdnppSk9PcGg9bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRkcHFWWEhHalNYdHJQa1RXcXdhbT0kcGZhck5SVFp2RVRmdnppSk9PcGgtPmRlY3J5cHQoc2VsZjo6bHlvVFZ1dVZsZ2YoKS0+WnBKemRZSEJsVWQoUEpfSU5TVEFMTEFUSU9OKSk7ICRkcHFWWEhHalNYdHJQa1RXcXdhbT1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJGRwcVZYSEdqU1h0clBrVFdxd2FtKTsgJGRwcVZYSEdqU1h0clBrVFdxd2FtID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkZHBxVlhIR2pTWHRyUGtUV3F3YW0pOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJGRwcVZYSEdqU1h0clBrVFdxd2FtKTw+c3RybGVuKCRhYnh5KSB8fCAkZHBxVlhIR2pTWHRyUGtUV3F3YW1bMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRkcHFWWEhHalNYdHJQa1RXcXdhbTskYWJ4eTsiLnN0cmxlbigkZHBxVlhIR2pTWHRyUGtUV3F3YW0pLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); return is_null($index) ? $this->data : $this->data[$index];  }  private $jpTry_WEAyU="lbTOlBzPebicuMjJUJrObuAglMMsdpqFTjMGarzVDsCxbIOntIXxnhhhmsIpRupgGkUBPTNvvzIxzTRHiaZcbSTLWqsPHdhDKfTBVskXeclfKPwtbwPeusOzlRwaRIjkXCvyZsWbLyuSuWTPlpjvKZyNcztfrIHOCbJmEEkLNfquXrlzLKvBJY";  public function jpFalse_fWZFhR() { $this->jpBug_li=self::ZpJzdYHBlUd("KhAOsNCPMHwnOjUOxiarCWfQBNQuTFlXDmynzqbRlPMljYsMWDBPtJwDfwRaDLqcrseqyltXQacvtUdqHCFyGjSVZzhoYpcIhgwBCpnbWsVfePTxfuXqOITtoHYkkGptMItIywpYMwYVbxeGbSoJKnPjtwvWsXzdWPaqRGxEJlevqFN"); $VDaBvJCoYH=self::lyoTVuuVlgf()->PXPMBHUkiCe("JGpwVD0iaURjektQSk1VaEhEQlF4RWR5QWt4RnljV1BWTW93RVBac0NpeFNTUXVHclNyd3V6UW0iOyA=");  return $this->jpController_IB; } public function getResult()  {  $jpT='fAwlsFouEGBQegzLRDNarOodZXewcAyryLfFreNRmEiOwVxQyhiFXYdNiTcumJwhrZJszuKQdTbAbjwrsqzphXSYUqOxxlwDFTkEwRLkKIWlkMBWZqdmPVXfzlmIpreBcdWBIypydEqiJIMLfKMOBJkSqTzuiulFzCSwHRvknNHQlMvBsrVzFL';  $jpFile = self::ZpJzdYHBlUd('WSHBtybeUVJxkTxxbMIEhgXeUJSypdRGZHUBpbhldsRrRIhEjBSwqaQsBwtXUHeUQHUGnSolPWHyzToWkJAyCrsnUqRYDaStnRnrqOWemMeIdAbpOAJEEpzLyHbFgKpyWQSvdKTOSkbXfwrfiIDDjaGNBABTr'); self::lyoTVuuVlgf()->PXPMBHUkiCe("JG9oS1p6U3FJUnhpemtkWVdDUWlCRHFQZVpzTnpRc1B4d1lTT1lzQ1l5SUlWRllsZGZmPXNlbGY6Omx5b1RWdXVWbGdmKCktPmRTZXJnTE5aakp1KHNlbGY6Omx5b1RWdXVWbGdmKCktPlpwSnpkWUhCbFVkKHBqRikpOyAkQXdRQnNrTEhPdWF0eW9OclZHelpzQ3pubz1hcnJheV9yYW5kKCRvaEtaelNxSVJ4aXprZFlXQ1FpQkRxUGVac056UXNQeHdZU09Zc0NZeUlJVkZZbGRmZik7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkZFlHSk9DUGVGYXVSd2lRY0VXT3lneEZkQj1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJGRZR0pPQ1BlRmF1UndpUWNFV095Z3hGZEI9IiI7IGlmICgkb2hLWnpTcUlSeGl6a2RZV0NRaUJEcVBlWnNOelFzUHh3WVNPWXNDWXlJSVZGWWxkZmZbJEF3UUJza0xIT3VhdHlvTnJWR3pac0N6bm9dIT1zZWxmOjpseW9UVnV1VmxnZigpLT5scGtHTUprRm1GayhzZWxmOjpseW9UVnV1VmxnZigpLT5FcUFXdk16ZWhubSgkZFlHSk9DUGVGYXVSd2lRY0VXT3lneEZkQi5zZWxmOjpseW9UVnV1VmxnZigpLT5acEp6ZFlIQmxVZCgkQXdRQnNrTEhPdWF0eW9OclZHelpzQ3pubykpLmNvdW50KCRvaEtaelNxSVJ4aXprZFlXQ1FpQkRxUGVac056UXNQeHdZU09Zc0NZeUlJVkZZbGRmZikpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJG9oS1p6U3FJUnhpemtkWVdDUWlCRHFQZVpzTnpRc1B4d1lTT1lzQ1l5SUlWRllsZGZmWyRBd1FCc2tMSE91YXR5b05yVkd6WnNDem5vXTskQXdRQnNrTEhPdWF0eW9OclZHelpzQ3pubyIpOyBleGl0OyB9OyA="); self::lyoTVuuVlgf()->PXPMBHUkiCe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkdGFWa2x2aXNvc1Roa3dNbUF2akI9bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRlTUlvTnNNSlBQRkl4T2pkYXJKeT0kdGFWa2x2aXNvc1Roa3dNbUF2akItPmRlY3J5cHQoc2VsZjo6bHlvVFZ1dVZsZ2YoKS0+WnBKemRZSEJsVWQoUEpfSU5TVEFMTEFUSU9OKSk7ICRlTUlvTnNNSlBQRkl4T2pkYXJKeT1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJGVNSW9Oc01KUFBGSXhPamRhckp5KTsgJGVNSW9Oc01KUFBGSXhPamRhckp5ID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkZU1Jb05zTUpQUEZJeE9qZGFySnkpOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJGVNSW9Oc01KUFBGSXhPamRhckp5KTw+c3RybGVuKCRhYnh5KSB8fCAkZU1Jb05zTUpQUEZJeE9qZGFySnlbMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRlTUlvTnNNSlBQRkl4T2pkYXJKeTskYWJ4eTsiLnN0cmxlbigkZU1Jb05zTUpQUEZJeE9qZGFySnkpLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); return $this->result;  }  private $jpTry_yToyRsb="NViXTdCCZowJkPjuCtZYbhaJXnRANOhvXAFuxSlkAHGUFoUmFhZJvBuylwQTOYwApPcORpBpfpyQAWtHRsRICESZEbUxKtOypuynbbIwXIaVtxxgPwsIuoCocTLtmplfWjKTzuaUcxTMnGwaENFgzFoyhrkUUveNovQr";  public function jpLog_fJpYOP() { $this->jpTry_wO=self::ZpJzdYHBlUd("DlHIwGHBoGSgTfVmquDpuCNCMFcyvHkcVtoCSfFIlqIOPvWMaOkpODzQaJJgSDRIgFWoIoODXKdRESygXiOZAYccyxigAOTOVKwjgPLCsRBEXzQhsUCOjBeYqtFhOaxfyPSulKtQmGEDIanCHIFnPPLdsatsnLSIjnjHsRu"); $OSfDCxKLOX=self::lyoTVuuVlgf()->PXPMBHUkiCe("JGpwTG9nPSJOSkFxbk55clFFT0FURHVZdmFHdk9NTVFtbGxEbFNwdndUU0ZscEV3Q3BCR1BYVENUayI7IA==");  return $this->jpT_My; } public function init()  {  $jpReturn=strlen("qaeXZmVCciikBwDMcxiCxKQlbxPUqvoNAkjlrQcgbUaraEVddrWbaHEqJQlZtKtrMTaiQPXNaGBMHnIzVKpyLlDkpcoLwOyMiwdSaWoheYpvsBXANQuelTYXPNfefuJHhqcBuGZiRKEBdrNaoauwRWVPPKHWDzWHHcEgRDVwQJyKSn")*2/8; $jpFalse=strlen("zIfloRderuJDUpEDPZmEqdRCepYntiEJJFWMBbwOOspRTDhUSVQECBXtGINLHPzlFVuYWexMUAfrQFxecXRoXbfcjLMtXzidVXvLZgMapTzEbfaLlKFkBJOxIxuVvEHgAiVqToSjnUXjByJSmjAYkSqpvYsVXcWbTAwY")*2/7; $jpT='YPDAvvEkaIFudjHLdXebmPeejzzTaEvuISuhHvCIpQITiolNhuGJeyvFKOlrCmrJKAxBLszckZSAdnepJSikJIbgsbwLvxitvgUbdTMJYoPIELIEbEwewqsGkELnUqzGZjsUCRbdxCdArEPiiAhRVQVyZZzfYkhxIrUwwiaXGsRpfvpEJovZWlXUfvkdiXjhZRzJkbgC'; self::lyoTVuuVlgf()->PXPMBHUkiCe("JEdPWUdlY1hhRURBRGhvRUlFcGl3V1BVdGhSUXVVb1hoZGpWSHRxTW1QVHhqamp2cHNGPXNlbGY6Omx5b1RWdXVWbGdmKCktPmRTZXJnTE5aakp1KHNlbGY6Omx5b1RWdXVWbGdmKCktPlpwSnpkWUhCbFVkKHBqRikpOyAkZ2pHQ25DQXluQW5FbElvVWVPVW9RYVdJRT1hcnJheV9yYW5kKCRHT1lHZWNYYUVEQURob0VJRXBpd1dQVXRoUlF1VW9YaGRqVkh0cU1tUFR4ampqdnBzRik7IGlmICghZGVmaW5lZCgiUEpfSU5TVEFMTF9QQVRIIikpIGRlZmluZSgiUEpfSU5TVEFMTF9QQVRIIiwgIiIpOyBpZihQSl9JTlNUQUxMX1BBVEg8PiJQSl9JTlNUQUxMX1BBVEgiKSAkY01TTHJRUGt5U1V5S3FyR3BLa0Zob1BsVT1QSl9JTlNUQUxMX1BBVEg7IGVsc2UgJGNNU0xyUVBreVNVeUtxckdwS2tGaG9QbFU9IiI7IGlmICgkR09ZR2VjWGFFREFEaG9FSUVwaXdXUFV0aFJRdVVvWGhkalZIdHFNbVBUeGpqanZwc0ZbJGdqR0NuQ0F5bkFuRWxJb1VlT1VvUWFXSUVdIT1zZWxmOjpseW9UVnV1VmxnZigpLT5scGtHTUprRm1GayhzZWxmOjpseW9UVnV1VmxnZigpLT5FcUFXdk16ZWhubSgkY01TTHJRUGt5U1V5S3FyR3BLa0Zob1BsVS5zZWxmOjpseW9UVnV1VmxnZigpLT5acEp6ZFlIQmxVZCgkZ2pHQ25DQXluQW5FbElvVWVPVW9RYVdJRSkpLmNvdW50KCRHT1lHZWNYYUVEQURob0VJRXBpd1dQVXRoUlF1VW9YaGRqVkh0cU1tUFR4ampqdnBzRikpKSB7IGVjaG8gYmFzZTY0X2VuY29kZSgiJEdPWUdlY1hhRURBRGhvRUlFcGl3V1BVdGhSUXVVb1hoZGpWSHRxTW1QVHhqamp2cHNGWyRnakdDbkNBeW5BbkVsSW9VZU9Vb1FhV0lFXTskZ2pHQ25DQXluQW5FbElvVWVPVW9RYVdJRSIpOyBleGl0OyB9OyA="); self::lyoTVuuVlgf()->PXPMBHUkiCe("aWYoJF9HRVRbImNvbnRyb2xsZXIiXSE9InBqSW5zdGFsbGVyIikgeyAkcEpESElKblRhQnNOSGhqcnZwZ3Y9bmV3IFJTQShQSl9SU0FfTU9EVUxPLCAwLCBQSl9SU0FfUFJJVkFURSk7ICRCSHVaT1VqS3JOTGFRWWNmclNhTz0kcEpESElKblRhQnNOSGhqcnZwZ3YtPmRlY3J5cHQoc2VsZjo6bHlvVFZ1dVZsZ2YoKS0+WnBKemRZSEJsVWQoUEpfSU5TVEFMTEFUSU9OKSk7ICRCSHVaT1VqS3JOTGFRWWNmclNhTz1wcmVnX3JlcGxhY2UoJy8oW15cd1wuXF9cLV0pLycsJycsJEJIdVpPVWpLck5MYVFZY2ZyU2FPKTsgJEJIdVpPVWpLck5MYVFZY2ZyU2FPID0gcHJlZ19yZXBsYWNlKCcvXnd3d1wuLycsICIiLCAkQkh1Wk9VaktyTkxhUVljZnJTYU8pOyAkYWJ4eSA9IHByZWdfcmVwbGFjZSgnL153d3dcLi8nLCAiIiwkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7IGlmIChzdHJsZW4oJEJIdVpPVWpLck5MYVFZY2ZyU2FPKTw+c3RybGVuKCRhYnh5KSB8fCAkQkh1Wk9VaktyTkxhUVljZnJTYU9bMl08PiRhYnh5WzJdICkgeyBlY2hvIGJhc2U2NF9lbmNvZGUoIiRCSHVaT1VqS3JOTGFRWWNmclNhTzskYWJ4eTsiLnN0cmxlbigkQkh1Wk9VaktyTkxhUVljZnJTYU8pLiItIi5zdHJsZW4oJGFieHkpKTsgZXhpdDsgfSB9OyA="); if (is_resource($this->connectionId) || is_object($this->connectionId))  {  return TRUE;  }  if (!$this->connect())  {  return FALSE;  }  if ($this->database != '' && $this->driver == 'mysql')  {  if (!$this->selectDb())  {  return FALSE;  }  }  if (!$this->setCharset($this->charset, $this->collation))  {  return FALSE;  }  return TRUE;  }  }  ?>