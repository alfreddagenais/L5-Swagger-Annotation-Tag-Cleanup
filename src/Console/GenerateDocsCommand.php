<?php

namespace L5SwaggerExtATC\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use L5SwaggerExtATC\Services\SwaggerFileService;
use L5Swagger\ConfigFactory;
use L5Swagger\Exceptions\L5SwaggerException;

class GenerateDocsCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'l5-swagger-extatc:generate {documentation?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate L5Swagger docs - With Cleanup';

    protected ConfigFactory $configFactory;
    protected SwaggerFileService $swaggerService;

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws L5SwaggerException
     */
    public function handle(ConfigFactory $configFactory, SwaggerFileService $swaggerService)
    {
        $this->configFactory = $configFactory;
        $this->swaggerService = $swaggerService;

        $all = $this->option('all');
        $all = empty($all) ? '' : '--all';

        $documentation = $this->argument('documentation');
        $documentation = empty($documentation) ? '' : $documentation;

        Artisan::call("l5-swagger:generate {$documentation} {$all}");
        // $this->call('l5-swagger:generate', ['--force' => $force]);

        // Log::debug('L5SwaggerExtATC Generate docs ok');

        if (!empty($all)) {
            $documentations = array_keys(config('l5-swagger.documentations', []));

            foreach ($documentations as $documentation) {
                $this->generateDocumentation($documentation);
            }

            return;
        }

        if (empty($documentation)) {
            $documentation = config('l5-swagger.default');
        }
        $this->generateDocumentation($documentation);
    }

    /**
     * @param  string  $documentation
     *
     * @throws L5SwaggerException
     */
    private function generateDocumentation(string $documentation)
    {
        $config = $this->configFactory->documentationConfig($documentation);
        // Log::debug('L5SwaggerExtATC config => ' . print_r($config, true));

        $atcOptions = $config['atcOptions'] ?? [];
        if (empty($atcOptions) || count($atcOptions) === 0) {
            $this->warn('L5SwaggerExtATC -> /!\ no atcOptions for doc -> ' . $documentation);
            return;
        }

        $jsonDocsFilePath = $this->swaggerService->getJsonDocsFilePath($config);
        if (empty($jsonDocsFilePath)) {
            return;
        }

        $this->info('L5SwaggerExtATC -> Regenerating docs ' . $documentation);

        $this->swaggerService->processSwaggerFile($atcOptions, $jsonDocsFilePath);
        $this->swaggerService->copySwaggerFileToYaml($config, $jsonDocsFilePath);
    }
}
