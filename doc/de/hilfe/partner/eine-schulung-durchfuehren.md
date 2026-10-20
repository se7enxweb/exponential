---
title: Eine Exponential-Schulung durchführen
audience: partner
category: partnership
order: 450
summary: Ein praktischer Leitfaden für Partner, die eine Exponential-Schulung durchführen - Ziele, Zielgruppe, Umgebung für die Teilnehmenden, Ablaufvorlagen für Redakteure, Administratoren und Entwickler, Übungen, Lizenzierung des Materials, Auswertung und Nachbereitung.
keywords: Schulung, Kurs, Ablauf, Übungen, Workshop, Redakteure, Administratoren, Entwickler, Umgebung, Auswertung, Material, GFDL, training course, agenda, exercises, workshop, editors, administrators, developers, environment, evaluation, material
type: help
---
# Eine Exponential-Schulung durchführen

Diese Seite ist für Trainer in Partnerunternehmen. Sie führt durch Vorbereitung, Durchführung und Nachbereitung einer
Schulung.

## 1. Ziel und Zielgruppe festlegen

Schreiben Sie einen Satz: „Nach dieser Schulung können [Zielgruppe] in Exponential ohne Hilfe [Aufgabe].“ Alles andere
folgt daraus. Trennen Sie die Zielgruppen: Redakteure, Administratoren und Entwickler brauchen unterschiedliche Schulungen.

## 2. Die Umgebung vorbereiten

- Eine Installation pro teilnehmender Person oder eine gemeinsame Installation mit einem Konto pro teilnehmender Person.
- Verwenden Sie das aktuelle Release der Linie, mit der die Teilnehmenden arbeiten werden.
- SQLite ist die Standarddatenbank und braucht keinen Datenbankserver, was Schulungsinstallationen einfach macht
  ([Datenbankunterstützung](../datenbankunterstuetzung.md)).
- Verwenden Sie nur Testkonten; nie echte Kundendaten oder die laufende Website des Kunden.
- Setzen Sie die Umgebung zwischen Schulungen zurück.

## 3. Ablaufvorlagen

### Redakteure, ein Tag

| Zeit | Thema | Hilfeseite |
|---|---|---|
| 09:00 | Anmelden, die Administration | [Konten und Anmeldung](../konten-und-anmeldung.md), [Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| 10:00 | Inhalte erstellen und bearbeiten | [Inhalte bearbeiten](../inhalte-bearbeiten.md) |
| 11:30 | Bilder und Medien | [Bilder und Medien](../medien-und-uploads.md) |
| 13:30 | Veröffentlichen und Versionen | [Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md) |
| 14:30 | Sprachen | [Sprachen](../sprachen.md) |
| 15:30 | Layouts | [Der Layouts-Editor](../layout-editor.md) |
| 16:30 | Hilfe finden, Tastenkürzel | [Suchen](../suchen.md), [Tastenkürzel](../tastenkuerzel.md) |

### Administratoren, ein Tag

| Thema | Quelle |
|---|---|
| Anforderungen und Installationswege | `doc/en/us/install/02-requirements.md` bis `07-console-install.md` |
| Die Website ausliefern, Engines | `doc/en/us/install/08-serving-the-site.md` |
| Sicherheitshärtung | [Sicherheitshärtung](../sicherheitshaertung.md) |
| Passwörter und Konten | [Das Administratorpasswort ändern](../administratorpasswort-aendern.md) |
| Upgrade | `doc/en/us/install/11-upgrading.md` |
| Wenn etwas schiefgeht | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) |

### Entwickler, zwei Tage

| Thema | Quelle |
|---|---|
| Git-Arbeitsweise | [Mit Git entwickeln](../mit-git-entwickeln.md) |
| Erweiterungen von Hand und mit den RAD-Werkzeugen | [Erweiterungen](../../anleitungen/extensions.md) |
| Eine Erweiterung veröffentlichen | [Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen) |
| Etwas zurückgeben | CONTRIBUTING.md, [Code zurückgeben](code-zurueckgeben.md) |

## 4. Übungen

Machen Sie jede Übung zu einer echten Aufgabe mit sichtbarem Ergebnis: „Veröffentlichen Sie einen Artikel in zwei
Sprachen mit einem Bild“, „Fügen Sie einer Erweiterung eine Einstellung hinzu und lesen Sie sie in einem Template“.
Geben Sie danach ein Lösungsblatt aus.

## 5. Material und Lizenzen

Material, das aus der Hilfe übernommen wurde, bleibt unter der GFDL 1.3 oder später: Behalten Sie die Hinweise und
geben Sie dieselben Freiheiten weiter ([Freie Dokumentation](../freie-dokumentation.md)). Ihre eigenen Folien dürfen
unter jeder Lizenz stehen, aber eine freie Lizenz hilft der Community und bringt als veröffentlichtes Material Punkte.

## 6. Auswertung und Nachbereitung

```text
Course evaluation
1. Can you now <goal task> without help?   yes / partly / no
2. What was most useful? ______________
3. What was missing? ______________
4. Pace: too slow / right / too fast
```

- Senden Sie den Teilnehmenden die Links zu den Hilfeseiten, die in der Schulung verwendet wurden.
- Erfassen Sie die Schulung für Punkte ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).
- Machen Sie aus wiederholten Fragen Verbesserungen der Dokumentation
  ([Dokumentation und Übersetzungen beitragen](dokumentation-und-uebersetzungen-beitragen.md)).

## Verwandte Seiten

- [Schulungspartner](schulungspartner.md)
- [Partner für Studierende und Bildung](partner-fuer-studierende-und-bildung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
