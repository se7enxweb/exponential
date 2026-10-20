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
  ([Datenbankunterstützung](../../../en/us/help/database-support.md)).
- Verwenden Sie nur Testkonten; nie echte Kundendaten oder die laufende Website des Kunden.
- Setzen Sie die Umgebung zwischen Schulungen zurück.

## 3. Ablaufvorlagen

### Redakteure, ein Tag

| Zeit | Thema | Hilfeseite |
|---|---|---|
| 09:00 | Anmelden, die Administration | [Konten und Anmeldung](../../../en/us/help/accounts-and-sign-in.md), [Rundgang durch die Administration](../../../en/us/help/admin-tour.md) |
| 10:00 | Inhalte erstellen und bearbeiten | [Inhalte bearbeiten](../../../en/us/help/editing-content.md) |
| 11:30 | Bilder und Medien | [Bilder und Medien](../../../en/us/help/media-and-uploads.md) |
| 13:30 | Veröffentlichen und Versionen | [Veröffentlichen und Versionen](../../../en/us/help/publishing-and-versions.md) |
| 14:30 | Sprachen | [Sprachen](../../../en/us/help/languages.md) |
| 15:30 | Layouts | [Der Layouts-Editor](../../../en/us/help/layouts-editor.md) |
| 16:30 | Hilfe finden, Tastenkürzel | [Suchen](../../../en/us/help/searching.md), [Tastenkürzel](../../../en/us/help/keyboard-shortcuts.md) |

### Administratoren, ein Tag

| Thema | Quelle |
|---|---|
| Anforderungen und Installationswege | `doc/en/us/install/02-requirements.md` bis `07-console-install.md` |
| Die Website ausliefern, Engines | `doc/en/us/install/08-serving-the-site.md` |
| Sicherheitshärtung | [Sicherheitshärtung](../../../en/us/help/security-hardening.md) |
| Passwörter und Konten | [Das Administratorpasswort ändern](../../../en/us/help/change-the-admin-password.md) |
| Upgrade | `doc/en/us/install/11-upgrading.md` |
| Wenn etwas schiefgeht | [Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md) |

### Entwickler, zwei Tage

| Thema | Quelle |
|---|---|
| Git-Arbeitsweise | [Mit Git entwickeln](../../../en/us/help/develop-with-git.md) |
| Erweiterungen von Hand und mit den RAD-Werkzeugen | [Erweiterungen](../../../en/us/guides/extensions.md) |
| Eine Erweiterung veröffentlichen | [Erweiterungen, „Release it“](../../../en/us/guides/extensions.md#7-release-it) |
| Etwas zurückgeben | CONTRIBUTING.md, [Code zurückgeben](code-zurueckgeben.md) |

## 4. Übungen

Machen Sie jede Übung zu einer echten Aufgabe mit sichtbarem Ergebnis: „Veröffentlichen Sie einen Artikel in zwei
Sprachen mit einem Bild“, „Fügen Sie einer Erweiterung eine Einstellung hinzu und lesen Sie sie in einem Template“.
Geben Sie danach ein Lösungsblatt aus.

## 5. Material und Lizenzen

Material, das aus der Hilfe übernommen wurde, bleibt unter der GFDL 1.3 oder später: Behalten Sie die Hinweise und
geben Sie dieselben Freiheiten weiter ([Freie Dokumentation](../../../en/us/help/free-documentation.md)). Ihre eigenen Folien dürfen
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
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
