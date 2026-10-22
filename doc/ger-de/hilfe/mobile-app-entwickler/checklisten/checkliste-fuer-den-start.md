---
title: Checkliste für den Start von Apps
audience: mobile-app-developer, app-creator, developer, project-manager, admin
category: mobile-apps
order: 920
summary: Eine Checkliste für die Woche, bevor eine App auf Exponential live geht - die Produktionsinstallation und ihre APIs, Konten und Zugangsdaten, Inhalte und Einstiegspunkte, Bilder, Caching und Last, Fehlerbehandlung, Überwachung, Support und der Plan für Server-Upgrades.
keywords: Start, Checkliste, Livegang, Produktion, Release, REST, expservices, Zugangsdaten, Inhalte, Bildaliase, Caching, Ratenlimit, Überwachung, Support, Upgrades, Mobile App, launch, checklist, go live, production, credentials, content, image aliases, rate limit, monitoring, upgrades, mobile app
type: help
---
# Checkliste für den Start von Apps

Diese Checkliste richtet sich an das App-Team und an die Personen, die die Exponential-Installation betreiben, in der
Woche, bevor die App gegen die Produktion live geht. Jeder Punkt verweist auf die Seite, die ihn erklärt.

## Inhalt

1. [Die Installation](#die-installation)
2. [Konten und Zugangsdaten](#konten-und-zugangsdaten)
3. [Inhalte](#inhalte)
4. [Leistung](#leistung)
5. [Fehler und Überwachung](#fehler-und-überwachung)
6. [Nach dem Start](#nach-dem-start)

## Die Installation

- [ ] Die Prüfanfragen antworten auf der Produktionsadresse wie erwartet
      ([Erst prüfen, dann bauen](../testen/gegen-eine-demo-testen.md#erst-prüfen-dann-bauen)).
- [ ] `ezprestapi` 1.2.6 oder neuer ist aktiv und `ezprestapiprovider` ist entfernt
      ([API-Versionen v1 und v2](../rest-api/api-versionen-v1-und-v2.md#der-veraltete-provider)).
- [ ] `expservices` ist aktiv, wenn die App Remote-Dienste nutzt, und seine Token-Tabelle existiert.
- [ ] Die Basis-URL der App ist Konfiguration und zeigt im Release-Build auf die Produktion.

## Konten und Zugangsdaten

- [ ] Der Integrationsbenutzer und seine Rolle existieren in der Produktion, nicht nur im Staging.
- [ ] Der OAuth-Client ist in der Produktion mit der exakten Weiterleitungsadresse der Produktion registriert.
- [ ] Produktionsschlüssel und -Secrets liegen im Geheimnisspeicher des Backends, und die Testschlüssel sind widerrufen.
- [ ] Die [Sicherheitscheckliste](sicherheitscheckliste.md) ist abgearbeitet.

## Inhalte

- [ ] Die Einstiegsordner existieren in der Produktion mit den Remote-IDs, mit denen die App konfiguriert ist
      ([Knoten-IDs, Remote-IDs und Deep Links](../inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)).
- [ ] Jede Klasse, die die App zeigt, hat Elemente, und die Felder, die die App braucht, sind ausgefüllt.
- [ ] Die Bildaliase, die die App verwendet, existieren in der Produktion
      ([Bildaliase für Bildschirmgrößen](../medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md)).
- [ ] Redakteure wissen, welche Felder die App zeigt und wie lange es dauert, bis eine Veröffentlichung dort ankommt
      ([Entwürfe, Vorschau und Veröffentlichung](../veroeffentlichungsablauf/entwuerfe-vorschau-und-veroeffentlichung.md#was-redakteure-wissen-sollten)).

## Leistung

- [ ] Listen werden mit kleinen Limits seitenweise geladen; Detailaufrufe werden nicht pro Zeile gemacht
      ([Antwortgruppen](../rest-api/antwortgruppen.md#gruppen-für-bildschirme-wählen)).
- [ ] Die App speichert mit stale-while-revalidate zwischen ([Caching in der App](../offline-und-leistung/caching-in-der-app.md)).
- [ ] Ein Backend, das einen einzigen API-Schlüssel verwendet, bleibt unter `RateLimitPerMinute`.
- [ ] Das Serverteam kennt den erwarteten Verkehr, und die TTL des REST-Caches ist abgestimmt
      ([Caching auf dem Server](../offline-und-leistung/caching-auf-dem-server.md)).

## Fehler und Überwachung

- [ ] Jeder Status aus [Fehler und Statuscodes](../rest-api/fehler-und-statuscodes.md) wird behandelt, auch `429` und
      Zeitüberschreitungen.
- [ ] Die App meldet API-Fehler mit Route, Status und Uhrzeit an Ihr Absturz- oder Analysewerkzeug, ohne Zugangsdaten.
- [ ] Jemand beobachtet in den ersten Tagen die Ablehnungen im Prüfprotokoll.
- [ ] Der Support kann „die App ist ausgefallen“ von „die Website ist ausgefallen“ unterscheiden
      ([Zuverlässigkeit des App-Backends](../../unternehmen/mobil-und-headless/zuverlaessigkeit-des-app-backends.md)).

## Nach dem Start

- [ ] Der Upgrade-Plan des Servers enthält zuerst einen Test der App gegen die neue Version
      ([Eine lokale Testinstallation](../testen/eine-lokale-testinstallation.md)).
- [ ] Klassenänderungen, die Felder der App berühren, werden dem App-Team angekündigt.
- [ ] Die Termine für den Austausch von Schlüsseln und Secrets stehen im Kalender.

## Verwandte Seiten

- [Mobil-Checkliste (für Unternehmen)](../../unternehmen/mobil-und-headless/checkliste-mobil.md)
- [Bereit für die App-Store-Prüfung](bereit-fuer-die-app-store-pruefung.md)
- [Bereitstellungs-Pipeline](../../ersteller/anwendungsentwickler/deployment-pipeline.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
