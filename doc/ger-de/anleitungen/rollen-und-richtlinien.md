# Rollen, Richtlinien und nicht aktivierte Benutzer

Diese Anleitung richtet sich an Administratoren. In etwa 25 Minuten lesen Sie, was jede Rolle gewährt und wem, bauen
eine Rolle mit eingeschränkten Richtlinien, bringen ihre Richtlinien in eine Reihenfolge, weisen sie einer Gruppe zu
und räumen Registrierungen auf, die niemand abgeschlossen hat.

Alle Seiten liegen in der Administration unter **Benutzer**: **Rollen und Richtlinien** (`role/list`) und **Nicht
aktivierte Benutzer** (`user/unactivated`). Der Editor-Siteaccess verweigert das Modul role; verwenden Sie den
Administrations-Siteaccess.

## Kurz gesagt

- Eine **Rolle** ist eine Menge von **Richtlinien**. Eine Richtlinie erlaubt ihren Benutzern, ein Modul oder eine
  Funktion eines Moduls zu verwenden, möglicherweise nur in bestimmten Sektionen, Klassen, Teilbäumen, Sprachen oder
  Siteaccesses.
- Eine Rolle bewirkt nichts, bis sie Benutzern oder Benutzergruppen **zugewiesen** ist. Ein Benutzer hat jede
  Richtlinie jeder Rolle, die ihm oder einer seiner Gruppen zugewiesen ist.
- Jede Änderung im Rolleneditor wird in einem **Entwurf** vorgenommen. Für niemanden ändert sich etwas, bis Sie
  **Speichern** drücken; **Abbrechen** verwirft den Entwurf.
- Eine Rolle mit einer Richtlinie über **jedes Modul** gewährt **vollen Zugriff**: Die Liste und die Rollenseite
  markieren sie rot. Eine Richtlinie über das Modul **role** erlaubt ihren Benutzern, Rollen zu ändern und sich so
  jeden beliebigen Zugriff zu verschaffen: gelb markiert.

## 1. Die Rollenliste lesen

Öffnen Sie **Benutzer > Rollen und Richtlinien**. Jede Rolle ist eine Karte mit ihrer ID, der Anzahl ihrer
Richtlinien und der Anzahl der Benutzer und Gruppen, denen sie zugewiesen ist (die Zahl verlinkt auf die
Rollenseite), sowie ihren Aktionen: Ansehen, Bearbeiten, Zuweisen, Kopieren.

- **Eine Rolle finden**: ein beliebiger Teil des Namens oder die ID. Groß- und Kleinschreibung spielen keine Rolle.
- **Reihenfolge**: nach Name, ID, Anzahl der Richtlinien oder Anzahl der Zuweisungen. Klicken Sie erneut auf die
  aktuelle Reihenfolge, um sie umzukehren. Reihenfolge und Suche bleiben in der Adresse erhalten
  (`/role/list/(sort)/policies/(dir)/desc/(q)/edit`), sodass Sie sie als Lesezeichen speichern können. Die
  Sortierungen nach Anzahl werden bis zu 500 Rollen angeboten.
- **Abzeichen**: *Voller Zugriff* (rot), *Kann Rollen ändern* (gelb), *Nicht zugewiesen* (die Rolle gewährt
  niemandem etwas).
- **Pro Seite**: Die Größen stammen aus `site.ini [RoleSettings] RolesPerPageList`.

Erwartet: Administrator zeigt *Voller Zugriff*; Anonymous zeigt bei einer frischen Installation etwa 15 Richtlinien
und 3 Zuweisungen.

## 2. Eine Rolle lesen

Klicken Sie auf eine Rolle. Die Seite zeigt:

- **Die Zahlen**: Richtlinien; Zuweisungen; Benutzergruppen; direkt zugewiesene Benutzer; und **insgesamt betroffene
  Benutzer**: jeder Benutzer, den die Rolle erreicht, wobei die Mitglieder ihrer Gruppen jeweils einmal gezählt
  werden (ein Benutzer in zwei ihrer Gruppen zählt einmal). Benutzer einer Zuweisung mit Teilbaum- oder
  Sektionseinschränkung zählen ebenfalls; die Zeile unter den Zahlen gibt an, wie viele Zuweisungen eingeschränkt sind.
- **Die Richtlinien als Sätze**, zum Beispiel:
  - *Darf Inhalte lesen, in der Sektion Standard, der Klassen Article und Folder*
  - *Darf sich anmelden, in den Siteaccesses site und bold*
  - *Darf alles tun, in jedem Modul* (voller Zugriff)

  Unter jedem Satz: die ID der Richtlinie, ihr Modul und ihre Funktion sowie ihre Einschränkungen, wie sie
  gespeichert sind, mit Links zu den Knoten und Teilbäumen. Eine Einschränkung, die kein Handler auswertet, ist mit
  *(ohne Auswertung, verweigert)* markiert: Diese Richtlinie gewährt keinen Zugriff. Sortieren Sie die Liste nach
  Reihenfolge der Rolle, Modul (unter jedem Modul gruppiert), Funktion oder Einschränkung.
- **Die Benutzer und Gruppen**, denen die Rolle zugewiesen ist, mit ihrer Einschränkung (*Nur in der Sektion
  Standard*, *Nur im Teilbaum Media*), einem Namensfilter, wenn es viele sind, und Blätterfunktion. Eine Zuweisung,
  deren Benutzer oder Gruppe entfernt wurde, wird zuerst aufgeführt und kann entfernt werden.

## 3. Eine Rolle mit eingeschränkten Richtlinien anlegen

1. Klicken Sie in der Rollenliste auf **Neue Rolle**. Der Editor öffnet sich mit einem neuen Entwurf.
2. Geben Sie den Namen ein, zum Beispiel `News editors`.
3. Klicken Sie auf **Neue Richtlinie**. Der Assistent zeigt seine drei Schritte: Modul, Funktion, Einschränkungen.
   - **Schritt 1, Modul**: Wählen Sie `content`. Mit JavaScript werden die Funktionen des Moduls sofort angeboten.
     *Zugriff auf alle Funktionen gewähren* fügt sofort eine Richtlinie über das gesamte Modul hinzu. *Jedes Modul*
     mit allen Funktionen zu wählen bedeutet vollen Zugriff; die Seite warnt, bevor Sie es hinzufügen.
   - **Schritt 2, Funktion**: Wählen Sie `read`. *Vollen Zugriff gewähren* fügt sie ohne Einschränkungen hinzu;
     *Eingeschränkten Zugriff gewähren* führt zu Schritt 3.
   - **Schritt 3, Einschränkungen**: Wählen Sie *Sektion: Standard* und *Klasse: Article* und *Folder* (Strg oder Cmd
     für mehrere). *Beliebig* lässt eine Einschränkung weg. Mehrere Einschränkungen müssen alle erfüllt sein; mehrere
     Werte einer Einschränkung sind Alternativen. Knoten und Teilbäume werden im Content-Browser ausgewählt. Drücken
     Sie **OK**.
4. Der Editor führt die Richtlinie als *Darf Inhalte lesen, in der Sektion Standard, der Klassen Article und Folder*
   auf und meldet **Nicht gespeicherte Änderungen**.
5. Drücken Sie **Speichern**. Die Rollenseite öffnet sich.

Um die Einschränkungen einer Richtlinie später zu ändern, klicken Sie im Editor daneben auf **Einschränkungen**.

## 4. Die Richtlinien in eine Reihenfolge bringen

Die Reihenfolge der Richtlinien ändert nichts daran, was jemand tun darf: Jede Richtlinie gewährt Zugriff für sich.
Sie hilft beim Lesen einer Rolle. Die Reihenfolge sind die IDs der Richtlinien im Entwurf (siehe
[Rollen: Richtlinien-IDs, Sortierung und Reihenfolge-Schaltflächen](../../eng-us/features/6.0/role-policy-order.md)); alle
Arten des Verschiebens verwenden sie.

Behalten Sie im Editor **Reihenfolge der Rolle** (ID aufsteigend) bei, dann:

- **Ziehen** Sie eine Richtlinie an ihrem Griff (dem gepunkteten Anfasser) an ihren neuen Platz. Die Seite sendet die
  Verschiebung, die Richtlinie landet dort, und die Seite meldet *Die Richtlinie wurde an Position N verschoben*.
- **Tastatur**: Gehen Sie mit Tab zum Griff und drücken Sie die Taste **Pfeil nach oben** oder **Pfeil nach unten**.
  Der Griff behält an seinem neuen Platz den Fokus.
- **Ohne JavaScript**: Die Schaltflächen **nach oben** und **nach unten** verschieben um einen Platz, auch über
  Seiten hinweg; das **Positionsfeld** und **Verschieben** setzen eine Richtlinie an eine beliebige Stelle der
  gesamten Liste.
- Drücken Sie **Speichern**, um die Reihenfolge zu behalten, oder **Abbrechen**, um sie zu verwerfen.

Ziehen wird nur in der Reihenfolge der Rolle und innerhalb der angezeigten Seite angeboten: In einer anderen
Sortierung ist die Liste nicht die Reihenfolge der Rolle, und Ziehen erreicht nur, was auf dem Bildschirm ist. Um
weiter zu verschieben, verwenden Sie das Positionsfeld.

## 5. Die Rolle zuweisen

Auf der Rollenseite:

- **Zuweisen** öffnet den Content-Browser im Benutzerbaum: Wählen Sie Benutzer oder Gruppen. Bevorzugen Sie Gruppen:
  Jedes Mitglied, jetzt und später, erhält die Rolle.
- **Mit Einschränkungen zuordnen**: Wählen Sie *Teilbaum* oder *Sektion*, dann den Teilbaum oder die Sektion, dann
  die Benutzer oder Gruppen. Die Rolle gilt dann nur dort.
- **Ausgewählte entfernen**: Kreuzen Sie Zuweisungen an, öffnen Sie die Bestätigung und drücken Sie *Angekreuzte
  Zuweisungen entfernen*. Die Benutzer und Gruppen verlieren die Rolle sofort; sie selbst werden nicht entfernt.

## 6. Rollen kopieren und entfernen

- **Kopieren** öffnet eine Seite, die angibt, was die Kopie enthält; *Kopie erstellen* legt „Kopie von <name>“ mit
  denselben Richtlinien und ohne Zuweisungen an und öffnet sie im Editor. Das bloße Öffnen der Kopier-Adresse
  erzeugt nichts.
- **Entfernen**: Kreuzen Sie in der Rollenliste Rollen an, öffnen Sie *Ausgewählte entfernen* und bestätigen Sie. Die
  Rollen, ihre Richtlinien und Zuweisungen werden endgültig entfernt.

## 7. Nicht aktivierte Benutzer aufräumen

**Benutzer > Nicht aktivierte Benutzer** listet Personen auf, die sich registriert, aber nie auf den Link ihrer
Aktivierungsmail geklickt haben.

- Die Überschrift zählt sie. **Eine Registrierung finden** durchsucht Login, E-Mail und Namen. Sortieren Sie nach
  Registrierungsdatum, Login, E-Mail oder Namen. Eine Registrierung, die älter als 30 Tage ist, ist mit **Alt**
  markiert.
- Kreuzen Sie Benutzer an und öffnen Sie dann eine der Bestätigungen:
  - **Ausgewählte Benutzer aktivieren**: Die Konten funktionieren sofort, als hätten ihre Inhaber auf den Link
    geklickt. Eine Registrierung, die auf ihre Aktivierung wartet, wird so abgeschlossen, wie es der Link tut (das
    Benutzerobjekt wird veröffentlicht, der Workflow post_register läuft); ein Konto ohne eine solche (durch ein
    Skript oder einen Import angelegt) wird von Hand veröffentlicht. In beiden Fällen erhält der Benutzer die Mail
    „Registrierung freigegeben“ (`user/registrationapproved.tpl`), außer bei
    `site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail=disabled` (nur diese Seite) oder
    `EmailRegistrationInfo=disabled` (überall). Das Aktivieren sendet niemals einen neuen Aktivierungslink.
  - **Aktivierungsmail erneut senden**: Jeder erhält einen neuen Link; der frühere Link funktioniert nicht mehr. Die
    Seite nennt den Mail-Transport (`site.ini [MailSettings] Transport`). Ein Benutzer ohne gültige Adresse wird
    gemeldet, nicht angeschrieben.
  - **Ausgewählte Benutzer entfernen**: endgültig entfernt, mitsamt ihren Benutzerobjekten.
- **Alle unaktivierten Benutzer entfernen** entfernt jeden nicht aktivierten Benutzer, auf jeder Seite. Bei aktiver
  Suche lautet die Schaltfläche *Alle N Treffer der Suche entfernen* und entfernt nur diese. Die Bestätigung nennt die
  Anzahl. Benutzer werden in Stapeln zu 50 entfernt. Jeder wird unmittelbar vor dem Entfernen erneut geprüft. Niemals
  entfernt werden: wer inzwischen aktiviert wurde, der anonyme Benutzer, das Konto `admin` und Sie selbst. Die Seite
  meldet *N entfernt, M übersprungen* mit den Gründen. Das Entfernen wird im Audit-Protokoll als
  `access.user.remove` mit den Anzahlen festgehalten.

Jede Aktion, hier und auf den Rollenseiten, erfordert das Formular-Token der Seite. Eine Anfrage ohne es wird mit 403
abgewiesen.

**Aus der Mail „Neuer Benutzer registriert“.** Mit `site.ini [UserSettings] RegistrationFeedback=email` sendet jede
Registrierung dem Administrator (`RegistrationEmail`, sonst `[MailSettings] AdminEmail`) eine HTML-Mail mit den
Angaben des Kontos, seinem Zustand und signierten Links: **Konto ansehen**, **Konto aktivieren** (oder **Freigeben**,
wenn das Freigabeelement eines Workflows es zurückhält), **Ablehnen und entfernen**, das Freigabeelement und die Liste
der nicht aktivierten Benutzer. Jeder öffnet `user/registration/<token>` in der Administration; dafür müssen Sie mit
`user/activation` angemeldet sein (oder als Freigebender des wartenden Freigabeelements), und Sie werden um
Bestätigung gebeten; das Öffnen eines Links ändert nichts. Freigeben aktiviert das Konto so, wie es **Ausgewählte
Benutzer aktivieren** tut, oder nimmt das Freigabeelement an und veröffentlicht das Konto sofort; Ablehnen entfernt
das Konto. Links zum Freigeben und Ablehnen funktionieren einmal, jeder Link läuft ab. Einzelheiten:
[Signierte Links in der Mail „Neuer Benutzer registriert“](../../eng-us/features/6.0/registration-admin-links.md).

Die Einstellungen, in `[UserSettings]` (am besten in `settings/override/site.ini.append.php`):

| Einstellung | Standard | Was sie bewirkt |
|---|---|---|
| `RegistrationAdminLinks` | `enabled` | `disabled`: Die Mail enthält keine Links, und `user/registration` wird nicht gefunden |
| `RegistrationAdminBaseURL` | leer | die Adresse der Administration, auf die die Links zeigen; leer: `https://` und die `SiteURL` des Administrations-Siteaccess |
| `RegistrationAdminSiteAccess` | leer | der Administrations-Siteaccess; leer: derjenige, dessen `[SiteSettings] FrontSiteAccess` der aktuelle Siteaccess ist, sonst `admin` |
| `RegistrationActionTTL` | `604800` | wie lange ein Link funktioniert, in Sekunden (7 Tage) |

```ini
[UserSettings]
RegistrationAdminBaseURL=https://admin.example.com
# or, for an administration under a path of the site:
# RegistrationAdminBaseURL=https://www.example.com/admin
RegistrationActionTTL=259200
```

Wenn die `SiteURL` des Administrations-Siteaccess keine Adresse ist, die der Administrator aus der Mail erreichen
kann (ein interner Hostname, `localhost`, ein Siteaccess, der über den Port oder über einen Pfad gematcht wird, den
die `SiteURL` weglässt), setzen Sie `RegistrationAdminBaseURL`; andernfalls führen die Links ins Leere.

Für die Konten, die aktiviert wurden, listet **Benutzer > Benutzerkonten prüfen** (`user/hygiene`) diejenigen auf, die
sich nie angemeldet haben, lange nicht gesehen wurden, deaktiviert, gesperrt, verwaist sind oder eine E-Mail-Adresse
teilen, und deaktiviert, aktiviert oder entsperrt sie in großer Zahl: [Benutzerkonten prüfen](../../eng-us/features/6.0/user-hygiene.md). Mit `[UserSettings]
DeactivateAndMove=enabled` setzt **Deaktivieren und verschieben** Konten außer Dienst, statt sie zu entfernen, und
verschiebt sie bei der Reaktivierung zurück ([Deaktivieren und verschieben](../../eng-us/features/6.0/user-hygiene.md#deactivate-and-move), Funktion
`user/deactivate`). Eine Rolle wird der Gruppe der deaktivierten Benutzer niemals zugewiesen: Zuweisen lässt sie aus
und sagt das.

## 8. Festlegen, wer sich registrieren und wer Knotendaten lesen darf

Zwei Berechtigungen standen standardmäßig allen offen und können jetzt stattdessen über Rollen vergeben werden. Beide
Ideen stammen aus der Distribution LoveStack von Mugo Web (Commits `98854ab935` und `aa307a7ff3`), mit Dank.

**Ein neues Konto registrieren (`user/register`).** Auf einer neuen Installation erfordert das Registrieren die
Richtlinie `user/register`, und die Basisdaten geben sie der Rolle **Anonymous**, sodass Besucher sich registrieren
können. Um die Registrierung zu schließen, entfernen Sie die Richtlinie *User / Register* aus der Rolle Anonymous; um
sie auf einer Site, die sie geschlossen hat, zu öffnen, fügen Sie sie wieder hinzu. Die anderen Schritte des
Registrierens (`user/activate`, `user/success`, `user/forgotpassword`) und das Anmelden bleiben in
`PolicyOmitList[]`, und die Rolle Anonymous hat außerdem *User / Login* für die öffentlichen Siteaccesses. Die
Anmeldeseiten der Administration, des Standard-Designs, des Media-Designs und der Starter-Designs zeigen *Neues Konto
registrieren*, *Nicht registriert?* oder *Registrieren* nur einem Besucher, der `user/register` öffnen darf, sodass eine
Site ohne offene Registrierung keinen toten Link zeigt.

- Eine aktualisierte Site lässt die Registrierung wie bisher offen: `user/register` bleibt in `site.ini [RoleSettings]
  PolicyOmitList[]`, und der Link bleibt auf der Anmeldeseite.
- Um sie auf einer aktualisierten Site zu schließen, ergänzen Sie in `settings/override/site.ini.append.php`:

  ```ini
  [RoleSettings]
  PolicyOmitListExclude[]=user/register
  ```

  und leeren Sie dann den INI-Cache. `PolicyOmitListExclude[]` nimmt einzelne Einträge aus `PolicyOmitList[]` heraus,
  nachdem jede Einstellungsdatei gelesen wurde, sodass die Einträge erhalten bleiben, die die Extensions hinzufügen
  (`ezjscore/call` und andere).

**Knotendaten als JSON oder XML (`ezjscore/call/ezjscnode::load`, `::subtree`, `::updatepriority`).** Diese Aufrufe
liefern Name, IDs, Remote-ID, Eigentümer, Sektion, Datumsangaben, Klasse und Übersetzungen eines Knotens an jeden
Benutzer, der den Knoten lesen darf, anonyme Besucher eingeschlossen. Mit `ezjscore.ini [ezjscServer_ezjscnode]
RequirePolicy=enabled` braucht ein Benutzer zusätzlich die Richtlinie *ezjscore / call* mit der Funktionsliste
**ezjscnode**; eine Anfrage, deren Aufrufe alle abgewiesen werden, wird mit 403 Forbidden beantwortet.

- Neue Installationen schalten das ein und geben der Rolle **Editor** *ezjscore / call* mit der Funktionsliste
  `ezjscnode`, die die Liste der Unterelemente (ihre Ersatzliste und die Prioritätsspalte) und die Vorschau der
  Bildgröße im Rich-Text-Editor verwenden. Administratoren haben sie über vollen Zugriff.
- Eine aktualisierte Site behält `RequirePolicy=disabled`: Es ändert sich nichts. Um es einzuschalten, geben Sie zuerst
  jeder Rolle, deren Benutzer die Administration ohne vollen Zugriff verwenden, *ezjscore / call* mit der
  Funktionsliste `ezjscnode`, und ergänzen Sie dann in `settings/override/ezjscore.ini.append.php`:

  ```ini
  [ezjscServer_ezjscnode]
  RequirePolicy=enabled
  ```

  und leeren Sie den INI-Cache. Die mitgelieferten Designs rufen `ezjscnode` für anonyme Besucher nicht auf; das
  eigene JavaScript einer Site, das es tut, bräuchte die Richtlinie für die Rolle Anonymous.

## 9. Festlegen, wer OAuth-Clients autorisieren darf

Eine unter **Setup > oAuth-Verwaltung** registrierte Anwendung schickt Benutzer zu `oauth/authorize`, wo sie ihr
erlauben, in ihrem Namen zu handeln. Ab 6.0.15 erfordert das die Richtlinie *OAuth / Authorize* (`oauth/authorize`),
optional auf bestimmte Siteaccesses eingeschränkt; ein Benutzer ohne sie wird mit `error=access_denied` zur
Anwendung zurückgeschickt.

- Neue Installationen geben sie der Rolle **Editor**; Administratoren haben sie über vollen Zugriff.
- Führen Sie auf einer aktualisierten Site `php bin/php/addoauthauthorizepolicy.php --dry-run` aus, dann ohne
  `--dry-run`: Jede Rolle, die Benutzern das Anmelden erlaubt, erhält die Richtlinie für dieselben Siteaccesses,
  sodass niemand verliert, was er vorher tun konnte. Die ganze Geschichte und wie Sie das alte Verhalten eine Zeit lang
  beibehalten, steht in
  [dem Upgrade-Hinweis](../../eng-us/bc/6.0/oauth-authorize-policy.md).

## Wer das darf

- Die Rollenseiten erfordern Zugriff auf das Modul `role`. Die Seite der nicht aktivierten Benutzer erfordert
  `user/activation`, ebenso die Links zum Freigeben und Ablehnen in der Mail „Neuer Benutzer registriert“ (oder die
  Eigenschaft als Freigebender des wartenden Freigabeelements; ein Ansichtslink öffnet sich auch mit `content/edit` auf
  dem Konto); Benutzerkonten prüfen erfordert `user/hygiene` und für die E-Mail-Adressen `role/assign`; Deaktivieren
  und verschieben erfordert `user/deactivate` und die Inhaltsrechte zum Verschieben des Kontos.
- **Niemand gibt mehr, als er hat.** Mit `site.ini [RoleSettings] PreventPrivilegeEscalation=enabled` (die
  Voreinstellung) kann ein Benutzer, der Rollen bearbeiten darf, nur gewähren, was seine eigenen Rollen abdecken:
  - Im Richtlinien-Assistenten wird eine Richtlinie, die er nicht besitzt, sofort wieder herausgenommen, und die Seite
    meldet *Die Richtlinie wurde nicht hinzugefügt* und nennt sie.
  - **Speichern** im Rolleneditor weist einen Entwurf mit einer neuen oder geänderten Richtlinie ab, die er nicht
    besitzt (*Die Rolle wurde nicht gespeichert*); der Entwurf bleibt erhalten, sodass Sie die Richtlinie eingrenzen
    oder entfernen können.
  - Der Richtlinieneditor weist eine Änderung ab, die eine Richtlinie weiter fasst als seinen Zugriff; die Richtlinie
    bleibt, wie sie war.
  - **Kopieren** ist für eine Rolle mit einer Richtlinie jenseits seines Zugriffs abgeschaltet, und **Zuweisen** weist
    eine solche Rolle ab (*Die Rolle wurde nicht zugewiesen*). Eine Zuweisung mit Teilbaum- oder
    Sektionseinschränkung gilt als durch diese eingegrenzt.
- Was „abgedeckt“ bedeutet: Eine der Richtlinien des Bearbeiters hat dasselbe Modul (oder jedes Modul) und dieselbe
  Funktion (oder jede Funktion), und jede Einschränkung dieser Richtlinie ist mindestens ebenso eng erfüllt: ein
  Teilbaum innerhalb eines ihrer Teilbäume (oder ein Knoten darin), Sektionen, Klassen, Knoten, Sprachen,
  Siteaccesses, Eigentümer- und Zustandswerte unter ihren Werten. Zwei Richtlinien für Sektion A und Sektion B decken
  zusammen eine Richtlinie für A und B ab. Eine dem Bearbeiter für eine Sektion oder einen Teilbaum zugewiesene Rolle
  zählt als diese Einschränkung. Die Prüfung ist eher vorsichtig als raffiniert: Ein Teilbaum, der zufällig in einer
  Sektion liegt, gilt nicht als durch eine Richtlinie für diese Sektion abgedeckt.
- Benutzer mit jeder Funktion jedes Moduls (Administrator) dürfen alles gewähren. Setzen Sie
  `PreventPrivilegeEscalation=disabled`, um das alte Verhalten wiederherzustellen, bei dem jeder mit dem Modul `role`
  jeder Rolle jede Richtlinie geben konnte.
- Die Rollenliste markiert weiterhin Rollen, die ihren Benutzern erlauben, Rollen zu ändern (*Kann Rollen ändern*):
  Vergeben Sie sie mit Bedacht.

## Probleme

| Sie sehen | Warum | Tun Sie |
|---|---|---|
| *Nicht gespeicherte Änderungen* im Editor | Der Entwurf unterscheidet sich von der gespeicherten Rolle | Speichern oder Abbrechen |
| Der Browser fragt nach, bevor Sie den Editor verlassen | Der Entwurf oder der Name hat sich geändert | Speichern, oder Abbrechen zum Verwerfen |
| Kein Griff neben den Richtlinien | Die Liste ist nicht in der Reihenfolge der Rolle | Klicken Sie auf **Reihenfolge der Rolle** |
| *(ohne Auswertung, verweigert)* nach einer Einschränkung | Keine Extension wertet diese Einschränkung aus | Aktivieren Sie die Extension, die sie hinzufügt, oder entfernen Sie die Richtlinie |
| Ein Benutzer steht nach *Aktivieren* noch in der Liste | Er wurde inzwischen aktiviert oder ist nicht unaktiviert | Lesen Sie die Meldung über der Liste |
| *Die Richtlinie wurde nicht hinzugefügt* / *Die Rolle wurde nicht gespeichert* | Die Richtlinie geht über Ihren eigenen Zugriff hinaus | Grenzen Sie sie auf das ein, was Ihre Rollen erlauben, oder fragen Sie einen Administrator mit vollem Zugriff |

## Für Entwickler

- `expRolePolicySentence` (kernel/classes/role) formuliert eine Richtlinie; `expRolePage` liefert die Zeilen der
  Rollenliste, die Zusammenfassungen, die Benutzer, die eine Rolle erreicht, den Entwurfsvergleich und die
  Verschiebungen von Richtlinien (`moveSteps()`, `movePolicyTo()`, aufgebaut auf `eZRole::movePolicy()`);
  `expRoleGrantCheck` entscheidet, was ein Bearbeiter gewähren darf; `expUserActivation` schließt eine Aktivierung ab;
  `expUnactivatedUsers` (kernel/classes/user) liefert die Liste, das Alter, das erneute Senden und Alle entfernen
  (`removeAllWith()` nimmt die Datenbankarbeit als Funktionen entgegen).
- Tests ohne Datenbank: `php vendor/bin/phpunit tests/tests/kernel/classes/expRolePagesTest.php` und
  `tests/tests/kernel/classes/expRoleGrantCheckTest.php`.
- Jedes Template liegt in `design/admin` und `design/admin4`; das Erscheinungsbild ist `role/exp_style.tpl`, auf
  `.exp-roles` beschränkt. Kein gemeinsames Stylesheet wird geändert.

**Demo-Modus und die Rolle Administrator.** Auf einer öffentlichen Demo (Umgebungsvariable `EXP_DEMO_MODE=1`) gilt ein Konto, dessen Rollen uneingeschränkten Zugriff auf jedes Modul und jede Funktion gewähren, wie es die Rolle **Administrator** tut, als Administrator: Sein Passwort kann weder geändert noch zurückgesetzt werden. Konten mit eingeschränkten Rollen behalten die üblichen Passwortregeln. [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md).

## Siehe auch

- [Rollen: Richtlinien-IDs, Sortierung und Reihenfolge-Schaltflächen](../../eng-us/features/6.0/role-policy-order.md)
- [Blätterfunktion für Rollenzuweisungen](../../eng-us/features/6.0/role-assignment-paging.md)
- [Signierte Links in der Mail „Neuer Benutzer registriert“](../../eng-us/features/6.0/registration-admin-links.md)
- [Sektionen](sektionen.md), [Objektzustände](objektzustaende.md), [Sicherheit und Audit](sicherheit-und-audit.md)
- [Eigentümer ändern](eigentuemer-aendern.md): die Richtlinien `owner/change` und `owner/subtree` von ezownerchange
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
