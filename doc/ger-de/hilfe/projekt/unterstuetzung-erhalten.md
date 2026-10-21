---
title: "Unterstützung erhalten: kostenlose und bezahlte Hilfe"
audience: user, editor, admin, developer, designer, partner
category: support
order: 20
summary: Jeder Weg, Hilfe zu Exponential zu bekommen, nach der Support-Seite von exponential.earth - zuerst die Dokumentation, eZpedia und die Artikel auf Share Exponential, dann die Foren und die Chats auf Telegram, Discord und Slack, alle kostenlos; oder bezahlter Support von 7x, wenn Sie nicht warten können. Was Sie zuerst versuchen sollten, wohin welche Art von Frage gehört und wie Sie so fragen, dass Sie eine Antwort bekommen.
keywords: Support, Hilfe, kostenloser Support, bezahlter Support, Community-Support, Foren, Dokumentation, Artikel, beauftragen, Beratung, Dienstleistungen, Antwortzeit, eine Frage stellen, Fehler, Sicherheit, support, help, free support, paid support, community support, forums, Share Exponential, Telegram, Discord, Slack, documentation, eZpedia, articles, 7x, hire, consulting, services, response time, ask a question, bug, security
featured: true
type: help
---
# Unterstützung erhalten: kostenlose und bezahlte Hilfe

Exponential ist freie Software, und seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten; die
[Support-Seite](https://exponential.earth/support) des Projekts sagt das offen und fügt hinzu, dass sie sich freuen,
wenn Menschen die Software nutzen, und gern die Extrameile gehen, wenn sie können. Es gibt also zwei Arten von
Support: **kostenlose Hilfe von der Community** und **bezahlte Hilfe von 7x**, wenn Sie sie schneller brauchen oder
jemanden brauchen, der die Arbeit erledigt.

## Zuerst: nachschlagen

Die meisten Fragen wurden schon einmal beantwortet. Sehen Sie in dieser Reihenfolge nach:

1. **Diese Hilfe.** Durchsuchen Sie sie mit dem Suchfeld des Hilfe-Dashboards, oder klicken Sie auf jeder Seite der
   Administration auf die Schaltfläche „?“, um Hilfe zu dieser Seite zu erhalten.
2. **Die Dokumentations-Website**, [exponential.doc.exponential.earth](https://exponential.doc.exponential.earth/),
   mit dem Installationshandbuch für Exponential 6 und dem technischen Handbuch.
3. **eZpedia**, [ezpedia.exponential.earth](https://ezpedia.exponential.earth/), das Wiki der Community.
4. **Die Artikel auf Share Exponential**, [share.exponential.earth/learn](https://share.exponential.earth/learn), zu
   zentralen Themen.

Weitere Orte finden Sie unter [Quellen der Dokumentation](quellen-der-dokumentation.md).

## Kostenlose Hilfe von der Community

Ist die Antwort nicht dokumentiert, fragen Sie. Diese Kanäle nennt die Support-Seite, alle kostenlos:

| Kanal | Geeignet für | Adresse |
|---|---|---|
| **Foren von Share Exponential** | Fragen, die eine längere Antwort brauchen und nach denen andere später suchen werden | <https://share.exponential.earth/forums> |
| **Telegram** | Schnelle Fragen in Echtzeit; die Support-Seite beschreibt es als Chat mit Mitgliedern des Entwicklungsteams und der Community | <https://t.me/exponentialcms> |
| **Discord** | Chat mit der Community, Gespräche über Entwicklung, Rückmeldungen | <https://discord.gg/zXzGMgfzej> |
| **Slack** | Chat, besonders über Entwicklung | <https://exponentialcms.slack.com/> |

Einzelheiten und die anderen Orte, an denen sich die Community trifft, finden Sie unter
[Community-Kanäle](community-kanaele.md).

### So fragen, dass Sie eine Antwort bekommen

- **Nennen Sie, was Sie betreiben**: die Exponential-Version, die PHP-Version, den Webserver (Velocity, Apache oder
  nginx mit PHP-FPM, FrankenPHP), die Datenbank. **Setup > Systeminformationen** liefert Ihnen eine Zusammenfassung
  zum Kopieren, ohne Passwörter, Schlüssel oder Serverpfade.
- **Sagen Sie, was Sie getan haben, was Sie erwartet haben und was geschehen ist**, mit der genauen Meldung.
- **Zeigen Sie, was Sie versucht** und was Sie bereits gelesen haben.
- **Veröffentlichen Sie nie** Passwörter, Schlüssel, Sitzungs-Cookies, Datenbankadressen oder personenbezogene Daten
  Ihrer Benutzer oder Kunden.
- **Melden Sie sich zurück**, wenn Sie es gelöst haben, damit die nächste Person die Antwort findet.

## Wenn es ein Fehler ist und keine Frage

Tut die Software etwas Falsches, melden Sie es dort, wo es für alle behoben werden kann:
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden) erklärt, welchen Issue-Tracker
Sie verwenden, und die Seite **Fehler melden** dieser Hilfe bereitet das Issue für Sie vor. Sicherheitsprobleme
werden nie öffentlich gemeldet: Schicken Sie sie vertraulich, wie
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#sicherheitsprobleme) erklärt.

## Negative Funktionen

Nicht alles, was wie ein Fehler aussieht, ist einer. Manches Verhalten ist eine bewusste Entscheidung, die einfach
nicht zu jeder Installation passt, und manches ist Auslegungssache: Zwei vernünftige Menschen lesen dieselbe
Dokumentation und erwarten verschiedene Ergebnisse. Fachleute nennen ein solches Verhalten eine **negative Funktion**
(englisch „negative feature“): Von einer Seite aus wirkt es wie ein Mangel, und doch gibt es es mit Absicht, oft zum
Schutz der Sicherheit, der Abwärtskompatibilität, der Leistung oder der vielen Sites, die sich schon heute genau auf
dieses Verhalten verlassen.

Beispiele für etwas, das eher eine negative Funktion als ein Fehler sein kann:

- eine Einstellung, die standardmäßig aus ist, weil das Einschalten bestehende Sites verändern würde (zum Beispiel
  Preise, URLs oder Berechtigungen);
- eine Grenze oder Ablehnung, die vor Missbrauch schützt, etwa eine Prüfung, die Eingaben zurückweist, die andere
  Sites nie senden;
- ein altes Verhalten, auf das sich Erweiterungen, Templates oder Anbindungen verlassen, auch wenn ein neuer Entwurf
  anders entscheiden würde;
- ein Unterschied zwischen dem, was Sie erwartet haben, und dem, was die Dokumentation tatsächlich zusagt.

Was das für ein Issue oder einen Pull Request bedeutet:

- **Erklären Sie das Warum, nicht nur das Was.** Beschreiben Sie Ihren Anwendungsfall, was Sie das heutige Verhalten
  kostet und wen es sonst betrifft. Eine Änderung an einer negativen Funktion wird gegen jede Installation
  abgewogen, die sich darauf verlässt.
- **Rechnen Sie mit Diskussion.** Eine solche Änderung kann eine ausführliche Begründung brauchen, und ein Pull
  Request kann mehrere Runden der Prüfung und Überarbeitung durchlaufen, bevor er angenommen und zusammengeführt
  wird. Das ist normal und keine Ablehnung.
- **Eine Option ist besser als ein neuer Standard.** Ein Vorschlag, der das heutige Verhalten als Standard behält und
  für das neue eine Einstellung, einen Hook oder einen Erweiterungspunkt hinzufügt, lässt sich viel leichter
  annehmen als einer, der die Site aller verändert.
- **Fragen Sie zuerst, wenn Sie unsicher sind.** Eine kurze Frage in der Community, bevor Sie Code schreiben, erspart
  Ihnen eine lange Prüfung.

Antwortet ein Maintainer, dass etwas wie beabsichtigt funktioniert, steht der Grund meist in der Dokumentation, im
Änderungsprotokoll oder im Code. Sie können trotzdem eine Änderung vorschlagen: Das Projekt möchte hören, warum das
heutige Verhalten für Sie nicht passt.

## Bezahlte Hilfe von 7x

Wenn Sie nicht warten können oder jemanden brauchen, der die Arbeit erledigt, empfiehlt die Support-Seite, **7x** zu
beauftragen, das Unternehmen, das Exponential pflegt: [se7enx.com/services](https://se7enx.com/services), oder nehmen
Sie über [se7enx.com/contact](https://se7enx.com/contact) Kontakt auf. Die Support-Seite merkt an, dass sich
Entwickler innerhalb und außerhalb des Exponential-Teams beauftragen lassen; 7x ist der Anbieter, den sie nennt. Mehr
über 7x: [Wer ist 7x?](../wer-ist-7x.md).

Unternehmen, die Dienstleistungen rund um Exponential anbieten, können auch Partner des Projekts werden, und Partner
haben eigene Support-Stufen ([Partner: Support-Stufen und Antwortzeiten](../partner/support-stufen-und-reaktionszeiten.md)).

## Was kostenloser Support nicht versprechen kann

Kostenlose Hilfe hängt von der Zeit ab, die Menschen geben. Niemand verspricht in den Foren oder Chats eine
Antwortzeit, und niemand meldet sich kostenlos auf Ihrem Server an. Für eine garantierte Antwortzeit oder Arbeit an
Ihrer Site nutzen Sie bezahlten Support. Hat Ihnen die kostenlose Hilfe Zeit oder Geld gespart, denken Sie darüber
nach, etwas zurückzugeben: eine Antwort auf die Frage eines anderen, eine Korrektur oder eine Spende
([Geben und spenden](geben-und-spenden.md)).

## Verwandte Seiten

- [Community-Kanäle](community-kanaele.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Exponential: Häufige Fragen](faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
