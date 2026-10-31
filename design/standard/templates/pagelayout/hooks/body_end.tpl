{* The end of the page body, for extensions: each template of site.ini [PageLayoutHooks] BodyEnd[] (a design: path),
   in the order of the list. A pagelayout of every engine includes this one file just before </body>:
     .tpl   {include uri='design:pagelayout/hooks/body_end.tpl'}
     .stpl  {expinclude uri='design:pagelayout/hooks/body_end.tpl'}
     .twig  {% expinclude uri='design:pagelayout/hooks/body_end.tpl' %}
   Without an entry it prints nothing. A hook template is rendered inside the pagelayout's cache blocks wherever the
   pagelayout keeps the whole page in one: what it prints may depend only on what those blocks key on (the
   siteaccess, the node, the signed-in state and the roles), never on the user themselves.
   Guide: doc/eng-us/features/6.0/pagelayout-hooks.md *}{if ezini_hasvariable( 'PageLayoutHooks', 'BodyEnd', 'site.ini' )}{foreach ezini( 'PageLayoutHooks', 'BodyEnd', 'site.ini' ) as $exp_body_end_hook}{if $exp_body_end_hook|ne( '' )}{include uri=concat( 'design:', $exp_body_end_hook )}{/if}{/foreach}{/if}
