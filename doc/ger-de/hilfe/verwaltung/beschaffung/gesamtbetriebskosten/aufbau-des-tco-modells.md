---
title: Der Aufbau eines Modells der Gesamtbetriebskosten für ein CMS
audience: government, project-manager
category: public-sector
order: 20
summary: Wie Sie ein Modell der Gesamtbetriebskosten für eine Website oder ein Intranet auf Exponential aufbauen - der Zeithorizont, die Phasen, einmalige und wiederkehrende Kosten, interne und externe Kosten, Risiken und Ausstieg -, damit Angebote mit und ohne Lizenzgebühren auf derselben Grundlage verglichen werden.
keywords: Gesamtbetriebskosten, TCO, Kostenmodell, Lebenszykluskosten, Budget, Wirtschaftlichkeitsbetrachtung, einmalige Kosten, wiederkehrende Kosten, Vertragslaufzeit, Vergleich, Kosten von Open Source, total cost of ownership, cost model, lifecycle cost, business case, one-off costs, recurring costs, contract period, comparison, open source cost
type: help
---
# Der Aufbau eines Modells der Gesamtbetriebskosten für ein CMS

Diese Seite richtet sich an Projektleitungen und Finanzpersonal, die den Kostenvergleich für ein
Content-Management-System aufbauen. Ein Modell der Gesamtbetriebskosten (TCO) addiert jede Kostenart des Besitzes eines
Systems über einen festen Zeitraum, nicht nur den Kaufpreis. Das zählt bei freier Software wie Exponential: Es gibt
keine Lizenzgebühr, aber Umsetzung, Hosting, Personal, Support, Schulung, Upgrades und Ausstieg kosten trotzdem Geld.
Diese Seite gibt den Aufbau; die anderen Seiten dieses Ordners füllen jeden Teil. Sie nennt keine Preise: Alle Zahlen
stammen aus Ihren eigenen Angeboten und Aufzeichnungen.

## Inhalt
1. [Den Zeithorizont wählen](#den-zeithorizont-wählen)
2. [Die Phasen des Modells](#die-phasen-des-modells)
3. [Einmalige und wiederkehrende Kosten](#einmalige-und-wiederkehrende-kosten)
4. [Interne und externe Kosten](#interne-und-externe-kosten)
5. [Risiken und Reserven](#risiken-und-reserven)
6. [Alles zusammenführen](#alles-zusammenführen)
7. [Fallstricke](#fallstricke)

## Den Zeithorizont wählen

- Verwenden Sie den Zeitraum, den das System voraussichtlich lebt, meist länger als der erste Vertrag. Websites
  öffentlicher Stellen überdauern oft mehrere Lieferanten.
- Verwenden Sie für jedes Angebot denselben Zeithorizont.
- Beziehen Sie die Kosten des Ausstiegs am Ende des Zeithorizonts ein, damit ein Angebot mit hohen Ausstiegskosten nicht
  günstiger wirkt, als es ist (siehe den Ordner mit den Ausstiegsplänen).

## Die Phasen des Modells

| Phase | Was geschieht | Seiten, die die Arbeit erklären |
|---|---|---|
| Beschaffung | Ausschreibung, Bewertung, Proof of Concept | [Plan für einen Proof of Concept](../bewertung/plan-fuer-einen-proof-of-concept.md) |
| Umsetzung | Inhaltsmodell, Design, Templates, Integrationen, Migration alter Inhalte | [Leitfaden für Umsetzungsprojekte](../../../partner/leitfaden-fuer-implementierungsprojekte.md), [Übersicht zur Migration](../../../../installation/migration/README.md) |
| Livegang | Härtung, Tests der Barrierefreiheit, Schulung, Start | [Sicherheitshärtung](../../../sicherheitshaertung.md), [Checkliste zum Start](../../../../../eng-us/help/creators/website-creators/launch-checklist.md) |
| Betrieb | Hosting, Support, Sicherungen, Überwachung, redaktionelle Arbeit | [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) |
| Weiterentwicklung | Upgrades, neue Funktionen, Redesigns | [Aktualisieren](../../../../anleitungen/aktualisieren.md) |
| Ausstieg | Export, Übergabe, Übergang | [Exponential verlassen](../../../../installation/migration/exponential-verlassen.md) |

## Einmalige und wiederkehrende Kosten

| Einmalig | Wiederkehrend |
|---|---|
| Umsetzungsprojekt | Hosting und Infrastruktur |
| Migration der Inhalte | Vertrag über Support und Wartung |
| erstes Audit der Barrierefreiheit und Erklärung | Einspielen von Sicherheitsreleases und Upgrades |
| Penetrationstest vor dem Start | regelmäßige Wiederholungstests zu Barrierefreiheit und Sicherheit |
| erste Schulung | Schulung neuer Beschäftigter |
| Ausstieg und Übergang | interne Personalzeit (Webteam, Redaktion, Administratoren) |

## Interne und externe Kosten

Externe Kosten erscheinen in Angeboten; interne nicht, aber sie sind real. Zählen Sie:

- die Personalzeit von Webteam, Redaktion in den Abteilungen, IT-Betrieb, Sicherheit und Datenschutz;
- die Zeit für Bewertung und Abnahmetests;
- die Zeit für Leitung und Vertragsmanagement.

Schätzen Sie die interne Zeit in Stunden je Rolle und multiplizieren Sie sie mit Ihrem eigenen internen Satz. Siehe
[Personal und Kompetenzen](personal-und-kompetenzen.md).

## Risiken und Reserven

Fügen Sie eine Zeile für Risiken hinzu, die Sie benennen können, mit einer Wahrscheinlichkeit und einer Auswirkung, die
Sie selbst schätzen:

- ein Lieferant stellt den Geschäftsbetrieb ein (Kosten eines neuen Lieferanten und des Übergangs);
- ein großes Upgrade braucht mehr eigene Arbeit als geplant (eigene Templates und Erweiterungen sind die übliche Ursache,
  [Aktualisieren](../../../../anleitungen/aktualisieren.md));
- ein Sicherheitsvorfall (Reaktion, forensische Arbeit, Kommunikation).

## Alles zusammenführen

```text
TCO over <n> years = Acquisition + Implementation + Go-live
                   + sum over years (Operation + Evolution)
                   + Exit
                   + Risk allowance
```

Tragen Sie jede Zeile in die [Vorlage für ein TCO-Arbeitsblatt](vorlage-fuer-ein-tco-arbeitsblatt.md) ein. Verlangen es Ihre
Regeln, zinsen Sie künftige Kosten mit dem eigenen Satz Ihrer Organisation auf den Barwert ab.

## Fallstricke

- Eine Lizenzgebühr in einem Angebot mit „keiner Lizenzgebühr“ in einem anderen vergleichen und dort aufhören.
- Interne Personalzeit weglassen, weil sie „schon bezahlt“ ist.
- Die Kosten des Ausstiegs vergessen.
- Für verschiedene Angebote verschiedene Zeithorizonte verwenden.

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Kostenkategorien](kostenkategorien.md)
- [Beschaffung: Übersicht](../README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
