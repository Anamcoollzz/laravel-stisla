/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

// Ensure Bootstrap modals are attached directly to <body> so they are not trapped inside stacking contexts
$(document).on('show.bs.modal', '.modal', function () {
  if (!$(this).parent().is('body')) {
    $(this).appendTo('body');
  }
});
