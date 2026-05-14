/* SIPRT — global scripts */

(function () {
'use strict';

/* ── Breakpoint utility ── */
var BP = {
    MOBILE:  640,
    DESKTOP: 1024,
    is: function (name) {
        var w = window.innerWidth;
        if (name === 'mobile')  return w < this.MOBILE;
        if (name === 'tablet')  return w >= this.MOBILE && w < this.DESKTOP;
        if (name === 'desktop') return w >= this.DESKTOP;
        return false;
    },
    current: function () {
        if (this.is('mobile'))  return 'mobile';
        if (this.is('tablet'))  return 'tablet';
        return 'desktop';
    },
};
window.SIPRT = window.SIPRT || {};
window.SIPRT.BP = BP;

/* ── Flash alert auto-dismiss ── */
setTimeout(function () {
    var alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(function (el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        } else {
            el.style.opacity = '0';
            el.style.transition = 'opacity .4s';
            setTimeout(function () { el.remove(); }, 400);
        }
    });
}, 4000);

/* ── DataTables global defaults (applied after DT loaded) ── */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $.fn === 'undefined' || typeof $.fn.dataTable === 'undefined') return;

    $.fn.dataTable.ext.errMode = 'none';

    /* Global defaults: search placeholder + page length options */
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            searchPlaceholder: 'Cari...',
        },
    });

    /* Toggle .processing on wrapper so tbody can fade */
    $(document).on('processing.dt', function (e, settings, processing) {
        $(settings.nTableWrapper).toggleClass('processing', processing);
    });
});

})();
