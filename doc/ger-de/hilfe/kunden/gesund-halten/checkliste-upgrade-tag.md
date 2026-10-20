---
title: Checkliste für den Upgrade-Tag
audience: customer, editor, project-manager
category: customer-success
order: 5
summary: Eine Checkliste für den Website-Betreiber und das Redaktionsteam für die Tage vor, den Tag und den Tag nach einem Upgrade einer Exponential-Website - Ankündigungen, die Frage nach dem Backup, die zu testenden Seiten und was festzuhalten ist.
keywords: Upgrade-Tag, Checkliste, Wartungsfenster, Testen, Smoke-Test, Ankündigung, Zurücksetzen, Redaktion, upgrade day, checklist, maintenance window, testing, smoke test, announcement, rollback, editors
type: help
---
# Checkliste für den Upgrade-Tag

Diese Checkliste ist für den Website-Betreiber und die Redaktion am Tag eines Upgrades. Die technischen Schritte gehören
der Person, die das Upgrade durchführt ([Upgrade](../../../anleitungen/aktualisieren.md), [Mit Git aktualisieren](../../mit-git-aktualisieren.md));
dies sind die Schritte auf Ihrer Seite, damit das Upgrade geplant, geprüft und festgehalten wird.

## Eine Woche vorher

- [ ] Datum, Startzeit und erwartete Dauer sind mit der Person vereinbart, die das Upgrade durchführt.
- [ ] Sie kennen die alte und die neue Version ([Release-Linie und Support](release-linie-und-support.md)).
- [ ] Sie haben eine Zusammenfassung dessen erhalten, was sich für die Redaktion und für Besucher ändert.
- [ ] Das Upgrade wurde auf einer Kopie der Website ausprobiert, oder Sie haben das Risiko akzeptiert, es nicht zu tun.
- [ ] Für das Zeitfenster ist keine Kampagne, kein Newsletter-Versand und keine Aktion geplant
      ([Planung für Lastspitzen](../../../../en/us/help/businesses/reliability/peak-traffic-planning.md)).
- [ ] Sie haben entschieden, was geschieht, wenn es fehlschlägt: zum Backup zurückkehren oder vorwärts korrigieren.

## Einen Tag vorher

- [ ] Die Redaktion kennt das Zeitfenster. Während des Fensters veröffentlichte Inhalte müssen möglicherweise erneut geprüft werden.
- [ ] Besucher sind informiert, falls die Website eine Wartungsseite zeigen wird; die Seite kann eine Nachricht und das
      voraussichtliche Ende tragen ([Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)).
- [ ] Ihre Testliste ist fertig (siehe unten).
- [ ] Ihre [Eskalationskontakte](../partner-und-hoster/vorlage-eskalationskontakte.md) sind aktuell.

## Ihre Testliste

Halten Sie sie kurz, fünf bis zehn Punkte, und immer gleich, damit ein Unterschied auffällt. Beispielpunkte:

| Prüfung | Gutes Ergebnis |
|---|---|
| Die Startseite | Lädt, mit Bildern |
| Eine tief liegende Seite, etwa ein Artikel | Lädt, mit ihren Bildern und Links |
| Die Suche der Website | Findet eine bekannte Seite ([Suchen](../../suchen.md)) |
| Anmeldung in der Administration | Das Dashboard öffnet sich ([In der Administration anmelden](../../anmelden-in-der-administration.md)) |
| Ein Testelement bearbeiten und veröffentlichen | Es erscheint auf der Website ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)) |
| Ein Bild hochladen | Es wird gespeichert und angezeigt ([Medien und Uploads](../../medien-und-uploads.md)) |
| Ein Formular, etwa Kontakt oder Anmeldung | Es lässt sich absenden |
| Der Shop, falls Sie einen haben | Eine Produktseite lädt, der Warenkorb funktioniert |
| Jede Sprache oder Website, die Sie betreiben | Ihre Startseite lädt |

Ersetzen Sie die Beispiele durch die Seiten, die für Ihr Geschäft wichtig sind.

## Am Tag selbst

- [ ] Die Person, die das Upgrade durchführt, bestätigt, dass das Backup von Datenbank und Dateien direkt davor erstellt
      wurde ([Backups](backups.md)).
- [ ] Melden Sie während des Zeitfensters nichts zu Seiten, die die Wartungsseite zeigen; das ist erwartet.
- [ ] Wenn Ihnen mitgeteilt wird, dass die Website wieder da ist, gehen Sie Ihre Testliste durch.
- [ ] Melden Sie jeden Unterschied sofort, mit Seitenadresse, Uhrzeit und gegebenenfalls einer Fehlerreferenz
      ([Probleme melden](probleme-melden.md)).

## Am Tag danach

- [ ] Sie haben die Nachweise erhalten: die neue Version, das Ergebnis der Upgrade-Prüfung und alles, was noch zu tun ist
      ([Upgrades mit Ihrem Anbieter](upgrades-mit-ihrem-anbieter.md)).
- [ ] Die Redaktion meldet alles, was sich anders verhält.
- [ ] Das Upgrade ist in Ihren Unterlagen vermerkt: Datum, alte und neue Version, wer es durchgeführt hat, aufgetretene Probleme.

> **Hinweis**
> Wenn nach einem Upgrade etwas nicht stimmt, hat der technische Leitfaden eine Tabelle mit Symptomen und Maßnahmen,
> einschließlich „das Backup aus Schritt 1 wiederherstellen“, wenn ein Datenbank-Update auf halbem Weg fehlgeschlagen ist
> ([Upgrade, wenn etwas schiefgeht](../../../anleitungen/aktualisieren.md)). Überlassen Sie die Entscheidung über das Zurücksetzen
> dem Betreiber und die Durchführung der Person, die das Upgrade gemacht hat.

## Verwandte Seiten

- [Wie Updates funktionieren](wie-updates-funktionieren.md)
- [Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)
- [Upgrades ohne Überraschungen](../../../../en/us/help/businesses/reliability/upgrades-without-surprises.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
