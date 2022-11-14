jQuery(document).ready(function() {
    //confirm-detailsページで支払い方法がCheckの場合銀行振込に変更
    target = jQuery(".pmpro_invoice-payment-method").children('p');
    if (target.text() == 'Check') target.text("銀行振込");
    target = jQuery(".pmpro_invoice_wrap").children('ul').children('li').eq(2);
    target.text(target.text().replace("Pending","ご入金確認中"));
});