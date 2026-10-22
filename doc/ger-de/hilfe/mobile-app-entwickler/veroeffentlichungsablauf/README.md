---
title: Veröffentlichungsablauf für Apps
audience: mobile-app-developer, app-creator, developer, editor
category: mobile-apps
order: 700
summary: Die Einstiegsseite zu den Seiten über den Veröffentlichungsablauf für App-Entwickler - wie Entwürfe, Versionen und das Veröffentlichen durch Redakteure mit dem zusammenhängen, was eine App sieht, die Vorschau von App-Bildschirmen vor dem Veröffentlichen, die Benutzerkonten und Rollen, unter denen eine App läuft, und das Prüfprotokoll der App-Aktivität.
keywords: Veröffentlichen, Veröffentlichungsablauf, Workflow, Entwürfe, Versionen, Vorschau, Rollen, Richtlinien, Berechtigungen, Integrationsbenutzer, Prüfprotokoll, Redakteure, Mobile App, publishing, workflow, drafts, versions, preview, roles, policies, permissions, integration user, audit, editors, mobile app
type: help
---
# Veröffentlichungsablauf für Apps

Dieser Ordner richtet sich an App-Entwickler, die wissen müssen, wie Inhalte vom Redakteur in die App gelangen, und
die die Konten einrichten, unter denen die App läuft. Redakteure arbeiten in der Administrationsoberfläche; die App
sieht nur, was veröffentlicht ist und was ihr Benutzer lesen darf.

## Inhalt

1. [Vom Entwurf zur App](#vom-entwurf-zur-app)
2. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Vom Entwurf zur App

```text
editor saves a draft  ->  (approval workflow, if any)  ->  published version  ->  REST read (may be cached)  ->  app
```

- Die REST-API liest nur die veröffentlichte Version.
- Freigabe und zeitgesteuertes Veröffentlichen werden auf dem Server konfiguriert; die App sieht den Eintrag, sobald
  er veröffentlicht ist ([Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md),
  [Workflows](../../../anleitungen/workflows.md)).
- Caches auf dem Server und in der App fügen ihre eigene Verzögerung hinzu
  ([Caching auf dem Server](../offline-und-leistung/caching-auf-dem-server.md)).
- Was der Benutzer der App lesen darf, entscheidet darüber, ob überhaupt etwas erscheint
  ([Berechtigungen für App-Benutzer](berechtigungen-fuer-app-benutzer.md)).

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Entwürfe, Vorschau und Veröffentlichung](entwuerfe-vorschau-und-veroeffentlichung.md) | Was die App von Entwürfen und Versionen sehen kann und wie Redakteure App-Bildschirme in der Vorschau ansehen |
| [Berechtigungen für App-Benutzer](berechtigungen-fuer-app-benutzer.md) | Integrationsbenutzer, Rollen, Einschränkungen und die Prüfung dessen, was die App tut |

## Verwandte Seiten

- [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)
- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)
- [Headless Exponential: Vorschau und Entwürfe](../../ersteller/anwendungsentwickler/headless-exponential.md#vorschau-und-entwürfe)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
