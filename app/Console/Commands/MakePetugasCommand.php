<?php

namespace App\Console\Commands;

use App\Models\Pengguna;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakePetugasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:petugas 
                            {role? : Role petugas (kasi atau kepala)} 
                            {username? : Username unik petugas} 
                            {--nama= : Nama lengkap petugas} 
                            {--password= : Kata sandi akun petugas} 
                            {--nip= : NIP petugas (opsional)} 
                            {--jabatan= : Jabatan petugas (opsional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat akun aparatur desa baru (Kasi Pelayanan atau Kepala Kampung)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Pembuatan Akun Petugas SIRUKTI ===');

        // 1. Role
        $role = $this->argument('role');
        if (! in_array($role, ['kasi', 'kepala'], true)) {
            $role = $this->choice(
                'Pilih peran/role petugas:',
                ['kasi' => 'Kasi Pelayanan', 'kepala' => 'Kepala Kampung'],
                'kasi'
            );
        }

        // 2. Username
        $username = $this->argument('username');
        while (empty($username)) {
            $username = $this->ask('Masukkan username petugas (tanpa spasi):');
            if (empty($username)) {
                $this->error('Username tidak boleh kosong.');
            }
        }

        // Validasi username unik
        $vUsername = Validator::make(['username' => $username], [
            'username' => ['required', 'string', 'max:50', 'unique:pengguna,username'],
        ]);

        if ($vUsername->fails()) {
            $this->error('Gagal: Username "'.$username.'" sudah digunakan oleh akun lain.');

            return self::FAILURE;
        }

        // 3. Nama Lengkap
        $nama = $this->option('nama');
        while (empty($nama)) {
            $nama = $this->ask('Masukkan nama lengkap petugas (beserta gelar jika ada):');
            if (empty($nama)) {
                $this->error('Nama lengkap tidak boleh kosong.');
            }
        }

        // 4. Password
        $password = $this->option('password');
        while (empty($password) || strlen($password) < 8) {
            $password = $this->secret('Masukkan kata sandi akun (minimal 8 karakter):');
            if (strlen($password) < 8) {
                $this->error('Kata sandi harus minimal 8 karakter.');
            }
        }

        // 5. NIP & Jabatan
        $nip = $this->option('nip') ?: null;
        $defaultJabatan = $role === 'kasi' ? 'Kasi Pelayanan' : 'Kepala Kampung';
        $jabatan = $this->option('jabatan') ?: $defaultJabatan;

        // Buat Akun Pengguna
        $petugas = Pengguna::create([
            'nama' => trim($nama),
            'username' => trim($username),
            'password' => Hash::make($password),
            'role' => $role,
            'jabatan' => $jabatan,
            'nip' => $nip,
            'status_aktif' => true,
            'wajib_ganti_password' => false,
        ]);

        $this->newLine();
        $this->info('✓ Akun petugas berhasil dibuat!');
        $this->table(
            ['ID', 'Nama', 'Username', 'Role', 'Jabatan', 'Status'],
            [[
                $petugas->id_pengguna,
                $petugas->nama,
                $petugas->username,
                strtoupper($petugas->role),
                $petugas->jabatan,
                'Aktif',
            ]]
        );

        return self::SUCCESS;
    }
}
