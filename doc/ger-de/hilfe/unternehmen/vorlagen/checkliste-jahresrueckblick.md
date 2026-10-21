---
title: "Checkliste: Jahresrückblick"
audience: businesses, project-manager, admin
category: business-solutions
order: 90
summary: Ein kopierfertiger jährlicher Rückblick auf eine Exponential-Website für Inhaber und Führungskräfte - Ziele und Zahlen, Kosten und ROI, Verfügbarkeit, Versionen und Updates, Sicherheit und Zugänge, Datenschutz und Aufbewahrung, Backups, Partner und Verträge sowie der Plan für das nächste Jahr.
keywords: Jahresrückblick, jährliche Prüfung, Checkliste, Governance, ROI, Kosten, Verfügbarkeit, Updates, PHP, Sicherheit, Zugriffsprüfung, Datenschutz, Aufbewahrung, Backups, Verträge, Roadmap, annual review, yearly review, checklist, costs, availability, security, access review, data protection, retention, contracts
type: help
---
# Checkliste: Jahresrückblick

Diese Seite ist für Inhaber und Führungskräfte, die einmal im Jahr einen Schritt zurücktreten und fragen, ob die Website
dem Geschäft noch dient, noch sicher ist und noch kostet, was geplant war. Sie fasst die jährlichen Aufgaben aus den
anderen Seiten dieses Bereichs in einer Besprechung zusammen. Planen Sie etwa zwei Stunden mit dem Inhaber der Website,
dem Administrator oder Partner und der Person ein, die für den Datenschutz zuständig ist.

> **Hinweis**
> Im Rückblick geht es um Ihre eigenen Zahlen und Entscheidungen. Wo rechtliche oder vertragliche Pflichten betroffen
> sind (Datenschutz, Barrierefreiheit, Buchhaltung), fragen Sie Ihre Berater; diese Checkliste ist keine Rechtsberatung.

## Inhalt

1. [Vor der Besprechung](#vor-der-besprechung)
2. [Die Checkliste](#die-checkliste)
3. [Festzuhaltende Ergebnisse](#festzuhaltende-ergebnisse)
4. [So verwenden Sie das Ergebnis](#so-verwenden-sie-das-ergebnis)

## Vor der Besprechung

Sammeln Sie:

- [ ] Die zwölf Monatsberichte ([Monatliche Betriebscheckliste](monatliche-betriebscheckliste.md#der-bericht-an-die-geschäftsleitung)).
- [ ] Rechnungen für Hosting, Support und Dienstleistungen des Jahres.
- [ ] Die Zahlen, die Sie als Ziele genannt haben (Umsatz, Anmeldungen, eingesparte Zeit), aus Ihren eigenen Systemen.
- [ ] Die letzte Zugriffsprüfung und den Aufbewahrungsplan.
- [ ] Notizen zu Vorfällen und Ausfallstunden.
- [ ] Kennzahlen, die die Website selbst liefert ([Kennzahlen aus Logs und Audit](../../fuehrung/kennzahlen-aus-logs-und-audit.md)).

## Die Checkliste

```text
JAHRESRÜCKBLICK - <Website>   Jahr: <Jahr>   Datum: <Datum>
Teilnehmer: <Namen und Rollen>

1. ZIELE UND ERGEBNISSE                                                 Status  Maßnahme
[ ] Ziele aus der Projektbeschreibung: erreicht / teilweise / nicht      ____    ______
[ ] Gemessene Zahlen mit dem Vorjahr verglichen                          ____    ______
[ ] Sind die Ziele für das nächste Jahr noch richtig?                    ____    ______

2. KOSTEN UND ROI
[ ] Ist-Kosten gegenüber Budget (einmalig, laufend, Dritte)              ____    ______
[ ] ROI-Arbeitsblatt mit gemessenen Zahlen aktualisiert                  ____    ______
[ ] Wartungsbudget für das nächste Jahr festgelegt                       ____    ______

3. VERFÜGBARKEIT UND VORFÄLLE
[ ] Ausfallstunden, geplant und ungeplant                                ____    ______
[ ] Vorfälle ausgewertet; Lehren umgesetzt                               ____    ______
[ ] Service-Levels von Hoster/Partner eingehalten                        ____    ______

4. VERSIONEN UND UPDATES
[ ] Website auf dem neuesten 6.0.x-Release                               ____    ______
[ ] PHP-Version noch unterstützt; sonst Upgrade geplant                  ____    ______
[ ] Erweiterungen aktuell; ungenutzte Erweiterungen entfernt             ____    ______
[ ] Eigener Code in Erweiterungen (Upgrade-Prüfung sauber)               ____    ______

5. SICHERHEIT UND ZUGÄNGE
[ ] Zugriffsprüfung durchgeführt und unterschrieben                      ____    ______
[ ] Zweistufige Anmeldung wie beschlossen eingerichtet                   ____    ______
[ ] Audit-Warnungen haben das ganze Jahr eine Person erreicht            ____    ______
[ ] Content Security Policy: noch report-only, wo enforce möglich ist    ____    ______
[ ] Mitarbeiter wissen, wie sie eine Sicherheitslücke vertraulich melden ____    ______

6. DATENSCHUTZ UND AUFBEWAHRUNG (mit Ihrem Berater)
[ ] Datenverzeichnis noch vollständig (neue Formulare, Klassen, Erw.?)   ____    ______
[ ] Aufbewahrungsfristen noch richtig; Einstellungen passen              ____    ______
[ ] Anfragen auf Auskunft oder Löschung bearbeitet; Ablauf funktioniert  ____    ______
[ ] Datenschutzhinweis und Mail-Fußzeile aktuell                         ____    ______

7. BACKUPS UND WIEDERHERSTELLUNG
[ ] Wiederherstellungstests in diesem Jahr durchgeführt (Daten: ____)    ____    ______
[ ] Aufbewahrung der Backups passt zum Aufbewahrungsplan                 ____    ______

8. BARRIEREFREIHEIT UND INHALTE
[ ] Barrierefreiheit auf Hauptseiten und Formularen geprüft              ____    ______
[ ] Veraltete Inhalte entfernt oder aktualisiert                         ____    ______

9. PARTNER UND VERTRÄGE
[ ] Leistung von Hoster und Partner bewertet                             ____    ______
[ ] Vertragsbedingungen, Preise und Ausstiegsregeln noch akzeptabel      ____    ______
[ ] Entscheidung über Verlängerung oder Neuausschreibung                 ____    ______

10. NÄCHSTES JAHR
[ ] Geplante Verbesserungen und ihr Budget                               ____    ______
[ ] Geplante Upgrades (PHP, Betriebssystem, Migrationen) und Zeitfenster ____    ______
[ ] Verantwortliche für jede Maßnahme benannt                            ____    ______

Freigegeben von: ______________   Nächster Rückblick: <Datum>
```

Wo jeder Teil erklärt wird:

| Teil | Seite |
|---|---|
| 1, 2 | [Rendite der Stabilität](../kosten/rendite-der-stabilitaet.md), [Vorlage: ROI-Arbeitsblatt](vorlage-roi-arbeitsblatt.md) |
| 3 | [Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md), [Störungsbehandlung für kleine Teams](../zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md) |
| 4 | [Budget für Aktualisierungen und Wartung](../kosten/budget-fuer-aktualisierungen-und-wartung.md), [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md) |
| 5 | [Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md), [Benutzerkonten und Passwörter](../sicherheit/benutzerkonten-und-passwoerter.md), [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md) |
| 6 | [Personenbezogene Daten in Exponential](../sicherheit/personenbezogene-daten-in-exponential.md), [Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md) |
| 7 | [Wiederherstellungsübungen](../zuverlaessigkeit/wiederherstellungsuebungen.md) |
| 8 | [Checklisten für Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md) |
| 9 | [Bewertungsbogen für Anbieter](bewertungsbogen-fuer-anbieter.md), [Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md) |

## Festzuhaltende Ergebnisse

```text
ERGEBNISSE DES JAHRESRÜCKBLICKS - <Website> - <Jahr>
Gesamtstatus: grün / gelb / rot
Drei wichtigste Erfolge: 1) ______ 2) ______ 3) ______
Drei größte Risiken: 1) ______ 2) ______ 3) ______
Entscheidungen: ______________________________________________
Maßnahmen (was - wer - bis wann):
1) ______ - ______ - ______
2) ______ - ______ - ______
3) ______ - ______ - ______
Budget für das nächste Jahr: einmalig ______ laufend ______
```

## So verwenden Sie das Ergebnis

- Senden Sie die Ergebnisse an die Geschäftsleitung und an Ihren Hoster oder Partner.
- Machen Sie aus den Maßnahmen Tickets mit Verantwortlichen und Terminen.
- Nutzen Sie die Risiken als Grundlage für das Budget und die Vertragsgespräche des nächsten Jahres.
- Wenn die Geschichte der Website es wert ist, erzählt zu werden, und die Fakten gemessen sind, denken Sie über eine
  Fallstudie nach, mit Freigabe ([Vorlage für Fallstudien](vorlage-fuer-fallstudien.md)).

## Verwandte Seiten

- [Monatliche Betriebscheckliste](monatliche-betriebscheckliste.md)
- [Bewertungsbogen Zuverlässigkeit](../zuverlaessigkeit/bewertungsbogen-zuverlaessigkeit.md)
- [Der Lesepfad für Führungskräfte](../../fuehrung/lesepfad-fuer-fuehrungskraefte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
