# Benutzerkonten prüfen

Lesen Sie diese Seite, wenn Sie die Benutzerkonten einer Installation betreuen: welche Konten niemand verwendet, welche
deaktiviert oder gesperrt sind, welche ihr Benutzerobjekt verloren haben, welche sich eine E-Mail-Adresse teilen und
wie Sie sie aufräumen, ohne jedes einzelne zu öffnen. Die Seite ist **Benutzer > Benutzerkonten prüfen**
(`user/hygiene`), außerdem eine Kachel der Setup-Übersicht in der Gruppe *Benutzer und Nachrichten*. Hinzugefügt am
15.10.2026.

## Kurz gefasst

- Eine Liste aller Benutzerkonten oder der Konten unterhalb einer Benutzergruppe, mit einem Filter: **Alle Konten**,
  **Nie angemeldet**, **Seit N Tagen nicht gesehen** (standardmäßig 180), **Deaktiviert**, **Gesperrt**, **Verwaist**, **Doppelter Benutzername** und **Doppelte
  E-Mail-Adresse** (nur mit dem Recht, Adressen zu sehen). Jeder Filter zeigt, wie viele Konten er enthält; eine Suche (Name, Login und, mit dem Recht, sie zu sehen,
  E-Mail-Adresse) und die Seitengröße (25, 50, 100) grenzen weiter ein. Die Spalten Name, Login, E-Mail-Adresse und
  letzter Besuch sowie die Anzahl der Anmeldungen sortieren die Liste in beide Richtungen.
- Jede Zeile: der Name (ein Link zum Knoten des Benutzers), Login, E-Mail-Adresse (nur mit dem Recht, siehe unten),
  die Zustände, der letzte Besuch mit den seither vergangenen Tagen, die Anzahl der Anmeldungen, die Gruppen (die
  Eltern seiner Platzierungen) und die Rollen, die es hat, seine eigenen und die der Gruppen darüber.
- **Deaktivieren**, **Aktivieren** und **Entsperren** der angekreuzten Konten. Jeder Button zeigt eine Bestätigung mit
  der Anzahl der Konten, die er ändern wird, und der Anzahl, die er auslässt, samt Grund; **Bestätigen** führt es aus.
- **Liste als CSV exportieren** sendet die gesamte gefilterte Liste, alle Seiten, als UTF-8-Datei mit Byte Order Mark.
- Nichts hier entfernt ein Konto. Ein Benutzerobjekt kommt in Exponential nie in den Papierkorb
  (`eZContentObjectTreeNode::isNodeTrashAllowed()`): Das Entfernen ist endgültig, daher bleibt es beim Entfernen
  des Knotens selbst, ein Konto nach dem anderen.
- Statt Konten zu entfernen: **Deaktivieren und verschieben** (optional, `[UserSettings] DeactivateAndMove=enabled`)
  deaktiviert Konten und führt alle ihre Platzierungen in einer versteckten Gruppe ohne Rollen zusammen, wobei
  festgehalten wird, wo sie waren; **Reaktivieren und zurückverschieben** setzt sie zurück. Siehe
  [Deaktivieren und verschieben](#deaktivieren-und-verschieben).

## Die Filter

| Filter | Konten |
|---|---|
| Alle Konten | jedes Konto, dessen Objekt nicht im Papierkorb liegt |
| Nie angemeldet | keine Anmeldung gezählt (`ezuservisit.login_count` 0 oder keine Zeile); nicht der anonyme Benutzer (`[UserSettings] AnonymousUserID`), der sich nicht anmelden kann |
| Seit N Tagen nicht gesehen | mindestens einmal angemeldet, und der letzte Besuch liegt mehr als N Tage zurück (1 bis 36500) |
| Deaktiviert | `ezuser_setting.is_enabled` 0; ein deaktiviertes Konto mit Aktivierungsschlüssel wird als *Nicht aktiviert* markiert |
| Gesperrt | mehr fehlgeschlagene Anmeldungen als `site.ini [UserSettings] MaxNumberOfFailedLogin`, wie `eZUser::isLocked()` entscheidet; keine, solange die Einstellung 0 ist |
| Verwaist | ein Konto (eine Zeile von `ezuser`), dessen Benutzerobjekt fehlt, nie veröffentlicht wurde (keine veröffentlichte Version) oder veröffentlicht ist, aber nirgends im Baum eine Platzierung hat (keine Zeile in `ezcontentobject_tree`). Eine Platzierung außerhalb des Benutzer-Teilbaums ist eine Platzierung: Mitglieder können absichtlich woanders platziert sein |
| Doppelter Benutzername | ein Login (unabhängig von Groß- und Kleinschreibung, wie `eZUser::fetchByName()` vergleicht), den ein anderes aufgeführtes Konto ebenfalls hat; nach Login sortiert. Auf diese Weise geteilte Logins sind das, was [LoveStack L04](../../bc/6.0/lovestack.md#l04--ein-konto-pro-anmeldename) mit `./console exp:user-login-check` meldet: Lösen Sie sie hier auf, dann kann `--fix-index` den eindeutigen Index hinzufügen |
| Doppelte E-Mail-Adresse | eine Adresse (unabhängig von Groß- und Kleinschreibung), die ein anderes aufgeführtes Konto ebenfalls hat; nach Adresse sortiert, sodass die Konten einer Adresse nebeneinander stehen. Nur mit `role/assign` |

Der **letzte Besuch** ist `ezuservisit.current_visit_timestamp`, der Zeitpunkt der letzten Anmeldung oder der ersten
Anfrage nach einer längeren Pause (`site.ini [Session] ActivityTimeout`); eine Abmeldung speichert ihn negiert, was die
Seite als denselben Zeitpunkt liest. Es ist nicht `eZUser::lastVisit()`, das den Besuch davor liefert.

Die andere Seite von *verwaist*, ein veröffentlichtes Benutzerobjekt ohne Kontozeile (ein Objekt einer Klasse mit einem
`ezuser`-Attribut und ohne Zeile in `ezuser`), kann nicht aufgelistet werden: Die Liste wird aus `ezuser` gelesen, und
keine Aktion gilt für ein Objekt ohne Konto. Die Seite zählt sie und nennt oberhalb der Liste ihre Anzahl; öffnen Sie
sie im Inhaltsbaum.

Unter den Reitern sagt die Seite in einer Zeile, was der aktuelle Filter enthält.

Die **Zustände** einer Zeile, die dringendsten zuerst: *Deaktiviert* oder *Nicht aktiviert*, *Gesperrt*, *Kein
Benutzerobjekt*, *Nie veröffentlicht* oder *Ohne Platzierung*, *Nie angemeldet*; *Aktiv*, wenn keiner zutrifft.

## Die Aktionen

| Aktion | Was sie tut | Ausgelassen |
|---|---|---|
| Deaktivieren | die Operation `user/setsettings` mit ausgeschaltetem *enabled*, wie es **Benutzereinstellungen bearbeiten** tut: Das Konto kann sich nicht anmelden, und seine Sitzungen enden. Inhalte, Gruppen und Rollen bleiben. | bereits deaktivierte Konten |
| Aktivieren | dieselbe Operation mit eingeschaltetem *enabled* | bereits aktivierte Konten; Konten, die nie aktiviert wurden (sie haben einen Aktivierungsschlüssel): Aktivieren Sie diese unter **Nicht aktivierte Benutzer**, was die Registrierung abschließt |
| Entsperren | `eZUser::setFailedLoginAttempts( id, 0, true )`, wie es **Fehlgeschlagene Anmeldungen zurücksetzen** tut | Konten ohne fehlgeschlagene Anmeldungen |

Jede Aktion lässt für jedes Konto außerdem aus:

- **den anonymen Benutzer** (`[UserSettings] AnonymousUserID`), **den Benutzer-Ersteller**
  (`[UserSettings] UserCreatorID`), **das Administratorkonto einer frischen Installation** (ID 14), **das Konto mit dem
  Login `admin`** und **Sie selbst**. Ihre Kästchen lassen sich nicht ankreuzen, und eine von Hand gesendete ID wird
  ebenfalls abgelehnt;
- Konten, deren Benutzerobjekt Sie nicht bearbeiten dürfen (`content/edit` darauf, mit seinen Einschränkungen);
- Konten ohne Benutzerobjekt (es gibt nichts, an dem der Zugriff geprüft werden könnte).

Die Bestätigung entscheidet für jedes angekreuzte Konto; **Bestätigen** entscheidet für jedes unmittelbar vor der
Änderung erneut, sodass eine veraltete Liste nichts ändert, was sie nicht ändern sollte. Eine Aktion umfasst höchstens
100 Konten, die größte Seite.

Jede Änderung wird von den eigenen Audit-Ereignissen des Kernels protokolliert, eines pro Konto: `access.user.disable`,
`access.user.enable` und `access.user.unlock` (Kanal *access*). Die CSV wird als `data.export.csv` protokolliert, mit
der Anzahl der Zeilen, dem Filter und der Angabe, ob E-Mail-Adressen enthalten waren, nie mit den Werten.

## Wer sie verwenden darf

| Was | Benötigt |
|---|---|
| Die Seite, ihre Aktionen und die CSV | die Funktion **user/hygiene** (`kernel/user/module.php`). Sie ist in keiner Rolle einer frischen Installation außer *Administrator* (`*/*`); geben Sie sie den Personen, die Konten betreuen, nicht Redakteuren. |
| Ein Konto ändern | zusätzlich `content/edit` auf das Benutzerobjekt dieses Kontos |
| E-Mail-Adressen (Spalte, Suche, Sortierung, der Filter *Doppelte E-Mail-Adresse*, CSV) | `role/assign`, dasselbe Recht wie für die E-Mail-Spalte der Unterelement-Tabelle (`settings/subitemscolumns.ini [Column_useremail]`) |

Jede Änderung und die CSV sind POST-Anfragen mit dem Formular-Token von `ezformtoken`; Bestätigen benötigt außerdem das
Token der Bestätigungsseite (das Feld `HygieneToken`, in der Sitzung als `expUserHygieneToken` gehalten), wie
Deaktivieren und verschieben, sodass eine Anfrage, die nicht von der Seite stammt, selbst ohne `ezformtoken` nichts
ändert. Ein Link kann weder ein Konto ändern noch einen Download starten. Jeder, der die Seite öffnen darf, sieht Name, Login, Zustände, Gruppen und Rollen jedes Kontos
(es gibt keine Leseprüfung pro Konto), geben Sie `user/hygiene` also nur den Personen, die Konten betreuen.

## Deaktivieren und verschieben

Redaktionsteams entfernen Benutzerkonten, weil es keinen anderen Weg gibt, sie stillzulegen, und ein entferntes
Benutzerobjekt ist endgültig weg: Benutzer kommen nie in den Papierkorb. **Deaktivieren und verschieben** legt ein Konto
stattdessen still: Es wird deaktiviert, und alle seine Platzierungen werden in einer Benutzergruppe für deaktivierte
Konten zusammengeführt, die keine Rollen hat und versteckt ist. Wo es war, wird festgehalten, sodass **Reaktivieren und
zurückverschieben** es aktiviert und zurücksetzt. Nichts wird entfernt: Seine Inhalte, seine Versionen, die dem Konto
selbst zugewiesenen Rollen und die Relationen bleiben.

**Standardmäßig ausgeschaltet (optional).** Ausgeliefert wird `site.ini [UserSettings] DeactivateAndMove=disabled`;
damit wird nichts angezeigt (kein Button, kein Menüeintrag, keine Sammelaktion), `user/deactivate` antwortet mit
*nicht gefunden*, und nichts wird angelegt.

```ini
# settings/override/site.ini.append.php, or the admin siteaccess's site.ini.append.php
[UserSettings]
DeactivateAndMove=enabled
# optional: a group of your own instead of the one found by its remote id
DeactivatedUserGroupNodeID=
```

### Die Gruppe

- `DeactivatedUserGroupNodeID` gesetzt: Dieser Knoten ist die Gruppe. Eine nicht existierende Knoten-ID bricht die
  Aktion mit einer Meldung ab; sie wird nie durch eine neue Gruppe ersetzt.
- Leer (der Standard): die Benutzergruppe mit der Remote-ID `exp_deactivated_users_group`. Gibt es noch keine, legt
  das erste **Deaktivieren und verschieben** sie unterhalb von `content.ini [NodeSettings] UserRootNode` (dem Knoten
  Benutzer) an: eine Benutzergruppe (`[UserSettings] UserGroupClassID`) namens *Deaktivierte Benutzer* in der Sprache
  der Administration, versteckt, ohne Rollen, in einer Transaktion, protokolliert als
  `access.user.deactivated_group.create`. Sie wird nur von einem Benutzer angelegt, der dort eine Benutzergruppe
  anlegen darf (`content/create`), und nur, wenn mindestens eines der bestätigten Konten hineinkommt; jeder andere
  erhält eine entsprechende Meldung, und nichts ändert sich. Das Verstecken ist Teil des Anlegens (es wird kein
  separates `content/hide` verlangt).
- Oder legen Sie sie vorab an, zum Beispiel auf einer Website, deren Redakteure keine Gruppen anlegen dürfen:

  ```bash
  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>             # dry run: what it would do
  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit    # creates it
  #   --name="Former staff"   the group's name      --section-id=N   a section of its own
  ```

  `-s` ist erforderlich (die Gruppe gehört zur Datenbank dieses Siteaccess). Das Skript veröffentlicht als
  `[UserSettings] UserCreatorID`, gibt die Knoten-ID und die Einstellungszeilen aus und schreibt nie eine INI-Datei. Der
  Installer legt nichts an.

Die Gruppe ist versteckt, damit deaktivierte Konten nicht auf der Website, in ihren Benutzerlisten oder in ihrer Suche
erscheinen (`ShowHiddenNodes=false`); die Administration zeigt sie, markiert mit *Versteckt durch übergeordneten
Eintrag*. Die Gruppe hat keine Rollen: Eine ihr zugewiesene Rolle wäre eine Rolle jedes deaktivierten Kontos. Daher:

- **Zuweisen** bei einer Rolle (role/view und role/assign, mit oder ohne Einschränkung) lässt die Gruppe aus den
  gewählten Benutzern und Gruppen aus, weist die anderen zu und vermerkt auf der Seite der Rolle, dass die Gruppe
  ausgelassen wurde (`expUserDeactivation::withoutGroup()`). Das gilt unabhängig davon, ob die Funktion eingeschaltet
  ist, solange die Gruppe existiert.
- Eine Rolle, die der Gruppe auf anderem Weg gegeben wurde (ein Skript, die Datenbank), wird bemerkt: **Deaktivieren
  und verschieben** verweigert dann, bevor irgendetwas geändert wird, bis die Zuweisung entfernt ist
  (`expUserDeactivation::groupHasRoles()`).
- Einem Konto selbst zugewiesene Rollen bleiben bei ihm; es kann sich nicht anmelden, solange es deaktiviert ist.

### Deaktivieren und verschieben

Für jedes Konto, in einer Transaktion (nichts bleibt, wenn ein Schritt fehlschlägt):

1. Seine Platzierungen werden festgehalten: der Elternknoten der Hauptplatzierung, die Elternknoten der anderen und ob
   es aktiviert war. Ein Konto, das bereits in der Gruppe ist und seitdem Platzierungen hinzugewonnen hat, behält
   seinen Eintrag, und die neuen Platzierungen werden ihm hinzugefügt, sodass nichts entfernt wird, ohne festgehalten
   zu sein;
2. es wird über die Operation `user/setsettings` deaktiviert (wie **Deaktivieren**; seine Sitzungen enden;
   `access.user.disable`); es gilt nur dann als deaktiviert, wenn die Operation fortgesetzt wurde: Ein Workflow, der
   sie abbricht oder anhält, lässt das Konto aktiviert, und der Schritt schlägt fehl (dasselbe gilt für Deaktivieren
   und Aktivieren bei den obigen Aktionen);
3. die Platzierungen außer der Hauptplatzierung werden entfernt (Operation `content/removelocation`);
4. die Hauptplatzierung wird in die Gruppe verschoben (Operation `content/move`); der Knoten behält seine ID.

Protokolliert als `access.user.deactivate_move` mit den Platzierungen vorher und nachher, neben den eigenen Ereignissen
des Kernels für die Schritte. Ein Konto, das bereits deaktiviert und nur in der Gruppe ist, wird ausgelassen (*bereits
in diesem Zustand*).

### Reaktivieren und zurückverschieben

Für jedes Konto, in einer Transaktion:

1. Es wird aktiviert (Operation `user/setsettings`, `access.user.enable`);
2. liegt seine Hauptplatzierung in der Gruppe, wird es zum festgehaltenen Haupt-Elternknoten zurückverschoben; existiert
   dieser nicht mehr oder dürfen Sie das Konto dort nicht platzieren, kommt es stattdessen nach
   `[UserSettings] DefaultUserPlacement`;
3. die übrigen festgehaltenen Elternknoten, die noch existieren und es aufnehmen dürfen, erhalten wieder eine
   Platzierung (Operation `content/addlocation`); die anderen werden ausgelassen;
4. die festgehaltenen Platzierungen werden vergessen.

Bestätigung und Ergebnis nennen, wie viele frühere Platzierungen ausgelassen werden, *nicht mehr vorhanden oder für Sie
nicht verwendbar* zusammengefasst, sodass die Zahl nichts über Knoten verrät, die Sie nicht sehen dürfen. Protokolliert
als `access.user.reactivate_move`. Ein Konto, das nie aktiviert wurde, wird ausgelassen (aktivieren Sie es unter
**Nicht aktivierte Benutzer**); eines ohne Ziel (kein festgehaltener Haupt-Elternknoten und keine
Standard-Benutzergruppe, in die es darf) wird ebenfalls ausgelassen. Ein Konto, das bereits vor dem Deaktivieren
deaktiviert war, wird in der Bestätigung entsprechend markiert, da das Reaktivieren es aktiviert.

### Wo die Platzierungen festgehalten werden

Eine Zeile in `ezsite_data` (`eZSiteData`) pro deaktiviertem Konto, mit dem Namen `exp_user_deactivation_<user id>`,
ihr Wert ist JSON: `main` (Elternknoten der Hauptplatzierung), `parents` (Elternknoten der anderen Platzierungen, nie
die Gruppe), `group`, `was_enabled`, `at`, `by`. Dies wurde einer eigenen Tabelle vorgezogen, weil es kein Datenbank-Update braucht, über
`eZPersistentObject` auf jeder vom Kernel unterstützten Engine funktioniert (gemessen auf MySQL/MariaDB; MongoDB ist
nicht getestet) und eine Installation die Funktion einschalten kann, ohne etwas auszuführen. Die Zeile enthält nur
Knoten-IDs, keine Namen; sie wird beim Reaktivieren entfernt. **Setup > Site-Daten** listet sie auf (für
`setup/system_info`). Eine Zeile eines später entfernten Kontos bleibt zurück und schadet nicht.

### Wo

| Wo | Was |
|---|---|
| Die Seite eines Benutzers (Knotenansicht von admin4 und admin) | **Deaktivieren und verschieben** für ein aktiviertes Konto oder eines außerhalb der Gruppe, **Reaktivieren und zurückverschieben** für ein deaktiviertes, eines in der Gruppe oder eines mit festgehaltenen Platzierungen; neben Entfernen |
| Das Kontextmenü, *Fortgeschritten* | **Deaktivieren und verschieben (Benutzerkonten)** und **Reaktivieren und zurückverschieben (Benutzerkonten)**; das Menü kennt die Klasse nicht, daher sagt die Bestätigung bei einem Eintrag, der kein Benutzerkonto ist, genau das und bietet nichts an |
| Benutzer > Benutzerkonten prüfen | **Angekreuzte Konten deaktivieren und verschieben** und **Angekreuzte Konten reaktivieren und zurückverschieben** neben Deaktivieren und Aktivieren (die bleiben, wie sie sind); ein Konto in der Gruppe trägt in seinen Gruppen ein Badge *deaktiviert* |

Jeder Eintrag führt zur Bestätigungsseite `user/deactivate`: jedes Konto mit den Gruppen, in denen es jetzt ist, und
wohin es kommt, die anderen gezählt nach dem Grund, aus dem sie ausgelassen werden; **Bestätigen** entscheidet für jedes
Konto unmittelbar vor der Änderung erneut. Höchstens 100 Konten auf einmal.

### Wer sie verwenden darf

| Was | Benötigt |
|---|---|
| Die Buttons, die Menüeinträge und `user/deactivate` | die Funktion **user/deactivate** (in keiner Rolle einer frischen Installation außer *Administrator*, `*/*`; eine Rolle mit der Richtlinie `user/*` erhält sie ebenfalls, sehen Sie sich diese Rollen also an, bevor Sie die Funktion einschalten); mit `user/hygiene` für die Sammelaktionen unter Benutzerkonten prüfen |
| Jedes Konto | `content/edit` auf sein Benutzerobjekt und `content/read` auf jede seiner Platzierungen |
| Deaktivieren | an der Hauptplatzierung `content/edit` und `content/remove` (zum Verschieben, `canMoveFrom()`), `content/create` der Benutzerklasse in der Gruppe (`canMoveTo()`); an jeder anderen Platzierung `content/remove` oder das Recht, eine Platzierung zu entfernen |
| Reaktivieren | das Verschieben der Hauptplatzierung aus der Gruppe (`canMoveFrom()`) und `content/create` der Benutzerklasse am Ziel (`canMoveTo()`, wie Verschieben es verlangt); für jede andere Platzierung `content/create` oder das Recht, dort eine Platzierung hinzuzufügen (wie Ort hinzufügen es verlangt) |
| Die Gruppe bei der ersten Verwendung anlegen | `content/create` der Benutzergruppenklasse unterhalb des Knotens Benutzer |

Nie angefasst werden: der anonyme Benutzer, der Benutzer-Ersteller, das Administratorkonto (ID 14, Login `admin`) und
Sie selbst (wie bei den anderen Aktionen). Ein Konto, dessen Objekt Sie nicht lesen dürfen, wird mit *nicht gefunden
oder für Sie nicht sichtbar* beantwortet, genau wie eine nicht existierende ID, sodass die Seite nichts über Objekte
verrät, die Sie nicht sehen dürfen. Namen werden nur für Konten angezeigt, die Sie ändern dürfen, und für Gruppen, die
Sie lesen dürfen. Jede Änderung ist ein POST mit dem Formular-Token von `ezformtoken`, und **Bestätigen** trägt
zusätzlich ein eigenes Token, das in der Sitzung gehalten wird, sodass keine Anfrage außer der Bestätigungsseite selbst
die Änderung ausführen kann, mit oder ohne `ezformtoken`; der Rückweg nach dem Ergebnis ist die Seite, von der Sie kamen (`user/hygiene` oder der Knoten des Benutzers), nie
eine Adresse von außen.

## Die CSV

Spalten: *User ID*, *Name*, *Login*, *E-mail* (nur mit `role/assign`), *Status* (die Zustände, durch Kommas getrennt),
*Last visit* (`YYYY-MM-DD HH:MM UTC`, leer, wenn nie), *Sign-ins*, *Groups*, *Roles*. Die Spaltenüberschriften sind in
der Sprache der Administration (auf Deutsch etwa *Benutzer-ID*, *Letzter Besuch*, *Anmeldungen*, *Gruppen*, *Rollen*).

UTF-8 mit Byte Order Mark, durch Kommas getrennt, CRLF-Zeilenenden, wie die anderen CSV-Dateien der Administration
(`expSubitemsCSVExport`). Eine Zelle, die mit `=`, `+`, `-`, `@`, einem Tabulator oder einem Wagenrücklauf beginnt (und
keine Zahl ist), erhält einen führenden Apostroph, sodass eine Tabellenkalkulation sie als Text anzeigt, statt sie als
Formel auszuführen. Die Datei enthält höchstens 100.000 Konten; sie werden in Schritten von 500 nach ID gelesen.

## Viele Konten

Die Liste und ihre Anzahl sind je eine Abfrage über `ezuser` und seine einzeiligen Begleittabellen (`ezuser_setting`,
`ezuservisit`, `ezcontentobject`); Gruppen, Rollen, Aktivierungsschlüssel, geteilte Adressen und geteilte Logins einer
Seite sind sechs Abfragen für die gesamte Seite, unabhängig von ihrer Größe; ein Aufruf der Seite fügt eine Zählung pro
Filter für die Reiter und eine für die Benutzerobjekte ohne Konto hinzu (insgesamt 16 Abfragen). Gemessen mit 50.000 Test-Konten in SQLite auf einem Laptop (PHP 8.5): eine Seite mit 100 Einträgen samt Anzahl
in 26 bis 146 ms je nach Filter (geteilte Adressen am langsamsten), ein vollständiger Aufruf der Seite mit den Zählungen
aller Filter in 216 ms, eine Seite bei Offset 49.000 in 108 ms, die CSV aller 50.000 Konten in 1,6 s mit 12 MB
Arbeitsspeicher. Eine Aktion liest jedes angekreuzte Konto einzeln (sein Objekt, seine Einstellung, seinen Schlüssel und
seinen Zugriff), höchstens 100.

Die Seite benötigt eine SQL-Datenbank (MySQL, MariaDB, PostgreSQL, SQLite, Oracle); unter MongoDB sagt sie das und listet nichts auf.

## In den Menüs

- Reiter Benutzer, linkes Menü: `menu.ini [Leftmenu_user] Links[user_hygiene]`, mit Icon und Beschreibung, und im
  Mega-Menü des Reiters Benutzer (`[Leftmenu_user_mega] MegaMenuLinks[]=access-user_hygiene`, Gruppe
  *Zugangskontrolle*).
- Setup-Übersicht und Setup-Menü: `menu.ini [Leftmenu_setup] Links[user_hygiene]` in `GroupLinks_users[]`, mit dem
  Icon `person-badge` (`LinkIcons[user_hygiene]`, hinzugefügt zu `design.ini [AdminDesignSettings] IconList[]`) und
  seiner Beschreibung (`LinkDescriptions[user_hygiene]`).

Beide verwenden den Link-Schlüssel `user_hygiene`, sodass ein Menü, das Links nach Schlüssel auflistet, ihn ebenfalls
nennen kann.

## Dateien

| Datei | Was |
|---|---|
| `kernel/user/hygiene.php`, `kernel/private/classes/views/user/hygiene.php` | die Ansicht |
| `kernel/classes/user/expuserhygiene.php` | `expUserHygiene`: die Filter, die Abfragen, die Entscheidungen der Aktionen, die CSV |
| `design/admin4/templates/user/hygiene.tpl`, `design/admin/templates/user/hygiene.tpl` | die Seite, dieselbe Datei in beiden Designs |
| `tests/tests/kernel/classes/user/expUserHygieneTest.php` | die Tests: jeder Filter gegen eine im Test aufgebaute SQLite-Datenbank, die Entscheidungen, die CSV, die Seite |
| `kernel/classes/user/expuserdeactivation.php` | `expUserDeactivation`: Deaktivieren und verschieben, die Gruppe, die festgehaltenen Platzierungen, die Entscheidungen |
| `kernel/user/deactivate.php`, `kernel/private/classes/views/user/deactivate.php` | die Ansicht `user/deactivate` (Bestätigung und Ergebnis) |
| `design/admin4/templates/user/deactivate.tpl`, `design/admin/templates/user/deactivate.tpl` | ihre Seite, dieselbe Datei in beiden Designs |
| `bin/php/ezcreatedeactivatedusergroup.php`, `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php` | legt die Gruppe vorab an |
| `tests/tests/kernel/classes/user/expUserDeactivationTest.php`, `expUserDeactivationLiveTest.php` | die Entscheidungen, der Plan für den Rückweg, die Seite und ihre Einstiegspunkte; die Kernel-Pfade gegen die Datenbank der Installation, jeder Test wird zurückgerollt |

## Verwandte Seiten

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Das Design admin4](admin4-design.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
