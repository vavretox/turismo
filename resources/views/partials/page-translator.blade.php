@php($targetLocale = app()->getLocale() === 'en' ? 'en' : 'es')

@if($targetLocale === 'en')
    <div id="google_translate_element" class="skiptranslate" aria-hidden="true"></div>
    <style>
        #google_translate_element {
            position: fixed !important;
            left: -10000px !important;
            top: -10000px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .goog-te-banner-frame,
        iframe.goog-te-banner-frame { display: none !important; }
        html, body { top: 0 !important; }
        .goog-text-highlight { background: transparent !important; box-shadow: none !important; }
    </style>
    <script>
        (function () {
            const hostname = window.location.hostname;
            const secure = window.location.protocol === 'https:' ? '; Secure' : '';
            let translateSelector = null;
            let refreshTimer = null;
            let translating = false;

            function setEnglishCookie() {
                document.cookie = 'googtrans=; path=/; max-age=0; SameSite=Lax' + secure;

                if (hostname.includes('.') && !/^\d+\.\d+\.\d+\.\d+$/.test(hostname)) {
                    document.cookie = 'googtrans=; path=/; domain=.' + hostname + '; max-age=0; SameSite=Lax' + secure;
                }

                const cookie = 'googtrans=/es/en; path=/; max-age=31536000; SameSite=Lax' + secure;
                document.cookie = cookie;
            }

            function selectEnglish(refresh) {
                if (!translateSelector || translating) {
                    return;
                }

                translating = true;

                if (refresh && translateSelector.value === 'en') {
                    translateSelector.value = '';
                    translateSelector.dispatchEvent(new Event('change', { bubbles: true }));
                }

                window.setTimeout(function () {
                    translateSelector.value = 'en';
                    translateSelector.dispatchEvent(new Event('change', { bubbles: true }));
                    document.documentElement.lang = 'en';
                    document.documentElement.dataset.translationReady = 'true';
                    window.setTimeout(function () {
                        translating = false;
                    }, 600);
                }, refresh ? 120 : 0);
            }

            function scheduleDynamicTranslation() {
                window.clearTimeout(refreshTimer);
                refreshTimer = window.setTimeout(function () {
                    selectEnglish(true);
                }, 500);
            }

            function watchDynamicContent() {
                const observer = new MutationObserver(function (mutations) {
                    const containsNewContent = mutations.some(function (mutation) {
                        if (!mutation.addedNodes.length) {
                            return false;
                        }

                        return Array.from(mutation.addedNodes).some(function (node) {
                            if (node.nodeType === Node.TEXT_NODE) {
                                return node.textContent.trim() !== ''
                                    && !node.parentElement?.closest('font, .skiptranslate');
                            }

                            return node.nodeType === Node.ELEMENT_NODE
                                && !node.matches('font, .skiptranslate')
                                && !node.closest('.skiptranslate');
                        });
                    });

                    if (containsNewContent) {
                        scheduleDynamicTranslation();
                    }
                });

                observer.observe(document.body, { childList: true, subtree: true });

                ['alpine:initialized', 'livewire:navigated', 'turismo:content-updated', 'itinerary:generated']
                    .forEach(function (eventName) {
                        document.addEventListener(eventName, scheduleDynamicTranslation);
                    });
            }

            setEnglishCookie();
            document.documentElement.lang = 'es';

            window.googleTranslateElementInit = function () {
                new google.translate.TranslateElement({
                    pageLanguage: 'es',
                    includedLanguages: 'en',
                    autoDisplay: false
                }, 'google_translate_element');

                let attempts = 0;
                const waitForSelector = function () {
                    translateSelector = document.querySelector('.goog-te-combo');

                    if (translateSelector) {
                        /*
                         * La cookie puede traducir automáticamente. Si todavía
                         * no lo hizo, seleccionamos inglés una sola vez.
                         */
                        if (translateSelector.value !== 'en') {
                            selectEnglish(false);
                        } else {
                            document.documentElement.lang = 'en';
                            document.documentElement.dataset.translationReady = 'true';
                        }

                        watchDynamicContent();
                        return;
                    }

                    if (attempts++ < 60) {
                        window.setTimeout(waitForSelector, 200);
                    } else {
                        document.documentElement.dataset.translationError = 'selector';
                    }
                };

                window.setTimeout(waitForSelector, 100);
            };

            const script = document.createElement('script');
            script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            script.async = true;
            script.onerror = function () {
                document.documentElement.dataset.translationError = 'script';
            };
            document.head.appendChild(script);
        })();
    </script>
@else
    <script>
        (function () {
            const hostname = window.location.hostname;
            const secure = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = 'googtrans=; path=/; max-age=0; SameSite=Lax' + secure;

            if (hostname.includes('.') && !/^\d+\.\d+\.\d+\.\d+$/.test(hostname)) {
                document.cookie = 'googtrans=; path=/; domain=.' + hostname + '; max-age=0; SameSite=Lax' + secure;
            }

            const cookie = 'googtrans=/es/es; path=/; max-age=31536000; SameSite=Lax' + secure;
            document.cookie = cookie;

            document.documentElement.lang = 'es';
        })();
    </script>
@endif
