<?php

namespace Tests\Unit\FileUpload;

use App\Common\Services\FileUpload\Contracts\UploadPipelineInterface;
use App\Common\Services\FileUpload\DTOs\FileDeleteCommand;
use App\Common\Services\FileUpload\Exceptions\InvalidMimeTypeException;
use App\Common\Services\FileUpload\FileUploadService;
use App\Common\Services\FileUpload\TenantPathResolver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function testUploadStoresFileUnderTenantPath(): void
    {
        $service = $this->makeService();
        $file = UploadedFile::fake()->createWithContent('nota.txt', 'contenido de prueba');

        $result = $service->upload($file);

        Storage::disk('local')->assertExists($result->path);
        $this->assertSame('local', $result->disk);
        $this->assertStringStartsWith('tenant_42/test/files/' . now()->format('Y/m') . '/', $result->path);
        $this->assertSame('text/plain', $result->mimeType);
        $this->assertGreaterThan(0, $result->size);
        $this->assertNotEmpty($result->url);
    }

    public function testUploadGeneratesUniqueFilenames(): void
    {
        $service = $this->makeService();

        $first = $service->upload(UploadedFile::fake()->createWithContent('a.txt', 'aaa'));
        $second = $service->upload(UploadedFile::fake()->createWithContent('a.txt', 'bbb'));

        $this->assertNotSame($first->path, $second->path);
    }

    public function testDeleteRemovesStoredFile(): void
    {
        $service = $this->makeService();
        $result = $service->upload(UploadedFile::fake()->createWithContent('borrar.txt', 'x'));

        $deleted = $service->delete(new FileDeleteCommand($result->disk, $result->path));

        $this->assertTrue($deleted);
        Storage::disk('local')->assertMissing($result->path);
    }

    public function testUploadRejectsDisallowedMimeType(): void
    {
        $service = $this->makeService(allowedMimeTypes: ['application/pdf']);

        $this->expectException(InvalidMimeTypeException::class);

        $service->upload(UploadedFile::fake()->createWithContent('nota.txt', 'texto, no pdf'));
    }

    public function testUploadAcceptsAllowedMimeType(): void
    {
        $service = $this->makeService(allowedMimeTypes: ['text/plain']);

        $result = $service->upload(UploadedFile::fake()->createWithContent('ok.txt', 'texto plano'));

        Storage::disk('local')->assertExists($result->path);
    }

    public function testUploadAppliesPipelines(): void
    {
        $pipeline = new class() implements UploadPipelineInterface {
            public bool $called = false;

            public function process(UploadedFile $file): UploadedFile
            {
                $this->called = true;

                return $file;
            }
        };

        $service = $this->makeService(pipelines: [$pipeline]);
        $service->upload(UploadedFile::fake()->createWithContent('pipe.txt', 'x'));

        $this->assertTrue($pipeline->called);
    }

    private function makeService(?array $allowedMimeTypes = null, array $pipelines = []): FileUploadService
    {
        $resolver = new class() extends TenantPathResolver {
            protected function getTenantId(): ?string
            {
                return '42';
            }
        };

        return new class($resolver, $allowedMimeTypes, $pipelines) extends FileUploadService {
            public function __construct(
                TenantPathResolver $pathResolver,
                private readonly ?array $allowedMimeTypes,
                private readonly array $pipelines,
            ) {
                parent::__construct($pathResolver);
            }

            protected function getDirectory(): string
            {
                return 'test/files';
            }

            protected function getAllowedMimeTypes(): ?array
            {
                return $this->allowedMimeTypes;
            }

            protected function getPipelines(): array
            {
                return $this->pipelines;
            }
        };
    }
}
