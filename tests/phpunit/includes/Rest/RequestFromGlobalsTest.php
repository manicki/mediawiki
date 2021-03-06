<?php

use GuzzleHttp\Psr7\UploadedFile;
use MediaWiki\Rest\RequestFromGlobals;

// phpcs:disable MediaWiki.Usage.SuperGlobalsUsage.SuperGlobals

/**
 * @covers \MediaWiki\Rest\RequestFromGlobals
 */
class RequestFromGlobalsTest extends MediaWikiTestCase {
	/**
	 * @var RequestFromGlobals
	 */
	private $reqFromGlobals;

	protected function setUp() : void {
		parent::setUp();
		$this->reqFromGlobals = new RequestFromGlobals();
	}

	/**
	 * @dataProvider provideGetMethod
	 */
	public function testGetMethod( $serverVars, $expected ) {
		$this->setServerVars( $serverVars );
		$this->assertEquals( $this->reqFromGlobals->getMethod(), $expected );
	}

	public static function provideGetMethod() {
		return [
			[
				[
					'REQUEST_METHOD' => 'POST'
				],
				'POST',
			],
			[
				[],
				'GET',
			]
		];
	}

	public function testGetUri() {
		$this->setServerVars( [
			'REQUEST_URI' => '/test.php'
		] );

		$this->assertEquals( $this->reqFromGlobals->getUri(), '/test.php' );
	}

	/**
	 * @dataProvider provideGetProtocolVersion
	 */
	public function testGetProtocolVersion( $serverVars, $expected ) {
		$this->setServerVars( $serverVars );
		$this->assertEquals( $this->reqFromGlobals->getProtocolVersion(), $expected );
	}

	public static function provideGetProtocolVersion() {
		return [
			[
				[
					'SERVER_PROTOCOL' => 'HTTP/2'
				],
				2
			],
			[
				[],
				1.1
			]
		];
	}

	public function testGetHeaders() {
		$this->setServerVars( [
			'HTTP_HOST' => '[::1]',
			'CONTENT_LENGTH' => 6,
			'CONTENT_TYPE' => 'application/json'
		] );

		$this->assertEquals( $this->reqFromGlobals->getHeaders(), [
			'host' => [ '[::1]' ],
			'content-length' => [ 6 ],
			'content-type' => [ 'application/json' ]
		] );
	}

	public function testGetBody() {
		$this->setServerVars( [
			'REQUEST_METHOD' => 'POST',
			'HTTP_ACCEPT' => 'text/html'
		] );

		$this->assertEquals( $this->reqFromGlobals->getBody(), '' );
	}

	public function testGetServerParams() {
		$serverVars = [
			'SERVER_NAME' => 'www.mediawiki.org',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REQUEST_METHOD' => 'POST',
			'HTTP_HOST' => 'www.mediawiki.org',
			'HTTP_ACCEPT' => 'text/html',
			'REMOTE_PORT' => '1234',
			'SCRIPT_NAME' => '/index.php',
		];
		$this->setServerVars( $serverVars );

		$expectedServerParams = $this->reqFromGlobals->getServerParams();

		$diffs = array_diff_assoc( $expectedServerParams, $serverVars );

		$this->assertEquals( count( $diffs ), 2 );
		$this->assertArrayHasKey( 'REQUEST_TIME_FLOAT', $diffs );
		$this->assertArrayHasKey( 'REQUEST_TIME', $diffs );
	}

	public function testGetCookieParams() {
		$_COOKIE = [
			'testcookie' => true
		];

		$this->assertEquals( $this->reqFromGlobals->getCookieParams(), [ 'testcookie' => true ] );
	}

	public function testGetQueryParams() {
		$query = [
			[
				'title' => 'foo',
				'action' => 'query'
			]
		];
		$_GET = $query;

		$this->assertEquals( $this->reqFromGlobals->getQueryParams(), $query );
	}

	public function testGetUploadedFiles() {
		$_FILES = [
			'file' => [
				'name' => 'Foo.txt',
				'type' => 'text/plain',
				'tmp_name' => '/tmp/foobar',
				'error' => UPLOAD_ERR_OK,
				'size' => 20,
			]
		];

		$this->assertEquals( $this->reqFromGlobals->getUploadedFiles(), [
			'file' => new UploadedFile(
				'/tmp/foobar',
				20, UPLOAD_ERR_OK,
				'Foo.txt',
				'text/plain'
			)
		] );
	}

	public function testGetPostParams() {
		$form = [
				'token' => '983yh4edji',
				'action' => 'login'
		];
		$_POST = $form;

		$this->assertEquals( $this->reqFromGlobals->getPostParams(), $form );
	}

	protected function setServerVars( $vars ) {
		// Don't remove vars which should be available in all SAPI.
		if ( !isset( $vars['REQUEST_TIME_FLOAT'] ) ) {
			$vars['REQUEST_TIME_FLOAT'] = $_SERVER['REQUEST_TIME_FLOAT'];
		}
		if ( !isset( $vars['REQUEST_TIME'] ) ) {
			$vars['REQUEST_TIME'] = $_SERVER['REQUEST_TIME'];
		}
		$_SERVER = $vars;
	}
}
