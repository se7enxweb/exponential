<?php
/**
 * Class attribute names are escaped where the standard and base designs print them: the registration form,
 * the comment edit form, the poll result and the version diff of the history view, the "wait until date" workflow
 * event (class and class attribute names) and the poll question and answers of the base design. The name is editable by anyone
 * who may edit classes and is shown to visitors.
 *
 * Source-text checks: each pins the escaped form and fails when the raw output comes back.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/StandardClassAttributeNameEscapingTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class StandardClassAttributeNameEscapingTest extends PHPUnit\Framework\TestCase
{
    public static function templateProvider()
    {
        return array(
            'user registration' => array( 'design/standard/templates/user/register.tpl',
                '$ContentObjectAttribute:item.contentclass_attribute.name', 1 ),
            'comment edit' => array( 'design/base/override/templates/edit/comment.tpl',
                '$attribute.contentclass_attribute.name', 3 ),
            'poll result' => array( 'design/standard/templates/content/collectedinfo/poll.tpl',
                '$:item.contentclass_attribute.name', 2 ),
            'history diff' => array( 'design/standard/templates/content/history.tpl',
                '$attr.contentclass_attribute.name', 1 ),
            // the "wait until date" workflow event of the standard design: class and class attribute names
            'wait until date edit, classes' => array( 'design/standard/templates/workflow/eventtype/edit/event_ezwaituntildate.tpl',
                '$HasClasses:item.name', 1 ),
            'wait until date edit, class attributes' => array( 'design/standard/templates/workflow/eventtype/edit/event_ezwaituntildate.tpl',
                '$HasClassAttributes:item.name', 1 ),
            'wait until date edit, entry class' => array( 'design/standard/templates/workflow/eventtype/edit/event_ezwaituntildate.tpl',
                '$Entries:item.class_name', 1 ),
            'wait until date edit, entry attribute' => array( 'design/standard/templates/workflow/eventtype/edit/event_ezwaituntildate.tpl',
                '$Entries:item.classattribute_name', 1 ),
            'wait until date view, entry class' => array( 'design/standard/templates/workflow/eventtype/view/event_ezwaituntildate.tpl',
                '$Entries:item.class_name', 1 ),
            'wait until date view, entry attribute' => array( 'design/standard/templates/workflow/eventtype/view/event_ezwaituntildate.tpl',
                '$Entries:item.classattribute_name', 1 ),
            // the poll of the base design: the question and the answers editors enter, shown to visitors
            'poll result question' => array( 'design/base/override/templates/collectedinfo/poll_result.tpl',
                '$contentobject_attribute.content.name', 1 ),
            'poll result answer' => array( 'design/base/override/templates/collectedinfo/poll_result.tpl',
                '$:item.value', 1 ),
            'embedded poll question' => array( 'design/base/override/templates/embed/poll.tpl',
                '$attribute.content.name', 1 ),
            'embedded poll answer' => array( 'design/base/override/templates/embed/poll.tpl',
                '$OptionList:item.value', 1 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('templateProvider')]
    public function testClassAttributeNameIsEscaped( $path, $expression, $count )
    {
        $tpl = AdminFiles::read( dirname( __DIR__, 3 ) . '/' . $path );
        $this->assertSame( $count, substr_count( $tpl, '{' . $expression . '|wash}' ), "$path: escaped output" );
        $this->assertStringNotContainsString( '{' . $expression . '}', $tpl, "$path: raw output" );
    }
}
