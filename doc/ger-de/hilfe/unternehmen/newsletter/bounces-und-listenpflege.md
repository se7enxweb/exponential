---
title: Bounces und Listenpflege
audience: businesses, admin, sales-marketing
category: business-solutions
order: 60
summary: Warum eine saubere Newsletter-Liste den Ruf eines Unternehmens als Absender schützt und wie cjw_newsletter sie sauber hält - Hard und Soft Bounces, Wiederholungsversuche, der Bounce-Schwellenwert, die Blacklist, die Sperrliste der Website, Beschwerden, der Sperrlisten-Import und eine monatliche Pflegeroutine.
keywords: Bounce, Hard Bounce, Soft Bounce, Beschwerde, Blacklist, Sperrliste, Listenpflege, Absenderreputation, Bounce-Postfach, Wiederholungsversuche, bounce, hard bounce, soft bounce, complaint, blacklist, suppression list, list hygiene, sender reputation, bounce mailbox, BounceThresholdValue, retries
type: help
---
# Bounces und Listenpflege

Diese Seite richtet sich an alle, die für die Ergebnisse eines Newsletters verantwortlich sind: Marketingverantwortliche,
Administratoren und Hosting-Partner. Sie erklärt in einfachen Worten, warum zurückkommende Mails wichtig sind, was
`cjw_newsletter` automatisch mit ihnen macht und welche kleine Routine eine Liste gesund hält.

## Inhalt

1. [Warum Listenpflege für ein Unternehmen wichtig ist](#warum-listenpflege-für-ein-unternehmen-wichtig-ist)
2. [Die Begriffe](#die-begriffe)
3. [Wie zurückkommende Mails den Newsletter erreichen](#wie-zurückkommende-mails-den-newsletter-erreichen)
4. [Was mit jeder Art geschieht](#was-mit-jeder-art-geschieht)
5. [Die Blacklist und die Sperrliste](#die-blacklist-und-die-sperrliste)
6. [Adressen importieren, die nie angeschrieben werden dürfen](#adressen-importieren-die-nie-angeschrieben-werden-dürfen)
7. [Das Dashboard und der Reparaturbefehl](#das-dashboard-und-der-reparaturbefehl)
8. [Eine monatliche Pflegeroutine](#eine-monatliche-pflegeroutine)

## Warum Listenpflege für ein Unternehmen wichtig ist

Mail-Anbieter beurteilen einen Absender nach seinem Verhalten. Wer immer wieder Adressen anschreibt, die es nicht gibt,
oder Menschen, die die Mail als Spam markieren, macht es wahrscheinlicher, dass der nächste Newsletter und womöglich
auch die Bestellbestätigungen von derselben Domain im Spam-Ordner landen. Die Tabelle zur Fehlerbehebung für
Website-Ersteller nennt „Mail wird als Spam markiert“ mit den Ursachen „DNS-Einträge der Absenderdomain, Inhalt und
Ruf des sendenden Servers“ und „Viele Bounces“ mit der Maßnahme „das Bounce-Postfach prüfen; die Liste bereinigen“
([Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md#when-something-goes-wrong)).

## Die Begriffe

| Begriff | Bedeutung |
|---|---|
| Bounce | Eine Mail, die als unzustellbar zurückgekommen ist |
| Hard Bounce | Dauerhaft: Die Adresse oder die Domain existiert nicht |
| Soft Bounce | Vorübergehend: ein volles Postfach, ein ausgefallener Server, eine Verzögerung, eine Ablehnung aufgrund von Richtlinien |
| Beschwerde | Der Empfänger hat die Mail als Spam markiert (eine Meldung aus einer Feedback-Schleife) |
| Blacklist | Die eigene Liste des Newsletters mit Adressen, die nie Mails bekommen |
| Sperrliste | Die websiteweite Liste der Adressen, die überhaupt keine optionalen Mails bekommen (ab Exponential 6.0.15) |

Die Definitionen von Hard Bounce, Soft Bounce und Beschwerde folgen dem Leitfaden der Erweiterung
([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

## Wie zurückkommende Mails den Newsletter erreichen

Zurückkommende Mails landen in einem Postfach. Der Newsletter liest sie auf eine von zwei Arten (derselbe Leitfaden):

- über ein **E-Mail-Konto des Newsletters** (Newsletter > E-Mail-Konten, der Cron-Teil `cjw_newsletter_mailbox`);
- über den **Bounce-Leser der E-Mail-Einstellungen** (`exp:mail:bounces`, Cron-Teil `mailbounces`), der jede Nachricht
  auch an den Newsletter weitergibt. Der Leser „bleibt ausgeschaltet, bis sein Postfach in einem Einstellungs-Override
  gesetzt ist“.

So oder so muss jemand das Postfach und den Cron-Teil einrichten. Tut das niemand, werden Bounces nie gelesen, und die
Liste füllt sich langsam mit toten Adressen. Die Cron-Zeilen stehen in
[Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up).

## Was mit jeder Art geschieht

| Art | Was die Erweiterung tut (4.2.0) |
|---|---|
| Hard Bounce | Die Adresse kommt mit dem Grund `bounce` auf die Sperrliste; das Einwilligungsprotokoll hält es fest |
| Beschwerde | Dasselbe, mit dem Grund `complaint` |
| Soft Bounce | Der Bounce-Zähler des Benutzers steigt; die Mail wird erneut gesendet, höchstens `SoftBounceMaxRetries`-mal (2), zuerst nach `SoftBounceRetryDelay` (3600 Sekunden), wobei jeder weitere Versuch doppelt so lange wartet |
| Zu viele Bounces | Nach `[BounceSettings] BounceThresholdValue` Bounces (Standard 3) erhält der Benutzer nichts mehr |

Quellen: [deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md) und die
mitgelieferte `cjw_newsletter.ini` (`[DeliverabilitySettings]`). Eine zugestellte Mail setzt den Soft-Bounce-Zähler auf
0 zurück. Für einen Benutzer, der gebounct, auf der Blacklist, entfernt oder gesperrt ist, gibt es keinen
Wiederholungsversuch.

> **Hinweis**
> Hard Bounces auf die Sperrliste zu setzen (`SuppressHardBounces=enabled`, der mitgelieferte Wert) blockiert jede
> optionale Mail der Website an diese Adresse, nicht nur den Newsletter. Das ist meist genau das, was Sie wollen: Die
> Adresse existiert nicht.

## Die Blacklist und die Sperrliste

- Die **Blacklist** (`newsletter/blacklist_item_list`) enthält Adressen, die den Newsletter nie bekommen dürfen; „sie
  hat Vorrang vor jeder Liste“ ([Newsletter-Listen und Abonnements](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist)).
- Die **Sperrliste** (Setup > E-Mail-Einstellungen > Sperrliste) enthält Hard Bounces, Beschwerden, Anfragen „alle
  optionalen E-Mails stoppen“, rechtliche Anfragen, Einträge eines Administrators und die Blacklist des Newsletters.
  „Gespeichert wird nur ein Hash der Adresse, die Liste lässt sich also nicht lesen; Sie finden einen Eintrag, indem Sie
  die Adresse eingeben“ ([Die Sperrliste](../../../anleitungen/mail-einstellungen-administrator.md#7-die-sperrliste)).
- Ein aufgehobener Eintrag der Sperrliste verschwindet auch von der Blacklist des Newsletters
  ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

Notwendige Mails (Zurücksetzen des Passworts, Bestellungen) gehen weiterhin an gesperrte Adressen (derselbe Leitfaden
für Administratoren).

## Adressen importieren, die nie angeschrieben werden dürfen

Newsletter > Sperrlisten-Import übernimmt eine CSV-Datei oder eingefügte Adressen in die Sperrliste, mit einem Grund
(`legal`, `admin`, `bounce`, `complaint` oder `unsubscribe_all`). Der Leitfaden nennt typische Quellen: „eine
Nicht-Kontaktieren-Liste (Robinsonliste), die Bounces eines früheren Mail-Systems, rechtliche Anfragen“. **Prüfen**
zählt nur und ändert nichts, sodass Sie zuerst sehen, was ein Import bewirken würde
([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

Wenn Sie von einem anderen Newsletter-Werkzeug wechseln, importieren Sie dessen Bounces und Abmeldungen vor dem ersten
Versand auf diese Weise, damit Menschen, die sich dort abgemeldet haben, hier nicht erneut angeschrieben werden.

## Das Dashboard und der Reparaturbefehl

Das Dashboard unter `/newsletter/index` listet Probleme auf, „jeweils mit dem, was dagegen zu tun ist (zum Beispiel
‚3 Abonnements gehören zu Newsletter-Benutzern, die es nicht mehr gibt‘, mit einer Schaltfläche **Entfernen**)“. Auf der
Shell zeigt `./console ext:cjw_newsletter:repair --dry-run` dieselbe Art von Reparaturen, ohne etwas zu ändern
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

## Eine monatliche Pflegeroutine

- [ ] Öffnen Sie das Dashboard und arbeiten Sie seine Liste der Probleme ab.
- [ ] Prüfen Sie, ob der Bounce-Cron-Teil kürzlich gelaufen ist (das Dashboard zeigt, wann jeder Cron-Teil zuletzt lief).
- [ ] Sehen Sie sich die Zahl der Hard Bounces und Beschwerden der letzten Versände an; ein plötzlicher Anstieg braucht
      eine Erklärung.
- [ ] Prüfen Sie wartende Abonnements, die älter als einige Wochen sind; viele davon deuten auf ein Problem mit der
      Bestätigungsmail hin.
- [ ] Entfernen Sie Listen, an die niemand mehr versendet, nachdem Sie exportiert haben, was Sie aufbewahren müssen.
- [ ] Löschen Sie Exporte und CSV-Dateien, die ihren Zweck erfüllt haben.
- [ ] Halten Sie fest, was Sie getan haben, in Ihrem Betriebsprotokoll ([Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md)).

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md)
- [Listen und Abonnements](listen-und-abonnements.md)
- [E-Mail-Einstellungen: der Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
