---
title: Einstellungen, Logs und Caches pro Website
audience: businesses, admin, hosting
category: business-solutions
order: 60
summary: Warum und wie jede Marke in einer Exponential-Installation ihre eigenen Einstellungen, Logdateien und Caches haben kann - Overrides pro Website in einer Website-Erweiterung, Logs pro Website, Caches auf einem Speicher-Dateisystem, INI-Caches pro Website leeren und der Log-Viewer.
keywords: Einstellungen pro Website, INI-Overrides, Website-Erweiterung, Logs pro Website, Log-Viewer, Multisite-Betrieb, Unternehmen, per-site settings, INI overrides, site extension, logs per site, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, ExpiryDir, log viewer, multisite operations, business
type: help
---
# Einstellungen, Logs und Caches pro Website

Diese Seite richtet sich an Führungskräfte, die für den Betrieb mehrerer Marken in einer Exponential-Installation
verantwortlich sind, und an die Administratoren, die ihn durchführen. Sie erklärt in geschäftlichen Begriffen, warum die
Einstellungen, Logs und Caches jeder Marke getrennt gehalten werden können, was das im täglichen Betrieb und im Support
ändert und welche Einstellungen das technische Team verwendet. Nichts davon ist standardmäßig eingeschaltet: Jede
Einstellung „behält das bisherige Verhalten bei“, bis jemand sie setzt
([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md#in-short)).

## Inhalt

1. [Warum Marken im Betrieb getrennt halten](#warum-marken-im-betrieb-getrennt-halten)
2. [Einstellungen, die zu einer Marke gehören](#einstellungen-die-zu-einer-marke-gehören)
3. [Logs pro Marke](#logs-pro-marke)
4. [Caches pro Marke](#caches-pro-marke)
5. [Logs ohne Shell-Zugang lesen](#logs-ohne-shell-zugang-lesen)
6. [Was Sie mit Ihrem Administrator vereinbaren sollten](#was-sie-mit-ihrem-administrator-vereinbaren-sollten)

## Warum Marken im Betrieb getrennt halten

| Ohne Trennung | Mit Trennung |
|---|---|
| Ein Fehlerlog für jede Marke; der Support durchsucht alle | Die Fehler jeder Marke in ihrem eigenen Verzeichnis |
| Das Leeren des Einstellungs-Caches betrifft jede Marke | Der Einstellungs-Cache einer Marke wird für sich allein geleert |
| Die Einstellungen aller Marken in einem großen Override-Verzeichnis | Die Einstellungen jeder Marke reisen mit ihrer eigenen Erweiterung |
| Caches auf derselben Platte wie alles andere | Der Cache einer Marke kann für mehr Geschwindigkeit im Arbeitsspeicher liegen |

Für ein Unternehmen verkürzt die Trennung die Bearbeitung von Störungen („Welche Marke ist betroffen?“) und lässt ein
Markenteam seine eigenen Einstellungen mit weniger Risiko für die anderen ändern.

## Einstellungen, die zu einer Marke gehören

Seit Januar 2026 kann eine Erweiterung, die eine Website enthält, **Override-Einstellungen mitbringen, die nur aktiv
sind, während einer ihrer eigenen Siteaccesses läuft**
([Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)). Für eine
Erweiterung `site_example`:

| Verzeichnis | Gilt für |
|---|---|
| `extension/site_example/settings/override/` | jeden Siteaccess der Marke |
| `extension/site_example/settings/override__<group>/` | jeden Siteaccess der Marke, dessen Name mit `<group>__` beginnt, etwa eine Sprachgruppe |
| `extension/site_example/settings/siteaccess/<siteaccess>/` | nur diesen Siteaccess |

Das eigene `settings/override/` der Installation hat über alle drei weiterhin das letzte Wort. Der geschäftliche Nutzen:
Design, Einstellungen und Templates einer Marke liegen gemeinsam in einem Repository, und eine Marke zu verschieben
oder einzustellen heißt, eine Erweiterung zu verschieben oder einzustellen.

> **Hinweis**
> Für Installationen mit vielen Website-Erweiterungen beschreibt dieselbe Seite eine optionale Konstante zur
> Beschleunigung, die die Override-Verzeichnisse anderer `site_`-Erweiterungen überspringt. „Lassen Sie sie aus, sofern
> Sie nicht viele Websites hosten.“

## Logs pro Marke

Alles in `site.ini [FileSettings]`, nach der Funktionsseite:

| Einstellung | Standard | Wirkung |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` schreibt die Debug-Logs und die Standard-Logs in das eigene Log-Verzeichnis der Marke statt nach `var/log` |
| `LogDir` | `log` | Das Log-Verzeichnis; ein absoluter Pfad, etwa auf einem anderen Dateisystem, wird unverändert verwendet |
| `LogVarDir` | leer | Ein Baum für die Logs aller Websites, etwa `var_log/<site>/log`, mit einer Zeile Einstellungen |

Meldungen, die geschrieben werden, bevor die Marke bekannt ist (während eine Anfrage einem Siteaccess zugeordnet wird),
landen weiterhin in `var/log`.

## Caches pro Marke

| Einstellung | Standard | Wirkung |
|---|---|---|
| `CacheVarDir` | leer | Ein Baum für die Caches aller Websites, etwa `var_cache/<site>/cache`, zum Beispiel auf einem Speicher-Dateisystem |
| `ExpiryDir` | leer | Hält die Datei mit den Zeitstempeln der Cache-Leerungen dort, wo der Speicher liegt, sodass ein Neustart, der einen Speicher-Cache leert, sie nicht verliert |
| `INICacheDir` | `global` | `site` speichert die Einstellungen der Marke in ihrem eigenen Cache-Verzeichnis, sodass ihr Leeren die anderen Marken nicht berührt |

Die Funktionsseite berichtet über die pro Siteaccess geprüften Cache-Befehle: Der Einstellungs-Cache der Marke wurde nur
durch seine eigene Leerung und durch die vollständigen Leerungen entfernt, der globale nur durch seine eigene Leerung
und die vollständigen Leerungen. Unter Velocity wird Velocity nach dem Leeren eines Einstellungs-Caches neu gestartet,
wie es unter Setup > Caches steht.

> **Warnung**
> Ein Cache-Verzeichnis außerhalb der Installation muss eines sein, in dem die Cache-Leerung löschen darf. Die
> Funktionsseite beschreibt die Regeln für `AllowedDeletionDirs`; ein Eintrag, der nicht existiert, erlaubt jetzt
> nichts. Bitten Sie Ihren Administrator, das zu prüfen, wenn Caches verlegt werden.

## Logs ohne Shell-Zugang lesen

**Setup > Logdateien** zeigt die Logdateien der Installation, einschließlich der Logs jeder Website
(`var/<site>/log/*.log`) und derjenigen, die Erweiterungen schreiben, mit einer Navigation, die nach Kernel, Website,
Erweiterungen, Webserver und rotierten Archiven gruppiert ist ([Der Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md)).
Jede Ansicht hat einen Permalink, sodass der Support einem Kollegen genau die gefilterte Ansicht schicken kann. Eine
Fehlerseite zeigt eine Referenz, die zu den Zeilen der fehlgeschlagenen Anfrage führt
([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

Eine weitere nützliche Funktion für Teams mit Staging-Kopien: Der Installationsname stellt den Namen einer
Nicht-Produktivinstallation vor den Seitentitel, sodass niemand die falsche Kopie bearbeitet
([Installationsname in Seiten](../../../../eng-us/features/6.0/installation-name-in-pages.md)).

## Was Sie mit Ihrem Administrator vereinbaren sollten

- [ ] Lebt jede Marke in ihrer eigenen Website-Erweiterung, mit ihren Einstellungen dort?
- [ ] Werden Logs pro Marke geführt, und wo (welches Dateisystem, welche Aufbewahrungsdauer)?
- [ ] Gibt es Caches pro Marke, und auf welchem Speicher?
- [ ] Wer darf Caches für eine Marke leeren, und wie wird Velocity nach einer Änderung der Einstellungen neu gestartet?
- [ ] Wer liest die Logs, wie oft, und wie erfährt ein Markenverantwortlicher von Fehlern auf seiner Website?
- [ ] Entspricht die Aufbewahrung der Logs Ihrer Datenschutzerklärung ([Sicherheit](../sicherheit/README.md))?

## Verwandte Seiten

- [Eine Installation, viele Websites](eine-installation-viele-websites.md)
- [Checkliste für mehrere Marken](checkliste-fuer-mehrere-marken.md)
- [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)
- [Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)
- [Multisite in einer Installation (für Hoster)](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#logs-und-caches-pro-website)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
