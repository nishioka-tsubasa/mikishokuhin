;(function ($) {
    // 入力画面: 住所自動入力用
    $('.mw_wp_form_input form').addClass('h-adr');

    // 確認画面: 入力画面専用の案内を非表示
    if ($('.mw_wp_form_confirm').length) {
        $('.contact-form-wrap').hide();
        $('.contact-form-privacy').hide();
        $('.contact-form-check').hide();
    }

    // 入力画面: 流入元の選択内容に合わせて補足文を切り替える
    function updateReferDetailLabel() {
        var isOther = $('#refer-site-3').prop('checked');

        $('#refer-detail-text1').toggle(!isOther);
        $('#refer-detail-text2').toggle(isOther);
    }

    updateReferDetailLabel();
    $('#refer-radio input').on('change', updateReferDetailLabel);

    // 入力画面: 入力に1つでも不備があれば表示
    $('#contact-mwwpform-block-error-index:has(.error)').show();

    // 入力画面: サンプル希望 チェック分岐 (ページ読み込み時)
    if ( $('tr#contact-mwwpform-block-request input#request_check-1').is(':checked') ) {
        $('tr#contact-mwwpform-block-sample').css('display','table-row');
    } else {
        $('tr#contact-mwwpform-block-sample').hide();
        // チェックがない場合は"サンプルの種類"選択をリセット
        $('#contact-mwwpform-block-sample span input').prop('checked', false);
    }

    // 入力画面: サンプル希望 チェック分岐 (チェックボックスクリック時)
    $('tr#contact-mwwpform-block-request input#request_check-1').change(function() {
        if ( $(this).is(':checked') ) {
            $('tr#contact-mwwpform-block-sample').css('display','table-row');
        } else {
            $('tr#contact-mwwpform-block-sample').hide();
            $('#contact-mwwpform-block-sample span input').prop('checked', false);
        }
    });

    // 入力画面: サンプル希望 チェックボックス個数制限
    $('tr#contact-mwwpform-block-sample input[type=checkbox]').click(function() {
        var $count = $("tr#contact-mwwpform-block-sample input[type=checkbox]:checked").length;
        var $not = $('tr#contact-mwwpform-block-sample input[type=checkbox]').not(':checked');
     
        //5つまでに制限
        if($count >= 5) {
            $not.attr("disabled",true);
        }else{
            $not.attr("disabled",false);
        }
    });

    // 確認画面: サンプル希望がある場合は表示
    $('tr#contact-mwwpform-block-sample:has(input[name="sample_check[data]"])').css('display','table-row');
})(jQuery);
