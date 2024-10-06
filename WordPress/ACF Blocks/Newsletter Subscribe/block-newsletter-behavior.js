/**
 * This file is provided as a sample and related to block-newsletter-button.php
 */

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
    target: '#hs_form_target_module_1531514890824345_block_subscribe_8504',
    formData: {
      cssClass: 'hs-form stacked'
    }
  });
  /**
   * Newsletter button
   */

  var closeButton, newsletterButton, newsLetterBlock, headerOffsetHeight;
  newsLetterBlock = document.querySelector('.block-newsletter');
  headerOffsetHeight = $('.site-header')[0].offsetHeight;

  if ('undefined' !== typeof newsLetterBlock && newsLetterBlock !== null) {
    newsletterButton = newsLetterBlock.querySelector('.block-newsletter__button');
    closeButton = newsLetterBlock.querySelector('.block-newsletter__close-button');
    newsletterButton.addEventListener('click', function (event) {
      event.stopPropagation();
      this.classList.remove('show');
      this.classList.add('hidden');
      this.setAttribute('aria-expanded', 'true');
      closeButton.classList.remove('hidden');
      closeButton.classList.add('show');
      this.nextElementSibling.classList.remove('hidden');
      document.documentElement.scrollTop = this.nextElementSibling.scrollHeight;
      $('.block-newsletter__form input[type="email"]')[0].focus({
        preventScroll: true
      });

      if (window.matchMedia('(max-width:600px)').matches) {
        var navOffset = -1 * headerOffsetHeight - 10;
        var elementY = this.nextElementSibling.getBoundingClientRect().top + scrollY + navOffset;
        window.scrollTo(0, elementY, {
          behavior: 'smooth'
        });
      }
    });
    closeButton.addEventListener('click', function (event) {
      this.classList.add('hidden');
      this.classList.remove('show');
      this.previousElementSibling.classList.add('hidden');
      newsletterButton.classList.remove('hidden');
      newsletterButton.classList.add('show');
      newsletterButton.setAttribute('aria-expanded', 'true');
      newsletterButton.focus({
        preventScroll: true
      });

      if (window.matchMedia('(max-width:600px)').matches) {
        var navOffset = -1 * headerOffsetHeight - 10;
        var elementY = newsLetterBlock.getBoundingClientRect().top + scrollY + navOffset;
        window.scrollTo(0, elementY, {
          behavior: 'smooth'
        });
      }
    });
  }
});