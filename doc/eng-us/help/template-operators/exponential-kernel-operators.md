---
title: Installation, environment, audit and CSP operators
audience: developer, designer, app-creator
category: development
order: 370
summary: Seven operators the Exponential kernel added - installation_name, is_production_system, exp_environment, exp_environment_badge, audit_label, csp_nonce and view_parameter - with the settings they read, what they return, the caching rule for csp_nonce, and examples rendered on a development installation.
keywords: installation_name, is_production_system, exp_environment, exp_environment_badge, audit_label, csp_nonce, view_parameter, view parameters, EXP_ENV, EzInstallationName, ProductionInstallationList, AdminEnvironment, Content Security Policy, nonce, audit
type: help
---
# Installation, environment, audit and CSP operators

These operators belong to the current Exponential kernel and are registered in
`kernel/common/eztemplateautoload.php`. They need no extension.

| Operator | Class and file |
|---|---|
| `installation_name`, `is_production_system` | `ExpInstallationOperator`, `kernel/classes/expinstallationoperator.php` |
| `exp_environment`, `exp_environment_badge` | `expEnvironmentOperator`, `kernel/common/expenvironmentoperator.php` |
| `audit_label` | `expAuditTemplateOperator`, `kernel/classes/audit/console/expaudittemplateoperator.php` |
| `csp_nonce` | `expContentSecurityPolicyOperator`, `kernel/common/expcontentsecuritypolicyoperator.php` |
| `view_parameter` | `expViewParameterOperator`, `kernel/common/expviewparameteroperator.php` |

## Contents

1. [The operators](#the-operators): [installation_name](#installation_name),
   [is_production_system](#is_production_system), [exp_environment](#exp_environment),
   [exp_environment_badge](#exp_environment_badge), [audit_label](#audit_label), [csp_nonce](#csp_nonce),
   [view_parameter](#view_parameter)
2. [Examples](#examples)

## The operators

### installation_name

Generated page: [installation_name](../../examples/template-operators/installation_name.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `hide_on_prod` | boolean | no | `false` |

- **Input:** none.
- **Returns:** `site.ini` `[SiteSettings] EzInstallationName`, trimmed (an empty string when not set); with
  `hide_on_prod` an empty string on a production system.

The value is read once per request (`ExpInstallationDetailsOutputFilter::installationName()`,
`kernel/classes/expinstallationdetailsoutputfilter.php`). Use it to label test copies of a site.

### is_production_system

Generated page: [is_production_system](../../examples/template-operators/is_production_system.md).

- **Input:** none. **Parameters:** none.
- **Returns:** `true` when the installation name is not empty and is listed in `[SiteSettings]
  ProductionInstallationList[]`, otherwise `false`.

### exp_environment

Generated page: [exp_environment](../../examples/template-operators/exp_environment.md).

- **Input:** none. **Parameters:** none.
- **Returns:** the environment name from the `EXP_ENV` constant (`config.env.php`, read by `eZINI::environment()`),
  or an empty string when it is not set or not valid (it must match `^[a-z][a-z0-9_-]{0,31}$`). The environment also
  decides which extra settings files `eZINI` reads.

```tpl
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

### exp_environment_badge

Generated page: [exp_environment_badge](../../examples/template-operators/exp_environment_badge.md).

- **Input:** none. **Parameters:** none.
- **Returns:** the environment badge of the administration header as a hash: `level`, `name`, `short`, `release`,
  `display`, `title` and `key`.

The badge comes from `site.ini` `[AdminEnvironment]` of the current siteaccess (`expEnvironmentOperator::badge()`):

- `Level`: `development`, `test`, `staging` or `production`; `auto` derives it from `EXP_ENV` (`dev`/`development`,
  `test`, `stage`/`staging`, `prod`/`production`/`live`). Empty, unknown, or `auto` without a matching environment:
  no badge (`level` is an empty string and every other field stays empty).
- `Name`, `ShortName`: the texts; empty takes the level's translated name and code (DEV, TEST, STAGE, PROD).
- `Release` and `ShowVersion=enabled`: text and the Exponential version after the name, joined by ` · `.
- `BadgeDisplay=always` shows it permanently (`display` is `always`), anything else only on hover or focus
  (`hover`); `ShowInTitle` other than `disabled` sets `title`.
- `key` joins all of them for the keys of cache-blocks that draw the badge.

### audit_label

Generated page: [audit_label](../../examples/template-operators/audit_label.md).

- **Input:** an audit event name such as `content.node.move`. **Parameters:** none.
- **Returns:** the human label from the audit taxonomy (`expAuditConsole::label()`), translated in the context
  `kernel/audit`; an empty string for an empty or non-scalar input.

A name the taxonomy does not know is turned into a label by dropping the first part and replacing dots and
underscores with spaces (`expAuditTaxonomy::label()`), and is not translated: `no.such.event` gives `Such event`.
The label is not escaped: `{$e.name|audit_label|wash}`.

### csp_nonce

Generated page: [csp_nonce](../../examples/template-operators/csp_nonce.md).

- **Input:** none. **Parameters:** none.
- **Returns:** the nonce of the Content Security Policy of this request (`expContentSecurityPolicy::templateNonce()`,
  `kernel/private/classes/expcontentsecuritypolicy.php`), or an empty string when the policy of the siteaccess is
  disabled or carries no nonce (`nonce=""` is harmless).

```tpl
<script nonce="{csp_nonce()}">...</script>
```

The nonce is the same for every call within one request and new for every request. **Never print it inside a
`cache-block` or a view-cached template**: the cached copy would carry the nonce of another request and the browser
would block the script. Better still, move the script into a file loaded through the design's script list, which
needs no nonce.

### view_parameter

Generated page: [view_parameter](../../examples/template-operators/view_parameter.md).

- **Input:** a text or a number. **Parameters:** none.
- **Returns:** the value as the value of a view parameter, `/(name)/<value>`, written as every view writes it
  (`expViewParameters::encode()`, `kernel/classes/expviewparameters.php`): `/` as `~2F`, `(` and `)` as `~28` and
  `~29`, `~` as `~7E`, `%` and `+` as `~25` and `~2B`, the rest readable (a space `%20`). An array gives an empty
  string.

```tpl
<a href={concat( 'settings/view/', $siteaccess, '/', $file, '/(q)/', $name|view_parameter )|ezurl}>...</a>
```

The view reads the value back with `expViewParameters::get()`. Never write a view parameter with `|urlencode`:
Apache refuses the `%2F` it makes of a slash, and the kernel's second decoding turns `%2B` into a space. The
encoding and its rules: [View parameters: how a value travels in an address](../../guides/urls-and-aliases.md#9-view-parameters-how-a-value-travels-in-an-address).

## Examples

On the `site` siteaccess, which has no installation name, no `EXP_ENV` and no nonce in its policy:

```tpl
1 installation_name: [{installation_name()}]
2 hide_on_prod: [{installation_name( true() )}]
3 is_production_system: {is_production_system()|choose( 'no', 'yes' )}
4 environment: [{exp_environment()}]
5 csp_nonce on site: length {csp_nonce()|count_chars}
6 audit_label: {'content.node.move'|audit_label} / {'no.such.event'|audit_label}
```

Output:

```text
1 installation_name: []
2 hide_on_prod: []
3 is_production_system: no
4 environment: []
5 csp_nonce on site: length 0
6 audit_label: Node move / Such event
```

On the `admin` siteaccess, whose policy carries a nonce (no badge level is set on a development installation):

```tpl
{def $badge = exp_environment_badge()}
level=[{$badge.level}] name=[{$badge.name}] short=[{$badge.short}] display={$badge.display} title={$badge.title|choose( 'no', 'yes' )}
csp_nonce on admin: length {csp_nonce()|count_chars}, same on the second call: {eq( csp_nonce(), csp_nonce() )|choose( 'no', 'yes' )}
```

Output:

```text
level=[] name=[] short=[] display= title=no
csp_nonce on admin: length 22, same on the second call: yes
```

## Related pages

- [Content security policy for applications](../creators/app-creators/content-security-policy.md)
- [Environment settings and secrets](../creators/app-creators/environment-settings-and-secrets.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
