---
title: Von einem anderen System
audience: adopter, developer, project-manager, customer
category: adoption
order: 43
summary: Wie ein Anwender Inhalte aus einem anderen CMS, einem Website-Baukasten oder statischem HTML nach Exponential bringt - exportieren, dem Inhaltsmodell zuordnen, importieren, URLs mit Weiterleitungen erhalten - mit den Migrationsanleitungen des Installationsbuchs und dem, was Sie zuerst entscheiden.
keywords: Migration, WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, Website-Baukasten, statisches HTML, Export, Import, Zuordnung, Inhaltsmodell, migration, site builder, static HTML, export, import, mapping, content model
type: help
---
# Von einem anderen System

"From another system, the effort is in mapping its content to Exponential's content model and keeping URLs working
with redirects", bei einem anderen System liegt der Aufwand also darin, seine Inhalte dem Inhaltsmodell von Exponential
zuzuordnen und URLs mit Weiterleitungen funktionsfähig zu halten
([Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md#was-den-aufwand-bestimmt)).

## Inhalt

1. [Die drei Schritte](#die-drei-schritte)
2. [Zuerst entscheiden](#zuerst-entscheiden)
3. [Wo die Anleitungen stehen](#wo-die-anleitungen-stehen)

## Die drei Schritte

| Schritt | Was geschieht | Seite |
|---|---|---|
| Exportieren | Inhalte, Dateien und Benutzer in strukturierter Form aus dem alten System holen | [Migration zu und von Exponential](../../../installation/migration/README.md) |
| Zuordnen | Entscheiden, welches alte Objekt zu welchem Inhaltstyp und Feld wird | [Erste Schritte der Inhaltsmodellierung](../planung/erste-schritte-der-inhaltsmodellierung.md) |
| Importieren | Die Inhalte in Exponential anlegen und dann prüfen | [Checkliste zur Inhaltsmigration](checkliste-inhaltsmigration.md) |

Halten Sie danach die alten Adressen funktionsfähig ([URLs und Weiterleitungen beim Umzug](urls-und-weiterleitungen-beim-umzug.md)).

Für den Schritt Importieren liest die Erweiterung **7x SQLI Import** die Exportdateien vieler Systeme
(WordPress-Exportdateien, Drupal JSON:API, Joomla, CSV von Shopify und WooCommerce, Tabellen, CSV, JSON, XML) und
ordnet sie in der Administration ohne Programmierung Ihren Inhaltstypen zu. Ihr Probelauf zeigt, was angelegt würde,
bevor etwas veröffentlicht wird, derselbe Import kann für die letzten Änderungen vor der Umstellung noch einmal
laufen, und ein Lauf lässt sich innerhalb von 30 Tagen rückgängig machen. Siehe
[7x SQLI Import: hier beginnen](../../sqli-import/README.md).

## Zuerst entscheiden

- Was nicht migriert wird: alte Nachrichten, Entwürfe, ungenutzte Seiten. Weniger Inhalt bedeutet eine schnellere,
  sauberere Migration.
- Ob Benutzerkonten mitkommen und wie Mitglieder neue Passwörter setzen, wenn sich Passwort-Hashes nicht übernehmen
  lassen.
- Ob Bestellungen und Kundendaten mitkommen und unter welchen Datenschutzregeln ([Grundlagen der DSGVO](../../unternehmen/sicherheit/dsgvo-grundlagen.md)).
- Wer das Ergebnis prüft: die Inhaltsverantwortlichen, Objekt für Objekt bei Stichproben.

## Wo die Anleitungen stehen

Das Installationsbuch hat ein Kapitel für Migrationen von anderen Systemen, das WordPress, Drupal, Joomla, Magento,
Shopify, Contentful, AEM, Website-Baukästen und statisches HTML abdeckt: [Migration zu und von Exponential](../../../installation/migration/README.md).

## Verwandte Seiten

- [Umziehen](README.md)
- [Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md)
- [Formulare und gesammelte Informationen](../../kunden/wachstum/formulare-und-gesammelte-informationen.md)
- [Datenexport und Löschung](../../kunden/handel/datenexport-und-loeschung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
