---
title: Zu vermeidende Lock-in-Klauseln und zu verlangende Klauseln
audience: government, project-manager, partner
category: public-sector
order: 110
summary: Vertragsbedingungen, die eine öffentliche Stelle an einen einzigen Lieferanten einer Exponential-Website binden - Eigentum am eigenen Code, geschlossene Erweiterungen, zurückgehaltene Sicherungen und Schlüssel, private Forks, Ausstiegsgebühren - und Musterformulierungen, die die Freiheit erhalten, die die GPL und die offenen Exportformate bereits geben. Vorlagentext, keine Rechtsberatung.
keywords: Lock-in, Anbieterabhängigkeit, Vertragsklauseln, Ausstiegsklausel, Eigentum am Quellcode, Hinterlegung, GPL, geistiges Eigentum, Datenrückgabe, Unterstützung bei Vertragsende, Portabilität, lock-in, vendor lock-in, contract clauses, exit clause, source code ownership, escrow, intellectual property, data return, termination assistance, portability
type: help
---
# Zu vermeidende Lock-in-Klauseln und zu verlangende Klauseln

Diese Seite richtet sich an Beschaffungsstellen und das Vertragsmanagement. Die Lizenz und die Exportwerkzeuge von
Exponential machen einen Lieferantenwechsel technisch möglich; ein Vertrag kann diese Freiheit trotzdem nehmen. Diese
Seite listet Bedingungen auf, die Sie vermeiden sollten, und gibt Musterformulierungen zum Anpassen.

> **Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Womit Sie beginnen](#womit-sie-beginnen)
2. [Zu vermeidende Klauseln](#zu-vermeidende-klauseln)
3. [Zu verlangende Klauseln](#zu-verlangende-klauseln)
4. [Musterformulierungen](#musterformulierungen)
5. [Einen Vertragsentwurf prüfen](#einen-vertragsentwurf-prüfen)

## Womit Sie beginnen

- Der Kernel steht unter der GNU GPL Version 2 oder neuer: Jeder darf ihn ausführen, untersuchen, ändern und
  weitergeben ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).
- Erweiterungen und Designs, die ein Lieferant an Sie weitergibt, sind abgeleitete Werke und fallen mit ihrem Quellcode
  unter die GPL, wie die Fragen zur Lizenzierung erklären; eine Agentur, die im Auftrag für Sie baut, handelt als Ihre
  Beauftragte.
- Inhalte lassen sich in einfachen Formaten exportieren ([Formate für den Datenexport](formate-fuer-den-datenexport.md)).

Der Vertrag sollte das bestätigen, nicht einschränken.

## Zu vermeidende Klauseln

| Klausel | Warum sie Sie bindet |
|---|---|
| Der Lieferant besitzt den eigenen Code und gewährt Ihnen nur eine Nutzungslizenz | ein anderer Lieferant darf ihn rechtlich nicht ändern |
| Eigene Erweiterungen ohne Quellcode oder verschleiert geliefert | niemand sonst kann sie pflegen |
| Sicherungen, Einstellungen und Schlüssel nur beim Lieferanten | Sie können ohne seine Mitwirkung nicht umziehen |
| Änderungen in einem privaten Fork des Kernels | jedes Upgrade hängt vom Lieferanten ab ([Kosten für Upgrades und Wartung](../gesamtbetriebskosten/kosten-fuer-upgrades-und-wartung.md)) |
| Daten werden „in einem vom Lieferanten gewählten Format“ zurückgegeben | Sie erhalten womöglich etwas, das niemand lesen kann |
| Unterstützung beim Ausstieg zu Sätzen, die im Vertrag nicht festgelegt sind | der Preis des Ausstiegs wird festgelegt, wenn Sie keine Wahl haben |
| Daten werden bei Kündigung sofort gelöscht | Sie verlieren womöglich Daten, bevor Sie Ihre Kopie geprüft haben |
| Hosting mit Support gebündelt, nicht trennbar | Sie können das eine nicht ohne das andere wechseln |
| Ausschließliches Recht, Änderungen an der Website vorzunehmen | andere Lieferanten sind ausgeschlossen |

## Zu verlangende Klauseln

1. **Eigentum oder volle Rechte** an eigenem Code, Dokumentation und Designs, die für Sie erstellt wurden, geliefert in
   Git mit Historie, unter der GPL, soweit sie auf Exponential aufbauen.
2. **Keine Änderungen an mitgelieferten Kernel-Dateien**; Anpassungen nur über Erweiterungen und Overrides.
3. **Rückgabe an das Projekt** allgemeiner Korrekturen, wo möglich ([Code zurückgeben](../../../partner/code-zurueckgeben.md)).
4. **Regelmäßige Lieferung** vollständiger Sicherungen, Einstellungen und Schlüssel an Sie ([Alles sichern](alles-sichern.md)).
5. **Benannte Exportformate** beim Ausstieg (Website-Archiv, Datenbank-Dump, Speicher, Layouts, Formulardaten,
   Einwilligung, Audit).
6. **Unterstützung beim Ausstieg** für einen festen Zeitraum nach der Kündigung, zu festen Sätzen.
7. **Löschung** erst nachdem Sie den Empfang bestätigt haben, mit schriftlicher Bestätigung.
8. **Trennbare Leistungen**: Hosting, Support und Entwicklung lassen sich getrennt beenden.
9. **Dokumentation** als Lieferung zu jedem Meilenstein ([Übergabe der Dokumentation](uebergabe-der-dokumentation.md)).

## Musterformulierungen

```text
1. Rights. All software, templates, designs and documentation created for the Customer under this contract are
   delivered to the Customer with their complete source code. Where they are derivative works of Exponential they are
   licensed to the Customer under the GNU General Public License version 2 or later. <Add ownership terms per your
   rules.>
2. Repository. The Supplier keeps all such code in a Git repository to which the Customer has read access at all
   times, and delivers a complete copy with history at each milestone and at the end of the contract.
3. Kernel. The Supplier makes no changes to files shipped with Exponential; customization is done in extensions
   and design overrides.
4. Data return. On request and at the end of the contract, the Supplier delivers within <n> working days: <list of
   formats from the exit plan>. The Supplier deletes the Customer's data only after the Customer has confirmed
   receipt in writing, and confirms deletion in writing.
5. Exit assistance. For <period> after termination, the Supplier provides exit assistance at <rate>, up to <hours>.
6. Separability. Hosting, support and development services may be terminated separately.
```

## Einen Vertragsentwurf prüfen

- [ ] Wem gehört der eigene Code, oder wer hält die Rechte daran? Wird der Quellcode geliefert?
- [ ] Ist ein privater Fork oder eine Änderung am Kernel erlaubt?
- [ ] Werden Sicherungen, Einstellungen und Schlüssel an Sie geliefert?
- [ ] Sind Exportformate benannt?
- [ ] Sind Sätze und Dauer der Unterstützung beim Ausstieg festgelegt?
- [ ] Lassen sich Leistungen getrennt beenden?
- [ ] Ist die Löschung an Ihre Bestätigung gebunden?

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Vorlage für einen Ausstiegsplan](vorlage-fuer-einen-ausstiegsplan.md)
- [Bewertung: Warnzeichen und Fallstricke](../bewertung/warnzeichen-und-fallstricke.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
