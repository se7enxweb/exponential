<?php
/** A schedule condition that is never met (cjwNewsletterEditorialTest ED-16); loaded after the kernel started. */
class cjwNewsletterEditorialTestConditionNo implements CjwNewsletterScheduleConditionInterface
{
    public static function isMet( $schedule, $now ) { return false; }
    public static function name() { return 'never (phpunit)'; }
}
