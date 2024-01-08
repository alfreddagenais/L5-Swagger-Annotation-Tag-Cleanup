<?php

namespace L5SwaggerExtATC\Tests\Unit;

use L5SwaggerExtATC\Tests\TestCase;
use L5SwaggerExtATC\Services\SwaggerFileService;

class SwaggerFileServiceTest extends TestCase
{
    protected function getTemporaryPath(array $content): string
    {
        // Creating a temporary file
        $filePath = tempnam(sys_get_temp_dir(), 'swagger');
        $jsonEncode = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($filePath, $jsonEncode);

        return $filePath;
    }

    protected function getProcessedContent(string $filePath): array
    {
        return json_decode(file_get_contents($filePath), true);
    }

    public function testProcessPaths()
    {
        $swaggerService = new SwaggerFileService();

        // Exemple de contenu Swagger
        $content = [
          'paths' => [
            '/example' => [
              'post' => [
                'tags' => ['customTag'],
                // ... other properties ...
              ],
              'get' => [
                'tags' => ['otherTag'],
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // Set to include only 'customTag' tags
        $atcOptions = [
          'include' => [
            'operations' => [
              'tags' => ['customTag'],
            ],
          ],
        ];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayHasKey('/example', $processedContent['paths']);
        $this->assertArrayHasKey('post', $processedContent['paths']['/example']);
        $this->assertArrayNotHasKey('get', $processedContent['paths']['/example']);

        // Delete temporary file
        unlink($filePath);
    }

    /**
     * Test the processPaths method with exclude options.
     */
    public function testProcessPathsWithExclude()
    {
        $swaggerService = new SwaggerFileService();

        // Example Swagger content
        $content = [
          'paths' => [
            '/example' => [
              'post' => [
                'tags' => ['customTag'],
                // ... other properties ...
              ],
              'get' => [
                'tags' => ['excludeTag'],
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // Set to exclude 'excludeTag' tags
        $atcOptions = [
          'exclude' => [
            'operations' => [
              'tags' => ['excludeTag'],
            ],
          ],
        ];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayHasKey('/example', $processedContent['paths']);
        $this->assertArrayHasKey('post', $processedContent['paths']['/example']);
        $this->assertArrayNotHasKey('get', $processedContent['paths']['/example']);

        // Delete temporary file
        unlink($filePath);
    }

    /**
     * Test processPaths method with no specific tags for inclusion or exclusion.
     */
    public function testProcessPathsWithNoSpecificTags()
    {
        $swaggerService = new SwaggerFileService();

        // Example Swagger content
        $content = [
          'paths' => [
            '/example' => [
              'post' => [
                'tags' => ['someTag'],
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // No specific tags for include or exclude
        $atcOptions = [];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayHasKey('/example', $processedContent['paths']);
        $this->assertArrayHasKey('post', $processedContent['paths']['/example']);

        // Delete temporary file
        unlink($filePath);
    }

    /**
     * Test processPaths method with a path becoming empty after filtering.
     */
    public function testProcessPathsWithEmptyPathAfterFiltering()
    {
        $swaggerService = new SwaggerFileService();

        // Example Swagger content
        $content = [
          'paths' => [
            '/example' => [
              'post' => [
                'tags' => ['excludedTag'],
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // Set to exclude 'excludedTag' tags
        $atcOptions = [
          'exclude' => [
            'operations' => [
              'tags' => ['excludedTag'],
            ],
          ],
        ];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayNotHasKey('/example', $processedContent['paths']);

        // Delete temporary file
        unlink($filePath);
    }



    public function testProcessSecuritySchemes()
    {
        $swaggerService = new SwaggerFileService();

        // Exemple de contenu Swagger
        $content = [
          'components' => [
            'securitySchemes' => [
              'api_key' => [
                'type' => 'apiKey"',
                'name' => 'X-API-KEY',
                'in' => 'header',
              ],
              'oauth2' => [
                'type' => 'oauth2',
                'description' => 'OAuth2 security',
                'in' => 'header',
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // Set to include only 'api_key' tags
        $atcOptions = [
          'include' => [
            'securitySchemes' => [
              'names' => ['api_key'],
            ],
          ],
        ];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayHasKey('securitySchemes', $processedContent['components']);
        $this->assertArrayHasKey('api_key', $processedContent['components']['securitySchemes']);
        $this->assertArrayNotHasKey('oauth2', $processedContent['components']['securitySchemes']);

        // Delete temporary file
        unlink($filePath);
    }

    /**
     * Test the processSecuritySchemes method with exclude options.
     */
    public function testProcessSecuritySchemesWithExclude()
    {
        $swaggerService = new SwaggerFileService();

        // Example of Swagger content
        $content = [
          'components' => [
            'securitySchemes' => [
              'apiKey' => [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'X-API-KEY',
              ],
              'oauth2' => [
                'type' => 'oauth2',
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // Configure to exclude 'oauth2'.
        $atcOptions = [
          'exclude' => [
            'securitySchemes' => [
              'names' => ['oauth2'],
            ],
          ],
        ];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check modifications
        $this->assertArrayHasKey('apiKey', $processedContent['components']['securitySchemes']);
        $this->assertArrayNotHasKey('oauth2', $processedContent['components']['securitySchemes']);

        // Delete temporary file
        unlink($filePath);
    }

    /**
     * Test the processSecuritySchemes method with no specific names provided.
     */
    public function testProcessSecuritySchemesWithNoSpecificNames()
    {
        $swaggerService = new SwaggerFileService();

        // Exemple de contenu Swagger
        $content = [
          'components' => [
            'securitySchemes' => [
              'apiKey' => [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'X-API-KEY',
              ],
              'oauth2' => [
                'type' => 'oauth2',
                // ... other properties ...
              ],
            ],
          ],
        ];

        // Creating a temporary file
        $filePath = $this->getTemporaryPath($content);

        // No specific name provided
        $atcOptions = [];
        $swaggerService->processSwaggerFile($atcOptions, $filePath);

        // Reading file contents after processing
        $processedContent = $this->getProcessedContent($filePath);

        // Assertions to check that nothing has been modified
        $this->assertArrayHasKey('apiKey', $processedContent['components']['securitySchemes']);
        $this->assertArrayHasKey('oauth2', $processedContent['components']['securitySchemes']);

        // Delete temporary file
        unlink($filePath);
    }
}
