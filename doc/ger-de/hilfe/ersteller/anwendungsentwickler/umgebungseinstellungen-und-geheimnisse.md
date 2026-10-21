---
title: Umgebungseinstellungen und Geheimnisse
audience: app-creator, developer, admin
category: app-creation
order: 550
summary: Einstellungen, die sich je Rechner unterscheiden, und Geheimnisse aus dem Code heraushalten - EXP_ENV und Varianten von Einstellungsdateien, settings/override für die Installation, Dateirechte, was nie in Git gehört und wie eine Anwendung ihre Schlüssel liest.
keywords: Umgebung, Varianten von Einstellungen, Geheimnisse, API-Schlüssel, Passwörter, Dateirechte, Anwendungsentwickler, environment, EXP_ENV, config.env.php, settings variants, secrets, API keys, passwords, settings/override, gitignore, file modes, app creator
type: help
---
# Umgebungseinstellungen und Geheimnisse

Diese Seite ist für Anwendungsentwickler, die mit Einstellungen umgehen, die sich je Rechner unterscheiden, und mit
Geheimnissen wie API-Schlüsseln und Passwörtern. Beides muss aus dem Code-Repository herausbleiben.

## Inhalt

1. [Einstellungen pro Umgebung](#einstellungen-pro-umgebung)
2. [Wo Geheimnisse liegen](#wo-geheimnisse-liegen)
3. [Geheimnisse im Code lesen](#geheimnisse-im-code-lesen)
4. [Checkliste](#checkliste)

## Einstellungen pro Umgebung

Aus [Einstellungen pro Umgebung (EXP_ENV)](../../../funktionen/6.0/umgebungseinstellungen.md):

1. Kopieren Sie `config.env.php.example` nach `config.env.php` und setzen Sie `define( 'EXP_ENV', 'test' );`. Die Datei
   wird im Web und auf der Kommandozeile vor `config.php` gelesen, und `.gitignore` hält sie aus der Versionsverwaltung
   heraus.
2. Legen Sie die Einstellungen der Umgebung in einer Variantendatei ab: der Name der Umgebung direkt nach `.ini`
   (`settings/override/site.ini.test.append.php`, `extension/<ext>/settings/site.ini.test.append.php`).
3. Leeren Sie einmal den INI-Cache; jede Umgebung hat ihre eigenen Cache-Dateien.

Typische Varianten: der Mailversand (`file` außerhalb der Produktivumgebung), die Debug-Ausgabe, Hosts, die Adresse von
Solr.

## Wo Geheimnisse liegen

| Geheimnis | Ort |
|---|---|
| Datenbankpasswort | `settings/override/site.ini.append.php` der Installation |
| API-Schlüssel Ihrer Anwendung | `settings/override/<yourfile>.ini.append.php`, Modus 0640 |
| Anbieterschlüssel von expai | ausschließlich `settings/override/expai.ini.append.php` |
| Geheimnis für das Formular-Token | `[HTMLForms] Secret` in `settings/override/` |

Niemals in: dem Repository Ihrer Erweiterung, einer `.ini`-Datei, die der Webserver ausliefern könnte (verwenden Sie
`.ini.append.php`, das den Inhalt in einen PHP-Kommentar einschließt), Templates, Nutzdaten von Jobs, Logs, URLs oder
Commit-Nachrichten.

## Geheimnisse im Code lesen

```php
$key = eZINI::instance( 'expdemo.ini' )->variable( 'ArchiveSettings', 'ApiKey' );
if ( $key === '' )
    throw new RuntimeException( 'expdemo.ini [ArchiveSettings] ApiKey is not set; put it in settings/override/expdemo.ini.append.php' );
```

Liefern Sie den Schlüssel in der `expdemo.ini` Ihrer Erweiterung als leeren Wert mit einem Kommentar aus, damit
Installationen sehen, wo er zu setzen ist.

## Checkliste

- [ ] `config.env.php` bleibt aus Git heraus (die mitgelieferte `.gitignore` führt sie auf); halten Sie in einem eigenen
      Projekt-Repository auch `settings/override/` aus der Versionsverwaltung heraus.
- [ ] Geheimnisse haben den Modus 0640 und gehören der Gruppe des Webservers.
- [ ] Jede Umgebung hat eigene Schlüssel; Schlüssel der Produktivumgebung liegen nie auf Entwicklungsrechnern.
- [ ] Ein offengelegter Schlüssel lässt sich ohne Codeänderung widerrufen.

**Demomodus.** Die Umgebungsvariable `EXP_DEMO_MODE=1` gehört ausschließlich zu öffentlichen Demos: Eine App, die mit
einer Demo spricht, erhält HTTP 403, wenn sie das Passwort eines Administrators ändern oder zurücksetzen will.
[Umgebungsvariablen](../../../anleitungen/umgebungsvariablen.md), [Demomodus](../../../bc/6.0/demomodus.md).

## Verwandte Seiten

- [Einstellungen und INI-Dateien in Ihrer Anwendung](einstellungen-und-ini-dateien.md)
- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Sicherheitsstandard](../../../standards/sicherheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
