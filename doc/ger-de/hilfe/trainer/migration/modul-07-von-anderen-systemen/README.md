---
title: "Modul 7: Von anderen Systemen"
audience: trainer, admin, developer
category: training
order: 70
summary: Das Modul zu Websites aus anderen Systemen - WordPress, Drupal und Joomla sowie statisches HTML -, das die Planungs-, Zuordnungs- und Werkzeugmethoden des Kurses auf echte Quellen anwendet. Drei Lektionen, 2 Stunden 45 Minuten.
keywords: migration zu exponential, statisches html, crawl, weiterleitungen, andere systeme, modul, migrating to exponential, WordPress, Drupal, Joomla, static HTML, WXR, JSON:API, Web Services API, redirects, other systems, module
type: help
---
# Modul 7: Von anderen Systemen

Die meisten Migrationen, die Trainer erleben, kommen von außerhalb der Exponential-Familie. Dieses Modul wendet den
Rahmen der Module 1 bis 3 auf die häufigsten Quellen an: WordPress, Drupal, Joomla und statisches HTML. Die
Dokumentation enthält weitere Anleitungen für Magento, Shopify, Website-Baukästen, Contentful, Adobe Experience Manager
und eigene Anwendungen; Trainer können daraus nach demselben Muster zusätzliche Lektionen erstellen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 2 Stunden 45 Minuten |
| Lektionen | 3 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [7.1 Von WordPress](von-wordpress.md) | 60 Minuten | Eine WordPress-Migration planen: Export, Zuordnung, Importreihenfolge, Weiterleitungen, Delta |
| [7.2 Von Drupal und Joomla](von-drupal-und-joomla.md) | 45 Minuten | WordPress, Drupal und Joomla in einer Tabelle vergleichen; zwei Adressfallen lösen |
| [7.3 Von einer statischen HTML-Website](von-statischem-html.md) | 60 Minuten | Drei Seiten durch Extraktion, Baum, Import und einen weiterleitenden Alias führen |

## Dieses Modul vorbereiten

- [ ] Die Module 2 und 3 zuerst unterrichtet.
- [ ] Für Lektion 7.3: die drei statischen Seiten (`about/index.html`, `about/team.html`, `products/widgets.html`)
      geschrieben und ausgeteilt, jede mit einem Bild; nichts ohne Erlaubnis von einer echten Website kopiert.
- [ ] Jede Person hat einen Kursordner und ein Redakteurs- oder Administratorkonto auf der Schulungsinstallation; die
      Schulungswebsite antwortet für `curl` unter einer bekannten Adresse.
- [ ] Wenn Sie eine echte WXR-Datei zeigen, verwenden Sie eine aus einer WordPress-Testinstallation mit Testbenutzern
      unter `example.invalid`.
- [ ] Prüfen Sie am Ende von Lektion 7.3, dass die Teilnehmenden ihren Test-Alias entfernt haben.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 7.1 | 60 |
| Lektion 7.2 | 45 |
| Pause | 15 |
| Lektion 7.3 | 60 |
| Gesamt | 180 (2 Stunden 45 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursüberblick](../README.md)
- [Migration zu und von Exponential, Abschnitt 4](../../../../installation/migration/README.md#4-migrationen-von-und-zu-anderen-systemen):
  alle Anleitungen für andere Systeme.
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
