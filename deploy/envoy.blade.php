@setup

require __DIR__.'/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$title = 'Deployment';
$br = 'echo "------------------------------------------------------------------"';

$local = (isset($local)) ? 'local' : '';
$dev = (isset($dev)) ? 'dev' : '';
$stage = (isset($stage)) ? 'stage' : '';
$production = (isset($production)) ? 'production' : '';
$env = ($e = $local.$dev.$stage.$production) ? $e : '';

if (!$env) throw new Exception('Environment variable is not set: try --dev --stage --production');

$servers = [
	'local' => ['server' => '127.0.0.1','path' => ''],
	'dev' => ['server' => getenv('DP_DEV_SERVER'),'path' => getenv('DP_DEV_PATH')],
	'stage' => ['server' => getenv('DP_STAGE_SERVER'),'path' => getenv('DP_STAGE_PATH')],
	'production' => ['server' => getenv('DP_PRODUCTION_SERVER'),'path' => getenv('DP_PRODUCTION_PATH')]
];

$current = (!empty($servers[$env]['server'])) ? $servers[$env] : NULL;
$confirm = (in_array($env,['stage','production'])) ? TRUE : FALSE;

if (!$current) throw new Exception('Connection data for '.$env.' is not defined in .env');

@endsetup

@servers(['current' => $current['server']])

@task('hello', ['on' => 'current'])
	HOSTNAME=$(hostname);
	echo "Hello World! Responding from $HOSTNAME";
@endtask

@task('deploy', ['on' => 'current', 'confirm' => $confirm])
	echo "{{ $title }} @ {{ $env }}"
	cd {{ $current['path'] }}
	whoami
	hostname
    pwd
	{{ $br }}
	git pull
	{{ $br }}
	git status
	{{ $br }}
	composer install -n --no-dev --no-scripts
	{{ $br }}
@endtask
