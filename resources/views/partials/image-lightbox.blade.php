@once
<div id="portal-image-lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/95 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Visor de fotografías">
    <button id="portal-lightbox-close" class="absolute right-4 top-4 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20" type="button" aria-label="Cerrar fotografía"><i class="fa-solid fa-xmark"></i></button>
    <button id="portal-lightbox-prev" class="absolute left-3 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 md:left-7" type="button" aria-label="Fotografía anterior"><i class="fa-solid fa-chevron-left"></i></button>
    <figure class="flex max-h-[92vh] max-w-[92vw] flex-col items-center">
        <img id="portal-lightbox-image" class="max-h-[82vh] max-w-full rounded-2xl object-contain shadow-2xl" src="" alt="">
        <figcaption id="portal-lightbox-caption" class="mt-4 text-center text-sm font-bold text-white/80"></figcaption>
    </figure>
    <button id="portal-lightbox-next" class="absolute right-3 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 md:right-7" type="button" aria-label="Fotografía siguiente"><i class="fa-solid fa-chevron-right"></i></button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('portal-image-lightbox');
    const image = document.getElementById('portal-lightbox-image');
    const caption = document.getElementById('portal-lightbox-caption');
    const previous = document.getElementById('portal-lightbox-prev');
    const next = document.getElementById('portal-lightbox-next');
    let gallery = [];
    let current = 0;

    const render = function () {
        const item = gallery[current];
        if (!item) return;
        image.src = item.dataset.lightboxImage;
        image.alt = item.dataset.lightboxCaption || item.querySelector('img')?.alt || '';
        caption.textContent = item.dataset.lightboxCaption || '';
        const multiple = gallery.length > 1;
        previous.hidden = !multiple;
        next.hidden = !multiple;
    };
    const close = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };
    const move = function (step) {
        current = (current + step + gallery.length) % gallery.length;
        render();
    };

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-lightbox-image]');
        if (!trigger) return;
        const group = trigger.dataset.lightboxGroup;
        gallery = [...document.querySelectorAll('[data-lightbox-image][data-lightbox-group="' + CSS.escape(group) + '"]')];
        current = Math.max(0, gallery.indexOf(trigger));
        render();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
    document.getElementById('portal-lightbox-close').addEventListener('click', close);
    previous.addEventListener('click', function () { move(-1); });
    next.addEventListener('click', function () { move(1); });
    modal.addEventListener('click', function (event) { if (event.target === modal) close(); });
    document.addEventListener('keydown', function (event) {
        if (modal.classList.contains('hidden')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') move(-1);
        if (event.key === 'ArrowRight') move(1);
    });
});
</script>
@endpush
@endonce
