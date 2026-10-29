---
title: Adding security.txt
audience: government, admin, hosting
category: public-sector
order: 260
summary: For public bodies - Exponential ships no security.txt, so this page shows how to write one following RFC 9116, where to put it, the one rewrite rule the shipped Apache rules need so it is served, and how to check it.
keywords: security.txt, RFC 9116, .well-known, vulnerability disclosure, Contact, Expires, Policy, Preferred-Languages, Canonical, rewrite rule, htaccess, Velocity, nginx, public sector
type: help
---
# Adding security.txt

This page is for administrators and hosting teams. `security.txt` is a small text file at
`/.well-known/security.txt` that tells security researchers how to report a vulnerability, defined by RFC 9116
(<https://www.rfc-editor.org/rfc/rfc9116>). **Exponential ships none**; the project lists that as a gap an operator can
close ([Security standards: Vulnerability disclosure](../../../standards/security.md#vulnerability-disclosure)).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Write the file](#write-the-file)
2. [Put it in place](#put-it-in-place)
3. [Make the web server serve it](#make-the-web-server-serve-it)
4. [Check it](#check-it)
5. [Keep it current](#keep-it-current)
6. [Related pages](#related-pages)

## Write the file

A minimal file with the two fields RFC 9116 requires (`Contact` and `Expires`) and common optional ones:

```
Contact: mailto:security@example.gov
Expires: 2027-10-31T23:00:00.000Z
Policy: https://www.example.gov/vulnerability-disclosure
Preferred-Languages: en
Canonical: https://www.example.gov/.well-known/security.txt
```

- `Contact`: a mailbox your security team reads (or an HTTPS form). Not the Exponential project's address: that one is
  for vulnerabilities in Exponential itself.
- `Expires`: less than a year ahead, as the RFC recommends.
- `Policy`: your disclosure policy page (see "Vulnerability disclosure").
- The RFC also describes signing the file with OpenPGP; follow your organization's practice.

## Put it in place

Create the directory `.well-known` in the document root and the file inside it, owned by the site's user and readable
by the web server:

```bash
mkdir -p .well-known
$EDITOR .well-known/security.txt
chmod 0644 .well-known/security.txt
```

Keep the file in your deployment (version control) so it survives the next release.

## Make the web server serve it

**Apache with the shipped rules.** `.htaccess_root` answers 404 for every dot path except `.well-known`, but its last
rule sends every other request to `index.php`, so `/.well-known/security.txt` would reach Exponential and get a not-found
page. Add one line next to the existing `robots.txt` rule, before the final `RewriteRule .* index.php`:

```apache
RewriteRule ^\.well-known/security\.txt$ - [L]
```

Merge it into your `.htaccess` (or virtual host) and into your own copy of the rules; repeat after upgrades that
change `.htaccess_root`.

**nginx**: the derived configuration in the installation guide keeps `.well-known` reachable; make sure a `location`
serves the file before the request falls through to `index.php`
([Chapter 8: Serving the site](../../../install/08-serving-the-site.md)).

**Velocity** answers files in the document root itself; check with `curl` below.

## Check it

```bash
curl -sI https://www.example.gov/.well-known/security.txt | head -5
curl -s  https://www.example.gov/.well-known/security.txt
```

Expected: status 200, `Content-Type: text/plain`, and your file. A 404 or an HTML page means the request reached
`index.php`.

## Keep it current

Put the `Expires` date in your calendar a month ahead, together with the review of the disclosure policy. A file past
its expiry date should be considered stale by researchers, under the RFC.

## Related pages

- [Vulnerability disclosure](vulnerability-disclosure.md)
- [A useful robots.txt out of the box](../../../features/6.0/robots-txt.md) (served the same way)
- [Security standards](../../../standards/security.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
