<?php

namespace App\Tests;

use App\Factory\SessionFactory;
use Symfony\Component\Panther\PantherTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SessionTest extends PantherTestCase
{
	private const SESSION_TITLE = 'Et quia odit ipsam doloremque';

	use ResetDatabase, Factories;


	/**
	 * @return void
	 */
	public function testViewSession()
	{
		$client = static::createPantherClient();
		$crawler = $client->request('GET','/');
		$this->assertSame('EvenVote Schedule', $crawler->filter('h1:first-of-type')->text());

		$client->takeScreenshot('/tmp/Symfony/test_view_real_time_panther.png');
	}

	/**
	 * @throws \Facebook\WebDriver\Exception\NoSuchElementException
	 * @throws \Facebook\WebDriver\Exception\TimeoutException
	 */
	public function testEmptyFeedbackList()
	{

		$session = SessionFactory::createOne();
		$session->enableAutoRefresh();

		$client = static::createPantherClient();
		$client->request('GET', '/');
		$crawler = $client->clickLink($session->getTitle());
		$this->assertSame($session->getTitle(), $crawler->filter('h1:first-of-type')->text());
		$client->waitFor('#feedback div p');
		$this->assertSame('Pas de feedback pour le moment', $crawler->filter('#feedback div p')->text());

		$client->takeScreenshot('/tmp/Symfony/test_feedback_real_time_panther.png');

	}

}