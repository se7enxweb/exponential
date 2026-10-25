---
title: Erweiterungspunkte in site.ini
audience: developer, admin
category: examples
summary: Einstellungen von site.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (28, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, site.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in site.ini

Einstellungen von `settings/site.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/site.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## Cache_restRoutes purgeClass

Ausgeliefert: `ezpRestRoutesCacheClear`. [Die Einstellung](../ini-einstellungen/site/Cache_restRoutes.md#purgeclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Cache_restRoutes]
purgeClass=ezpRestRoutesCacheClear    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Cache_exphelpcontent purgeClass

Ausgeliefert: `expHelpContentRepository`. [Die Einstellung](../ini-einstellungen/site/Cache_exphelpcontent.md#purgeclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Cache_exphelpcontent]
purgeClass=expHelpContentRepository    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## DatabaseSettings DatabaseImplementation

Ausgeliefert: `ezmysqli`. [Die Einstellung](../ini-einstellungen/site/DatabaseSettings.md#databaseimplementation).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezmysqli    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Event Listeners

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/Event.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Event]
Listeners[]=expExampleListener    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RunnableSettings Implementation

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/RunnableSettings.md#implementation).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RunnableSettings]
Implementation[]=expExampleImplementation    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RunnableSettings Listeners

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/RunnableSettings.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RunnableSettings]
Listeners[]=expExampleListener    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ExtensionSettings ExtensionDirectory

Ausgeliefert: `extension`. [Die Einstellung](../ini-einstellungen/site/ExtensionSettings.md#extensiondirectory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ExtensionSettings]
ExtensionDirectory=extension    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ExtensionSettings AdditionalExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/ExtensionSettings.md#additionalextensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ExtensionSettings]
AdditionalExtensionDirectories[]=expExampleAdditionalExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Session Handler

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/site/Session.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Session]
Handler=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Session SessionNameHandler

Ausgeliefert: `default`. [Die Einstellung](../ini-einstellungen/site/Session.md#sessionnamehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Session]
SessionNameHandler=default    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## URLTranslator FilterClasses

Ausgeliefert: eine Liste mit 1: `eZURLAliasFilterAppendNodeID`. [Die Einstellung](../ini-einstellungen/site/URLTranslator.md#filterclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[URLTranslator]
FilterClasses[]=expExampleFilterClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AppendNodeIDFilterSettings ApplyOnClass

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/AppendNodeIDFilterSettings.md#applyonclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[AppendNodeIDFilterSettings]
ApplyOnClass[]=expExampleApplyOnClas    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## SiteSettings ErrorHandler

Ausgeliefert: `displayerror`. [Die Einstellung](../ini-einstellungen/site/SiteSettings.md#errorhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SiteSettings]
ErrorHandler=displayerror    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## SearchSettings DelayedIndexingClassList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/SearchSettings.md#delayedindexingclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SearchSettings]
DelayedIndexingClassList[]=expExampleDelayedIndexingClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## SearchSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/SearchSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SearchSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## UserSettings WeakPasswordCheck

Ausgeliefert: `enabled`. [Die Einstellung](../ini-einstellungen/site/UserSettings.md#weakpasswordcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
WeakPasswordCheck=enabled    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## UserSettings LoginHandler

Ausgeliefert: eine Liste mit 1: `standard`. [Die Einstellung](../ini-einstellungen/site/UserSettings.md#loginhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
LoginHandler[]=expExampleLoginHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## UserSettings ExtensionDirectory

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/UserSettings.md#extensiondirectory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
ExtensionDirectory[]=expExampleExtensionDirectory    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## PasswordSettings MinCharacterClasses

Ausgeliefert: `0`. [Die Einstellung](../ini-einstellungen/site/PasswordSettings.md#mincharacterclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[PasswordSettings]
MinCharacterClasses=0    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## SignInSecondFactorSettings Handlers

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/SignInSecondFactorSettings.md#handlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SignInSecondFactorSettings]
Handlers[]=expExampleHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ReauthSettings CodeHandlers

Ausgeliefert: eine Liste mit 1: `expReauthTotpCode`. [Die Einstellung](../ini-einstellungen/site/ReauthSettings.md#codehandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ReauthSettings]
CodeHandlers[]=expExampleCodeHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## SiteAccessSettings MobileDeviceFilterClass

Ausgeliefert: `ezpMobileDeviceRegexpFilter`. [Die Einstellung](../ini-einstellungen/site/SiteAccessSettings.md#mobiledevicefilterclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SiteAccessSettings]
MobileDeviceFilterClass=ezpMobileDeviceRegexpFilter    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RoleSettings LimitationHandlers

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/RoleSettings.md#limitationhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RoleSettings]
LimitationHandlers[]=expExampleLimitationHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RegionalSettings LanguageSwitcherClass

Ausgeliefert: `ezpLanguageSwitcher`. [Die Einstellung](../ini-einstellungen/site/RegionalSettings.md#languageswitcherclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RegionalSettings]
LanguageSwitcherClass=ezpLanguageSwitcher    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ContentSettings StaticCacheHandler

Ausgeliefert: `eZStaticCache`. [Die Einstellung](../ini-einstellungen/site/ContentSettings.md#staticcachehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ContentSettings]
StaticCacheHandler=eZStaticCache    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ContentSettings ContentClassEditHandler

Ausgeliefert: `eZContentClassEditHandler`. [Die Einstellung](../ini-einstellungen/site/ContentSettings.md#contentclassedithandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ContentSettings]
ContentClassEditHandler=eZContentClassEditHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## MailSettings Transport

Ausgeliefert: `sendmail`. [Die Einstellung](../ini-einstellungen/site/MailSettings.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[MailSettings]
Transport=sendmail    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RSSSettings DefaultFeedItemClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/site/RSSSettings.md#defaultfeeditemclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RSSSettings]
DefaultFeedItemClasses[]=expExampleDefaultFeedItemClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von site.ini](../ini-einstellungen/site/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
