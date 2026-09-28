{* Kernel error 6 (eZError::KERNEL_FORM_TOKEN_REFUSED): a form was sent without
   its form token, or with one that does not match, and was refused with 403.

   The error view passes $parameters (ezpFormTokenRefusal::templateParameters()):
     reason     'missing' or 'wrong'
     referrer   the page the form was on, checked to be on this site, or ''
     retry_url  where "reload the form" goes: the referrer, else the URL posted to
     is_ajax    the request wanted JSON (it gets JSON, not this page)

   A sign-in form is told apart by the page it came from (user/login): the
   form token check only applies to signed-in users, so a sign-in form is
   refused when the visitor signed in meanwhile in another window.

   This file only works out the links; the page itself is error/parts/formtoken.tpl,
   which each design (standard, admin, admin3, media) gives its own look. *}
{def $formtoken_parameters = cond( and( is_set( $parameters ), is_array( $parameters ) ), $parameters, hash() )
     $formtoken_home = '/'|ezurl( 'no' )
     $formtoken_reload = $formtoken_home
     $formtoken_reason = 'missing'}
{if and( is_set( $formtoken_parameters.retry_url ), is_string( $formtoken_parameters.retry_url ), $formtoken_parameters.retry_url|ne( '' ), $formtoken_parameters.retry_url|ne( '/' ) )}
    {set $formtoken_reload = $formtoken_parameters.retry_url}
{elseif and( is_set( $formtoken_parameters.referrer ), is_string( $formtoken_parameters.referrer ), $formtoken_parameters.referrer|ne( '' ) )}
    {set $formtoken_reload = $formtoken_parameters.referrer}
{/if}
{if and( is_set( $formtoken_parameters.reason ), eq( $formtoken_parameters.reason, 'wrong' ) )}
    {set $formtoken_reason = 'wrong'}
{/if}
{include uri='design:error/parts/formtoken.tpl'
         reason=$formtoken_reason
         reload_url=$formtoken_reload
         home_url=$formtoken_home
         signed_out=and( is_set( $current_user ), $current_user, eq( $current_user.contentobject_id, $anonymous_user_id ) )
         login_form=$formtoken_reload|contains( 'user/login' )}
{undef $formtoken_parameters $formtoken_home $formtoken_reload $formtoken_reason}
