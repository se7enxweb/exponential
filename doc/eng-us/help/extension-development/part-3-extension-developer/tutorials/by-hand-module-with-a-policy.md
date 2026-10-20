---
title: "By hand: a module protected by a policy"
audience: developer, app-creator
category: extension-development
order: 140
summary: An admin page with a form: a policy with a limitation, POST actions, input validation, a limited access check in PHP and an admin4 template, checked with php -l and the engine.
keywords: tutorial, by hand, policy, FunctionList, limitation, values, hasAccessTo, accessWord, limited, single_post_actions, isCurrentAction, form, POST, form token, admin4, a4-form
type: help
---
# By hand: a module protected by a policy

Tutorial 13. You add an administration page to the `hello` module: a small form that composes a greeting. It shows the
whole security pattern of an admin page: a policy function with a **limitation**, POST actions, validated input, an
access check that respects the limitation, and an admin4 template with no inline CSS or script. The PHP files passed
`php -l`; the template was rendered by the template engine.

## 1. The view and its policy

Extend `extension/mysite_hello/modules/hello/module.php`:

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array(
    'script' => 'world.php',
    'params' => array( 'Name' ),
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart' );

$ViewList['compose'] = array(
    'script' => 'compose.php',
    'functions' => array( 'compose' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'PreviewButton' => 'Preview',
                                    'CancelButton' => 'Cancel' ) );

$Greeting = array(
    'name' => 'Greeting',
    'values' => array( array( 'Name' => 'Hello', 'value' => 'hello' ),
                       array( 'Name' => 'Welcome', 'value' => 'welcome' ) ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['compose'] = array( 'Greeting' => $Greeting );
```

The limitation's `values` are fixed, in the same form as the kernel's `audit/read` and its `Channel` limitation
(`kernel/audit/module.php`). In the role editor, a policy `hello/compose` can now be granted for all greetings or only
for "Hello".

## 2. The view script

`extension/mysite_hello/modules/hello/compose.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/content/dashboard' );
}

// Which greetings may this user compose? 'yes' = all, 'limited' = those of the Greeting limitation.
$all = array( 'hello' => 'Hello', 'welcome' => 'Welcome' );
$access = eZUser::currentUser()->hasAccessTo( 'hello', 'compose' );
$allowed = array();
if ( $access['accessWord'] === 'yes' )
{
    $allowed = $all;
}
else if ( $access['accessWord'] === 'limited' )
{
    foreach ( $access['policies'] as $limitations )
    {
        foreach ( isset( $limitations['Greeting'] ) ? (array)$limitations['Greeting'] : array() as $value )
        {
            if ( isset( $all[$value] ) )
                $allowed[$value] = $all[$value];
        }
    }
}

$preview = false;
$error = false;
if ( $Module->isCurrentAction( 'Preview' ) )
{
    $name = $http->postVariable( 'Name', '' );
    $greeting = $http->postVariable( 'Greeting', '' );
    if ( !is_string( $name ) || trim( $name ) === '' || mb_strlen( $name ) > 100 )
        $error = ezpI18n::tr( 'extension/mysite_hello', 'Enter a name of at most 100 characters.' );
    else if ( !is_string( $greeting ) || !isset( $allowed[$greeting] ) )
        $error = ezpI18n::tr( 'extension/mysite_hello', 'Choose one of the greetings you may use.' );
    else
        $preview = $allowed[$greeting] . ', ' . trim( $name ) . '!';
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'greetings', $allowed );
$tpl->setVariable( 'preview', $preview );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/compose.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/mysite_hello', 'Compose a greeting' ), 'url' => false ) );
```

What it shows:

- The kernel has already refused users without `hello/compose`; the script only narrows by the limitation.
- A posted value is checked for its type (`is_string()`) and against the allowed list, never trusted.
- This view changes nothing, so a preview needs no redirect; a view that stores must redirect after the POST.
- The `ezformtoken` extension adds and checks the form token automatically, so the form below carries none by hand.

## 3. The admin4 template

`extension/mysite_hello/design/admin4/templates/hello/compose.tpl`:

```text
<div class="a4-page">
    <h1 class="a4-pagehead-title">{'Compose a greeting'|i18n( 'extension/mysite_hello' )}</h1>
    {if $error}
        <div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>
    {/if}
    {if $preview}
        <div class="a4-notice is-ok" role="status"><p>{$preview|wash}</p></div>
    {/if}
    {if $greetings|count}
    <form method="post" action={'/hello/compose'|ezurl} class="a4-form">
        <div class="a4-field">
            <label class="a4-label" for="mysite-hello-name">{'Name'|i18n( 'extension/mysite_hello' )}</label>
            <input class="a4-input" id="mysite-hello-name" type="text" name="Name" maxlength="100" required="required" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mysite-hello-greeting">{'Greeting'|i18n( 'extension/mysite_hello' )}</label>
            <select class="a4-input" id="mysite-hello-greeting" name="Greeting">
            {foreach $greetings as $value => $label}
                <option value="{$value|wash}">{$label|wash}</option>
            {/foreach}
            </select>
        </div>
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary" name="PreviewButton">{'Preview'|i18n( 'extension/mysite_hello' )}</button>
            <button type="submit" class="a4-btn" name="CancelButton">{'Cancel'|i18n( 'extension/mysite_hello' )}</button>
        </div>
    </form>
    {else}
        <p class="a4-empty">{'Your roles allow no greeting.'|i18n( 'extension/mysite_hello' )}</p>
    {/if}
</div>
```

Rendered with one allowed greeting and a preview, it printed the success notice `Hello, Ada!` and one option. Every
style comes from the shared components; there is no `style=""` or script in it.

## 4. Try the policy

1. `php -l` both files, clear the caches.
2. Grant `hello/compose` with the limitation Greeting = Hello to a test role, assign it to a test user.
3. Sign in as that user: the form offers only "Hello". Post `welcome` by editing the form in the browser's developer
   tools: the page answers "Choose one of the greetings you may use."
4. A user without `hello/compose` gets the access denied page.

Use a throwaway test user for this, never a shared administrator account.

## Related pages

- Previous: [By hand: a module with a view](by-hand-module-with-a-view.md)
- Next: [By hand: a datatype](by-hand-datatype.md)
- [Policy functions and access checks](../policy-functions-and-access.md), [Security in extensions](../security-in-extensions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
