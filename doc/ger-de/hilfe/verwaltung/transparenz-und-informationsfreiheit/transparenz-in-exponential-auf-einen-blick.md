---
title: Transparenz in Exponential auf einen Blick
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: Ein Überblick über die Funktionen von Exponential, die eine öffentliche Stelle für Transparenz und Informationsfreiheit nutzt (Veröffentlichen, Antragsformulare, Freigabe, Versionsgeschichte, Audit-Trail, Exporte), mit dem, was jede leistet, und den Lücken, die Sie einplanen sollten.
keywords: Transparenz, Informationsfreiheit, IFG, Informationszugang, proaktive Veröffentlichung, Offenlegungsprotokoll, Audit-Trail, Versionsgeschichte, Rechenschaft, Überblick, transparency, freedom of information, FOI, FOIA, access to information, proactive disclosure, disclosure log, audit trail, version history, accountability, overview
type: help
---
# Transparenz in Exponential auf einen Blick

Diese Seite richtet sich an Führungskräfte, Zuständige für Schriftgut und Administratoren, die eine mit Exponential
gebaute Website nutzen, um Informationen proaktiv zu veröffentlichen und Anträge auf Informationszugang zu
beantworten. Sie ordnet die üblichen Transparenzaufgaben den Produktfunktionen zu und nennt jeweils die Dokumentation
dazu. Die anderen Seiten dieses Bereichs behandeln jede Aufgabe im Einzelnen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Informationen veröffentlichen](#informationen-veröffentlichen)
2. [Anträge entgegennehmen und beantworten](#anträge-entgegennehmen-und-beantworten)
3. [Rechenschaft](#rechenschaft)
4. [Lücken, die Sie einplanen sollten](#lücken-die-sie-einplanen-sollten)

## Informationen veröffentlichen

| Aufgabe | Funktion | Dokumentation |
|---|---|---|
| Ein Veröffentlichungsplan oder eine Seite „Informationen, die wir veröffentlichen“ | Ordner und Seiten im Inhaltsbaum | [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md) |
| Dokumente zum Herunterladen | Dateiobjekte, Downloads über `content/download` geprüft | [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Register (Beschlüsse, Verträge, Ausgaben) | eine eigene Inhaltsklasse pro Register | [Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) |
| Ein PDF eines Bereichs | Setup > PDF-Export | [PDF-Exporte](../../../anleitungen/pdf-exporte.md) |
| Zu einem festen Zeitpunkt veröffentlichen | Workflow-Ereignis *Bis Datum warten* | [Workflows](../../../anleitungen/workflows.md) |
| Prüfung vor dem Veröffentlichen | Workflow-Ereignis *Freigeben* | [Workflows](../../../anleitungen/workflows.md) |
| Feeds neuer Veröffentlichungen | Setup > RSS | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| Massenlisten als CSV, JSON, XML | Export mit `xrowextract` | [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md) |

## Anträge entgegennehmen und beantworten

| Aufgabe | Funktion | Dokumentation |
|---|---|---|
| Ein Antragsformular | Klasse und Objekt mit Informationssammler | [Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Das Team über einen Antrag benachrichtigen | E-Mail an einen Empfänger pro Formular | dieselbe Seite, „Mail to a receiver“ |
| Anträge lesen und exportieren | Setup > Gesammelte Informationen, CSV-Export | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Spamschutz | Formular-Token, optionale CAPTCHA-Erweiterungen | [Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md) |
| Eine Auskunft veröffentlichen | eine Seite mit den geschwärzten Dokumenten | siehe „Eine Auskunft nach Informationsfreiheitsrecht veröffentlichen“ |

## Rechenschaft

| Frage | Funktion | Dokumentation |
|---|---|---|
| Was stand letzten Monat auf der Seite? | Versionen, **Zwei Versionen vergleichen** | [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md) |
| Wer hat es geändert oder entfernt? | Audit-Trail, standardmäßig eingeschaltet, mit Hash-Kette | [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Wer darf was ändern? | Rollen, Sektionen, Objektzustände | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Wurde die Aufzeichnung verändert? | `./console exp:audit verify` | [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes) |

## Lücken, die Sie einplanen sollten

- **Keine Vorgangsverwaltung.** Exponential speichert Formulareinsendungen; es hat keine Verfolgung von Anträgen mit
  Fristen, Zuweisungen, Uhren oder Erinnerungen. Verwenden Sie ein Vorgangssystem oder ein Register, das Sie als Inhalt
  führen.
- **Kein Werkzeug zum Schwärzen.** Das Schwärzen von Dokumenten ist ein manueller redaktioneller Schritt in dem
  Programm, mit dem das Dokument erstellt wurde; Exponential veröffentlicht die Datei, die Sie hochladen (siehe
  „Schwärzen ist Handarbeit“).
- **Keine Zertifizierung für die Schriftgutverwaltung.** Versionen und der Audit-Trail helfen; sie machen die Website
  nicht zu einem Schriftgutsystem im Sinne von ISO 15489-1
  ([ISO 15489-1:2016](https://www.iso.org/standard/62542.html)).
- **Versionsgrenzen.** Alte Versionen werden jenseits der Grenzen in `content.ini [VersionManagement]` entfernt
  (siehe „Versionen länger aufbewahren“).

## Verwandte Seiten

- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)
- [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
