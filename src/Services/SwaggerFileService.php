<?php

namespace L5SwaggerExtATC\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Yaml;

/**
 * Service to process Swagger files.
 */
class SwaggerFileService
{
    protected $atcOptions;

    /**
     * Process a Swagger file based on ATC options.
     *
     * @param array $atcOptions Options for processing.
     * @param string $filePath File path of the Swagger file.
     */
    public function processSwaggerFile(array $atcOptions, string $filePath): void
    {
        if (!File::exists($filePath)) {
            return;
        }

        $this->atcOptions = $atcOptions;
        $content = json_decode(File::get($filePath), true);

        $this->processPaths($content);
        $content['components']['securitySchemes'] = $this->processSecuritySchemes($content['components']['securitySchemes'] ?? []);

        $cleanOrphanSchemas = $this->atcOptions['cleanOrphanSchemas'] ?? false;
        if ($cleanOrphanSchemas) {
            // Collect schema references from paths
            $schemaRefs = $this->collectSchemaRefs($content['paths']);

            // Clean orphan schemas
            $this->cleanOrphanSchemas($content, $schemaRefs);
        }

        File::put($filePath, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Process the paths of the Swagger file.
     *
     * @param array $content Content from Swagger file.
     * @return void
     */
    protected function processPaths(&$content): void
    {
        if (!isset($content['paths'])) {
            return;
        }

        $includeTags = $this->atcOptions['include']['operations']['tags'] ?? [];
        $excludeTags = $this->atcOptions['exclude']['operations']['tags'] ?? [];

        foreach ($content['paths'] as $path => $methods) {
            foreach ($methods as $method => $details) {
                $detailsTags = $details['tags'] ?? [];

                if (!$this->shouldInclude($includeTags, $detailsTags) || $this->shouldExclude($excludeTags, $detailsTags)) {
                    // Remove the specific method from this path
                    unset($content['paths'][$path][$method]);
                }
            }

            // Check whether the path is empty after filtering and delete it if necessary.
            if (empty($content['paths'][$path])) {
                unset($content['paths'][$path]);
            }
        }
    }

    /**
     * Process security schemes of the Swagger file.
     *
     * @param array $securitySchemes Security schemes from Swagger file.
     * @return array Processed security schemes.
     */
    protected function processSecuritySchemes(array $securitySchemes): array
    {
        $includeNames = $this->atcOptions['include']['securitySchemes']['names'] ?? [];
        $excludeNames = $this->atcOptions['exclude']['securitySchemes']['names'] ?? [];

        return array_filter($securitySchemes, function ($name) use ($includeNames, $excludeNames) {
            $shouldInclude = empty($includeNames) || in_array($name, $includeNames);
            $shouldExclude = in_array($name, $excludeNames);

            return $shouldInclude && !$shouldExclude;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Determine if a set of tags should be included based on configuration.
     *
     * @param array $includeTags Tags specified in the include configuration.
     * @param array $tags Tags to check.
     * @return bool True if tags should be included.
     */
    protected function shouldInclude(array $includeTags, array $tags): bool
    {
        return empty($includeTags) || !empty(array_intersect($includeTags, $tags));
    }

    /**
     * Determine if a set of tags should be excluded based on configuration.
     *
     * @param array $excludeTags Tags specified in the exclude configuration.
     * @param array $tags Tags to check.
     * @return bool True if tags should be excluded.
     */
    protected function shouldExclude(array $excludeTags, array $tags): bool
    {
        return !empty(array_intersect($excludeTags, $tags));
    }

    /**
     * Copy Swagger file to YAML format.
     *
     * @param array $config Configuration for YAML conversion.
     * @param string $filePath File path of the Swagger file.
     */
    public function copySwaggerFileToYaml(array $config, string $filePath): void
    {
        if (!File::exists($filePath)) {
            return;
        }

        $generateYamlCopy = $config['generate_yaml_copy'] ?? false;
        if (!$generateYamlCopy) {
            return;
        }

        $content = json_decode(File::get($filePath), true);
        $yamlDocs = (new YamlDumper(2))->dump($content, 20, 0, Yaml::DUMP_OBJECT_AS_MAP ^ Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

        $yamlDocsFile = $this->getYamlDocsFilePath($config);
        if ($yamlDocsFile) {
            File::put($yamlDocsFile, $yamlDocs);
        }
    }

    /**
     * Get the file path for Json documentation.
     *
     * @param array $config Configuration for paths.
     * @return string|null Json documentation file path or null if not set.
     */
    protected function getDocsFilePath(array $config, string $doc): ?string
    {
        $configPaths = $config['paths'] ?? [];
        $configPathsDocs = $configPaths['docs'] ?? null;
        $configPathsDoc = $configPaths[$doc] ?? null;

        if (empty($configPathsDocs) || empty($configPathsDoc)) {
            return null;
        }

        return "{$configPathsDocs}/{$configPathsDoc}";
    }

    /**
     * Get the file path for Json documentation.
     *
     * @param array $config Configuration for paths.
     * @return string|null Json documentation file path or null if not set.
     */
    public function getJsonDocsFilePath(array $config): ?string
    {
        return $this->getDocsFilePath($config, 'docs_json');
    }

    /**
     * Get the file path for YAML documentation.
     *
     * @param array $config Configuration for paths.
     * @return string|null YAML documentation file path or null if not set.
     */
    public function getYamlDocsFilePath(array $config): ?string
    {
        return $this->getDocsFilePath($config, 'docs_yaml');
    }

    /**
     * Collects unique schema references from Swagger paths.
     *
     * @param array $paths The Swagger paths.
     * @return array Array of unique schema references.
     */
    protected function collectSchemaRefs(array $paths): array
    {
        $refs = [];
        array_walk_recursive($paths, function ($value, $key) use (&$refs) {
            if ($key === '$ref') {
                $refs[] = $value;
            }
        });
        return array_unique($refs);
    }

    /**
     * Removes orphan schemas from Swagger components.
     *
     * @param array &$content The Swagger documentation content.
     * @param array $schemaRefs Array of schema references to retain.
     */
    protected function cleanOrphanSchemas(&$content, array $schemaRefs): void
    {
        if (!isset($content['components']['schemas'])) {
            return;
        }

        foreach ($content['components']['schemas'] as $schema => $details) {
            $schemaRef = '#/components/schemas/' . $schema;
            if (!in_array($schemaRef, $schemaRefs)) {
                unset($content['components']['schemas'][$schema]);
            }
        }
    }
}
