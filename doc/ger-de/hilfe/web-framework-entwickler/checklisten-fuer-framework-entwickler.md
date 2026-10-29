---
title: Checklisten für Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 510
summary: Vier Checklisten für Entwickler, die von anderen Frameworks kommen - Exponential für ein Projekt bewerten, eine Entwicklungsumgebung einrichten, ein erstes Modul vor dem Merge prüfen und eine Anwendung live schalten - jeder Punkt mit einem Link auf die Seite, die ihn erklärt.
keywords: Checkliste, Bewertung, Einrichtung, Code-Review, Livegang, Inbetriebnahme, Deployment, Sicherheit, Caching, Testen, checklist, evaluation, setup, code review, go-live, launch, deployment, security, caching, testing, Web-Framework-Entwickler, web framework developer
type: help
---
# Checklisten für Framework-Entwickler

Diese Seite richtet sich an Entwickler, die lieber Punkte abhaken als Kapitel lesen. Jeder Punkt verweist auf die
Seite, die ihn erklärt.

## Inhalt

1. [Exponential für ein Projekt bewerten](#exponential-für-ein-projekt-bewerten)
2. [Eine Entwicklungsumgebung einrichten](#eine-entwicklungsumgebung-einrichten)
3. [Ein erstes Modul prüfen](#ein-erstes-modul-prüfen)
4. [Eine Anwendung live schalten](#eine-anwendung-live-schalten)

## Exponential für ein Projekt bewerten

- [ ] Die Daten, die Redakteure verwalten, sind aufgelistet, und jede Art ist entweder eine Inhaltsklasse oder eine
      eigene Tabelle ([Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md)).
- [ ] Websites, Sprachen und Zielgruppen sind Siteaccesses zugeordnet ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).
- [ ] Berechtigungen sind als Rollen mit Funktionen und Einschränkungen beschrieben ([Autorisierung](autorisierung-rollen-und-richtlinien.md)).
- [ ] Jede Funktion, auf die Sie zählen, ist im Produkt und in der Distribution geprüft, die Sie installieren werden,
      und nicht aus einem anderen Framework abgeleitet ([Die Plattformprodukte](die-plattformprodukte.md), [Begriffswörterbuch: keine direkte Entsprechung](begriffswoerterbuch.md#kein-direktes-gegenstück)).
- [ ] Das Team hat [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md) gelesen.

## Eine Entwicklungsumgebung einrichten

- [ ] Eine lokale Installation: `./console exp:install --apply` (SQLite) ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).
- [ ] `config.env.php` mit `EXP_ENV=dev`; Debug-Ausgabe und der Mail-Transport `file` in der Variante `dev`
      ([Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md)).
- [ ] Velocity mit `Engine=qbix` oder Apache bzw. nginx mit PHP-FPM ([Server und Deployment](server-und-deployment.md)).
- [ ] Template-Pfad-Kommentare und die Debug-Leiste eingeschaltet ([Debugging](debugging-logging-und-fehler.md)).
- [ ] Eine eigene Erweiterung, aktiv, in einem eigenen Git-Repository ([Pakete](pakete-erweiterungen-und-composer.md),
      [Entwicklungsumgebung](../ersteller/anwendungsentwickler/entwicklungsumgebung.md)).

## Ein erstes Modul prüfen

- [ ] Jeder View nennt seine Richtlinienfunktionen in `module.php` ([Routing](routing-mit-modulen-und-views.md)).
- [ ] Parameter werden umgewandelt und geprüft; Weiterleitungsziele werden geprüft ([Validierung](validierung.md)).
- [ ] Jeder ausgegebene Wert wird mit wash behandelt ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)).
- [ ] Formulare senden das Formular-Token mit; `$_POST` wird nirgends direkt gelesen ([Formulare und CSRF](formulare-und-csrf-schutz.md)).
- [ ] Kein SQL in Inhaltstabellen; persistente Objekte oder maskiertes SQL für eigene Tabellen ([Abfragen](inhalte-und-tabellen-abfragen.md)).
- [ ] Kein Zustand pro Request in statischen Eigenschaften ([Server und Deployment](server-und-deployment.md#regeln-für-persistente-worker)).
- [ ] Einstellungen sind in der INI-Datei Ihrer Erweiterung dokumentiert, Geheimnisse liegen nur in `settings/override/` ([Konfiguration](konfiguration-ini-dateien.md)).
- [ ] Logik in Klassen mit Unit-Tests, die ohne Datenbank laufen ([Testen](testen.md)).
- [ ] Kein Inline-`<style>` und kein `style=""` in Administrations-Templates ([Styling von admin4](../../bc/6.0/admin4-gestaltung.md)).

## Eine Anwendung live schalten

- [ ] Ihr Schema und Ihre Klassen lassen sich mit einem Befehl installieren; für spätere Releases gibt es Upgrade-Schritte ([Migrationen](migrationen-und-dba-schemadateien.md)).
- [ ] Die Cronjob-Teile `frequent` und `infrequent`, Ihre eigenen Teile und die Queue-Worker sind eingeplant ([Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md), [Warteschlangen](warteschlangen-und-hintergrundjobs.md)).
- [ ] Die Debug-Ausgabe ist für Besucher ausgeschaltet; Geheimnisse sind pro Maschine mit dem Modus 0640 gesetzt.
- [ ] `[HTMLForms] Secret` ist für die Installation gesetzt ([Formulare und CSRF](formulare-und-csrf-schutz.md)).
- [ ] Caches und HTTP-Header sind festgelegt: View-Cache, Cache-Blöcke, HTTP-Cache, Velocity-Response-Cache ([Caching](cache-ebenen.md), [HTTP-Caching](http-caching-und-reverse-proxies.md)).
- [ ] Der Ablauf eines Releases ist aufgeschrieben und auf Staging erprobt ([Server und Deployment](server-und-deployment.md#ein-release-schritt-für-schritt)).
- [ ] Die Anleitung zur Sicherheitshärtung wurde gelesen ([Sicherheitshärtung](../sicherheitshaertung.md)).
- [ ] Die Checkliste für den Website-Start ist für die öffentliche Website abgearbeitet ([Checkliste für den Start](../ersteller/website-ersteller/checkliste-fuer-den-start.md)).

## Verwandte Seiten

- [Zehn Stolperfallen für Framework-Entwickler](stolperfallen-fuer-framework-entwickler.md)
- [FAQ für Web-Framework-Entwickler](faq-fuer-web-framework-entwickler.md)
- [Sicherheit für Anwendungen](../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
