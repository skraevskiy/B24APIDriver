# Driver for B24 REST API

Bitrix24 CRM REST API Tutorial: https://dev.1c-bitrix.ru/rest_help/

<b>Realized methods:</b>

task.item.add
task.item.getdata
task.item.update
task.checklistitem.add
task.dependence.add
im.message.add
lists.element.get
lists.element.update
lists.field.get
lists.field.update
department.get
user.get
crm.lead.add

<b>Example use this driver:</b>

<pre>require_once 'B24API.php';

class SomeClass extends B24API {
  function __construct($domain, $adminId, $tokenIn) {
    parent::__construct($domain, $adminId, $tokenIn);
  }

  function message($to, $msg) {
    if (self::b24_imMessageAdd($to, $msg) === false) return false;
    return self::b24_batch();
  }
}

$someObj = @new SomeClass($_REQUEST['domain'], $_REQUEST['adminId'], $_REQUEST['tokenIn']);
$someObj->message(1, 'Test message!');</pre>
