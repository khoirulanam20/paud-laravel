<?php

$migrationsDir = __DIR__ . '/d:\Project\Project Real\Firstudio\paud-laravel\database\migrations';
// Wait, absolute paths should be used safely.
$migrationsDir = 'd:/Project/Project Real/Firstudio/paud-laravel/database/migrations';

$schemas = [
    'lembagas' => "\$table->id();\n            \$table->string('name');\n            \$table->text('address')->nullable();\n            \$table->string('phone')->nullable();\n            \$table->timestamps();",
    'sekolahs' => "\$table->id();\n            \$table->foreignId('lembaga_id')->constrained()->cascadeOnDelete();\n            \$table->string('name');\n            \$table->text('address')->nullable();\n            \$table->string('phone')->nullable();\n            \$table->timestamps();",
    'pengajars' => "\$table->id();\n            \$table->foreignId('user_id')->constrained()->cascadeOnDelete();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->string('name');\n            \$table->string('jabatan')->nullable();\n            \$table->text('education_history')->nullable();\n            \$table->timestamps();",
    'anaks' => "\$table->id();\n            \$table->foreignId('user_id')->constrained()->cascadeOnDelete();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->string('name');\n            \$table->date('dob')->nullable();\n            \$table->string('parent_name')->nullable();\n            \$table->string('photo')->nullable();\n            \$table->timestamps();",
    'saranas' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->string('name');\n            \$table->string('condition')->nullable();\n            \$table->integer('quantity')->default(1);\n            \$table->timestamps();",
    'menu_makanans' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->date('date');\n            \$table->string('menu');\n            \$table->text('nutrition_info')->nullable();\n            \$table->string('photo')->nullable();\n            \$table->timestamps();",
    'kegiatans' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->foreignId('pengajar_id')->constrained()->cascadeOnDelete();\n            \$table->date('date');\n            \$table->string('title');\n            \$table->text('description')->nullable();\n            \$table->string('photo')->nullable();\n            \$table->timestamps();",
    'cashflows' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->enum('type', ['in', 'out']);\n            \$table->decimal('amount', 15, 2);\n            \$table->text('description')->nullable();\n            \$table->date('date');\n            \$table->timestamps();",
    'matrikulasis' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->string('indicator');\n            \$table->text('description')->nullable();\n            \$table->timestamps();",
    'pencapaians' => "\$table->id();\n            \$table->foreignId('anak_id')->constrained()->cascadeOnDelete();\n            \$table->foreignId('matrikulasi_id')->constrained('matrikulasis')->cascadeOnDelete();\n            \$table->foreignId('pengajar_id')->constrained()->cascadeOnDelete();\n            \$table->text('feedback')->nullable();\n            \$table->integer('score')->nullable();\n            \$table->timestamps();",
    'kritik_sarans' => "\$table->id();\n            \$table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();\n            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();\n            \$table->text('message');\n            \$table->string('status')->default('pending');\n            \$table->timestamps();",
];

$files = scandir($migrationsDir);

foreach ($files as $file) {
    if (strpos($file, '.php') === false) continue;
    
    foreach ($schemas as $table => $schema) {
        if (strpos($file, 'create_' . $table . '_table') !== false) {
            $path = $migrationsDir . '/' . $file;
            $content = file_get_contents($path);
            $content = preg_replace('/\$table->id\(\);\s*\$table->timestamps\(\);/', $schema, $content);
            file_put_contents($path, $content);
            echo "Updated $file\n";
        }
    }
}
echo "Done.\n";
