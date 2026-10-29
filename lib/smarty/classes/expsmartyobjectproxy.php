<?php
/**
 * An object of the template system as a Smarty template sees it: $node.name reads attribute( 'name' ). The work is
 * done by expTemplateObjectProxy of the shared bridge (lib/templatebridge); isset() is true for an attribute that
 * exists and is not null.
 *
 * @package lib
 */
class expSmartyObjectProxy extends expTemplateObjectProxy
{
    /**
     * @return object the object of the template system
     */
    public function expSmartyObject()
    {
        return $this->expObject();
    }
}
