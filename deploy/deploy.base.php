<?php

namespace Deployer;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

require 'recipe/laravel.php';
require __ROOT__.'/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::create(__ROOT__);
if (is_file(__ROOT__.'/.env')) $dotenv->load();

// Project
set('application', getenv('APP_NAME'));
set('allow_anonymous_stats', false);
set('keep_releases', 1);

// Default
set('default_stage', 'local');

// Repository
set('repository', getenv('DP_REPOSITORY'));
set('branch', getenv('DP_BRANCH'));
set('git_recursive', false); // Ignore submodules
set('git_tty', true); // Known host & passphrase
set('import_path', __ROOT__.'/vendor/abetter/wordpress/database/');

//argument('import', InputArgument::OPTIONAL, 'Import file.');
option('import', null, InputOption::VALUE_OPTIONAL, 'Import file.');

// Setup
set('shared_files', [
    '.env'
]);
set('shared_dirs', [
    'storage'
]);
add('writable_dirs', [
	'bootstrap/cache',
    'storage'
]);

// Hosts
host('local')->stage('local')->hostname('127.0.0.1')->set('server','127.0.0.1')->set('deploy_path','');
if ($s = env('DP_DEV_SERVER')) {
	host('dev')->stage('dev')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_DEV_PATH'))->set('deploy_unlock',env('DP_DEV_UNLOCK'))->set('branch',$b=env('DP_DEV_BRANCH')?:get('branch'));
}
if ($s = env('DP_STAGE_SERVER')) {
	host('stage')->stage('stage')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_STAGE_PATH'))->set('deploy_unlock',env('DP_STAGE_UNLOCK'))->set('branch',$b=env('DP_STAGE_BRANCH')?:get('branch'));
}
if ($s = env('DP_PRODUCTION_SERVER')) {
	host('production')->stage('production')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_PRODUCTION_PATH'))->set('deploy_unlock',env('DP_PRODUCTION_UNLOCK'))->set('branch',$b=env('DP_PRODUCTION_BRANCH')?:get('branch'));
}

// -------------------------------------

function writeLine($message="",$output="",$style="info") {
	writeln(date("\[H:i:s\] ")."<$style>".$message.(($output)?": ".$output:"")."</$style>");
}

function writeRun($command,$message="") {
	writeLine(($message)?$message:$command,run($command));
}

function writeRunLocally($command,$message="") {
	writeLine(($message)?$message:$command,runLocally($command));
}

// -------------------------------------

// Tasks

task('hello', function () {
	$stage = get('stage');
	$lock = ($unlock = get('deploy_unlock')) ? FALSE : TRUE;
	writeLine('{{ server }}');
	cd('{{ deploy_path }}');
	writeRun("hostname");
	writeRun("whoami","username");
	writeRun("pwd","destination");
    writeLine("Hello world, ready to go @ ".ucwords($stage));
	writeLine(ucwords($stage).' is '.(($lock)?'LOCKED':'OPEN').' for push! (set DP_'.strtoupper($stage).'_UNLOCK in .ENV)');
	writeLine("Available tasks:");
	writeln("> dep setuponce local");
	writeln("> dep reset local");
	writeln("> dep build local");
	writeln("> dep prepare $stage");
	writeln("> dep deploy $stage");
	writeln("> dep deploy:hot $stage");
	writeln("> dep deploy:composer $stage");
	writeln("> dep hot $stage");
	writeln("> dep db:import $stage");
	writeln("> dep db:pull $stage");
	writeln("> dep db:push $stage".(($lock)?" LOCKED!":""));
	writeln("> dep db $stage");
	writeln("> dep media:pull $stage");
	writeln("> dep media:push $stage".(($lock)?" LOCKED!":""));
	writeln("> dep media $stage");
	writeln("> dep service $stage");
	writeln("> dep service:aws $stage");
	writeln("> dep aws $stage");
});

// Tasks / Setup

task('setuponce', function () {
	writeLine("Local setup prepare");
	// ---
	writeRunLocally("rm -rf public/css");
	writeRunLocally("rm -rf public/js");
	writeRunLocally("rm -rf public/favicon.ico");
	writeRunLocally("rm -rf public/robots.txt");
	writeRunLocally("rm -rf public/web.config");
	writeRunLocally("rm -rf public/mix-manifest.json");
	writeRunLocally("rm -rf public/.htaccess");
	writeRunLocally("rm -rf webpack.mix.js");
	// ---
	writeRunLocally("mkdir -p bootstrap/cache");
	writeRunLocally("mkdir -p storage/wordpress/uploads");
	writeRunLocally("mkdir -p storage/framework/sessions");
	writeRunLocally("mkdir -p storage/framework/views");
	writeRunLocally("mkdir -p storage/framework/cache");
	writeRunLocally("mkdir -p storage/cache");
	writeRunLocally("mkdir -p storage/service");
	writeRunLocally("mkdir -p resources/scripts");
	writeRunLocally("mkdir -p resources/styles");
	writeRunLocally("mkdir -p resources/fonts");
	writeRunLocally("mkdir -p resources/images");
	writeRunLocally("mkdir -p resources/videos");
	writeRunLocally("mkdir -p resources/wordpress");
	writeRunLocally("mkdir -p public/fonts");
	writeRunLocally("mkdir -p public/images");
	writeRunLocally("mkdir -p public/scripts/components");
	writeRunLocally("mkdir -p public/styles/components");
	writeRunLocally("chmod -R 777 storage");
	writeRunLocally("chmod -R 777 bootstrap/cache");
	writeRunLocally("chmod -R 777 public/fonts");
	writeRunLocally("chmod -R 777 public/images");
	writeRunLocally("chmod -R 777 public/scripts");
	writeRunLocally("chmod -R 777 public/styles");
	// ---
	writeRunLocally("cp -n vendor/abetter/toolkit/public/.htaccess public/.htaccess");
	writeRunLocally("cp -n vendor/abetter/toolkit/mix/webpack.mix.js webpack.mix.js");
	writeRunLocally("cp -n vendor/abetter/toolkit/scripts/app.js resources/scripts/app.js");
	writeRunLocally("cp -n vendor/abetter/toolkit/styles/app.scss resources/styles/app.scss");
	writeRunLocally("cp -n vendor/abetter/toolkit/styles/editor.scss resources/styles/editor.scss");
	writeRunLocally("cp -n vendor/abetter/toolkit/styles/_variables.scss resources/styles/_variables.scss");
	// ---
	writeRunLocally("cp -n vendor/abetter/wordpress/resources/functions.php resources/wordpress/functions.php");
	writeRunLocally("cp -n vendor/abetter/wordpress/resources/helpers.php resources/wordpress/helpers.php");
	writeRunLocally("cp -n vendor/abetter/wordpress/resources/templates.php resources/wordpress/templates.php");
	// ---
	writeRunLocally("ln -s ../vendor/abetter/wordpress/core public/wp");
	// --
	writeRunLocally("composer install");
	// ---
	writeRunLocally("npm install");
	writeRunLocally("npm install normalize.css --save-dev");
	writeRunLocally("npm install font-awesome --save-dev");
	writeRunLocally("npm install uikit --save-dev");
	writeRunLocally("npm install what-input --save-dev");
	writeRunLocally("npm install foundation-sites --save-dev");
	writeRunLocally("npm install browser-sync --save-dev");
	writeRunLocally("npm install browser-sync-webpack-plugin --save-dev");
	writeRunLocally("npm install es6-promise --save-dev");
	writeRunLocally("npm install request --save-dev");
	writeRunLocally("npm install rellax --save-dev");
	writeRunLocally("npm install basicscroll --save-dev");
	// ---
	writeLine("Local setup done!");
	writeRunLocally("dep build local");
});

task('setup-old', function () {
	writeLine("Local setup prepare");
	runLocally("mkdir -p bootstrap/cache");
	runLocally("mkdir -p storage/wordpress/uploads");
	runLocally("mkdir -p storage/framework/sessions");
	runLocally("mkdir -p storage/framework/views");
	runLocally("mkdir -p storage/framework/cache");
	runLocally("mkdir -p storage/cache");
	runLocally("mkdir -p storage/service");
	runLocally("mkdir -p resources/fonts");
	runLocally("mkdir -p resources/images");
	runLocally("mkdir -p resources/wordpress");
	runLocally("mkdir -p public/fonts");
	runLocally("mkdir -p public/images");
	runLocally("mkdir -p public/scripts/components");
	runLocally("mkdir -p public/styles/components");
	runLocally("chmod -R 777 storage");
	runLocally("chmod -R 777 bootstrap/cache");
	runLocally("chmod -R 777 public/fonts");
	runLocally("chmod -R 777 public/images");
	runLocally("chmod -R 777 public/scripts");
	runLocally("chmod -R 777 public/styles");
	runLocally("cp -n .env.example .env || true");
	writeRunLocally("composer install");
	writeRunLocally("npm install");
	writeLine("Local setup done!");
});

// Tasks / Reset

task('reset', function () {
	writeLine("Local reset prepare");
	runLocally("rm -rf storage/clockwork/*");
	runLocally("rm -rf storage/cache/*");
	runLocally("rm -rf storage/service/*");
	runLocally("rm -rf public/fonts/*");
	runLocally("rm -rf public/images/*");
	runLocally("rm -rf public/scripts/*");
	runLocally("rm -rf public/styles/*");
	runLocally("mkdir -p bootstrap/cache");
	runLocally("mkdir -p storage/wordpress/uploads");
	runLocally("mkdir -p storage/framework/sessions");
	runLocally("mkdir -p storage/framework/views");
	runLocally("mkdir -p storage/framework/cache");
	runLocally("mkdir -p storage/cache");
	runLocally("mkdir -p storage/service");
	runLocally("mkdir -p resources/scripts");
	runLocally("mkdir -p resources/styles");
	runLocally("mkdir -p resources/fonts");
	runLocally("mkdir -p resources/images");
	runLocally("mkdir -p resources/videos");
	runLocally("mkdir -p resources/wordpress");
	runLocally("mkdir -p public/fonts");
	runLocally("mkdir -p public/images");
	runLocally("mkdir -p public/videos");
	runLocally("mkdir -p public/scripts/components");
	runLocally("mkdir -p public/styles/components");
	runLocally("chmod -R 777 storage");
	runLocally("chmod -R 777 bootstrap/cache");
	runLocally("chmod -R 777 public/fonts");
	runLocally("chmod -R 777 public/images");
	runLocally("chmod -R 777 public/videos");
	runLocally("chmod -R 777 public/scripts");
	runLocally("chmod -R 777 public/styles");
	runLocally("rm -rf public/wp");
	runLocally("ln -s ../vendor/abetter/wordpress/core public/wp");
	writeLine("Local reset done!");
});

// Tasks / Build

task('build', function () {
	writeLine("Build prepare");
	runLocally("mkdir -p public/scripts/components || true");
	runLocally("mkdir -p public/styles/components || true");
	runLocally("mkdir -p storage/cache || true");
	runLocally("mkdir -p storage/service || true");
	runLocally("rm -rf storage/cache/* || true");
	runLocally("rm -rf storage/service/* || true");
	runLocally("rm -rf storage/clockwork/* || true");
	runLocally("rm -rf storage/logs/* || true");
	runLocally("rm -rf public/scripts/* || true");
	runLocally("rm -rf public/styles/* || true");
	runLocally("chmod -R 777 public/scripts || true");
	runLocally("chmod -R 777 public/styles || true");
	runLocally("chmod -R 777 storage || true");
	runLocally("chmod -R 777 service || true");
	writeLine("Building...");
	writeRunLocally("npm run production");
	runLocally("rm -rf public/fonts/.fonts-here.txt || true");
	runLocally("rm -rf public/images/.images-here.txt || true");
	runLocally("rm -rf public/videos/.videos-here.txt || true");
	writeLine("Build done!");
});

// Tasks / Watch

task('watch', function () {
	writeRunLocally("npm run watch");
});

// Tasks / Prepare

task('prepare', function () {
	$stage = get('stage');
	$path = get('deploy_path');
	$server = get('server');
	$dir = "{$server}:{$path}";
	$time = date("Ymd_His");
	cd("{{ deploy_path }}");
	// --
	$confirm = "Are you sure you want to DELETE all files except .env in %s? : \n{$dir}";
	$ask = str_replace('%s',ucwords($stage),$confirm);
	if (!askConfirmation($ask)) return false;
	// ---
	writeLine("Prepare task");
	writeRun("cp .env ../tmp/{$time}.env","backup .env => ../tmp/{$time}.env");
	run("rm -rf *");
	run("rm -rf .git*");
	writeLine("Prepare done!");
});

// Tasks / Deploy

task('deploy', function () {
	$stage = get('stage');
	$confirm = "Are you sure you want to deploy with rsync to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	cd("{{ deploy_path }}");
	writeRun("pwd","Deploy prepare destination");
	// ---
	$dirs = ['app','bootstrap','config','database','resources','routes','storage','tests','vendor','public','artisan','composer.json','composer.lock','server.php'];
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		writeRunLocally("rsync -vr --exclude=cache --exclude=clockwork --links --quiet ./{$dir} {{ server }}:{{ deploy_path }}/{$dest}","rsync: {$dir}");
	}
	// ---
	writeRun("chmod -R 777 public/scripts || true");
	writeRun("chmod -R 777 public/styles || true");
	writeRun("rm -rf public/scripts/components/* || true");
	writeRun("rm -rf public/styles/components/* || true");
	writeRun("mkdir -p public/scripts/components || true");
	writeRun("mkdir -p public/styles/components || true");
	writeRun("chmod -R 777 public/scripts/components || true");
	writeRun("chmod -R 777 public/styles/components || true");
	// ---
	writeLine("Updating composer/laravel");
	writeRun("mkdir -p bootstrap/cache");
	writeRun("chmod -R 777 bootstrap/cache || true");
	writeRun("composer install -n --no-dev");
	writeRun("php artisan cache:clear");
	writeRun("php artisan route:clear");
	writeRun("php artisan view:clear");
	writeRun("php artisan config:clear");
	writeRun("rm -rf storage/framework/sessions/*");
	writeRun("mkdir -p storage");
	writeRun("chmod -R 777 storage || true");
	writeLine("Deploy done!");
});

task('deploy:nova', function () {
	$stage = get('stage');
	$confirm = "Are you sure you want to deploy with rsync to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	cd("{{ deploy_path }}");
	writeRun("pwd","Deploy prepare destination");
	// ---
	$dirs = ['app','nova','bootstrap','config','database','resources','routes','storage','tests','vendor','public','artisan','composer.json','composer.lock','server.php'];
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		writeRunLocally("rsync -vr --exclude='*.key' --exclude=cache --exclude=clockwork --links --quiet ./{$dir} {{ server }}:{{ deploy_path }}/{$dest}","rsync: {$dir}");
	}
	// ---
	writeRun("chmod -R 777 public/scripts || true");
	writeRun("chmod -R 777 public/styles || true");
	writeRun("rm -rf public/scripts/components/* || true");
	writeRun("rm -rf public/styles/components/* || true");
	writeRun("mkdir -p public/scripts/components || true");
	writeRun("mkdir -p public/styles/components || true");
	writeRun("chmod -R 777 public/scripts/components || true");
	writeRun("chmod -R 777 public/styles/components || true");
	// ---
	writeLine("Updating composer/laravel");
	writeRun("mkdir -p bootstrap/cache");
	writeRun("chmod -R 777 bootstrap/cache || true");
	writeRun("composer install -n --no-dev");
	writeRun("php artisan cache:clear");
	writeRun("php artisan route:clear");
	writeRun("php artisan view:clear");
	writeRun("php artisan config:clear");
	writeRun("rm -rf storage/framework/sessions/*");
	writeRun("mkdir -p storage");
	writeRun("chmod -R 777 storage || true");
	// ---
	writeRun("php artisan passport:keys");
	// ---
	writeLine("Deploy done!");
});

// ---

task('deploy:hot', function () {
	$stage = get('stage');
	$confirm = "Are you sure you want to hot-deploy with rsync to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	cd("{{ deploy_path }}");
	writeRun("pwd","Hot-deploy prepare destination");
	// ---
	$dirs = ['resources','routes','public'];
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		writeRunLocally("rsync -vr --exclude=cache --exclude=clockwork --links --quiet ./{$dir} {{ server }}:{{ deploy_path }}/{$dest}","rsync: {$dir}");
	}
	// ---
	writeRun("chmod -R 777 public/scripts || true");
	writeRun("chmod -R 777 public/styles || true");
	writeRun("rm -rf public/scripts/components/* || true");
	writeRun("rm -rf public/styles/components/* || true");
	writeRun("mkdir -p public/scripts/components || true");
	writeRun("mkdir -p public/styles/components || true");
	writeRun("chmod -R 777 public/scripts/components || true");
	writeRun("chmod -R 777 public/styles/components || true");
	// ---
	writeRun("php artisan cache:clear");
	writeRun("php artisan route:clear");
	writeRun("php artisan view:clear");
	writeRun("php artisan config:clear");
	writeRun("rm -rf storage/framework/sessions/* || true");
	writeLine("Hot-deploy done!");
});

task('hot', function () {
	invoke('deploy:hot');
});

task('deploy:composer', function () {
	$stage = get('stage');
	$confirm = "Are you sure you want to composer-deploy with rsync to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	cd("{{ deploy_path }}");
	// ---
	writeLine("Updating composer/laravel");
	writeRun("mkdir -p bootstrap/cache");
	writeRun("chmod -R 777 bootstrap/cache || true");
	writeRun("composer install -n --no-dev");
	writeLine("Composer-Deploy done!");
});

// Tasks / Database

task('db:import', function () {
	$import = (input()->hasOption('import')) ? input()->getOption('import') : null;
	$import_path = get('import_path');
	$destination = 'local';
	$confirm = "Are you sure you want to replace %d database with %f";
	$ask = str_replace(['%d','%f'],[ucwords($destination),$import],$confirm);
	if (!askConfirmation($ask)) return false;
	$time = date("Ymd_His");
	$filename = "{$stage}-{$time}";
	$local['user'] = env('DB_USERNAME');
	$local['pass'] = env('DB_PASSWORD');
	$local['db'] = env('DB_DATABASE');
	// ---
	writeLine("Import prepare");
	runLocally('mkdir -p tmp');
	runLocally('mkdir -p backup/db');
	runLocally('chmod 777 tmp');
	runLocally('chmod 777 backup/db');
	// ---
	runLocally("mysqldump --user={$local['user']} --password={$local['pass']} {$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	runLocally("gzip backup/db/{$local['db']}_{$time}.sql");
	writeLine("mysqldump local:{$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	// ---
	runLocally("mysql --user={$local['user']} --password={$local['pass']} {$local['db']} < {$import_path}{$import}");
	writeLine("mysql local:{$local['db']} < {$import_path}{$import}");
	// ---
	writeLine("Import done");
});

task('db:pull', function () {
	$destination = 'local';
	$stage = get('stage');
	$confirm = "Are you sure you want to replace %d database with %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%d','%s'],[ucwords($destination),ucwords($stage)],$confirm);
		if (!askConfirmation($ask)) return false;
	}
	$time = date("Ymd_His");
	$filename = "{$stage}-{$time}";
	$local['user'] = env('DB_USERNAME');
	$local['pass'] = env('DB_PASSWORD');
	$local['db'] = env('DB_DATABASE');
	// ---
	cd('{{ deploy_path }}');
	writeLine("Prepare target & destination");
	run('mkdir -p tmp');
	run('chmod 777 tmp');
	runLocally('mkdir -p tmp');
	runLocally('mkdir -p backup/db');
	runLocally('chmod 777 tmp');
	runLocally('chmod 777 backup/db');
	download('{{ deploy_path }}/.env', "tmp/{$stage}.env");
	// ---
	$tmpenv = \Dotenv\Dotenv::create(__ROOT__.'/tmp',"{$stage}.env");
	$tmpenv->overload();
	$remote['user'] = env('DB_USERNAME');
	$remote['pass'] = env('DB_PASSWORD');
	$remote['db'] = env('DB_DATABASE');
	// ---
	run("mysqldump --user={$remote['user']} --password={$remote['pass']} {$remote['db']} > tmp/{$filename}.sql");
	run("gzip tmp/{$filename}.sql");
	download("{{ deploy_path }}/tmp/{$filename}.sql.gz", "tmp/{$filename}.sql.gz");
	run("rm -rf tmp/{$filename}.sql.gz");
	writeLine("mysqldump remote:{$remote['db']} > tmp/{$filename}.sql");
	// ---
	runLocally("mysqldump --user={$local['user']} --password={$local['pass']} {$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	runLocally("gzip backup/db/{$local['db']}_{$time}.sql");
	writeLine("mysqldump local:{$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	// ---
	runLocally("gunzip tmp/{$filename}.sql.gz");
	runLocally("mysql --user={$local['user']} --password={$local['pass']} {$local['db']} < tmp/{$filename}.sql");
	writeLine("mysql local:{$local['db']} < tmp/{$filename}.sql");
	// ---
	runLocally('rm -rf tmp/*.env tmp/*.sql tmp/*.sql.gz');
	writeLine("Pull database done!");
});

task('db:push', function () {
	$origin = 'local';
	$stage = get('stage');
	$lock = ($unlock = get('deploy_unlock')) ? FALSE : TRUE;
	if ($lock) return writeLine('Database is LOCKED for push! (set DP_'.strtoupper($stage).'_UNLOCK in .ENV)');
	$confirm = "Are you sure you want to replace %s database with %o?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%o','%s'],[ucwords($origin),ucwords($stage)],$confirm);
		if (!askConfirmation($ask)) return false;
	}
	$time = date("Ymd_His");
	$local['user'] = env('DB_USERNAME');
	$local['pass'] = env('DB_PASSWORD');
	$local['db'] = env('DB_DATABASE');
	// ---
	cd('{{ deploy_path }}');
	run('mkdir -p tmp');
	run('mkdir -p backup/db');
	run('chmod 777 tmp');
	run('chmod 777 backup/db');
	runLocally('mkdir -p tmp');
	runLocally('chmod 777 tmp');
	download('{{ deploy_path }}/.env', "tmp/{$stage}.env");
	// ---
	$tmpenv = \Dotenv\Dotenv::create(__ROOT__.'/tmp',"{$stage}.env");
	$tmpenv->overload();
	$remote['user'] = env('DB_USERNAME');
	$remote['pass'] = env('DB_PASSWORD');
	$remote['db'] = env('DB_DATABASE');
	writeLine("prepare target & destination");
	// ---
	run("mysqldump --user={$remote['user']} --password={$remote['pass']} {$remote['db']} > backup/db/{$remote['db']}_{$time}.sql");
	run("gzip backup/db/{$remote['db']}_{$time}.sql");
	writeLine("mysqldump remote:{$remote['db']} > backup/db/{$remote['db']}_{$time}.sql");
	// ---
	runLocally("mysqldump --user={$local['user']} --password={$local['pass']} {$local['db']} > tmp/local_{$time}.sql");
	runLocally("gzip tmp/local_{$time}.sql");
	upload("tmp/local_{$time}.sql.gz", "{{ deploy_path }}/tmp/local_{$time}.sql.gz");
	run("gunzip tmp/local_{$time}.sql.gz");
	writeLine("mysqldump local:{$local['db']} > tmp/local_{$time}.sql");
	// ---
	run("mysql --user={$remote['user']} --password={$remote['pass']} {$remote['db']} < tmp/local_{$time}.sql");
	writeLine("mysql remote:{$remote['db']} < tmp/local_{$time}.sql");
	// ---
	runLocally('rm -rf tmp/*.env tmp/*.sql tmp/*.sql.gz');
	run('rm -rf tmp/*.sql tmp/*.sql.gz');
	writeLine("Push database done!");
});

task('db', function () {
	invoke('db:push');
});

// Tasks / Media

task('media:pull', function () {
	$dirs = [
		'storage/app/public',
		'storage/wordpress/uploads'
	];
	$destination = 'local';
	$stage = get('stage');
	$confirm = "Are you sure you want to download %s media to %d?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%d','%s'],[ucwords($destination),ucwords($stage)],$confirm);
		if (!askConfirmation($ask)) return false;
	}
	// ---
	writeLine("Pull prepare");
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		writeRunLocally("rsync -vr --quiet {{ server }}:{{ deploy_path }}/{$dir} {$dest}","rsync: {$dir}");
	}
	writeLine("Pull media done!");
});

task('media:push', function () {
	$dirs = [
		'storage/app/public',
		'storage/wordpress/uploads'
	];
	$origin = 'local';
	$stage = get('stage');
	$lock = ($unlock = get('deploy_unlock')) ? FALSE : TRUE;
	if ($lock) return writeLine('Media is LOCKED for push! (set DP_'.strtoupper($stage).'_UNLOCK in .ENV)');
	$confirm = "Are you sure you want to upload %o media to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%o','%s'],[ucwords($origin),ucwords($stage)],$confirm);
		if (!askConfirmation($ask)) return false;
	}
	// ---
	writeLine("Push prepare");
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		writeRunLocally("rsync -vr --quiet ./{$dir} {{ server }}:{{ deploy_path }}/{$dest}","rsync: {$dir}");
	}
	writeLine("Push media done!");
});

task('media', function () {
	invoke('media:push');
});

// Tasks / Service

task('service', function () {
	global $service; if (!$service) return writeLine("Error: no service defined.");
	$env = run("cat {{ deploy_path }}/.env");
	if (!preg_match('/APP_URL=(.+)/',$env,$match)) return writeLine("Error: no service domain found.");
	$domain = $match[1];
	writeLine("Calling service: {$domain}{$service}");
	writeRun("curl {$domain}{$service}","Response");
});

task('service:aws', function () {
	global $service; $service = "/service/aws/invalidate.json";
	invoke('service');
});

task('aws', function () {
	invoke('service:aws');
});

// ---

task('old:install:delete', function () {
	$stage = get('stage');
	$path = get('deploy_path');
	$server = get('server');
	$dir = "{$server}:{$path}";
	$time = date("Ymd_His");
	cd('{{ deploy_path }}');
	// --
	$confirm = "Are you sure you want to DELETE all files except .env in %s? : \n{$dir}";
	$ask = str_replace('%s',ucwords($stage),$confirm);
	if (!askConfirmation($ask)) return false;
	// ---
	writeln("delete prepare ------------------------------------------------");
	$cp = run("cp .env ../tmp/{$time}.env"); writeln("cp .env ../tmp/{$time}.env: $cp");
	$rm = run('rm -rf *'); writeln("rm -rf *: $rm");
	$rm = run('rm -rf .env*'); writeln("rm -rf .env*: $rm");
	$rm = run('rm -rf .git*'); writeln("rm -rf .git*: $rm");
	writeln("delete done ------------------------------------------------");
});

task('old:install', function () {
	$stage = get('stage');
	$repository = get('repository');
	$branch = get('branch');
	$confirm = "Are you sure you want to clone repository into %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	writeln("install prepare ------------------------------------------------");
	cd('{{ deploy_path }}');
	$pwd = run('pwd'); writeln("pwd: $pwd");
	$git = run("git clone {$repository} ."); writeln("git clone {$repository} .: $git");
	$git = run("git fetch"); writeln("git fetch: $git");
	$git = run("git checkout origin/{$branch}"); writeln("git checkout origin/{$branch}: $git");
	$git = run('git reset --hard'); writeln("git reset --hard: $git");
	$git = run('git status'); writeln("git status: $git");
	$cp = run('cp .env.example .env'); writeln("cp .env.example .env: $cp");
	run('mkdir -p bootstrap/cache');
	run('mkdir -p storage');
	$chmod = run('chmod -R 777 bootstrap/cache'); writeln("chmod -R 777 bootstrap/cache: $chmod");
	$chmod = run('chmod -R 777 storage'); writeln("chmod -R 777 storage: $chmod");
	$composer = run('composer install -n --no-dev'); writeln("composer install -n --no-dev: $composer");
	$artisan = run('php artisan key:generate'); writeln("php artisan key:generate: $artisan");
	writeln("install done ------------------------------------------------");
});

task('old:push', function () {
	$branch = get('branch');
	$confirm = "Are you sure you want to commit and push changes to branch %b?";
	$ask = str_replace('%b',ucwords($branch),$confirm);
	if (!askConfirmation($ask)) return false;
	if (!$message = ask('Enter commit message:')) return false;
	$git = runLocally('git add -A'); writeln("git add -A: $git");
	$git = runLocally("git commit -m \"{$message}\""); writeln("git commit -m \"{$message}\": $git");
	$git = runLocally('git push'); writeln("git push: $git");
});

task('old:deploy', function () {
	$stage = get('stage');
	$branch = get('branch');
	$confirm = "Are you sure you want to deploy to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		if (!askConfirmation($ask)) return false;
	}
	writeln("deploy prepare ------------------------------------------------");
	cd('{{ deploy_path }}');
	$pwd = run('pwd'); writeln("pwd: $pwd");
	$git = run("git fetch"); writeln("git fetch: $git");
	$git = run("git checkout --force origin/{$branch}"); writeln("git checkout --force origin/{$branch}: $git");
	$git = run('git reset --hard'); writeln("git reset --hard: $git");
	$git = run('git status'); writeln("git status: $git");
	run('mkdir -p bootstrap/cache');
	run('mkdir -p storage');
	$composer = run('composer install -n --no-dev'); writeln("composer install -n --no-dev: $composer");
	$artisan = run('php artisan cache:clear'); writeln("php artisan cache:clear: $artisan");
	$artisan = run('php artisan route:clear'); writeln("php artisan route:clear: $artisan");
	$artisan = run('php artisan view:clear'); writeln("php artisan view:clear: $artisan");
	$artisan = run('php artisan config:clear'); writeln("php artisan config:clear: $artisan");
	$rm = run('rm -rf storage/framework/sessions/*'); writeln("rm -rf storage/framework/sessions/*: $rm");
	$chmod = run('chmod -R 777 bootstrap/cache'); writeln("chmod -R 777 bootstrap/cache: $chmod");
	$chmod = run('chmod -R 777 storage'); writeln("chmod -R 777 storage: $chmod");
	writeln("deploy done ------------------------------------------------");
});

// ---
