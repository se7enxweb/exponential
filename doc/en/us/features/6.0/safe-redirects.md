# Safe redirects and "back to where you came from"

This page is for developers who send a visitor somewhere after a form, and for site owners who wonder where Cancel
goes. Since 6 October 2026 every edit form with a Cancel or Discard button goes back to the page it was opened from,
and every redirect target that comes from outside the code (a form field, the session, a URL parameter) passes one
set of rules, kept in `eZRedirectManager` (`kernel/classes/ezredirectmanager.php`).

## Where Cancel goes

| View | Button | Goes to, in this order |
|---|---|---|
| `user/edit` | `CancelButton` | the form's page, the page viewed last, `content/view/sitemap/5` |
| `user/password` | `CancelButton` (and Continue after a change) | the form's page (`RedirectIfDiscarded`, else the older `RedirectOnCancel`, else `RedirectURI`), the page viewed last, `site.ini [SiteSettings] DefaultPage` |
| `user/setting` | `CancelSettingButton` | the form's page, the page viewed last, the user's own page |
| `section/edit` | `CancelButton` | the form's page, the page viewed last, `section/list` |
| `state/edit` | `CancelButton` | the form's page, the page viewed last, the state group |
| `state/group_edit` | `CancelButton` | the form's page, the page viewed last, `state/groups` |
| `role/edit` | `Discard` (after the draft is removed) | the form's page, the page viewed last, `role/list` |
| `class/edit` | `DiscardButton` (after the draft is removed) | the form's page, the page viewed last, the class group the edit started from, `class/grouplist` |
| `class/groupedit` | `DiscardButton` | the form's page, the page viewed last, `class/grouplist` |
| `user/register` | `CancelButton` | the form's page, the page viewed last, `/` |
| `content/edit` | Discard draft | the form's page (kept in the session), else as before |

The last column always ends with the place Cancel went before, so nothing changes where nothing is known.

**The form's page** is the hidden field `RedirectIfDiscarded`. Each of these views sets the template variable
`redirect_if_discarded` and its templates (standard, admin, admin4, and the Admin UI and media designs) send it:

```
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
```

The value is the page viewed before the form was opened, frozen into the form, so Cancel goes back there even when
other pages were opened in another tab in between. A template of your own that renders one of these forms can add the
same line; without it, Cancel uses the page viewed last, which is usually the same.

**The page viewed last** is the session's `LastAccessesURI`, which the kernel records after every page that is not an
edit, administration, browse, authentication or ajax view.

## The rules

A page is taken only when all of these hold; otherwise the next one in the list is tried.

1. It is a path of this site (`/content/view/full/2`, `content/view/full/2`, `/Company/About`, also with the
   siteaccess prefix, a query and a fragment), or an `http`/`https` URL whose host is the current one, one of
   `site.ini [SiteSettings] AllowedRedirectHosts`, or a host of `[SiteAccessSettings] HostMatchMapItems` and
   `HostUriMatchMapItems`.
2. It has no control character (CR and LF split headers; browsers drop tab, CR and LF inside a URL, so
   `/<TAB>/evil.example` is `//evil.example`).
3. It has no backslash before the query: browsers read `\` as `/`, so `/\evil.example` and `\\evil.example` are
   another host.
4. Percent-decoded (up to three times) it does not start with `//`, a scheme, a backslash or a control character
   (`%2F%2Fevil.example`, `/%5Cevil.example`, `javascript%3A...`).
5. It has no scheme other than `http` and `https` (`javascript:`, `data:`, `vbscript:` are refused) and no user name
   or password (`https://own.example@evil.example` names evil.example).
6. It is not the running view itself (Cancel would land on the form again) and not one of
   `site.ini [SiteSettings] DisallowedReturnViews`: views that act on a POST, end the session or start a download
   (`content/action`, `user/logout`, `content/download`, ...).
7. For the page viewed last only: the current user may still view it (a node they can read, a module view their
   roles allow). Such a page could be viewed when it was recorded, but a role may have changed since.

A path with the siteaccess prefix of the current request (`/admin/...` with URI matching) is redirected as it is,
so it stays in that siteaccess; a path with another siteaccess's prefix is put after the current prefix, as every
module redirect is, and stays in the current siteaccess too.

## Every redirect

`eZModule::redirectTo()`, the redirect behind every module view, applies rules 1 to 5 to every target. A target that
breaks them is answered with **403** and "Redirection requested on non-authorized host" (rules 1 and 5, as before)
or "Redirection requested to an unsafe address" (the others), and the reason is written to the error log. So a
redirect parameter that reaches it unchecked (`RedirectURI` of `user/login`, `content/action`, `user/preferences`)
can no longer send a visitor to another host by a backslash, an encoded slash or a `javascript:` URL.

`eZRedirectManager::redirectURI()` and `redirectTo()` apply the rules to their preferred URI and to the page from the
session; the default the caller passes is the caller's own and is not checked.

## For developers

```php
// the target, or false when it breaks a rule
$uri = eZRedirectManager::safeURI( $http->postVariable( 'RedirectURI' ) );

// why: false, 'type', 'empty', 'control', 'backslash', 'encoded', 'scheme', 'userinfo' or 'host'
$reason = eZRedirectManager::unsafeReason( $uri );

// "back to where you came from": the first safe page the form names, else the page viewed last, else the default
$module->redirectTo( eZRedirectManager::returnURI( $module, '/section/list', eZRedirectManager::formReturnURIs() ) );

// the value for the hidden field RedirectIfDiscarded of the form's template
$tpl->setVariable( 'redirect_if_discarded', eZRedirectManager::formReturnURI( $module ) );
```

`returnURI()` takes a list of pages, and its options (`session`, `viewable`, `disallowed_views`, `allowed_hosts`,
`current_host`, `prefix`) let a test run it without a session, a database or settings. Each view above has a static
`cancelURI()` that returns its target, so a test can check it the same way.

## Tests

No database is needed:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/eZRedirectManagerSafeURITest.php
php vendor/bin/phpunit tests/tests/kernel/classes/eZUserEditCancelTest.php
```

`eZRedirectManagerSafeURITest` covers the rules case by case (protocol-relative, backslash, scheme, encoded,
CR/LF, hosts, userinfo), the module view of a path, `returnURI()` with and without a session, and the Cancel target
of every view in the table, with a safe and with an unsafe page in the form.

## Upgrading

- A template of your own for one of the views above keeps working; it sends no `RedirectIfDiscarded`, so Cancel
  goes to the page viewed last (before: a fixed page). Add the line above to freeze the page into the form.
- Code that redirected with a `javascript:` or `data:` URL, a backslash or a control character now gets a 403.
- `eZModule::getAllowedRedirectHosts()` (private) is now `eZModule::allowedRedirectHosts()` (public static).
- Velocity: the changed classes are kernel classes, so `exp:velocity deploy --kernel` (or a restart after the archive
  rebuild); then clear the template-override cache for the new hidden fields.
