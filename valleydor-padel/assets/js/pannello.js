/* Valley d'Or – pannello di gestione: foto dalla libreria media e prezzo a persona */
(function ($) {
  'use strict';

  /* ---------- foto: aggiungi, togli, riordina ---------- */
  $('.vd-media').each(function () {
    var $box = $(this);
    var $input = $box.find('input[type="hidden"]');
    var $list = $box.find('.vd-media__list');
    var frame;

    function sync() {
      var ids = $list.children('li').map(function () { return $(this).data('id'); }).get();
      $input.val(ids.join(',')).trigger('change');
      $box.find('.vd-media__empty').prop('hidden', ids.length > 0);
      $box.find('.vd-media__clear').prop('hidden', ids.length === 0);
    }

    function item(id, url) {
      return $('<li>').attr('data-id', id).data('id', id)
        .append($('<img alt="">').attr('src', url))
        .append('<button type="button" class="vd-media__remove" aria-label="Togli questa foto">×</button>');
    }

    $list.sortable({ tolerance: 'pointer', update: sync });

    $box.on('click', '.vd-media__remove', function () {
      $(this).closest('li').remove();
      sync();
    });

    $box.on('click', '.vd-media__clear', function () {
      if (window.confirm('Togliere tutte le foto da questa sezione? Le foto restano nella libreria media.')) {
        $list.empty();
        sync();
      }
    });

    $box.on('click', '.vd-media__add', function (e) {
      e.preventDefault();
      if (!frame) {
        frame = wp.media({
          title: $box.data('title'),
          button: { text: 'Aggiungi al sito' },
          library: { type: 'image' },
          multiple: 'add'
        });
        frame.on('select', function () {
          var presenti = $input.val().split(',');
          frame.state().get('selection').each(function (att) {
            var a = att.toJSON();
            if (presenti.indexOf(String(a.id)) !== -1) return;
            var url = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
            $list.append(item(a.id, url));
          });
          sync();
        });
      }
      frame.open();
    });
  });

  /* ---------- elenchi: tariffe, extra, corsi ---------- */
  var seq = 0;
  function nuovaChiave(prefisso) { seq += 1; return prefisso + Date.now().toString(36) + seq; }

  function num(v) {
    var n = parseFloat(String(v).replace('€', '').replace(/\s/g, '').replace(',', '.'));
    return isNaN(n) ? null : n;
  }
  function fmt(n) { return n % 1 === 0 ? String(n) : n.toFixed(2).replace('.', ','); }

  // Stato della riga: spenta, in evidenza, in offerta, prezzo a persona.
  function aggiorna($row) {
    $row.toggleClass('is-off', !$row.find('> .vd-row__flags .vd-js-visibile').prop('checked'));
    $row.toggleClass('is-star', $row.find('> .vd-row__flags .vd-js-evidenza').prop('checked'));
    var $prezzo = $row.find('> .vd-row__fields > .vd-f--euro .vd-js-prezzo');
    var $offerta = $row.find('> .vd-row__fields > .vd-f--euro .vd-js-offerta');
    $row.toggleClass('is-sale', $.trim($offerta.val()) !== '' && $.trim($prezzo.val()) !== '');
    $row.find('.vd-subrow').each(function () {
      var $s = $(this);
      $s.toggleClass('is-sale', $.trim($s.find('.vd-js-offerta').val()) !== '' && $.trim($s.find('.vd-js-prezzo').val()) !== '');
    });
    var $pers = $row.find('.vd-js-persone');
    if ($pers.length) {
      var p = num($.trim($offerta.val()) !== '' ? $offerta.val() : $prezzo.val());
      var g = parseInt($pers.val(), 10);
      $row.find('.vd-persona').text(p !== null && g > 1 ? '= ' + fmt(p / g) + ' € a persona' : '');
    }
  }

  function vuoto($list) {
    $list.find('.vd-list__empty').prop('hidden', $list.find('> .vd-list__rows > .vd-row').length > 0);
  }

  $('.vd-list').each(function () {
    var $list = $(this);
    var $rows = $list.find('> .vd-list__rows');

    $rows.sortable({ handle: '> .vd-row__handle', axis: 'y', tolerance: 'pointer', placeholder: 'vd-row-placeholder', forcePlaceholderSize: true });
    $rows.find('.vd-sublist').sortable({ handle: '.vd-row__handle', axis: 'y', tolerance: 'pointer' });
    $rows.children('.vd-row').each(function () { aggiorna($(this)); });

    $list.on('click', '> .vd-add', function () {
      var html = $list.find('> template.vd-tpl').html().split('__K__').join(nuovaChiave('n'));
      var $row = $(html).appendTo($rows);
      $row.find('.vd-sublist').sortable({ handle: '.vd-row__handle', axis: 'y', tolerance: 'pointer' });
      // Un corso nuovo parte con una riga di prezzo vuota.
      if ($row.hasClass('vd-row--corso')) { $row.find('.vd-add-sub').trigger('click'); }
      aggiorna($row);
      vuoto($list);
      $row.find('input[type="text"]').first().trigger('focus');
      $list.closest('form').trigger('change');
    });
  });

  $(document).on('click', '.vd-add-sub', function () {
    var $f = $(this).closest('.vd-f');
    var html = $f.find('> template.vd-tpl-sub').html().split('__J__').join(nuovaChiave('r'));
    $(html).appendTo($f.find('> .vd-sublist')).find('input').first().trigger('focus');
    $(this).closest('form').trigger('change');
  });

  $(document).on('click', '.vd-list .vd-row__del', function () {
    var $sub = $(this).closest('.vd-subrow');
    if ($sub.length) { $sub.remove(); return; }
    var $row = $(this).closest('.vd-row');
    var nome = $.trim($row.find('.vd-f--nome input').first().val());
    if (!window.confirm('Togliere ' + (nome ? '"' + nome + '"' : 'questa voce') + '?\nSe vuoi solo nasconderla dal sito, spegni "Visibile".')) { return; }
    var $list = $row.closest('.vd-list');
    $row.remove();
    vuoto($list);
    $list.closest('form').trigger('change');
  });

  // Pulsante in evidenza: grigio se spento.
  $(document).on('change', '.vd-row--promo .vd-js-visibile', function () {
    $(this).closest('.vd-row').toggleClass('is-off', !this.checked).toggleClass('is-star', this.checked);
  });

  $(document).on('input change', '.vd-list .vd-row input, .vd-list .vd-row textarea', function () {
    aggiorna($(this).closest('.vd-row'));
  });

  /* ---------- avviso se si esce senza salvare ---------- */
  var cambiato = false;
  $('.vd-panel form').on('input change', function () { cambiato = true; })
    .on('submit', function () { cambiato = false; });
  $(window).on('beforeunload', function () { if (cambiato) return 'Ci sono modifiche non salvate.'; });
})(jQuery);
