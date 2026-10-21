<?php
/**
 * Seeder user admin (CLI).
 *
 * Interaktif:
 *   docker compose exec app php database/seed_admin.php
 *
 * Dengan argumen:
 *   docker compose exec app php database/seed_admin.php --username=budi --password=rahasia123 \
 *       --nama="Budi Santoso" --jabatan=Petugas
 *
 * Generate beberapa petugas sekaligus (username petugas2, petugas3, ...):
 *   docker compose exec app php database/seed_admin.php --count=5 --password=admin123
 *
 * Opsi:
 *   --username   Username login (huruf, angka, titik, underscore; 3-50 karakter)
 *   --password   Password (minimal 6 karakter)
 *   --nama       Nama petugas (default: sama dengan username)
 *   --jabatan    Superadmin | Petugas (default: Petugas)
 *   --count=N    Buat N akun petugas bernomor otomatis
 *   --update     Perbarui nama/password/jabatan jika username sudah ada
 *   --list       Tampilkan daftar user admin
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $file = BASE_PATH . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Models\UserAdmin;

$opt = getopt('', ['username:', 'password:', 'nama:', 'jabatan:', 'count:', 'update', 'list', 'help']);

if (isset($opt['help'])) {
    preg_match('#/\*\*(.*?)\*/#s', file_get_contents(__FILE__), $m);
    echo preg_replace('/^\s*\* ?/m', '', $m[1]) . PHP_EOL;
    exit(0);
}

function fail(string $msg): never
{
    fwrite(STDERR, "✗ $msg" . PHP_EOL);
    exit(1);
}

function ask(string $label, ?string $default = null, bool $hidden = false): string
{
    $prompt = $label . ($default !== null ? " [$default]" : '') . ': ';
    if ($hidden && stream_isatty(STDIN)) {
        echo $prompt;
        shell_exec('stty -echo');
        $value = trim((string) fgets(STDIN));
        shell_exec('stty echo');
        echo PHP_EOL;
    } else {
        $value = trim((string) readline($prompt));
    }
    return $value !== '' ? $value : (string) $default;
}

function validate(string $username, string $password, string $jabatan): void
{
    if (!preg_match('/^[A-Za-z0-9._]{3,50}$/', $username)) {
        fail("Username \"$username\" tidak valid (3-50 karakter: huruf, angka, titik, underscore).");
    }
    if (strlen($password) < 6) {
        fail('Password minimal 6 karakter.');
    }
    if (!in_array($jabatan, ['Superadmin', 'Petugas'], true)) {
        fail("Jabatan harus Superadmin atau Petugas, bukan \"$jabatan\".");
    }
}

function printList(UserAdmin $model): void
{
    $rows = $model->all();
    printf("%-4s %-20s %-30s %s\n", 'ID', 'Username', 'Nama', 'Jabatan');
    foreach ($rows as $r) {
        printf("%-4d %-20s %-30s %s\n", $r['id_user'], $r['username'], $r['nama_petugas'], $r['jabatan']);
    }
}

$model = new UserAdmin();

if (isset($opt['list'])) {
    printList($model);
    exit(0);
}

$jabatan = ucfirst(strtolower($opt['jabatan'] ?? 'Petugas'));
$update  = isset($opt['update']);

// Mode --count: buat petugasN berurutan, lewati username yang sudah dipakai
if (isset($opt['count'])) {
    $count    = (int) $opt['count'];
    $password = $opt['password'] ?? fail('--password wajib diisi bersama --count.');
    if ($count < 1 || $count > 100) fail('--count harus antara 1 dan 100.');
    validate('petugas', $password, $jabatan);

    $prefix = $jabatan === 'Superadmin' ? 'admin' : 'petugas';
    $n = 1;
    for ($dibuat = 0; $dibuat < $count; $n++) {
        $username = $prefix . $n;
        if ($model->findByUsername($username)) continue;
        $model->create(ucfirst($prefix) . ' ' . $n, $username, $password, $jabatan);
        echo "✓ $username ($jabatan) dibuat" . PHP_EOL;
        $dibuat++;
    }
    exit(0);
}

$interaktif = !isset($opt['username']);
$username   = $opt['username'] ?? ask('Username');
$nama       = $opt['nama']     ?? ($interaktif ? ask('Nama petugas', $username) : $username);
if ($interaktif && !isset($opt['jabatan'])) {
    $jabatan = ucfirst(strtolower(ask('Jabatan (Superadmin/Petugas)', 'Petugas')));
}
$password = $opt['password'] ?? ask('Password', null, true);
if ($interaktif && !isset($opt['password']) && ask('Ulangi password', null, true) !== $password) {
    fail('Konfirmasi password tidak cocok.');
}

validate($username, $password, $jabatan);

if ($existing = $model->findByUsername($username)) {
    if (!$update) {
        fail("Username \"$username\" sudah ada. Tambahkan --update untuk memperbarui datanya.");
    }
    // Tanpa --nama / --jabatan, pertahankan nilai lama
    $model->update(
        isset($opt['nama']) || $interaktif ? $nama : $existing['nama_petugas'],
        $username,
        $password,
        isset($opt['jabatan']) || $interaktif ? $jabatan : $existing['jabatan']
    );
    echo "✓ User \"$username\" diperbarui ($jabatan)" . PHP_EOL;
    exit(0);
}

$id = $model->create($nama, $username, $password, $jabatan);
echo "✓ User \"$username\" dibuat (id $id, $jabatan)" . PHP_EOL;
