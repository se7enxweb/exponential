---
title: Double-Opt-in und Einwilligung
audience: government, admin, editor
category: public-sector
order: 40
summary: Wie Abonnements in cjw_newsletter gültig werden (Double-Opt-in und Status), was das Anmeldeformular sagen muss, wie die Einwilligung von den E-Mail-Einstellungen des Kernels festgehalten wird, bestehende Listen nur mit Einwilligung importieren und personenbezogene Statistiken.
keywords: Double-Opt-in, Einwilligung, Status des Abonnements, ausstehend, bestätigt, Einwilligungsprotokoll, DSGVO, ePrivacy, CSV-Import, Newsletter-Statistik, E-Mail-Einstellungen, double opt-in, consent, subscription status, pending, confirmed, consent log, GDPR, CSV import, newsletter statistics, mail preferences
type: help
---
# Double-Opt-in und Einwilligung

Diese Seite richtet sich an Administratoren und Redakteure, die für Newsletter-Abonnenten verantwortlich sind. Die
Mailinglisten einer Behörde enthalten personenbezogene Daten und müssen auf einer klaren Grundlage beruhen;
Double-Opt-in beweist, dass der Inhaber der Adresse es verlangt hat.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wie ein Abonnement gültig wird](#wie-ein-abonnement-gültig-wird)
2. [Was das Anmeldeformular sagt](#was-das-anmeldeformular-sagt)
3. [Nachweise der Einwilligung](#nachweise-der-einwilligung)
4. [Bestehende Listen importieren](#bestehende-listen-importieren)
5. [Statistik und Einwilligung](#statistik-und-einwilligung)

## Wie ein Abonnement gültig wird

| Status | Bedeutung |
|---|---|
| Ausstehend | angemeldet, Bestätigungsmail gesendet, noch nicht bestätigt |
| Bestätigt | der Abonnent hat auf den Bestätigungslink geklickt |
| Freigegeben | bestätigt und, wo die Liste es verlangt, von einem Administrator freigegeben |
| Vom Benutzer / vom Administrator entfernt | erhält keine Mails mehr |
| Weicher oder harter Rückläufer | die Mail konnte nicht zugestellt werden |

Nur bestätigte oder freigegebene Abonnenten erhalten Ausgaben
([Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md)).

## Was das Anmeldeformular sagt

- Was Abonnenten erhalten und wie oft.
- Wer es sendet (die Behörde), und der Kontakt für Fragen.
- Welche Daten aufbewahrt werden, warum, wie lange und wie man sich abmeldet.
- Ein Link auf die Datenschutzerklärung.

## Nachweise der Einwilligung

Die E-Mail-Einstellungen des Kernels führen ein Einwilligungsprotokoll (`expmail_consent_log`): Zeit, Kategorie, alter
und neuer Zustand, Quelle, der genau angezeigte Text, handelnder Benutzer, Siteaccess. Werbliche Kategorien sind
Opt-in, der Widerruf ist so einfach wie die Einwilligung, und eine Person kann ihre Einstellungen und ihren
Einwilligungsverlauf herunterladen ([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md#gdpr),
[E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)). Der Newsletter
arbeitet seit 4.2.0 über diese E-Mail-Einstellungen
([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

## Bestehende Listen importieren

`newsletter/subscription_list_csvimport` importiert Abonnenten aus CSV. Importieren Sie nur Menschen, die dem Empfang
dieses Newsletters zugestimmt haben, und halten Sie fest, woher die Einwilligung kam. Im Zweifel senden Sie eine
einzelne Mail zur erneuten Einwilligung über einen anderen Kanal oder beginnen die Liste neu.

## Statistik und Einwilligung

Öffnungen und Klicks je Person werden nur mit der Einwilligung des Empfängers in die Kategorie „Newsletter statistics“
gezählt; sonst nur anonyme Summen ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)). Das
Tracking ist aus, bis es eingeschaltet wird. Viele Behörden brauchen überhaupt keine personenbezogenen Statistiken.

## Verwandte Seiten

- [Abmeldung mit einem Klick und das Mail-Gateway](abmeldung-mit-einem-klick-und-das-mail-gateway.md)
- [E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
