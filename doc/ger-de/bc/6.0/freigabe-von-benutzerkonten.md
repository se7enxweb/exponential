# Freigabe neuer Benutzerkonten: Administratoren geben neue Redakteure frei

Redakteure verwalten die Mitglieder der Website (englisch: [member-management.md](../../../eng-us/bc/6.0/member-management.md)).
Seit 6.0.15 dürfen sie auch das Konto eines neuen **Redakteurs** anlegen, aber dieses Konto wird erst veröffentlicht,
wenn ein **Administrator** es freigibt. Bis dahin kann es sich nicht anmelden. Die Freigabe läuft über das
Workflow-System und das Kollaborationssystem, so wie die Freigabe von Inhalten (das Workflow-Ereignis *Freigabe*,
`ezapprove`) es immer getan hat; die Freigebenden bekommen zusätzlich eine E-Mail mit dem Link zur Freigabe.

Welche Benutzergruppen eine Freigabe brauchen, wer freigibt und wer keine Freigabe braucht, sind die Einstellungen des
Workflow-Ereignisses **Benutzerkonten freigeben** (englisch *Approve user accounts*, `event_expuserapprove`), die unter
Setup > Workflows wie bei jedem anderen Ereignis geändert werden. Eine neue Installation wird mit den Standardwerten
eingerichtet; eine bestehende bekommt sie mit `bin/php/adduserapproval.php`.

## Was geschieht

1. Ein Redakteur legt einen Benutzer in **Editors** an (Benutzer > Editors > Hier anlegen > Benutzerkonto) und drückt
   *Zur Veröffentlichung senden*. Der Workflow auf content/publish (vorher) führt das Ereignis aus.
2. Das Ereignis sieht ein Benutzerkonto, dessen neue Version es in eine Gruppe mit Freigabepflicht setzt, veröffentlicht
   von jemandem, der eine Freigabe braucht. Es legt ein Freigabeelement an (der Kollaborations-Handler `ezapprove`,
   dasselbe Element wie bei der Inhaltsfreigabe), in den Kollaborationslisten der Freigebenden und des Redakteurs, und
   hält die Veröffentlichung an: die Version ist *ausstehend*, das Objekt ist nicht veröffentlicht, und das Konto **kann
   sich nicht anmelden** (die Anmeldung verlangt ein veröffentlichtes Konto).
3. Jeder Freigebende mit E-Mail-Adresse bekommt die Mail *New user account waiting for approval: <Name>*: wer gefragt
   hat, Name und Benutzername des Kontos, die Gruppe und genau einen Link:
   `<Administration>/collaboration/item/full/<id>`. Der Link enthält keinen Schlüssel und kein Token. Er verlangt die
   Anmeldung und gibt selbst nichts frei und lehnt nichts ab.
4. Der Freigebende öffnet das Element (aus der Mail oder unter Dashboard > Kollaboration), sieht die Vorschau des Kontos,
   schreibt bei Bedarf einen Kommentar und drückt **Freigeben** oder **Ablehnen** (beides mit Rückfrage). Dann fragt
   „Identität bestätigen“ (die Gruppe users, `collaboration/action other CollaborationAction_Accept
   CollaborationAction_Deny CollaborationAction_Defer`): Die Entscheidung wird angehalten, bis der Freigebende mit dem
   Passwort, einem Passkey oder einem Code bestätigt, und dann so ausgeführt, wie sie gedrückt wurde. Inhaltsfreigaben
   fragen nicht ([Passkeys und „Identität bestätigen“](passkeys-und-identitaet-bestaetigen.md)).
5. **Freigeben**: der Workflow läuft sofort weiter (nicht erst beim nächsten Lauf des Workflow-Cronjobs): das Konto
   wird unter Editors veröffentlicht und kann sich anmelden. **Ablehnen**: die Version ist wieder ein Entwurf des
   Redakteurs, nichts wird veröffentlicht, das Konto kann sich nicht anmelden. Der Redakteur kann den Entwurf ändern und
   erneut senden; dann wird wieder eine Freigabe verlangt.
6. Anfrage, Freigabe, Ablehnung und jeder abgewiesene Umgehungsversuch stehen im Prüfprotokoll.

Ein Administrator, der selbst ein Redakteurskonto anlegt, veröffentlicht es direkt: Freigebende brauchen nie eine
Freigabe.

## Was eine neue Installation bekommt

Der Einrichtungsassistent und der Kickstarter rufen nach dem Schritt der Mitgliederverwaltung
`expUserApproval::ensure()` auf (`kernel/setup/steps/ezstep_create_sites.php`), für jedes Site-Paket. Es legt an, was
fehlt:

| Was | Standard |
| --- | --- |
| Workflow | **Approve new editor accounts** in der Workflow-Gruppe *Standard* |
| Ereignis | **Approve user accounts**: Gruppen mit Freigabepflicht *Editors*; freigebende Gruppen *Administrator users*; Gruppen ohne Freigabe keine; Mail an die Freigebenden an |
| Trigger | content / publish / before führt den Workflow aus |
| Rolle | **Editor account requests**, Editors ohne Einschränkung zugewiesen: `content/read` mit `Node` = Knoten der Gruppe Editors (um die Gruppe zu öffnen), `content/create` mit `Class` = user, `ParentClass` = user_group, `Node` = Knoten der Gruppe Editors (um ein Konto direkt darin anzulegen) |

Die Rolle gibt unter Editors nichts weiter: die Konten anderer Redakteure bleiben außer Reichweite eines Redakteurs (der
Schutz der Mitgliederverwaltung sorgt dafür), und ein Redakteur sieht in der Liste der Gruppe keinen anderen
Redakteur.

## Die Einstellungen des Ereignisses

| Einstellung | Feld | Bedeutung |
| --- | --- | --- |
| Benutzergruppen, deren neue Konten eine Freigabe brauchen | `data_text1` | ein Konto, das in eine dieser Gruppen oder darunter veröffentlicht wird, wartet |
| Benutzer, die die Konten freigeben | `data_text3` | diese Benutzer geben frei (und brauchen selbst keine Freigabe) |
| Gruppen, die die Konten freigeben | `data_text4` | jeder Benutzer unter diesen Gruppen gibt frei |
| Benutzergruppen ohne Freigabe | `data_text2` | ihre Mitglieder veröffentlichen solche Konten direkt |
| E-Mail an die Freigebenden | `data_int1` (0 = ja, 1 = nein) | die Mail mit dem Link zum Element |

Mindestens ein freigebender Benutzer oder eine freigebende Gruppe und eine Gruppe mit Freigabepflicht sind nötig; ohne
sie speichert das Formular das Ereignis nicht.

**Welche Platzierungen eine Freigabe brauchen.** Die der veröffentlichten Version, die das Konto noch nicht hat und die
in einer Gruppe mit Freigabepflicht liegen: ein neues Konto in Editors, ein bestehendes Konto, das im Bearbeitungsformular
einen Ort in Editors bekommt, eine Kopie eines Kontos nach Editors. Das Bearbeiten eines Kontos, das schon in Editors
ist (Name, Passwort), braucht keine. Benutzergruppen zählen wie Konten.

**Wer keine braucht.** Die freigebenden Benutzer, jedes Mitglied einer freigebenden Gruppe und die Mitglieder der Gruppen
ohne Freigabe. Alle anderen warten, auch eine anonyme Registrierung, deren Platzierung in einer solchen Gruppe liegt.

## Verschieben und Ort hinzufügen

Ein Konto (oder eine Benutzergruppe) in eine Gruppe mit Freigabepflicht zu verschieben oder dort einen Ort
hinzuzufügen, veröffentlicht nichts und kann darum nicht auf eine Freigabe warten. Es wird jedem verweigert, der beim
Veröffentlichen derselben Platzierung warten müsste (`expUserApproval::placementAllowed()`, gefragt von content/move,
`eZContentObjectTreeNodeOperations::move()` und `eZContentOperationCollection::addAssignment()`). Die Verweigerung wird
als `access.user.approval.refused` protokolliert.

## Die Mail

`design:workflow/eventtype/expuserapprove_mail.tpl` (HTML mit Textteil, im Aussehen von `design:mail/layout.tpl`),
Mail-Kategorie `security` (wesentlich: die Mail-Schranke hält sie nie zurück), eine Mail je Freigebendem. Die Adresse
der Administration ist die der Mail „Neuer Benutzer registriert“: `site.ini [UserSettings] RegistrationAdminBaseURL`,
sonst https:// und die SiteURL von `RegistrationAdminSiteAccess`, sonst die Adresse dieses Siteaccess. Setzen Sie
`RegistrationAdminBaseURL`, wenn die Administration einen eigenen Host hat.

Der Link öffnet nur das Freigabeelement. Freigeben und Ablehnen sind die Schaltflächen des Elements in der
Administration: kein Ein-Klick-Link, kein Schlüssel, kein Token in der Mail.

## Das Prüfprotokoll

| Ereignis | Wann | Akteur | Objekt, Ziel, danach |
| --- | --- | --- | --- |
| `access.user.approval.request` | das Ereignis hält ein Konto zur Freigabe an | wer veröffentlicht hat | das Konto; das Freigabeelement; Version, Gruppen, Zahl der Freigebenden, gesendete Mails, Workflow, Prozess |
| `access.user.approval.approve` | ein Freigebender gibt frei | der Freigebende | das Konto; das Element; Version, Prozess, wer gefragt hat |
| `access.user.approval.deny` | ein Freigebender lehnt ab | der Freigebende | das Konto; das Element; Version, Prozess, wer gefragt hat |
| `access.user.approval.refused` | ein Verschieben oder neuer Ort in eine Gruppe mit Freigabepflicht wird verweigert | wer es versucht hat | das Konto oder die Gruppe; der Zielknoten; Workflow, Ereignis |

Alle vier sind standardmäßig an, im Kanal `access`.

## Auf einer bestehenden Installation

```bash
php bin/php/adduserapproval.php --dry-run        # was es anlegen und hinzufügen würde
php bin/php/adduserapproval.php                  # ausführen; prüft das Ergebnis, gibt PASS oder FAIL aus
```

Der Befehl legt Workflow, Ereignis und Trigger an, wenn sie fehlen. Führt content/publish (vorher) schon einen anderen
Workflow aus (je Trigger läuft nur ein Workflow), kommt das Ereignis an die **erste** Stelle dieses Workflows, damit ein
wartendes Konto die anderen Ereignisse nicht startet. Er legt die Rolle *Editor account requests* an und weist sie den
anfragenden Gruppen zu. Ein zweiter Lauf ändert nichts. Optionen: `--required=<Gruppe>` (Standard Editors),
`--approvers=<Gruppe>` (Standard Administrator users), `--requesters=<Gruppe>` (Standard Editors), `--no-role`,
`--admin-id=<id>`; Gruppen als `remote:<Remote-ID>`, `node:<Knoten-ID>` oder `name:<Name einer obersten Gruppe>`.
Danach `--clear-tag=ini` und auf Velocity `exp:velocity deploy --kernel`.

## Einzelheiten

- Das Element ist ein gewöhnliches Element des Handlers `ezapprove`; die Tabelle `ezapprove_items` verbindet es mit dem
  Workflow-Prozess.
- Nach *Freigeben* oder *Ablehnen* führt `expUserApproval::decided()` den wartenden Prozess einmal aus, wie
  `cronjobs/workflow.php`. Schlägt das fehl, bleibt der Prozess und der Workflow-Cronjob beendet ihn.
- Niemand zum Freigeben (eine leere freigebende Gruppe): die Veröffentlichung wird verweigert (die Version ist wieder ein
  Entwurf), das Fehlerprotokoll nennt das Ereignis.

Code: `kernel/classes/workflowtypes/event/expuserapprove/expuserapprovetype.php`, `kernel/classes/user/expuserapproval.php`,
der Haken in `kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php`, der Befehl
`kernel/private/classes/commands/adduserapproval.php`. Test ohne Datenbank:
`tests/tests/kernel/classes/expUserApprovalTest.php`. Anleitung:
[Neue Redakteurskonten freigeben](../../anleitungen/neue-redakteurskonten-freigeben.md).
