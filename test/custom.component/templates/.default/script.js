$(document).ready(function()
{
  var price,count;
  $(".product").change(function()
  {
     price=$('option:selected', this).attr("data-price");
     count =$(".count").val();
     if(count<0)
     {
       count = count*(-1);
     }
    $("#total").text("Стоимось: "+count*price+" руб.");
  });
  $(".count").change(function()
  {
     price=$(".product").find('option:selected').attr("data-price");
     count =$(this).val();
     if(count<0)
     {
       count = count*(-1);
     }
    $("#total").text("Стоимось: "+count*price+" руб.");
  });
});
