import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

window.liveFilter = function(config = {}) {
    return {
        showFilters: config.hasActiveFilters || false,
        loading: false,
        activeCount: config.activeCount || 0,
        timeout: null,

        init() {
            // Tangkap klik paginasi di dalam container agar tidak reload
            this.$el.addEventListener('click', (e) => {
                const link = e.target.closest('#data-container nav a');
                if (link && link.href) {
                    e.preventDefault();
                    this.fetchData(link.href);
                }
            });
        },

        countActiveFilters() {
            const form = this.$refs.filterForm;
            if (!form) return;
            let count = 0;
            form.querySelectorAll('input[type=date], select').forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    count++;
                }
            });
            this.activeCount = count;
        },

        applyLive() {
            this.countActiveFilters();
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                const form = this.$refs.filterForm;
                if (!form) return;
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value && value.toString().trim() !== '') {
                        params.append(key, value);
                    }
                }
                const url = form.action + (params.toString() ? '?' + params.toString() : '');
                this.fetchData(url);
            }, 300);
        },

        resetFilters() {
            const form = this.$refs.filterForm;
            if (!form) return;
            form.querySelectorAll('input:not([type=hidden]), select').forEach(input => {
                input.value = '';
            });
            this.activeCount = 0;
            this.showFilters = false;
            this.fetchData(form.action);
        },

        async fetchData(url) {
            this.loading = true;
            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Gagal memuat data');
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newContainer = doc.getElementById('data-container');
                const curContainer = document.getElementById('data-container');
                if (newContainer && curContainer) {
                    curContainer.innerHTML = newContainer.innerHTML;
                }

                // Perbarui badge total data jika ada
                const newCount = doc.getElementById('total-count-badge');
                const curCount = document.getElementById('total-count-badge');
                if (newCount && curCount) {
                    curCount.innerHTML = newCount.innerHTML;
                }

                window.history.replaceState(null, '', url);
            } catch (err) {
                console.error('Filter fetch error:', err);
            } finally {
                this.loading = false;
            }
        }
    };
};

Alpine.start();

