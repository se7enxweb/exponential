# Upgrade note: authorizing an OAuth client needs the policy oauth/authorize

From 6.0.15 on, a signed in user may allow an OAuth client to act for them (the page `oauth/authorize`) only with the
policy **oauth / authorize**. A user without it is sent back to the client with `error=access_denied` (RFC 6749,
section 4.1.2.1) and no code or token is issued.

## What was wrong

The view had always meant to check this policy, but the check read

```php
if ( !$access[ 'accessWord' ] == 'yes' )
```

PHP evaluates `!` before `==`, so the condition was `( !$access['accessWord'] ) == 'yes'`, which is never true. On top
of that the `oauth` module defined no policy function, so nobody could have been given the policy. Every signed in
user could authorize every registered client, whatever their roles said.

## What changes

- The `oauth` module has the policy function **authorize**, with an optional **SiteAccess** limitation (the same one
  as `user/login`: the user may authorize clients only on the siteaccesses listed).
- `oauth/authorize` checks it with `expOAuthAuthorizePolicy` (kernel/private/rest/classes/oauth/authorize_policy.php).
  Without the policy the browser goes to the client's `redirect_uri` with
  `error=access_denied&error_description=The+user+may+not+authorize+clients`. The `redirect_uri` has been checked
  against the client's end point before, so this redirect cannot point anywhere else.
- `rest.ini [OAuthSettings] AuthorizeRequiresPolicy=enabled` is the default. `disabled` gives back the old
  behavior (every signed in user may authorize clients).
- New installations give the **Editor** role *OAuth / Authorize*; the **Administrator** role has it through full
  access. Other roles of a site package (members, partners) do not get it: add it where people need it.
- Tokens and authorization codes issued before the upgrade stay valid until they expire; a client a user had
  authorized before is authorized again only if the user has the policy.

## What to do when you upgrade

Users who sign in to REST clients today would be refused after the upgrade unless their roles get the policy. The
script `bin/php/addoauthauthorizepolicy.php` gives it to exactly those roles:

```bash
php bin/php/addoauthauthorizepolicy.php                  # dry run: lists what it would grant, changes nothing
php bin/php/addoauthauthorizepolicy.php --apply          # grants it
php bin/php/addoauthauthorizepolicy.php --role=Editor --role=12   # only the roles named (dry run)
```

- Every role with `user/login` (or `user/*`) gets `oauth/authorize` with the same siteaccesses: an unlimited
  `user/login` gives an unlimited policy, `user/login` limited to some siteaccesses gives `oauth/authorize` limited to
  those siteaccesses.
- Roles that grant it already (`oauth/authorize`, `oauth/*`, every module) are skipped, so the script can run again.
- A role assigned only to the anonymous user, or to its own group (Anonymous Users), is skipped: an anonymous user
  can never authorize a client. A role also assigned to other users or groups is granted: on sites built from the
  standard packages the Anonymous role is assigned to Members and Partners too and gives them `user/login`, so it
  gets the policy (harmless for the anonymous user, who must sign in before authorizing).
- With `--role` (an ID or a name, repeatable) only the roles named are considered; a named role without `user/login`
  gets an unlimited policy.
- The role cache is cleared after a grant.

The grant keeps the old behavior for the people who had it. If only some of your users should authorize clients,
run the script with `--role` for those roles instead, or remove the policy from the other roles afterwards in
**Setup > Roles and policies**.

### Keeping the old behavior for a short time

If you cannot grant the policy right away, add to `settings/override/rest.ini.append.php`

```ini
[OAuthSettings]
AuthorizeRequiresPolicy=disabled
```

and clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`). Remove the line again once the roles have the
policy: with it, every signed in user may authorize every client, which is the bug this change fixes.

## Checking it

- `php vendor/bin/phpunit tests/tests/kernel/private/oauth/` (no database): the access result evaluation, the
  upgrade plan and the view's `access_denied` redirect.
- Sign in as a user without the policy and open
  `/oauth/authorize?client_id=<id>&redirect_uri=<the client's end point>&response_type=code`: the browser is sent to
  the end point with `error=access_denied`.
