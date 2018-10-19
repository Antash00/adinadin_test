<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
  use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;
class CustomComponent extends  CBitrixComponent
{
  public function  executeComponent()
  {

    if(isset($_POST['submit']))
    {
      $this->getAjax($_POST);
    }
    $this->getElement();
    $this->includeComponentTemplate();

  }
  public function getElement()
  {
    $arFilter = Array(
   "IBLOCK_ID"=>$this->arParams["IBLOCK_ID"],
   "ACTIVE"=>"Y"
   );
  $arSelect = array('*','CATALOG_GROUP_1');
  $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
  while($ar_fields = $res->GetNextElement())
    {
     $this->arResult["ITEMS"][] = $ar_fields->GetFields();
    }
  return $this->arResult;
  }

  public function getAjax($POST)
  {
    global $USER;
    $siteId = Context::getCurrent()->getSite();
    $currencyCode = CurrencyManager::getBaseCurrency();
    if (empty($POST['email']))
    {
      $arResult["ERRORS"][] ="Не введена почта";
    }
    else if (empty($POST['name']))
    {
      $arResult["ERRORS"][] ="Не введено имя";
    }
    else if (empty($POST['count']) )
    {
      $arResult["ERRORS"][] ="НКоличество не может быть отрицательным";
    }
    if ($USER->IsAuthorized())
    {
      $user_id=$USER->GetID();
    }
    else
    {
      $user_id=1;
    }
    $order = Order::create($siteId,$user_id);
    $order->setPersonTypeId($user_id);
    $order->setField('CURRENCY', $currencyCode);
    $basket = Basket::create($siteId);
    $product=explode($POST['product']," ");
    $item = $basket->createItem('catalog',$POST['product']);
    $item->setFields(array(
        'QUANTITY' => $POST['count'],
        'CURRENCY' => $currencyCode,
        'LID' => $siteId,
        'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
    ));
    $order->setBasket($basket);
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
    $shipment->setFields(array(
        'DELIVERY_ID' => $service['ID'],
        'DELIVERY_NAME' => $service['NAME'],
    ));
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    $shipmentItem = $shipmentItemCollection->createItem($item);
    $shipmentItem->setQuantity($item->getQuantity());
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection->createItem();
    $paySystemService = PaySystem\Manager::getObjectById(1);
    $payment->setFields(array(
        'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
        'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
    ));
    $order->doFinalAction(true);
    $result = $order->save();
    if (!$result->isSuccess())
       {
           $this->arResult["ERRORS"][]=$result->getErrors();
       }
       else {
    $orderId = $order->getId();
    $arSendFields = array(
      "ORDER_ID"=>$orderId,
      "PRODUCT_NAME"=>$product[1],
      "QUANTITY"=>$POST['count'],
      "USER_NAME"=>$POST['name'],
      "EMAIL"=>$POST["email"]
    );
    if (CEvent::Send($this->arParams["SEND_TYPE"], SITE_ID, $arSendFields))
    {
    $this->arResult["SUCCESS"]="Поздравляем! Ваша заявка принята. В ближайшее время с Вами свяжется наш менеджер";
    }
  }
  }
};
?>
