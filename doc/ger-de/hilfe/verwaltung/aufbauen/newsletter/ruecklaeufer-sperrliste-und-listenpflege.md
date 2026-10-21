---
title: Rückläufer, Sperrliste und Listenpflege
audience: government, admin
category: public-sector
order: 60
summary: Wie Sie die Newsletter-Listen einer Behörde mit cjw_newsletter sauber halten - das Postfach für Rückläufer und sein Cron-Teil, die Schwelle für Rückläufer, die Sperrliste, die über jeder Liste steht, Exporte der Abonnenten und die Aufbewahrung der Abonnentendaten.
keywords: Rückläufer, Postfach für Rückläufer, Sperrliste, Unterdrückungsliste, Listenpflege, Export der Abonnenten, Aufbewahrung, Datenminimierung, bounces, bounce mailbox, blacklist, suppression list, list hygiene, BounceThresholdValue, cjw_newsletter_mailbox, subscriber export, retention, data minimization
type: help
---
# Rückläufer, Sperrliste und Listenpflege

Diese Seite richtet sich an Administratoren von Newsletter-Listen. Saubere Listen schützen den Ruf des Absenders und die
Daten der Abonnenten
([Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md)).

## Inhalt

1. [Rückläufer](#rückläufer)
2. [Die Sperrliste](#die-sperrliste)
3. [Wo Abonnenten verwaltet werden](#wo-abonnenten-verwaltet-werden)
4. [Aufbewahrung](#aufbewahrung)

## Rückläufer

1. Richten Sie ein Postfach ein, das Rückläufer empfängt (**Newsletter > Mail accounts**, `newsletter/mailbox_list`,
   `mailbox_edit`).
2. Führen Sie den Cron-Teil `cjw_newsletter_mailbox` aus (in der dokumentierten Crontab stündlich).
3. Rückläufer werden je Abonnent gezählt; nach `BounceThresholdValue` Rückläufern (voreingestellt 3) erhält der
   Abonnent nichts mehr.

**Newsletter > Bounces** (`newsletter/mailbox_item_list`) listet die Nachrichten. Seit 4.2.0 kommen harte Rückläufer und
Beschwerden auf die Unterdrückungsliste, weiche Rückläufer werden erneut versucht
([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

## Die Sperrliste

**Newsletter > Blacklists** (`newsletter/blacklist_item_list`, `blacklist_item_add`, `blacklist_item_remove`): Adressen,
die nie eine Mail erhalten. Sie steht über jeder Liste. Verwenden Sie sie für Menschen, die nie wieder kontaktiert
werden möchten.

## Wo Abonnenten verwaltet werden

| View | Verwendung |
|---|---|
| `newsletter/subscription_list` | Abonnements einer Liste |
| `newsletter/user_list`, `user_view`, `user_edit` | Newsletter-Benutzer über Listen hinweg |
| `subscription_list_csvexport` | die Abonnenten einer Liste exportieren |

Exporte enthalten personenbezogene Daten: Bewahren Sie sie nur dort auf, wo Ihre Regeln es erlauben, und löschen Sie
sie, wenn Sie fertig sind.

## Aufbewahrung

Entscheiden Sie, wie lange entfernte und zurückgewiesene Abonnenten aufbewahrt werden und wie lange nie abgeschlossene
Bestätigungen bleiben. Das Entfernen eines Benutzerkontos löscht die E-Mail-Einstellungen der Person und anonymisiert
das Einwilligungsprotokoll; der Newsletter-Benutzer, die Interessen, die SMS-Daten und die personenbezogenen Statistiken
einer gelöschten Person werden ebenfalls entfernt
([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md#gdpr),
[cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

## Verwandte Seiten

- [Mail-Einwilligung und Rückläufer](../../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)
- [Double-Opt-in und Einwilligung](double-opt-in-und-einwilligung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
