<?php

$modelsDir = 'd:/Project/Project Real/Firstudio/paud-laravel/app/Models';

$relationships = [
    'User' => "public function lembaga() { return \$this->belongsTo(Lembaga::class); }\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function pengajar() { return \$this->hasOne(Pengajar::class); }\n    public function anaks() { return \$this->hasMany(Anak::class, 'user_id'); }\n    public function kritikSarans() { return \$this->hasMany(KritikSaran::class); }",
    'Lembaga' => "protected \$guarded = [];\n    public function sekolahs() { return \$this->hasMany(Sekolah::class); }\n    public function users() { return \$this->hasMany(User::class); }",
    'Sekolah' => "protected \$guarded = [];\n    public function lembaga() { return \$this->belongsTo(Lembaga::class); }\n    public function users() { return \$this->hasMany(User::class); }\n    public function pengajars() { return \$this->hasMany(Pengajar::class); }\n    public function anaks() { return \$this->hasMany(Anak::class); }\n    public function saranas() { return \$this->hasMany(Sarana::class); }\n    public function menuMakanans() { return \$this->hasMany(MenuMakanan::class); }\n    public function kegiatans() { return \$this->hasMany(Kegiatan::class); }\n    public function cashflows() { return \$this->hasMany(Cashflow::class); }\n    public function matrikulasis() { return \$this->hasMany(Matrikulasi::class); }\n    public function kritikSarans() { return \$this->hasMany(KritikSaran::class); }",
    'Pengajar' => "protected \$guarded = [];\n    public function user() { return \$this->belongsTo(User::class); }\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function kegiatans() { return \$this->hasMany(Kegiatan::class); }\n    public function pencapaians() { return \$this->hasMany(Pencapaian::class); }",
    'Anak' => "protected \$guarded = [];\n    public function user() { return \$this->belongsTo(User::class, 'user_id'); } // Parent\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function pencapaians() { return \$this->hasMany(Pencapaian::class); }",
    'Sarana' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }",
    'MenuMakanan' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }",
    'Kegiatan' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function pengajar() { return \$this->belongsTo(Pengajar::class); }",
    'Cashflow' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }",
    'Matrikulasi' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function pencapaians() { return \$this->hasMany(Pencapaian::class); }",
    'Pencapaian' => "protected \$guarded = [];\n    public function anak() { return \$this->belongsTo(Anak::class); }\n    public function matrikulasi() { return \$this->belongsTo(Matrikulasi::class); }\n    public function pengajar() { return \$this->belongsTo(Pengajar::class); }",
    'KritikSaran' => "protected \$guarded = [];\n    public function sekolah() { return \$this->belongsTo(Sekolah::class); }\n    public function user() { return \$this->belongsTo(User::class); }",
];

foreach ($relationships as $model => $methods) {
    if ($model === 'User') {
        $path = $modelsDir . '/' . $model . '.php';
        $content = file_get_contents($path);
        // Ensure Spatie HasRoles is imported
        if (strpos($content, 'use Spatie\Permission\Traits\HasRoles;') === false) {
            $content = str_replace("use Laravel\Sanctum\HasApiTokens;", "use Laravel\Sanctum\HasApiTokens;\nuse Spatie\Permission\Traits\HasRoles;", $content);
            $content = str_replace("use HasFactory, Notifiable;", "use HasFactory, Notifiable, HasRoles;", $content);
        }
        // Insert methods before the last closing brace
        $pos = strrpos($content, '}');
        if ($pos !== false) {
            $content = substr_replace($content, "\n    " . $methods . "\n}", $pos, 1);
        }
        file_put_contents($path, $content);
        echo "Updated $model\n";
    } else {
        $path = $modelsDir . '/' . $model . '.php';
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $content = str_replace("use HasFactory;", "use HasFactory;\n\n    " . $methods, $content);
            file_put_contents($path, $content);
            echo "Updated $model\n";
        }
    }
}
echo "Done.\n";
