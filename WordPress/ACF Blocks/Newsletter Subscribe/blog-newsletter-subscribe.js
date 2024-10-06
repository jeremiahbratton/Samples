"use strict";

jQuery(function ($) {
  /**
   * Hubspot Form Init
   */
  hbspt.forms.create({
    formsBaseUrl: 'https://blog.site.com/_hcms/forms/',
    portalId: '12345',
    formId: 'xxxxxxxx',
    formInstanceId: '0000',
    pageId: '123456',
    pageName: 'Sample Page Name',
    contentType: 'blog-post',
    inlineMessage: "Thanks for subscribing!",
    css: '',
    target: '#hs_form_blog_mid',
    formData: {
      cssClass: 'hs-form stacked'
    }
  });

  var handleNewsletterButton = function handleNewsletterButton() {
    $('#jb_blog_mid_subscribe').toggleClass('open');
  };

  $('#blog-newsletter-subscribe').on('click', handleNewsletterButton);
  $('#blog-mid-subscribe-close').on('click', handleNewsletterButton);
});