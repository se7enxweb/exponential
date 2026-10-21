---
title: Funktionale Anforderungen an ein CMS für den öffentlichen Sektor
audience: government, project-manager, editor, admin
category: public-sector
order: 30
summary: Ein Katalog funktionaler Anforderungen, die eine öffentliche Stelle üblicherweise an ein Web-Content-Management-System stellt - Inhaltsmodell, Bearbeitung, Freigabe, Versionen, Berechtigungen, Suche, Formulare, Feeds, Multisite -, jede prüfbar formuliert, mit der Dokumentation von Exponential, die sie beantwortet.
keywords: funktionale Anforderungen, CMS, Inhaltsmodell, Bearbeitung, Freigabe-Workflow, Versionen, Rollen, Berechtigungen, Sektionen, Suche, Formulare, RSS, PDF, Multisite, Ausschreibung, Spezifikation, functional requirements, content model, editing, approval workflow, versions, roles, permissions, sections, search, forms, multisite, tender, specification
type: help
---
# Funktionale Anforderungen an ein CMS für den öffentlichen Sektor

Diese Seite richtet sich an Projektleitungen und Beschaffungsteams, die auflisten, was das CMS für Redaktion,
Freigebende und Besucher leisten muss. Jede Anforderung ist so geschrieben, dass ein Bieter sie beantworten und eine
bewertende Person sie prüfen kann. Die letzte Spalte nennt die Seite von Exponential, die die Funktion dokumentiert,
sodass Sie sehen, wie eine durch Nachweise gestützte Antwort aussieht. Passen Sie Formulierungen und Prioritäten an
Ihren eigenen Bedarf an; siehe [Prüfbare Anforderungen schreiben](pruefbare-anforderungen-schreiben.md).

## Inhalt
1. [Inhalte und Bearbeitung](#inhalte-und-bearbeitung)
2. [Freigabe, Versionen und Historie](#freigabe-versionen-und-historie)
3. [Berechtigungen und Organisation](#berechtigungen-und-organisation)
4. [Suche, Navigation und Adressen](#suche-navigation-und-adressen)
5. [Formulare, Feeds und Ausgaben](#formulare-feeds-und-ausgaben)
6. [Mehrere Websites](#mehrere-websites)
7. [Was Sie weglassen oder gesondert erfragen](#was-sie-weglassen-oder-gesondert-erfragen)

## Inhalte und Bearbeitung

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-01 | Inhaltstypen (zum Beispiel Nachricht, Dienstleistungsseite, Veranstaltung, Dokument) können vom Kunden mit eigenen Feldern ohne Programmierung festgelegt werden. | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Klassengruppen](../../../../anleitungen/klassengruppen.md) |
| FUN-02 | Die Redaktion schreibt Rich Text mit Überschriften, Listen, Links, Tabellen und eingebetteten Bildern in einem Editor im Browser. | [Online-Editor TinyMCE 8](../../../../funktionen/6.0/online-editor-tinymce8.md), [Inhalte bearbeiten](../../../inhalte-bearbeiten.md) |
| FUN-03 | Bilder und Dateien werden einmal gespeichert und auf mehreren Seiten wiederverwendet. | [Medien und Uploads](../../../medien-und-uploads.md) |
| FUN-04 | Die Redaktion kann ein Element an mehreren Stellen der Website-Struktur platzieren. | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| FUN-05 | Gelöschte Inhalte kommen in einen Papierkorb, aus dem sie wiederhergestellt werden können, mit einem Nachweis, wer sie entfernt hat. | [Papierkorb: wer und wo](../../../../funktionen/6.0/papierkorb-wer-und-wo.md) |
| FUN-06 | Die Redaktion findet ihre unfertigen Entwürfe und die Elemente, die auf sie warten. | [Meine Entwürfe und meine Warteliste](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md) |

## Freigabe, Versionen und Historie

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-10 | Inhalte können vor der Veröffentlichung die Freigabe durch eine benannte Gruppe erfordern. | [Workflows: Freigaben, Trigger](../../../../anleitungen/workflows.md) |
| FUN-11 | Jede veröffentlichte Änderung wird als Version aufbewahrt; frühere Versionen lassen sich vergleichen und wiederherstellen. | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| FUN-12 | Inhalte können vom Kunden selbst festgelegte Stufen durchlaufen (zum Beispiel Entwurf, rechtliche Prüfung, bereit), mit unterschiedlichen Berechtigungen je Stufe. | [Objektzustände](../../../../anleitungen/objektzustaende.md) |
| FUN-13 | Ein Nachweis zeigt, wer Inhalte wann veröffentlicht, verschoben oder entfernt hat. | [Das Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |

## Berechtigungen und Organisation

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-20 | Zugriff wird über Rollen mit fein abgestuften Richtlinien gewährt (nach Modul, nach Inhaltstyp, nach Teil der Website). | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| FUN-21 | Inhalte lassen sich in Bereiche gruppieren (zum Beispiel je Abteilung), die eigene Berechtigungen tragen. | [Sektionen](../../../../anleitungen/sektionen.md) |
| FUN-22 | Administratoren können Konten finden, die eine Zeit lang nicht benutzt wurden, und sie deaktivieren. | [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md) |
| FUN-23 | Die Administration zeigt der Redaktion nur die Links, die sie verwenden darf. | [Links der Administration folgen den Berechtigungen](../../../../funktionen/6.0/admin-links-folgen-den-berechtigungen.md) |

## Suche, Navigation und Adressen

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-30 | Besucher können die Website durchsuchen; die Suche lässt sich durch eine Enterprise-Suchmaschine ersetzen. | [Suche auf der Website](../../../ersteller/website-ersteller/suche-auf-der-website.md), [Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) |
| FUN-31 | Jede Seite hat eine lesbare, aus ihrem Titel abgeleitete Adresse; alte Adressen funktionieren nach einer Verschiebung weiter. | [Links, URL-Aliase, Platzhalter](../../../../anleitungen/urls-und-aliase.md), [Umleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md) |
| FUN-32 | Inhalte können mit einem kontrollierten Vokabular verschlagwortet werden. | [Verschlagwortung mit eztags](../../../ersteller/website-ersteller/verschlagworten-mit-eztags.md) |
| FUN-33 | Die Website veröffentlicht eine XML-Sitemap und eine robots.txt. | [Sitemaps](../../../ersteller/website-ersteller/sitemaps.md), [robots.txt](../../../../funktionen/6.0/robots-txt.md) |

## Formulare, Feeds und Ausgaben

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-40 | Die Redaktion kann einfache Formulare bauen (Kontakt, Rückmeldung, Umfrage) und die Eingaben in der Administration lesen. | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md), [Formulare mit dem Information Collector](../../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md) |
| FUN-41 | Nachrichten und andere Listen werden als RSS-Feeds veröffentlicht; externe Feeds lassen sich importieren. | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| FUN-42 | Ein Teil der Website lässt sich als ein PDF-Dokument exportieren. | [PDF-Exporte](../../../../anleitungen/pdf-exporte.md) |
| FUN-43 | Abonnenten können Newsletter erhalten, mit Einwilligungsnachweisen und Abmeldung mit einem Klick. | [Newsletter mit cjw_newsletter](../../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md), [E-Mail-Einstellungen](../../../../funktionen/6.0/e-mail-einstellungen.md) |
| FUN-44 | Die Website lässt sich für Wartungsarbeiten mit einer Nachricht für Besucher vom Netz nehmen. | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |

## Mehrere Websites

| Id | Anforderung | Nachweis für Exponential |
|---|---|---|
| FUN-50 | Mehrere Websites (zum Beispiel die Hauptwebsite und Kampagnen- oder Abteilungswebsites) laufen aus einer Installation, mit eigenen Adressen, Designs und Sprachen. | [Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md), [INI-Overrides für mehrere Websites](../../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md) |
| FUN-51 | Die Redaktion kann zwischen diesen Websites verlinken, ohne Adressen fest einzutragen. | [Links zwischen Websites im Multisite-Betrieb](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md) |

## Was Sie weglassen oder gesondert erfragen

- **Funktionen, die die Dokumentation nicht zeigt.** Zeitgesteuertes Veröffentlichen zu einem späteren Datum hat zum
  Beispiel keinen eigenen Leitfaden; der Leitfaden zu Workflows führt unter den Workflow-Ereignissen einen Ereignistyp
  *Bis Datum warten* auf ([Workflows](../../../../anleitungen/workflows.md)). Brauchen Sie zeitgesteuertes Veröffentlichen,
  bitten Sie den Bieter anzugeben, wie es geliefert wird, und es vorzuführen.
- **Karten und geografische Informationen.** Exponential hat keine GIS-Funktionen; ein Kartenfeld aus einer Erweiterung
  stützt sich auf einen Kartendienst eines Dritten. Fragen Sie gesondert danach und behandeln Sie den Dienst als
  Auftragsverarbeiter.
- **Analyse.** Der Kernel liefert keine mit; eine Erweiterung, die einen Analysedienst eines Dritten hinzufügt, braucht
  eine Einwilligung ([Datenschutzstandards](../../../../standards/datenschutz.md)).
- **Barrierefreiheit, Sicherheit, Datenschutz und Sprachen** haben in diesem Ordner eigene Seiten mit Anforderungen.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Anforderungen an Integration und API](anforderungen-an-integration-und-api.md)
- [Anforderungen an Sprache und Mehrsprachigkeit](anforderungen-an-sprache-und-mehrsprachigkeit.md)
- [Was Sie bauen können](../../../ersteller/website-ersteller/was-sie-bauen-koennen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
