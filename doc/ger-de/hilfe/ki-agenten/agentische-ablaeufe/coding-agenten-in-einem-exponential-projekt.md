---
title: Coding-Agenten in einem Exponential-Projekt
audience: ai-agents, developer, app-creator, designer
category: ai-agents
order: 69
summary: Wie Sie einen KI-Coding-Assistenten in einem Exponential-Projekt so einrichten, dass er auf Grundlage von Fakten arbeitet - die Dokumentation über exp:help, Laufzeitfakten über nur lesende Konsolenbefehle (exp:ini where, exp:access:check, exp:logs) auf einer Entwicklungskopie, die eigenen Build-Schritte des Projekts (php -l, Autoloads, Caches) - und die Regeln, die Geheimnisse, Einstellungs-Overrides und Produktionsdaten außer Reichweite und aus Commits heraus halten.
keywords: Coding-Agent, Coding-Assistent, KI-Pair-Programming, Projektnotizen, exp:help, exp:ini where, exp:access:check, exp:logs, Autoloads, ezpgenerateautoloads, Cache leeren, settings/override, Geheimnisse, Commits, Prüfung, Entwicklungsablauf, coding agent, coding assistant, AI pair programming, project notes, autoloads, cache clear, secrets, commits, review, development workflow
type: help
---
# Coding-Agenten in einem Exponential-Projekt

Diese Seite richtet sich an Entwickler, die mit einem KI-Coding-Assistenten an einer Erweiterung, einem Design oder einer ganzen
Installation arbeiten. Ein solcher Assistent liest Code gut, aber das Verhalten von Exponential hängt von Einstellungsschichten,
Siteaccesses, Overrides und Caches ab, die der Code allein nicht zeigt. Geben Sie ihm die Fakten, und halten Sie ihn von dem fern,
was er nicht anfassen darf.

## Inhalt

1. [Was der Agent braucht](#was-der-agent-braucht)
2. [Dokumentation über exp:help](#dokumentation-über-exphelp)
3. [Laufzeitfakten über die Konsole](#laufzeitfakten-über-die-konsole)
4. [Die Build-Schritte, die er kennen muss](#die-build-schritte-die-er-kennen-muss)
5. [Was er nicht anfassen darf](#was-er-nicht-anfassen-darf)
6. [Seine Arbeit prüfen](#seine-arbeit-prüfen)

## Was der Agent braucht

| Bedarf | Quelle |
|---|---|
| Wie eine Funktion funktionieren soll | Die Hilfe in `doc/` ([Agentenfreundliche Dokumentation](agentenfreundliche-dokumentation.md)) |
| Wie diese Installation gerade konfiguriert ist | Nur lesende Konsolenbefehle auf einer Entwicklungskopie |
| Wie eine Änderung gebaut, getestet und bereitgestellt wird | Die eigenen Anweisungen des Projekts und die Seiten für Anwendungsentwickler |
| Was er ändern darf und was nicht | Ihre Projektnotizen für den Agenten |

## Dokumentation über exp:help

Sagen Sie dem Agenten, dass er sucht, bevor er antwortet: `./console exp:help search "<words>" --json`, und dann die Seiten liest.
Der Abschnitt für Anwendungsentwickler ist die Landkarte des Entwicklers:
[Anwendungsentwickler: hier beginnen](../../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md).

## Laufzeitfakten über die Konsole

Auf einer Entwicklungs- oder Staging-Kopie antworten diese Befehle aus der laufenden Installation statt aus einer Vermutung:

| Frage | Befehl |
|---|---|
| Welche Dateien setzen diese Einstellung, in Ladereihenfolge, und welcher Wert gewinnt? | `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions` |
| Was ist ihr Wert? | `./console exp:ini get site.ini/DesignSettings/SiteDesign` |
| Darf dieser Benutzer diesen Knoten lesen oder bearbeiten, und welche Einschränkung verweigert es? | `./console exp:access:check --user=<login> --node=<id>` |
| Was sagte das Fehlerlog nach der Änderung? | `./console exp:logs --tail=var/log/error.log --lines=50` |
| Was sagt die Dokumentation? | `./console exp:help search "<words>"` |

Geben Sie dem Agenten nur die lesenden Befehle. Befehle, die Einstellungen oder Daten ändern (`exp:ini set`, das Leeren von Caches,
schreibende Skripte), bleiben bei einem Menschen. Ein Befehl läuft als der Systembenutzer, der ihn startet, mit Zugriff auf die
ganze Installation, weshalb das auf eine Entwicklungskopie gehört, nicht in die Produktion.

## Die Build-Schritte, die er kennen muss

Aus den Seiten für Anwendungsentwickler, die Schritte, die ein Agent am häufigsten vergisst:

| Nach | Ausführen | Lesen |
|---|---|---|
| Dem Bearbeiten einer PHP-Datei | `php -l <file>` | [Debugging und Logs](../../ersteller/anwendungsentwickler/fehlersuche-und-logs.md) |
| Dem Hinzufügen oder Umbenennen einer Klasse | `php bin/php/ezpgenerateautoloads.php -e` (Erweiterungen) | [Autoloads und Klassenbenennung](../../ersteller/anwendungsentwickler/autoloads-und-klassenbenennung.md) |
| Dem Ändern von Einstellungen, Klassen oder Templates | Die betroffenen Caches leeren (`php bin/php/ezcache.php`) | [Caching in Anwendungen](../../ersteller/anwendungsentwickler/caching-in-anwendungen.md) |
| Jeder Änderung auf einem laufenden Server | PHP-FPM neu laden oder den Anwendungsserver neu starten, wo Klassen im Speicher gehalten werden | [Mit Velocity bereitstellen](../../ersteller/anwendungsentwickler/mit-velocity-ausliefern.md) |

## Was er nicht anfassen darf

Schreiben Sie das in Ihre Projektnotizen für den Agenten:

- **`settings/override/` und jede Datei mit Schlüsseln oder Passwörtern**: nie in das Gespräch einlesen, nie committen.
  Anbieterschlüssel gehören dorthin und nirgendwo sonst hin.
- **Produktionsdaten**: keine Datenbank-Dumps oder Kundendaten im Kontext des Agenten.
- **Zerstörerische Befehle** (Dateien entfernen, Tabellen löschen, in der Produktion alles leeren) nur nach Bestätigung durch einen
  Menschen.
- **Commits**: Dateien namentlich vormerken, nie alles; den Diff vor dem Committen prüfen.

## Seine Arbeit prüfen

Behandeln Sie die Änderung des Agenten wie den Pull-Request eines Beitragenden:

- Führen Sie die Tests und Prüfungen aus, die Ihr Projekt verwendet ([Anwendungen testen](../../ersteller/anwendungsentwickler/anwendungen-testen.md),
  [CI für Erweiterungen](../../ersteller/anwendungsentwickler/ci-fuer-erweiterungen.md));
- prüfen Sie, dass er keine Kernel-Klassen, Einstellungen oder Template-Operatoren erfunden hat: `exp:help search` und `exp:ini get`
  sagen Ihnen, ob es sie gibt;
- prüfen Sie die Dokumentation, die er geändert hat, falls vorhanden, mit `./console exp:help check`.

## Verwandte Seiten

- [Agentenfreundliche Dokumentation](agentenfreundliche-dokumentation.md)
- [Ein Diagnoseablauf mit der Konsole](ablauf-diagnose-mit-der-konsole.md)
- [Umgebung, Einstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
