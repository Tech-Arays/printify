(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

$(document).ready(function () {
  $('.next-page-arrow').on('click', function (e) {
    e.preventDefault();
    $.fn.fullpage.moveSectionDown();
  });
  var mobileMenu = $('.mobile-menu'),
      mobileMenuIcon = $('.mobile-menu-icon');
  $('.mobile-menu-trigger').find('.trigger').on('click', function (e) {
    e.preventDefault();
    $(this).toggleClass('is-triggered');
    mobileMenuIcon.toggleClass('is-clicked');
    if (mobileMenu.hasClass('is-visible')) {
      mobileMenu.removeClass('is-visible');
    } else {
      mobileMenu.addClass('is-visible');
    }
  });
  $('.mobile-menu .has-sub-menu > a').on('click', function (e) {
    e.preventDefault();
    $(this).siblings('.sub-menu').toggleClass('open');
    $(this).children('.sign').toggleClass('open');
  });
  $('.mobile-menu a').not('.mobile-menu .has-sub-menu > a').on('click', function (e) {
    mobileMenuIcon.removeClass('is-clicked');
    mobileMenu.removeClass('is-visible');
  });
  $('.smooth-scroll').find('a').on('click', function (e) {
    e.preventDefault();
    $('body,html').animate({ 'scrollTop': $(this.hash).offset().top }, 600);
  });
  $('.section').scrollSpy();
  var header = $('.landing-header');
  $('.section').on('scrollSpy:enter', function () {
    $(this).find('.animated').addClass('fadeInUp');
    if ($('a[href="#' + $(this).attr('id') + '"]')[[0]]) {
      $('.smooth-scroll').find('a').removeClass('active');
      $('.smooth-scroll').find('a[href="#' + $(this).attr('id') + '"]').addClass('active');
    }
  });
  $('.scheduleCall').on('click',function(){
     
      $('.calendlyEmbed').toggle();

  });
  
  $('.schedulePCall').on('click',function(){
     
      $('.calendlyPhoneEmbed').toggle();

  });
});

},{}]},{},[1]);

//# sourceMappingURL=landing.js.map