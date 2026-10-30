# Mitgliederverwaltung: Redakteure verwalten die Mitglieder der Website

Redakteure verwalten die Konten der Mitglieder der Website: Sie legen sie an, bearbeiten sie (Name, E-Mail-Adresse,
Passwort), aktivieren und deaktivieren sie, verschieben sie zwischen Mitgliedergruppen und entfernen sie. Die Konten
von Administratoren und anderen Redakteuren erreichen sie nie, und sie weisen keine Rollen zu und ändern weder Rollen,
Sektionen noch die Einrichtung. Jede dieser Aktionen wird im Auditprotokoll aufgezeichnet, ebenso jede Abweisung.

Die Regeln stehen in `settings/membermanagement.ini`. Eine Website legt dort fest, welche Gruppen welche anderen
Gruppen verwalten, welche Gruppen niemand über eine solche Regel verwaltet und welche Richtlinien ein Verwalter
erhält. Standardmäßig erhält die Gruppe **Editors** die Rolle **Member management** über **Members**, und
**Administrator users** sowie **Editors** sind geschützt.

## Was eine neue Installation erhält

Der Einrichtungsassistent und der Kickstarter wenden die Regeln nach den Rollen des Website-Pakets an
(`expMemberManagement::ensure()` in `kernel/setup/steps/ezstep_create_sites.php`), für jedes Website-Paket und die
Basisdaten. Die Multisite-Installer (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) rufen denselben
Schritt aus ihrem eigenen Rollenschritt auf. Das Ergebnis mit den Standardwerten:

| Richtlinie der Rolle „Member management" | Einschränkung | Damit kann ein Redakteur |
| --- | --- | --- |
| content / read | Subtree Members | die Mitglieder und ihre Gruppen sehen |
| content / read | Node: der Benutzerstamm (content.ini `[NodeSettings] UserRootNode`) | den Reiter **Benutzer** öffnen; die Liste darunter zeigt nur die Gruppen, die er lesen darf |
| content / create | Class user, user_group; ParentClass user_group; Subtree Members | ein Mitglied oder eine Mitgliedergruppe in einer Mitgliedergruppe anlegen |
| content / edit | Class user, user_group; Subtree Members | ein Mitglied bearbeiten: Name, E-Mail-Adresse, Login, Passwort |
| content / remove | Class user, user_group; Subtree Members | ein Mitglied entfernen |
| content / manage_locations | Class user, user_group; Subtree Members | ein Mitglied in eine andere Mitgliedergruppe verschieben |
| content / versionread, versionremove, diff | Subtree Members | die Versionen eines Mitgliedskontos |
| user / preferences | – (siehe Schutz) | ein Mitglied aktivieren und deaktivieren, die Zahl gleichzeitiger Anmeldungen (user/setting) |

Die Rolle wird der Gruppe Editors **ohne** Einschränkung zugewiesen: Jede Inhaltsrichtlinie trägt den verwalteten
Teilbaum selbst. Nur so kann die Rolle auch den Benutzerstamm allein lesen (eine auf Members eingeschränkte Zuweisung
würde diese Richtlinie wegschneiden). Die Rolle Editor behält ihre eigene Zuweisung (Content und Media).

## Der Schutz

Eine Subtree-Einschränkung ist erfüllt, wenn **irgendein** Ort eines Objekts im Teilbaum liegt. Ein Administrator, der
auch einen Ort unter Members hätte, wäre damit in Reichweite eines Mitgliederverwalters. Der Schutz schließt das:
`eZContentObject::checkAccess()` und `eZContentObjectTreeNode::checkAccess()` fragen `expMemberManagement::guardAllows()`,
nachdem sie eine der geschützten Funktionen (`edit`, `move`, `remove`, `manage_locations`, `hide`, `translate`,
`versionremove`) für ein Konto erlaubt haben (ein Objekt einer Klasse mit Benutzerkonto-Attribut oder einer Klasse in
`GuardedClasses[]`, standardmäßig `user_group`). Jeder Ort dieses Kontos unter einer geschützten Gruppe muss dieselbe
Funktion für sich erlauben; tut es einer nicht, wird die Funktion verweigert. Administratoren sind nicht betroffen:
Ihre Richtlinien erlauben es überall.

Auch die verwaltete Gruppe selbst (Members) ist vor ihren Verwaltern geschützt: Eine Subtree-Richtlinie auf
`/1/5/12/` schließt Knoten 12 ein, ein Verwalter könnte sonst die ganze Gruppe mit allen Mitgliedern umbenennen,
verschieben oder entfernen. Für die Knoten der verwalteten Gruppen verlangt der Schutz dieselbe Funktion beim
Elternknoten (dem Benutzerstamm), die ein Verwalter nicht hat. Gruppen, die die Verwalter unter Members anlegen, dürfen
sie bearbeiten, verschieben und entfernen.

`user/setting` (aktivieren, deaktivieren, gleichzeitige Anmeldungen) wird mit `user/preferences` geöffnet, einer
Funktion ohne Einschränkungen. Mit `[MemberGuardSettings] SettingRequiresEdit=enabled` (Standard) verlangt es auch
`content/edit` für das Konto, sodass `user/preferences` allein nicht mehr jedes Konto der Installation erreicht. Eine
Website, die `user/preferences` einer Rolle ohne `content/edit` für Benutzer gegeben hat, verliert diesen Weg; geben
Sie der Rolle `content/edit`, eingeschränkt auf die Konten, die sie erreichen soll, oder setzen Sie
`SettingRequiresEdit=disabled`.

Ein Mitglied in eine geschützte Gruppe zu verschieben oder dort einen Ort hinzuzufügen, braucht `content/create` am
Ziel, das die Rolle nur unter Members gibt: verweigert. Einen Benutzer in einer geschützten Gruppe anzulegen, wird
ebenso verweigert, außer in Editors: Dort lässt die Rolle *Editor account requests* Redakteure ein Konto anlegen, das
dann auf die Freigabe durch einen Administrator wartet; ein Mitglied nach Editors zu verschieben oder dort einen Ort
hinzuzufügen, bleibt für sie verweigert ([Freigabe von Benutzerkonten](freigabe-von-benutzerkonten.md)).

## Einstellungen: `membermanagement.ini`

Gruppen werden als `remote:<remote id>` (die Remote-ID des Gruppenobjekts oder seines Knotens: Die Standardgruppen
behalten ihre auf jeder Installation), `node:<node id>` oder `name:<name>` (eine Gruppe direkt unter dem
Benutzerstamm) benannt.

```ini
[MemberManagementSettings]
MemberManagement=enabled            # disabled: Einrichtungsschritt und Befehl ändern nichts
Rules[]=editors_manage_members      # je ein Block [MemberRule_<name>]
ProtectedGroups[]=remote:9b47a45624b023b1a76c73b74d704acf   # Administrator users
ProtectedGroups[]=remote:3c160cca19fb135f83bd02d911f04db2   # Editors

[MemberGuardSettings]
Guard=enabled
GuardedClasses[]=user_group
GuardedFunctions[]=edit             # und move, remove, manage_locations, hide, translate, versionremove
SettingRequiresEdit=enabled

[MemberRule_editors_manage_members]
RoleName=Member management
ManagerGroups[]=remote:3c160cca19fb135f83bd02d911f04db2     # Editors
ManagedGroups[]=remote:5f7f0bdb3381d6a461d8c29ff53d908f     # Members
PolicySet=member_management
EntryNodes[]=users                  # nur Lesen dieses Knotens; "users" = der Benutzerstamm

[MemberPolicySet_member_management]
Policies[]=content/read
Policies[]=content/create;Class=user,user_group;ParentClass=user_group
Policies[]=content/edit;Class=user,user_group
...
Policies[]=user/preferences
```

Abweichungen gehören in `settings/override/membermanagement.ini.append.php` oder in die Einstellungen einer
Erweiterung:

- **Eine weitere Verwaltergruppe**, etwa eine Gruppe „Support", die die Mitglieder ebenfalls verwaltet: eine Regel mit
  eigenen `ManagerGroups[]` (oder die Gruppe zur vorhandenen Regel hinzufügen).
- **Partner, verwaltet von einem Partnerbüro**: eine Regel `ManagedGroups[]=name:Partners`,
  `ManagerGroups[]=name:Partner desk`.
- **Mitgliederbüro nur lesend**: ein Richtliniensatz nur mit `content/read` und `content/versionread`.
- **Keine Mitgliederverwaltung**: `MemberManagement=disabled` vor der Installation, oder die Rolle danach entfernen.

Eine Regel wird als Ganzes abgewiesen (der Einrichtungsschritt überspringt sie und nennt den Grund im Setup-Protokoll,
der Befehl gibt `FAIL` aus), wenn eine verwaltete Gruppe eine geschützte Gruppe ist, eine enthält oder unter einer
liegt, wenn eine Verwaltergruppe in der Gruppe liegt, die sie verwalten würde, wenn eine Gruppe nicht gefunden wird,
wenn eine Richtlinienzeile `module/*`, ein Modul der Administration (`role`, `setup`, `section`, `class`, `state`,
`workflow`), eine unbekannte Klasse oder ihre eigene `Subtree`/`Node`-Einschränkung nennt. Richtlinien werden nie
erweitert: Eine Zeile, deren Einschränkung sich nicht auflösen lässt, entfällt ganz.

## Das Auditprotokoll

Das Auditprotokoll des Kernels ([audit.md](audit.md)) zeichnet jede Aktion der Mitgliederverwaltung auf, aus jeder
Oberfläche (die Admin-Designs, der Siteaccess editor, REST, Befehle), unter Apache wie unter Velocity, im Kanal
`access`. Jeder Eintrag nennt, wer (Benutzer-ID und Login, Siteaccess, Host, Adresse nach den Datenschutzregeln,
User-Agent), wann, das Konto (ID und Login) und was sich geändert hat:

| Ereignis | Wann | Vorher → nachher |
| --- | --- | --- |
| `access.user.create` | ein Konto wird angelegt | – → Login, E-Mail (Datenschutzregeln), Gruppen |
| `access.user.change` | eine neue Version eines Kontos wird veröffentlicht | die geänderten Felder als Text → dieselben Felder (nie das Benutzerkonto-Attribut) |
| `access.user.email.change` | die E-Mail-Adresse ändert sich | E-Mail → E-Mail (standardmäßig gehasht) |
| `access.user.login.change` | der Login ändert sich | Login → Login |
| `access.user.password.change` | das Passwort wird gesetzt oder geändert | – (nie das Passwort oder sein Hash) |
| `access.user.enable` / `access.user.disable` | user/setting | enabled, max_login → dieselben |
| `access.user.move` | ein Konto wird in eine andere Gruppe verschoben | Gruppe → Gruppe |
| `content.node.remove` (an), `content.node.add` (standardmäßig aus: `Enabled[]=content.node.add`) | ein Ort wird entfernt oder hinzugefügt | Elternknoten → Elternknoten |
| `access.user.remove` | ein Konto wird entfernt | Login, E-Mail → – |
| `content.object.restore` | ein Konto kommt aus dem Papierkorb zurück | – |
| `access.role.assign` / `access.role.unassign` | eine Rolle wird zugewiesen oder entzogen | Einschränkung |
| `access.permission.refused` | eine Ansicht wird verweigert: einen Administrator bearbeiten, user/setting eines Redakteurs, role/assign | – → die erfragte Richtlinie, Einschränkung; Ziel ist das Konto (ID, Login), wenn die Ansicht eines nennt |

Das Protokoll besteht aus Dateien mit einer Hash-Kette (nur anhängend; niemand bearbeitet einen Eintrag über die
Oberfläche) und einem Index in der Datenbank der Website (`expaudit_event` und seine Nebentabellen, in
`share/db_schema.dba` für jede Engine; `update/common/scripts/6.0/createaudittables.php` für eine Installation, die
älter ist als sie). Administratoren lesen es im Reiter **Audit** (`audit/dashboard`, `audit/console`, Richtlinie
`audit/read`, einschränkbar nach Kanal), filtern nach Ereignis, Benutzer, Objekt, Zeit und Text und exportieren einen
Filter als CSV, JSON-Zeilen oder JSON (`audit/export`). Was ein Eintrag enthalten darf, regelt
`audit.ini [AuditPrivacySettings]`: `Field[]` je Feld `full`, `truncate`, `hash` oder `off` (`actor.ip=truncate`
behält /24 und /48), `NeverRecord[]` lässt Schlüssel überall weg (fügen Sie einen Feldbezeichner der Benutzerklasse
hinzu, um ihn aus `access.user.change` herauszuhalten), `PseudonymiseAfterDays`. Die Aufbewahrung gilt je Kanal
(`[AuditChannel_access] LiveDays`, `ArchiveDays`). Welche Ereignisse aufgezeichnet werden, regelt
`[AuditEventSettings] Enabled[]`/`Disabled[]` (Muster); Erweiterungen fügen ihre eigenen Namen und Senken hinzu
(`[AuditSinkSettings] SinkClasses[]`: syslog, Webhook, E-Mail oder eine Klasse, die `expAuditSink` implementiert),
ohne den Kernel zu ändern.

Die Ereignisse sind allgemein: je Benutzer, nach Namen typisiert, mit Akteur, Ziel und vorher/nachher. Ereignisse der
Sitzungsverfolgung, des Workflows und der Zusammenarbeit kommen als weitere Namen in dasselbe Protokoll, nicht in eine
andere Tabelle.

Je Konto stehen dieselben Ereignisse auch im Benutzer-Auditprotokoll, der Tabelle `expuser_audit`: **Benutzer >
Benutzer-Auditprotokoll** (`user/audit`, Richtlinie `user/audit`) zeigt, was einem Mitglied geschah und was ein
Redakteur getan hat, mit den Feldern vorher und nachher, abgewiesene Versuche eingeschlossen, und exportiert es als
CSV. Siehe [Benutzer-Auditprotokoll](../../funktionen/6.0/benutzer-auditprotokoll.md).

## Konten in geschützten Gruppen

Die geschützten Gruppen bleiben außerhalb jeder Regel: Einen Benutzer nach Administrator users oder Editors zu
verschieben und ihre Konten zu bearbeiten, bleibt Sache der Administratoren. Die Mitgliederverwaltung fügt keinen
eigenen Warte- oder Freigabezustand hinzu.

Das Anlegen eines neuen **Redakteurs** ist die eine Ausnahme, und sie läuft über eine Freigabe: Die Rolle *Editor
account requests* lässt Redakteure ein Konto direkt in Editors anlegen, und das Workflow-Ereignis *Approve user
accounts* hält es zurück, bis ein Administrator es freigibt. Siehe
[Freigabe neuer Benutzerkonten](freigabe-von-benutzerkonten.md).

## Was auf einer bestehenden Installation zu tun ist

```bash
php bin/php/addmembermanagement.php               # was angelegt, hinzugefügt und zugewiesen würde; ändert nichts
php bin/php/addmembermanagement.php --apply       # ausführen; prüft das Ergebnis, gibt PASS oder FAIL aus
php bin/php/addmembermanagement.php --apply --rule=editors_manage_members
```

Ohne `--apply` ändert das Skript nichts (seit dem 28. Oktober 2026; vorher schrieb es standardmäßig und nahm
`--dry-run` an, das weiterhin angenommen wird und dasselbe bedeutet wie keine Option). Der Probelauf endet mit
`PASS: 0 change(s) would be made (dry run, nothing changed; --apply writes)`, wenn die Installation vollständig ist.
`--apply` zusammen mit `--dry-run` wird abgelehnt (Exit-Code 2).

Mit `--apply` legt das Skript die Rolle an, wenn sie fehlt, fügt die geplanten Richtlinien hinzu, die die Rolle nicht genau in
dieser Form hat (ein zweiter Lauf fügt nichts hinzu), weist die Rolle den Verwaltergruppen zu und verwirft die
Rollen- und Benutzer-Caches. Es entfernt nie eine Richtlinie. Sobald die Kernel-Dateien da sind, leeren Sie den
INI-Cache (`--clear-tag=ini`), damit `membermanagement.ini` gelesen wird, und starten Sie unter Velocity die Worker neu
(`exp:velocity deploy --kernel`): Der Schutz steckt in der Zugriffsprüfung des Kernels.

Code: `kernel/classes/expmembermanagement.php`, Befehl `kernel/private/classes/commands/addmembermanagement.php`,
Schutz in `kernel/classes/ezcontentobject.php` und `kernel/classes/ezcontentobjecttreenode.php`, user/setting in
`kernel/private/classes/views/user/setting.php`, `access.user.change` in
`kernel/content/ezcontentoperationcollection.php`, `access.user.move` in `ezcontentobjecttreenode.php`. Test ohne
Datenbank: `tests/tests/kernel/classes/expMemberManagementTest.php`.
