# B24APIDriver
Driver for B24 REST API.

Bitrix24 REST API tutorial: https://dev.1c-bitrix.ru/rest_help/

Example use this driver:

require_once 'B24API.php';

class SomeClass extends B24API {
  function __construct($domain, $adminId, $tokenIn) {
    parent::__construct($domain, $adminId, $tokenIn);
  }

}

@new SomeClass($_REQUEST['domain'], $_REQUEST['adminId'], $_REQUEST['tokenIn']);
