function plus(id, price) {
    let qty = document.getElementById('qty' + id);
    qty.value = parseInt(qty.value) + 1;

    updateSubtotal(id, price);
    saveQty(id, qty.value);
}

function minus(id, price) {
    let qty = document.getElementById('qty' + id);

    if (qty.value > 1) {
        qty.value--;
        updateSubtotal(id, price);
        saveQty(id, qty.value);
    }
}

function updateSubtotal(id, price) {
    let qty = document.getElementById('qty' + id).value;
    let subtotal = qty * price;

    document.getElementById('subtotal' + id).innerText =
        "Rp " + subtotal.toLocaleString();

    updateTotal(); 
    
}

function saveQty(id, qty) {
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id + '&qty=' + qty
    });
    showToast("Ditambahkan ke keranjang");
}

function updateTotal() {
    let subtotals = document.querySelectorAll('[id^="subtotal"]');
    let total = 0;

    subtotals.forEach(el => {
        let value = el.innerText.replace(/[^0-9]/g, '');
        total += parseInt(value);
    });

    document.getElementById('totalHarga').innerText =
        "Rp " + total.toLocaleString();
}

function plusDetail() {
    let qty = document.getElementById('qty');
    qty.value = parseInt(qty.value) + 1;
}

function minusDetail() {
    let qty = document.getElementById('qty');
    if (qty.value > 1) qty.value--;
}

function deleteItem(id) {
    if (!confirm('Hapus item ini?')) return;

    fetch('/cart/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
    })
    .then(() => {
        // hapus dari UI
        document.getElementById('item' + id).remove();
        updateTotal();
    });
    showToast("Item dihapus");
}

function showToast(text) {
    let toast = document.getElementById('toast');
    toast.innerText = text;
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.display = 'none';
    }, 2000);
}

function validateQty(input) {
    if (input.value < 1 || isNaN(input.value)) {
        input.value = 1;
    }
}

function selesaiOrder(id) {
    if (!confirm('Tandai pesanan selesai?')) return;

    fetch('/admin/orders/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + id
    })
    .then(() => {
    document.getElementById('status' + id).innerText = 'selesai';
});
}

const imgInput = document.querySelector('input[name="image"]');

if (imgInput) {
    imgInput.onchange = function(e) {
        let preview = document.getElementById('preview');
        if (preview) {
            preview.src = URL.createObjectURL(e.target.files[0]);
            preview.style.display = 'block';
        }
    };
}

const input = document.getElementById('imageInput');

if (input) {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('previewImg');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
}

function rateProduct(productId, rating) {
    fetch('/rate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&rating=${rating}`
    })
    .then(() => {
        alert("Rating berhasil ⭐");
    });
}

const stars = document.querySelectorAll('#ratingBox .star');

if (stars.length > 0) {

    let selectedRating = 0;

    stars.forEach((star, index) => {

        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => {
                s.classList.toggle('active', i <= index);
            });
        });

        star.addEventListener('mouseout', () => {
            stars.forEach((s, i) => {
                s.classList.toggle('active', i < selectedRating);
            });
        });

        star.addEventListener('click', () => {
            selectedRating = index + 1;

            const productId = document.getElementById('ratingBox').dataset.product;

            fetch('/rate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${productId}&rating=${selectedRating}`
            })
            .then(() => {
                alert("Rating berhasil ⭐");
                location.reload();
            });
        });

    });

}




// ── Star picker untuk form ulasan di order_detail.php ─────────────
var starLabels = ['','Sangat Buruk','Buruk','Cukup','Bagus','Sangat Bagus!'];

function selectStar(productId, val) {
    var box = document.getElementById('starBox' + productId);
    if (!box) return;
    box.dataset.selected = val;
    var stars = box.querySelectorAll('.star');
    stars.forEach(function(s, i) {
        s.classList.toggle('selected', i < val);
        s.classList.remove('hovered');
    });
    var lbl = document.getElementById('starLabel' + productId);
    if (lbl) lbl.textContent = starLabels[val] || '';
}

function hoverStar(productId, val) {
    var box = document.getElementById('starBox' + productId);
    if (!box) return;
    var sel = parseInt(box.dataset.selected) || 0;
    box.querySelectorAll('.star').forEach(function(s, i) {
        s.classList.toggle('hovered', i < val);
        s.classList.toggle('selected', i < sel);
    });
}

function resetHover(productId) {
    var box = document.getElementById('starBox' + productId);
    if (!box) return;
    var sel = parseInt(box.dataset.selected) || 0;
    box.querySelectorAll('.star').forEach(function(s, i) {
        s.classList.remove('hovered');
        s.classList.toggle('selected', i < sel);
    });
}

// ── Submit ulasan (order_detail.php & detail.php) ─────────────────
function submitReview(productId) {
    // Cari elemen — order_detail menggunakan ID berbeda per item
    var textEl  = document.getElementById('review' + productId) || document.getElementById('reviewText');
    var msgEl   = document.getElementById('reviewMsg' + productId) || document.getElementById('reviewMsg');
    var starBox = document.getElementById('starBox' + productId);

    if (!textEl) return;
    var review = textEl.value.trim();
    if (!review) {
        if (msgEl) { msgEl.textContent = '⚠️ Tulis ulasan terlebih dahulu.'; msgEl.style.color = '#DC2626'; }
        return;
    }

    var rating = starBox ? (parseInt(starBox.dataset.selected) || 0) : 0;

    if (msgEl) { msgEl.textContent = '⏳ Mengirim...'; msgEl.style.color = 'var(--gray)'; }

    var formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('review', review);
    if (rating > 0) formData.append('rating', rating);

    fetch('/review', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString(),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(res => {
        if (msgEl) {
            msgEl.textContent = res.success ? '✓ ' + res.message : '⚠️ ' + res.message;
            msgEl.style.color = res.success ? '#059669' : '#DC2626';
        }
        if (res.success) {
            textEl.value = '';
            if (starBox) { starBox.dataset.selected = 0; starBox.querySelectorAll('.star').forEach(s => s.classList.remove('selected','hovered')); }
            var lbl = document.getElementById('starLabel' + productId);
            if (lbl) lbl.textContent = '';
            setTimeout(() => location.reload(), 1500);
        }
    });
}
