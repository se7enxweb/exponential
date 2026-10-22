---
title: "Umgebungen und Geheimnisse: EXP_ENV und settings/override"
audience: web-framework-developer, developer
category: web-frameworks
order: 180
summary: Das Gegenstück in Exponential zu APP_ENV, .env-Dateien, Rails-Credentials und Spring-Profilen – die Konstante EXP_ENV in config.env.php, Einstellungsvarianten pro Umgebung, was der Umgebungsname ändert, wo Geheimnisse liegen, wie Code sie liest und was nicht aus Umgebungsvariablen gelesen wird.
keywords: Umgebung, environment, EXP_ENV, config.env.php, APP_ENV, .env, dotenv, Einstellungsvarianten, settings variants, Geheimnisse, secrets, settings/override, Credentials, credentials, Profile, profiles, exp_environment, eZINI::environment, EXP_DEMO_MODE, Web-Framework-Entwickler, web framework developer
type: help
---
# Umgebungen und Geheimnisse: EXP_ENV und settings/override

Diese Seite richtet sich an Entwickler, die Umgebungen mit `APP_ENV` umschalten, Geheimnisse in `.env.local`, in
Rails-Credentials oder einem Vault aufbewahren und erwarten, dass sich `dev`, `test` und `prod` unterschiedlich
verhalten. Exponential benennt die Umgebung einer Maschine mit einer einzigen Konstante, `EXP_ENV`, und erlaubt jeder
Einstellungsdatei eine Variante dafür ([Einstellungen pro Umgebung (EXP_ENV)](../../funktionen/6.0/umgebungseinstellungen.md)).

## Inhalt

1. [Die Umgebung benennen](#die-umgebung-benennen)
2. [Einstellungsvarianten](#einstellungsvarianten)
3. [Was der Name ändert](#was-der-name-ändert)
4. [Geheimnisse](#geheimnisse)
5. [Umgebungsvariablen](#umgebungsvariablen)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Die Umgebung benennen

Kopieren Sie `config.env.php.example` im Stammverzeichnis der Installation nach `config.env.php`:

```php
<?php
define( 'EXP_ENV', 'test' );
```

`config.env.php` wird von den Web-Einstiegspunkten und den Kommandozeilenskripten vor `config.php` gelesen, und die
mitgelieferte `.gitignore` hält die Datei aus der Versionsverwaltung heraus. Ein Name besteht aus Kleinbuchstaben,
Ziffern, `-` oder `_`, beginnt mit einem Buchstaben und hat höchstens 32 Zeichen; ein ungültiger Name wird so
behandelt, als gäbe es keine Umgebung.

## Einstellungsvarianten

Der Umgebungsname steht direkt nach `.ini`:

| Standarddatei | Variante für `test` |
|---|---|
| `settings/site.ini` | `settings/site.ini.test` |
| `settings/override/site.ini.append.php` | `settings/override/site.ini.test.append.php` |
| `settings/siteaccess/admin/site.ini.append.php` | `settings/siteaccess/admin/site.ini.test.append.php` |
| `extension/site_example/settings/site.ini.append.php` | `extension/site_example/settings/site.ini.test.append.php` |

Eine Variante wird in jeder Ebene direkt nach ihrer Standarddatei gelesen und überschreibt nur diese Ebene, sodass eine
Siteaccess-Einstellung weiterhin gegen eine Basisvariante gewinnt. Jede Umgebung hat eigene INI-Cache-Dateien. Typische
Varianten: der Mail-Transport (`file` außerhalb der Produktion), die Debug-Ausgabe, Hosts, die Solr-Adresse.

```ini
# settings/override/site.ini.test.append.php
<?php /*
[MailSettings]
Transport=file

[DebugSettings]
DebugOutput=enabled
*/ ?>
```

## Was der Name ändert

| `EXP_ENV` | Wirkung |
|---|---|
| `dev`, `test` | `index.php` zeigt PHP-Fehler im Browser an (`display_errors` eingeschaltet) |
| `prod`, jeder andere Name, keiner | `display_errors` bleibt so, wie php.ini es setzt |

Templates lesen den Namen mit `{exp_environment()}`, PHP mit `eZINI::environment()` (der Name oder `false`). Die
Administrationsdesigns admin4 und admin4l können die Umgebung als Plakette anzeigen (`site.ini [AdminEnvironment] Level=auto`).
Anders als die `dev`-Umgebung von Symfony oder `DEBUG` in Django schaltet der Name von sich aus weder einen Profiler
noch eine Debug-Toolbar ein: Diese schalten Sie in der Variantendatei der Umgebung ein.

## Geheimnisse

| Geheimnis | Wo |
|---|---|
| Datenbankpasswort | `settings/override/site.ini.append.php` der Installation |
| API-Schlüssel Ihrer Anwendung | `settings/override/<yourfile>.ini.append.php`, Modus 0640 |
| Geheimnis für Formular-Token | `site.ini [HTMLForms] Secret` in `settings/override/` |

Niemals im Repository Ihrer Erweiterung, in einer `.ini`-Datei, die der Webserver ausliefern könnte, in Templates,
Job-Nutzdaten, Logs, URLs oder Commit-Nachrichten. Liefern Sie jeden Schlüssel als leeren Wert mit einem Kommentar in der
INI-Datei Ihrer Erweiterung aus und brechen Sie mit einer klaren Meldung ab, wenn er leer ist
([Umgebungseinstellungen und Geheimnisse](../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)).

```php
$key = eZINI::instance( 'expdemo.ini' )->variable( 'ArchiveSettings', 'ApiKey' );
if ( $key === '' )
    throw new RuntimeException( 'expdemo.ini [ArchiveSettings] ApiKey is not set; put it in settings/override/expdemo.ini.append.php' );
```

## Umgebungsvariablen

Einstellungen werden nicht aus Umgebungsvariablen gelesen, wie es `%env(...)%`, `env()` oder `os.environ` tun. Einige
Umgebungsvariablen gibt es für bestimmte Zwecke, zum Beispiel `EXP_DEMO_MODE=1` für öffentliche Demos
([Umgebungsvariablen](../../anleitungen/umgebungsvariablen.md)). Wenn Ihre Plattform Geheimnisse als Umgebungsvariablen
einspeist, lesen Sie sie in Ihrem eigenen Code mit `getenv()` und dokumentieren Sie das, oder lassen Sie Ihr Deployment
sie in `settings/override/` schreiben.

## Im Vergleich mit anderen Frameworks

| Framework | Umgebung | Geheimnisse | Exponential |
|---|---|---|---|
| Symfony | `APP_ENV`, `when@env` | `.env.local`, Secrets-Vault | `EXP_ENV`, Varianten; `settings/override/` |
| Laravel | `APP_ENV` | `.env` | dasselbe |
| Django | Einstellungsmodule, `DJANGO_SETTINGS_MODULE` | Umgebung, Vaults | dasselbe |
| Rails | `RAILS_ENV` | verschlüsselte Credentials | dasselbe; keine eingebaute Verschlüsselung |
| Spring | Profile | Property Sources, Vaults | dasselbe |

## Verwandte Seiten

- [Konfiguration: INI-Dateien](konfiguration-ini-dateien.md)
- [Einstellungen pro Umgebung (EXP_ENV)](../../funktionen/6.0/umgebungseinstellungen.md)
- [Sicherheitsstandard](../../standards/sicherheit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
