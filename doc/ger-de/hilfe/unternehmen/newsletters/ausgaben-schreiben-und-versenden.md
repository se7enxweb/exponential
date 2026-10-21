---
title: Ausgaben schreiben und versenden
audience: businesses, editor, sales-marketing
category: business-solutions
order: 50
summary: Die Routine einer Newsletter-Ausgabe in cjw_newsletter - die Ausgabe als Inhalt schreiben, einen Skin wählen, die Vorschau als Abonnent, Testgruppen, Freigabe, den Versand einplanen, wiederkehrende Versände, A/B-Tests von Betreffzeilen und einen Versand stoppen, der nicht hinausgehen soll.
keywords: Newsletter-Ausgabe, Newsletter schreiben, Testversand, Testgruppe, Freigabe, Newsletter versenden, wiederkehrender Versand, Artikel-Pool, Skin, A/B-Test der Betreffzeile, Versand abbrechen, Archiv, newsletter edition, write newsletter, test send, test group, approval, send newsletter, recurring send, article pool, A/B subject test, abort send, archive
type: help
---
# Ausgaben schreiben und versenden

Diese Seite richtet sich an die Menschen, die jede Ausgabe erstellen: Redakteure, Mitarbeiter im Marketing und die
Führungskraft, die sie abzeichnet. Sie beschreibt die Routine einer Ausgabe vom ersten Entwurf bis zum Archiv, in der
Reihenfolge, in der Sie sie erledigen, und verlinkt auf die technischen Seiten für diejenigen, die die Teile dahinter
einrichten.

## Inhalt

1. [Die Routine auf einen Blick](#die-routine-auf-einen-blick)
2. [Die Ausgabe schreiben](#die-ausgabe-schreiben)
3. [Das Aussehen wählen](#das-aussehen-wählen)
4. [Testen, bevor jemand anderes sie sieht](#testen-bevor-jemand-anderes-sie-sieht)
5. [Freigabe vor dem Versand](#freigabe-vor-dem-versand)
6. [Versenden](#versenden)
7. [Wiederkehrende Versände](#wiederkehrende-versände)
8. [A/B-Tests von Betreffzeilen](#ab-tests-von-betreffzeilen)
9. [Wenn etwas nicht hinausgehen soll](#wenn-etwas-nicht-hinausgehen-soll)

## Die Routine auf einen Blick

| Schritt | Wer | Wo |
|---|---|---|
| 1. Die Ausgabe mit ihren Artikeln anlegen | Autor | Inhaltsbaum, unterhalb der Liste |
| 2. Den Skin wählen | Autor | Versandformular der Ausgabe |
| 3. Vorschau als Abonnent und Versand an eine Testgruppe | Autor | `newsletter/preview_as/...`, Formular für den Testversand |
| 4. Um Freigabe bitten (wenn die Liste es verlangt) | Autor | `newsletter/approval/<edition>/<version>` |
| 5. Freigeben oder ablehnen | Freigebender | Posteingang der Zusammenarbeit oder die Freigabeseite |
| 6. Den Versand einplanen | Versender | **Versenden** an der Ausgabe |
| 7. Den Versand beobachten und das Archiv prüfen | Versender | Dashboard `/newsletter/index`, `newsletter/archive` |

## Die Ausgabe schreiben

Eine Ausgabe (Klasse `cjw_newsletter_edition`) wird unterhalb ihrer Liste angelegt und enthält Artikel
(`cjw_newsletter_article`). „Sie ist ein Inhalt, also hat sie Entwürfe und eine Vorschau“
([Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md#eine-ausgabe-schreiben-und-versenden)).

Release 4.2.0 bringt **Artikel-Pools** und eine Artikelauswahl: Auf der Versandseite einer Ausgabe bietet „Artikel
auswählen“ Inhalte aus dem Pool der Liste an, zum Beispiel die neuesten Artikel Ihres Nachrichtenordners. Für das
automatische Befüllen werden nur Inhalte angeboten, die der anonyme Benutzer lesen darf, „weil ein Newsletter öffentlich
ist“ ([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)).

Gute Praxis für Autoren, aus der Checkliste für Website-Ersteller
([Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md#vor-jedem-versand)):

- [ ] Betreff, Vorschauzeile und Absendername stimmen.
- [ ] Jeder Link funktioniert und zeigt auf die öffentliche Website.
- [ ] Bilder haben Alternativtexte; die Mail liest sich auch ohne Bilder gut.

## Das Aussehen wählen

Das Aussehen einer Mail ist ihr **Skin**. Release 4.2.0 liefert vier (`default`, `company`, `news`, `shop`); die drei
neuen sind Tabellenlayouts mit 600px Breite, jedem Stil inline und ohne Webfonts, „damit sie in Outlook, Gmail und Apple
Mail gleich aussehen, auch mit blockierten Bildern“. Eine Liste kann einschränken, welche Skins verwendet werden dürfen,
und ein Versand kann einen anderen erlaubten Skin wählen
([rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)). Ein Designer kann in Ihrem
Design einen eigenen Skin schreiben (`design:newsletter/skin/<name>`,
[cjw_newsletter, Einstellungen, die Sie verwenden werden](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#einstellungen-die-sie-verwenden-werden)).

## Testen, bevor jemand anderes sie sieht

- **Vorschau als Abonnent** (`newsletter/preview_as/...`) zeigt Betreff, Sprache, Interessen, HTML- und Textteil so, wie
  ein ausgewählter Abonnent sie bekäme. „Nichts wird gesendet oder gespeichert“ (rendering.md).
- **Testgruppen** (Newsletter > Testgruppen) sind benannte Gruppen von Adressen, standardmäßig höchstens 25
  (`MaxTestGroupSize`). Jede Testmail hat einen Betreff, der mit `[Test]` beginnt, und den Header `X-Cjwnl-Test: 1`, und
  standardmäßig bekommt jede Adresse ihre eigene Mail, sodass Tester die Adressen der anderen nicht sehen. Testmails
  werden nie in den Statistiken gezählt
  ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

> **Warnung**
> Testmails verwenden den Vorschau-Transport, `TransportMethodPreview`. In der mitgelieferten `cjw_newsletter.ini` ist
> das `sendmail`, mit dem Kommentar „the extension default really sends“ (der Standard der Erweiterung sendet wirklich).
> Setzen Sie ihn während einer Probe ebenfalls auf `file`, sonst verlassen Testversände den Server
> ([Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md)).

## Freigabe vor dem Versand

Die Freigabe wird pro Liste eingeschaltet („Approval: yes“). Dann gilt
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)):

1. Der Autor bittet unter `newsletter/approval/<edition>/<version>` um Freigabe, mit einer Nachricht.
2. Die Anfrage erscheint im **Posteingang der Zusammenarbeit** der Freigebenden: der Benutzer aus `[ApprovalSettings]
   ApproverUserIds[]` und der Mitglieder der Gruppen `ApproverGroupIds[]` (standardmäßig 12, der Administrator-Benutzer
   einer Standardinstallation), nie der Person, die gefragt hat.
3. Ein Benutzer mit der Richtlinie `newsletter/approve` gibt sie frei oder lehnt sie ab, mit einem Kommentar. Niemand
   entscheidet über seine eigene Anfrage, sofern nicht `AllowSelfApproval=enabled` gesetzt ist.
4. Das Versandformular weist eine nicht freigegebene Version zurück, und die Warteschlange hält jeden Versand einer
   solchen Version an.

Die Freigabe gilt pro **Version**: Ändern Sie die Ausgabe nach der Freigabe, braucht sie erneut eine Freigabe. Genau
darum geht es: Was die Führungskraft freigegeben hat, ist das, was hinausgeht. Zur Freigabe gewöhnlicher Webinhalte
siehe [Workflows](../../../anleitungen/workflows.md).

## Versenden

1. **Versenden** plant die Ausgabe ein ([Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md#wie-ein-versand-abläuft)).
2. Der Cron-Teil `cjw_newsletter_mailqueue_create` baut die Warteschlange auf: ein Eintrag pro Abonnent.
3. `cjw_newsletter_mailqueue_process` sendet, was wartet.
4. Die Ausgabe wird archiviert (`newsletter/archive`).

Seit 4.1.17 startet **Jetzt senden** im Dashboard die Arbeit im Hintergrund und zeigt ihren Fortschritt, „sodass ein
langsamer Mail-Server die Anfrage nicht mehr aufhält“
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#dashboard-befehle-und-hintergrundläufe-4117)).

> **Tipp**
> Wählen Sie einen Versandzeitpunkt, der zu Ihren Lesern passt, und versenden Sie nicht kurz bevor Sie das Büro
> verlassen: Wenn etwas nicht stimmt, wollen Sie da sein, um es zu stoppen.

## Wiederkehrende Versände

Wiederkehrende Versände (Newsletter > Wiederkehrende Versände) laufen an gewählten Wochentagen, wöchentlich oder
monatlich, zu einer Tageszeit in einer Zeitzone. Im Modus **neueste** versendet ein Zeitplan die neueste Ausgabe der
Liste, die noch nie versendet wurde; im Modus **Kopie** kopiert er eine Vorlage-Ausgabe und kann sie aus dem
Artikel-Pool mit dem füllen, was seit dem letzten Versand veröffentlicht wurde. Mit **„skip if empty“** (der Standard)
wird nichts versendet, wenn es nichts Neues gibt. **Probieren** sagt, was ein Lauf jetzt tun würde, und ändert nichts.
Läufe, die verpasst wurden, während Cron ausgefallen war, werden nicht nachgeholt
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)).

Verlangt die Liste eine Freigabe, wartet ein geplanter Versand darauf wie jeder andere.

## A/B-Tests von Betreffzeilen

Das Versandformular kann zwei oder mehr Betreffzeilen an einer Stichprobe der Liste testen (standardmäßig 10 Prozent pro
Variante), warten (standardmäßig 4 Stunden) und den Rest mit dem Gewinner versenden. Ein Test braucht eingeschaltetes
Tracking ([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)), er steht also nur
zur Verfügung, wenn Sie sich für Statistiken entschieden haben
([Opt-in und Einwilligung](opt-in-und-einwilligung.md#statistiken-nur-mit-einwilligung)).

## Wenn etwas nicht hinausgehen soll

| Situation | Tun Sie Folgendes |
|---|---|
| Die falsche Ausgabe ist eingeplant | Brechen Sie sie mit `newsletter/send_abort` ab, bevor Cron die Warteschlange verarbeitet |
| Ein Versand läuft und muss stoppen | Brechen Sie ihn ab; Mails, die schon an den Mail-Server übergeben wurden, lassen sich nicht zurückholen |
| Der gesamte Versand muss pausieren | Pausieren Sie den Transport unter Newsletter > Mengengrenzen und Pakete (gilt auch, wenn die Drosselung aus ist) |
| Nichts wird gesendet | Prüfen Sie, ob Cron für den richtigen Siteaccess läuft und der Transport nicht `file` ist |

Quellen: [Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md#wenn-etwas-schiefgeht)
und [deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md).

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md)
- [Bounces und Listenpflege](bounces-und-listenpflege.md)
- [Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
