<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?if(isset($arResult["ERRORS"]))
{
  ?><div class="errors"><?
  foreach ($arResult["ERRORS"] as $error)
    {
      echo $error."<br>";
    }?>
    <div>
<?
}
  else if (isset($arResult["SUCCESS"]))
  {?>
  <div class="success"><?=$arResult["SUCCESS"]?></div>
  <?
  }
  else
  {
?>
<form class="order-form" method="POST" action="<?=POST_FORM_ACTION_URI?>">
<?=bitrix_sessid_post()?>
  <h2>Форма заказа</h2>
  <div class="order-block">
  <label>Товар</label>
  <select name="product" class="product">
  <?foreach($arResult["ITEMS"] as $arItem):?>
  <option value="<?echo $arItem["ID"]." ".$arItem["NAME"]?>" data-price="<?=$arItem['CATALOG_PRICE_1']?>">
    <?=$arItem['NAME']?>
  </option>
  <?endforeach?>
  </select>
  <label>Количество</label>
  <input type="number" class="count" name="count" min="1" value="1">
  <div id="total"></div>
  </div>
  <h2>Данные покупателя</h2>
  <div class="client-block">
  <label>Ваше имя</label>
  <input type="text" name="name" required>
  <label>Ваша почта</label>
  <input type="email" name="email" required>
  </div>
  <div class="sub">
  <input type="submit" class="submit" name="submit" value="Выполнить">
  </div>
</form>
<?}?>
