---
title: audit.ini [AuditAlertSettings]
audience: developer, admin
category: examples
summary: "The settings of [AuditAlertSettings] in audit.ini: Alerts, EvaluateIn, Rules, RuleClasses, BusinessDays, BusinessHours, Recipients"
keywords: audit.ini, AuditAlertSettings, INI, setting, Alerts, EvaluateIn, Rules, RuleClasses, BusinessDays, BusinessHours, Recipients
type: generated
generated: exp:help:examples
---
# audit.ini [AuditAlertSettings]

The block `[AuditAlertSettings]` of `settings/audit.ini`, with 7 setting(s).

Never edit `settings/audit.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/audit.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in an extension (read when the extension is active).

## Alerts

> F5: built-in rules, INI rules (\[AlertRule\_\*\]) and rule classes.  enabled | disabled  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
Alerts=enabled
*/ ?>
```

## EvaluateIn

> Where rules are evaluated: flush (at the end of the request that wrote the event) and  
> cronjob (the audit cronjob part, for windows and rules over many requests).  

Default: a list of 2: `flush`, `cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# an empty "EvaluateIn[]" first replaces the shipped list; without it the items are added
EvaluateIn[]
EvaluateIn[]=flush
EvaluateIn[]=cronjob
*/ ?>
```

## Rules

> The rules in use: \[AlertRule\_<name>\] blocks.  

Default: a list of 7: `brute_force`, `brute_force_user`, `admin_role_granted`, `settings_out_of_hours`, `mass_delete`, `audit_disabled`, `chain_broken`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# an empty "Rules[]" first replaces the shipped list; without it the items are added
Rules[]
Rules[]=brute_force
Rules[]=brute_force_user
Rules[]=admin_role_granted
Rules[]=settings_out_of_hours
Rules[]=mass_delete
Rules[]=audit_disabled
Rules[]=chain_broken
*/ ?>
```

## RuleClasses

> Rule classes: name => class implementing expAuditAlertRule (RAD survey registry  
> "auditalertrules").  
> Built in.  

Default: a hash of 3: `threshold=expAuditThresholdRule`, `match=expAuditMatchRule`, `schedule=expAuditScheduleRule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# an empty "RuleClasses[]" first replaces the shipped list; without it the items are added
RuleClasses[]
RuleClasses[threshold]=expAuditThresholdRule
RuleClasses[match]=expAuditMatchRule
RuleClasses[schedule]=expAuditScheduleRule
*/ ?>
```

## BusinessDays

> Business hours for rules with OutOfHours=enabled: days 1 (Mon) … 7 (Sun), hours 0-24,  
> in the site's time zone.  

Default: `1-5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
BusinessDays=1-5
*/ ?>
```

## BusinessHours

Default: `7-19`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
BusinessHours=7-19
*/ ?>
```

## Recipients

> Who gets alert mail (rules with Sinks\[\]=mail, and any other record mailed): the  
> default list. A rule's own \[AlertRule\_<rule>\] Recipients\[\] replaces it for that rule.  
> Empty (and \[AuditSink\_mail\] Receivers\[\] empty too) = the site's AdminEmail.  
> One recipient per line, combinable; addresses are deduplicated (case-insensitive),  
> users are resolved when the mail is sent with their current e-mail address, disabled  
> users are left out, and nothing in the event itself ever becomes a recipient:  
>   admin                    site.ini \[MailSettings\] AdminEmail  
>   address:ops@example.com  an e-mail address (a bare address with @ works too)  
>   group:security           the named list \[AlertRecipients\_security\] below  
>   user:14                  the user with content object id 14  
>   login:editor1            the user with that login  
>   usergroup:12             every user below the user group with node id 12,  
>                            sub-groups included (usergroup:<remote id> works too,  
>                            the node's or the object's remote id)  
>   role:Administrator       every user the role is assigned to, directly or through  
>                            a user group (role:<role id> works too)  
> Mail is throttled per rule (or event name) and recipient (\[AuditSink\_mail\] Throttle).  
> Show who would get what: ./console exp:audit alerts recipients \[--rule=<rule>\]  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# an empty "Recipients[]" first replaces the shipped list; without it the items are added
Recipients[]
*/ ?>
```

## Related

- [All blocks of audit.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/audit.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
