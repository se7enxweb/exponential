---
title: FAQ für Unternehmen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 90
summary: Kurze Antworten, jede mit ihrer Quelle, auf die Fragen, die Inhaber und Führungskräfte stellen, bevor sie ihr Geschäft mit Exponential betreiben - Kosten, Lizenz, Hosting, Support, Shops, Apps, Sprachen, Datenschutz, Migration und was geschieht, wenn ein Partner aussteigt.
keywords: FAQ, Fragen, Antworten, Kosten, Lizenzgebühr, GPL, Hosting, Support, 7x, Community, Shop, Zahlungen, App, REST, Sprachen, DSGVO, Migration, eZ Publish, Partner, Anbieterbindung, Backups, Aktualisierungen, kleines Unternehmen, questions, answers, cost, license fee, store, payments, languages, GDPR, lock-in, upgrades, small business
type: help
---
# FAQ für Unternehmen

Kurze Antworten auf die Fragen, die Inhaber und Führungskräfte am häufigsten stellen. Jede Antwort nennt ihre Quelle
und verweist auf die Seite mit den Einzelheiten.

## Inhalt

1. [Kosten und Lizenz](#kosten-und-lizenz)
2. [Betrieb](#betrieb)
3. [Verkaufen und Kunden bedienen](#verkaufen-und-kunden-bedienen)
4. [Daten, Sicherheit und Compliance](#daten-sicherheit-und-compliance)
5. [Menschen und Support](#menschen-und-support)
6. [Wechsel zu Exponential](#wechsel-zu-exponential)

## Kosten und Lizenz

**Kostet Exponential eine Lizenzgebühr?**
Nein. Es ist freie Software unter der GNU General Public License, Version 2 oder später
([Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../geschaeft/lizenzen-fuer-kaeufer.md)). Sie bezahlen für
das Hosting, für die Arbeit am Aufbau und an der Pflege Ihrer Website und für einen Supportvertrag, falls Sie einen
wählen, nicht für die Software. Siehe [Wofür Sie nicht bezahlen](kosten/wofuer-sie-nicht-bezahlen.md).

**Was kostet dann der Betrieb?**
Hosting, die Zeit von Menschen für Updates, Backups und Inhalte und die Arbeit an eigenen Funktionen. Die Seiten
[Gesamtbetriebskosten](kosten/gesamtbetriebskosten.md) und [Kostentreiber beim Hosting](kosten/kostentreiber-beim-hosting.md)
listen jeden Posten auf; die durchgerechneten Beispiele verwenden Beispielannahmen, die Sie durch Ihre eigenen ersetzen.

**Darf ich es für eine kommerzielle Website nutzen?**
Eine Website zu betreiben ist kein Verbreiten der Software: Abschnitt 0 der Lizenz sagt „The act of running the
Program is not restricted“ (das Ausführen des Programms ist nicht eingeschränkt). Die Bedingungen der GPL gelten, wenn
jemand Kopien verbreitet, zum Beispiel ein auf Exponential aufgebautes Produkt, das Sie an Ihre eigenen Kunden
verkaufen. Einzelheiten und die Fragen, die Sie mit einem Rechtsberater klären sollten, stehen in
[Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../geschaeft/lizenzen-fuer-kaeufer.md#eine-website-zu-betreiben-ist-keine-weitergabe).

## Betrieb

**Was braucht ein Server?**
PHP 8.0 oder neuer auf fast jedem Betriebssystem; einen Webserver (Apache, Nginx und die anderen oder das
mitgelieferte Velocity); und eine Datenbank, wobei SQLite 3, die Standardwahl, keinen Datenbankserver braucht
(`README.md`, „Requirements“; [Anforderungen auf einen Blick](../hosting/grundlagen/anforderungen-auf-einen-blick.md)).

**Läuft es auf Shared Hosting?**
Meist ja, mit Apache oder LiteSpeed und `.htaccess`. Velocity braucht einen langlebigen Prozess, den Shared Hosting
„meist nicht“ erlaubt (`README.md`, „When a traditional server is still the right answer“).
Siehe [Hosting-Modelle im Vergleich](auswahl/hosting-modelle-im-vergleich.md).

**Wie schnell ist es?**
Die dokumentierten Werte für den Stack sind 0,36 ms pro Seite aus dem Cache und 8.941 Anfragen pro Sekunde im
Dauerbetrieb auf einer Maschine mit 12 Kernen, mit ihrer Messmethode (`README.md`, „What that measures as“). Ihre
eigenen Werte hängen von Ihrer Hardware und Ihren Seiten ab. Siehe [Velocity für Unternehmen](../../../eng-us/help/businesses/reliability/velocity-for-businesses.md).

**Wie hoch ist die Verfügbarkeit?**
Das Projekt veröffentlicht keinen Verfügbarkeitswert, und diese Hilfe erfindet keinen. Die Verfügbarkeit hängt vom
Hosting, von der Überwachung und von der Art ab, wie die Website betrieben wird. Siehe
[Verfügbarkeit in der Praxis](../../../eng-us/help/businesses/reliability/uptime-practice.md) und
[Grundlagen zu Verträgen und SLAs](auswahl/grundlagen-zu-vertraegen-und-slas.md).

**Wie werden Updates ausgeliefert?**
Als neue 6.0.x-Releases; „Ein veröffentlichter Tag wird nie geändert“ (`SECURITY.md`). Die Administration prüft eine
Installation vor einem Upgrade gegen das Release
([Die Upgrade-Prüfung: Dateien und Datenbank vor einem Upgrade](../../anleitungen/upgrade-pruefung.md)).
Siehe [Aktualisierungen ohne Überraschungen](../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md).

## Verkaufen und Kunden bedienen

**Gibt es einen eingebauten Shop?**
Ja: „Advanced e-Commerce Stores (Built in)“ (`README.md`), mit Produkten, Warenkorb, Bestellungen, USt und Währungen
sowie Zahlungsanbietern wie PayPal über `ezpaypal`. Siehe [Online-Shops](shops/README.md).

**Können wir eine mobile App haben?**
Ja, über die REST API („A headless CRUD REST API“, `README.md`), die dieselben Berechtigungen prüft wie die Website
([REST API v1 und v2: eine Extension](../../anleitungen/rest-api-v1-und-v2.md)). Viele Unternehmen beginnen mit einer
responsiven Website. Siehe [Mobil und headless](../../../eng-us/help/businesses/mobile-and-headless/README.md).

**Wie viele Sprachen?**
268 Locale-Dateien lassen sich als Inhaltssprachen und Siteaccess-Locales wählen, 40 davon werden von rechts nach
links geschrieben; die Administration hat 33 Übersetzungen, neunzehn davon vollständig
([Sprachen in Exponential](../../../eng-us/languages.md)). Siehe [Mehrsprachige Unternehmen](../../../eng-us/help/businesses/multilingual/README.md).

**Können wir mehrere Marken aus einer Installation betreiben?**
Ja, „Multi-site support“ ist eingebaut (`README.md`). Siehe [Marken mit Multisite](../../../eng-us/help/businesses/multisite/README.md).

**Können Mitarbeiter, Partner und die Öffentlichkeit dieselbe Installation nutzen?**
Ja. Rollen, Richtlinien, Sektionen und Siteaccesses entscheiden, wer was sieht
([Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)). Siehe
[Intranets](intranets/README.md) und [Extranets](extranets/README.md).

## Daten, Sicherheit und Compliance

**Wo sind unsere Daten?**
In Ihrer Datenbank und Ihrem Dateispeicher, auf dem Server, den Sie wählen. Nichts wird anderswohin gesendet, sofern
Sie es nicht einrichten, zum Beispiel einen Zahlungsanbieter oder den optionalen Schreibassistenten, der „nichts
irgendwohin sendet, bis Sie einen Anbieterschlüssel hinzufügen“ (`README.md`, „Exponential Intelligence“). Siehe
[Personenbezogene Daten in Exponential](../../../eng-us/help/businesses/security/personal-data-in-exponential.md).

**Macht Exponential uns DSGVO-konform?**
Das schafft keine Software allein. Sie gibt Ihnen Werkzeuge (Berechtigungen, einen Audit-Trail, Kontoverwaltung), die
helfen; die Pflichten liegen bei Ihnen. Siehe [DSGVO-Grundlagen](../../../eng-us/help/businesses/security/gdpr-basics.md); das sind Grundlagen, keine
Rechtsberatung.

**Wie werden Sicherheitsprobleme behandelt?**
Melden Sie sie vertraulich an security@se7enx.com, mit „einer ersten Antwort innerhalb eines Werktags“; Korrekturen
werden mit dem nächsten 6.0.x-Release veröffentlicht (`SECURITY.md`). Siehe
[Sicherheitsupdates und Offenlegung](../../../eng-us/help/businesses/security/security-updates-and-disclosure.md).

**Können wir nachweisen, wer was geändert hat?**
Ja, mit dem manipulationssicheren Audit-Trail ([Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md)).
Siehe [Compliance-Nachweise aus dem Audit-Trail](../../../eng-us/help/businesses/security/compliance-evidence-from-the-audit-trail.md).

## Menschen und Support

**Wer steht hinter Exponential?**
7x leitet das Projekt, und die gemeinnützige Exponential Foundation betreibt es ([Wer ist 7x?](../wer-ist-7x.md),
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

**Wo bekommen wir Support?**
Von Ihrem Partner oder Hoster; von 7x, das „Support- und Wartungsverträge für bestehende Websites“ und Managed Hosting
anbietet ([Wer ist 7x?](../wer-ist-7x.md#dienstleistungen-und-hosting)); und von der Community: den Foren unter
<https://share.exponential.earth/forums>, dem Telegram-Chat und den Issue-Trackern (`README.md`, „Where to get more
help“, „Issue tracker“). Siehe [Zusammenarbeit mit 7x und dem Projekt](auswahl/zusammenarbeit-mit-7x-und-dem-projekt.md).

**Was, wenn unser Partner die Zusammenarbeit beendet?**
Der Code ist freie Software, und die Inhalte liegen in Ihrer Datenbank, sodass ein anderer Partner übernehmen kann.
Stellen Sie sicher, dass Sie Zugang zu Ihrem Repository, Ihrem Server und Ihren Backups haben. Siehe
[Einen Partner auswählen](auswahl/einen-partner-auswaehlen.md).

**Können wir das Projekt finanziell unterstützen?**
Ja: Sponsoring und Spenden sind in `README.md` unter „Donate and make a support subscription“ aufgeführt
(unter anderem <https://github.com/sponsors/se7enxweb>).

## Wechsel zu Exponential

**Wir haben eine Website aus den Jahren von eZ Publish. Können wir sie behalten?**
Ja. Das Installationshandbuch hat Kapitel zur Migration von der 4.x-Linie, vom 5.x-Legacy-Stack und von eZ Platform
oder Ibexa ([14. Migration von der 4.x-Linie](../../installation/14-migration-von-4x.md),
[15. Migration vom 5.x-Legacy-Stack](../../installation/15-migration-von-5x-legacy.md),
[16. Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md)).

**Wir verwenden ein anderes System. Wie wechseln wir?**
Siehe [Zu Exponential wechseln](auswahl/zu-exponential-wechseln.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](unternehmen-hier-beginnen.md)
- [Glossar für Unternehmen](glossar-fuer-unternehmen.md)
- [FAQ für Website-Ersteller](../../../eng-us/help/creators/website-creators/website-creator-faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
