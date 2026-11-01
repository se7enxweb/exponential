<?php
/**
 * File containing the expUpgradeStepReviewHold class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The review hold (expReviewHold): the object state group review_hold with its states not_held (every object) and
 * held, the limitation StateGroup_review_hold = not_held on every content/edit and content/publish policy that does not
 * name the group yet, and the role "Review hold" (state/assign of the group, assigned to nobody). Module-wide policies
 * are listed as notes: a hold does not stop them.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepReviewHold extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-review-hold';
    }

    public function description()
    {
        return 'The review hold: the state group review_hold, its limitation on content/edit and content/publish, the role "Review hold"';
    }

    protected function changes( expUpgradeContext $context )
    {
        return expReviewHold::changes();
    }

    protected function write( expUpgradeContext $context )
    {
        $done = expReviewHold::apply();
        foreach ( expReviewHold::notes() as $note )
            $done[] = 'NOTE ' . $note;
        return $done;
    }
}
