---
title: Konsolenbefehle und Umbenennungen
audience: dxp-developer
category: dxp-development
order: 35
summary: Die Konsolenbefehle, die ein DXP-Entwickler verwendet, das Präfix exponential und seine Aliase ibexa, die Befehle, die ihre Upstream-Namen behalten, und wie Sie alte Namen in Skripten und Cron-Dateien finden und umstellen.
keywords: Konsole, bin/console, exponential:, ibexa:, Aliase, umbenannte Befehle, exponential:install, exponential:reindex, ibexa:cron:run, ibexa:graphql:generate-schema, cleanup-versions, copy-subtree, console, aliases, renamed commands
type: help
---
# Konsolenbefehle und Umbenennungen

Diese Seite richtet sich an Entwickler, die ein DXP-Projekt mit Skripten steuern. Die Befehle des Inhaltskernels haben
einen primären Namen `exponential:`; die früheren Namen `ibexa:` bleiben als funktionierende Aliase. Die vollständige
Referenz ist [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md); diese Seite
ist die Kurzform.

## Inhalt

1. [Die Regel](#die-regel)
2. [Die Befehle des Core](#die-befehle-des-core)
3. [Befehle mit Upstream-Namen](#befehle-mit-upstream-namen)
4. [Befehle von Symfony und Doctrine, die Sie brauchen](#befehle-von-symfony-und-doctrine-die-sie-brauchen)
5. [Alte Namen umstellen](#alte-namen-umstellen)

## Die Regel

| Präfix | Status |
|---|---|
| `exponential:*` | primärer Name, in allem Neuen verwenden |
| `ibexa:*` | veralteter Alias, voll funktionsfähig |
| `ezplatform:*` | existiert auf Platform v5 nicht |

## Die Befehle des Core

Jeder dieser Befehle wurde im installierten `se7enxweb/exponential-platform-dxp-core` gefunden:

| Befehl | Zweck |
|---|---|
| `exponential:install [type]` | Schema und Ausgangsdaten installieren (standardmäßig `exponential-oss`; `--skip-indexing`) |
| `exponential:reindex` | den Suchindex neu aufbauen ([Neu indexieren](neu-indexieren.md)) |
| `exponential:check-urls` | externe Links in Inhalten prüfen |
| `exponential:content:cleanup-versions` | Entwürfe und archivierte Versionen aufräumen (`--keep`, `--status`, `--excluded-content-types`) |
| `exponential:content:remove-duplicate-fields` | doppelte Inhaltsfelder entfernen |
| `exponential:content-type-group:set-system` | eine Gruppe von Inhaltstypen als System kennzeichnen |
| `exponential:copy-subtree <source> <target>` | einen Teilbaum von Platzierungen kopieren |
| `exponential:delete-content-translation` | eine Übersetzung eines Inhaltselements löschen |
| `exponential:urls:regenerate-aliases` | URL-Aliase neu aufbauen |
| `exponential:images:resize-original` | gespeicherte Originalbilder gesammelt verkleinern |
| `exponential:images:normalize-paths` | Speicherpfade von Bildfeldern korrigieren |
| `exponential:timestamps:to-utc` | Datums- und Zeitwerte in UTC umwandeln (zuerst ein Probelauf) |
| `exponential:io:migrate-files` | Binärdateien zwischen Speicher-Handlern verschieben |
| `exponential:user:validate-password-hashes` | Hash-Algorithmen der Passwörter prüfen |
| `exponential:user:expire-password` | den Ablauf von Passwörtern erzwingen |
| `exponential:debug:config-resolver` (kurz: `exponential:debug:config`) | den Wert zeigen, den ein Siteaccess auflöst |

Befehle, die Inhalte ändern, nehmen `--user`; der Benutzer braucht die passenden Richtlinien.

## Befehle mit Upstream-Namen

| Befehl | Zweck |
|---|---|
| `ibexa:cron:run` | die Cron-Jobs ausführen ([Cron](cron.md)) |
| `ibexa:graphql:generate-schema` | das GraphQL-Schema erzeugen ([GraphQL](graphql-und-graphiql.md)) |
| `ibexa:user:update-user` | einen Benutzer von der Kommandozeile aktualisieren (aus `ibexa/user`) |
| `lexik:jwt:generate-keypair` | JWT-Schlüssel ([JWT](jwt-authentifizierung.md)) |
| `liip:imagine:cache:remove` | erzeugte Bildvariationen entfernen |
| `fos:httpcache:invalidate:path`, `fos:httpcache:invalidate:tag` | den HTTP-Cache leeren |
| `bazinga:js-translation:dump` | JavaScript-Übersetzungen der Administration |

## Befehle von Symfony und Doctrine, die Sie brauchen

```bash
php bin/console list                                   # everything registered
php bin/console help <command>                         # options of one command
php bin/console cache:clear && php bin/console cache:warmup
php bin/console debug:router
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console messenger:consume ibexa.messenger.transport
```

## Alte Namen umstellen

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d /var/spool/cron ./deploy 2>/dev/null
```

Ersetzen Sie jeden Treffer, dessen Befehl einen Namen `exponential:` hat. Nichts bricht, wenn Sie warten: Die Aliase
bleiben.

## Verwandte Seiten

- [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Befehle für den Alltag](befehle-fuer-den-alltag.md)
- [Forks der Plattformpakete, Schritt 4](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-4-namen-der-konsolenbefehle)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
