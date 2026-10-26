---
title: Patterns in kernel/classes/mailpreferences
audience: developer
category: standards
order: 504
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/mailpreferences with its design patterns.
keywords: kernel/classes/mailpreferences, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/mailpreferences

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/mailpreferences` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (18)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expConsentContext` | class | [Factory and static factory methods](factory.md) | `kernel/classes/mailpreferences/expconsentcontext.php:19` | Where a change of a preference comes from, as the consent log records it: the source (page, link, admin, import, signup, bridge, confirm, system), th… |
| `expConsentLog` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/mailpreferences/expconsentlog.php:22` | The consent log (table expmail_consent_log): one row per change of a person's e-mail preferences, with the time, the category, the action (on, off, p… |
| `expMailAddressChange` | class | [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailaddresschange.php:20` | A new e-mail address for an account takes effect only once it is confirmed (double opt-in, mailpreferences.ini [EmailChangeSettings] Confirm=enabled,… |
| `expMailBounceReader` | class | [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailbouncereader.php:36` | Reads the mailbox that bounces and complaints arrive in (mailpreferences.ini [BounceSettings], set in settings/override/mailpreferences.ini.append.ph… |
| `expMailCategory` | class | – | `kernel/classes/mailpreferences/expmailcategory.php:18` | One category of e-mail (content notifications, newsletters, account security, ...): a value object. |
| `expMailCategoryHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | The optional PHP class of a category ([Category_] HandlerClass): it reads data that existed before the preference system (subtree notifications, dige… |
| `expMailCategoryRegistry` | class | [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | All categories of e-mail, in the order the preference page shows them: |
| `expMailCategoryRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | A category made or changed in the admin's category manager (table expmail_category). |
| `expMailGate` | class | [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailgate.php:34` | The central mail gate. |
| `expMailPendingRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/mailpreferences/expmailpendingrow.php:14` | A double opt-in waiting for its confirmation (table expmail_pending): kind 'category' (a newsletter or marketing category switched on), 'email_change… |
| `expMailPreferenceRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | One stored preference (table expmail_preference): a recipient (recipient_key 'u:' or 'a:'), a category, its state (on, off, pending) and frequency. |
| `expMailPreferences` | class | [Factory and static factory methods](factory.md) | `kernel/classes/mailpreferences/expmailpreferences.php:28` | The e-mail preferences of one person (expMailRecipient): the master switch "all optional mail", the state of each category (on, off, or pending while… |
| `expMailPreferencesService` | class | [Facade / service](facade.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | The flows around the preferences: the double opt-in mail and its confirmation, the one-click unsubscribe, "send me a link" for people without login,… |
| `expMailRecipient` | class | [Factory and static factory methods](factory.md) | `kernel/classes/mailpreferences/expmailrecipient.php:23` | Identifies the person whose preferences are meant: a user account, or an e-mail address without account. |
| `expMailSecret` | class | [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailsecret.php:20` | The site secret of the e-mail preference system: the key of the links in mail (expMailToken) and the salt of the address hashes (expMailSuppression,… |
| `expMailSenderDetails` | class | [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailsenderdetails.php:27` | Who sends the optional mail: the organisation name and the postal address the mail gate puts in the footer of every optional mail (CAN-SPAM, CASL and… |
| `expMailSuppression` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailsuppression.php:22` | The suppression list (table expmail_suppression): addresses no optional mail goes to (hard bounces, complaints, "unsubscribe from all", legal request… |
| `expMailToken` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/mailpreferences/expmailtoken.php:24` | The links in mail that work without login: unsubscribe (one click, RFC 8058), manage (the preference page) and confirm (a double opt-in). |
