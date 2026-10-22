---
title: Caches verstehen
audience: student
category: learning
order: 20
summary: Caches für Lernende erklärt - warum Exponential gespeicherte Ergebnisse vorhält, der Inhalts-, Template- und INI-Cache, denen Sie in Übungen begegnen, wie Sie den kleinsten Cache in der Administrationsoberfläche oder in der Shell leeren, was die Engine php und Velocity hinzufügen und wie Sie ein Cache-Problem von einem echten Fehler unterscheiden.
keywords: cache, caches, inhalts-cache, template-cache, INI-cache, cache leeren, ezcache, exp:cache, velocity-cache, veraltete seite, lernende, erklärung, content cache, template cache, INI cache, clear cache, velocity cache, stale page, learner, explanation
type: help
---
# Caches verstehen

Ein **Cache** hält gespeicherte Ergebnisse vor, die Seiten schneller machen; Exponential hat viele davon
(Inhaltsansicht, Template, INI, HTTP, statisch, SQL-Abfragen, Antworten) ([Glossar](../../glossar.md#c)). Lernende
begegnen Caches, wenn eine Änderung, die sie gemacht haben, nicht erscheint. Diese Seite erklärt, welchen Cache Sie in
Verdacht haben sollten und wie Sie nur diesen leeren.

## Inhalt

1. [Warum es Caches gibt](#warum-es-caches-gibt)
2. [Die drei Caches, denen Sie zuerst begegnen](#die-drei-caches-denen-sie-zuerst-begegnen)
3. [In der Administrationsoberfläche leeren](#in-der-administrationsoberfläche-leeren)
4. [In der Shell leeren](#in-der-shell-leeren)
5. [Wenn die Seite immer noch alt ist](#wenn-die-seite-immer-noch-alt-ist)
6. [Cache-Problem oder echter Fehler](#cache-problem-oder-echter-fehler)

## Warum es Caches gibt

Eine Seite aufzubauen heißt, Einstellungen zu lesen, Templates zu finden, sie zu kompilieren und Inhalte zu holen.
Caches bewahren die Ergebnisse auf, sodass der nächste Besucher die Seite ohne diese Arbeit bekommt. Exponential
erneuert sie, wenn Inhalte veröffentlicht werden, „daher ist das Leeren von Hand die Ausnahme“
([Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md)).

## Die drei Caches, denen Sie zuerst begegnen

| Sie haben geändert | Zu leerender Cache | Typische Übung |
|---|---|---|
| Inhalte, und eine Seite zeigt die alte Fassung | **Inhalt** (Cache der Inhaltsansichten) | Veröffentlichen in [Projekt 2](projekt-02-eine-kleine-website-struktur.md) |
| Ein Template oder eine Designdatei, oder Sie haben einen neuen Template-Pfad hinzugefügt | **Template**-Caches | [Projekt 3](projekt-03-ein-template-override.md) |
| Eine Einstellung in einer Datei `.ini` | **INI**-Caches | [Projekt 5](projekt-05-eine-mehrsprachige-website.md) |
| Sie wissen es nicht, oder mehreres davon | **Alle Caches** | Selten: Danach wird alles neu aufgebaut |

Quelle: [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md). Die Regel: Leeren Sie den kleinsten Cache, der
das Problem löst.

## In der Administrationsoberfläche leeren

- **Schnell:** der Kasten **Cache leeren** in der rechten Spalte: Wählen Sie einen Cache und klicken Sie auf
  **Leeren**.
- **Volle Auswahl:** **Setup > Cache-Verwaltung**. Die Seite **Caches** gruppiert jeden Cache (**Inhalte und
  Ansichten**, **Templates**, **INI und Einstellungen**, **Bilder**, **Seiten, Abfragen und der Server**, **Sonstige**)
  und hat Schaltflächen für Inhalts-, Template-, INI- oder alle Caches.

Sie brauchen die Richtlinie `setup/managecache`. Der Administrator Ihrer Lernwebsite hat sie.

## In der Shell leeren

Im Wurzelverzeichnis der Installation ([Caches](../../anleitungen/caches.md#5-in-einer-shell)):

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user      # the content caches
php bin/php/ezcache.php --clear-tag=template --allow-root-user     # the template caches
php bin/php/ezcache.php --clear-tag=ini --allow-root-user          # the settings caches
php bin/php/ezcache.php --clear-all --allow-root-user              # everything
php bin/php/console exp:cache clear --id=content --dry-run --allow-root-user   # what would be cleared
```

Lassen Sie `--allow-root-user` weg, wenn Sie nicht `root` sind. Ein Probelauf ist eine gute Gewohnheit: Er zeigt, was
ein Leeren entfernen würde, mit Größen.

Die Anleitung zu Templates fügt eine Regel für Overrides hinzu: Ein **neuer** Template-Pfad braucht einmal das Leeren
des Override- und des Template-Caches ([Templates und Design](../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)).

## Wenn die Seite immer noch alt ist

| Ursache | Was Sie tun | Quelle |
|---|---|---|
| Der Browser zeigt seine eigene Kopie | Mit gedrückter Umschalttaste neu laden oder ein privates Fenster verwenden | [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md#was-schiefgehen-kann) |
| Der Antwort-Cache von Velocity antwortet für einige Sekunden | `php bin/php/console exp:velocity cache clear`, oder warten | [Caches](../../anleitungen/caches.md#4-velocity) |
| Ein laufendes Velocity hält Einstellungen im Speicher | `php bin/php/console exp:velocity restart --allow-root-user` nach dem Leeren der INI-Caches | [Caches](../../anleitungen/caches.md#4-velocity) |
| Eine Änderung der Einstellungen auf einem Server mit PHP-FPM | Das PHP des Webservers muss neu laden; fragen Sie die Person, die bereitstellt | [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md#was-schiefgehen-kann) |

Auf einer Lernwebsite, die mit der Engine `php` ausgeliefert wird, sind die ersten beiden Zeilen die üblichen.

## Cache-Problem oder echter Fehler

| Anzeichen | Wahrscheinlich |
|---|---|
| Die alten Inhalte oder das alte Markup erscheinen, ohne Fehler | Ein Cache |
| Eine Fehlerseite, eine weiße Seite oder ein Status 500 | Ein echter Fehler: Lesen Sie die Logs ([Logs und Fehler lesen](logs-und-fehler-lesen.md)) |
| Die Änderung erscheint in der Vorschau der Administrationsoberfläche, aber nicht auf der Website | Ein Cache auf der Seite der Website |
| Die Änderung erscheint nirgends, auch nach dem Leeren aller Caches | Die Änderung wurde nicht dort gespeichert, wo Sie denken: Prüfen Sie Siteaccess und Datei ([Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md)) |

Schreiben Sie jedes Mal in Ihr [Lerntagebuch](ein-lerntagebuch-fuehren.md), wenn ein Cache die Antwort war, und
welcher. Nach einigen Wochen kennen Sie Ihre Caches auswendig.

## Verwandte Seiten

- [Caches](../../anleitungen/caches.md)
- [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md)
- [Logs und Fehler lesen](logs-und-fehler-lesen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
