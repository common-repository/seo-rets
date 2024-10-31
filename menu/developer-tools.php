<?php
$sr = $seo_rets_plugin;
$blogUrl = get_bloginfo('url');
?>


<script type="text/javascript">

    var sr_popup;
    jQuery(function ($) {
        $("#tabgroup").tabs();
        var sitemap_message = $("#sitemap-message");
        var boot_mess = $("#boot_mess");
        var order_mess = $("#order_mess");
        var css_message = $("#css-message");
        var css_box = $("#edit-css");
        var facebook_appID = $("#facebook_appID");
        var google_appID = $("#Client_appID");
        var seo_message = $("#seo-message");
        var seo_message_list = $("#seo-message-list");
        var extrapage_message = $("#extrapage_message");
        var popup_message = $("#popup-message");
        var template_message = $("#template-message");
        var mailchimp_message = $("#mailchimp-message");
        var leadcapture_message = $("#leadcapture-message");
        var emailmethod_message = $("#emailmethod-message");
        var customform_message = $("#customform-message");
        var unfoundpage_message = $("#unfoundpage-message");
        var plugintext_message = $("#plugintext-message");
        var features_message = $("#features-message");
        var facebook_message = $("#facebook_appID-message");
        var google_message = $("#Client_appID-message");
        var crm_message = $("#crm_message");
        var oinw_message = $("#open_in_new_window_message");
        var boot_refine = $("#boot-refine-message");
        var boot_refine_sc = $("#bootstrap_refine_sc-message");
        var ucp = $("#use_custom_pagi_message");
        var fqm = $("#fast_query-message");

        $('#facebook_appID_submit').click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=fb-appid',
                type: "POST",
                data: {
                    appid: facebook_appID.val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        facebook_message.addClass('sr-success');
                    } else {
                        facebook_message.addClass('sr-fail');
                    }
                    facebook_message.show();
                    facebook_message.text(response.mes);
                    facebook_message.delay(800).fadeOut("slow");
                }
            });
        });
        $('#Client_appID_submit').click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=google-appid',
                type: "POST",
                data: {
                    appid: google_appID.val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        google_message.addClass('sr-success');
                    } else {
                        google_message.addClass('sr-fail');
                    }
                    google_message.show();
                    google_message.text(response.mes);
                    google_message.delay(800).fadeOut("slow");
                }
            });
        });

        jQuery('#template-related-properties').change(function () {
            jQuery('.extraRP').toggle();
        });

        $('#showCustomCheckBox').change(function () {
            if ($('#showCustomCheckBox')[0].checked) {
                $('#customHtmlForm').show();
                $('#email').attr('readonly', 'readonly');
                $('#title').attr('readonly', 'readonly');
                $('#sub').attr('readonly', 'readonly');
                $('#error').attr('readonly', 'readonly');
                $('#success').attr('readonly', 'readonly');
                $('#btn').attr('readonly', 'readonly');

            }
            else {
                $('#customHtmlForm').hide();
                $('#email').removeAttr('readonly');
                $('#title').removeAttr('readonly');
                $('#sub').removeAttr('readonly');
                $('#error').removeAttr('readonly');
                $('#success').removeAttr('readonly');
                $('#btn').removeAttr('readonly');
            }
        });
        $('#showCustomCheckBox').change();
        $("#generate-sitemap").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=submit-sitemap',
                success: function (response) {
                    if (response.error == 0) {
                        sitemap_message.addClass('sr-success');
                    } else {
                        sitemap_message.addClass('sr-fail');
                    }
                    sitemap_message.show();
                    sitemap_message.text(response.mes);
                    sitemap_message.delay(800).fadeOut("slow");
                }
            });
        });
        $("#edit-css-btn").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-css',
                type: "POST",
                data: {css: css_box.val()},
                success: function (response) {
                    if (response.error == 0) {
                        css_message.addClass('sr-success');
                    } else {
                        css_message.addClass('sr-fail');
                    }
                    css_message.show();
                    css_message.text(response.mes);
                    css_box.val(response.css);
                    css_message.delay(800).fadeOut("slow");
                }
            });
        });
        $("#seo-btn").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-seo',
                type: "POST",
                data: {
                    title: $("#seo-title").val(),
                    keywords: $("#seo-keywords").val(),
                    description: $("#seo-description").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        seo_message.addClass('sr-success');
                    } else {
                        seo_message.addClass('sr-fail');
                    }
                    seo_message.show();
                    seo_message.text(response.mes);
                    seo_message.delay(800).fadeOut("slow");
                }
            });
        });
        $("#seo-btn-list").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-seo-list',
                type: "POST",
                data: {
                    title: $("#seo-title-list").val(),
                    keywords: $("#seo-keywords-list").val(),
                    description: $("#seo-description-list").val(),
                    seo_introd_p: $("#seo-introd-p-list").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        seo_message_list.addClass('sr-success');
                    } else {
                        seo_message_list.addClass('sr-fail');
                    }
                    seo_message_list.show();
                    seo_message_list.text(response.mes);
                    seo_message_list.delay(800).fadeOut("slow");
                }
            });
        });
        $("#edit-extrapage-btn").click(function () {
            console.log('click button');

            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-extrapage-info',
                type: "POST",
                data: {
                    temp_community: base64_encode($("#sr_templates_community").val()),
                    temp_overview: base64_encode($("#sr_templates_overview").val()),
                    temp_features: base64_encode($("#sr_templates_features").val()),
                    temp_map: base64_encode($("#sr_templates_map").val()),
                    temp_video: base64_encode($("#sr_templates_video").val())
                },
                success: function (response) {

                    if (response.error == 0) {
                        extrapage_message.addClass('sr-success');
                    } else {
                        extrapage_message.addClass('sr-fail');
                    }
                    extrapage_message.show();
                    extrapage_message.text(response.mes);
                    extrapage_message.delay(800).fadeOut("slow");
                }
            });
        });
        $("#popupsave").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-popup',
                type: "POST",
                data: {
                    status: $("#status").val(),
                    title: $("#title").val(),
                    force: $("#force").val(),
                    success: $("#success").val(),
                    error: $("#error").val(),
                    sub: $("#sub").val(),
                    css: $("#css").val(),
                    num: $("#num").val(),
                    btn: $("#btn").val(),
                    email: $("#email").val(),
                    customHtml: base64_encode($("#customHtml").val()),
                    showCustom: $("#showCustomCheckBox")[0].checked,
                    showType: $("#LPShowType")[0].checked
                },
                success: function (response) {
                    if (response.error == 0) {
                        popup_message.addClass('sr-success');
                    } else {
                        popup_message.addClass('sr-fail');
                    }
                    popup_message.show();
                    popup_message.text(response.mes);
                    popup_message.delay(800).fadeOut("slow");
                }
            });
        });
        function base64_encode(data) {
            var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
                ac = 0,
                enc = "",
                tmp_arr = [];

            if (!data) {
                return data;
            }

            do { // pack three octets into four hexets
                o1 = data.charCodeAt(i++);
                o2 = data.charCodeAt(i++);
                o3 = data.charCodeAt(i++);

                bits = o1 << 16 | o2 << 8 | o3;

                h1 = bits >> 18 & 0x3f;
                h2 = bits >> 12 & 0x3f;
                h3 = bits >> 6 & 0x3f;
                h4 = bits & 0x3f;

                // use hexets to index into b64, and append result to encoded string
                tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
            } while (i < data.length);

            enc = tmp_arr.join('');

            var r = data.length % 3;

            return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);

        }

        function saveTemplate() {
            var id;
            if (window.oldTemplateListValue > -1) {
                id = window.oldTemplateListValue;
            }
            else {
                id = $('#templates-list').val();
            }
            console.log(id);
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-templates',
                type: "POST",
                data: {
                    css: base64_encode($("#css-template").val()),
                    details: base64_encode($("#details-template").val()),
                    result: base64_encode($("#result-template").val()),
                    id: id,
                    relatedproperties: $("#template-related-properties").is(":checked"),
                    rpzipcode: $("#RPZipcode").is(":checked"),
                    rpbedrooms: $("#RPBedrooms").is(":checked")

                },
                success: function (response) {
                    template_message.removeClass();
                    if (response.error == 0) {
                        template_message.addClass('sr-success');
                    } else {
                        template_message.addClass('sr-fail');
                    }
                    template_message.show();
                    template_message.text(response.mes);
                    template_message.delay(800).fadeOut("slow");
                }
            });
            window.oldTemplateListValue = -5;
        }

        $("#edit-templates-btn").click(function () {
            window.oldTemplateListValue = -5;
            saveTemplate();
        });


        $("#reset-templates-btn").click(function () {
            oldTemplateListValue = -5;
            if (!confirm("Are you sure you want to reset both templates?")) {
                return;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=reset-templates',
                type: "POST",
                success: function (response) {
                    template_message.removeClass();
                    if (response.error == 0) {
                        $('#templates-list').change();
                        template_message.addClass('sr-success');
                    } else {
                        template_message.addClass('sr-fail');
                    }
                    if (response.reload == 'true') {
                        location.reload();
                    }
                    if (response.isset == 0) {
                        var option = '<option value="' + response.id + '">' + response.name + '</option>';
                        $('#templates-list').append(option);
                        $('#templates-list').change();
                    }
                    template_message.show();
                    template_message.text(response.mes);
                    template_message.delay(800).fadeOut("slow");
                }
            });
        });
        $("#create-templates-btn").click(function () {
            window.oldTemplateListValue = -5;
            var r = confirm("Do you want to save current template ?");
            if (r == true) {
                saveTemplate();
            }
            var templateName = prompt("Please enter name of new template", "");
            if (templateName != '' && templateName != null) {
                $.ajax({
                    url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=create-templates',
                    type: "POST",
                    data: {
                        css: '',
                        details: '',
                        result: '',
                        name: templateName
                    },
                    success: function (response) {
                        template_message.removeClass();
                        if (response.error == 0) {
                            template_message.addClass('sr-success');
                            $('#templates-list option').removeAttr('selected');
                            $('#css-template').val('');
                            $('#details-template').val('');
                            $('#result-template').val('');
                            var option = '<option value="' + response.id + '" selected>' + response.name + '</option>';
                            $('#templates-list').append(option);
                        } else {
                            template_message.addClass('sr-fail');
                        }
                        template_message.show();
                        template_message.text(response.mes);
                        template_message.delay(800).fadeOut("slow");
                    }
                });
            }
        });
        $("#delete-templates-btn").click(function () {
            window.oldTemplateListValue = -5;
            var r = confirm("Are you sure, you want to delete this template ?");
            if (r == true) {

                $.ajax({
                    url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=delete-templates',
                    type: "POST",
                    data: {
                        id: $('#templates-list').val()
                    },
                    success: function (response) {
                        template_message.removeClass();
                        if (response.error == 0) {
                            template_message.addClass('sr-success');
                            $('#templates-list option:selected').remove();
                            $('#templates-list option[value|="' + response.newDefaultID + '"]').attr('selected', 'selected');
                            $('#templates-list').change();
                        } else {
                            template_message.addClass('sr-fail');
                        }
                        template_message.show();
                        template_message.text(response.mes);
                        template_message.delay(800).fadeOut("slow");
                    }
                });
            }

        });
        $("#set-default-templates-btn").click(function () {
            window.oldTemplateListValue = -5;
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=set-default-templates',
                type: "POST",
                data: {
                    id: $('#templates-list').val()
                },
                success: function (response) {
                    template_message.removeClass();
                    if (response.error == 0) {
                        template_message.addClass('sr-success');
                    } else {
                        template_message.addClass('sr-fail');
                    }
                    template_message.show();
                    template_message.text(response.mes);
                    template_message.delay(800).fadeOut("slow");
                }
            });

        });
        oldTemplateListValue = -5;
        $("#templates-list").change(function () {
            if (oldTemplateListValue > -1) {
                var r = confirm("Do you want to save current template ?");
                if (r == true) {
                    saveTemplate();
                }
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=get-data-templates',
                type: "POST",
                data: {
                    id: $('#templates-list').val()
                },
                success: function (response) {
                    $('#details-template').val(response.templates.details);
                    $('#result-template').val(response.templates.result);
                    $('#css-template').val(response.templates.css);
                }
            });

        });
        $("#templates-list").click(function () {
            window.oldTemplateListValue = jQuery('#templates-list').val();
        });
        $("#mailchimp-add").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=mailchimp-key',
                type: "POST",
                data: {
                    key: $("#mailchimpapikey").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        mailchimp_message.addClass('sr-success');
                    } else {
                        mailchimp_message.addClass('sr-fail');
                    }
                    mailchimp_message.show();
                    mailchimp_message.text(response.mes);
                    mailchimp_message.delay(1500).fadeOut("slow");
                    if (response.error == 0) {
                        html = '<option value="" selected="selected">None</option>';
                        for (i = 0; i < response.lists.length; i++) {
                            html += "<option value=\"" + response.lists[i]['id'] + "\">" + response.lists[i]['name'] + "</option>";
                        }
                        if (response.lists.length == 0) {
                            $('#mcwarning').html('<p><span style="color:red;font-weight:bold;">Warning:</span> You currently have no lists set up in your Mail Chimp Account. You will have to setup a list in order for SEO RETS to integrate with Mail Chimp. Please setup a list, and then refresh this page. For more information on how to do this visit the MailChimp help page: <a href="http://kb.mailchimp.com/article/how-do-i-create-a-new-list">How do I create a new list?</a></p>');
                        }
                        $('#mailchimp-list').html(html);
                        $('#mailchimpshowkey').html($("#mailchimpapikey").val());
                        $('#mc-noaccess').hide();
                        $('#mc-access').show();
                    }
                }
            });
        });

        $("#mailchimp-remove").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=mailchimp-key',
                type: "POST",
                data: {},
                success: function (response) {
                    if (response.error == 0) {
                        mailchimp_message.addClass('sr-success');
                    } else {
                        mailchimp_message.addClass('sr-fail');
                    }
                    mailchimp_message.show();
                    mailchimp_message.text(response.mes);
                    mailchimp_message.delay(1500).fadeOut("slow");
                    if (response.error == 0) {
                        $('#mc-noaccess').show();
                        $('#mc-access').hide();
                    }
                }
            });
        });

        $("#edit-plugintext").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-plugintext',
                type: "POST",
                data: {
                    login: $("#plugintext-login").val(),
                    signup: $("#plugintext-signup").val(),
                    forgot: $("#plugintext-forgot").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        plugintext_message.addClass('sr-success');
                    } else {
                        plugintext_message.addClass('sr-fail');
                    }
                    plugintext_message.show();
                    plugintext_message.text(response.mes);
                    plugintext_message.delay(1500).fadeOut("slow");
                }
            });
        });

        $("#mailchimp-save").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=mailchimp-list',
                type: "POST",
                data: {
                    id: $("#mailchimp-list").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        mailchimp_message.addClass('sr-success');
                    } else {
                        mailchimp_message.addClass('sr-fail');
                    }
                    mailchimp_message.show();
                    mailchimp_message.text(response.mes);
                    mailchimp_message.delay(1500).fadeOut("slow");
                }
            });
        });

        $("#leadcapture-save").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=leadcapture-save',
                type: "POST",
                data: {
                    emails: $("#leadcapture-emails").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        leadcapture_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        leadcapture_message.addClass('sr-fail');
                    }
                    leadcapture_message.show();
                    leadcapture_message.text(response.mes);
                    leadcapture_message.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#using_boot-save").click(function () {
            var check = true;
            if ($('#using_boot').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=using_boot-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        boot_mess.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        boot_mess.addClass('sr-fail');
                    }
                    boot_mess.show();
                    boot_mess.text(response.mes);
                    boot_mess.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#fast_query-save").click(function () {
            var check = true;
            if ($('#fast_query').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=fast_query-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        fqm.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        fqm.addClass('sr-fail');
                    }
                    fqm.show();
                    fqm.text(response.mes);
                    fqm.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#using_crm-save").click(function () {
            var check = true;
            if ($('#using_crm').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=using_crm-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        crm_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        boot_mess.addClass('sr-fail');
                    }
                    crm_message.show();
                    crm_message.text(response.mes);
                    crm_message.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#show_features-save").click(function () {
            var check = true;
            if ($('#show_features').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=show_features-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        features_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        features_message.addClass('sr-fail');
                    }
                    features_message.show();
                    features_message.text(response.mes);
                    features_message.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#open_in_new_window-save").click(function () {
            var check = true;
            if ($('#open_in_new_window').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=open_in_new_window-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        oinw_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        oinw_message.addClass('sr-fail');
                    }
                    oinw_message.show();
                    oinw_message.text(response.mes);
                    oinw_message.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#bootstrap_refine-save").click(function () {
            var check = true;
            if ($('#bootstrap_refine').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=bootstrap_refine-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        boot_refine.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        boot_refine.addClass('sr-fail');
                    }
                    boot_refine.show();
                    boot_refine.text(response.mes);
                    boot_refine.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#bootstrap_refine_sc-save").click(function () {
            var check = true;
            if ($('#bootstrap_refine_sc').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=bootstrap_refine_sc-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        boot_refine_sc.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        boot_refine_sc.addClass('sr-fail');
                    }
                    boot_refine_sc.show();
                    boot_refine_sc.text(response.mes);
                    boot_refine_sc.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#use_custom_pagi-save").click(function () {
            var check = true;
            if ($('#use_custom_pagi').is(":checked")) {
                check = true;
            } else {
                check = false;
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=use_custom_pagi-save',
                type: "POST",
                data: {
                    check: check
                },
                success: function (response) {
                    if (response.error == 0) {
                        ucp.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        ucp.addClass('sr-fail');
                    }
                    ucp.show();
                    ucp.text(response.mes);
                    ucp.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#order-save").click(function () {
            var sort = "none";
            if ($('#highorlow').is(":checked")) {
                sort = $('#price-order option:selected').val();
            } else if ($('#none-order').is(":checked")) {
                sort = "none";
            }
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=order-save',
                type: "POST",
                data: {
                    order: sort
                },
                success: function (response) {
                    if (response.error == 0) {
                        order_mess.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        order_mess.addClass('sr-fail');
                    }
                    order_mess.show();
                    order_mess.text(response.mes);
                    order_mess.delay(1500).fadeOut("slow");
                }
            });
        });

        $("#emailmethod-save").click(function () {
            console.log($("input[name='email-option']:checked").val());
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=emailmethod-save',
                type: "POST",
                data: {
                    emailmethod: $("input[name='email-option']:checked").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        emailmethod_message.addClass('sr-success');
                    } else {
                        emailmethod_message.addClass('sr-fail');
                    }
                    emailmethod_message.show();
                    emailmethod_message.text(response.mes);
                    emailmethod_message.delay(1500).fadeOut("slow");
                }
            });
        });
        $("#customform-save").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=customform-save',
                type: "POST",
                data: {
                    html: base64_encode($("#customform-html").val())
                },
                success: function (response) {
                    if (response.error == 0) {
                        customform_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        customform_message.addClass('sr-fail');
                    }
                    customform_message.show();
                    customform_message.text(response.mes);
                    customform_message.delay(1500).fadeOut("slow");
                }
            });
        });

        $("#unfoundpage-save").click(function () {
            $.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=unfoundpage-save',
                type: "POST",
                data: {
                    html: $("#unfoundpage-html").val()
                },
                success: function (response) {
                    if (response.error == 0) {
                        unfoundpage_message.addClass('sr-success');
                        <?php //FIXME need to remove sr-fail class ?>
                    } else {
                        unfoundpage_message.addClass('sr-fail');
                    }
                    unfoundpage_message.show();
                    unfoundpage_message.text(response.mes);
                    unfoundpage_message.delay(1500).fadeOut("slow");
                }
            });
        });
        function close_popup() {
            jQuery.ajax({
                url: "<?php echo home_url() ?>/sr-ajax?action=no-popup"
            });
            jQuery.magnificPopup.close();
            jQuery("#sr-popup-form").fadeOut("slow", function () {
                sr_popup.fadeOut("slow");
            });
        };
        $("#preview").click(function () {

//        $("#sr-popup").remove();
//
//        var htm = '<div id="sr-popup" style="display:none;"><div id="sr-popup-form" style="display:none;">';
//
//        if ($("#force").val() == 'disabled') {
//            htm += '<a href="javascript: void(0);"><img src="<?php //echo $sr->plugin_dir?>///resources/images/close.png" id="sr-popup-close" /></a>';
//        }
//
//        htm += '<iframe id="sr-popup-frame" scrolling="no" src="<?php //echo get_bloginfo('url')?>///sr-contact?title=' + encodeURIComponent($("#title").val()) + '&sub=' + encodeURIComponent($("#sub").val()) + '&btn=' + encodeURIComponent($("#btn").val()) + '&css=' + encodeURIComponent($("#css").val()) + '&err=' + encodeURIComponent($("#error").val()) + '&success=' + encodeURIComponent($("#success").val()) + '"></iframe></div></div>';
//
//        $("body").append(htm);
//
//        sr_popup = $("#sr-popup");
//
//        sr_popup.fadeIn("slow", function () {
//            $("#sr-popup-form").fadeIn("slow");
//        });
//
//        jQuery("#sr-popup-close").click(close_popup);
//        window.setTimeout(callSizeFrame,1150);
            closeButton = false;
            if ($("#force").val() == 'disabled') {
                closeButton = true;
            }
            htm = '<?php echo get_bloginfo('url')?>/sr-contact?title=' + encodeURIComponent($("#title").val()) + '&sub=' + encodeURIComponent($("#sub").val()) + '&btn=' + encodeURIComponent($("#btn").val()) + '&css=' + encodeURIComponent($("#css").val()) + '&err=' + encodeURIComponent($("#error").val()) + '&success=' + encodeURIComponent($("#success").val());

//            htm = '<?php //echo get_bloginfo('url') ;?>///sr-contact';
            var sizeFrame = function sizeFrame() {
                var F = document.getElementById("sr-popup-frame");
                var F2 = document.getElementById("sr-popup2");
                var value;
                var constantHeight = 40;

                if (F.contentDocument) {
                    value = F.contentDocument.documentElement.scrollHeight + constantHeight; //FF 3.0.11, Opera 9.63, and Chrome
                } else {
                    value = F.contentWindow.document.body.scrollHeight + constantHeight; //IE6, IE7 and Chrome
                }
                if (value == '' || value < 220) {
                    return false;
                }
                var windowHeight = jQuery(window).height();
                if (value + constantHeight <= windowHeight) {
                    F.height = value;
                }
                else {
                    F.height = windowHeight - (windowHeight / 100 * 15);
                }
                F.contentDocument.documentElement.scrollHeight = parseInt(F.height);
                F.contentWindow.document.body.scrollHeight = parseInt(F.height);
                jQuery("#sr-popup-frame").height(parseInt(F.height));
                document.getElementById('#sr-popup-frame').contentWindow.document.body.offsetHeight + 'px';
//                jQuery("#sr-popup-frame").css('height', parseInt(F.height));
//                jQuery("#sr-popup-frame").css('width', parseInt(F2.width));
//                jQuery("#sr-popup2").css('height', parseInt(F.height));
                return true;

            }
            var callAmount = 0;
            var callSizeFrame = function callSizeFrame() {
                var iframeLoadFlag;
                iframeLoadFlag = !sizeFrame();

                if (iframeLoadFlag) {
                    window.setTimeout(callSizeFrame, 500);
                }
            }
            jQuery.magnificPopup.open({
                items: {
                    src: htm,
                    type: 'iframe'
                },
                iframe: {
                    markup: '<div style="text-align:center;" id="sr-popup2" class="zoom-anim-dialog"><div class="mfp-close"></div>' +
                    '<iframe class="mfp-iframe" id="sr-popup-frame" allowfullscreen style="border:0;width:100%" frameborder="0"></iframe>' +
                    '</div>',

                },
                fixedContentPos: true,
                fixedBgPos: true,
                overflowY: 'auto',
                showCloseBtn: true,
                closeBtnInside: true,
                preloader: false,

                midClick: false,
                closeOnBgClick: false,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            });
            callSizeFrame();

        });


    });
    var callAmount = 0;
    var callSizeFrame = function callSizeFrame() {
        var iframeLoadFlag = true;
        while (iframeLoadFlag) {
            if (window.callAmount > 500) {
                break;
            }
            iframeLoadFlag = !sizeFrame();
            window.callAmount++;
        }
//    window.setTimeout(callSizeFrame,1000);
    }
    var sizeFrame = function sizeFrame() {
        var F = document.getElementById("sr-popup-frame");
        var value;
        var constantHeight = 40;

        if (F.contentDocument) {

            value = F.contentDocument.documentElement.scrollHeight + constantHeight; //FF 3.0.11, Opera 9.63, and Chrome
        } else {
            value = F.contentWindow.document.body.scrollHeight + constantHeight; //IE6, IE7 and Chrome

        }
        if (value == '' || value < 50) {
            return false;
        }
        var windowHeight = jQuery(window).height();
        if (value + constantHeight <= windowHeight) {
            F.height = value;
        }
        else {
            F.height = windowHeight - (windowHeight / 100 * 15);
        }
        F.contentDocument.documentElement.scrollHeight = parseInt(F.height);
        F.contentWindow.document.body.scrollHeight = parseInt(F.height);
        jQuery("#sr-popup-frame").height(parseInt(F.height));
        jQuery("#sr-popup-frame").css('height', parseInt(F.height));
        jQuery('#test').text(windowHeight + ' ' + F.height);
        return true;

    }
    //jQuery(window).resize(function(){
    //    window.callAmount=0;
    //    sizeFrame();
    //});
</script>
<div class="wrap">
    <div id="icon-tools" class="icon32">
    </div>
    <h2>SEO RETS :: Developer Tools</h2>

    <div class="tool-box">
        <div id="tabgroup">
            <ul>
                <li><a href="#seodata">Customize SEO Data</a></li>
                <li><a href="#sitemap">Sitemap Submission</a></li>
                <li><a href="#styles">Edit Styles</a></li>
                <li><a href="#templates">Edit Templates</a></li>
                <li><a href="#leadpopup">Lead Popup</a></li>
                <li><a href="#plugintext">Plugin Text</a></li>
                <li><a href="#advanced">Advanced Settings</a></li>
                <!--                <li><a href="#social">Social Network</a></li>-->

            </ul>
            <div id="seodata">
                <div>
                    <div style="float:left;">
                        <?php
                        $seodata = get_option('sr_seodata');
                        ?>
                        <h3>Customize SEO Data</h3>

                        <p style="width:400px;">
                            You can use this page to customize the data that search engines use to gather keywords and
                            information
                            about
                            your site.
                        </p>

                        <p style="font-weight:bold;margin-bottom:0px;">
                            Title And H1 (Theme Dependent)
                        </p>

                        <p style="margin-top:5px;">
                            <input type="text" style="width:400px;" id="seo-title"
                                   value="<?php echo isset($seodata['title']) ? htmlentities($seodata['title']) : $sr->seo_defaults['title'] ?>"/>
                        </p>

                        <p style="font-weight:bold;margin-bottom:0px;">
                            Meta Keywords
                        </p>

                        <p style="margin-top:5px;">
                            <input type="text" style="width:400px;" id="seo-keywords"
                                   value="<?php echo isset($seodata['keywords']) ? htmlentities($seodata['keywords']) : $sr->seo_defaults['keywords'] ?>"/>
                        </p>

                        <p style="font-weight:bold;margin-bottom:0px;">
                            Meta Description
                        </p>

                        <p style="margin-top:5px;">
        <textarea style="width:400px;" rows="6"
                  id="seo-description"><?php echo isset($seodata['description']) ? htmlentities($seodata['description']) : $sr->seo_defaults['description'] ?></textarea>
                        </p>
                        <input type="submit" class="button-primary" value="Save SEO Data" id="seo-btn"/> <span
                            id="seo-message"></span>
                    </div>
                    <div style="float:left;margin-left:20px;">
                        <div class="widget" style="margin-top: 113px;">
                            <div class="widget-top" style="cursor: default;">
                                <div class="widget-title">
                                    <h4 style="margin-left: 15px;margin-right:15px;">Avaliable Variables</h4>
                                </div>
                            </div>
                            <div class="widget-inside" style="padding: 10px;display:block;">
                                <table style="width:250px;">
                                    <tr>
                                        <td>
                                            <strong>Address:</strong>
                                        </td>
                                        <td>
                                            %address%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>City:</strong>
                                        </td>
                                        <td>
                                            %city%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>State:</strong>
                                        </td>
                                        <td>
                                            %state%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Zip:</strong>
                                        </td>
                                        <td>
                                            %zip%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>MLS #:</strong>
                                        </td>
                                        <td>
                                            %mls_id%
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div style="clear:left;">
                    </div>
                    <div>
                        <div style="float:left">
                            <?php
                            $seodata_list = get_option('sr_seodata_list');
                            ?>
                            <br>
                            <br>
                            <br>

                            <h3>Customize SEO Content</h3>
                            <h4>For cities, condos and subdivision auto generated pages </h4>

                            <p style="font-weight:bold;margin-bottom:0px;">
                                Title And H1 (Theme Dependent)
                            </p>

                            <p style="margin-top:5px;">
                                <input type="text" style="width:400px;" id="seo-title-list"
                                       value="<?php echo isset($seodata_list['title']) ? htmlentities($seodata_list['title']) : '' ?>"/>
                            </p>

                            <p style="font-weight:bold;margin-bottom:0px;">
                                Meta Keywords
                            </p>

                            <p style="margin-top:5px;">
                                <input type="text" style="width:400px;" id="seo-keywords-list"
                                       value="<?php echo isset($seodata_list['keywords']) ? htmlentities($seodata_list['keywords']) : '' ?>"/>
                            </p>

                            <p style="font-weight:bold;margin-bottom:0px;">
                                Meta Description
                            </p>

                            <p style="margin-top:5px;">
            <textarea style="width:400px;" rows="6"
                      id="seo-description-list"><?php echo isset($seodata_list['description']) ? htmlentities($seodata_list['description']) : '' ?></textarea>
                            </p>

                            <p style="font-weight:bold;margin-bottom:0px;">
                                Introduction Paragraph for City and Subdivision pages
                            </p>

                            <p style="margin-top:5px;">
            <textarea style="width:400px;" rows="6"
                      id="seo-introd-p-list"><?php echo isset($seodata_list['introduction-p']) ? htmlentities($seodata_list['introduction-p']) : '' ?></textarea>

                            </p>
                            <input type="submit" class="button-primary" value="Save SEO Data" id="seo-btn-list"/> <span
                                id="seo-message-list"></span>

                        </div>
                        <div style="float:left;margin-left:20px;">
                            <div class="widget" style="margin-top: 113px;">
                                <div class="widget-top" style="cursor: default;">
                                    <div class="widget-title">
                                        <h4 style="margin-left: 15px;margin-right:15px;">Avaliable Variables</h4>
                                    </div>
                                </div>
                                <div class="widget-inside" style="padding: 10px;display:block;">
                                    <table style="width:250px;">
                                        <tr>
                                            <td>
                                                <strong>Property type:</strong>
                                            </td>
                                            <td>
                                                %type%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Object of search (cities, communities, condos):</strong>
                                            </td>
                                            <td style="vertical-align:middle !important;">
                                                %object%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Search object value:</strong>
                                            </td>
                                            <td>
                                                %object_value%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Number of properties:</strong>
                                            </td>
                                            <td>
                                                %numberofresults%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Number of properties (digits):</strong>
                                            </td>
                                            <td>
                                                %numberofresults_digits%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Average price:</strong>
                                            </td>
                                            <td>
                                                %calculatedaverageprice%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Lowest price:</strong>
                                            </td>
                                            <td>
                                                %calculatedlowprice%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Highest price:</strong>
                                            </td>
                                            <td>
                                                %calculatedhighprice%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Average sqft:</strong>
                                            </td>
                                            <td>
                                                %calculatedaveragesqft%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Biggest sqft:</strong>
                                            </td>
                                            <td>
                                                %calculatedhighsqft%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Smallest sqft:</strong>
                                            </td>
                                            <td>
                                                %calculatedlowsqft%
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="clear:left;">
                        </div>
                        <div style="clear:left;">
                        </div>
                    </div>
                </div>
            </div>
            <div id="sitemap">
                <h3>Sitemap Submission</h3>

                <p>
                    You can use the button below to submit your SEO RETS XML sitemap to all of the major search engines.
                    Sitemaps
                    are important because search engines use them to find and index the pages on your site.
                </p>
                <input type="submit" class="button-primary" value="Submit Sitemap" id="generate-sitemap"/> <span
                    id="sitemap-message"></span>

                <p>If you want to manually add your sitemap to search engines you can find the urls below.</p>
    <textarea rows="4" cols="40"><?php
        $total = 0;

        foreach ($seo_rets_plugin->metadata as $key => $m) {
            if (SEO_RETS_Plugin::is_type_hidden($key)) {
                continue;
            }
            $request = $sr->api_request('get_listings', array(
                'type' => $key,
                'fields' => array('doesntexist' => 1),
                'count' => true,
                'limit' => array(
                    'range' => 1
                )
            ));

            $total += $request->count;
        }

        $num_of_files = ceil($total / 50000);

        for ($n = 1; $n <= $num_of_files; $n++) {
            echo get_bloginfo('url') . "/sr-sitemap.xml?n={$n}\n";
        }
        ?></textarea>
            </div>
            <div id="styles">
                <h3>Edit Styles</h3>

                <p>
                    Use the section below to add or edit styles for the listing HTML the SEO RETS plugin outputs. You
                    can reset the
                    styles to their initial values by clearing the editor below and clicking "Save CSS"
                </p>

    <textarea id="edit-css" style="width:100%;"
              rows="26"><?php echo htmlentities(get_option('sr_css') ? get_option('sr_css') : $sr->include_return("resources/defaults/template-styles.css")) ?></textarea>
                <input type="submit" id="edit-css-btn" class="button-primary" value="Save CSS"
                       style="margin-top: 15px;"/> <span
                    id="css-message"></span>
            </div>
            <div id="templates">
                <?php
                $templates = get_option('sr_templates');
                $extraFieldsTemplate = get_option('sr_templates_extra');
                ?>
                <h3>Edit Templates</h3>

                <p>
                    Use the section below to edit template HTML.
                </p>

                <p>
                    <label for="template-related-properties">Would you like to display related properties?
                        : </label><input <?php echo $extraFieldsTemplate['show_related_properties'] == 'true' ? 'checked' : ''; ?>
                        id="template-related-properties" type="checkbox" name="template-related-properties">

                <div
                    class="extraRP <?php echo $extraFieldsTemplate['show_related_properties'] == 'true' ? '' : 'hide'; ?>">
                    <input <?php echo $extraFieldsTemplate['rp_zipcode'] == 'true' ? 'checked' : ''; ?> id="RPZipcode"
                                                                                                        type="checkbox"
                                                                                                        name="RPZipcode"><label
                        for="RPZipcode"> Display properties within the same zip code </label><br>
                    <input <?php echo $extraFieldsTemplate['rp_bedrooms'] == 'true' ? 'checked' : ''; ?> id="RPBedrooms"
                                                                                                         type="checkbox"
                                                                                                         name="RPBedrooms"><label
                        for="RPBedrooms"> Display properties with the same number of bedrooms </label>
                </div>
                </p>
                <label for="templates-list">Choose template:</label>

                <select id="templates-list">
                    <?php
                    $templatesList = get_option('sr_templates_list');

                    foreach ($templatesList as $template) {
                        if (isset($template['name'])) {
                            if ($template['default'] == 1) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="' . $template["id"] . '" ' . $selected . ' >' . $template["name"] . '</option>';
                        }
                    }
                    ?>
                </select>

                <h3>Css Styles / Java Script</h3>
    <textarea id="css-template" style="width:100%;"
              rows="18"><?php echo isset($templates['css']) ? htmlentities($templates['css']) : file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-css-js.php") ?></textarea>

                <h3>Listing Details</h3>
    <textarea id="details-template" style="width:100%;"
              rows="26"><?php echo isset($templates['details']) ? htmlentities($templates['details']) : file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-details.php") ?></textarea>

                <h3>Listing Result</h3>
    <textarea id="result-template" style="width:100%;"
              rows="26"><?php echo isset($templates['result']) ? htmlentities($templates['result']) : file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-result.php") ?></textarea>

                <input type="submit" id="edit-templates-btn" class="button-primary" value="Save Templates"
                       style="margin-top: 15px;"/>
                <input type="submit" id="create-templates-btn" class="button-primary"
                       value="Create New Template" style="margin-top: 15px;"/>
                <input type="submit" id="delete-templates-btn" class="button-primary"
                       value="Delete Template" style="margin-top: 15px;"/>
                <input type="submit" id="set-default-templates-btn" class="button-primary"
                       value="Set As Default" style="margin-top: 15px;"/>
                <input type="submit" id="reset-templates-btn" class="button-primary"
                       value="Reset Seo Rets Templates" style="margin-top: 15px;"/>
    <span
        id="template-message"></span>
            </div>
            <div id="leadpopup">
                <?php
                $popup = get_option('sr_popup');
                ?>
                <h3>Lead Popup</h3>

                <p>You can use this page to enable and customize a lead popup form. The form will display after a given
                    number of
                    listings are viewed.</p>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="status">Status</label>
                        </th>
                        <td>
                            <select name="status" id="status">
                                <option
                                    value="disabled"<?php echo ($popup['status'] == "disabled" || !isset($popup['status'])) ? " selected" : "" ?>>
                                    Disabled
                                </option>
                                <option
                                    value="enabled"<?php echo ($popup['status'] == "enabled") ? " selected" : "" ?>>
                                    Enabled
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="force">Forced Submissions</label>
                        </th>
                        <td>
                            <select name="force" id="force">
                                <option
                                    value="disabled"<?php echo ($popup['force'] == "disabled" || !isset($popup['force'])) ? " selected" : "" ?>>
                                    Disabled
                                </option>
                                <option
                                    value="enabled"<?php echo ($popup['force'] == "enabled") ? " selected" : "" ?>>
                                    Enabled
                                </option>
                            </select> <span class="description">Forces users to fill out form before they can view any more listings.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="num">Amount Viewed</label>
                        </th>
                        <td>
                            <input name="num" type="text" id="num" class="regular-text" style="width:4em;"
                                   value="<?php echo (isset($popup['num']) && $popup['num']) ? htmlentities($popup['num']) : htmlentities($sr->popup_defaults['num']) ?>"/>
                            <span class="description">The number of listings a user views before the lead popup is activated.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="title">Receiving Email</label>
                        </th>
                        <td>
                            <input name="email" type="text" id="email" class="regular-text"
                                   value="<?php echo (isset($popup['email']) && $popup['email']) ? htmlentities($popup['email']) : htmlentities($sr->popup_defaults['email']) ?>"/>
                            <span class="description">The email address that receives the leads.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="title">Popup Title</label>
                        </th>
                        <td>
                            <input name="title" type="text" id="title" class="regular-text"
                                   value="<?php echo (isset($popup['title']) && $popup['title']) ? htmlentities($popup['title']) : htmlentities($sr->popup_defaults['title']) ?>"/>
                            <span class="description">The text that displays on the first line of your popup.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="sub">Popup Sub Text</label>
                        </th>
                        <td>
                            <input name="sub" type="text" id="sub" class="regular-text"
                                   value="<?php echo (isset($popup['sub']) && $popup['sub']) ? htmlentities($popup['sub']) : htmlentities($sr->popup_defaults['sub']) ?>"/>
                            <span class="description">The text that displays on the second line of your popup.</span>
                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">
                            <label for="error">Error Message</label>
                        </th>
                        <td>
                            <input name="error" type="text" id="error" class="regular-text"
                                   value="<?php echo (isset($popup['error']) && $popup['error']) ? htmlentities($popup['error']) : htmlentities($sr->popup_defaults['error']) ?>"/>
                <span
                    class="description">The message that displays when a user fails to fill out the form correctly.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="success">Success Message</label>
                        </th>
                        <td>
                            <input name="success" type="text" id="success" class="regular-text"
                                   value="<?php echo (isset($popup['success']) && $popup['success']) ? htmlentities($popup['success']) : htmlentities($sr->popup_defaults['success']) ?>"/>
                            <span class="description">The message that displays when a user completes the form.</span>
                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">
                            <label for="btn">Button Text</label>
                        </th>
                        <td>
                            <input name="btn" type="text" id="btn" class="regular-text"
                                   value="<?php echo (isset($popup['btn']) && $popup['btn']) ? htmlentities($popup['btn']) : htmlentities($sr->popup_defaults['btn']) ?>">
                            <span class="description">The text that displays inside the submit button.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="css">CSS Styles</label>
                        </th>
                        <td>
                <textarea id="css" name="css"
                          style="width:600px;height:300px;"><?php echo (isset($popup['css']) && $popup['css']) ? htmlentities($popup['css']) : htmlentities($sr->popup_defaults['css']) ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="showCustomCheckBox">Custom html form</label>
                        </th>
                        <td>
                            <input type="checkbox" id="showCustomCheckBox"
                                   name="showCustomCheckBox" <?php echo(isset($popup['showCustom']) && ($popup['showCustom'] == 'true') ? "checked" : ""); ?>/>

                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="LPShowType">Show Type</label>
                        </th>
                        <td>
                            <input type="checkbox" id="LPShowType"
                                   name="LPShowType" <?php echo(isset($popup['showType']) && ($popup['showType'] == 'true') ? "checked" : ""); ?>/>
                        </td>
                    </tr>
                    <tr valign="top" id="customHtmlForm" style="display:none;">
                        <th scope="row">
                            <label for="customHtml">Custom HTML</label>
                        </th>
                        <td>
                <textarea id="customHtml" name="customHtml"
                          style="width:600px;height:300px;"><?php echo (isset($popup['customHtml']) && $popup['customHtml']) ? htmlentities($popup['customHtml']) : "" ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="popupsave" id="popupsave" class="button-primary" value="Save Changes">
                    <input type="submit" name="preview" id="preview" class="button-primary" value="Preview"> <span
                        id="popup-message"></span>
                </p>

            </div>
            <div id="plugintext">
                <h3>Plugin Text</h3>
                <?php
                $text_settings = get_option('sr-plugintext');

                if ($text_settings) {
                    $text_settings_put = $text_settings;
                } else {
                    $text_settings_put = $sr->text_defaults;
                }

                ?>
                <p>You can use these settings to customize the text of the various plugin pages on your site.</p>

                <h3>Login</h3>
                <textarea style="width:100%;height:100px;"
                          id="plugintext-login"><?php echo $text_settings_put['login'] ?></textarea>

                <h3>Sign Up</h3>
    <textarea style="width:100%;height:100px;"
              id="plugintext-signup"><?php echo $text_settings_put['signup'] ?></textarea>

                <h3>Forgot Password</h3>
    <textarea style="width:100%;height:100px;"
              id="plugintext-forgot"><?php echo $text_settings_put['forgot'] ?></textarea>

                <input type="submit" id="edit-plugintext" class="button-primary" value="Save Settings"
                       style="margin-top: 15px;">
                <span id="plugintext-message"></span>
            </div>
            <div id="advanced">
                <h3>Default sort option</h3>

                <p>Select in what order thi listings will be show</p>
                <label for="none-order"><input type="radio" <?php if (get_option('sr_listingsOrder') == "none") {
                        echo "checked='checked'";
                    } ?> id="none-order" name="order">Without order</label><br/>
                <label for="highorlow"><input type="radio" <?php if (get_option('sr_listingsOrder') != "none") {
                        echo "checked='checked'";
                    } ?> id="highorlow" name="order">Use price sort
                    <select name="price-order" id="price-order">
                        <option <?php if (get_option('sr_listingsOrder') == "heightolow") {
                            echo "selected";
                        } ?> value="heightolow">High to low
                        </option>
                        <option <?php if (get_option('sr_listingsOrder') == "lowtoheigh") {
                            echo "selected";
                        } ?> value="lowtoheigh">Low to high
                        </option>
                    </select>
                </label><br/>
                <input type="submit" id="order-save" class="button-primary" value="Save"/>
                <span id="order_mess"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Activate CRM</h3>

                <p>You can activvate/deactivate SEO RETS CRM</p>
                <label for="using_crm"><input type="checkbox"
                                              id="using_crm" <?php if (get_option('sr_crm') == 'true') {
                        echo "checked='checked'";
                    } ?> name="using_crm">
                    Using CRM</label>
                <br/>
                <br/>

                <input type="submit" id="using_crm-save" class="button-primary" value="Save"/>
                <span id="crm_message"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Plugin Using Bootstrap</h3>

                <p>If your theme already uses bootstrap, you can disable it.</p>
                <label for="using_boot"><input type="checkbox"
                                               id="using_boot" <?php if (get_option('sr_boot') == 'true') {
                        echo "checked='checked'";
                    } ?> name="using_boot">
                    Using Bootstrap</label>
                <br/>
                <br/>

                <input type="submit" id="using_boot-save" class="button-primary" value="Save"/>
                <span id="boot_mess"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Show Features</h3>

                <p>Show Features on advanced search page.</p>
                <label for="show_features"><input type="checkbox"
                                                  id="show_features" <?php if (get_option('sr_show_features') == 'true') {
                        echo "checked='checked'";
                    } ?> name="show_features">
                    Show Features</label>
                <br/>
                <br/>

                <input type="submit" id="show_features-save" class="button-primary" value="Save"/>
                <span id="features-message"></span>
                <br/>
                <br/>
                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Search option</h3>

                <p>You can customize the search</p>
                <label for="open_in_new_window"><input type="checkbox"
                                                       id="open_in_new_window" <?php if (get_option('sr_open_in_new_window') == 'true') {
                        echo "checked='checked'";
                    } ?> name="open_in_new_window">
                    Open search results in new window</label>
                <br/>
                <br/>

                <input type="submit" id="open_in_new_window-save" class="button-primary" value="Save"/>
                <span id="open_in_new_window_message"></span>
                <br/>
                <br/>
                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Optional Pagination</h3>

                <p>Use word pagination</p>
                <label for="use_custom_pagi"><input type="checkbox"
                                                    id="use_custom_pagi" <?php if (get_option('sr_use_custom_pagi') == 'true') {
                        echo "checked='checked'";
                    } ?> name="use_custom_pagi">
                    Activate word pagination.</label>
                <br/>
                <br/>

                <input type="submit" id="use_custom_pagi-save" class="button-primary" value="Save"/>
                <span id="use_custom_pagi_message"></span>
                <br/>
                <br/>
                <div style="height:1px;background-color:#CCC;"></div>

                <h3>Bootstrap Refine Search</h3>

                <p>Include refine search, narrow search and map results</p>
                <p>To use this option in your theme, it must include the Bootstrap framework!</p>
                <label for="bootstrap_refine"><input type="checkbox"
                                                     id="bootstrap_refine" <?php if (get_option('bootstrap_refine') == 'true') {
                        echo "checked='checked'";
                    } ?> name="bootstrap_refine">
                    Activate Bootstrap Refine Search</label>
                <br/>
                <br/>

                <input type="submit" id="bootstrap_refine-save" class="button-primary" value="Save"/>
                <span id="boot-refine-message"></span>
                <br/>
                <br/>
                <div style="height:1px;background-color:#CCC;"></div>
                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Bootstrap Refine Shortcode</h3>

                <p>Include refine search, narrow search and map results</p>
                <p>To use this option in your theme, it must include the Bootstrap framework!</p>
                <p>This option will change standard refine block in the shortcode when refine was added as option to
                    shortcode.</p>
                <label for="bootstrap_refine_sc"><input type="checkbox"
                                                        id="bootstrap_refine_sc" <?php if (get_option('bootstrap_refine_sc') == 'true') {
                        echo "checked='checked'";
                    } ?> name="bootstrap_refine_sc">
                    Activate Bootstrap Refine Shortcode</label>
                <br/>
                <br/>

                <input type="submit" id="bootstrap_refine_sc-save" class="button-primary" value="Save"/>
                <span id="bootstrap_refine_sc-message"></span>
                <br/>
                <br/>


                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Fast Query</h3>
                <label for="fast_query"><input type="checkbox"
                                               id="fast_query" <?php if (get_option('sr_fq') == 'true') {
                        echo "checked='checked'";
                    } ?> name="fast_query">
                    Activate Fast Query</label>
                <br/>
                <br/>

                <input type="submit" id="fast_query-save" class="button-primary" value="Save"/>
                <span id="fast_query-message"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>
                <script type="text/javascript">

                    jQuery(function ($) {
                        var singletemplate_message = $("#singletemplate-message");

                        $("input[name='template-option']").change(function () {

                            if ($(this).val() == "every") {
                                $("#single-template").attr("disabled", true);

                                $("#hidy-thingy").slideDown("slow");

                            } else if ($(this).val() == "all") {
                                $("#single-template").removeAttr("disabled");

                                $("#hidy-thingy").slideUp("slow");
                            }
                        });

                        if ($("input[name='template-option']:checked").val() == "every") {

                            $("#hidy-thingy").slideDown("slow");
                            $("#single-template").attr("disabled", true);
                        }

                        $("#singletemplate-btn").click(function () {

                            var anarray = {};

                            $(".every-value").each(function () {
                                anarray[$("input", this).val()] = $("select", this).val();
                            });

                            $.ajax({
                                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=edit-template',
                                type: "POST",
                                data: {
                                    type: $("input[name='template-option']:checked").val(),
                                    allvalue: $("#single-template").val(),
                                    everyvalues: anarray
                                },
                                success: function (response) {
                                    if (response.error == 0) {
                                        singletemplate_message.addClass('sr-success');
                                    } else {
                                        singletemplate_message.addClass('sr-fail');
                                    }
                                    singletemplate_message.show();
                                    singletemplate_message.text(response.mes);
                                    singletemplate_message.delay(800).fadeOut("slow");
                                }
                            });
                        });
                    });
                </script>

                <h3>Plugin Custom Page Templates</h3>

                <p>Some WordPress themes include multiple templates that contain different content, layouts, or styles.
                    Using the
                    option below, you can customize the templates used for every custom plugin page.</p>
                <?php

                $temps = get_page_templates();
                $temps = array_merge(array('Default' => ''), $temps);

                $templates = get_option('sr_template');

                ?>

                <input type="radio" name="template-option"
                       value="all"<?php echo ($templates['type'] == "all") ? " checked" : "" ?> /> Use the <select
                    id="single-template" name="template">
                    <?php foreach ($temps as $pretty => $file): ?>
                        <?php if ($templates['all-value'] == $file): ?>
                            <option value="<?php echo $file ?>" selected><?php echo $pretty ?></option>
                        <?php else: ?>
                            <option value="<?php echo $file ?>"><?php echo $pretty ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select> template for all custom plugin pages.

                <br/>
                <input type="radio" name="template-option"
                       value="every"<?php echo ($templates['type'] == "every") ? " checked" : "" ?> /> Choose the
                template for every
                page

                <div id="hidy-thingy" style="display:none;">
                    <table id="multi-template" style="margin-left:15px;margin-top:10px;">
                        <?php foreach ($templates['every-values'] as $page => $temp): ?>
                            <tr>
                                <td align="right"><?php echo $page ?></td>
                                <td class="every-value"><input type="hidden" name="page-name"
                                                               value="<?php echo $page ?>"/>
                                    <select>
                                        <?php foreach ($temps as $pretty => $file): ?>
                                            <?php if ($temp == $file): ?>
                                                <option value="<?php echo $file ?>"
                                                        selected><?php echo $pretty ?></option>
                                            <?php else: ?>
                                                <option value="<?php echo $file ?>"><?php echo $pretty ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </table>
                </div>

                <br/><br/>
                <input type="submit" name="submit" id="singletemplate-btn" class="button-primary" value="Save"/> <span
                    id="singletemplate-message"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Lead Alert Settings</h3>

                <p>Enter email addresses to be alerted in the text box below, put each email on it's own line.</p>
                <textarea id="leadcapture-emails" cols="40"
                          rows="5"><?php echo get_option("sr_leadcapture") ?></textarea>
                <br/>
                <input type="submit" id="leadcapture-save" class="button-primary" value="Save"/>
                <span id="leadcapture-message"></span>

                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Email function settings</h3>

                <p>Select a function for sending emails.</p>
                <br>
                <input type="radio" name="email-option"
                       value="use_php_mail" <?= get_option('sr_emailmethod') != 'use_wp_mail' ? 'checked' : '' ?> />Use
                standard PHP method mail()
                <br/>
                <input type="radio" name="email-option"
                       value="use_wp_mail" <?= get_option('sr_emailmethod') == 'use_wp_mail' ? 'checked' : '' ?> />Use
                WordPress method wp_mail()
                <br/>

                <p style="font-size: 8px;">(Now we <?php echo get_option('sr_emailmethod'); ?>)</p>
                <br/>
                <br/>
                <input type="submit" id="emailmethod-save" class="button-primary" value="Save"/>
                <span id="emailmethod-message"></span>
                <br/>
                <br/>

                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Custom search form</h3>

                <p>Enter the HTML for a custom search form, shortcodes will work inside form. You can use this custom
                    form with the
                    shortcode [sr-search type="customform"]</p>
                <textarea id="customform-html" cols="100" rows="50"><?php echo get_option("sr_customform") ?></textarea>
                <br/>
                <input type="submit" id="customform-save" class="button-primary" value="Save"/><span
                    id="customform-message"></span>
                <br/>
                <br/>


                <!--unfound page start-->
                <div style="height:1px;background-color:#CCC;"></div>


                <h3>Unfound page message</h3>

                <p>Enter the HTML for a unfound page, shortcodes will work inside form.</p>
				<textarea id="unfoundpage-html" cols="40"
                          rows="5"><?php echo get_option("sr_unfoundpage") ?></textarea>
                <br/>
                <input type="submit" id="unfoundpage-save" class="button-primary" value="Save"/><span
                    id="unfoundpage-message"></span>
                <br/>
                <br/>
                <!--unfound page end-->
                <div style="height:1px;background-color:#CCC;"></div>
                <h3>Mail Chimp Integration</h3>
                <?php $show = false; ?>
                <div id="mc-noaccess"<?php if (get_option('sr-mailchimptoken')) {
                    echo ' style="display:none;"';
                    $show = true;
                } ?>>
                    <p>You haven't given us access to MailChimp yet.</p>
                    API Key: <input type="textbox" id="mailchimpapikey"/>
                    <input type="submit" id="mailchimp-add" class="button-primary" value="Save"/>
                </div>
                <div id="mc-access"<?php if (!$show) {
                    echo ' style="display:none;"';
                } ?>>
                    <p>You have given us access to MailChimp with the following API Key: <span
                            id="mailchimpshowkey"><?php echo get_option('sr-mailchimptoken') ?></span> <input
                            type="submit"
                            id="mailchimp-remove"
                            class="button-primary"
                            value="Revoke"/></p>

                    <p>Select a list to add subscribers to.<select id="mailchimp-list">
                            <?php if ($show) {
                                require_once($seo_rets_plugin->server_plugin_dir . "/includes/MCAPI.class.php");
                                $mailchimp = new MCAPI(get_option('sr-mailchimptoken'), true);
                                $curlist = get_option('sr-mailchimplist');
                                $data = $mailchimp->lists();
                                $lists = $data['data'];
                                if ($curlist == false) {
                                    echo "<option value=\"\" selected=\"selected\">None</option>";
                                } else {
                                    echo "<option value=\"\">None</option>";
                                }
                                foreach ($lists as $list) {
                                    if ($list['id'] == $curlist) {
                                        echo "<option value=\"{$list['id']}\" selected=\"selected\">{$list['name']}</option>\r\n";
                                    } else {
                                        echo "<option value=\"{$list['id']}\">{$list['name']}</option>\r\n";
                                    }
                                }
                            } ?></select>

                        <div id="mcwarning">
                            <?php if ($show) {
                            if (count($lists) == 0) { ?>
                    <p>
                        <span style="color:red;font-weight:bold;">Warning:</span>
                        You currently have no lists
                        set up in your
                        Mail Chimp Account. You will have to setup a list in order for SEO RETS to integrate
                        with Mail Chimp.
                        Please setup a list, and then refresh this page. For more information on how to do this
                        visit the
                        MailChimp help page:
                        <a
                            href='http://kb.mailchimp.com/article/how-do-i-create-a-new-list'>How do I
                            create a new list?</a>
                    </p>
                    <?php
                    }
                    } ?>
                </div>
                <!--            </div>-->
                <input type="submit" id="mailchimp-save" class="button-primary" value="Save"/>
                <span id="mailchimp-message"></span>

            </div>

        </div>

    </div>
</div>
