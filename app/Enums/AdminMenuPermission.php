<?php

namespace App\Enums;

enum AdminMenuPermission: string
{
    case Siswa           = 'menu.siswa';
    case Pendaftaran     = 'menu.pendaftaran';
    case KelolakKelas    = 'menu.kelola-kelas';
    case PresensiSiswa   = 'menu.presensi-siswa';
    case KesehatanSiswa  = 'menu.kesehatan-siswa';
    case Matrikulasi     = 'menu.matrikulasi';
    case SkalaCapaian    = 'menu.skala-capaian';
    case AgendaBelajar   = 'menu.agenda-belajar';
    case KegiatanRutin   = 'menu.kegiatan-rutin';
    case PencapaianSiswa = 'menu.pencapaian-siswa';
    case Monev           = 'menu.monev';
    case DataPengajar    = 'menu.data-pengajar';
    case PresensiGuru    = 'menu.presensi-guru';
    case Sarana          = 'menu.sarana';
    case MenuMakanan     = 'menu.menu-makanan';
    case AkunCoa         = 'menu.akun-coa';
    case Cashflow        = 'menu.cashflow';
    case JurnalUmum      = 'menu.jurnal-umum';
    case SumberDana      = 'menu.sumber-dana';
    case Rkas            = 'menu.rkas';
    case LaporanRkas     = 'menu.laporan-rkas';
    case BiayaHarian     = 'menu.biaya-harian';
    case Diskon          = 'menu.diskon';
    case RekapPembayaran = 'menu.rekap-pembayaran';
    case KritikSaran     = 'menu.kritik-saran';
    case ChatOrangtua    = 'menu.chat-orangtua';
    case Role            = 'menu.role';
    case Pengguna        = 'menu.pengguna';
    case SettingAkuntansi = 'menu.setting-akuntansi';
    case PengaturanAi    = 'menu.pengaturan-ai';
    case LogAktivitas    = 'menu.log-aktivitas';
}
