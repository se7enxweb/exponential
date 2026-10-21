# Gesammelte Informationen: Formulare, Umfragen und was Besucher gesendet haben

Diese Anleitung erklärt das Sammeln von Informationen in Exponential und die Seiten **Setup > Gesammelte
Informationen** (`/infocollector/overview`): welche Inhalte sammeln, die Übersicht mit ihren Kennzahlen, Suche,
Sortierung und Blättern, das Lesen der Sammlungen eines Formulars, ihr Export als CSV, ihr Entfernen und was zu tun
ist, wenn sich etwas nicht wie erwartet verhält.

Sie richtet sich an Administratoren und Redakteure, die Formulare betreiben. Jede Seite, jedes Feld und jede Regel
unten wurde am 6. Oktober 2026 am Code dieses Repositorys geprüft; die Dateien sind unter [Quellen](#quellen)
genannt.

[Anleitungen](README.md) · Siehe auch: [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md) ·
[Sicherheit und Audit](sicherheit-und-audit.md) · [Blättern in Listen](../../eng-us/features/6.0/admin-list-paging.md)

## Kurz gesagt

- Ein Objekt **sammelt**, wenn seine Klasse Attribute hat, die als **Informationssammler** markiert sind (ein
  Feedbackformular, eine Umfrage, ein Formular zur Kontaktgewinnung). Jedes Mal, wenn ein Besucher das Formular
  absendet, wird eine **Sammlung** gespeichert, mit einem Wert pro Sammler-Attribut.
- **Setup > Gesammelte Informationen** listet die Objekte auf, die gesammelt haben, mit Kennzahlen, einer Suche nach
  Namen, Sortierung und Blättern. Jede Karte verlinkt auf die Sammlungen des Objekts, auf das Objekt und auf einen
  **CSV-Export**.
- Wenn nichts (oder wenig) gesammelt wurde, listet die Seite die veröffentlichten Objekte auf, die sammeln **können**,
  damit Sie wissen, wo Sie nachsehen müssen.
- Entfernen löscht nach einer Bestätigung jede Sammlung der markierten Objekte (oder die markierten Sammlungen eines
  Objekts). Die Objekte und ihre Formulare bleiben. Exportieren Sie zuerst, wenn die Daten noch gebraucht werden.
- Für alles ist die Richtlinie `infocollector/read` nötig.

## 1. Die Übersicht

| Teil | Was er zeigt |
|---|---|
| Kennzahlen | Objekte mit Sammlungen, Sammlungen insgesamt, Sammlungen der letzten 30 Tage und der letzten 7 Tage, das Datum der neuesten Sammlung und (rot, nur wenn es welche gibt) Objekte, die entfernt wurden oder im Papierkorb liegen und deren Sammlungen nicht aufgeführt werden |
| Objekt finden | Ein Teil des Objektnamens; **Liste aktualisieren** wendet ihn an, **Suche löschen** entfernt ihn. `%` und `_` stehen für sich selbst. Die Suche wird in Ihrer Sitzung gespeichert. |
| Sortieren nach | Name (A bis Z), Sammlungen, letzte Sammlung, erste Sammlung (neueste bzw. größte zuerst), Typ; die aktuelle Sortierung erneut zu wählen kehrt sie um. Die Sortierung steht in der Adresse (`/(sortby)/collections/(order)/desc`). |
| Die Karten | Name (ein Link auf die Sammlungen), Klassensymbol und -name, die Anzahl der Sammlungen und wie viele davon in den letzten 30 Tagen eingingen, erste und letzte Sammlung, Objekt-ID; Schaltflächen **Sammlungen**, **CSV exportieren**, **Objekt** |
| Pro Seite und Seiten | Die Größen aus `admininterface.ini [PaginationSettings] ItemsPerPageList_infocollector_overview[]` (10, 25 und 50, wenn nicht gesetzt) und die Seitennavigation |
| Objekte, die sammeln können, aber noch nichts haben | Bis zu zehn veröffentlichte Objekte von Sammler-Klassen ohne Sammlungen, mit ihrer Klasse und ID, auf der ersten Seite angezeigt, wenn die Liste kürzer als eine Seite ist |

Unter MongoDB ist die Liste nach Namen sortiert und hat weder Kennzahlen noch Suche; alles andere ist gleich.

## 2. Sammlungen lesen und exportieren

**Sammlungen** öffnet `infocollector/collectionlist/<object id>`: eine Zeile pro Absendung, die jeweils
`infocollector/view/<collection id>` mit jedem Wert öffnet.

**CSV exportieren** lädt `infocollector/export/<object id>` als `<name>-collected.csv` herunter (UTF-8 mit
Byte-Order-Mark, damit Tabellenkalkulationen Akzente richtig lesen): die ID der Sammlung, wann sie gesendet und
geändert wurde (UTC, `2026-10-06 14:05:00 UTC`), die ID des Benutzers, der sie gesendet hat (bei Besuchern der
anonyme Benutzer), dann eine Spalte pro Informationssammler-Attribut in der Reihenfolge der Klasse. Ein Wert ist sein
Text oder, wenn er keinen Text hat, seine Zahl. Werte, die eine Tabellenkalkulation als Formel lesen würde (`=`, `+`,
`-`, `@` am Anfang), beginnen mit einem Apostroph. Höchstens 50 000 Sammlungen, die neuesten zuerst. Jeder Export
wird im Audit als `data.export.csv` mit der Anzahl der Zeilen festgehalten, nie mit den Werten.

Der Export enthält, was Besucher eingegeben haben, oft Namen und E-Mail-Adressen. Bewahren Sie die Datei dort auf, wo
personenbezogene Daten hingehören, und löschen Sie sie, wenn sie nicht mehr gebraucht wird.

## 3. Sammlungen entfernen

Markieren Sie in der Übersicht Objekte und wählen Sie **Ausgewählte entfernen**. Die Bestätigung sagt, wie viele
Sammlungen verschwinden, listet die Objekte mit ihren Anzahlen auf und sagt, dass die Objekte und ihre Formulare
bleiben. **Entfernen** löscht; **Abbrechen** ändert nichts. Die Meldung danach sagt, wie viele Sammlungen von wie
vielen Objekten entfernt wurden.

Markieren Sie in der Sammlungsliste eines Objekts Sammlungen und wählen Sie **Ausgewählte entfernen**: dieselbe
Bestätigung, nur für diese Sammlungen.

Entfernungen werden im Audit als `data.infocollection.remove` festgehalten. Die Namen der Schaltflächen und Felder
sind die früherer Versionen (`RemoveObjectCollectionButton`, `ObjectIDArray[]`, `ConfirmRemoveButton`,
`CancelButton`), und jedes Formular enthält das Formular-Token.

## 4. Probleme

| Symptom | Ursache und Lösung |
|---|---|
| Ein Formular wurde gesendet, aber nichts erscheint | Das Klassenattribut ist nicht als **Informationssammler** markiert, oder das Formular-Template sendet nicht mit `ActionCollectInformation` an `content/action`. Das Objekt steht dann nicht unter „Objekte, die sammeln können“. |
| Ein Objekt fehlt in der Liste | Es hat keinen Hauptort (entfernt oder im Papierkorb). Die rote Kennzahl zählt solche Objekte; stellen Sie das Objekt aus dem Papierkorb wieder her, um seine Sammlungen wieder zu sehen. |
| Die CSV hat eine leere Spalte | Das Attribut wurde zum Sammler gemacht, nachdem diese Sammlungen gesendet wurden, oder seine Werte haben keinen Text und die Zahl 0. |
| In der CSV fehlt eine Spalte | Das Attribut ist in der Klasse kein Sammler mehr; nur aktuelle Sammler-Attribute werden exportiert. |

## Quellen

- Views: `kernel/private/classes/views/infocollector/overview.php` (`Overview`), `.../export.php` (`Export`),
  `.../collectionlist.php`, `.../view.php`; Moduldefinition `kernel/infocollector/module.php` (Richtlinie
  `infocollector/read`).
- Templates (design/admin und design/admin4): `infocollector/overview.tpl`, `infocollector/confirmremoval.tpl`,
  `infocollector/exp_style.tpl`.
- CSV-Zellen: `kernel/classes/subitems/expsubitemscsvexport.php` (`cell()`, `writeLine()`).
- Einstellungen: `admininterface.ini [PaginationSettings] ItemsPerPageList_infocollector_overview[]`.
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
