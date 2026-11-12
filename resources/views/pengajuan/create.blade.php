<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Ajukan Surat</x-slot>
    <x-slot name="title">Ajukan Surat Baru — SIRUKTI</x-slot>

    @php
        $layananJson = $daftarLayanan->map(function ($l) {
            return [
                'id' => $l->id_layanan,
                'kode' => $l->kode,
                'nama' => $l->nama_layanan,
                'deskripsi' => $l->deskripsi,
                'syarat' => $l->syaratAktif->map(fn($s) => [
                    'id' => $s->id_syarat,
                    'nama' => $s->nama_syarat,
                    'petunjuk' => $s->petunjuk,
                    'wajib' => (bool) $s->wajib,
                ]),
            ];
        })->values()->all();

        $aktifJson = $pengajuanAktif->mapWithKeys(function ($p, $key) {
            return [$key => [
                'no_pengajuan' => $p->no_pengajuan,
                'status' => $p->status,
            ]];
        })->all();
    @endphp

    <!-- Wizard Component Definition (Defined early to guarantee availability) -->
    <script>
        function getPengajuanWizardState(config) {
            return {
                currentStep: 1,
                stepTitles: {
                    1: 'Pilih Jenis Layanan Surat',
                    2: 'Isi Data Permohonan',
                    3: 'Unggah Berkas Persyaratan',
                    4: 'Tinjau & Kirim Pengajuan'
                },
                stepShortTitles: {
                    1: 'Pilih Layanan',
                    2: 'Isi Data',
                    3: 'Unggah Berkas',
                    4: 'Tinjau & Kirim'
                },
                layananList: config.layananList || [],
                pengajuanAktif: config.pengajuanAktif || {},
                configLayanan: config.configLayanan || {},
                alamatDefault: config.alamatDefault || '',
                storageKey: config.storageKey || 'sirukti_draft_wizard',
                adaDraftTersimpan: false,
                draftData: null,
                draftLabel: '',
                layananDipilih: null,
                kodeLayanan: '',
                formData: {},
                berkasFiles: {},
                berkasPreviews: {},
                errors: {},
                berkasErrors: {},
                setujuPernyataan: false,
                isSubmitting: false,

                init() {
                    // Cek jika ada old input setelah validasi gagal
                    if (config.oldInput && config.oldInput.id_layanan) {
                        const l = this.layananList.find(item => item.id == config.oldInput.id_layanan);
                        if (l) {
                            this.pilihLayanan(l);
                            if (config.oldInput.data_isian) {
                                this.formData = Object.assign({}, config.oldInput.data_isian);
                            }
                        }
                    } else {
                        // Periksa apakah terdapat draf tersimpan di localStorage
                        this.cekDraftTersimpan();
                    }

                    // Watcher untuk otomatis menyimpan draf ke localStorage
                    this.$watch('formData', () => {
                        this.simpanDraft();
                    }, { deep: true });
                    this.$watch('layananDipilih', () => {
                        this.simpanDraft();
                    });
                },

                cekDraftTersimpan() {
                    if (!this.storageKey) return;
                    try {
                        const raw = localStorage.getItem(this.storageKey);
                        if (!raw) return;
                        const draft = JSON.parse(raw);
                        if (draft && draft.id_layanan && draft.formData && Object.keys(draft.formData).length > 0) {
                            const layanan = this.layananList.find(item => item.id == draft.id_layanan);
                            if (layanan && !this.pengajuanAktif[layanan.id]) {
                                this.adaDraftTersimpan = true;
                                this.draftData = draft;
                                const waktu = draft.savedAt ? new Date(draft.savedAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                                this.draftLabel = `${layanan.nama}${waktu ? ' (Tersimpan pukul ' + waktu + ')' : ''}`;
                            }
                        }
                    } catch (e) {
                        console.warn('Gagal membaca draf permohonan dari localStorage:', e);
                    }
                },

                pulihkanDraft() {
                    if (!this.draftData) return;
                    const layanan = this.layananList.find(item => item.id == this.draftData.id_layanan);
                    if (layanan) {
                        this.pilihLayanan(layanan);
                        this.formData = Object.assign({}, this.draftData.formData);
                        if (this.draftData.currentStep && this.draftData.currentStep <= 3) {
                            this.keLangkah(this.draftData.currentStep);
                        } else {
                            this.keLangkah(2);
                        }
                    }
                    this.adaDraftTersimpan = false;
                },

                buangDraft() {
                    try {
                        if (this.storageKey) {
                            localStorage.removeItem(this.storageKey);
                        }
                    } catch (e) {
                        console.warn('Gagal menghapus draf:', e);
                    }
                    this.adaDraftTersimpan = false;
                    this.draftData = null;
                    this.formData = {};
                },

                simpanDraft() {
                    if (!this.storageKey) return;
                    if (!this.layananDipilih) return;
                    try {
                        const draft = {
                            id_layanan: this.layananDipilih.id,
                            currentStep: this.currentStep <= 3 ? this.currentStep : 3,
                            formData: this.formData,
                            savedAt: new Date().toISOString()
                        };
                        localStorage.setItem(this.storageKey, JSON.stringify(draft));
                    } catch (e) {
                        console.warn('Gagal menyimpan draf ke localStorage:', e);
                    }
                },

                hapusDraftSubmit() {
                    this.isSubmitting = true;
                    try {
                        if (this.storageKey) {
                            localStorage.removeItem(this.storageKey);
                        }
                    } catch (e) {
                        console.warn('Gagal membersihkan draf sebelum submit:', e);
                    }
                },

                pilihLayanan(layanan) {
                    if (this.layananDipilih && this.layananDipilih.id !== layanan.id) {
                        this.formData = {};
                    }
                    this.layananDipilih = layanan;
                    this.kodeLayanan = layanan.kode;
                    this.errors = {};
                    this.berkasErrors = {};

                    // Set default form data berdasarkan layanan
                    if (layanan.kode === 'DOM') {
                        if (!this.formData.alamat_domisili) {
                            this.formData.alamat_domisili = this.alamatDefault;
                        }
                    }
                },

                getLayananTheme(kode) {
                    const k = (kode || '').toUpperCase();
                    if (k === 'SKCK') {
                        return {
                            color: 'emerald',
                            badgeBg: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            iconBox: 'bg-emerald-100 text-emerald-700',
                            activeRing: 'border-emerald-500 ring-4 ring-emerald-500/20',
                        };
                    }
                    if (k === 'DOM') {
                        return {
                            color: 'blue',
                            badgeBg: 'bg-blue-50 text-blue-700 border border-blue-200',
                            iconBox: 'bg-blue-100 text-blue-700',
                            activeRing: 'border-blue-500 ring-4 ring-blue-500/20',
                        };
                    }
                    if (k === 'SKTM') {
                        return {
                            color: 'amber',
                            badgeBg: 'bg-amber-50 text-amber-800 border border-amber-200',
                            iconBox: 'bg-amber-100 text-amber-800',
                            activeRing: 'border-amber-500 ring-4 ring-amber-500/20',
                        };
                    }
                    if (k === 'SKU') {
                        return {
                            color: 'purple',
                            badgeBg: 'bg-purple-50 text-purple-700 border border-purple-200',
                            iconBox: 'bg-purple-100 text-purple-700',
                            activeRing: 'border-purple-500 ring-4 ring-purple-500/20',
                        };
                    }
                    return {
                        color: 'slate',
                        badgeBg: 'bg-slate-50 text-slate-700 border border-slate-200',
                        iconBox: 'bg-slate-100 text-slate-700',
                        activeRing: 'border-primary ring-4 ring-primary/20',
                    };
                },

                keLangkah(step) {
                    this.currentStep = step;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                validasiDanKeLangkah3() {
                    this.errors = {};

                    // Validasi form data langkah 2 inline
                    if (this.kodeLayanan === 'SKCK') {
                        if (!this.formData.keperluan || !this.formData.keperluan.trim()) {
                            this.errors['keperluan'] = 'Harap isi keperluan pembuatan SKCK.';
                        }
                    } else if (this.kodeLayanan === 'DOM') {
                        if (!this.formData.alamat_domisili || !this.formData.alamat_domisili.trim()) {
                            this.errors['alamat_domisili'] = 'Harap isi alamat domisili.';
                        }
                        if (this.formData.lama_tinggal_tahun === undefined || this.formData.lama_tinggal_tahun === '' || this.formData.lama_tinggal_tahun === null) {
                            this.errors['lama_tinggal_tahun'] = 'Harap isi lama tinggal (tahun).';
                        }
                        if (!this.formData.keperluan || !this.formData.keperluan.trim()) {
                            this.errors['keperluan'] = 'Harap isi keperluan surat domisili.';
                        }
                    } else if (this.kodeLayanan === 'SKTM') {
                        if (!this.formData.tujuan || !this.formData.tujuan.trim()) {
                            this.errors['tujuan'] = 'Harap isi tujuan / keperluan SKTM.';
                        }
                        if (this.formData.penghasilan_per_bulan === undefined || this.formData.penghasilan_per_bulan === '' || this.formData.penghasilan_per_bulan === null) {
                            this.errors['penghasilan_per_bulan'] = 'Harap isi penghasilan rata-rata per bulan.';
                        }
                        if (this.formData.jumlah_tanggungan === undefined || this.formData.jumlah_tanggungan === '' || this.formData.jumlah_tanggungan === null) {
                            this.errors['jumlah_tanggungan'] = 'Harap isi jumlah tanggungan keluarga.';
                        }
                    } else if (this.kodeLayanan === 'SKU') {
                        if (!this.formData.nama_usaha || !this.formData.nama_usaha.trim()) {
                            this.errors['nama_usaha'] = 'Harap isi nama usaha.';
                        }
                        if (!this.formData.jenis_usaha || !this.formData.jenis_usaha.trim()) {
                            this.errors['jenis_usaha'] = 'Harap isi jenis / bidang usaha.';
                        }
                        if (!this.formData.alamat_usaha || !this.formData.alamat_usaha.trim()) {
                            this.errors['alamat_usaha'] = 'Harap isi alamat tempat usaha.';
                        }
                        if (this.formData.lama_usaha_tahun === undefined || this.formData.lama_usaha_tahun === '' || this.formData.lama_usaha_tahun === null) {
                            this.errors['lama_usaha_tahun'] = 'Harap isi lama usaha (tahun).';
                        }
                    }

                    const errorKeys = Object.keys(this.errors);
                    if (errorKeys.length > 0) {
                        this.$nextTick(() => {
                            const firstKey = errorKeys[0];
                            const el = document.querySelector(`[name="data_isian[${firstKey}]"]`);
                            if (el) {
                                el.focus();
                                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                        return;
                    }

                    this.keLangkah(3);
                },

                validasiDanKeLangkah4() {
                    this.berkasErrors = {};
                    const syaratWajib = (this.layananDipilih?.syarat || []).filter(s => s.wajib);
                    let firstMissingId = null;

                    for (const s of syaratWajib) {
                        if (!this.berkasFiles[s.id]) {
                            this.berkasErrors[s.id] = `Berkas persyaratan '${s.nama}' wajib diunggah.`;
                            if (!firstMissingId) firstMissingId = s.id;
                        }
                    }

                    if (firstMissingId) {
                        this.$nextTick(() => {
                            const el = document.getElementById(`syarat-card-${firstMissingId}`);
                            if (el) {
                                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                        return;
                    }

                    this.keLangkah(4);
                },

                async handleFileUpload(event, syaratId) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validasi ekstensi inline
                    const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                    if (!validTypes.includes(file.type)) {
                        this.berkasErrors[syaratId] = 'Format berkas tidak didukung. Harap pilih gambar JPG, PNG, atau dokumen PDF.';
                        event.target.value = '';
                        return;
                    }

                    let finalFile = file;

                    // Kompresi gambar client-side (Canvas) jika ukuran > 1MB dan bertipe gambar
                    if (file.type.startsWith('image/') && file.size > 1024 * 1024) {
                        try {
                            finalFile = await this.kompresiGambar(file);
                        } catch (e) {
                            console.warn('Gagal mengompresi gambar, menggunakan file asli:', e);
                        }
                    }

                    // Validasi batas maksimal 2MB inline
                    if (finalFile.size > 2 * 1024 * 1024) {
                        this.berkasErrors[syaratId] = `Ukuran berkas (${(finalFile.size / 1024 / 1024).toFixed(1)} MB) melebihi batas 2 MB.`;
                        event.target.value = '';
                        return;
                    }

                    // Hapus error jika berhasil
                    delete this.berkasErrors[syaratId];
                    this.berkasFiles[syaratId] = finalFile;

                    // Pasang file ke elemen input form nyata via DataTransfer
                    const dt = new DataTransfer();
                    dt.items.add(finalFile);
                    const realInput = document.getElementById(`input-real-${syaratId}`);
                    if (realInput) {
                        realInput.files = dt.files;
                    }

                    // Generate thumbnail jika bertipe gambar
                    if (finalFile.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.berkasPreviews[syaratId] = e.target.result;
                        };
                        reader.readAsDataURL(finalFile);
                    } else {
                        this.berkasPreviews[syaratId] = null;
                    }
                },

                hapusBerkas(syaratId) {
                    delete this.berkasFiles[syaratId];
                    delete this.berkasPreviews[syaratId];
                    delete this.berkasErrors[syaratId];
                    const realInput = document.getElementById(`input-real-${syaratId}`);
                    if (realInput) {
                        realInput.value = '';
                    }
                },

                kompresiGambar(file) {
                    return new Promise((resolve, reject) => {
                        const img = new Image();
                        img.src = URL.createObjectURL(file);
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            const maxDimension = 1600;

                            if (width > maxDimension || height > maxDimension) {
                                if (width > height) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                } else {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    const compressedFile = new File([blob], file.name, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now(),
                                    });
                                    resolve(compressedFile);
                                } else {
                                    resolve(file);
                                }
                            }, 'image/jpeg', 0.8);
                        };
                        img.onerror = reject;
                    });
                },

                formatUkuran(bytes) {
                    if (!bytes) return '0 KB';
                    const kb = Math.ceil(bytes / 1024);
                    if (kb >= 1024) {
                        return (kb / 1024).toFixed(1) + ' MB';
                    }
                    return kb + ' KB';
                },

                formatLabel(key) {
                    const map = {
                        keperluan: 'Keperluan',
                        alamat_domisili: 'Alamat Domisili',
                        lama_tinggal_tahun: 'Lama Menetap',
                        tujuan: 'Tujuan Permohonan',
                        penghasilan_per_bulan: 'Penghasilan per Bulan',
                        jumlah_tanggungan: 'Jumlah Tanggungan',
                        nama_usaha: 'Nama Usaha',
                        jenis_usaha: 'Jenis Usaha',
                        alamat_usaha: 'Lokasi Usaha',
                        lama_usaha_tahun: 'Lama Beroperasi'
                    };
                    return map[key] || key.replace(/_/g, ' ');
                },

                formatValue(key, val) {
                    if (key === 'penghasilan_per_bulan') {
                        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                    }
                    if (key.includes('lama') || key.includes('tahun')) {
                        return `${val} Tahun`;
                    }
                    if (key === 'jumlah_tanggungan') {
                        return `${val} Jiwa`;
                    }
                    return val || '-';
                }
            };
        }

        window.pengajuanWizard = getPengajuanWizardState;
        document.addEventListener('alpine:init', () => {
            Alpine.data('pengajuanWizard', getPengajuanWizardState);
        });
    </script>

    <div
        x-data="pengajuanWizard({
            layananList: {{ Js::from($layananJson) }},
            pengajuanAktif: {{ Js::from($aktifJson) }},
            configLayanan: {{ Js::from($configLayanan) }},
            alamatDefault: {{ Js::from($alamatLengkap) }},
            oldInput: {{ Js::from(old()) }},
            storageKey: 'sirukti_draft_wizard_{{ Auth::id() }}'
        })"
        class="space-y-5 max-w-5xl mx-auto"
    >
        <!-- Header Context Area (Compact & Clean) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">Ajukan Surat Baru</h1>
                <p class="text-xs sm:text-sm text-ink-muted mt-0.5">Lengkapi 4 langkah mudah untuk permohonan surat administrasi kependudukan Anda.</p>
            </div>
            <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-bold self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Pelayanan Mandiri
            </div>
        </div>

        <!-- Stepper Tracker Card -->
        <div class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card space-y-3">
            <div class="flex items-center justify-between">
                <!-- Desktop Stepper Label -->
                <span class="hidden sm:flex text-sm font-bold text-primary items-center gap-2">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span x-text="`Langkah ${currentStep} dari 4: ${stepTitles[currentStep]}`">Langkah 1 dari 4: Pilih Jenis Layanan Surat</span>
                </span>

                <!-- Mobile Stepper Label (Ringkas 1/4 · Pilih Layanan) -->
                <div class="sm:hidden flex items-center gap-2 text-xs font-bold text-ink">
                    <span class="px-2 py-0.5 rounded-full bg-primary text-white text-[11px] font-extrabold tracking-wide" x-text="`${currentStep}/4`">1/4</span>
                    <span class="text-primary font-bold" x-text="stepShortTitles[currentStep]">Pilih Layanan</span>
                </div>

                <span class="hidden sm:inline-flex text-xs font-semibold text-ink-muted bg-canvas border border-border-soft px-3 py-1 rounded-full">
                    Estimasi: 3 Menit
                </span>
            </div>

            <!-- Desktop Horizontal Stepper Track (>= sm) -->
            <div class="hidden sm:grid grid-cols-4 gap-2 relative pt-2">
                <div class="absolute top-6 left-[12%] right-[12%] h-[2px] bg-border-soft -z-0">
                    <div class="h-full bg-primary transition-all duration-500" :style="`width: ${((currentStep - 1) / 3) * 100}%`"></div>
                </div>

                <template x-for="(title, stepIdx) in {1: 'Pilih Layanan', 2: 'Isi Data', 3: 'Unggah Berkas', 4: 'Tinjau & Kirim'}">
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div
                            class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 mb-1.5"
                            :class="{
                                'bg-primary text-white ring-4 ring-primary/20 scale-105 shadow-sm': currentStep == stepIdx,
                                'bg-primary text-white': currentStep > stepIdx,
                                'bg-canvas border border-border-soft text-ink-muted': currentStep < stepIdx
                            }"
                        >
                            <span x-show="currentStep <= stepIdx" x-text="stepIdx"></span>
                            <svg x-show="currentStep > stepIdx" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold" :class="currentStep >= stepIdx ? 'text-primary' : 'text-ink-muted'" x-text="title"></span>
                    </div>
                </template>
            </div>

            <!-- Mobile Stepper Progress Bar (Segmented 4 Batang < sm) -->
            <div class="sm:hidden grid grid-cols-4 gap-1.5 pt-0.5">
                <div class="h-1.5 rounded-full transition-all duration-300" :class="currentStep >= 1 ? 'bg-primary' : 'bg-canvas border border-border-soft'"></div>
                <div class="h-1.5 rounded-full transition-all duration-300" :class="currentStep >= 2 ? 'bg-primary' : 'bg-canvas border border-border-soft'"></div>
                <div class="h-1.5 rounded-full transition-all duration-300" :class="currentStep >= 3 ? 'bg-primary' : 'bg-canvas border border-border-soft'"></div>
                <div class="h-1.5 rounded-full transition-all duration-300" :class="currentStep >= 4 ? 'bg-primary' : 'bg-canvas border border-border-soft'"></div>
            </div>
        </div>

        <!-- Global Error Messages if Any -->
        @if ($errors->any())
            <div class="p-4 rounded-card bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc pl-7 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Element Wrapped around All 4 Steps -->
        <form
            id="form-pengajuan"
            method="POST"
            action="{{ route('pengajuan.store') }}"
            enctype="multipart/form-data"
            @submit="hapusDraftSubmit()"
        >
            @csrf

            <!-- Hidden Inputs to bind selected service -->
            <input type="hidden" name="id_layanan" :value="layananDipilih?.id">

            <!-- ============================================================= -->
            <!-- LANGKAH 1: PILIH JENIS LAYANAN SURAT                          -->
            <!-- ============================================================= -->
            <div x-show="currentStep === 1" class="space-y-6">
                <!-- Draf Tersimpan Banner Notice -->
                <div
                    x-show="adaDraftTersimpan"
                    x-cloak
                    class="p-4 rounded-card bg-primary-light border border-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-xs"
                >
                    <div class="flex items-center gap-2.5 text-ink">
                        <div class="w-8 h-8 rounded-lg bg-surface border border-border-soft flex items-center justify-center shrink-0 text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-ink block sm:inline">Ditemukan draf pengajuan tersimpan:</span>
                            <span class="text-ink-muted ml-1" x-text="draftLabel"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                        <button
                            type="button"
                            @click="pulihkanDraft()"
                            class="min-h-[36px] px-3.5 py-1.5 rounded-lg bg-primary text-white font-bold hover:bg-primary-hover transition text-xs shadow-xs"
                        >
                            Lanjutkan Draf
                        </button>
                        <button
                            type="button"
                            @click="buangDraft()"
                            class="min-h-[36px] px-3 py-1.5 rounded-lg bg-surface border border-border-soft text-ink-muted hover:text-error hover:border-error transition text-xs font-semibold"
                        >
                            Hapus Draf
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="item in layananList" :key="item.id">
                        <div
                            x-data="{ showSyarat: false }"
                            class="rounded-card p-4 sm:p-5 border transition duration-200 flex flex-col justify-between"
                            :class="{
                                'bg-canvas border-border-soft opacity-60 cursor-not-allowed': pengajuanAktif[item.id],
                                [getLayananTheme(item.kode).activeRing]: !pengajuanAktif[item.id] && layananDipilih?.id === item.id,
                                'bg-surface border-border-soft shadow-card hover:border-ink-muted/30 cursor-pointer': !pengajuanAktif[item.id] && layananDipilih?.id !== item.id,
                                'bg-surface shadow-card cursor-pointer': !pengajuanAktif[item.id] && layananDipilih?.id === item.id
                            }"
                            @click="!pengajuanAktif[item.id] && pilihLayanan(item)"
                        >
                            <div class="space-y-3">
                                <!-- Top Bar Card: Icon, Kode, and Status/Checkmark -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center font-bold text-lg transition shadow-xs shrink-0"
                                            :class="getLayananTheme(item.kode).iconBox"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider" :class="getLayananTheme(item.kode).badgeBg" x-text="item.kode"></span>
                                            <h3 class="text-base sm:text-lg font-bold text-ink mt-0.5 leading-snug" x-text="item.nama"></h3>
                                        </div>
                                    </div>

                                    <!-- Right Badges: Sedang Diajukan or Selected Checkmark -->
                                    <div class="shrink-0 pt-0.5">
                                        <template x-if="pengajuanAktif[item.id]">
                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 border border-amber-300 px-2.5 py-0.5 rounded-full text-[11px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>Sedang Berjalan</span>
                                            </span>
                                        </template>

                                        <template x-if="!pengajuanAktif[item.id] && layananDipilih?.id === item.id">
                                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-ink-muted leading-relaxed line-clamp-2" x-text="item.deskripsi"></p>

                                <!-- Syarat Dokumen Accordion / Toggle -->
                                <div class="pt-2 border-t border-border-soft/60" @click.stop>
                                    <button
                                        type="button"
                                        @click.stop="showSyarat = !showSyarat"
                                        class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center justify-between w-full py-1 min-h-[36px]"
                                    >
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span x-text="`Lihat Syarat Berkas (${item.syarat ? item.syarat.length : 0})`">Lihat Syarat Berkas</span>
                                        </span>
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="showSyarat ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="showSyarat" x-collapse class="pt-2 pb-1">
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="s in item.syarat" :key="s.id">
                                                <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-canvas border border-border-soft text-ink-muted font-medium" x-text="s.nama"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Info jika sedang berjalan -->
                            <template x-if="pengajuanAktif[item.id]">
                                <div class="mt-3 pt-2.5 border-t border-border-soft text-xs text-amber-900 leading-normal bg-amber-50/60 -mx-4 -mb-4 sm:-mx-5 sm:-mb-5 p-3 rounded-b-card">
                                    Permohonan no. <strong x-text="pengajuanAktif[item.id].no_pengajuan"></strong> masih diproses. Selesaikan permohonan tersebut sebelum mengajukan surat baru.
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Step 1 Sticky Action Bar (Mobile Bottom Fixed / Desktop Static) -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-border-soft p-3 sm:static sm:bg-transparent sm:border-t sm:border-border-soft sm:pt-4 sm:p-0 safe-bottom">
                    <div class="flex items-center justify-between gap-3 max-w-5xl mx-auto">
                        <a
                            href="{{ route('dashboard') }}"
                            class="px-4 sm:px-5 h-11 rounded-btn bg-canvas text-ink hover:bg-gray-200 text-xs sm:text-sm font-semibold transition flex items-center justify-center border border-border-soft shrink-0"
                        >
                            Batal
                        </a>
                        <button
                            type="button"
                            :disabled="!layananDipilih"
                            @click="keLangkah(2)"
                            class="flex-1 sm:flex-none sm:px-7 h-11 rounded-btn bg-primary text-white font-bold text-xs sm:text-sm shadow-featured hover:bg-primary-hover active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2"
                        >
                            <span>Lanjut: Isi Formulir</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- LANGKAH 2: ISI DATA PERMOHONAN                               -->
            <!-- ============================================================= -->
            <div x-show="currentStep === 2" class="space-y-5">
                <!-- Compact Data Diri Pemohon (Strip 1 Baris di Mobile dengan Accordion) -->
                <div x-data="{ expandedProfil: false }" class="bg-canvas border border-border-soft rounded-card p-3 sm:p-4 transition">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-primary-light text-primary flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="truncate">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-ink truncate">{{ $user->nama }}</span>
                                    <span class="hidden sm:inline-block text-[11px] font-mono text-ink-muted">· NIK {{ $warga->nik }}</span>
                                </div>
                                <span class="sm:hidden block text-[11px] font-mono text-ink-muted truncate">NIK: {{ $warga->nik }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                type="button"
                                @click="expandedProfil = !expandedProfil"
                                class="text-xs font-semibold text-ink-muted hover:text-ink px-2 py-1 rounded-lg hover:bg-surface border border-border-soft transition flex items-center gap-1"
                            >
                                <span x-text="expandedProfil ? 'Tutup' : 'Rincian'">Rincian</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="expandedProfil ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <a href="{{ route('profile.edit') }}" class="text-xs text-primary font-bold hover:underline px-1 py-1">
                                Ubah
                            </a>
                        </div>
                    </div>

                    <!-- Collapsible Full Profile Details -->
                    <div x-show="expandedProfil" x-collapse class="mt-3 pt-3 border-t border-border-soft">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-ink-muted block text-[11px]">Nomor KK:</span>
                                <span class="font-bold font-mono text-ink text-xs">{{ $warga->no_kk ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-[11px]">No. WhatsApp / HP:</span>
                                <span class="font-bold font-mono text-ink text-xs">{{ $user->no_hp ?? '-' }}</span>
                            </div>
                            <div class="sm:col-span-2 md:col-span-1">
                                <span class="text-ink-muted block text-[11px]">Alamat Domisili:</span>
                                <span class="font-medium text-ink text-xs">{{ $alamatLengkap }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Isian Khusus Layanan -->
                <div class="bg-surface rounded-card p-4 sm:p-6 border border-border-soft shadow-card space-y-4">
                    <div class="pb-3 border-b border-border-soft">
                        <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                            <span>Formulir Isian:</span>
                            <span class="text-primary" x-text="layananDipilih?.nama"></span>
                        </h3>
                        <p class="text-xs text-ink-muted mt-0.5">Lengkapi rincian keterangan yang diperlukan untuk penerbitan surat ini.</p>
                    </div>

                    <!-- 1. Formulir Khusus SKCK -->
                    <template x-if="kodeLayanan === 'SKCK'">
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <x-input-label for="skck_keperluan">
                                    Keperluan Pembuatan SKCK <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <textarea
                                    id="skck_keperluan"
                                    name="data_isian[keperluan]"
                                    x-model="formData.keperluan"
                                    @input="if ($data.errors) delete $data.errors['keperluan']"
                                    rows="3"
                                    maxlength="150"
                                    required
                                    placeholder="Contoh: Persyaratan melamar pekerjaan sebagai Tenaga Medis di RSUD / BUMN"
                                    class="w-full px-4 py-3 bg-surface text-ink text-sm sm:text-base rounded-btn border transition placeholder:text-ink-muted/60 focus:border-primary focus:ring-4 focus:ring-primary/15"
                                    :class="$data.errors?.keperluan ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                ></textarea>
                                <p x-show="$data.errors?.keperluan" x-text="$data.errors?.keperluan" class="text-xs text-error font-medium"></p>
                                <div class="flex items-center justify-between text-xs text-ink-muted">
                                    <span>Jelaskan tujuan penggunaan SKCK secara ringkas dan jelas.</span>
                                    <span x-text="`${(formData.keperluan || '').length}/150`"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 2. Formulir Khusus DOM -->
                    <template x-if="kodeLayanan === 'DOM'">
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <x-input-label for="dom_alamat">
                                    Alamat Domisili Lengkap <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <input
                                    id="dom_alamat"
                                    type="text"
                                    name="data_isian[alamat_domisili]"
                                    x-model="formData.alamat_domisili"
                                    @input="if ($data.errors) delete $data.errors['alamat_domisili']"
                                    required
                                    placeholder="Contoh: Jl. Poros Utama No. 12, RT 001 / RW 001, Dusun I A"
                                    class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                    :class="$data.errors?.alamat_domisili ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                />
                                <p x-show="$data.errors?.alamat_domisili" x-text="$data.errors?.alamat_domisili" class="text-xs text-error font-medium"></p>
                                <p class="text-xs text-ink-muted">Alamat domisili tempat tinggal Anda saat ini.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label for="dom_lama">
                                        Lama Tinggal (Tahun) <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="dom_lama"
                                        name="data_isian[lama_tinggal_tahun]"
                                        type="number"
                                        min="0"
                                        max="100"
                                        x-model="formData.lama_tinggal_tahun"
                                        @input="if ($data.errors) delete $data.errors['lama_tinggal_tahun']"
                                        required
                                        placeholder="Contoh: 5"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.lama_tinggal_tahun ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.lama_tinggal_tahun" x-text="$data.errors?.lama_tinggal_tahun" class="text-xs text-error font-medium"></p>
                                </div>

                                <div class="space-y-1.5">
                                    <x-input-label for="dom_keperluan">
                                        Keperluan Surat Domisili <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="dom_keperluan"
                                        type="text"
                                        name="data_isian[keperluan]"
                                        x-model="formData.keperluan"
                                        @input="if ($data.errors) delete $data.errors['keperluan']"
                                        maxlength="150"
                                        required
                                        placeholder="Contoh: Pendaftaran sekolah / Pembuatan rekening bank"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.keperluan ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.keperluan" x-text="$data.errors?.keperluan" class="text-xs text-error font-medium"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 3. Formulir Khusus SKTM -->
                    <template x-if="kodeLayanan === 'SKTM'">
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <x-input-label for="sktm_tujuan">
                                    Tujuan / Keperluan Permohonan SKTM <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <input
                                    id="sktm_tujuan"
                                    type="text"
                                    name="data_isian[tujuan]"
                                    x-model="formData.tujuan"
                                    @input="if ($data.errors) delete $data.errors['tujuan']"
                                    maxlength="150"
                                    required
                                    placeholder="Contoh: Persyaratan Beasiswa KIP Kuliah / Keringanan Biaya RS"
                                    class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                    :class="$data.errors?.tujuan ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                />
                                <p x-show="$data.errors?.tujuan" x-text="$data.errors?.tujuan" class="text-xs text-error font-medium"></p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label for="sktm_penghasilan">
                                        Penghasilan Rata-rata per Bulan (Rp) <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="sktm_penghasilan"
                                        name="data_isian[penghasilan_per_bulan]"
                                        type="number"
                                        min="0"
                                        x-model="formData.penghasilan_per_bulan"
                                        @input="if ($data.errors) delete $data.errors['penghasilan_per_bulan']"
                                        required
                                        placeholder="Contoh: 1200000"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.penghasilan_per_bulan ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.penghasilan_per_bulan" x-text="$data.errors?.penghasilan_per_bulan" class="text-xs text-error font-medium"></p>
                                    <p class="text-xs text-ink-muted">Tuliskan angka tanpa titik atau koma.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <x-input-label for="sktm_tanggungan">
                                        Jumlah Tanggungan Keluarga (Jiwa) <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="sktm_tanggungan"
                                        name="data_isian[jumlah_tanggungan]"
                                        type="number"
                                        min="0"
                                        max="20"
                                        x-model="formData.jumlah_tanggungan"
                                        @input="if ($data.errors) delete $data.errors['jumlah_tanggungan']"
                                        required
                                        placeholder="Contoh: 3"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.jumlah_tanggungan ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.jumlah_tanggungan" x-text="$data.errors?.jumlah_tanggungan" class="text-xs text-error font-medium"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 4. Formulir Khusus SKU -->
                    <template x-if="kodeLayanan === 'SKU'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label for="sku_nama">
                                        Nama Usaha <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="sku_nama"
                                        type="text"
                                        name="data_isian[nama_usaha]"
                                        x-model="formData.nama_usaha"
                                        @input="if ($data.errors) delete $data.errors['nama_usaha']"
                                        maxlength="100"
                                        required
                                        placeholder="Contoh: Toko Berkah Mandiri"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.nama_usaha ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.nama_usaha" x-text="$data.errors?.nama_usaha" class="text-xs text-error font-medium"></p>
                                </div>

                                <div class="space-y-1.5">
                                    <x-input-label for="sku_jenis">
                                        Jenis / Bidang Usaha <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <input
                                        id="sku_jenis"
                                        type="text"
                                        name="data_isian[jenis_usaha]"
                                        x-model="formData.jenis_usaha"
                                        @input="if ($data.errors) delete $data.errors['jenis_usaha']"
                                        maxlength="100"
                                        required
                                        placeholder="Contoh: Perdagangan Sembako / Bengkel Motor"
                                        class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                        :class="$data.errors?.jenis_usaha ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                    />
                                    <p x-show="$data.errors?.jenis_usaha" x-text="$data.errors?.jenis_usaha" class="text-xs text-error font-medium"></p>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <x-input-label for="sku_alamat">
                                    Alamat / Lokasi Tempat Usaha <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <input
                                    id="sku_alamat"
                                    type="text"
                                    name="data_isian[alamat_usaha]"
                                    x-model="formData.alamat_usaha"
                                    @input="if ($data.errors) delete $data.errors['alamat_usaha']"
                                    maxlength="255"
                                    required
                                    placeholder="Contoh: Jl. Diponegoro No. 8, Dusun I A, Rukti Harjo"
                                    class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                    :class="$data.errors?.alamat_usaha ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                />
                                <p x-show="$data.errors?.alamat_usaha" x-text="$data.errors?.alamat_usaha" class="text-xs text-error font-medium"></p>
                            </div>

                            <div class="space-y-1.5 sm:max-w-xs">
                                <x-input-label for="sku_lama">
                                    Lama Usaha Berjalan (Tahun) <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <input
                                    id="sku_lama"
                                    name="data_isian[lama_usaha_tahun]"
                                    type="number"
                                    min="0"
                                    max="100"
                                    x-model="formData.lama_usaha_tahun"
                                    @input="if ($data.errors) delete $data.errors['lama_usaha_tahun']"
                                    required
                                    placeholder="Contoh: 2"
                                    class="h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 w-full"
                                    :class="$data.errors?.lama_usaha_tahun ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                                />
                                <p x-show="$data.errors?.lama_usaha_tahun" x-text="$data.errors?.lama_usaha_tahun" class="text-xs text-error font-medium"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Draft Auto-save Indicator & Reset Option -->
                    <div class="pt-3 border-t border-border-soft flex items-center justify-between text-xs text-ink-muted">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Draf formulir tersimpan otomatis di perangkat ini.</span>
                        </div>
                        <button
                            type="button"
                            @click="buangDraft()"
                            class="text-ink-muted hover:text-error hover:underline transition text-xs font-medium"
                            title="Kosongkan formulir"
                        >
                            Kosongkan Formulir
                        </button>
                    </div>
                </div>

                <!-- Step 2 Sticky Action Bar -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-border-soft p-3 sm:static sm:bg-transparent sm:border-t sm:border-border-soft sm:pt-4 sm:p-0 safe-bottom">
                    <div class="flex items-center justify-between gap-3 max-w-5xl mx-auto">
                        <button
                            type="button"
                            @click="keLangkah(1)"
                            class="px-4 sm:px-5 h-11 rounded-btn bg-canvas text-ink hover:bg-gray-200 text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-1.5 border border-border-soft shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </button>
                        <button
                            type="button"
                            @click="validasiDanKeLangkah3()"
                            class="flex-1 sm:flex-none sm:px-7 h-11 rounded-btn bg-primary text-white font-bold text-xs sm:text-sm shadow-featured hover:bg-primary-hover active:scale-95 transition flex items-center justify-center gap-2"
                        >
                            <span>Lanjut: Unggah Berkas</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- LANGKAH 3: UNGGAH BERKAS PERSYARATAN                          -->
            <!-- ============================================================= -->
            <div x-show="currentStep === 3" class="space-y-5">
                <div class="p-3.5 bg-primary-light border border-primary/20 rounded-card flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-xs text-primary-dark leading-relaxed">
                        <strong>Petunjuk:</strong> Format yang didukung <strong>JPG, PNG, atau PDF</strong> maks. <strong>2 MB</strong> per berkas. Foto langsung dari kamera smartphone otomatis dikompresi agar hemat kuota.
                    </div>
                </div>

                <!-- Slot Berkas Persyaratan -->
                <div class="space-y-4">
                    <template x-for="syarat in (layananDipilih?.syarat || [])" :key="syarat.id">
                        <div
                            :id="`syarat-card-${syarat.id}`"
                            class="bg-surface rounded-card p-4 sm:p-5 border shadow-card space-y-3 transition"
                            :class="$data.berkasErrors?.[syarat.id] ? 'border-error ring-1 ring-error' : 'border-border-soft'"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-canvas border border-border-soft flex items-center justify-center text-primary font-bold shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm sm:text-base font-bold text-ink leading-snug" x-text="syarat.nama"></h4>
                                        <p class="text-xs text-ink-muted mt-0.5 leading-relaxed" x-text="syarat.petunjuk"></p>
                                    </div>
                                </div>
                                <div class="shrink-0 pt-0.5">
                                    <template x-if="syarat.wajib">
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">Wajib</span>
                                    </template>
                                    <template x-if="!syarat.wajib">
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-canvas text-ink-muted border border-border-soft">Opsional</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Upload Box / Preview Box -->
                            <div class="pt-1">
                                <!-- State 1: File Belum Diunggah -->
                                <div
                                    x-show="!berkasFiles[syarat.id]"
                                    class="border-2 border-dashed border-border-soft rounded-2xl p-4 sm:p-5 text-center hover:border-primary/60 transition bg-canvas/40"
                                >
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <svg class="w-7 h-7 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <div class="text-xs text-ink-muted">
                                            Ambil foto langsung atau pilih berkas dokumen
                                        </div>

                                        <!-- Mobile Stacked Buttons (target sentuh >= 44px) -->
                                        <div class="grid grid-cols-2 gap-2 w-full max-w-xs mt-2">
                                            <!-- Tombol Kamera (Mobile capture) -->
                                            <label class="h-11 px-3 rounded-btn bg-surface border border-border-soft hover:bg-gray-100 text-ink text-xs font-bold cursor-pointer transition flex items-center justify-center gap-1.5 shadow-xs">
                                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Ambil Foto</span>
                                                <input
                                                    type="file"
                                                    accept="image/*"
                                                    capture="environment"
                                                    class="sr-only"
                                                    @change="handleFileUpload($event, syarat.id)"
                                                >
                                            </label>

                                            <!-- Tombol Galeri / Dokumen -->
                                            <label class="h-11 px-3 rounded-btn bg-primary text-white hover:bg-primary-hover text-xs font-bold cursor-pointer transition flex items-center justify-center gap-1.5 shadow-xs">
                                                <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Pilih Berkas</span>
                                                <input
                                                    type="file"
                                                    accept="image/jpeg,image/png,application/pdf"
                                                    class="sr-only"
                                                    @change="handleFileUpload($event, syarat.id)"
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- State 2: File Sudah Diunggah (Preview Box) -->
                                <div
                                    x-show="berkasFiles[syarat.id]"
                                    class="bg-canvas border border-border-soft rounded-2xl p-3 sm:p-4 flex items-center justify-between gap-3"
                                >
                                    <div class="flex items-center gap-3 overflow-hidden min-w-0">
                                        <!-- Thumbnail Image or PDF Icon -->
                                        <div class="w-12 h-12 rounded-xl bg-surface border border-border-soft flex items-center justify-center shrink-0 overflow-hidden shadow-xs">
                                            <template x-if="berkasPreviews[syarat.id]">
                                                <img :src="berkasPreviews[syarat.id]" alt="Preview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!berkasPreviews[syarat.id]">
                                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                                                </svg>
                                            </template>
                                        </div>

                                        <div class="truncate text-xs min-w-0">
                                            <p class="font-bold text-ink truncate" x-text="berkasFiles[syarat.id]?.name"></p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-ink-muted" x-text="formatUkuran(berkasFiles[syarat.id]?.size)"></span>
                                                <span class="text-emerald-700 font-bold">✓ Terlampir</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <label class="h-10 px-3 rounded-lg bg-surface border border-border-soft text-xs font-semibold text-ink hover:bg-gray-100 cursor-pointer transition flex items-center justify-center">
                                            Ganti
                                            <input
                                                type="file"
                                                accept="image/jpeg,image/png,application/pdf"
                                                class="sr-only"
                                                @change="handleFileUpload($event, syarat.id)"
                                            >
                                        </label>
                                        <button
                                            type="button"
                                            @click="hapusBerkas(syarat.id)"
                                            class="w-10 h-10 rounded-lg text-error hover:bg-red-50 transition flex items-center justify-center"
                                            title="Hapus berkas"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Inline Error untuk Berkas Syarat -->
                                <p x-show="$data.berkasErrors?.[syarat.id]" class="text-xs text-error font-medium mt-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span x-text="$data.berkasErrors?.[syarat.id]"></span>
                                </p>
                            </div>

                            <!-- Native Hidden Input to hold compressed file for form submission -->
                            <input
                                type="file"
                                :name="`berkas[${syarat.id}]`"
                                :id="`input-real-${syarat.id}`"
                                class="sr-only"
                            >
                        </div>
                    </template>
                </div>

                <!-- Step 3 Sticky Action Bar -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-border-soft p-3 sm:static sm:bg-transparent sm:border-t sm:border-border-soft sm:pt-4 sm:p-0 safe-bottom">
                    <div class="flex items-center justify-between gap-3 max-w-5xl mx-auto">
                        <button
                            type="button"
                            @click="keLangkah(2)"
                            class="px-4 sm:px-5 h-11 rounded-btn bg-canvas text-ink hover:bg-gray-200 text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-1.5 border border-border-soft shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </button>
                        <button
                            type="button"
                            @click="validasiDanKeLangkah4()"
                            class="flex-1 sm:flex-none sm:px-7 h-11 rounded-btn bg-primary text-white font-bold text-xs sm:text-sm shadow-featured hover:bg-primary-hover active:scale-95 transition flex items-center justify-center gap-2"
                        >
                            <span>Lanjut: Tinjau</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- LANGKAH 4: TINJAU DAN KIRIM                                   -->
            <!-- ============================================================= -->
            <div x-show="currentStep === 4" class="space-y-5">
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-card flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-xs text-emerald-900 leading-relaxed">
                        Silakan periksa kembali kelengkapan dan kebenaran data Anda sebelum dikirimkan ke loket pelayanan Balai Kampung Rukti Harjo.
                    </div>
                </div>

                <div class="bg-surface rounded-card p-4 sm:p-6 border border-border-soft shadow-card space-y-5">
                    <!-- Seksi 1: Layanan Dipilih -->
                    <div class="pb-4 border-b border-border-soft flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-ink-muted uppercase tracking-wider font-bold">Layanan Yang Diajukan:</span>
                            <h3 class="text-base sm:text-lg font-extrabold text-primary" x-text="layananDipilih?.nama"></h3>
                        </div>
                        <button type="button" @click="keLangkah(1)" class="text-xs text-primary font-bold hover:underline">
                            Ganti
                        </button>
                    </div>

                    <!-- Seksi 2: Data Pemohon -->
                    <div class="pb-4 border-b border-border-soft space-y-2">
                        <span class="text-[11px] text-ink-muted uppercase tracking-wider font-bold">Data Identitas Pemohon:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                            <div>
                                <span class="text-ink-muted block text-[11px]">Nama Pemohon:</span>
                                <span class="font-bold text-ink text-xs sm:text-sm">{{ $user->nama }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-[11px]">NIK:</span>
                                <span class="font-bold font-mono text-ink text-xs sm:text-sm">{{ $warga->nik }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-[11px]">Nomor WhatsApp:</span>
                                <span class="font-bold font-mono text-ink text-xs">{{ $user->no_hp ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-[11px]">Alamat Domisili:</span>
                                <span class="font-medium text-ink text-xs">{{ $alamatLengkap }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 3: Isian Formulir -->
                    <div class="pb-4 border-b border-border-soft space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-ink-muted uppercase tracking-wider font-bold">Rincian Isian Surat:</span>
                            <button type="button" @click="keLangkah(2)" class="text-xs text-primary font-bold hover:underline">
                                Edit Isian
                            </button>
                        </div>

                        <div class="bg-canvas border border-border-soft rounded-2xl p-3 sm:p-4 text-xs space-y-2">
                            <template x-for="(val, key) in formData" :key="key">
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-0.5 sm:gap-2 py-1 border-b border-border-soft/60 last:border-0">
                                    <span class="text-ink-muted capitalize font-medium text-[11px]" x-text="formatLabel(key)"></span>
                                    <span class="font-bold text-ink text-xs sm:text-sm sm:text-right" x-text="formatValue(key, val)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Seksi 4: Berkas Terlampir -->
                    <div class="pb-4 border-b border-border-soft space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-ink-muted uppercase tracking-wider font-bold">Berkas Persyaratan Terlampir:</span>
                            <button type="button" @click="keLangkah(3)" class="text-xs text-primary font-bold hover:underline">
                                Kelola Berkas
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <template x-for="syarat in (layananDipilih?.syarat || [])" :key="syarat.id">
                                <div class="bg-canvas border border-border-soft rounded-xl p-2.5 sm:p-3 flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-lg bg-surface border border-border-soft flex items-center justify-center shrink-0">
                                        <template x-if="berkasPreviews[syarat.id]">
                                            <img :src="berkasPreviews[syarat.id]" class="w-full h-full object-cover rounded-lg">
                                        </template>
                                        <template x-if="!berkasPreviews[syarat.id]">
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="truncate text-xs min-w-0">
                                        <span class="font-bold text-ink block truncate" x-text="syarat.nama"></span>
                                        <span class="text-ink-muted truncate block text-[11px]" x-text="berkasFiles[syarat.id]?.name || 'Belum diunggah'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Seksi 5: Checkbox Pernyataan Hukum (Ringkas) -->
                    <div class="p-3.5 bg-canvas border border-border-soft rounded-2xl">
                        <label class="flex items-start gap-2.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                x-model="setujuPernyataan"
                                required
                                class="w-5 h-5 mt-0.5 rounded text-primary focus:ring-primary border-border-soft shrink-0"
                            >
                            <span class="text-xs text-ink leading-relaxed font-medium">
                                Saya menyatakan bahwa seluruh data dan berkas yang saya lampirkan adalah <strong>benar, sah, dan sesuai kondisi sebenarnya</strong>.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Step 4 Sticky Action Bar -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-border-soft p-3 sm:static sm:bg-transparent sm:border-t sm:border-border-soft sm:pt-4 sm:p-0 safe-bottom">
                    <div class="flex items-center justify-between gap-3 max-w-5xl mx-auto">
                        <button
                            type="button"
                            @click="keLangkah(3)"
                            class="px-4 sm:px-5 h-11 sm:h-12 rounded-btn bg-canvas text-ink hover:bg-gray-200 text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-1.5 border border-border-soft shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </button>

                        <button
                            type="submit"
                            :disabled="!setujuPernyataan || isSubmitting"
                            class="flex-1 sm:flex-none sm:px-8 h-11 sm:h-12 rounded-btn bg-primary text-white font-bold text-xs sm:text-sm shadow-featured hover:bg-primary-hover active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2"
                        >
                            <template x-if="isSubmitting">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>Mengirim...</span>
                                </div>
                            </template>
                            <template x-if="!isSubmitting">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span>Kirim Surat</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
