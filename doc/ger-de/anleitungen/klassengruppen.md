# Klassengruppen: Klassen finden und eine Gruppe sicher entfernen

Diese Anleitung erklärt die Klassenseiten der Administrationsoberfläche (Setup > Klassen, `/class/grouplist`, die
Klassen einer Gruppe, eine Klasse und ihr Formular): was eine Klassengruppe ist, was jeder Teil der Seite aussagt und
wie Sie eine Gruppe entfernen, ohne Inhalte zu verlieren, die Sie behalten wollten.

Sie richtet sich an Administratoren, die das Inhaltsmodell pflegen. Jede Angabe unten wurde am 6. Oktober 2026 auf dem
Demonstrationsserver am Code geprüft (`kernel/private/classes/views/class/grouplist.php` und `removegroup.php`).

[Anleitungen](README.md) · Siehe auch: [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md),
[Blättern in Listen der Administration](../funktionen/6.0/seitenweise-listen-im-admin.md)

## Kurz gesagt

- Eine **Klasse** definiert eine Art von Inhalt. Klassen werden in **Gruppen** geführt (Content, Users, Media, Setup
  ...), damit sie leicht zu finden sind. Eine Klasse kann in mehr als einer Gruppe sein.
- Die Seite zeigt eine Übersicht (Gruppen, Klassen, veröffentlichte Objekte, Klassen in mehr als einer Gruppe,
  Klassen in keiner Gruppe), eine Suche über Gruppennamen, Klassennamen und Bezeichner und eine Karte pro Gruppe.
- Jede Karte listet die Klassen der Gruppe mit ihrer Anzahl veröffentlichter Objekte auf, wann sich die Gruppe oder
  eine ihrer Klassen zuletzt geändert hat, und **was das Entfernen der Gruppe entfernen würde**.
- Das Entfernen einer Gruppe entfernt **jede Klasse, die in keiner anderen Gruppe ist, mit allen ihren Objekten**. Die
  Karte nennt die Anzahl, bevor Sie die Gruppe markieren, und **Ausgewählte entfernen** fragt auf der Seite
  class/removegroup zuerst nach.

## 1. Die Übersicht

| Kennzahl | Was sie zählt |
|---|---|
| Gruppen | Klassengruppen |
| Klassen | Definierte Klassen |
| Veröffentlichte Objekte | Veröffentlichte Objekte aller Klassen |
| In mehr als einer Gruppe | Klassen, die mit zwei oder mehr Gruppen verknüpft sind |
| In keiner Gruppe | Klassen, die keine Gruppe aufführt (nur angezeigt, wenn es welche gibt): Sie sind nur über ihre ID oder ihren Bezeichner erreichbar |

## 2. Eine Gruppenkarte

Der Kopf zeigt das Symbol und den Namen der Gruppe (ein Link auf ihre Klassenliste), ihre ID, die Anzahl der Klassen
und Objekte sowie **Öffnen** und **Bearbeiten**. Darunter:

- die Klassen als Chips, nach Namen, jede mit ihrer Anzahl veröffentlichter Objekte und ihrem Bezeichner als Tooltip;
  eine Gruppe mit mehr als zwölf Klassen zeigt „und N weitere“, mit einem Link auf die Gruppe;
- **Letzte Änderung**: die neueste aus der eigenen Änderung der Gruppe und den Änderungen ihrer Klassen, mit dem Namen
  der Klasse;
- **Gruppe geändert**: wann und von wem die Gruppe selbst gespeichert wurde;
- **Beim Entfernen**: „Entfernt nur die Gruppe“, wenn jede Klasse auch in einer anderen Gruppe ist, oder, in Rot,
  wenn Objekte verschwinden würden, „Entfernt N Klassen und ihre M Objekte“, gefolgt davon, wie viele Klassen in
  ihren anderen Gruppen bleiben.

**Neue Klassengruppe** legt eine Gruppe an. Klassen werden innerhalb einer Gruppe angelegt: Öffnen Sie sie und
verwenden Sie dort Neue Klasse.

## 3. Eine Gruppe entfernen

1. Lesen Sie die Zeile **Beim Entfernen** der Karte.
2. Eine Klasse, die Sie behalten wollen, die aber nur in dieser Gruppe ist: Öffnen Sie sie, bearbeiten Sie sie und
   fügen Sie sie zuerst einer anderen Gruppe hinzu.
3. Markieren Sie die Gruppe und klicken Sie auf **Ausgewählte entfernen**. Die Bestätigungsseite listet jede Klasse,
   die entfernt wird, mit ihrer Anzahl Objekte auf. **OK** entfernt sie; **Abbrechen** führt zurück.

Nichts auf der Listenseite entfernt von sich aus etwas.

## 4. Die Klassen einer Gruppe

Öffnen Sie eine Gruppe (über ihren Namen oder **Öffnen**), um `/class/classlist/<group id>` aufzurufen:

- die ID der Gruppe und ihre letzte Änderung, mit **Zurück zu den Klassengruppen**, **Bearbeiten** (umbenennen) und
  **Entfernen** (die Bestätigung des Entfernens der Gruppe);
- Kennzahlen: Klassen, ihre veröffentlichten Objekte, Container; eine Suche über Namen und Bezeichner;
- **Neue Klasse** legt eine Klasse in dieser Gruppe an, in der daneben gewählten Sprache, und öffnet sie zur
  Bearbeitung;
- eine Zeile pro Klasse: Name und Symbol, Bezeichner, ID, Container, veröffentlichte Objekte, die anderen Gruppen, in
  denen sie ist („nur hier“, wenn keine), die letzte Änderung sowie **Ansehen**, **Bearbeiten** und **Kopieren**.
  Kopieren legt eine Kopie in denselben Gruppen an (`content.ini [CopySettings] ClassRedirect` bestimmt, wo Sie landen:
  im Bearbeitungsformular der Kopie, in der Liste, auf ihrer Seite oder in der Gruppenliste). Nur ein Formular legt
  eine Kopie an: Ein alter Link oder ein Lesezeichen auf `/class/copy/<id>` zeigt, was die Kopie sein wird, mit einer
  Schaltfläche **Kopieren**, und kopiert nichts, bevor diese geklickt wird;
- **Ausgewählte entfernen** öffnet die Bestätigung: pro Klasse, wie viele Objekte mit ihr verschwinden und deren
  Unterelemente, oder warum sie nicht entfernt werden kann. Eine Klasse, die auch in einer anderen Gruppe ist, verlässt
  nur diese.

## 5. Eine Klassenseite und das Klassenformular

Die Klassenseite (`/class/view/<id>`) beginnt mit **Bearbeiten** in der gewählten Sprache, dann folgen Kennzahlen
(Objekte, Attribute, Gruppen, Übersetzungen), die Einstellungen und eine Karte pro Attribut mit seinem Typ, seinen
Flags, seiner Kategorie, seiner Beschreibung und den Einstellungen seines Typs. Die Chips darunter blenden die
Klassengruppen (die Klasse einer Gruppe hinzufügen oder aus den markierten herausnehmen), die Override-Templates und
die Übersetzungen (ansehen, bearbeiten, die Hauptübersetzung festlegen, entfernen) ein oder aus.

Das Klassenformular hat **OK**, **Anwenden**, **Abbrechen** und **Attribut hinzufügen** in einer Leiste oben und noch
einmal am Ende. **Die Klasse** enthält Name, Bezeichner, Beschreibung, die Namensmuster für Objekte und URL-Aliase,
die Standardsortierung, Container und Standardverfügbarkeit, jeweils mit dem, was die Angabe bewirkt. Jedes Attribut
ist eine Karte: Pfeile für die Reihenfolge und Position, Name, Bezeichner, Beschreibung, Kategorie, die Flags
(erforderlich, durchsuchbar, Informationssammler, Übersetzung deaktivieren; die, die sein Typ nicht haben kann, sind
ausgegraut) und die Einstellungen seines Typs. Vor **OK** erreicht nichts die Objekte; **Abbrechen** verwirft den
Entwurf und kehrt zu der Seite zurück, von der aus das Formular geöffnet wurde. Eine Klasse in einer Sprache, die sie
noch nicht hat, fragt zuerst, welche Sprache hinzugefügt werden soll und von welcher aus begonnen wird.

## 6. Zuletzt geänderte Klassen

Unter den Gruppen die zehn zuletzt geänderten Klassen, mit ID, Bezeichner, Bearbeiter, Zeitpunkt, Objekten und
**Bearbeiten**.

## Quellen

- Modul und Views: `kernel/class/module.php`, `kernel/private/classes/views/class/grouplist.php`
  (`Grouplist::links()`, `classFacts()`, `overview()`, `summary()`), `removegroup.php`.
- Templates: `design/admin4/templates/class/grouplist.tpl`, `classlist.tpl`, `view.tpl` mit `windows.tpl`, `window_controls.tpl`, `groups.tpl`, `translations.tpl`, `templates.tpl`, `edit.tpl`, `select_language.tpl`, `edit_denied.tpl`, `groupedit.tpl`, `removeclass.tpl`, `removegroup.tpl`, `removetranslation.tpl`, `exp_style.tpl`, `exp_list_script.tpl` (dieselben Dateien
  in `design/admin/templates/class/`).
- Blättern: `settings/admininterface.ini [PaginationSettings] ItemsPerPage[class/grouplist]`.
- Tests: `tests/tests/kernel/classes/expAdminListsRedesignTest.php` (ohne Datenbank).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
