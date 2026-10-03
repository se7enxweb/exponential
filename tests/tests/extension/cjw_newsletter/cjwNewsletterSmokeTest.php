<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
class cjwNewsletterSmokeTest extends cjwNewsletterTestCase
{
    public function testIndexView()
    {
        $r = $this->runView( 'index' );
        $this->assertViewOk( $r );
    }
    public function testSubscriberAndEdition()
    {
        $u = $this->newSubscriber( 'a' );
        $this->assertNotNull( $this->subscriptionOf( $u ) );
        $e = $this->newEdition();
        $c = $this->editionContent( $e );
        $this->assertInstanceOf( 'CjwNewsletterEdition', $c );
    }
}
