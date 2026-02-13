<?php

namespace Photo\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Photo\Application\Service\ImportPhoenixPhotosService;
use App\Photo\Domain\Entity\Photo;
use App\Photo\Infrastructure\Http\PhoenixClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use stdClass;

class ImportPhoenixPhotosServiceTest extends TestCase
{
    private function makeUser(?string $token): User
    {
        $user = new User('user', 'www@mail.pl', 'asd', 'asd', 30, 'bio');

        if ($token === null) {
            $user->clearPhoenixApiToken();
        } else {
            $user->assignPhoenixApiToken($token);
        }

        return $user;
    }

    public function testImportThrowsWhenTokenMissing(): void
    {
        $phoenix = $this->createMock(PhoenixClient::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $phoenix->expects(self::never())->method('fetchPhotos');
        $em->expects(self::never())->method('flush');
        $em->expects(self::never())->method('persist');

        $svc = new ImportPhoenixPhotosService($phoenix, $em);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Phoenix API token is not set.');

        $svc->import($this->makeUser(null));
    }

    public function testImportPersistsOnlyNewValidPhotosAndFlushesOnce(): void
    {
        $phoenix = $this->createMock(PhoenixClient::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(ObjectRepository::class);

        $user = $this->makeUser('token123');

        $phoenix->expects(self::once())
            ->method('fetchPhotos')
            ->with('token123')
            ->willReturn([
                ['photo_url' => ' https://img/1 '],
                ['photo_url' => ''],
                [],
                ['photo_url' => 'https://img/1'],
                ['photo_url' => 'https://img/2'],
            ]);

        $em->expects(self::any())
            ->method('getRepository')
            ->with(Photo::class)
            ->willReturn($repo);

        $repo->expects(self::exactly(3))
            ->method('findOneBy')
            ->willReturnCallback(function (array $criteria) {
                $url = $criteria['imageUrl'] ?? null;

                static $calls = 0;
                $calls++;

                if ($url === 'https://img/1' && $calls === 1) {
                    return null;
                }

                return new stdClass();
            });

        $em->expects(self::once())
            ->method('persist')
            ->with(self::callback(function ($entity) use ($user): bool {
                if (!$entity instanceof Photo) {
                    return false;
                }

                return true;
            }));

        $em->expects(self::once())->method('flush');

        $svc = new ImportPhoenixPhotosService($phoenix, $em);

        $imported = $svc->import($user);

        self::assertSame(1, $imported);
    }

    public function testImportFlushesEvenWhenNothingImported(): void
    {
        $phoenix = $this->createMock(PhoenixClient::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(ObjectRepository::class);

        $user = $this->makeUser('token123');

        $phoenix->method('fetchPhotos')->willReturn([
            ['photo_url' => 'https://img/exists'],
        ]);

        $em->method('getRepository')->with(Photo::class)->willReturn($repo);

        $repo->method('findOneBy')->willReturn(new stdClass());

        $em->expects(self::never())->method('persist');
        $em->expects(self::once())->method('flush');

        $svc = new ImportPhoenixPhotosService($phoenix, $em);

        self::assertSame(0, $svc->import($user));
    }
}
