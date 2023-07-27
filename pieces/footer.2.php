<?php
global $companyInfo, $landings_default_domain, $default_main_site;
global $prepaid_info_html;
$doclinks = isset($doclinks) ? $doclinks : true;
$pplink = isset($pplink) ? $pplink : true;
if (array_key_exists('pplink', $_GET) && $_GET['pplink'] == '0') {
  $pplink = false;
}
$linksPosition = isset($linksPosition) ? $linksPosition : 'column';
$policy = isset($policy) ? $policy : true;

$company_detail = isset($companyInfo['detail']) ? $companyInfo['detail'] : 'ООО "Интернет реклама" &nbsp; ИНН 8721147082 &nbsp; КПП 230211102 &nbsp;  ОГРН 5247716358181';
$company_address = isset($companyInfo['address']) ? $companyInfo['address'] : '115035, г. Москва, Большой грузинский переулок д. 2/2, к. А';
?>
<p>Toshkent shahar,Uchtepa tumani shirin ko'chasi</p>



