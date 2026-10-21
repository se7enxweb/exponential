---
title: Exponential-Hosting vermarkten
audience: hosting, sales-marketing
category: hosting
order: 60
summary: Wie ein Hoster Exponential-Hosting wahrheitsgemäß vermarktet - die Fakten, die Sie nutzen können, mit ihren Quellen, Aussagen, die Sie nicht machen dürfen, Namensregeln, der Inhalt einer Landingpage und eine Checkliste für den Start.
keywords: Marketing, Hosting-Angebot, Landingpage, Aussagen, Fakten, Namen, Marke, Exponential, Start, SEO, Botschaften, Belege, Open Source, GPL, keine Lizenzgebühr, Velocity, marketing, hosting offer, landing page, claims, facts, naming, brand, launch, messaging, proof points, open source, no license fee
type: help
---
# Exponential-Hosting vermarkten

Diese Seite ist für Mitarbeitende im Marketing eines Hosting-Anbieters, der Exponential-Hosting anbietet. Sie
beantwortet: welche Fakten Sie nutzen können und woher sie stammen, was Sie nicht behaupten dürfen, wie Sie Dinge
benennen und was auf die Landingpage Ihres Angebots gehört.

Die allgemeinen Regeln für alle, die öffentlich über Exponential sprechen, stehen im Bereich Vertrieb und Marketing
dieser Hilfe, beginnend bei [Vertrieb und Marketing: hier beginnen](../../geschaeft/vertrieb-und-marketing-hier-beginnen.md).
Diese Seite ergänzt, was für das Hosting spezifisch ist.

## Inhalt

1. [Fakten, die Sie nutzen können](#fakten-die-sie-nutzen-können)
2. [Aussagen, die Sie nicht machen dürfen](#aussagen-die-sie-nicht-machen-dürfen)
3. [Namen](#namen)
4. [Eine Landingpage für Ihr Angebot](#eine-landingpage-für-ihr-angebot)
5. [Botschaften für verschiedene Käufer](#botschaften-für-verschiedene-käufer)
6. [Eine Checkliste für den Start](#eine-checkliste-für-den-start)

## Fakten, die Sie nutzen können

Jeder Fakt hat eine Quelle, die ein Kunde prüfen kann.

| Fakt | Quelle |
|---|---|
| Exponential ist ein professionelles PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität (Content-Management-System) | `README.md` |
| Freie Software unter der GNU GPL v2 oder später; keine Lizenzgebühr | `COPYRIGHT.md`, `README.md` |
| Läuft auf PHP 8.0 bis 8.5 | [Kapitel 2](../../../installation/02-voraussetzungen.md) |
| Sechs unterstützte Datenbanken: SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Läuft auf fast jedem Betriebssystem, das PHP 8 ausführt | [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| Bringt seinen eigenen Webserver und seine eigene Engine mit, Exponential Velocity, mit eigenen HTTPS-Zertifikaten | [Kapitel 8](../../../installation/08-die-website-ausliefern.md), [HTTPS-Zertifikate](../../../../eng-us/features/6.0/velocity-https-certificates.md) |
| Eine durchgesetzte Content Security Policy in der Administration | [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md) |
| Sicherheitsmeldungen erhalten eine erste Antwort innerhalb eines Werktags; veröffentlichte Versionen ändern sich nie | `SECURITY.md` |
| Eine lange Geschichte: führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat | [Wer ist 7x?](../../wer-ist-7x.md) |
| Gepflegt von 7x und der Exponential Foundation | [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md) |

Was Ihr eigenes Angebot hinzufügt (Ihr Rechenzentrum, Ihre Support-Zeiten, Ihre Backups), nennen Sie als Ihre Fakten,
mit Ihren eigenen Belegen.

## Aussagen, die Sie nicht machen dürfen

| Sagen Sie nicht | Warum | Sagen Sie stattdessen |
|---|---|---|
| „Zertifiziertes“ oder „offizielles“ Exponential-Hosting | kein Zertifizierungsprogramm ist dokumentiert | „Exponential-Hosting von <Ihr Unternehmen>“ |
| „Partner von 7x“ oder „der Foundation“, es sei denn, Sie haben eine Vereinbarung | für Sie nicht dokumentiert | beschreiben Sie Ihre tatsächliche Beziehung |
| Eine Zahl von Installationen, Benutzern oder Kunden von Exponential | keine Quelle nennt eine | nennen Sie die öffentlichen Orte: GitHub, Packagist, die Foren |
| Leistungszahlen, die Sie nicht auf Ihrer eigenen Plattform gemessen haben | kein veröffentlichter Benchmark deckt Ihre Hardware ab | Ihre eigene Messung, mit Methode und Datum |
| Compliance-Zertifizierungen für die Software („DSGVO-zertifiziert“) | Software wird durch solche Aussagen nicht zertifiziert | beschreiben Sie die Funktionen, siehe [Compliance und DSGVO](../sicherheit/compliance-und-dsgvo.md) |
| Dass das Projekt mit eZ Systems oder Ibexa verbunden ist | das README sagt das Gegenteil | „Dieses Projekt ist nicht mit der ursprünglichen Software eZ Publish oder ihrem ursprünglichen Entwickler, eZ Systems oder Ibexa, verbunden“ |
| Ein SLA des Projekts | es gibt keines | Ihr eigenes SLA ([SLA-Optionen](sla-optionen-fuer-exponential-hosting.md)) |

## Namen

- Das Produkt heißt **Exponential**; diese Release-Linie ist **Exponential 6**. Der alte Name wird nur als Geschichte
  verwendet: führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt
  hat.
- Die Engine heißt **Exponential Velocity** (kurz: Velocity).
- Das Unternehmen heißt **7x**; die gemeinnützige Organisation ist die **Exponential Foundation**.
- Benennen Sie Ihr Angebot als Ihres: „<Ihr Unternehmen> Exponential Hosting“ ist klar; vermeiden Sie Namen, die
  nahelegen, dass es vom Projekt stammt.

Vollständige Regeln: [Marken- und Namensregeln](../../geschaeft/marken-und-namensregeln.md).

## Eine Landingpage für Ihr Angebot

| Abschnitt | Inhalt |
|---|---|
| Überschrift | was Sie anbieten, für wen, in einem Satz |
| Warum Exponential | drei oder vier Fakten aus der Tabelle oben, mit Links |
| Was enthalten ist | die Bausteine Ihrer Tarife ([Hosting-Tarife gestalten](hosting-tarife-gestalten.md)) |
| Tarife | Ihre Tariftabelle, mit den Grenzen, die Sie gemessen haben |
| Sicherheit | Ihre Härtung, Backups und Update-Richtlinie; verlinken Sie die `SECURITY.md` des Projekts |
| Migration | wie Sie die Website eines Kunden zu sich holen ([Kunden migrieren](../migrationen/kunden-zu-ihrem-hosting-migrieren.md)) |
| Support | Ihre Stufen und Zeiten ([Support-Stufen](support-stufen-fuer-exponential-hosting.md)) |
| Über die Software | ein Link zum Projekt und zu dieser freien Dokumentation |
| Kontakt | wie man Ihren Vertrieb erreicht |

> **Tipp**
> Verlinken Sie die Hilfeseiten, statt sie zu kopieren. Wenn Sie kopieren, dürfen Sie das: Die Dokumentation steht unter
> der GNU FDL 1.3, behalten Sie also den Lizenzhinweis ([Freie Dokumentation](../../freie-dokumentation.md)).

## Botschaften für verschiedene Käufer

| Käufer | Was ihm wichtig ist | Botschaft |
|---|---|---|
| Agentur | viele Websites, schnelle Installationen, Staging | Installationen mit einem Befehl, Ausrollen über Git, eine Staging-Zusatzoption |
| Organisation mit eigenen Redakteuren | Zuverlässigkeit, Backups, Support | getestete Wiederherstellungen, ein SLA, das Sie vorzeigen können, redaktionelle Hilfe im Produkt |
| Entwickler | Shell, Git, Composer, PHP-Versionen | Shell-Zugang, Wahl von PHP 8.x, Velocity oder ein klassischer Stack |
| Öffentlicher Sektor oder regulierte Branchen | Sicherheit und Umgang mit Daten | Härtung, die CSP, Prüfprotokoll, maskierte Anfrage-Logs; Ihr Datenstandort |

## Eine Checkliste für den Start

- [ ] Jeder Fakt auf der Landingpage verlinkt seine Quelle.
- [ ] Keine Aussage aus der Tabelle der Aussagen, die Sie nicht machen dürfen.
- [ ] Das Produkt heißt überall Exponential.
- [ ] Grenzen der Tarife stammen aus Ihren Messungen, mit Datum.
- [ ] Das SLA und die Support-Bedingungen sind von Ihrem rechtlichen Berater geprüft.
- [ ] Ein Testkunde hat Bestellung, Installation und die erste Wiederherstellung aus einem Backup durchlaufen.
- [ ] Die Support-Mitarbeitenden haben die [Runbooks](../runbooks/README.md) gelesen.

## Verwandte Seiten

- [Positionierung und Wertversprechen](../../geschaeft/positionierung-und-nutzenversprechen.md)
- [Die Community und das Ökosystem als Verkaufsargument](../../geschaeft/community-und-oekosystem.md)
- [Partnerprogramme](partnerprogramme-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
