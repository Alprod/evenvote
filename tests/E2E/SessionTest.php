<?php

namespace App\Tests\E2E;

use App\Factory\SessionFactory;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SessionTest extends PantherTestCase
{
	use ResetDatabase, Factories;

	private const SESSION_TITLE = 'Et quia odit ipsam doloremque';
	private Client $client;
	private Proxy $session;

	protected function setUp(): void
	{
		parent::setUp();
		$this->client = static::createPantherClient();
		$this->session = SessionFactory::createOne();
	}

	/**
	 * @return void
	 */
	public function testViewSession(): void
	{
		$crawler = $this->client->request('GET','/');
		$this->assertSame('EvenVote Schedule', $crawler->filter('h1:first-of-type')->text());

		$this->client->takeScreenshot('/tmp/Symfony/test_view_real_time_panther.png');
	}

	/**
	 * @throws \Facebook\WebDriver\Exception\NoSuchElementException
	 * @throws \Facebook\WebDriver\Exception\TimeoutException
	 */
	public function testEmptyFeedbackList(): void
	{

		$this->session->enableAutoRefresh();

		$this->client->request('GET', '/');
		$crawler = $this->client->clickLink($this->session->getTitle());
		$this->assertSame($this->session->getTitle(), $crawler->filter('h1:first-of-type')->text());
		$this->client->waitFor('#feedback div p');
		$this->assertSame('Pas de feedback pour le moment', $crawler->filter('#feedback div p')->text());

		$this->client->takeScreenshot('/tmp/Symfony/test_feedback_list_empty_panther.png');
	}

	/**
	 * @throws \Exception
	 */
	public function testGivenFeedback(): void
	{
		$this->session->enableAutoRefresh();

		$this->client->request('GET', '/session/'.$this->session->getId());

		$this->client->getMouse()->clickTo('.vue-star-rating-pointer:nth-of-type(3)');
		$crawler = $this->client->submitForm('Valider',[
			'author' => 'François',
			'comment' => 'Je vous ai trouver génial'
		]);


		$this->client->waitFor('#feedback div ul li');
		$this->assertStringContainsString( 'Je vous ai trouver génial', $crawler->filter( '#feedback li')->text());
		$this->assertNotSame('Pas de feedback pour le moment', $crawler->filter('#feedback div p')->text());

		$this->client->takeScreenshot('/tmp/Symfony/test_given_feedback_panther.png');

	}
}