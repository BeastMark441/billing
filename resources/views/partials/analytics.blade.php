@php
    $yandexId = env('YANDEX_METRIKA_ID');
    $gaId = env('GA_MEASUREMENT_ID');
    $gtmId = env('GTM_CONTAINER_ID');
@endphp

@if($yandexId)
    <meta name="yandex-metrika-id" content="{{ $yandexId }}">
    <noscript>
        <div>
            <img src="https://mc.yandex.ru/watch/{{ urlencode($yandexId) }}" style="position:absolute; left:-9999px;" alt="" />
        </div>
    </noscript>
@endif

@if($gaId)
    <meta name="ga-measurement-id" content="{{ $gaId }}">
@endif

@if($gtmId)
    <meta name="gtm-container-id" content="{{ $gtmId }}">
@endif

<script>
    (function () {
        var yandexMeta = document.querySelector('meta[name="yandex-metrika-id"]');
        var yandexId = yandexMeta ? Number(yandexMeta.getAttribute('content')) : 0;
        if (yandexId) {
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments);
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t);
                a = e.getElementsByTagName(t)[0];
                k.async = 1;
                k.src = r;
                a.parentNode.insertBefore(k, a);
            })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym');

            window.ym(yandexId, 'init', {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
            });
        }

        var gaMeta = document.querySelector('meta[name="ga-measurement-id"]');
        var gaId = gaMeta ? (gaMeta.getAttribute('content') || '') : '';
        var gtmMeta = document.querySelector('meta[name="gtm-container-id"]');
        var gtmId = gtmMeta ? (gtmMeta.getAttribute('content') || '') : '';

        if (gtmId) {
            (function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js',
                });
                var f = d.getElementsByTagName(s)[0];
                var j = d.createElement(s);
                var dl = l !== 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(i) + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', gtmId);

            return;
        }

        if (gaId) {
            var script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gaId);
            document.head.appendChild(script);

            window.dataLayer = window.dataLayer || [];
            function gtag() {
                window.dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', gaId);
        }
    })();
</script>

@if($gtmId)
    <noscript>
        <iframe
            src="https://www.googletagmanager.com/ns.html?id={{ urlencode($gtmId) }}"
            height="0"
            width="0"
            style="display:none;visibility:hidden"
        ></iframe>
    </noscript>
@endif
