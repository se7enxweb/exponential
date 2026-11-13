---
title: "Request shield: the rules"
audience: admin, hosting, developer
category: administration
order: 40
summary: The rules page of Setup > Request shield and the rule files behind it - which main rule file the shield uses, the shipped Exponential rules, the site's own file and its copies, whether the rules compile, and the commands that check, test and trace them on the server.
keywords: request shield, rules, requestshield/rules, rule file, request-shield.rules, exponential.rules, exponential-admin-uri.rules, exponential-admin-host.rules, settings/request-shield, EXP_REQUEST_SHIELD_RULES, include, compile, check, test, trace, expect, host, www.example.org, ShowRuleText, rule ID
type: help
---
# Request shield: the rules

The shield is configured in **rule files**, plain text files on the server. **Setup > Request shield > Rules**
(`/requestshield/rules`) shows them, read-only: whether they compile, the main file and the files it includes, and
the commands that check them. Changing the rules is done on the server, except the mode, which
[the switches](the-switches.md) can change.

## Contents

1. [Which file the shield uses](#which-file-the-shield-uses)
2. [The shipped files](#the-shipped-files)
3. [The site's own file](#the-sites-own-file)
4. [Checking the rules on the server](#checking-the-rules-on-the-server)
5. [Reading a rule](#reading-a-rule)

## Which file the shield uses

The shield reads one **main rule file**, which may include others. It is the first of:

1. the file named in `config.php` by `EXP_REQUEST_SHIELD_RULES`;
2. the site's own file, `settings/request-shield/request-shield.rules`;
3. the extension's default, `extension/exp_request_shield/rules/request-shield.rules`.

The page says which one is in use and why. When it is the extension's default, it says so: updates of the extension
replace that file, so a real site should have its own.

## The shipped files

| File | What it is |
|---|---|
| `request-shield.rules` | the main file: includes the Exponential rules, sets the mode (enforce), the store and the log |
| `exponential.rules` | the rules for the public site: system URLs, internal files, forms, the search, the pace |
| `exponential-admin-uri.rules` | adds the administration as the siteaccess `/admin` |
| `exponential-admin-host.rules` | adds the administration on a host name of its own |

The three `exponential*.rules` come from the library unchanged; why each rule exists is explained in its
[Exponential use case](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md).

## The site's own file

The site's file lives in `settings/request-shield/`, with copies of the three `exponential*.rules` beside it (an
`include` may not reach outside its file's directory with `..`). The first mode switch on the dashboard makes these
files; otherwise whoever looks after the server copies them
([the guide](../../guides/request-shield.md#2-give-the-site-its-own-rules)).

In it, the site names its own host names, so a request for any other name is refused. Keep `www.example.org` in a
`host` line: the shipped examples use that documentation domain, and the tests fail five of them without it. No real
visitor ever sends it.

## Checking the rules on the server

The page lists the commands; run them as the user the site's PHP runs as:

```bash
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

- `check` names any mistake by file and line.
- `test` decides every `expect` example written next to the rules; it must pass after every change.
- `trace` shows how one request is decided, rule by rule: the quickest answer to "why was this refused?".

## Reading a rule

Every rule has an ID in square brackets, and `expect` lines next to it are examples:

```
[SITE-DASH]  restrict /rs/** to 127.0.0.1 ::1
expect GET /rs/stats/overview from 198.51.100.27 403 by SITE-DASH
```

This one closes the library's own pages below `/rs` to everyone but the server, and the example says that a request
from outside gets 403 by that rule. The same ID appears in the log. The rule language is documented upstream:
[rule files](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md),
[settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

The text of the files is shown when `ShowRuleText=enabled` in `requestshield.ini [AdminSettings]` (the default).
