<script>
    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[data-unduh-tte]');
        if (!link) {
            return;
        }
        e.preventDefault();
        var berkas = JSON.parse(link.getAttribute('data-unduh-tte'));
        berkas.reduce(function(lanjut, item) {
            return lanjut.then(function() {
                return fetch(item.url, { credentials: 'same-origin' }).then(function(res) {
                    if (!res.ok) {
                        throw new Error('Gagal unduh');
                    }
                    return res.blob().then(function(blob) {
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = item.nama;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(function() { URL.revokeObjectURL(url); }, 2000);
                    });
                });
            });
        }, Promise.resolve()).catch(function() {
            window.location.href = link.href;
        });
    });
</script>
