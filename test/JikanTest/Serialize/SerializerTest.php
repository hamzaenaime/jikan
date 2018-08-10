<?php

namespace JikanTest\Serializer;

use Jikan\Model\Common\MalUrl;
use Jikan\MyAnimeList\MalClient;
use Jikan\Request\Anime\AnimeRequest;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class JikanTest
 */
class SerializerTest extends TestCase
{
    /**
     * @var MalClient
     */
    private $jikan;

    public function setUp()
    {
        $this->jikan = new MalClient;
    }

    /**
     * @test
     * @vcr JikanTest_it_gets_anime.yaml
     */
    public function it_gets_anime()
    {
        $anime = $this->jikan->getAnime(new AnimeRequest(21));
        self::assertInstanceOf(\Jikan\Model\Anime\Anime::class, $anime);


        // Serialization POC
        $serializer = (new SerializerBuilder())
            ->addMetadataDir(__DIR__.'/metadata')
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerHandler('serialization', MalUrl::class, 'json',
                    function ($visitor, MalUrl $obj, array $type) {
                        return $obj->getUrl();
                    }
                );
            })
            ->build();

        echo $serializer->serialize($anime, 'json');
    }
}
