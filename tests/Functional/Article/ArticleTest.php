<?php

namespace App\Tests\Functional\Article;

use App\Entity\Article;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleTest extends WebTestCase
{

    public function testArticleAll(): void
    {
        $client = static::createClient();
        /** @var UrlgeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');
        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var ArticleRepository */
        $articleRepositiory = $entityManager->getRepository(Article::class);
        $article = $articleRepositiory->find(1);


       
        $client->request(Request::METHOD_GET,$urlGeneratorInterface->generate('app_main'));  
        $this->assertResponseIsSuccessful();
    }

}