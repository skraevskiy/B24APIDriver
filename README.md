# Driver for B24 REST API

Bitrix24 CRM REST API Tutorial: https://dev.1c-bitrix.ru/rest_help/

<b>Example use this driver:</b>

<pre>require_once 'B24API.php';

class SomeClass extends B24API {
  function __construct($domain, $adminId, $tokenIn) {
    parent::__construct($domain, $adminId, $tokenIn);
  }

}

@new SomeClass($_REQUEST['domain'], $_REQUEST['adminId'], $_REQUEST['tokenIn']);</pre>
