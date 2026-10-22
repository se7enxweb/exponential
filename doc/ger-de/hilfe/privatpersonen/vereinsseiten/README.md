---
title: Vereinsseiten
audience: individual, website-creator, customer
category: personal-sites
order: 1710
summary: Der Bereich für Menschen, die die Website eines Vereins, eines Teams oder einer Hobbygruppe mit Exponential betreiben, meist ehrenamtlich - Mitglieder und Registrierung, ein Mitgliederbereich, Newsletter mit cjw_newsletter, Veranstaltungen, Formulare, Umfragen und Foren, die Arbeit teilen und was nicht eingebaut ist (Beiträge, Tickets).
keywords: Vereinsseite, Verein, Team, Hobbygruppe, Gesellschaft, Ehrenamtliche, Mitglieder, Registrierung, Mitgliederbereich, Newsletter, cjw_newsletter, Veranstaltungen, Kalender, Formulare, Umfragen, Foren, Anmeldung, Mitgliedsbeiträge, Tickets, club site, volunteers, members area, dues
type: help
---
# Vereinsseiten

Dieser Bereich ist für Privatpersonen, die die Website eines Vereins, einer Sportmannschaft, eines Chors, eines
Lesekreises oder einer anderen Hobbygruppe betreuen, oft ehrenamtlich und oft allein. Eine Vereinsseite ist nach
außen öffentlich (wer wir sind, wann wir uns treffen, wie man beitritt) und hat eine Seite für Mitglieder
(Neuigkeiten, Dokumente, Anmeldungen, ein Newsletter).

Ein eingetragener gemeinnütziger Verein oder eine größere Organisation mit Angestellten findet mehr unter
[Gemeinnützige Organisationen](../../../../eng-us/help/non-profits/non-profits-start-here.md) und unter
[Kleine Gruppen und Vereine](../../unternehmen/vereine/README.md).

## Inhalt

1. [Was eine Vereinsseite meist enthält](#was-eine-vereinsseite-meist-enthält)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Was für Vereine mitgeliefert wird](#was-für-vereine-mitgeliefert-wird)
4. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)

## Was eine Vereinsseite meist enthält

| Teil | Öffentlich oder Mitglieder | Typische Klasse |
|---|---|---|
| Über den Verein, wie man beitritt, Kontakt | Öffentlich | *Article*, *Feedback form* |
| Neuigkeiten | Öffentlich, manche nur für Mitglieder | *Blog* oder *Folder* mit *Article* |
| Veranstaltungen und Treffen | Öffentlicher Kalender, Einzelheiten für Mitglieder | *Event calendar* mit *Event* |
| Fotos | Öffentlich oder Mitglieder | *Gallery* |
| Dokumente (Protokolle, Satzung, Formulare) | Mitglieder | *File* in Ordnern |
| Anmeldungen und Umfragen | Mitglieder | *Feedback form*, *Poll* |
| Diskussion | Mitglieder | *Forums*, *Forum*, *Forum topic*, *Forum reply* |
| Newsletter | Abonnenten | `cjw_newsletter` |

## Die Seiten dieses Bereichs

| Seite | Lesen Sie sie, wenn |
|---|---|
| [Eine Vereinsseite planen](eine-vereinsseite-planen.md) | Bevor Sie bauen, am besten mit dem Vorstand |
| [Mitglieder und Registrierung](mitglieder-und-registrierung.md) | Mitglieder sich registrieren und anmelden sollen |
| [Vereinsnewsletter](vereinsnewsletter.md) | Sie Neuigkeiten an Mitglieder und Freunde mailen möchten |
| [Veranstaltungen und Kalender für Vereine](veranstaltungen-und-kalender-fuer-vereine.md) | Sie Treffen, Spiele oder Konzerte veröffentlichen |
| [Formulare, Umfragen und Anmeldungen](formulare-umfragen-und-anmeldungen.md) | Sie Anmeldungen, Antworten und Stimmen sammeln |
| [Die Arbeit mit Ehrenamtlichen teilen](die-arbeit-mit-ehrenamtlichen-teilen.md) | Sie nicht die einzige Person sein möchten, die die Website aktualisieren kann |

## Was für Vereine mitgeliefert wird

| Bedarf | Was es trägt | Quelle |
|---|---|---|
| Selbstregistrierung mit Aktivierung per E-Mail | `user/register`, `VerifyUserType=email` | Kernel; [Mitgliederregistrierung und -bereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md) |
| Eine Gruppe und Rolle Members, eine Sektion Restricted | Vom Installer des Standardpakets angelegt | `sevenx-multi-site-installer.php` |
| Veranstaltungen und Kalender | Klassen *Event*, *Event calendar*, *Multicalendar* | Pakete `sevenx_classes`, `ezwebin` |
| Formulare und Umfragen | Informationssammler; Klassen *Feedback form*, *Poll* | Kernel |
| Foren | Klassen *Forums*, *Forum*, *Forum topic*, *Forum reply* | `sevenx_classes` |
| Newsletter | `cjw_newsletter` | `composer.json` im Wurzelverzeichnis |
| Karten für einen Veranstaltungsort | `ezgmaplocation` | `composer.json` im Wurzelverzeichnis |
| Spamschutz | `ezformtoken`, `recaptcha`, `hcaptcha` | `composer.json` im Wurzelverzeichnis |

## Was nicht eingebaut ist

- **Mitgliedsbeiträge und deren Einzug.** Der Shop kann mit einem Zahlungsanbieter Artikel verkaufen, aber es gibt
  kein Modul für Mitgliedsbeiträge oder Verlängerungen.
- **Ticketverkauf** und kostenpflichtige Veranstaltungsanmeldung.
- **Private Nachrichten zwischen Mitgliedern**: `ezpm` gibt es, es wird in `composer.json` aber nur vorgeschlagen und
  nicht standardmäßig installiert.
- **Kalenderexport** auf Smartphones.
- **Die E-Mail-Zustellung selbst**: Newsletter und Registrierungs-E-Mails brauchen einen funktionierenden Mailserver.

## Verwandte Seiten

- [Privatpersonen: hier beginnen](../privatpersonen-hier-beginnen.md)
- [Mitgliederwebsites (Unternehmen)](../../unternehmen/mitglieder/README.md)
- [Kleine Gruppen und Vereine](../../unternehmen/vereine/README.md)
- [Gemeinnützige Organisationen: hier beginnen](../../../../eng-us/help/non-profits/non-profits-start-here.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
