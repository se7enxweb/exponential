---
title: Zu den Forks beitragen
audience: dxp-developer
category: dxp-development
order: 46
summary: Wie ein DXP-Entwickler etwas zurückgibt - wo Sie einen Fehler melden (Skeleton, Metapaket oder ein Fork), wie Sie einen Pull-Request so senden, wie es das README des Skeletons beschreibt, wie Sie eine Änderung an einem Fork in Ihrem eigenen Projekt testen und wie Sicherheitsmeldungen erfolgen.
keywords: beitragen, Pull-Request, Fork, Issue, Fehlerbericht, se7enxweb, GitHub, phpunit, Path-Repository, Sicherheitsmeldung, Gemeinschaft, Diskussionen, contribute, pull request, bug report, path repository, security report, community, discussions
type: help
---
# Zu den Forks beitragen

Diese Seite richtet sich an Entwickler, die bei der Arbeit an einem DXP-Projekt einen Fehler gefunden oder eine
Verbesserung geschrieben haben. Das Projekt wächst durch Beiträge; die se7enxweb-Repositorys nehmen Pull-Requests an.

## Inhalt

1. [Wohin eine Änderung gehört](#wohin-eine-änderung-gehört)
2. [Einen Fehler melden](#einen-fehler-melden)
3. [Ein Pull-Request, Schritt für Schritt](#ein-pull-request-schritt-für-schritt)
4. [Eine Änderung an einem Fork in Ihrem Projekt testen](#eine-änderung-an-einem-fork-in-ihrem-projekt-testen)
5. [Sicherheitsmeldungen](#sicherheitsmeldungen)
6. [Orte zum Austausch](#orte-zum-austausch)

## Wohin eine Änderung gehört

| Das Problem liegt in | Repository |
|---|---|
| der Installationsanleitung, der Projektvorlage | `se7enxweb/exponential-platform-dxp-skeleton` |
| welchen Paketen die DXP installiert | `se7enxweb/exponential-platform-dxp` |
| dem Inhaltskernel und seinen Befehlen | `se7enxweb/exponential-platform-dxp-core` |
| der Administrationsoberfläche | `se7enxweb/admin-ui` (Assets: `se7enxweb/admin-ui-assets`) |
| Rich Text | `se7enxweb/fieldtype-richtext` |
| einem Upstream-Paket `ibexa/*`, das die DXP unverändert verlangt | dem Upstream-Projekt; blockiert es die DXP, eröffnen Sie zusätzlich ein Issue beim Metapaket |

`composer show -s` und `composer why <package>` sagen Ihnen, aus welchem Paket eine Klasse kommt.

## Einen Fehler melden

Verwenden Sie den Issue-Tracker des Repositorys. Geben Sie die PHP-Version, `composer show "se7enxweb/*"`, den genauen
Fehler aus `var/log/` und die Schritte zum Reproduzieren auf einer frischen Installation des Skeletons an (mit SQLite geht
das schnell, [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)).
[Gute Issues schreiben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/gute-issues-schreiben.md) gilt auch hier.

Prüfen Sie zuerst, ob das Verhalten nicht beabsichtigt ist. Manche gemeldeten Fehler sind
[negative Funktionen](../projekt/unterstuetzung-erhalten.md#negative-funktionen): Verhalten, das absichtlich ist oder sich
unterschiedlich auslegen lässt und auf das sich andere Projekte verlassen. Eines davon zu ändern braucht eine klare
Begründung, funktioniert am besten als Option statt als neuer Standard und kann mehrere Prüfrunden brauchen, bevor es
gemergt wird.

## Ein Pull-Request, Schritt für Schritt

Das README des Skeletons beschreibt den Weg:

1. Forken Sie das Repository auf GitHub.
2. Klonen Sie Ihren Fork und legen Sie einen Branch an: `git checkout -b feature/my-improvement`.
3. Installieren Sie den vollständigen Entwicklungs-Stack (`APP_ENV=dev`).
4. Nehmen Sie die Änderung vor und folgen Sie dabei den Codierstandards des Repositorys.
5. Testen Sie mit `php bin/phpunit` und prüfen Sie auf Regressionen.
6. Pushen Sie den Branch und eröffnen Sie einen Pull-Request gegen den Standard-Branch (`master` beim Skeleton).
7. Beantworten Sie die Prüfung.

Schreiben Sie die Commit-Nachricht so wie das Projekt: was sich geändert hat und warum, mit einem Präfix wie `Added:`
oder `Updated:` ([Commit-Nachrichten](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)).

## Eine Änderung an einem Fork in Ihrem Projekt testen

Richten Sie Composer mit einem Path-Repository auf Ihren Klon, in einem Branch Ihres Projekts, nie in der Produktion:

```json
{
    "repositories": [
        { "type": "path", "url": "../admin-ui", "options": { "symlink": true } }
    ]
}
```

```bash
composer require "se7enxweb/admin-ui:dev-my-branch as 5.0.5.99"
```

Entfernen Sie den Repository-Eintrag wieder, bevor Sie den Branch Ihres Projekts mergen.

## Sicherheitsmeldungen

Eröffnen Sie für eine Schwachstelle kein öffentliches Issue. Das README des Skeletons nennt eine Sicherheits-E-Mail-Adresse
für verantwortungsvolle Meldungen; verwenden Sie sie und geben Sie den Maintainern Zeit, eine Korrektur zu
veröffentlichen.

## Orte zum Austausch

Das README des Skeletons listet die Orte der Gemeinschaft: GitHub Discussions im Repository des Metapakets, die Foren unter
share.exponential.earth und die Chat-Kanäle. Siehe auch
[Wer ist die Exponential Foundation?, Orte der Gemeinschaft](../wer-ist-die-exponential-foundation.md#orte-der-community).

## Verwandte Seiten

- [Mit Pull-Requests zurückgeben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)
- [Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
