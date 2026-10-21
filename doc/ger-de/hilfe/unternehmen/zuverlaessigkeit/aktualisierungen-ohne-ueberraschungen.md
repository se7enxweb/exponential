---
title: Aktualisierungen ohne Überraschungen
audience: businesses, project-manager, admin, developer
category: business-solutions
order: 170
summary: Wie ein Unternehmen eine Aktualisierung von Exponential plant, probt und durchführt, sodass Kunden nur die Verbesserungen bemerken, mit den Phasen, den Rollen, den mitgelieferten Werkzeugen und den Entscheidungen, die vor dem Öffnen des Wartungsfensters zu treffen sind.
keywords: Aktualisierung, Upgrade, Update, Release, Probe, Wartungsfenster, Zurückrollen, mit Git aktualisieren, Changelog, Verhaltensänderungen, Upgrade-Prüfung, upgrade, upgrades, update, release, rehearsal, maintenance window, rollback, ezupdate, update with Git, changelog, behavior changes, upgrade check
type: help
---
# Aktualisierungen ohne Überraschungen

Diese Seite richtet sich an Inhaber und Führungskräfte, die Aktualisierungen genehmigen, und an die Projektleitung, die
sie plant. Sie erklärt, warum regelmäßige Aktualisierungen eine Maßnahme für Zuverlässigkeit sind, wie eine
Aktualisierung geplant und geprobt wird, welche mitgelieferten Werkzeuge von Exponential sie vorhersehbar machen und was
vor dem Öffnen des Wartungsfensters zu entscheiden ist. Der ausführliche Plan mit einer Checkliste zum Kopieren steht
unter [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md); die technischen Schritte stehen
unter [Upgrade](../../../anleitungen/aktualisieren.md).

## Inhalt

1. [Warum Aktualisieren eine Maßnahme für Zuverlässigkeit ist](#warum-aktualisieren-eine-maßnahme-für-zuverlässigkeit-ist)
2. [Wie oft](#wie-oft)
3. [Die Phasen einer Aktualisierung](#die-phasen-einer-aktualisierung)
4. [Die Werkzeuge, die sie vorhersehbar machen](#die-werkzeuge-die-sie-vorhersehbar-machen)
5. [Zwei Wege zum Aktualisieren: Kommandozeile oder Administration](#zwei-wege-zum-aktualisieren-kommandozeile-oder-administration)
6. [Vor dem Wartungsfenster entscheiden](#vor-dem-wartungsfenster-entscheiden)
7. [Die eine Regel](#die-eine-regel)
8. [Checkliste für das Unternehmen](#checkliste-für-das-unternehmen)

## Warum Aktualisieren eine Maßnahme für Zuverlässigkeit ist

Sicherheitskorrekturen erreichen Sie nur über das nächste 6.0.x-Release: „Ein veröffentlichter Tag wird nie geändert:
Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag erreicht Sie als nächstes Release, nicht als
korrigierte Kopie des alten“ ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Eine
Website, die nicht aktualisiert wird, sammelt nach und nach bekannte Probleme an, und ihre nächste Aktualisierung wird
größer und riskanter.

Kleine, regelmäßige Aktualisierungen sind das Gegenteil: Jede ist kurz, gut verstanden und leicht zu proben. Die
Planungsseite sagt es als Regel: „Behandeln Sie ‚auf die neueste 6.0.x aktualisieren‘ als wiederkehrende Aufgabe, nicht
als einmaliges Projekt“
([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#wozu-sich-das-projekt-verpflichtet)).

## Wie oft

Aus [Release- und Upgrade-Planung, entscheiden, wann aktualisiert wird](../../fuehrung/release-und-upgrade-planung.md#entscheiden-wann-ein-upgrade-erfolgt):

| Situation | Empfehlung |
|---|---|
| Für Ihre Linie gibt es ein Sicherheits-Release | Aktualisieren Sie im nächstmöglichen Wartungsfenster |
| Sie betreiben 6.0.x und liegen ein bis zwei Releases zurück | Planen Sie eine routinemäßige Aktualisierung |
| Sie betreiben 6.0.7 oder älter unter PHP 7.4 | Planen Sie zuerst den Wechsel von PHP; 6.0.8 braucht PHP 8.1 |
| Sie betreiben 5.x oder älter | Planen Sie ein Projekt ([Upgrade](../../../anleitungen/aktualisieren.md)) |
| Sie haben eigene Änderungen in Kernel-Dateien | Planen Sie Zeit ein, um sie zusammenzuführen |

Live-Websites sollten Release-Tags folgen; die Entwicklungslinie `main` „bewegt sich jeden Tag; ein Tag ändert sich
nie“ (dieselbe Seite). Siehe [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md).

## Die Phasen einer Aktualisierung

Die Planungsseite nennt elf Phasen; aus geschäftlicher Sicht lassen sie sich in vier Gruppen zusammenfassen
([Release- und Upgrade-Planung, Phase für Phase](../../fuehrung/release-und-upgrade-planung.md#der-upgrade-plan-phase-für-phase)):

| Gruppe | Phasen | Was das Unternehmen sieht |
|---|---|---|
| Vorbereiten | Bestandsaufnahme, Release Notes lesen, schätzen, Upgrade-Prüfung ausführen | Eine Liste der zu prüfenden Punkte, mit Verantwortlichen und einer Schätzung |
| Belegen | An einer Kopie proben, Redakteure nehmen die Kopie ab | Eine abgenommene Probe und eine **gemessene** Dauer |
| Durchführen | Ankündigen, sichern, innerhalb eines Wartungsfensters aktualisieren | Ein kurzes Fenster zu einer ruhigen Zeit |
| Bestätigen | Prüfen, Upgrade-Prüfung erneut ausführen, die neuen Versionen festhalten | Ein kurzer Bericht: was sich geändert hat, was geprüft wurde |

Die Probe ist es, die Überraschungen beseitigt. „Ihr eigenes Fenster hängt von Ihrer Website ab; die Probe in Phase 5
sagt Ihnen, wie lange es wirklich dauert. Schreiben Sie diese gemessene Zeit in den Plan, keine Schätzung“ (dieselbe
Seite).

## Die Werkzeuge, die sie vorhersehbar machen

| Werkzeug | Was es für das Unternehmen tut | Lesen |
|---|---|---|
| Changelogs und Hinweise zu Verhaltensänderungen | Sagen, was sich geändert hat und was zu prüfen ist, je Release und Zeitraum | [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md#wo-sie-nachlesen-was-sich-geändert-hat) |
| **Setup > Systeminformationen** | Hält die Versionen vorher und nachher fest; warnt bei PHP und Einstellungen | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| **Setup > Upgrade-Prüfung** | Vergleicht Dateien und Datenbank mit dem Release, nur lesend | [Upgrade-Prüfung und Dateikonsistenz](upgrade-pruefung-und-dateikonsistenz.md) |
| Wartungsmodus | Eine saubere 503-Seite, während die Arbeit läuft | [Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md) |
| `exp:velocity deploy` | Führt das Neuladen des Codes und die Cache-Schritte in der richtigen Reihenfolge aus, stoppt beim ersten Fehler | [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md) |
| Der Audit-Trail | Zeichnet das Wartungsfenster, Deployments und Änderungen an Einstellungen auf | [Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md) |
| `ezupdate` | Sucht nach Updates und führt Composer aus der Administration aus, mit Probelauf | [Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md) |

## Zwei Wege zum Aktualisieren: Kommandozeile oder Administration

Eine Website aus Git-Klonen lässt sich auf der Kommandozeile oder aus der Administration aktualisieren
([Mit Git aktualisieren](../../mit-git-aktualisieren.md#kommandozeile-oder-administration)):

| | Kommandozeile | Administration |
|---|---|---|
| Für | Administratoren mit einer Shell auf dem Server | Administratoren ohne Shell |
| Kernel | `git merge` des neuesten Release-Tags oder eines anderen Branches | **„Git Manager“** zieht einen Branch; einen Release-Tag kann er nicht zusammenführen |
| Erweiterungen | `composer update` | **„Updates and packages“** führt Composer im Hintergrund aus, zuerst mit **Vorschau**; ausgeschaltet, bis `[UpdateSettings] AllowUpdate` eingeschaltet wird |
| Neuladen des Codes | `exp:velocity deploy` auf einer Velocity-Website | Cache-Verwaltung; Velocity neu starten kann sie nicht |

Die Zusammenfassung der Seite: Die Kommandozeile „kann alles“, während die Administration „eine routinemäßige
Aktualisierung einer Website abdeckt, die einem Branch folgt … ohne eine Shell zu öffnen, ein Release-Tag, ein
Merge-Konflikt und der Neustart von Velocity aber weiterhin die Kommandozeile brauchen“.

Zur Automatisierung: `ezupdate` „hat keinen eigenen Zeitplan, der eine Website unbeaufsichtigt aktualisiert, und die
mitgelieferten Schalter lassen Aktualisieren und Installieren ausgeschaltet, bis ein Administrator sie einschaltet“
([Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md#was-es-tut)). Die
Seite zum Aktualisieren mit Git beschreibt ein Skript zur Selbstaktualisierung für Websites, die sich dafür entscheiden
([Lassen Sie sie sich selbst aktualisieren](../../mit-git-aktualisieren.md#automatisch-aktualisieren-lassen)); für eine geschäftliche
Live-Website ist eine geprobte Aktualisierung in einem Wartungsfenster der sicherere Standard.

## Vor dem Wartungsfenster entscheiden

- **Go oder No-Go**: Wer entscheidet bis wann, dass das Fenster stattfindet.
- **Zurückrollen**: Wer entscheidet bis wann über ein Zurückrollen. „Ein Zurückrollen heißt ‚das Backup
  wiederherstellen‘ für die Datenbank und ‚den vorherigen Tag auschecken‘ für den Code; es ist nur möglich, wenn das
  Backup getestet wurde“
  ([Release- und Upgrade-Planung, wenn etwas schiefgeht](../../fuehrung/release-und-upgrade-planung.md#wenn-etwas-schiefgeht)).
- **Kommunikation**: Wer Kunden sagt, dass die Website zurück ist oder dass das Fenster verlängert wird.
- **Redaktionsstopp**: Redakteure veröffentlichen während des Fensters nicht und wissen, wann sie wieder anfangen dürfen.
- **Bereitschaft**: Wer am ersten Tag nach der Aktualisierung erreichbar ist.

## Die eine Regel

„**Führen Sie nie einen Installer über eine bestehende Website aus.** Der Setup-Assistent, der Kickstarter und
`exp:install` legen eine neue Datenbank an. Eine Aktualisierung ersetzt den Code und aktualisiert die bestehende
Datenbank an Ort und Stelle“
([Release- und Upgrade-Planung, die eine Regel](../../fuehrung/release-und-upgrade-planung.md#die-eine-regel)).
Nehmen Sie diese Regel in die Einweisung aller auf, die den Server anfassen.

## Checkliste für das Unternehmen

- [ ] Die Aktualisierung auf die neueste 6.0.x ist ein wiederkehrender Punkt im Kalender.
- [ ] Jede Aktualisierung hat eine verantwortliche Person, eine Probe an einer Kopie und eine gemessene Dauer.
- [ ] Redakteure haben die geprobte Kopie abgenommen.
- [ ] Das Fenster liegt in einer ruhigen Zeit, ist angekündigt und durch ein frisches, getestetes Backup abgesichert.
- [ ] Für die Entscheidungen über Go/No-Go und Zurückrollen gibt es benannte Verantwortliche.
- [ ] Die Upgrade-Prüfung wurde vorher und nachher ausgeführt, und die Berichte sind aufbewahrt.
- [ ] Die neuen Versionen sind im Übergabedokument festgehalten.

Die vollständige technische Checkliste zum Kopieren in ein Ticket steht unter
[Release- und Upgrade-Planung, Upgrade-Checkliste](../../fuehrung/release-und-upgrade-planung.md#upgrade-checkliste).

## Verwandte Seiten

- [Upgrade-Prüfung und Dateikonsistenz](upgrade-pruefung-und-dateikonsistenz.md)
- [Upgrade](../../../anleitungen/aktualisieren.md)
- [Budget für Aktualisierungen und Wartung](../kosten/budget-fuer-aktualisierungen-und-wartung.md)
- [Kunden-Websites aktualisieren (Hosting)](../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
