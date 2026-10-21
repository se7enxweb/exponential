# Bearbeitungszugriff auf Objekte, die nie veröffentlicht wurden (6.0.15)

Ein Objekt, das nie veröffentlicht wurde (ein neues Objekt, dessen erste Version noch ein Entwurf ist oder von einem
Freigabe-Workflow abgelehnt wurde), hat noch keine Knoten. Mehrere Bearbeitungsprüfungen behandelten ein solches
Objekt so, als könne es nur in Version 1 vorliegen und als habe es überhaupt keinen Ort. In 6.0.15 lesen sie den Ort,
an dem das Objekt veröffentlicht werden wird: den Elternknoten der Hauptknotenzuweisung seiner aktuellen Version
(`eZContentObject::draftParentNodeIDArray()`, `draftMainNodeAssignment()`).

Diese Seite ist für Administratoren, die Rollen und Freigabe-Workflows pflegen, und für Erweiterungen, die die
Zugriffsprüfungen aufrufen.

## Wer jetzt was bearbeiten darf

| Wer | Vorher | Jetzt |
| --- | --- | --- |
| Jemand mit **Anlege**-Zugriff unter dem geplanten Elternknoten (ohne Bearbeitungsrichtlinie oder ohne passende), bei **Version 2 oder später** des Objekts | verweigert | darf bearbeiten |
| Jemand mit einer **Bearbeitungs**-Richtlinie, die per **Subtree** auf den Teilbaum eingeschränkt ist, in dem das Objekt veröffentlicht werden wird (ein Freigebender) | verweigert, außer Besitzer | darf bearbeiten |
| Jemand, dessen Rolle mit **edit** **für diesen Teilbaum zugewiesen** ist (User_Subtree, „mit Einschränkung zuweisen: Teilbaum“) | verweigert, außer Besitzer | darf bearbeiten |
| Dieselben Personen für **read**, **remove**, **pdf**, **diff**, **versionread** und die anderen Funktionen | verweigert | verweigert (unverändert) |
| Eine Bearbeitungsrichtlinie oder Zuweisung für einen **anderen** Teilbaum | verweigert | verweigert (unverändert) |
| Der Besitzer des Entwurfs | erlaubt | erlaubt (unverändert, wird zuerst geprüft) |
| Veröffentlichte Objekte | | unverändert |

Die Regel für das Anlegen galt schon vorher, aber nur bei Version 1. Freigabe-Workflows bearbeiten eine abgelehnte
erste Version als Version 2 (content/edit erzeugt eine neue Version), daher konnte sie ab dann nur noch der Besitzer
oder jemand mit einer uneingeschränkten Bearbeitungsrichtlinie bearbeiten. Die Regel gilt jetzt bei jeder Version, und
überall, wo sie ausgeschrieben war, wird dieselbe Regel verwendet: `eZContentObject::checkAccess()`,
`eZContentObjectTreeNode::checkAccess()` (der Vorschauknoten von content/versionview),
`eZContentObjectVersion::checkAccess()` und content/removeeditversion. Sie liest die **Haupt**-Knotenzuweisung (bisher
die erste), und ohne Knotenzuweisung oder Elternobjekt verweigert sie, statt in „Call to a member function attribute()
on null“ zu enden. Der gemeinsame Code ist `eZContentObject::draftCreateAccess()`.

Der Entwurf eines anderen bleibt für das Lesen verschlossen: Eine Leserichtlinie für den Teilbaum zeigt keine Entwürfe
in `content/history`, `ezjscnode::load` oder einem Fetch. Um eine Version zu bearbeiten, muss man weiterhin ihr
Ersteller sein (`eZContentObjectVersion::checkAccess( 'edit' )` ist unverändert); ein Freigebender, der die Version
eines anderen bearbeitet, erhält die übliche Wahl, eine Kopie als neue Version zu bearbeiten.

## Die Versionen eines solchen Objekts (`content/history`)

Seit dem 6. Oktober 2026 öffnet sich `content/history` für alle, die das Objekt **bearbeiten** dürfen, nicht nur für
alle, die es lesen dürfen, sodass die Bearbeiter eines Entwurfs (ein Freigebender mit einer auf einen Teilbaum
eingeschränkten Bearbeitungsrichtlinie, ein weiterer Bearbeiter, den eine Erweiterung zulässt) ihre nächste Version aus
einer abgelehnten erzeugen können. Die Seite zeigt dann nur, was ein solcher Bearbeiter sehen darf:

| Auf der Seite | Wer bearbeiten, aber nicht lesen darf | Wer lesen darf (mit einer Bearbeitungsrichtlinie für irgendeinen Inhalt, wie bisher) |
| --- | --- | --- |
| Öffnen der Seite | ja (`History::canOpen()`, mit `eZContentObject::editAccess()` und dem Filter `content/edit/access`) | ja |
| Die Liste: Nummer, Status, Übersetzung, Ersteller, Daten jeder Version | ja | ja |
| Vergleichen und Kopieren der **veröffentlichten**, **archivierten** und eigenen Versionen | ja | ja |
| Vergleichen und Kopieren einer **abgelehnten** Version eines anderen | ja, um daraus die nächste Version zu erzeugen | nur mit `content/versionread` dafür |
| Vergleichen und Kopieren des **Entwurfs** oder einer **wartenden** Version eines anderen | nur mit `content/versionread` dafür | nur mit `content/versionread` dafür |
| Der Link auf `content/versionview` einer Version | nur, wenn `content/versionread` diese Version erlaubt | ebenso |
| Versionen entfernen | Entwürfe, archivierte, abgelehnte und unberührte Entwürfe, mit `content/versionremove` | ebenso |

Ein Hinweis oben auf der Seite sagt einem Bearbeiter, der das Objekt nicht lesen darf, was er dort tun kann. Ein
Vergleich, eine Kopie oder ein Entfernen, das eine Anfrage für eine Version verlangt, die der Benutzer nicht sehen oder
entfernen darf, wird nicht ausgeführt, und die Seite sagt, welche Versionen ausgelassen wurden
(`design:content/history_access_messages.tpl`); ein Debug-Hinweis nennt Benutzer und Versionen. Das Kopieren beginnt
den neuen Entwurf in einer der eigenen Übersetzungen der Version.

Die Richtlinienfunktionen der View sind `read or edit` (bisher `read` und `edit`), sodass ein Benutzer mit einer
Bearbeitungsrichtlinie und ohne Leserichtlinie die View erreicht und die View für das Objekt entscheidet. Ein Leser
ohne jede Bearbeitungsrichtlinie, etwa der anonyme Benutzer, bleibt wie bisher draußen. Die Entscheidungen sind
`History::canOpen()`, `History::canSeeVersionContent()` und `History::isRemovableStatus()` in
`kernel/private/classes/views/content/history.php`; die Templates erhalten `content_versions` (die Nummern der
Versionen, deren Inhalt der Benutzer sehen darf), `can_read` und `refused`.

## Woran eine Website es merkt

- **Freigebende mit auf einen Teilbaum eingeschränkten Editor-Rollen** erhalten einen funktionierenden Link
  „Bearbeiten“ an einem wartenden Element im Posteingang der Zusammenarbeit (`content/edit/<object>/<version>`), auch
  für Objekte, die nie veröffentlicht wurden. Hat sich eine Website darauf verlassen, dass Freigebende solche Entwürfe
  *nicht* bearbeiten können, geben Sie der Rolle des Freigebenden kein `content/edit` für diesen Teilbaum (die Freigabe
  braucht nur `content/read` und `content/versionread`).
- **Editoren mit Anlege-Zugriff** können die Entwürfe neuer Objekte ab Version 2 öffnen und verwerfen
  (`content/removeeditversion`), wie sie es bei Version 1 konnten. Eine Website, die die Versionsnummer als Schutz
  verwendet hat („nach der ersten Ablehnung nur noch der Besitzer“), verliert diesen Schutz; es gibt keine Einstellung
  dafür.
- `canEdit()` / `can_edit` kann für solche Objekte in Templates jetzt für diese Benutzer wahr sein, sodass Links und
  Schaltflächen erscheinen, die davon abhängen. Die Aktionen Bearbeiten und Veröffentlichen von
  `content/versionview` verlangen weiterhin auch den Ersteller der Version.
- Für Objekte, die einmal veröffentlicht wurden, ändert sich nichts, ebenso wenig für anonyme Benutzer (sie haben
  standardmäßig keinen Anlege-Zugriff).

## Erweiterungen

Die hier genannten Bearbeitungsprüfungen sind die Antwort des Kernels. Eine Erweiterung kann diese Antwort für ihre
eigenen Regeln ändern (weitere Bearbeiter eines Entwurfs, durch einen Zustand gesperrte Objekte), mit einem Listener
des Filters `content/edit/access`, den jede Bearbeitungsprüfung von `content/edit`, `content/history`,
`content/removeeditversion`, `content/versionview`, `content/multiedit` und der REST-Schnittstelle fragt: siehe
[Filter für Zugriff und Ansichts-Cache](../../funktionen/6.0/zugriffs-und-cache-filter.md).

## Prüfen

- Ohne Datenbank: `tests/tests/kernel/classes/eZContentObjectDraftLocationTest.php` (die Wahl der Hauptzuweisung, der
  geplante Elternknoten, die Regel für das Anlegen mit Stellvertreter-Objekten).
- Auf einer laufenden Installation: `tests/tests/kernel/classes/eZContentObjectDraftAccessLiveTest.php` legt eigene
  Benutzer (nie veröffentlicht, Adressen unter `example.invalid`), Rollen und Entwürfe an und entfernt sie wieder. Ohne
  Installation wird der Test übersprungen.
- Die Versionsgeschichte: `tests/tests/kernel/classes/eZContentHistoryAccessTest.php` (ohne Datenbank) und
  `tests/tests/kernel/classes/eZContentHistoryAccessLiveTest.php` (eigene Benutzer, Rollen und Entwürfe, wieder
  entfernt).
- Mit einer Testdatenbank: die Tests `@group database` in `tests/tests/kernel/classes/eZContentObjectTest.php`.
