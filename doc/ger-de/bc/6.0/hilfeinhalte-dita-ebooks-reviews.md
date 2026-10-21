# Hilfe als Inhalt gepflegt: DITA-Export, gespeicherte E-Books, überfällige Reviews (6.0.15)

Drei Teile des Hilfe-Plans ([help-system.md](../../spezifikationen/6.0/hilfesystem.md)), die nach Phase 1 von
[Hilfe als Inhalt gepflegt](../../../eng-us/features/6.0/help-content.md) noch offen waren, sind in 6.0.15 enthalten.
Diese Seite beschreibt, was sich für eine Website ändert, die sie verwendet, und was nach dem Deploy zu tun ist.

## Was sich ändert

| Teil | Was er tut | Wo |
|---|---|---|
| DITA-Export | die Handbücher, Themen und das Glossar im Zustand Released als DITA-1.3-Bookmaps, DITAVAL-Dateien und Themen, nach dem Schreiben geprüft | `./console exp:help:content dita`; **DITA herunterladen** auf der Seite eines Handbuchs und **Alles als DITA herunterladen** auf der Liste der Handbücher |
| Gespeicherte E-Books | EPUB und PDF jedes Handbuchs im Zustand Released als Dateiobjekte unter E-Books (`help-ebooks`), eine neue Version pro neuem Build, neu gebaut, wenn ein Objekt der Hilfe auf Released gesetzt wird | `./console exp:help:content ebooks`; die Zuweisung des Zustands; die Seite des Handbuchs listet sie auf |
| Überfällige Reviews | Themen, deren Review-Datum überschritten ist oder die länger als `ReviewMaxDays` ohne Änderung im Zustand Review sind | das Dashboard der Hilfe, für die, die etwas dagegen tun können; `./console exp:help:content review` |

Nichts hiervon ändert eine Website, die die Hilfe als Inhalt nicht installiert hat (`exp:help:content install`): Das
Dashboard zeigt keine Liste, der Release-Hook kehrt sofort zurück, die Seiten der Handbücher existieren nicht.

## Verhaltensänderungen

- **Ein Release kann jetzt Inhalte anlegen.** Wird ein Objekt der Hilfe auf Released gesetzt (in der Administration
  oder über `eZContentOperationCollection::updateObjectState()`), werden die E-Books der Handbücher im Zustand
  Released gebaut, die es enthalten, und als Objekte der Klasse `file` unter E-Books veröffentlicht, als der Benutzer,
  der das Release ausgelöst hat. Die Objekte erhalten den Zustand Released und die Sektion `help_public` (öffentliches
  Handbuch) oder `help_internal`. Um Releases davon frei zu halten, setzen Sie
  `exphelp.ini [ContentSettings] EbooksOnRelease=disabled` und bauen die Bücher mit dem Befehl, wenn Sie sie brauchen.
- **Ein öffentliches E-Book lässt sich ohne Anmeldung herunterladen.** Das Buch eines Handbuchs mit öffentlicher
  Zielgruppe kommt in die Sektion `help_public` mit dem Zustand Released, die der anonyme Benutzer lesen darf (die
  eigene Richtlinie der Hilfe). Es enthält nur öffentliche Themen, Kapitel und Abbildungen im Zustand Released. Prüfen
  Sie die Zielgruppe Ihrer Handbücher vor dem ersten Build.
- **EPUB-Dateien werden nach ihrem Inhalt datiert.** Die Einträge eines EPUB und sein `dcterms:modified` sind die
  jüngste Änderung des Handbuchs und seiner Themen, nicht der Zeitpunkt des Builds. Zwei Builds desselben Inhalts
  ergeben dieselbe Datei.
- **Der Kernel ruft die Hilfe auf, wenn Zustände zugewiesen werden.**
  `eZContentOperationCollection::updateObjectState()` ruft nach dem Ereignis `content/state/assign`
  `expHelpContentEbooks::onStateAssign()` auf. Es kehrt sofort zurück bei Objekten, die nicht zur Hilfe gehören, bei
  anderen Zuständen als Released oder wenn die Einstellung ausgeschaltet ist.
- **Das Dashboard der Hilfe hat eine neue Karte** für Redakteure der Hilfe und Release-Manager. Andere sehen nichts
  Neues.

## Neue Einstellungen: exphelp.ini [ContentSettings]

| Einstellung | Standard | |
|---|---|---|
| `EbooksOnRelease` | `enabled` | ein Release baut die gespeicherten E-Books neu; `disabled`: nur der Befehl tut das |
| `ReviewMaxDays` | `14` | Tage, die ein Thema ohne Änderung im Zustand Review bleiben darf, bevor es überfällig ist |
| `ReviewListLimit` | `10` | höchstens so viele überfällige Themen auf dem Dashboard |

## Nach dem Deploy

Die neuen Klassen müssen im Autoload-Array stehen, und ein Server, der PHP laufen lässt (PHP-FPM, Exponential
Velocity), muss die geänderte Kernel-Datei `kernel/content/ezcontentoperationcollection.php` laden:

```
php bin/php/ezpgenerateautoloads.php -k        # or: ./console exp:velocity deploy --kernel
./console exp:help:content ebooks -s <admin siteaccess> --dry-run
./console exp:help:content ebooks -s <admin siteaccess>
./console exp:help:content review -s <admin siteaccess>
```

Es gibt keine Änderung an der Datenbank. Der erste Lauf von `ebooks` legt ein Dateiobjekt pro Handbuch und Format an;
ein zweiter Lauf gibt `No change.` aus. Ein Serverprozess, der vor dem Deploy gestartet wurde, zeigt keine
Review-Karte und bietet keinen DITA-Download an, bis er neu gestartet wird (die Klassen werden vorher nachgeschlagen;
nichts schlägt fehl).

Die Texte der Oberfläche stehen in den 24 Übersetzungskatalogen, die den Kontext der Hilfe haben (`kernel/exphelp`);
leeren Sie den Übersetzungs-Cache zusammen mit den anderen Caches.
