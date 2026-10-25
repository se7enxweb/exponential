# Hinweis zum Aktualisieren: die alltäglichen Richtlinien der Rolle Editor

Ein Benutzer der Gruppe **Editors** hat die Richtlinien der Rolle **Editor**, bei den Website-Paketen auch die der
Rolle **Member**. Dieser Hinweis sagt, was ein Redakteur einer neuen Installation nicht tun konnte, was sich ändert,
und wie die Rolle einer bestehenden Installation vervollständigt wird.

## Was falsch war

Die Grunddaten geben der Rolle Editor `content/*` (mit `ezoe/*`, `user/login` und den Oberflächen-Auswahlen) und weisen
sie der Gruppe Editors zweimal zu, begrenzt auf die Teilbäume Content (`/1/2/`) und Media (`/1/43/`). Jedes
Website-Paket außer `plain_site` entfernt `content/*` und fügt stattdessen einzelne Funktionen hinzu: `content/create`
begrenzt nach Klasse und Elternklasse, `content/edit`, `content/read` begrenzt auf die Sektionen Standard, Restricted
und Media, `content/remove`, `content/manage_locations`, `content/translate`, die Versionen, Lesezeichen, die Liste der
ausstehenden Objekte und das Dashboard; es weist der Gruppe Editors außerdem die Rolle Member zu (eigenes Passwort,
eigenes Konto, Benachrichtigungen).

Dieser Liste fehlten drei Funktionen, die ein Redakteur täglich braucht, und kein Paket gab den Mehrfach-Upload frei:
Ausblenden und Einblenden (`content/hide`), den Papierkorb ansehen und daraus wiederherstellen (`content/restore`), die
Liste der Objekte, die ein Objekt verwenden (`content/reverserelatedlist`), mehrere Dateien auf einmal hochladen
(`ezmultiupload/*`) und der Bildeditor (`expimageeditor/edit`). Bei `plain_site`, das die Rolle der Grunddaten behält
und keine Rolle Member zuweist, konnte ein Redakteur außerdem das eigene Passwort (`user/password`), das eigene Konto
(`user/selfedit`) und die Benachrichtigungen (`notification/use`) nicht ändern.

## Was sich ändert

- **Der Setup-Assistent und der Kickstarter** prüfen nach den Rollen des Website-Pakets, dass die Rolle Editor
  `content/hide`, `content/restore`, `content/reverserelatedlist`, `ezmultiupload/*` und `expimageeditor/edit` hat
  (jeweils, wenn die Installation die Erweiterung hat), und `user/password`, `user/selfedit` und `notification/use`,
  sofern keine andere Rolle der Gruppe Editors sie hat; was fehlt, wird hinzugefügt (`expEditorPolicies::ensure()` in
  `kernel/setup/steps/ezstep_create_sites.php`).
- **Die Website-Pakete** `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` und
  `cjw_multisite` fügen die Richtlinien in ihrem Editor-Schritt selbst hinzu.
- **`bin/php/addeditorpolicies.php`** vervollständigt die Rolle bei einer bestehenden Installation (unten).
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

Die Richtlinien kommen in die Rolle Editor; die Zuweisung der Rolle begrenzt sie wie den Rest: für die Gruppe Editors
auf die Teilbäume Content und Media.

## Was bleibt, und warum

- **Den Papierkorb leeren** (`content/cleantrash`) entfernt die gelöschten Objekte aller endgültig; Redakteure stellen
  wieder her, ein Administrator leert.
- **Benutzer**: Die Rolle Editor liest und bearbeitet nichts unter Users (`/1/5/`). Die Mitglieder der Website
  verwaltet die Rolle "Member management", begrenzt auf Members, nie die Konten der Administratoren und Redakteure.
- **Git-Manager und Update-Dashboard** bleiben bei den Administratoren.

## Zu weite Rechte, die das Skript meldet

`bin/php/addeditorpolicies.php` gibt für jede Funktion, die eine Redakteursrolle nicht haben sollte, eine `WARN`-Zeile
aus: `content/cleantrash`, `content/translations`, `content/urltranslator`, `content/jobs`, die Module `setup`,
`class`, `role`, `section`, `workflow`, `state/administrate`, `git_manager` und `update`. Seit dem 24. Oktober 2026
ersetzen der Setup-Schritt und das Skript das `content/*` von `plain_site`; die Zeilen erscheinen also nur noch für ein
`content/*` mit Begrenzungen, für eines, das mit `--keep-content-wildcard` behalten wurde, oder für Funktionen, die
einer Rolle von Hand gegeben wurden.

## Was beim Aktualisieren zu tun ist

Das Skript zuerst mit `--dry-run` ausführen. Es listet jede Richtlinie, die es hinzufügen würde, und den Grund, warum
eine wegfällt, dann die `WARN`-Zeilen:

```bash
php bin/php/addeditorpolicies.php --dry-run
php bin/php/addeditorpolicies.php
```

Bei einer Rolle mit `content/*` ohne Begrenzungen (eine Installation von `plain_site`) ersetzt das Skript es zuerst, und
der Probelauf sagt genau, was geht und was kommt:

```
would remove content/*                    content/* also grants content/cleantrash, content/translations, content/urltranslator, content/jobs
would add    content/create               in place of content/*
...
PASS: content/* would be replaced by 16 content functions; 0 policies would be added to Editor (3) (dry run, nothing changed)
```

Eine Funktion, die die Rolle schon als einzelne Richtlinie hat (auch begrenzt), bleibt und wird nicht noch einmal
hinzugefügt; danach wird die gespeicherte Rolle geprüft. Rollen ohne `content/*` (jedes andere Website-Paket) bleiben
unberührt. Ein zweiter Lauf ändert nichts.

- `--keep-content-wildcard` lässt `content/*`, wie es ist.
- `--skip=<Modul/Funktion>` lässt eine Richtlinie weg, wiederholbar; ein übersprungenes `content/hide`,
  `content/restore` oder `content/reverserelatedlist` kommt auch nicht anstelle von `content/*`.
- `--only=<Modul/Funktion>` fügt nur diese Richtlinie hinzu; `content/*` bleibt dann.
- `--role=<Name oder ID>` vervollständigt eine andere Rolle als Editor.

Code: `kernel/classes/expeditorpolicies.php`, Befehl `kernel/private/classes/commands/addeditorpolicies.php`. Test ohne
Datenbank: `tests/tests/kernel/classes/expEditorPoliciesTest.php`.
