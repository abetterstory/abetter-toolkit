<?php

namespace Deployer;

require 'recipe/laravel.php';
require __DIR__.'/vendor/autoload.php';
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Project
set('application', getenv('APP_NAME'));
set('allow_anonymous_stats', false);
set('keep_releases', 1);

// Repository
set('repository', getenv('DP_REPOSITORY'));
set('branch', getenv('DP_BRANCH'));
set('git_recursive', false); // Ignore submodules
set('git_tty', true); // Known host & passphrase

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
	host('dev')->stage('dev')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_DEV_PATH'))->set('branch',$b=env('DP_DEV_BRANCH')?:get('branch'));
}
if ($s = env('DP_STAGE_SERVER')) {
	host('stage')->stage('stage')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_STAGE_PATH'))->set('branch',$b=env('DP_STAGE_BRANCH')?:get('branch'));
}
if ($s = env('DP_PRODUCTION_SERVER')) {
	host('production')->stage('production')->hostname($s)->set('server',$s)->set('deploy_path',env('DP_PRODUCTION_PATH'))->set('branch',$b=env('DP_PRODUCTION_BRANCH')?:get('branch'));
}

// -------------------------------------

// Tasks

task('hello', function () {
	writeln('{{ server }}');
	$stage = get('stage');
	cd('{{ deploy_path }}');
	$hostname = run('hostname'); writeln("server: $hostname");
	$whoami = run('whoami'); writeln("user: $whoami");
	$pwd = run('pwd'); writeln("path: $pwd");
    writeln('Hello world, ready to go @ '.ucwords($stage));
});

// ---

task('install', function () {
	$stage = get('stage');
	$repository = get('repository');
	$branch = get('branch');
	$confirm = "Are you sure you want to clone repository into %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		askConfirmation($ask);
	}
	writeln("install prepare ------------------------------------------------");
	cd('{{ deploy_path }}');
	$pwd = run('pwd'); writeln("pwd: $pwd");
	$git = run("git clone {$repository} ."); writeln("git clone {$repository} .: $git");
	$git = run("git fetch"); writeln("git fetch: $git");
	$git = run("git checkout origin/{$branch}"); writeln("git checkout origin/{$branch}: $git");
	$git = run('git reset --hard'); writeln("git reset --hard: $git");
	$git = run('git status'); writeln("git status: $git");
	$chmod = run('chmod -R 777 bootstrap/cache'); writeln("chmod -R 777 bootstrap/cache: $chmod");
	$chmod = run('chmod -R 777 storage'); writeln("chmod -R 777 storage: $chmod");
	$cp = run('cp vendor/abetter/wordpress/wp-config.php resources/wordpress/core/wp-config.php'); writeln("cp vendor/abetter/wordpress/wp-config.php resources/wordpress/core/wp-config.php: $cp");
	$cp = run('cp .env.example .env'); writeln("cp .env.example .env: $cp");
	$artisan = run('php artisan key:generate'); writeln("php artisan key:generate: $artisan");
	$composer = run('composer install -n --no-dev'); writeln("composer install -n --no-dev: $composer");
	$composer = run('composer update -n --no-dev'); writeln("composer update -n --no-dev: $composer");
	writeln("install done ------------------------------------------------");
});

// ---

task('deploy', function () {
	$stage = get('stage');
	$branch = get('branch');
	$confirm = "Are you sure you want to deploy to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace('%s',ucwords($stage),$confirm);
		askConfirmation($ask);
	}
	writeln("deploy prepare ------------------------------------------------");
	cd('{{ deploy_path }}');
	$pwd = run('pwd'); writeln("pwd: $pwd");
	$git = run("git fetch"); writeln("git fetch: $git");
	$git = run("git checkout --force origin/{$branch}"); writeln("git checkout --force origin/{$branch}: $git");
	$git = run('git reset --hard'); writeln("git reset --hard: $git");
	$git = run('git status'); writeln("git status: $git");
	$composer = run('composer install -n --no-dev'); writeln("composer install -n --no-dev: $composer");
	$composer = run('composer update -n --no-dev'); writeln("composer update -n --no-dev: $composer");
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

task('db:pull', function () {
	$destination = 'local';
	$stage = get('stage');
	$confirm = "Are you sure you want to replace %d database with %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%d','%s'],[ucwords($destination),ucwords($stage)],$confirm);
		askConfirmation($ask);
	}
	$time = date("Ymd_His");
	$filename = "{$stage}-{$time}";
	$local['user'] = env('DB_USERNAME');
	$local['pass'] = env('DB_PASSWORD');
	$local['db'] = env('DB_DATABASE');
	// ---
	cd('{{ deploy_path }}');
	run('mkdir -p tmp'); run('chmod 777 tmp');
	runLocally('mkdir -p tmp'); runLocally('chmod 777 tmp');
	runLocally('mkdir -p backup/db'); runLocally('chmod 777 backup/db');
	download('{{ deploy_path }}/.env', "tmp/{$stage}.env");
	// ---
	$tmpenv = new \Dotenv\Dotenv(__DIR__.'/tmp',"{$stage}.env");
	$tmpenv->overload();
	$remote['user'] = env('DB_USERNAME');
	$remote['pass'] = env('DB_PASSWORD');
	$remote['db'] = env('DB_DATABASE');
	writeln("prepare hosts ------------------------------------------------");
	// ---
	run("mysqldump --user={$remote['user']} --password={$remote['pass']} {$remote['db']} > tmp/{$filename}.sql");
	run("gzip tmp/{$filename}.sql");
	download("{{ deploy_path }}/tmp/{$filename}.sql.gz", "tmp/{$filename}.sql.gz");
	run("rm -rf tmp/{$filename}.sql.gz");
	writeln("mysqldump remote:{$remote['db']} > tmp/{$filename}.sql");
	// ---
	runLocally("mysqldump --user={$local['user']} --password={$local['pass']} {$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	runLocally("gzip backup/db/{$local['db']}_{$time}.sql");
	writeln("mysqldump local:{$local['db']} > backup/db/{$local['db']}_{$time}.sql");
	// ---
	runLocally("gunzip tmp/{$filename}.sql.gz");
	runLocally("mysql --user={$local['user']} --password={$local['pass']} {$local['db']} < tmp/{$filename}.sql");
	writeln("mysql local:{$local['db']} < tmp/{$filename}.sql");
	// ---
	runLocally('rm -rf tmp/*.env tmp/*.sql tmp/*.sql.gz');
	writeln("cleanup hosts ------------------------------------------------");
});

// ---

task('db:push', function () {
	$origin = 'local';
	$stage = get('stage');
	$confirm = "Are you sure you want to replace %s database with %o?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%o','%s'],[ucwords($origin),ucwords($stage)],$confirm);
		askConfirmation($ask);
	}
	$time = date("Ymd_His");
	$local['user'] = env('DB_USERNAME');
	$local['pass'] = env('DB_PASSWORD');
	$local['db'] = env('DB_DATABASE');
	// ---
	cd('{{ deploy_path }}');
	run('mkdir -p tmp'); run('chmod 777 tmp');
	run('mkdir -p backup/db'); run('chmod 777 backup/db');
	runLocally('mkdir -p tmp'); runLocally('chmod 777 tmp');
	download('{{ deploy_path }}/.env', "tmp/{$stage}.env");
	// ---
	$tmpenv = new \Dotenv\Dotenv(__DIR__.'/tmp',"{$stage}.env");
	$tmpenv->overload();
	$remote['user'] = env('DB_USERNAME');
	$remote['pass'] = env('DB_PASSWORD');
	$remote['db'] = env('DB_DATABASE');
	writeln("prepare hosts ------------------------------------------------");
	// ---
	run("mysqldump --user={$remote['user']} --password={$remote['pass']} {$remote['db']} > backup/db/{$remote['db']}_{$time}.sql");
	run("gzip backup/db/{$remote['db']}_{$time}.sql");
	writeln("mysqldump remote:{$remote['db']} > backup/db/{$remote['db']}_{$time}.sql");
	// ---
	runLocally("mysqldump --user={$local['user']} --password={$local['pass']} {$local['db']} > tmp/local_{$time}.sql");
	runLocally("gzip tmp/local_{$time}.sql");
	upload("tmp/local_{$time}.sql.gz", "{{ deploy_path }}/tmp/local_{$time}.sql.gz");
	run("gunzip tmp/local_{$time}.sql.gz");
	writeln("mysqldump local:{$local['db']} > tmp/local_{$time}.sql");
	// ---
	run("mysql --user={$remote['user']} --password={$remote['pass']} {$remote['db']} < tmp/local_{$time}.sql");
	writeln("mysql remote:{$remote['db']} < tmp/local_{$time}.sql");
	// ---
	runLocally('rm -rf tmp/*.env tmp/*.sql tmp/*.sql.gz');
	run('rm -rf tmp/*.sql tmp/*.sql.gz');
	writeln("cleanup hosts ------------------------------------------------");
});


// ---

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
		askConfirmation($ask);
	}
	// ---
	writeln("rsync prepare ------------------------------------------------");
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		$rsync = runLocally("rsync -vr {{ server }}:{{ deploy_path }}/{$dir} {$dest}");
		writeln("rsync: {$dir} $rsync");
	}
	writeln("rsync done ------------------------------------------------");
});

// ---

task('media:push', function () {
	$dirs = [
		'storage/app/public',
		'storage/wordpress/uploads'
	];
	$origin = 'local';
	$stage = get('stage');
	$confirm = "Are you sure you want to upload %o media to %s?";
	if (in_array($stage,['production','stage'])) {
		$ask = str_replace(['%o','%s'],[ucwords($origin),ucwords($stage)],$confirm);
		askConfirmation($ask);
	}
	// ---
	writeln("rsync prepare ------------------------------------------------");
	foreach ($dirs AS $dir) {
		$dest = dirname($dir);
		$rsync = runLocally("rsync -vr ./{$dir} {{ server }}:{{ deploy_path }}/{$dest}");
		writeln("rsync: {$dir} $rsync");
	}
	writeln("rsync done ------------------------------------------------");
});

// ---
