<?php

use Instagram2Vk\Classes\InstagramCrawler;

class InstagramTest extends PHPUnit_Framework_TestCase
{
    private $access_token = null;
    private $access_token_username = null;

    public function setUp()
    {
        parent::setUp();

        $this->access_token = getenv('INSTAGRAM_ACCESS_TOKEN');
        $this->access_token_username = getenv('INSTAGRAM_ACCESS_TOKEN_USERNAME');

    }

    /**
     * Test that we can gather user media
     */
    public function test_gather_user_media() {

        $client = new GuzzleHttp\Client();
        $crawler = new InstagramCrawler($client,$this->access_token,[/*no tags*/],["dmitriy.lezhnev"]);

        $data = $crawler->crawl();

        $this->assertNotEmpty($data['users']);
        $this->assertNotEmpty($data['users'][$this->access_token_username]);

    }

    /**
     * Test that invalid user handles properly
     */
    public function test_invalid_user() {

        $client = new GuzzleHttp\Client();
        $crawler = new InstagramCrawler($client,$this->access_token,[/*no tags*/],["worng.user.never.exi7ste31d"]);

        $this->setExpectedException('Instagram2Vk\Exceptions\InstagramException');
        $data = $crawler->crawl();

    }

    /**
     * Test that we can gather tags media
     */
    public function test_gather_tag_media() {

        $client = new GuzzleHttp\Client();
        $crawler = new InstagramCrawler($client,$this->access_token,['russia'],[/*no users*/]);

        $data = $crawler->crawl();

        $this->assertNotEmpty($data['tags']);
        $this->assertTrue(array_key_exists('russia',$data['tags']));

    }

    /**
     * Test that we can gather tags and users media
     */
    public function test_gather_tag_user_media() {

        $client = new GuzzleHttp\Client();
        $crawler = new InstagramCrawler($client,$this->access_token,['russia','moscow'],[$this->access_token_username,'applemusic']);

        $data = $crawler->crawl();

        $this->assertNotEmpty($data['tags']);
        $this->assertNotEmpty($data['users']);
        $this->assertTrue(array_key_exists('russia',$data['tags']));
        $this->assertTrue(array_key_exists('moscow',$data['tags']));

        $this->assertTrue(array_key_exists($this->access_token_username,$data['tags']));

        // @todo enable this test after app approval
        $this->assertTrue(array_key_exists('applemusic',$data['tags']));

    }

}