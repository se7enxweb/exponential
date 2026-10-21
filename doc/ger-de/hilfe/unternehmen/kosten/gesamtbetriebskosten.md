---
title: Gesamtbetriebskosten
audience: businesses, project-manager, admin
category: business-solutions
order: 10
summary: Die Kostenposten einer Webanwendung auf Exponential über ihre gesamte Lebensdauer, welche davon die Software kleiner macht, und eine durchgerechnete Fünfjahresrechnung zum Übernehmen mit Ihren eigenen Zahlen.
keywords: Gesamtbetriebskosten, TCO, fünf Jahre, Kostenposten, Umsetzung, Hosting, Wartung, Support, Schulung, Ausstiegskosten, Lizenzgebühr, Budget, total cost of ownership, five years, cost lines, implementation, hosting, maintenance, support, training, exit cost, license fee, budget
type: help
---
# Gesamtbetriebskosten

Diese Seite ist für Inhaber, Führungskräfte und Mitarbeiter der Finanzabteilung, die sagen müssen, was eine
Webanwendung wirklich kostet, nicht nur im ersten Jahr. Sie listet jeden Kostenposten auf, den eine Website auf
Exponential über ihre Lebensdauer hat, zeigt, welche Fakten über die Software einen Posten kleiner machen, und rechnet
eine Fünfjahresrechnung durch, die Sie übernehmen können.

> **Hinweis**
> Alle Zahlen in der Berechnung sind **Beispielannahmen**: runde, veranschaulichende Zahlen in einer neutralen
> Währungseinheit. Es sind weder Marktpreise noch Angebote. Ersetzen Sie jede durch Ihre eigenen Zahlen, bevor Sie das
> Ergebnis verwenden.

## Inhalt

1. [Was TCO hier bedeutet](#was-tco-hier-bedeutet)
2. [Die Kostenposten](#die-kostenposten)
3. [Was jeden Posten bei Exponential kleiner macht](#was-jeden-posten-bei-exponential-kleiner-macht)
4. [Ein durchgerechnetes Fünfjahresbeispiel](#ein-durchgerechnetes-fünfjahresbeispiel)
5. [Kosten, die vergessen werden](#kosten-die-vergessen-werden)
6. [Wie Sie Ihre eigenen Zahlen sammeln](#wie-sie-ihre-eigenen-zahlen-sammeln)

## Was TCO hier bedeutet

Die Gesamtbetriebskosten (Total Cost of Ownership, TCO) sind die Summe aller Kosten einer Website von der Entscheidung,
sie zu bauen, bis zu dem Tag, an dem sie ersetzt wird, geteilt durch die Jahre, die sie läuft. Ein billiger Start mit
später teuren Jahren ist nicht billig. Fünf Jahre sind ein üblicher Zeitraum für den Vergleich; wählen Sie den
Zeitraum, den Ihre Organisation auch für andere Investitionen verwendet.

## Die Kostenposten

| Posten | Einmalig oder laufend | Typischer Inhalt |
|---|---|---|
| Lizenz | bei vielen Produkten laufend | bei Exponential: keine (siehe unten) |
| Umsetzung | einmalig | Planung, Inhaltsmodell, Design, Templates, Erweiterungen, Datenmigration |
| Hosting | laufend | Server oder ein Hosting-Tarif, Backups, Monitoring, TLS-Zertifikate, Mailzustellung |
| Betrieb | laufend | Aktualisierungen einspielen, Logs beobachten, Cronjobs, Wiederherstellungstests |
| Aktualisierungen | periodisch | Wechsel auf ein neueres Release, PHP-Aktualisierungen, Tests |
| Support | laufend | ein Supportvertrag oder die Zeit Ihrer eigenen Mitarbeiter |
| Schulung | einmalig und bei Personalwechsel | Redakteure, Administratoren |
| Inhaltsarbeit | laufend | Schreiben, Übersetzen, Prüfen; oft der größte Posten und bei jedem Produkt gleich |
| Ausstieg | am Ende | Inhalte und Daten exportieren oder zu einem anderen Anbieter umziehen |

## Was jeden Posten bei Exponential kleiner macht

| Posten | Fakt | Quelle |
|---|---|---|
| Lizenz | „Using Exponential under the terms of the GNU GPL is free of charge.“ (Die Nutzung unter der GNU GPL ist kostenlos.) | `README.md`, „License“ |
| Lizenz pro Server, Website oder Benutzer | „The act of running the Program is not restricted“ (Das Ausführen des Programms ist nicht eingeschränkt) | `LICENSE.md` Abschnitt 0; [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| Umsetzung | Shop, Multisite, mehrsprachige Inhalte, Workflows, eine REST API, Kontaktformulare, Feeds und ein Audit-Trail sind eingebaut | `README.md`, „Main Exponential features“ |
| Hosting | SQLite 3, die Standarddatenbank, braucht keinen Datenbankserver; Velocity braucht außer PHP nichts installiert | `README.md`, „Requirements“ |
| Hosting | Läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer | [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| Betrieb | Ein Befehl installiert eine Website; Wartungsmodus, ein Log-Viewer und ein Health-Check sind dabei | [Installation mit einem Befehl](../../../../eng-us/features/6.0/install-in-one-command.md), [Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md), [Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md) |
| Aktualisierungen | Eine gepflegte Linie (6.0); Korrekturen kommen als nächstes Release 6.0.x | `SECURITY.md`; [Aktualisieren](../../../anleitungen/aktualisieren.md) |
| Support | Es gibt kostenlose Community-Kanäle; bezahlten Support können 7x oder jedes andere Unternehmen leisten | `README.md`, „Where to get more help“; [Wer ist 7x?](../../wer-ist-7x.md) |
| Ausstieg | Die GPL erlaubt Ihnen, den Code zu jedem Anbieter mitzunehmen; die vollständige Git-Historie ist öffentlich | [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md), [Mit Git entwickeln](../../mit-git-entwickeln.md) |

Nichts davon macht die Inhaltsarbeit von sich aus billiger: Inhalte zu schreiben und zu prüfen kostet auf jedem System
dieselbe Zeit. Zählen Sie sie trotzdem, denn dorthin geht meist das meiste Geld.

## Ein durchgerechnetes Fünfjahresbeispiel

Das Beispiel ist die Website eines mittelgroßen Unternehmens mit einem Kontaktformular und einem Nachrichtenbereich.
Jeder Eingabewert ist eine **Beispielannahme**.

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Umsetzung durch einen Partner | 20.000 Währungseinheiten, einmalig |
| Beispielannahme: Schulung von Redakteuren und Administratoren | 1.000 Währungseinheiten, einmalig |
| Beispielannahme: Hosting-Tarif | 100 Währungseinheiten im Monat |
| Beispielannahme: Betrieb durch eigene Mitarbeiter | 20 Stunden im Jahr zu 50 Währungseinheiten pro Stunde |
| Beispielannahme: Supportvertrag | 1.500 Währungseinheiten im Jahr |
| Lizenzgebühr | 0 (Fakt: `README.md`, „License“) |

Schritt für Schritt:

1. Einmalige Kosten: 20.000 + 1.000 = **21.000**.
2. Hosting pro Jahr: 100 x 12 = 1.200. Über fünf Jahre: 1.200 x 5 = **6.000**.
3. Betrieb pro Jahr: 20 x 50 = 1.000. Über fünf Jahre: 1.000 x 5 = **5.000**.
4. Support über fünf Jahre: 1.500 x 5 = **7.500**.
5. Lizenz über fünf Jahre: 0 x 5 = **0**.
6. Gesamtbetriebskosten: 21.000 + 6.000 + 5.000 + 7.500 + 0 = **39.500**.
7. Pro Jahr: 39.500 / 5 = **7.900**.

Ersetzen Sie nun jeden Beispielwert durch Ihren eigenen. Wenn Sie mit einem anderen Produkt vergleichen, fügen Sie
dessen Lizenz- oder Abonnementposten aus dem schriftlichen Angebot des Herstellers hinzu, und lassen Sie alle anderen
Posten gleich, damit der Vergleich fair bleibt.

> **Tipp**
> Fügen Sie einen Posten für eine Aktualisierung im Zeitraum hinzu und einen für die erwarteten Ausfallkosten. Die
> [Seite zum Budget](budget-fuer-aktualisierungen-und-wartung.md) und das
> [Arbeitsblatt: Kosten eines Ausfalls](arbeitsblatt-kosten-eines-ausfalls.md) zeigen, wie.

## Kosten, die vergessen werden

- [ ] Arbeitszeit für Wiederherstellungstests
      ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] PHP- und Betriebssystem-Aktualisierungen auf dem Server, nicht nur Aktualisierungen von Exponential.
- [ ] Mailzustellung für Formulare, Belege und Newsletter ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).
- [ ] Gebühren des Zahlungsanbieters für einen Shop; sie legt der Anbieter fest, nicht Exponential.
- [ ] Gebühren eines gehosteten KI-Anbieters, wenn Sie Exponential Intelligence mit einem gehosteten Modell einschalten
      (`README.md`, „Requirements“, nennt „an API key of a hosted AI provider, or a local open-weight model“ – einen
      API-Schlüssel eines gehosteten KI-Anbieters oder ein lokales Modell mit offenen Gewichten).
- [ ] Übergabezeit, wenn ein Mitarbeiter oder Ehrenamtlicher geht.
- [ ] Prüfungen zu Barrierefreiheit und Datenschutz.
- [ ] Die Kosten für den Auszug am Ende, auch wenn es nie dazu kommt.

## Wie Sie Ihre eigenen Zahlen sammeln

1. Bitten Sie zwei oder drei Partner oder Hoster um schriftliche Angebote zu denselben Anforderungen
   ([Checkliste Anforderungen](../vorlagen/checkliste-anforderungen.md)).
2. Bitten Sie Ihre eigenen Mitarbeiter, die Stunden für den Betrieb zu schätzen, und prüfen Sie sie an einem Monat
   echter Arbeit.
3. Nehmen Sie die Hostingzahlen aus dem Tarif, den Sie wirklich kaufen würden; zur Dimensionierung siehe
   [Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md), die nur Zahlen
   nennt, die das Repository liefert.
4. Tragen Sie jede Zahl mit ihrer Quelle in die [Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md) ein.
5. Prüfen Sie die Zahlen einmal im Jahr mit der [Checkliste Jahresrückblick](../vorlagen/checkliste-jahresrueckblick.md).

## Verwandte Seiten

- [Wofür Sie nicht bezahlen](wofuer-sie-nicht-bezahlen.md)
- [Rendite der Stabilität](rendite-der-stabilitaet.md)
- [Kostentreiber beim Hosting](kostentreiber-beim-hosting.md)
- [Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
