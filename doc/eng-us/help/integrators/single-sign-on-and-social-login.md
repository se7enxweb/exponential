---
title: Single sign-on and social login
audience: integrator, developer, admin
category: integration
order: 230
summary: What Exponential offers for single sign-on - the SingleSignOnHandlerArray hook, social login as an OAuth 2.0 client in sevenx_authentication_2fa, putting a SAML or OpenID Connect proxy in front of the site - and what it does not offer, with a worked header-based SSO handler and its security conditions.
keywords: single sign-on, SSO, SingleSignOnHandlerArray, SSOHandler, handleSSOLogin, social login, Google, Facebook, ID.me, OAuth client, PKCE, OpenID Connect, SAML, identity-aware proxy, trusted header, sevenx_authentication_2fa
type: help
---
# Single sign-on and social login

This page is for integrators asked to let users "sign in once" with a corporate identity provider or with a social
account. Exponential has a hook for single sign-on and a social login extension; it has **no SAML implementation and
no OpenID Connect provider**. This page explains what is there, the common architecture that fills the gap, and how
to build it safely.

## Contents

1. [What exists](#what-exists)
2. [The single sign-on hook](#the-single-sign-on-hook)
3. [Example: an SSO handler behind an identity-aware proxy](#example-an-sso-handler-behind-an-identity-aware-proxy)
4. [Social login](#social-login)
5. [Choosing an approach](#choosing-an-approach)

## What exists

| Need | Provided | Where |
|---|---|---|
| Sign in with a directory password | Yes | Login handlers: LDAP, textfile, your own ([page](login-handlers-ldap-and-textfile.md)) |
| Recognize an already signed-in user without a login form | A hook | `site.ini [UserSettings] SingleSignOnHandlerArray[]` |
| Sign in with Google, Facebook, Meta, Instagram, Twitter, ID.me | Yes, as an OAuth 2.0 client with `state` and PKCE | `sevenx_authentication_2fa` (`login_handler/`) |
| SAML 2.0 service provider | No | An identity-aware proxy in front, or custom work |
| OpenID Connect client with ID token validation | No (userinfo only in the social handlers) | Proxy, or custom work |
| Exponential as identity provider for other applications | Only as an OAuth 2.0 authorization server for its REST API | [OAuth applications](oauth-applications-for-integrations.md) |

## The single sign-on hook

On each request of an **anonymous** visitor who has not just signed out, `eZUser::currentUser()` asks every handler
listed in `[UserSettings] SingleSignOnHandlerArray[]`. For a name `<name>` it creates the class
`eZ<name>SSOHandler` (found through the autoloads) and calls `handleSSOLogin()`. A handler returns an `eZUser` to sign
that user in, or `false`. When a user is returned, the kernel signs the session in, updates the last visit and
redirects to the same address (`kernel/classes/datatypes/ezuser/ezuser.php`).

No handler ships with the kernel; the hook is for yours.

## Example: an SSO handler behind an identity-aware proxy

A common architecture: a reverse proxy (or a web server module) speaks SAML or OpenID Connect with the identity
provider, and passes the authenticated login to Exponential in a request header or server variable. The handler maps
it to a local user.

```ini
# extension/myintegration/settings/site.ini.append.php
[UserSettings]
SingleSignOnHandlerArray[]=myproxy
```

```php
<?php
// extension/myintegration/classes/ezmyproxyssohandler.php
class eZmyproxySSOHandler
{
    /** @return eZUser|false */
    public function handleSSOLogin()
    {
        // set by the proxy; the web server must strip this header from every request that does not come through it
        $login = isset( $_SERVER['HTTP_X_AUTH_USER'] ) ? trim( (string)$_SERVER['HTTP_X_AUTH_USER'] ) : '';
        if ( $login === '' || !preg_match( '/^[A-Za-z0-9._@-]{1,150}$/', $login ) )
        {
            return false;
        }
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser || !$user->isEnabled() )
        {
            return false;   // provision users with a job of your own; never create administrators here
        }
        return $user;
    }
}
```

Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) so `class_exists()` finds the class.

Conditions that make this safe:

- [ ] The header is set **only** by the proxy, and the proxy strips any incoming header of the same name.
- [ ] The application server accepts requests only from the proxy (firewall, bind address); otherwise anyone could
  send the header. Configure trusted proxies as described in [Trusted proxies](../../bc/6.0/trusted-proxies.md).
- [ ] Administrators sign in through a separate siteaccess that does not use the handler, with two-step sign-in.
- [ ] Sign-out is handled at the proxy too, or the next request signs the user in again.
- [ ] Tested: a request without the header is anonymous; a request with an unknown login is anonymous.

## Social login

`sevenx_authentication_2fa` (required as `~1.0.14`) adds **Continue with ...** buttons on the login pages for each
provider enabled in `sevenxauthentication2fa.ini`. It is an OAuth 2.0 client using the authorization code grant, a
one-time `state` bound to the provider (600 seconds), PKCE (`S256`) where configured, and it refuses e-mail addresses
the provider marks as not verified. A user found this way still passes their own second step. The steps per provider
are in the extension's `INSTALL.md`; the overview is [Two-step sign-in, social login](../../guides/two-factor-login.md#social-login).

Limits: ID tokens are not validated (identity comes from the provider's userinfo answer), and there is no OpenID
Connect discovery; endpoints are configured per provider.

## Choosing an approach

| Situation | Approach |
|---|---|
| Staff with a directory, password typed on the site is acceptable | LDAP login handler |
| Corporate SSO (SAML, Entra ID, Okta, Keycloak) required, no password on the site | Identity-aware proxy plus an SSO handler |
| Public members with social accounts | Social login of `sevenx_authentication_2fa` |
| Partner portal where another application acts for users | OAuth application of Exponential |
| Provider with a password-check API | A login handler of your own |

Whatever the approach: user provisioning (creating users and assigning groups) and de-provisioning (disabling
leavers) are part of the integration; estimate them separately.

## Related pages

- [Login handlers - LDAP, textfile and your own](login-handlers-ldap-and-textfile.md)
- [Two-step sign-in](../../guides/two-factor-login.md)
- [API and protocol standards, OpenID Connect](../../standards/apis-and-protocols.md#openid-connect-and-oauth-20-as-a-client-social-login)
- [Integrations FAQ for public bodies](../government/faq/integrations-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
