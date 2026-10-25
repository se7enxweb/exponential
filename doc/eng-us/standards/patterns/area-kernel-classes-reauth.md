---
title: Patterns in kernel/classes/reauth
audience: developer
category: standards
order: 510
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/reauth with its design patterns.
keywords: kernel/classes/reauth, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/reauth

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/reauth` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expReauth` | class | [Utility (static helper)](utility.md) | `kernel/classes/reauth/expreauth.php:37` | "Verify it's you" (doc/eng-us/guides/verify-its-you.md): before a sensitive action the signed-in user confirms who they are again, with their passwor… |
| `expReauthCodeHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/reauth/expreauthcodehandler.php:14` | A class that checks the code of a two-step sign-in method for "Verify it's you" (expReauth). |
| `expReauthRules` | class | [Utility (static helper)](utility.md) | `kernel/classes/reauth/expreauthrules.php:28` | The rules of "Verify it's you" (site.ini [ReauthSettings], [ReauthGroup_] Rules[]) and the decision whether a request matches one (doc/eng-us/guides/… |
| `expReauthSettings` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/reauth/expreauthsettings.php:18` | The settings of "Verify it's you" as the page setup/reauth edits them (doc/eng-us/guides/verify-its-you.md): the switch of the whole feature, the min… |
| `expReauthTotpCode` | class implements expReauthCodeHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/reauth/expreauthtotpcode.php:19` | "Verify it's you" with the code of an authenticator app set up in sevenx_authentication_2fa (when that extension is active): the account's TOTP secre… |
