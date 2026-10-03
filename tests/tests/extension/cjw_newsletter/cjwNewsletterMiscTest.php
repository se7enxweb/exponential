<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** Template operators, extended attribute filters, class installer, log, datatypes, release files. */
class cjwNewsletterMiscTest extends cjwNewsletterTestCase
{
    public function testTheErrorCollectorOfThisSuiteSeesWarningsRaisedInTheExtension()
    {
        $this->assertSame( array(), $this->phpIssues );
        call_user_func( $this->collector, E_WARNING, 'probe', '/x/extension/cjw_newsletter/classes/probe.php', 1 );
        call_user_func( $this->collector, E_WARNING, 'elsewhere', '/x/kernel/probe.php', 1 );
        $this->assertSame( array( 'probe at extension/cjw_newsletter/classes/probe.php:1' ), $this->phpIssues );
        $this->phpIssues = array();
    }

    public function testTemplateOperators()
    {
        $op = new CjwNewsletterOperators();
        $this->assertSame( array( 'cjw_newsletter_preg_replace', 'cjw_newsletter_str_replace', 'cjw_newsletter_variable' ), $op->operatorList() );
        $this->assertTrue( $op->namedParameterPerOperator() );
        $this->assertArrayHasKey( 'cjw_newsletter_variable', $op->namedParameterList() );
        $tpl = null;
        $value = 'Hello World';
        $op->modify( $tpl, 'cjw_newsletter_str_replace', array(), '', '', $value, array( 'string_search' => 'World', 'string_replace' => 'There' ) );
        $this->assertSame( 'Hello There', $value );
        $op->modify( $tpl, 'cjw_newsletter_preg_replace', array(), '', '', $value, array( 'string_search' => '/H.llo/', 'string_replace' => 'Bye' ) );
        $this->assertSame( 'Bye There', $value );
        $op->modify( $tpl, 'cjw_newsletter_variable', array(), '', '', $value, array( 'variable_name' => 'available_subscription_status_id_name_array' ) );
        $this->assertIsArray( $value );
        $op->modify( $tpl, 'cjw_newsletter_variable', array(), '', '', $value, array( 'variable_name' => 'nothing' ) );
        $this->assertFalse( $value );
    }

    public function testOperatorsAreRegisteredForTemplates()
    {
        include 'extension/cjw_newsletter/autoloads/eztemplateautoload.php';
        $this->assertSame( 'CjwNewsletterOperators', $eZTemplateOperatorArray[0]['class'] );
        $this->assertFileExists( $eZTemplateOperatorArray[0]['script'] );
    }

    public function testListFilterBuildsAConditionForTheSiteaccesses()
    {
        $filter = new CjwNewsletterListFilter();
        $this->assertSame( array( 'tables' => false, 'joins' => false, 'columns' => false ), $filter->createSqlParts( array() ) );
        $GLOBALS['eZCurrentAccess']['name'] = 'site';
        $parts = $filter->createSqlParts( array( 'siteaccess' => 'current_siteaccess' ) );
        $this->assertStringContainsString( 'ezcontentobject_tree.contentobject_id IN (', $parts['joins'] );
        $this->assertStringContainsString( (string)self::LIST_OBJECT_ID, $parts['joins'], 'the test list is on siteaccess site' );
        $parts = $filter->createSqlParts( array( 'siteaccess' => array( 'a', 'b' ) ) );
        $this->assertIsString( $parts['joins'] );
        $parts = $filter->createSqlParts( array( 'siteaccess' => "x' OR '1'='1" ) );
        $this->assertStringContainsString( ' IN (0 )', $parts['joins'], 'a hostile name matches no list' );
    }

    public function testListFilterWithSeveralSiteaccessesUsesAllOfThem()
    {
        $filter = new CjwNewsletterListFilter();
        $GLOBALS['eZCurrentAccess']['name'] = 'site';
        $one = $filter->createSqlParts( array( 'siteaccess' => array( 'site' ) ) );
        $two = $filter->createSqlParts( array( 'siteaccess' => array( 'site', 'no_such_access' ) ) );
        $this->assertStringContainsString( (string)self::LIST_OBJECT_ID, $one['joins'] );
        $this->assertStringNotContainsString( (string)self::LIST_OBJECT_ID, $two['joins'], 'a list must be on every named siteaccess' );
    }

    public function testEditionFilterBuildsAConditionPerStatus()
    {
        $filter = new CjwNewsletterEditionFilter();
        foreach ( array( 'draft', 'process', 'archive', 'abort' ) as $status )
        {
            $parts = $filter->createSqlParts( array( 'status' => $status ) );
            $this->assertStringContainsString( 'cjwnl_edition_send', $parts['joins'], $status );
            $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree WHERE ' . $parts['joins'] . ' 1=1' );
            $this->assertArrayHasKey( 'c', $rows[0], $status . ' is valid SQL' );
        }
        $this->assertSame( array( 'tables' => false, 'joins' => false, 'columns' => false ), $filter->createSqlParts( array( 'status' => 'nonsense' ) ) );
    }

    public function testEditionFilterFindsAnArchivedEdition()
    {
        $this->newSubscriber( 'filtarch' );
        $edition = $this->newEdition();
        $this->editionContent( $edition )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $parts = ( new CjwNewsletterEditionFilter() )->createSqlParts( array( 'status' => 'archive' ) );
        $rows = eZDB::instance()->arrayQuery( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE ' . $parts['joins'] . ' 1=1' );
        $this->assertContains( (string)$edition->attribute( 'id' ), array_map( 'strval', array_column( $rows, 'contentobject_id' ) ) );
    }

    public function testClassInstallerReadsItsPackagesAndKnowsItsClasses()
    {
        $found = array();
        foreach ( CjwNewsletterClassInstaller::packageFiles() as $package )
        {
            $this->assertFileExists( $package );
            $found = array_merge( $found, array_keys( CjwNewsletterClassInstaller::readClassXml( $package ) ) );
        }
        $this->assertCount( 6, $found, 'six class definitions in the packages' );
        $this->assertSame( array(), CjwNewsletterClassInstaller::readClassXml( '/no/such/package.ezpkg' ) );
        foreach ( CjwNewsletterClassInstaller::classIdentifiers() as $identifier )
            $this->assertNotNull( eZContentClass::fetchByIdentifier( $identifier ), $identifier );
    }

    public function testClassInstallerIsIdempotent()
    {
        $report = CjwNewsletterClassInstaller::install();
        $this->assertCount( 6, $report );
        foreach ( $report as $identifier => $state )
            $this->assertSame( 'already present', $state, $identifier );
    }

    public function testLogWritesWithoutFailing()
    {
        CjwNewsletterLog::writeInfo( 'nltest info', 'tests', 'misc', array( 'k' => 'v' ) );
        CjwNewsletterLog::writeNotice( 'nltest notice', 'tests', 'misc', array( 'k' => 'v' ) );
        CjwNewsletterLog::writeError( 'nltest error', 'tests', 'misc', array( 'k' => 'v' ) );
        CjwNewsletterLog::writeDebug( 'nltest debug', 'tests', 'misc', array( 'k' => 'v' ) );
        $this->assertIsBool( CjwNewsletterLog::getInstance()->isDebugEnabled() );
    }

    public function testEditionDatatypeContentAndStatus()
    {
        $edition = $this->newEdition();
        $map = eZContentObject::fetch( $edition->attribute( 'id' ) )->dataMap();
        $attr = $map['newsletter_edition'];
        $type = $attr->dataType();
        $this->assertInstanceOf( 'CjwNewsletterEditionType', $type );
        $this->assertTrue( $type->hasObjectAttributeContent( $attr ) );
        $this->assertInstanceOf( 'CjwNewsletterEdition', $type->objectAttributeContent( $attr ) );
        $this->assertSame( '', $type->metaData( $attr ) );
        $this->assertFalse( $type->isIndexable() );
        $this->assertIsString( $type->title( $attr ) );
        $this->assertIsString( $type->toString( $attr ) );
    }

    public function testEditionDatatypeRefusesToChangeAnEditionInProcess()
    {
        $this->newSubscriber( 'dtproc' );
        $edition = $this->newEdition();
        $this->editionContent( $edition )->createNewsletterSendObject( time() + 86400 );
        $attr = eZContentObject::fetch( $edition->attribute( 'id' ) )->dataMap()['newsletter_edition'];
        $http = eZHTTPTool::instance();
        $this->assertSame( eZInputValidator::STATE_INVALID, $attr->dataType()->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $attr ) );
    }

    public function testListDatatypeValidatesTheForm()
    {
        $attr = eZContentObject::fetch( self::LIST_OBJECT_ID )->dataMap()['newsletter_list'];
        $type = $attr->dataType();
        $this->assertInstanceOf( 'CjwNewsletterListType', $type );
        $id = $attr->attribute( 'id' );
        $prefix = 'ContentObjectAttribute_CjwNewsletterList_';
        $good = array( $prefix . 'MainSiteaccess_' . $id => 'site', $prefix . 'SiteaccessArray_' . $id => array( 'site' ),
            $prefix . 'OutputFormatArray_' . $id => array( 0, 1 ), $prefix . 'EmailSender_' . $id => 'newsletter@example.com',
            $prefix . 'EmailReplyTo_' . $id => '', $prefix . 'EmailReturnPath_' . $id => '', $prefix . 'EmailSenderName_' . $id => 'Name',
            $prefix . 'EmailReceiverTest_' . $id => 'a@example.com;b@example.com', $prefix . 'AutoApproveRegisterdUser_' . $id => 1,
            $prefix . 'PersonalizeContent_' . $id => 1 );
        $_POST = $good;
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attr ) );
        foreach ( array( $prefix . 'EmailSender_' . $id => 'not an address', $prefix . 'MainSiteaccess_' . $id => '',
                         $prefix . 'EmailReceiverTest_' . $id => 'x@example.com;broken', $prefix . 'OutputFormatArray_' . $id => array() ) as $field => $value )
        {
            $_POST = array_merge( $good, array( $field => $value ) );
            $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attr ), $field );
        }
        $_POST = array_merge( $good, array( $prefix . 'SiteaccessArray_' . $id => 'a string, not an array' ) );
        $this->assertContains( $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attr ),
            array( eZInputValidator::STATE_ACCEPTED, eZInputValidator::STATE_INVALID ), 'a posted string is no fatal' );
        $this->assertArrayHasKey( 'available_output_format_array', $type->classAttributeContent( $attr->attribute( 'contentclass_attribute' ) ) );
    }

    public function testReleaseFilesAgree()
    {
        $info = cjw_newsletterInfo::info();
        $this->assertSame( cjw_newsletterInfo::SOFTWARE_VERSION, $info['Version'] );
        $xml = simplexml_load_file( 'extension/cjw_newsletter/extension.xml' );
        $this->assertSame( cjw_newsletterInfo::SOFTWARE_VERSION, (string)$xml->metadata->version );
        $manifest = file( 'extension/cjw_newsletter/share/filelist.md5', FILE_IGNORE_NEW_LINES );
        $this->assertStringContainsString( 'version: ' . cjw_newsletterInfo::SOFTWARE_VERSION, implode( "\n", array_slice( $manifest, 0, 4 ) ) );
        $this->assertSame( 'GNU General Public License v2.0 (or any later version)', $info['License'] );
        $this->assertStringContainsString( 'se7enxweb', $info['Info_url'] );
    }

    public function testEveryPhpFileOfTheExtensionHasNoSyntaxError()
    {
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( 'extension/cjw_newsletter', FilesystemIterator::SKIP_DOTS ) );
        $count = 0;
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.php' )
                continue;
            try { token_get_all( file_get_contents( $file->getPathname() ), TOKEN_PARSE ); $out = 'No syntax errors'; } catch ( ParseError $e ) { $out = $e->getMessage(); }
            $this->assertStringContainsString( 'No syntax errors', (string)$out, $file->getPathname() );
            $count++;
        }
        $this->assertGreaterThan( 100, $count );
    }

    public function testNoPhp8BreakersAreLeftInTheCode()
    {
        $patterns = array( '/\beach\s*\(/', '/create_function\s*\(/', '/\bmoney_format\s*\(/', '/\butf8_(en|de)code\s*\(/', '/\bmysql_[a-z_]+\s*\(/', '/\bereg[i]?(_replace)?\s*\(/',
                           '/\bsplit\s*\(/', '/\$[a-zA-Z_]+\{[0-9\'"$a-z]+\}/', '/\(unset\)/', '/\bget_magic_quotes_gpc\s*\(/' );
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( 'extension/cjw_newsletter', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.php' )
                continue;
            $code = '';
            foreach ( token_get_all( file_get_contents( $file->getPathname() ) ) as $token )
                if ( is_array( $token ) && !in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT, T_CONSTANT_ENCAPSED_STRING, T_INLINE_HTML ) ) )
                    $code .= $token[1];
                elseif ( !is_array( $token ) )
                    $code .= $token;
            foreach ( $patterns as $pattern )
                $this->assertDoesNotMatchRegularExpression( $pattern, $code, $file->getPathname() . ' ' . $pattern );
        }
    }

    public function testVirtualListHasARealConstructorSoPhp8RunsIt()
    {
        // an old-style constructor (a method named like the class) is not called by PHP 8
        $constructor = ( new ReflectionClass( 'CjwNewsletterListVirtual' ) )->getMethod( '__construct' );
        $this->assertSame( 'CjwNewsletterListVirtual', $constructor->getDeclaringClass()->getName() );
        $list = new CjwNewsletterListVirtual();
        $this->assertSame( 1, (int)$list->attribute( 'is_virtual' ) );
        $this->assertStringContainsString( '<filters', $list->generateFilterXML() );
        $this->assertTrue( $list->addFilter( 'cjwnl_email' ) );
        $this->assertCount( 1, $list->getFilterTypesActive() );
        $this->assertNotEmpty( $list->getFilterTypesAvailable() );
        $this->assertTrue( $list->removeFilterByIdex( 0 ) );
    }
}
