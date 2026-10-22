---
title: Hosting-Wiederverkäufer
audience: reseller, hosting, partner
category: reselling
order: 70
summary: Wie ein Hosting-Unternehmen oder ein Wiederverkäufer von Hosting Tarife für Exponential-Hosting aufbaut und verkauft - was der Tarif technisch enthalten muss, welche Engine und Datenbank zu welchem Tarif passen, was Sie versprechen und was Sie dem Kunden überlassen, Lizenzierung für Hoster und wo der Hosting-Bereich der Hilfe übernimmt.
keywords: Hosting-Wiederverkäufer, Hosting-Tarif, Shared Hosting, VPS, Managed Hosting, Velocity, PHP-FPM, SQLite, PostgreSQL, Isolation, Datensicherungen, Tarifgestaltung, Reseller-Hosting, White-Label-Hosting, hosting reseller, hosting plan, shared hosting, managed hosting, isolation, backups, plan design, reseller hosting, white-label hosting
type: help
---
# Hosting-Wiederverkäufer

Diese Seite ist für Hosting-Unternehmen und für Agenturen, die Hosting-Kapazität weiterverkaufen, und die Tarife für
Exponential-Websites verkaufen möchten. Der Hosting-Bereich der Hilfe behandelt die technische Seite ausführlich
([Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md)); diese Seite ist die Sicht des
Wiederverkäufers: was Sie verkaufen, was es Sie kostet und was Sie versprechen dürfen.

## Inhalt

1. [Drei Wege, Hosting weiterzuverkaufen](#drei-wege-hosting-weiterzuverkaufen)
2. [Was ein Tarif enthalten muss](#was-ein-tarif-enthalten-muss)
3. [Technische Entscheidungen, die den Preis prägen](#technische-entscheidungen-die-den-preis-prägen)
4. [Was Sie versprechen dürfen und was nicht](#was-sie-versprechen-dürfen-und-was-nicht)
5. [Lizenzierung für einen Hosting-Wiederverkäufer](#lizenzierung-für-einen-hosting-wiederverkäufer)
6. [Checkliste](#checkliste)

## Drei Wege, Hosting weiterzuverkaufen

| Weg | Ihnen gehört | Ihr Risiko | Hinweise |
|---|---|---|---|
| Eigene Infrastruktur | Server oder Cloud-Konten | Am höchsten: Sie betreiben alles | Volle Kontrolle über Preis und Qualität |
| Kapazität eines Hosting-Anbieters unter Ihrer Marke weiterverkaufen | Die Kundenbeziehung | Mittel: Sie hängen von der Qualität des Anbieters ab | Prüfen Sie, dass die Server des Anbieters die unten genannten Anforderungen erfüllen |
| White-Label-Hosting von 7x | Die Kundenbeziehung | Mittel | „White-Label 7x Services“ ab Professional; Bedingungen nicht veröffentlicht, fragen Sie das Partnerteam ([White-Label-Wiederverkäufer](white-label-wiederverkaeufer.md)) |

## Was ein Tarif enthalten muss

Der Hosting-Bereich nennt die Bausteine jedes Tarifs ([Hosting-Tarife gestalten](../../hosting/geschaeft/hosting-tarife-gestalten.md)):

| Baustein | Entscheiden Sie |
|---|---|
| Engine | Exponential Velocity oder Apache bzw. nginx mit PHP-FPM |
| PHP | Welche Versionen im unterstützten Bereich 8.0 bis 8.5, und wer wählt |
| Datenbank | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL (die offizielle Reihenfolge) |
| Speicher | Ein Kontingent für `var/storage`, Caches und Logs |
| Isolation | Ein Systembenutzer und ein PHP-FPM-Pool je Website, ein VPS, ein Container oder eine dedizierte Maschine |
| Datensicherungen | Datenbank plus `var/storage`, `settings/override` und `settings/siteaccess`, wie oft und wie lange |
| Updates | Der Kunde, der Hoster auf Anfrage oder der Hoster nach Plan |
| Support | Was Sie beantworten, wie schnell ([Support-Stufen, die Sie anbieten](../angebote/support-stufen-die-sie-anbieten.md)) |

Der Mindestserver steht unter [Hosting-Anforderungen für Ihre Angebote](../angebote/hosting-anforderungen-fuer-angebote.md).

## Technische Entscheidungen, die den Preis prägen

| Entscheidung | Günstiger | Teurer |
|---|---|---|
| Datenbank | SQLite 3, die Voreinstellung: „a full production database for a site served from one machine“ (eine vollwertige Produktivdatenbank für eine Website, die von einer Maschine ausgeliefert wird), kein Datenbankserver zu betreiben | Ein Datenbankserver (zuerst PostgreSQL) für Tarife mit mehreren Web-Knoten |
| Engine | Apache oder nginx mit einem PHP-FPM-Pool je Kunde, auf gemeinsam genutzten Maschinen | Velocity mit eigenem Worker-Pool je Website, auf VPS-, dedizierten oder Managed-Tarifen |
| Isolation | Pools auf einer gemeinsam genutzten Maschine | Ein VPS oder Container je Kunde ([Mandantenisolation](../../hosting/sicherheit/trennung-von-mandanten.md)) |
| Umgebungen | Nur Produktion | Produktion plus Staging |

Quellen: [Hosting-Tarife gestalten](../../hosting/geschaeft/hosting-tarife-gestalten.md),
[Datenbankunterstützung](../../datenbankunterstuetzung.md). Messen Sie, bevor Sie Grenzen festlegen
([Benchmarks einer gehosteten Website](../../hosting/leistung/benchmark-einer-gehosteten-website.md)).

## Was Sie versprechen dürfen und was nicht

| Sie dürfen versprechen | Nur wenn Sie es selbst liefern |
|---|---|
| Verfügbarkeit Ihrer Server | Verfügbarkeitswerte brauchen Ihre eigene Überwachung ([Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)) |
| Datensicherungen und Wiederherstellungstests | Eine Wiederherstellung, die tatsächlich getestet wurde ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)) |
| Einspielen von Exponential-Updates | Ein Verfahren, das sie einspielt ([Kunden-Websites aktualisieren](../../hosting/betrieb/kunden-websites-aktualisieren.md)) |
| Sicherheitskorrekturen innerhalb einer festgelegten Zeit | Ihr eigenes Personal und Ihr eigener Zeitplan; das Projekt veröffentlicht kein SLA, nur „a first answer within one business day“ (eine erste Antwort innerhalb eines Werktags) auf vertrauliche Sicherheitsmeldungen (`SECURITY.md`) |

Versprechen Sie nie, dass das Projekt oder 7x das Problem eines Kunden innerhalb einer bestimmten Zeit behebt: Die
Reaktionszeit Ihres Partnertarifs ist das, was 7x Ihnen verspricht ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)).

## Lizenzierung für einen Hosting-Wiederverkäufer

Exponential für Kunden zu betreiben ist nicht eingeschränkt, braucht keine Gebühr und keine Erlaubnis. Images,
Vorlagen und Ein-Klick-Installer, die Kunden Kopien übergeben, bringen die Pflichten bei der Weitergabe mit sich
([Lizenzierung für Hoster](../../hosting/geschaeft/lizenzierung-fuer-hoster.md),
[Checkliste der Pflichten bei der Weitergabe](../verantwortlichkeiten/checkliste-der-pflichten-bei-der-weitergabe.md)).

## Checkliste

- [ ] Tarifblatt für jeden Tarif geschrieben, mit jedem oben genannten Baustein entschieden.
- [ ] Server an den Anforderungen geprüft ([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
- [ ] Datensicherung und Wiederherstellung an einer echten Website in Kundengröße getestet.
- [ ] Support-Stufen und Schweregrade aufgeschrieben ([Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)).
- [ ] Offboarding-Verfahren aufgeschrieben, damit Kunden sauber gehen können ([Kunden-Offboarding](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
- [ ] Marketingtexte auf korrekte Namen geprüft ([Namen und Marke für Wiederverkäufer](../verkauf/namen-und-marke.md)).

## Verwandte Seiten

- [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md)
- [Hosting-Partner](../../partner/hosting-partner.md)
- [Exponential-Hosting vermarkten](../../hosting/geschaeft/exponential-hosting-vermarkten.md)
- [Wiederverkäufer verwalteter Dienste](wiederverkaeufer-verwalteter-dienste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
