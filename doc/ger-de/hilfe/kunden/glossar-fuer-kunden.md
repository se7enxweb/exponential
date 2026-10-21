---
title: Glossar für Kunden
audience: customer, businesses, editor
category: customer-success
order: 4
summary: Die Begriffe, die einem Website-Betreiber beim Betrieb einer Website mit Exponential begegnen, in einfacher Sprache erklärt: von Administration, Freigabe und Prüfprotokoll bis Version, USt-Typ und Workflow.
keywords: Glossar, Begriffe, Wörter, Definitionen, Website-Betreiber, Administration, Siteaccess, Inhaltsklasse, Knoten, Version, Entwurf, Workflow, Rolle, Richtlinie, Sektion, Cache, Upgrade, Mehrwertsteuer, Bestellstatus, glossary, terms, definitions, site owner, content class, node, draft, role, policy, section, VAT, order status
type: help
---
# Glossar für Kunden

Die Begriffe, die Sie von Ihrem Team, Ihrem Partner und Ihrem Hoster hören werden, in einfacher Sprache. Jeder Eintrag
verweist auf eine Seite, die mehr erklärt. Die Einträge sind nach dem englischen Begriff geordnet, der in Klammern
steht. Das vollständige Glossar der Dokumentation steht in `doc/eng-us/glossary.md`.

## A bis C

**Administration** (*Administration*): der Teil der Website, in dem Ihr Team arbeitet, meist unter `/admin` nach der
Adresse Ihrer Website. Siehe [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md).

**Freigabe** (*Approval*): ein Schritt, der eine neue Version zurückhält, bis jemand mit dem Recht zur Freigabe sie
annimmt. Es ist ein Workflow-Ereignis namens *Approve* ([Workflows](../../anleitungen/workflows.md), [Freigabe und Prüfung](alltag/freigabe-und-pruefung.md)).

**Prüfprotokoll** (*Audit trail*): der manipulationssichere Nachweis, wer in der Administration was getan hat, mit
eigenem Reiter **Audit** ([Prüfprotokoll](../../funktionen/6.0/pruefprotokoll.md)).

**Backup** (*Backup*): eine Kopie der Datenbank und der gespeicherten Dateien, aus der die Website wiederhergestellt
werden kann. Nur eine Wiederherstellung beweist ein Backup ([Backups](gesund-halten/backups.md)).

**Cache** (*Cache*): gespeicherte Ergebnisse, die Seiten schnell machen. Nach manchen Änderungen wird der Cache geleert,
damit Besucher die neue Fassung sehen; das erledigt Ihr Administrator ([Geschwindigkeit und Caching](gesund-halten/geschwindigkeit-und-caching.md)).

**Inhaltsklasse** (*Content class*): die Art eines Inhaltselements (Article, Product, Folder ...) und die Felder, die es
hat. Die Klassen Ihrer Website wurden beim Bau ausgewählt ([Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)).

**Inhaltselement** (*Content item*, auch *Objekt*): ein einzelner Inhalt, etwa ein Artikel oder ein Produkt, mit seinen
Versionen und Übersetzungen.

**Inhaltsbaum** (*Content tree*): die Hierarchie, in der Elemente platziert sind; ein Element kann Plätze in mehreren
Zweigen haben.

## D bis M

**Entwurf** (*Draft*): eine Version in Arbeit, die die Öffentlichkeit nicht sieht ([Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md)).

**Exponential Intelligence**: der in die Administration eingebaute Schreibassistent; er schreibt nur Entwürfe und sendet
nichts, bis ein Schlüssel eines Anbieters hinzugefügt wird (`README.md`) ([Exponential Intelligence für Betreiber](wachstum/exponential-intelligence.md)).

**Exponential Layouts**: der visuelle Page Builder mit Layouts, Zonen und Blöcken ([Der Layouts-Editor](../layout-editor.md)).

**Exponential Velocity**: ein eigener Anwendungsserver von Exponential, neben Apache, Nginx und den anderen
(`README.md`, „How Exponential is served“).

**Erweiterung** (*Extension*): ein Paket, das der Installation Funktionen, Designs oder Einstellungen hinzufügt. Der
meiste für Ihre Website geschriebene Code liegt in Erweiterungen ([Was Ihnen gehört](was-ihnen-gehoert.md)).

**Hosting-Anbieter, Hoster** (*Hosting company, host*): der Anbieter, der den Server, die Datenbank und meist die Backups betreibt.

**Implementierungspartner, Partner** (*Implementation partner*): das Unternehmen, das Code und Design Ihrer Website
gebaut hat oder pflegt.

**Wartungsmodus** (*Maintenance mode*): ein Zustand, in dem Besucher eine Wartungsseite sehen, während gearbeitet wird;
die Administration kann erreichbar bleiben (**Setup > Wartung**, [Wartungsmodus](../../funktionen/6.0/wartungsmodus.md)).

## N bis S

**Knoten** (*Node*, auch *Ort*): ein Platz eines Inhaltselements im Baum, mit eigener Nummer.

**Objektzustand** (*Object state*): eine Kennzeichnung an einem Inhaltselement (etwa Draft, Review, Released), anhand
derer Rollen entscheiden können, wer was tun darf ([Objektzustände](../../anleitungen/objektzustaende.md)).

**Bestellstatus** (*Order status*): die Stufe einer Bestellung im Shop, vom Eingang bis zur Zustellung ([Bestellstatus](handel/bestellstatus.md)).

**Zahlungsanbieter, Payment-Gateway** (*Payment gateway*): die Verbindung zwischen dem Shop und einem Zahlungsanbieter
([Zahlungsanbieter](handel/zahlungsanbieter.md)).

**Richtlinie** (*Policy*): eine einzelne Berechtigung innerhalb einer Rolle, etwa „darf Inhalte in dieser Sektion
bearbeiten“ ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).

**Release** (*Release*): eine veröffentlichte Version von Exponential, etwa 6.0.14. Ein veröffentlichtes Release ändert
sich nie (`SECURITY.md`).

**REST-API** (*REST API*): die Schnittstelle, über die Apps und andere Systeme Inhalte lesen und schreiben ([Die REST-API für Betreiber](wachstum/rest-api-fuer-betreiber.md)).

**Rolle** (*Role*): eine Menge von Richtlinien, die Benutzern oder Benutzergruppen gegeben wird ([Rollen für Ihr Team](alltag/rollen-fuer-ihr-team.md)).

**Sektion** (*Section*): eine Kennzeichnung an einem Teil des Inhaltsbaums, mit der Rollen trennen, was Menschen sehen
oder bearbeiten dürfen ([Sektionen](../../anleitungen/sektionen.md)).

**Siteaccess** (*Siteaccess*): ein Zugang zur Installation (eine öffentliche Website, eine Sprache, die Administration),
zugeordnet über Adresse oder Host. Mehrere Websites einer Installation sind mehrere Siteaccesses.

**SLA (Service Level Agreement)**: der Teil eines Vertrags, der sagt, was ein Anbieter verspricht und wie schnell
([Verträge und SLAs](partner-und-hoster/vertraege-und-slas.md)).

## T bis Z

**Übersetzung** (*Translation*): ein Inhaltselement in einer anderen Sprache; jedes Element kann mehrere haben ([Sprachen](../sprachen.md)).

**Upgrade** (*Upgrade*): die Installation auf ein neueres Release von Exponential bringen ([Wie Updates funktionieren](gesund-halten/wie-updates-funktionieren.md)).

**Upgrade-Prüfung** (*Upgrade check*): **Setup > Upgrade-Prüfung**, die die installierten Dateien und die Datenbank mit
dem vergleicht, was das Release erwartet ([Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)).

**URL-Alias** (*URL alias*): die lesbare Adresse einer Seite, etwa `/news/our-new-shop` ([URLs und Aliase](../../anleitungen/urls-und-aliase.md)).

**USt-Typ und USt-Regel** (*VAT type and VAT rule*): die Steuersätze und die Regeln, die für ein Produkt und ein Land im
Shop einen davon auswählen ([USt-Typen und USt-Regeln](handel/ust-typen-und-ust-regeln.md)).

**Version** (*Version*): ein gespeicherter Stand eines Inhaltselements. Das Veröffentlichen erzeugt eine neue Version und
bewahrt die früheren.

**Workflow** (*Workflow*): eine Liste von Schritten (Ereignissen), die abläuft, wenn etwas geschieht, etwa vor dem
Veröffentlichen ([Workflows](../../anleitungen/workflows.md)).

## Verwandte Seiten

- [FAQ für Kunden](faq-fuer-kunden.md)
- [Glossar für Hoster](../hosting/glossar-fuer-hoster.md)
- [Glossar für Unternehmen](../unternehmen/glossar-fuer-unternehmen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
