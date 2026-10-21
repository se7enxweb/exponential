---
title: Der Paketassistent
audience: admin, developer, designer
category: install-upgrade
order: 104
summary: Der neu gestaltete Assistent zum Erstellen von Paketen unter Setup > Pakete > Neues Paket erstellen - die vier Assistenten, die Schritte und ihre Schaltflächen, die Auswahlelemente, die Überprüfung, wohin das Paket geschrieben wird, der Einstieg aus dem Inhaltsbaum und wie seine Ausgabe in ein Seitenpaket einfließt.
keywords: Paketassistent, package/create, Neues Paket erstellen, Assistent starten, Schritt-Assistent, Fortfahren, Zurück, Abbrechen, Paket erstellen, Überprüfung, Export von Inhaltsklassen, Export von Inhaltsobjekten, Extension-Export, Seitenstil, Fortgeschritten > Exportieren, package wizard, Create new package, Start the wizard, step wizard, review, content class export, content object export, extension export, site style
type: help
---
# Der Paketassistent

Diese Seite ist für Administratoren und Entwickler, die Pakete im Browser erstellen. Seit Oktober 2026 ist der Assistent zum Erstellen von
Paketen (`/package/create`) ein Schritt-Assistent aus den gemeinsamen Komponenten der Administration: Er zeigt, wo Sie sind, fragt jeden Teil
in einer Karte ab, zeigt Fehler an ihren Feldern und lässt Sie das ganze Paket überprüfen, bevor es geschrieben wird. Was er sendet und was
er schreibt, hat sich nicht geändert. Die vollständige Beschreibung, mit jedem Template und jedem gesendeten Namen, ist
[Der Paketassistent](../../../funktionen/6.0/paketassistent.md); diese Seite ist die Kurzanleitung.

![Paket erstellen: die vier Assistenten](../../../../eng-us/help/images/help/sp-wizard.webp)

## Inhalt

1. [Die vier Assistenten](#die-vier-assistenten)
2. [Durch die Schritte gehen](#durch-die-schritte-gehen)
3. [Die Auswahlelemente](#die-auswahlelemente)
4. [Die gemeinsamen Schritte](#die-gemeinsamen-schritte)
5. [Die Überprüfung und das Ergebnis](#die-überprüfung-und-das-ergebnis)
6. [Aus dem Inhaltsbaum](#aus-dem-inhaltsbaum)
7. [Das Ergebnis in einem Seitenpaket nutzen](#das-ergebnis-in-einem-seitenpaket-nutzen)

## Die vier Assistenten

**Setup > Pakete > Neues Paket erstellen** listet die Assistenten auf, die Ihre Rolle nutzen darf:

| Assistent | Exportiert | Schritte |
|---|---|---|
| Export von Inhaltsklassen | Klassendefinitionen, keine Inhalte | Aufzunehmende Inhaltsklassen, Paket Informationen, Paket Betreuer, Paket-Changelog |
| Export von Inhaltsobjekten | Knoten oder Teilbäume mit ihren Inhalten, wahlweise Klassen und Templates | Objekte, die inkludiert werden sollen, Limits von Objekten, Paket Informationen, Paket Betreuer, Paket-Changelog |
| Extension-Export | die Dateien von Erweiterungen | Extensions, die inkludiert werden sollen, Paket Informationen, Paket Betreuer, Paket-Changelog |
| Seitenstil | zwei Stylesheets, Bilder und ein Vorschaubild | Paket Miniaturbild, CSS-Dateien, Bilddateien, Paket Informationen, Paket Betreuer, Paket-Changelog |

Wählen Sie einen und drücken Sie **Assistent starten**. Der Schritt für den Betreuer wird ausgelassen, wenn Ihre Rolle keine Betreuerrolle
vergeben darf. Der Extension-Export listet jede Erweiterung auf, lässt Sie aber nur die wählen, für die das Exportieren eingeschaltet ist
(`package.ini [ExtensionExportSettings]`), jede andere mit ihrem Grund.

## Durch die Schritte gehen

Jeder Schritt zeigt oben die Liste der Schritte (der aktuelle markiert, die erledigten abgehakt), „Schritt n von m“, seinen Namen und was er
abfragt.

| Schaltfläche | Tut |
|---|---|
| **Fortfahren** | prüft den Schritt und geht weiter; bei einem Fehler bleibt der Schritt, und eine Zusammenfassung oben verlinkt jeden Fehler mit seinem Feld |
| **Zurück** | behält, was Sie eingegeben haben (wenn gültig), und geht einen Schritt zurück; vom ersten Schritt zurück zur Wahl des Assistenten |
| **Abbrechen** | kehrt zur Paketliste zurück; nichts wird geschrieben |

Enter in einem Feld ist **Fortfahren**. Pflichtfelder werden vom Server geprüft, sodass die Meldungen die in der Sprache der Oberfläche
sind.

## Die Auswahlelemente

- **Inhaltsklassen**: eine Tabelle jeder Klasse mit Kontrollkästchen, Bezeichner und Gruppen, „n von m ausgewählt“ und einem Filter nach
  Name oder Bezeichner. Eine durch den Filter verborgene Klasse behält ihren Haken.
- **Inhaltsobjekte**: die gewählten Knoten und Teilbäume, jeweils mit Klasse und URL. **Knoten hinzufügen** und **Teilbaum hinzufügen**
  öffnen den Inhaltsbrowser; **Ausgewählte entfernen** nimmt angehakte Zeilen heraus.
- **Optionen** (Export von Inhaltsobjekten): Klassendefinitionen und Templates (mit den Siteaccesses, deren Templates mitgehen), Versionen,
  Sprachen und wie Knotenzuweisungen und verwandte Objekte behalten werden.
- **Extensions**: Version, letzte Änderung und Details pro Erweiterung.
- **Bilddateien** (Seitenstil): **Bild hinzufügen** fügt eine Datei hinzu und bleibt auf dem Schritt.

## Die gemeinsamen Schritte

| Schritt | Fragt ab | Geprüft |
|---|---|---|
| Paket Informationen | Name, Zusammenfassung, Beschreibung, Version, Format (klassisches XML oder JSON), Lizenz, Host, Paketierer | Name a-z, 0-9 und `_`, nicht vergeben; Zusammenfassung erforderlich; SemVer-Version mit höchstens 24 Zeichen; Lizenz aus der Liste |
| Paket Betreuer | Person, E-Mail, Rolle (`package.ini [MaintainerSettings] RoleList[]`: lead, developer, designer, contributor, tester) | Name und E-Mail erforderlich |
| Paket-Changelog | Person, E-Mail, Text | alle drei erforderlich |

## Die Überprüfung und das Ergebnis

Der letzte Schritt zeigt die **Überprüfen**: Name, Zusammenfassung, Version, Format, Lizenz, Host, Paketierer und Betreuer und was das
Paket enthält. **Paket erstellen** schreibt es ins Repository `local` und öffnet seine Seite, mit **Herunterladen**, **Prüfen**,
**Installieren** und den anderen Aktionen ([Die Paketliste und die Paketseite](die-paketliste-und-die-paketseite.md)).

Der Assistent schreibt nur ein Paket; die Website ändert sich nicht.

## Aus dem Inhaltsbaum

**Fortgeschritten > Exportieren** im Inhaltsbaum öffnet den Assistenten für Inhaltsobjekte mit dem bereits gewählten Knoten:
`/package/create/(creator)/ezcontentobject/(node)/<node id>`, wahlweise `(subtree)/1`, `(classes)/0|1` und `(versions)/current|all`
(`kernel/package/module.php`). Er füllt nur den Assistenten aus; das Paket entsteht, wenn Sie seine Schritte bestätigen. Siehe
[Das Exportmenü für Inhalte](../../../bc/6.0/menue-inhalte-exportieren.md).

## Das Ergebnis in einem Seitenpaket nutzen

Der Assistent erstellt die Pakete, die ein Seitenpaket verlangt, nie das Seitenpaket selbst (keiner der Ersteller schreibt den Typ
`site`). Um seine Ausgabe mit Ihrem eigenen Seitenpaket auszuliefern, laden Sie sie herunter, entpacken Sie sie in Ihr Quell-Repository,
geben Sie ihr einen `<vendor>` und nennen Sie sie in `<requires>` Ihres Seitenpakets:
[Pakete mit dem Assistenten erstellen](../eigene/pakete-mit-dem-assistenten-erstellen.md).

## Verwandte Seiten

- [Pakete in der Administration](README.md)
- [Der Paketassistent (Beschreibung der Funktion)](../../../funktionen/6.0/paketassistent.md)
- [Der Paketassistent Schritt für Schritt](../../designer/erweiterungen/paketerstellungs-assistent-schritt-fuer-schritt.md)
- [Berechtigungen für Pakete](berechtigungen-fuer-pakete.md)
- [Paketlizenzen und Versionen](../../../funktionen/6.0/paketlizenzen-und-versionen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
