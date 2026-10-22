---
title: Wie Sicherheitsmeldungen behandelt werden
audience: community, admin, developer, hosting, advocate
category: community
order: 53
summary: Was Community-Mitglieder tun, wenn sie ein Sicherheitsproblem in Exponential finden oder davon hören - der vertrauliche Meldeweg, was hineingehört und was nicht, was danach geschieht und wie Sie sich in öffentlichen Kanälen verhalten, bis eine Korrektur veröffentlicht ist.
keywords: Sicherheit, Sicherheitslücke, Schwachstelle, Meldung, security@se7enx.com, vertrauliche Schwachstellenmeldung, SECURITY.md, verantwortungsvolle Offenlegung, Korrektur, Release, security, vulnerability, report, private vulnerability reporting, responsible disclosure, fix
type: help
---
# Wie Sicherheitsmeldungen behandelt werden

Sicherheitsprobleme nehmen einen anderen Weg als alle anderen Meldungen: Sie bleiben vertraulich, bis eine Korrektur
veröffentlicht ist.

## Inhalt

1. [Wohin Sie eine Meldung senden](#wohin-sie-eine-meldung-senden)
2. [Was hineingehört](#was-hineingehört)
3. [Was danach geschieht](#was-danach-geschieht)
4. [In öffentlichen Kanälen](#in-öffentlichen-kanälen)

## Wohin Sie eine Meldung senden

„Bitte melden Sie Sicherheitsprobleme vertraulich per E-Mail an security@se7enx.com, nicht im öffentlichen
Issue-Tracker oder in den Diskussionen“ (`SECURITY.md`). Auch die vertrauliche Schwachstellenmeldung von GitHub für
das Repository wird angenommen ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Was hineingehört

Aus `SECURITY.md`:

- die Version (`composer show se7enxweb/exponential` oder der Tag oder Commit, den Sie verwenden) und die PHP-Version;
- die Engine, die die Website ausliefert (Exponential Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM), und die
  Datenbank;
- was ein Angreifer tun kann, und die Schritte, um es nachzustellen.

„Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.“

Wenn Sie ein KI-Werkzeug verwendet haben, um das Problem zu finden oder zu beschreiben, überprüfen Sie es zuerst:
„Ungeprüfte Sicherheitsmeldungen gehören nirgendwo an die Öffentlichkeit; überprüfen Sie sie und senden Sie sie so,
wie es `SECURITY.md` beschreibt“ (`CODE_OF_CONDUCT.md`).

## Was danach geschieht

| Schritt | Was die Richtlinie sagt |
|---|---|
| Erste Antwort | „Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen“ |
| Entscheidung | „Ihnen wird mitgeteilt, ob die Meldung angenommen oder abgelehnt wird“ |
| Während eine Korrektur vorbereitet wird | Sie werden „auf dem Laufenden gehalten“ |
| Die Korrektur | „Ein angenommenes Problem wird auf `main` behoben und mit der nächsten 6.0.x-Version veröffentlicht“ |

## In öffentlichen Kanälen

- Wenn jemand eine Sicherheitslücke öffentlich postet, bitten Sie darum, sie an security@se7enx.com zu senden;
  diskutieren Sie die Einzelheiten nicht ([Sicherheitsmeldungen bleiben vertraulich](../../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md)).
- Spekulieren Sie nicht über unveröffentlichte Korrekturen.
- Sobald ein Release erschienen ist, verweisen Sie auf das Upgrade, nicht auf die Einzelheiten des Angriffs.
- Für Ratschläge zur Absicherung verlinken Sie [Kapitel 13 des Installationsbuchs](../../../installation/13-sicherheitshaertung.md).

## Verwandte Seiten

- [So funktioniert die Community](README.md)
- [Verantwortung für die Sicherheit (Partner)](../../partner/sicherheitspflichten.md)
- [Vertrauliche Sicherheitsmeldungen (Kunden)](../../kunden/gesund-halten/vertrauliche-sicherheitsmeldungen.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
