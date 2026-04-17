/* Enamak Makina - Admin JS */

// Confirm delete
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-sil]');
    if (btn) {
        if (!confirm(btn.getAttribute('data-sil') || 'Silmek istediğinize emin misiniz?')) {
            e.preventDefault();
            return false;
        }
    }
});

// Settings tabs
document.querySelectorAll('.settings-tab').forEach(function(t) {
    t.addEventListener('click', function() {
        const target = t.getAttribute('data-tab');
        document.querySelectorAll('.settings-tab').forEach(function(x) { x.classList.remove('active'); });
        document.querySelectorAll('.settings-panel').forEach(function(x) { x.classList.remove('active'); });
        t.classList.add('active');
        document.getElementById(target)?.classList.add('active');
    });
});

// File input preview
document.addEventListener('change', function(e) {
    if (e.target.type === 'file' && e.target.hasAttribute('data-preview')) {
        const id = e.target.getAttribute('data-preview');
        const img = document.getElementById(id);
        if (img && e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) { img.src = ev.target.result; img.style.display = 'block'; };
            reader.readAsDataURL(e.target.files[0]);
        }
    }
});

// User dropdown dışına tıklayınca kapat
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-menu')) {
        document.querySelectorAll('.user-dropdown.show').forEach(function(d) { d.classList.remove('show'); });
    }
});

// Modal aç/kapa
window.modalAc = function(id) {
    document.getElementById(id)?.classList.add('show');
};
window.modalKapa = function(id) {
    document.getElementById(id)?.classList.remove('show');
};
document.querySelectorAll('.modal-backdrop').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === m) m.classList.remove('show');
    });
});

// Slug oluştur
window.slugYap = function(kaynakId, hedefId) {
    const s = document.getElementById(kaynakId);
    const h = document.getElementById(hedefId);
    if (!s || !h) return;
    if (h.value && h.getAttribute('data-manual') === '1') return;
    const tr = {'ç':'c','Ç':'c','ğ':'g','Ğ':'g','ı':'i','İ':'i','ö':'o','Ö':'o','ş':'s','Ş':'s','ü':'u','Ü':'u'};
    let v = (s.value || '').split('').map(function(c) { return tr[c] || c; }).join('').toLowerCase();
    v = v.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    h.value = v;
};
document.querySelectorAll('[data-slug-target]').forEach(function(i) {
    i.addEventListener('input', function() {
        slugYap(i.id, i.getAttribute('data-slug-target'));
    });
});
document.querySelectorAll('[data-slug-source]').forEach(function(i) {
    i.addEventListener('input', function() { i.setAttribute('data-manual', '1'); });
});
