---
title: Security in extensions
audience: developer, app-creator
category: extension-development
order: 310
summary: The security rules every extension follows: policies on views, POST and form tokens, escaping, safe SQL, untrusted input, safe redirects, files, secrets and no inline code.
keywords: security, access control, policy, CSRF, ezformtoken, ezxform_token, XSS, wash, SQL injection, escapeString, input validation, redirect, eZRedirectManager, path traversal, secrets, CSP
type: help
---
# Security in extensions

Part 3, chapter 31. An extension runs with the full rights of the kernel. A mistake in it is a mistake in every site
that installs it. This chapter is the checklist that the kernel's own code follows, with the mechanism for each rule.

## 1. Every view has a policy

```php
$ViewList['admin'] = array( 'script' => 'admin.php', 'functions' => array( 'administrate' ) );
$FunctionList['administrate'] = array();
```

A view without `'functions'` is open to everyone who reaches the site. Enforce limitations in your code, and use the
content objects' own checks (`canRead()`, `canEdit()`) for content. See
[Policy functions and access checks](policy-functions-and-access.md).

## 2. Changes need POST and the form token

Cross-site request forgery: a page elsewhere makes the visitor's browser send a request to your site, with their
session. Defenses:

- Change data only on **POST**; a GET request must never change anything (an `<img src=".../delete/5">` would).
- The **ezformtoken** extension adds a hidden field `ezxform_token` to every form in the output and checks it on every
  POST (`ezxFormToken::output()` and `ezxFormToken::input()`, attached as listeners of `response/output` and
  `request/input`). Keep it active. A POST without a valid token is refused before your view runs.
- Ajax: `Exp.io` sends the token with its POST calls ([Ajax with ezjscore server functions](ajax-with-ezjscore-server-functions.md)).

Details: [ezformtoken and CSRF protection](../part-8-reference/ezformtoken-and-csrf.md).

## 3. Escape output

In templates, `|wash` every value that can come from a person: names, titles, search words, view parameters, attribute
values you print as text. In PHP that builds HTML, `htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' )`. In a JavaScript
string inside a data attribute or JSON, `|wash( 'javascript' )` or `json_encode()`.

## 4. Safe SQL

| Do | Do not |
|---|---|
| `eZPersistentObject` conditions (escaped for you) | build SQL from request values |
| `(int)` for every number you put in SQL | trust an ID because it "comes from our own form" |
| `$db->escapeString( $text )` and quotes for strings | string concatenation of unescaped text |
| a list of allowed column names for sorting | a column name taken from the address |

## 5. Input is untrusted, including its type

A posted value can be an array (`name[]=x`), very long, or missing. Check with `is_string()` before you treat it as
text, cast numbers, compare choices against fixed lists, and limit lengths. The kernel's datatypes were hardened the
same way in September 2026 ([Datatype input hardening](../../../specifications/6.0/datatype-input-hardening.md)).

## 6. Redirects go only where they should

A redirect target from a form field, the session or the address can send a visitor to another site. Use the kernel's
rules: `eZRedirectManager` (`kernel/classes/ezredirectmanager.php`) chooses a safe "back" target (`redirectTo()`,
`returnURI()`), and its rules apply to every target from outside the code. See
[Safe redirects](../../../features/6.0/safe-redirects.md).

## 7. Files and paths

- Never build a path from input without removing directory parts (`basename()` at least) and checking the result stays
  inside the directory you meant.
- Store uploads through the kernel's file and cluster handlers, under `var/<site>/storage`, never in the web root.
- Send downloads with the kernel's helpers, which quote the file name safely (`eZFile::downloadHeaders()`).

## 8. Secrets

Never commit or ship keys, passwords or tokens; read them from settings the installation fills in its own
`settings/override/`, name them so they are masked, and never log them
([.ini.append.php files and keeping settings safe](../part-2-builder/append-php-files-and-safety.md)).

## 9. No inline scripts or styles

The administration's Content Security Policy refuses inline code; it is also a defense in depth against cross-site
scripting. See [Content Security Policy for extension authors](content-security-policy-for-extensions.md).

## 10. Generated code is code

Extensions written by the RAD wizards are protected by eight guards (values cannot break out of comments or INI
lines, nothing is written outside `extension/<name>`, generated templates escape output, generated views validate
input). Review what any generator, including your own scripts, writes. See [RAD tools: security](../../../bc/6.0/rad-security.md).

## Reporting a vulnerability

If you find a security problem in an extension you did not write, report it privately to its maintainers; for the
kernel and the se7enxweb extensions, `SECURITY.md` names `security@se7enx.com` and GitHub's private vulnerability
reporting ([Asking the community for help](../part-1-from-user-to-developer/asking-the-community.md)).

## Related pages

- Previous: [Kernel class overrides: the last resort](kernel-overrides-the-last-resort.md)
- Next: [Content Security Policy for extension authors](content-security-policy-for-extensions.md)
- [Evaluating an old extension before you use it](../part-1-from-user-to-developer/evaluating-an-old-extension.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
