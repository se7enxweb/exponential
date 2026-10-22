---
title: Siteaccesses und Konfiguration
audience: software-engineer, developer
category: software-engineering
order: 130
summary: Wie Exponential konfiguriert wird - INI-Dateien, über eZINI gelesen, die Reihenfolge, in der sich Override-Verzeichnisse durchsetzen, Siteaccess-Zuordnung, Varianten je Umgebung mit EXP_ENV, Konfiguration, die die Bereitstellung verwaltet, Geheimnisse in settings/override und die Befehle, die zeigen, woher ein Wert kommt.
keywords: Konfiguration, Einstellungen, INI, eZINI, Kaskade, Override, Siteaccess, MatchOrder, ActiveExtensions, EXP_ENV, Umgebung, ManagedByDeployment, exp:ini, Geheimnisse, Softwareingenieur, configuration, settings, cascade, environment, secrets, software engineer
type: help
---
# Siteaccesses und Konfiguration

Diese Seite richtet sich an Ingenieure, die über Konfiguration nachdenken müssen: woher ein Wert kommt, warum er sich
zwischen zwei Websites einer Installation unterscheidet und wie Geheimnisse und rechnerspezifische Werte aus der
Versionsverwaltung herausbleiben. Die vollständige Referenz ist
[Die Kaskade der INI-Einstellungen](../../standards/architektur/kernel-architektur.md#die-kaskade-der-ini-einstellungen) und
[INI-Override-Verzeichnisse und Platzierungen](../../spezifikationen/6.0/ini-override-platzierungen.md).

## Inhalt

1. [INI-Dateien und eZINI](#ini-dateien-und-ezini)
2. [Die Reihenfolge der Kaskade](#die-reihenfolge-der-kaskade)
3. [Siteaccesses](#siteaccesses)
4. [Umgebungen, Bereitstellungen und Geheimnisse](#umgebungen-bereitstellungen-und-geheimnisse)
5. [Herausfinden, woher ein Wert kommt](#herausfinden-woher-ein-wert-kommt)
6. [Regeln für Konfigurationsänderungen](#regeln-für-konfigurationsänderungen)

## INI-Dateien und eZINI

Die gesamte Konfiguration besteht aus INI-Dateien, die über `eZINI` (`lib/ezutils/classes/ezini.php`) gelesen werden.
Die Standards werden in `settings/<file>.ini` mitgeliefert; Overrides heißen `<file>.ini.append.php` (das Suffix `.php`
verhindert, dass sie als Text ausgeliefert werden). Code liest einen Wert so:

```php
$value = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteName' );
```

Kein Code in `kernel/`, `lib/` oder `bin/` parst INI-Dateien selbst. Das zusammengeführte Ergebnis wird als PHP-Arrays im
INI-Cache zwischengespeichert; leeren Sie ihn nach einer Einstellungsänderung mit dem Tag, der sowohl den INI-Cache je
Siteaccess als auch den globalen abdeckt:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## Die Reihenfolge der Kaskade

Später gewinnt:

| Reihenfolge | Geltungsbereich | Verzeichnisse |
|---|---|---|
| 0 | Standards | `settings/<file>.ini` |
| 1 | `sa-extension` | Verzeichnisse je Siteaccess innerhalb von Erweiterungen (`extension/<ext>/settings/siteaccess/<sa>/` und verwandte) |
| 2 | `siteaccess` | `settings/siteaccess/<sa>/` |
| 3 | `extension` | `extension/<ext>/settings/` jeder aktiven Erweiterung |
| 4 | `override` | `settings/override/` |

Zwei Folgen, über die Ingenieure stolpern:

- `settings/` einer Erweiterung setzt sich gegen `settings/siteaccess/<sa>/` durch.
- `settings/override/` setzt sich gegen alles durch.

Erweiterungen werden in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) und `ActiveAccessExtensions[]` (je
Siteaccess) eingeschaltet; diese beiden lassen sich nicht innerhalb einer Erweiterung setzen. Die Reihenfolge der
Erweiterungen kann ihren deklarierten Abhängigkeiten folgen
([Ladereihenfolge der Erweiterungen](../../funktionen/6.0/ladereihenfolge-der-extensions.md)).

## Siteaccesses

Ein Siteaccess ist eine benannte Konfiguration, die je Anfrage gewählt wird: eine öffentliche Website, die
Administration, eine Sprachfassung. `eZSiteAccess` (`kernel/classes/ezsiteaccess.php`) ordnet ihn anhand von
`site.ini [SiteAccessSettings]` zu:

| Einstellung | Zweck |
|---|---|
| `MatchOrder` | die versuchten Zuordnungsarten, mitgeliefert `uri;host;port` |
| `AvailableSiteAccessList[]` | nur diese Siteaccesses werden zugeordnet |
| `RelatedSiteAccessList[]` | verwandte Siteaccesses mit derselben Datenbank |
| `HostMatchMapItems[]` | Hostname zu Siteaccess |
| `AnonymousAccessList[]` | Module oder Modul-Views, die auf einem Siteaccess, der sonst eine Anmeldung verlangt, keine brauchen (`user/register`, `csp/report` ...) |

Code nimmt den aktuellen Siteaccess aus `eZSiteAccess::current()` und parst die URL nie selbst. Zuordnung nach Host und
Sprache: [Siteaccess-Zuordnung nach Host und Sprache](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).

## Umgebungen, Bereitstellungen und Geheimnisse

| Bedarf | Mechanismus |
|---|---|
| Unterschiedliche Werte auf einem Entwicklerrechner, einer Testkopie und der Live-Website | `EXP_ENV` in `config.env.php` (aus `config.env.php.example`) und Variantendateien wie `settings/override/site.ini.test.append.php` ([Einstellungen je Umgebung](../../funktionen/6.0/umgebungseinstellungen.md)) |
| Konfiguration, die nur die Versionsverwaltung ändern darf | `[ConfigurationSettings] ManagedByDeployment=enabled` je Siteaccess: Die Administration schreibt keine Einstellungen, Templates oder Pakete mehr auf dem Server ([Konfiguration, die die Bereitstellung verwaltet](../../funktionen/6.0/durch-deployment-verwaltet.md)) |
| Geheimnisse (Datenbankpasswörter, API-Schlüssel, Signaturschlüssel) | `settings/override/`, das nie committet wird; `config.env.php` steht in `.gitignore` |
| Mehrere Websites in einer Installation | Einstellungen je Website innerhalb von Erweiterungen ([Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md)) |

## Herausfinden, woher ein Wert kommt

```bash
./console exp:ini where site.ini/SiteSettings/SiteName
./console exp:ini get site.ini/SiteSettings/SiteName
```

`where` listet jede Datei, die den Wert setzt, in Ladereihenfolge, und den geltenden Wert; `exp:ini` setzt, ergänzt und
entfernt außerdem Werte in einem gewählten Geltungsbereich ([exp:ini](../../bc/6.0/console-exp-ini.md)). Auf einer
laufenden Installation zeigt die Administration die zusammengeführten Einstellungen unter Setup
([Die Einstellungsseite](../../anleitungen/einstellungsseite.md)).

## Regeln für Konfigurationsänderungen

- Eine neue Einstellung erhält ihren Standard in der mitgelieferten `settings/<file>.ini` mit einem Kommentar darüber,
  einem sicheren Standardwert, einem Abschnitt auf ihrer Funktionsseite und einer Zeile im Changelog.
- Neue Schalter verwenden `enabled`/`disabled`.
- Einstellungen werden mit `./console exp:ini` oder `eZINI::save()` geschrieben, nie durch Zeichenersetzung in einem
  Skript.
- Eine neue Klasse, auf die eine mitgelieferte INI-Datei verweist, sollte im Kernel mit `class_exists()` abgesichert
  werden, weil ein Velocity-Worker, der vor der Bereitstellung gestartet wurde, sie nicht kennt
  ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)).

## Verwandte Seiten

- [Einstellungen und INI-Dateien](../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [Geltungsbereiche der Einstellungen und ihre Reihenfolge](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md)
- [Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
