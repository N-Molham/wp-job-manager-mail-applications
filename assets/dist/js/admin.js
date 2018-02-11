/**
 * Created by Nabeel on 2016-02-02.
 */
!function(a,b,c){b(function(){var a=b("#mail-application-recipients").select2({width:"100%",tags:!0});b("#wpjm_ma_mail_application").on("click","button.button-primary",function(c){var d=b(c.currentTarget),e=b.map(a.select2("data"),function(a){return a.text});if(!e.length)return!0;var f=b.extend({},d.data(),{recipients:e});d.prop("disabled",!0).addClass("loading"),b.post(ajaxurl,f,function(a){alert(a.data)}).always(function(){d.prop("disabled",!1).removeClass("loading")})})})}(window,jQuery);