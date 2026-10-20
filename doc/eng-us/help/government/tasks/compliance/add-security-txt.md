---
title: Add a security.txt file
audience: government, admin, hosting
category: public-sector
order: 380
summary: Step by step - publish /.well-known/security.txt (RFC 9116) for your site, which Exponential does not ship, add the one rewrite rule the shipped Apache rules need to serve it, and check it with curl.
keywords: security.txt, RFC 9116, .well-known, vulnerability disclosure, Contact, Expires, Policy, rewrite rule, htaccess, nginx, Velocity, public sector
type: help
---
# Add a security.txt file

For administrators and hosting teams. A `security.txt` tells researchers where to report vulnerabilities in your site.
The project's statement lists it as not shipped ([Adding security.txt](../../security/adding-security-txt.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- A security contact mailbox and, ideally, a published disclosure policy page (see "Vulnerability disclosure").
- Access to the document root and the web server configuration.

## Steps

1. Create `.well-known/security.txt` in the document root:

   ```
   Contact: mailto:security@example.gov
   Expires: 2027-10-31T23:00:00.000Z
   Policy: https://www.example.gov/vulnerability-disclosure
   Preferred-Languages: en
   Canonical: https://www.example.gov/.well-known/security.txt
   ```

   `Contact` and `Expires` are required by RFC 9116 (<https://www.rfc-editor.org/rfc/rfc9116>); keep `Expires` less than
   a year ahead.
2. **Apache with `.htaccess_root`**: dot paths except `.well-known` answer 404, and the last rule sends everything else
   to `index.php`. Add, before `RewriteRule .* index.php`:

   ```apache
   RewriteRule ^\.well-known/security\.txt$ - [L]
   ```

3. **nginx**: make sure a `location` serves the file before requests fall through to `index.php`.
4. Add the file (and the rule) to your deployment so the next release keeps them.

## Check that it worked

```bash
curl -sI https://www.example.gov/.well-known/security.txt | head -3
curl -s https://www.example.gov/.well-known/security.txt
```

Status 200, a plain text type, your file. An HTML page means the request reached `index.php`. Put the `Expires` date in
your calendar.

## Related pages

- [Adding security.txt](../../security/adding-security-txt.md)
- [Vulnerability disclosure](../../security/vulnerability-disclosure.md)
- [A useful robots.txt out of the box](../../../../features/6.0/robots-txt.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
