# Meine Entwürfe und meine ausstehenden Elemente

Diese Anleitung erklärt zwei Seiten des Dashboards der Administration: **Meine Entwürfe** (`/content/draft`), die
Versionen, die Sie begonnen und nicht veröffentlicht haben, und **Meine ausstehenden Elemente**
(`/content/pendinglist`), die Versionen, die Sie zur Veröffentlichung geschickt haben und die ein Workflow, meist eine
Freigabe, noch zurückhält, sowie die Versionen, die auf Ihre Freigabe warten.

Sie richtet sich an Redakteure und Freigebende. Jede Seite, jedes Feld und jede Regel unten wurde am 6. Oktober 2026
am Code dieses Repositorys geprüft; die Dateien sind unter [Quellen](#quellen) genannt.

[Anleitungen](README.md) · Siehe auch: [Versionen eines Objekts](inhaltsverlauf.md) ·
[Bearbeitungszugriff auf nie veröffentlichte Objekte](../../en/us/bc/6.0/draft-edit-access.md) ·
[Workflows](workflows.md) · [Blättern in Listen](../../en/us/features/6.0/admin-list-paging.md)

## Kurz gesagt

- **Ihre Entwürfe gehören Ihnen.** Nur Sie sehen sie auf dieser Seite, und die Seite entfernt nur Ihre eigenen
  Entwürfe: Jeder Entwurf wird im Moment des Entfernens erneut geprüft, gleichgültig, was die Anfrage nennt.
- Der Entwurf eines **neuen Objekts** (nie veröffentlicht) nimmt das neue Objekt mit, wenn er entfernt wird. Der
  Entwurf eines veröffentlichten Objekts lässt die veröffentlichte Version unverändert.
- Alte Entwürfe lassen sich in einem Schritt entfernen: **Alte Entwürfe entfernen**, nach 7, 30, 90 oder 365 Tagen
  ohne Änderung.
- Eine ausstehende Version zeigt, **was sie zurückhält** (die Freigabe, ihren Zustand und die Freigebenden, die
  Workflows), und verlinkt auf die Freigabe, wenn Sie daran beteiligt sind. Eine Version, die auf Ihre Freigabe wartet,
  wird nur aufgeführt, wenn Sie sie lesen dürfen (`content/versionread`).
- Filter, Suche, Reihenfolge und Seite werden in der Adresse festgehalten. Alles funktioniert ohne JavaScript.

## 1. Meine Entwürfe

| Teil | Was er zeigt |
| --- | --- |
| Kennzahlen | Ihre Entwürfe, die davon zu neuen Objekten, die seit 30 Tagen (oder dem gewählten Alter) nicht geänderten, Übersetzungen, Klassen |
| Finden | Eine Suche über den Namen, den Ort und die Klasse (`?q=`) |
| Filter | Übersetzung und Klasse (wenn es mehr als eine gibt), Alter (beliebig, 7, 30, 90, 365 Tage), Reihenfolge (zuletzt geändert, älteste, Name, Klasse) |
| Karten | Name, Klasse, Ort (oder „Neu, wird unter ... veröffentlicht“), Übersetzung, Version, erstellt, geändert und das Alter; **Bearbeiten**, **Ansehen** (mit `content/versionread`) und **Entfernen**, direkt an Ort und Stelle bestätigt |
| Untere Leiste | **Ausgewählte entfernen** (die Bestätigung nennt die markierten Entwürfe), **Alte Entwürfe entfernen** (wählen Sie das Alter; die Anzahl wird angezeigt), **Alle entfernen**, jeweils direkt an Ort und Stelle bestätigt |
| Blättern | 10, 25 oder 50 pro Seite (`admininterface.ini [PaginationSettings]`, Schlüssel `content/draft`; Ihre Wahl wird als Präferenz gespeichert) |

Ein Entfernen meldet, wie viele Entwürfe verschwunden sind. Nennt eine Anfrage eine Version, die keiner Ihrer Entwürfe
ist (den eines anderen, eine veröffentlichte Version), bleibt sie unverändert, und die Seite sagt das.

## 2. Meine ausstehenden Elemente

| Teil | Was er zeigt |
| --- | --- |
| Kennzahlen | Von Ihnen geschickt, wartet auf Ihre Freigabe, von einer Freigabe gehalten |
| Filter | Alle zeigen, von Ihnen geschickt oder wartet auf Ihre Freigabe; Klasse; Reihenfolge (neueste, am längsten wartend, Name) |
| Karten | Name, der Zustand ihrer Freigabe, Klasse, Ort, Übersetzung und Version, wer sie wann geschickt hat, was sie zurückhält; **Ansehen** (mit `content/versionread`), **Freigabe öffnen** oder **Freigabe und Kommentare** (wenn Sie an der Freigabe beteiligt sind), **Veröffentlichte Seite** |

Eine Version, die kein Freigabe- oder Workflow-Prozess mehr zurückhält, die aber noch ausstehend ist, sagt das:
Bitten Sie einen Administrator, sich **Setup > Workflow-Prozesse** anzusehen.

## Probleme

| Symptom | Ursache und Lösung |
| --- | --- |
| Ein erwarteter Entwurf fehlt | Er wird möglicherweise herausgefiltert (prüfen Sie die Adresse) oder ist älter als der Altersfilter, oder er gehört jemand anderem |
| „... sind keine Entwürfe von Ihnen und blieben, wie sie sind“ | Die Anfrage nannte Versionen, die nicht Ihre Entwürfe sind; davon wurde nichts entfernt |
| Eine Version, die auf Ihre Freigabe wartet, wird nicht aufgeführt | Sie dürfen sie nicht lesen: Die Freigabe erfordert `content/versionread` für sie |

## Quellen

- Views: `kernel/private/classes/views/content/draft.php`, `kernel/private/classes/views/content/pendinglist.php`
- Listen: `kernel/classes/expcontentdraftlist.php` (`removable()` entscheidet, was entfernt werden darf),
  `kernel/classes/expcontentpendinglist.php`
- Templates: `design/admin4/templates/content/draft.tpl`, `pendinglist.tpl` (dasselbe in `design/admin`); das
  Aussehen steht in den Abschnitten zu Historie und Entwürfen von `stylesheets/modules/content.css`
- Test: `tests/tests/kernel/classes/expContentDraftListTest.php` (ohne Datenbank)
