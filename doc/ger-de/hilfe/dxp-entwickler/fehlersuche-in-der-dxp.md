---
title: Fehlersuche in einem DXP-Projekt
audience: dxp-developer
category: dxp-development
order: 40
summary: Die Probleme, denen DXP-Entwickler am häufigsten begegnen, mit Ursache und Abhilfe - weiße Seiten, Klasse nicht gefunden, fehlende Assets, Fehler beim Admin-Build, veraltete Inhalte, fehlende Bildvariationen, veraltete Suche, Berechtigungsfehler, JWT-Fehler und die zwei SQLite-Fehler.
keywords: Fehlersuche, Fehler, 500, weiße Seite, Klasse nicht gefunden, Assets 404, Module not found, veralteter Cache, Bildvariationen, Suche veraltet, Permission denied, JWT, SQLite, readonly database, Logs, troubleshooting, errors, white screen, class not found, stale cache, image variations, search outdated, permission denied, logs
type: help
---
# Fehlersuche in einem DXP-Projekt

Diese Seite richtet sich an Entwickler, die jetzt eine Lösung brauchen. Die Fälle sind die aus Abschnitt 20 der
Anleitung des Skeletons, plus die Ursachen dahinter. Beginnen Sie mit den Logs.

## Inhalt

1. [Zuerst die Logs](#zuerst-die-logs)
2. [Probleme und Abhilfen](#probleme-und-abhilfen)
3. [Probleme mit den Forks](#probleme-mit-den-forks)
4. [Um Hilfe bitten](#um-hilfe-bitten)

## Zuerst die Logs

```bash
tail -f var/log/prod.log                     # or dev.log
tail -f /var/log/nginx/error.log             # or /var/log/apache2/error.log
APP_ENV=dev php bin/console cache:clear      # a development cache, for a stack trace on a test machine
```

Schalten Sie eine öffentliche Produktionswebsite nie auf `dev` um; reproduzieren Sie an einer Kopie.

## Probleme und Abhilfen

| Problem | Ursache | Abhilfe |
|---|---|---|
| Weiße Seite oder Fehler 500 | eine Exception | `var/log/<env>.log` lesen |
| "Class not found" nach `composer update` | veralteter Autoloader oder Cache | `composer dump-autoload -o`, dann `cache:clear` |
| 404 bei `/bundles/` oder `/assets/` | öffentliche Assets nicht installiert oder nicht gebaut | `assets:install --symlink --relative public`, `yarn dev`, `yarn ibexa:build` |
| `yarn ibexa:build` schlägt mit "Module not found" fehl | `var/encore/` ist leer | zuerst `assets:install` ausführen |
| Inhalte oder Seiten veraltet | Anwendungs- oder HTTP-Cache | `cache:clear`; den HTTP-Cache leeren ([HTTP-Cache](http-cache-und-varnish.md)) |
| Bildvariationen fehlen | erzeugte Dateien entfernt oder Einstellungen geändert | `liip:imagine:cache:remove`, `cache:clear`; sie kommen bei der nächsten Anfrage zurück |
| Suchergebnisse veraltet | Index nicht aktualisiert | `exponential:reindex` ([Neu indexieren](neu-indexieren.md)) |
| "Permission denied" in `var/` oder `public/var/` | der Webserver kann nicht schreiben | die ACLs setzen ([Dateiberechtigungen](dateiberechtigungen.md)) |
| JWT-Fehler bei REST oder GraphQL | fehlende oder nicht lesbare Schlüssel | `lexik:jwt:generate-keypair --overwrite`, `cache:clear` ([JWT](jwt-authentifizierung.md)) |
| `no such table: ibexa_section` (SQLite) | Datenbank nie initialisiert | `exponential:install exponential-oss` ausführen |
| `attempt to write a readonly database` (SQLite) | falscher Eigentümer der Datenbankdatei | `chmod 664`, `chown` auf die Gruppe des Webservers |
| REST-Client erhält 404 auf `/api/ezp/v2/` | das Präfix von v5 ist anders | `ibexa.rest.path_prefix` prüfen ([Die REST-API](die-rest-api.md#wo-sie-liegt)) |

## Probleme mit den Forks

| Symptom | Ursache | Prüfen |
|---|---|---|
| Der Admin-Build liest Dateien aus `vendor/ibexa/...` | das Upstream-Paket ist neben dem Fork installiert | `composer why ibexa/admin-ui-assets` |
| Eine Methode "existiert nicht" an einer Klasse, die sie hat | zwei Pakete definieren die Klasse | `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"` |
| "Call to undefined method" in einem Layouts-Block-Template unter PHP 8.4 | Upstream `netgen/layouts-core` statt des Forks | `composer why netgen/layouts-core` ([Layouts Core](layouts-core-in-der-dxp.md)) |

Siehe [Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md#die-regel-nie-beide).

## Um Hilfe bitten

Sammeln Sie die PHP-Version, `composer show "se7enxweb/*"`, den genauen Fehler aus dem Log und die Schritte zum
Reproduzieren. Melden Sie Fehler im Issue-Tracker des betroffenen Repositorys (Skeleton, Metapaket oder Fork); melden Sie
Sicherheitsprobleme vertraulich, wie es das README des Skeletons verlangt, nicht in einem öffentlichen Issue.

## Verwandte Seiten

- [Befehle für den Alltag](befehle-fuer-den-alltag.md)
- [FAQ für DXP-Entwickler](faq-fuer-dxp-entwickler.md)
- [Wenn etwas schiefgeht (Exponential 6)](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
