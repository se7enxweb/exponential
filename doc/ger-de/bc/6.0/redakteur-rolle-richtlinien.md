# Hinweis zum Aktualisieren: die alltäglichen Richtlinien der Rolle Editor

Ein Benutzer der Gruppe **Editors** hat die Richtlinien der Rolle **Editor**, bei den Website-Paketen auch die der
Rolle **Member**. Dieser Hinweis sagt, was ein Redakteur einer neuen Installation nicht tun konnte, was sich ändert,
und wie die Rolle einer bestehenden Installation vervollständigt wird.

## Was falsch war

Die Grunddaten geben der Rolle Editor `content/*` (mit `ezoe/*`, `user/login` und den Oberflächen-Auswahlen) und weisen
sie der Gruppe Editors zweimal zu, begrenzt auf die Teilbäume Content (`/1/2/`) und Media (`/1/43/`). Jedes
Website-Paket außer `plain_site` entfernt `content/*` und fügt stattdessen eine Liste einzelner Funktionen hinzu:
`content/create` begrenzt nach Klasse und Elternklasse, `content/edit`, `content/read` begrenzt auf die Sektionen
Standard, Restricted und Media, `content/remove`, `content/manage_locations`, `content/translate`, die Versionen,
Lesezeichen, die Liste der ausstehenden Objekte und das Dashboard; es weist der Gruppe Editors außerdem die Rolle Member
zu (eigenes Passwort, eigenes Konto, Benachrichtigungen).

Dieser Liste fehlen drei Funktionen, die ein Redakteur täglich braucht, und kein Paket gibt den Mehrfach-Upload frei:

| Was der Redakteur tut | Abgelehnt, weil | Seit |
| --- | --- | --- |
| Einen Knoten aus- oder einblenden (`content/hide/<node>`, Ausblenden und Einblenden im Knotenmenü) | kein `content/hide` | den ezwebin-Installationsprogrammen, aus denen die Pakete gemacht sind |
| In den Papierkorb sehen (`content/trash`) und ein Objekt wiederherstellen (`content/restore/<object>`) | kein `content/restore` | wie oben: Ein Redakteur konnte in den Papierkorb löschen, aber nichts zurückholen |
| Sehen, welche Objekte eines verwenden (`content/reverserelatedlist/<node>`), bevor er es verschiebt oder entfernt | kein `content/reverserelatedlist` | wie oben |
| Mehrere Dateien auf einmal in die Medienbibliothek hochladen (`ezmultiupload/upload/<node>`, Hochladen im Dashboard der AdminUI) | keine Richtlinie für das Modul `ezmultiupload` | die Pakete haben ezmultiupload ohne Richtlinie dafür installiert |
| Einen Artikel mit Karte (`geo_article`) oder ein Video (`video`) anlegen | die Klassenlisten sind älter als diese beiden Klassen des Klassenpakets | wie oben |
| Ein Bild im Bearbeitungsformular bearbeiten (die Schaltfläche **Edit image** von expimageeditor, das an die Stelle von ezie tritt) | kein `expimageeditor/edit`: Die Schaltfläche wird nur für einen Benutzer gezeichnet, der es hat, Redakteure sahen also gar keinen Bildeditor | expimageeditor (Pull-Request #302 im Hauptrepository); das Modul von ezie brauchte `ezie/*`, das ebenfalls kein Paket vergab |

Bei `plain_site`, das die Rolle der Grunddaten behält und keine Rolle Member zuweist, konnte ein Redakteur das eigene
Passwort nicht ändern (`user/password`), das eigene Konto nicht bearbeiten (`user/edit`, Funktion *selfedit*) und die
Einstellungen der Benachrichtigungen nicht öffnen (`notification/use`).

## Was sich ändert

- **Der Setup-Assistent und der Kickstarter** prüfen nach den Rollen des Website-Pakets, dass die Rolle Editor
  `content/hide`, `content/restore`, `content/reverserelatedlist`, `ezmultiupload/*`, `expimageeditor/edit` und,
  wo das Website-Paket ezie behält, `ezie/*` hat (jeweils, wenn die neue Installation die Erweiterung aktiviert; seit
  dem 30. Oktober 2026 zählt eine nur installierte Erweiterung nicht mehr, `ezie/*` wird also nicht vergeben, wo
  expimageeditor an seine Stelle tritt), und `user/password`, `user/selfedit` und `notification/use`,
  sofern keine andere Rolle der Gruppe Editors (die Rolle Member der Website-Pakete) sie hat; was fehlt, wird
  hinzugefügt (`expEditorPolicies::ensure()` in `kernel/setup/steps/ezstep_create_sites.php`). Das gilt für jedes
  Website-Paket und alle Grunddaten, auf jeder Datenbank-Engine.
- **Die Website-Pakete** `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` und
  `cjw_multisite` fügen die vier Richtlinien in ihrem Editor-Schritt selbst hinzu und lassen Redakteure `geo_article`
  und `video` unter einem Ordner anlegen (mit der Website-Werkzeugleiste für beide).
- **`bin/php/addeditorpolicies.php --apply`** vervollständigt die Rolle bei einer bestehenden Installation (unten;
  ohne `--apply` zeigt es nur, was es ändern würde).
- **`content/*` von plain_site wird eingeengt** (24. Oktober 2026). Bevor die Richtlinien oben geprüft werden, ersetzt
  der Setup-Schritt ein `content/*` ohne Begrenzungen der Rolle Editor, das `plain_site` aus den Grunddaten behält,
  durch die Inhaltsfunktionen, die die Website-Pakete ihren Redakteuren geben: `create`, `edit`, `read`,
  `manage_locations`, `diff`, `versionread`, `versionremove`, `remove`, `translate`, `bookmark`, `pendinglist`,
  `dashboard`, `view_embed`, `hide`, `restore` und `reverserelatedlist` (`expEditorPolicies::narrowPlan()` und
  `narrow()`). Sie werden ohne Begrenzungen hinzugefügt, die Zuweisung der Rolle begrenzt sie also wie zuvor
  `content/*` auf Content und Media. Ein Redakteur von `plain_site` verliert, was `content/*` darüber hinaus gab: den
  Papierkorb aller endgültig leeren (`cleantrash`), die Sprachen der Installation (`translations`), ihre URL-Aliase und
  Platzhalter (`urltranslator`), die Inhaltsjobs aller (`jobs`) sowie `move`, `publish`, `pdf`, `tipafriend`,
  `view_system_url` und `publish_without_notification`, nach denen keine Ansicht eines Redakteurs fragt. Die
  Grunddaten bleiben unverändert: Die anderen Pakete entfernen `content/*` selbst und finden nichts einzuengen, und ein
  `content/*` mit Begrenzungen ist die Entscheidung der Website und bleibt, wie es ist.

Die Richtlinien kommen in die Rolle Editor, die Zuweisung der Rolle begrenzt sie also wie den Rest: für die Gruppe
Editors auf die Teilbäume Content und Media. Ausblenden und die Liste der verwendenden Objekte funktionieren nur dort.

## Was bleibt, und warum

- **Den Papierkorb leeren** (`content/cleantrash`) entfernt die gelöschten Objekte aller endgültig; Redakteure stellen
  wieder her, ein Administrator leert.
- **Benutzer**: Die Rolle Editor liest und bearbeitet nichts unter Users (`/1/5/`); Redakteure ändern ihr eigenes Konto
  über `user/edit` und `user/password`. Die Mitglieder der Website verwaltet die Rolle "Member management", die der
  Setup-Schritt aus `membermanagement.ini` anlegt und der Gruppe Editors zuweist, begrenzt auf Members, damit Redakteure
  nie die Konten der Administratoren und Redakteure erreichen ([Mitgliederverwaltung](mitgliederverwaltung.md)).
- **Tags, Newsletter, Layouts, Objektzustände**: Keine Klasse des Standard-Klassenpakets hat ein Tags-Feld, die
  redaktionellen Ansichten des Newsletters sind eigene Rollen von cjw_newsletter, der Layout-Editor (`explayouts_ui`)
  und das Zustandsmodul sind im Editor-Siteaccess abgeschaltet, und die einzige Zustandsgruppe einer neuen Installation
  ist die Sperrgruppe.
- **Der Git-Manager und das Update-Dashboard** bleiben bei den Administratoren: Ihre Module prüfen eigene Richtlinien
  (`git_manager/git_manager`, `update/ezupdate`), das Dashboard zeigt seine Karte "Stay secure" nur einem Benutzer, der
  sie öffnen kann (`fetch( 'user', 'can_open', ... )`), und der Editor-Siteaccess schaltet den Git-Manager außerdem ab.

## ezie antwortet "Modul nicht gefunden"

Eine Website, die expimageeditor an Stelle von ezie aktiviert (der Setup-Assistent und der Kickstarter tun das seit
Oktober 2026), beantwortet jede Adresse `ezie/...` mit dem Kernel-Fehler 20, "Modul nicht gefunden", für jeden
Benutzer, auch für Administratoren. Das ist so gewollt und kein Fehler der Rolle: ezie bleibt installiert
(`composer.json` verlangt es), aber seine `module.ini` (`ExtensionRepositories[]=ezie`) wird nur gelesen, solange die
Erweiterung in `ActiveExtensions[]` steht, das Modul liegt also nicht auf dem Modulpfad. Keine Seite einer solchen
Website fragt nach ezie: Die Bildbearbeitungsvorlage von admin4 öffnet ezie nur, wenn `ezmodule( 'ezie/prepare' )` es
erlaubt, und expimageeditor ersetzt diese Vorlage. Um ezie wieder zu verwenden, aktivieren Sie es an Stelle von
expimageeditor und führen für die Redakteure `php bin/php/addeditorpolicies.php --apply --only=ezie/*` aus.

## Zu weite Rechte, die das Skript meldet

`bin/php/addeditorpolicies.php` gibt für jede Funktion der Rolle, die eine Redakteursrolle nicht haben sollte, eine
`WARN`-Zeile aus und entfernt nie etwas:

| Funktion | Warum sie nicht zu einem Redakteur gehört |
| --- | --- |
| content / cleantrash | leert den Papierkorb aller endgültig |
| content / translations | fügt die Sprachen der ganzen Installation hinzu und entfernt sie |
| content / urltranslator | die URL-Aliase und Platzhalter der ganzen Installation |
| content / jobs | die Inhaltsjobs aller |
| setup, class, role, section, workflow, state / administrate | die Administration |
| git_manager, update | das Aktualisieren der Installation |

Das `content/*` der Grunddaten enthält die ersten vier; die Teilbaumbegrenzung der Rollenzuweisung begrenzt sie nicht,
da sie keinen Knoten haben. Die Website-Pakete entfernen `content/*`, und seit dem 24. Oktober 2026 ersetzen der
Setup-Schritt und das Skript das eine, das `plain_site` behält (oben); die Zeilen erscheinen also nur noch für ein
`content/*` mit Begrenzungen, für eines, das mit `--keep-content-wildcard` behalten wurde, oder für eine Rolle, der
diese Funktionen von Hand gegeben wurden.

## Was beim Aktualisieren zu tun ist

Führen Sie das Skript zuerst ohne Optionen aus: Ohne `--apply` ändert es nichts (seit dem 28. Oktober 2026; vorher
schrieb es standardmäßig und nahm `--dry-run`, das weiter angenommen wird und dasselbe bedeutet wie keine Option). Es
listet jede Richtlinie, die es hinzufügen würde, und den Grund, warum eine wegfällt (die Rolle hat sie schon in
irgendeiner Form, auch begrenzt oder als `module/*`; eine andere Rolle derselben Gruppen hat sie; oder das Modul ist
nicht installiert), dann die `WARN`-Zeilen. Führen Sie es danach mit `--apply` aus, um die Änderung vorzunehmen:

```bash
php bin/php/addeditorpolicies.php           # was es ändern würde; ändert nichts
php bin/php/addeditorpolicies.php --apply   # ausführen; prüft das Ergebnis, gibt PASS oder FAIL aus
```

Mit `--apply` fügt es nur hinzu, was fehlt, ohne Begrenzungen, lässt den Rollen-Cache verfallen, prüft das Gespeicherte
und gibt `PASS` oder `FAIL` aus. Ein zweiter Lauf fügt nichts hinzu. `--apply` zusammen mit `--dry-run` wird abgelehnt
(Exit-Code 2).

Bei einer Rolle mit `content/*` ohne Begrenzungen (eine Installation von `plain_site`) ersetzt das Skript es zuerst, und
der Probelauf sagt genau, was geht und was kommt:

```
would remove content/*                    content/* also grants content/cleantrash, content/translations, content/urltranslator, content/jobs
would add    content/create               in place of content/*
would add    content/edit                 in place of content/*
...
would add    content/reverserelatedlist   in place of content/*
...
PASS: content/* would be replaced by 16 content functions; 0 policies would be added to Editor (3) (dry run, nothing changed; --apply writes)
```

Eine Funktion, die die Rolle schon als einzelne Richtlinie hat (auch begrenzt), bleibt und wird nicht noch einmal
hinzugefügt; danach wird die gespeicherte Rolle geprüft (kein `content/*` mehr, jede Funktion vorhanden). Rollen ohne
`content/*` (jedes andere Website-Paket) bleiben unberührt.

Die folgenden Optionen werden mit `--apply` kombiniert; ohne `--apply` zeigen sie, was sie tun würden:

- `--keep-content-wildcard` lässt `content/*`, wie es ist.
- `--skip=<Modul/Funktion>` lässt eine Richtlinie weg (z. B. `--skip=content/restore`), wiederholbar; ein
  übersprungenes `content/hide`, `content/restore` oder `content/reverserelatedlist` kommt auch nicht anstelle von
  `content/*`.
- `--only=<Modul/Funktion>` fügt nur diese Richtlinie hinzu, wiederholbar; `content/*` bleibt dann.
- `--role=<Name oder ID>` vervollständigt eine andere Rolle als Editor (eine eigene Redakteursrolle der Website).

Das Skript fügt `geo_article` und `video` nicht zu `content/create` hinzu: Klassenlisten sind die Entscheidung der
Website. Fügen Sie sie unter Rollen und Richtlinien hinzu, wenn Ihre Redakteure solche Inhalte schreiben.

Code: `kernel/classes/expeditorpolicies.php`, Befehl `kernel/private/classes/commands/addeditorpolicies.php`. Test ohne
Datenbank: `tests/tests/kernel/classes/expEditorPoliciesTest.php`.
