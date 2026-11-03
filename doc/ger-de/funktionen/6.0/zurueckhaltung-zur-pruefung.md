# Zurückhaltung zur Prüfung: Bearbeiten und Veröffentlichen anhalten, bis ein Prüfer freigibt (6.0.15)

Lesen Sie diese Seite, wenn sich eine Seite nicht ändern darf, während jemand sie prüft (Recht, Compliance, ein Kunde,
eine Sperrfrist für die Presse), oder wenn Sie ein Werkzeug schreiben, das eine solche Zurückhaltung setzt oder aufhebt.
Die [Website-Seitenleiste](https://github.com/se7enxweb/expwt) bietet Zurückhaltungen in ihrem Page-Panel an.

## Was eine Zurückhaltung ist

Eine Zurückhaltung ist ein **Objektzustand**: die Zustandsgruppe `review_hold` mit zwei Zuständen.

| Zustand | Bezeichner | Bedeutung |
|---|---|---|
| Nicht zurückgehalten | `not_held` | die Voreinstellung jedes Objekts, alt und neu: Bearbeiten und Veröffentlichen, wie es die Rollen erlauben |
| Zur Prüfung zurückgehalten | `held` | Bearbeiten und Veröffentlichen warten, bis ein Prüfer das Objekt freigibt |

Weil es ein Objektzustand ist, funktioniert alles, was der Kernel über Zustände weiß, damit: der Reiter States des
Objekts in der Administration, `fetch( 'content', 'object' ).state_identifier_array`, die Limitierung
`StateGroup_review_hold` in jeder Richtlinie, Suchfilter auf Zustände.

## Was eine Zurückhaltung anhält

- **Bearbeiten**, durch die eigenen Richtlinien der Rollen. Jede Richtlinie `content/edit` und `content/publish` trägt
  die Limitierung `StateGroup_review_hold = not_held` (der Upgrade-Schritt fügt sie hinzu), sodass `canEdit()` für ein
  zurückgehaltenes Objekt falsch ist: kein neuer Entwurf, kein Bearbeiten eines vorhandenen Entwurfs, kein
  Bearbeiten-Knopf.
- **Veröffentlichen**, durch die Veröffentlichungsoperation selbst. Solange ein Objekt zurückgehalten ist, wird eine
  Version davon nur von einem Benutzer veröffentlicht, dessen `content/publish` den Zustand `held` erlaubt. Andernfalls
  wird die Veröffentlichung abgebrochen, die Version bleibt ein Entwurf, und `content.object.hold.refused` wird
  aufgezeichnet. Die Prüfung läuft, bevor sich irgendetwas ändert, und noch einmal nach den `pre_publish`-Workflows,
  sodass auch eine Veröffentlichung, die vor der Zurückhaltung **geplant** war oder **auf Freigabe wartete**, nicht
  stattfindet: Sie wird abgelehnt, wenn der Workflow weiterläuft, und die Version ist wieder ein Entwurf.

Eine Zurückhaltung verbirgt die Seite nicht: Besucher sehen weiter die veröffentlichte Version. Lesen, Verschieben,
Verbergen und Entfernen werden nicht verändert (beschränken Sie sie mit derselben Limitierung, wenn Sie möchten).

## Wer trotzdem bearbeiten darf

- Eine Richtlinie, die die Gruppe schon nennt, bleibt, wie sie ist. Eine Rolle, die zurückgehaltene Objekte bearbeiten
  darf (die eigenen Korrekturen der Prüfer), sagt das mit beiden Zuständen: `content/edit` mit
  `StateGroup_review_hold = not_held, held`.
- Modulweite Richtlinien (`content/*`, `*/*`) können keine Limitierung tragen: Administratoren bearbeiten und
  veröffentlichen weiter. Der Upgrade-Schritt listet jede solche Rolle.

## Wer eine Zurückhaltung setzt und aufhebt

Setzen und Aufheben sind `state/assign` der Gruppe. Der Upgrade-Schritt legt die Rolle **Review hold** an, niemandem
zugewiesen:

```
state/assign  StateGroup_review_hold = not_held, held   NewState = not_held, held
```

Weisen Sie sie Ihren Prüfern zu (Roles and policies > Review hold > Assign, auf einen Teilbaum beschränkt, wenn Sie
möchten). Eine Rolle, die Zurückhaltungen nur setzen darf, bekommt `NewState = held`; eine, die sie nur aufheben darf,
`NewState = not_held`.

## Protokoll

- `content.object.state`: wer eine Zurückhaltung gesetzt oder aufgehoben hat, mit den Zuständen davor und danach (der
  Kernel zeichnet jede Zustandszuweisung auf).
- `content.object.hold.refused`: eine Veröffentlichung, die eine Zurückhaltung angehalten hat, mit dem Objekt und der
  Version.

Siehe den [Audit-Katalog](../../bc/6.0/audit.md). Die Website-Seitenleiste fügt einen eigenen Eintrag mit der Begründung
des Prüfers hinzu.

## Einrichtung

Der Upgrade-Schritt `6.0.15-review-hold` erledigt alles, idempotent:

```bash
./console exp:upgrade:scripts --only=6.0.15-review-hold           # what it would change (a dry run)
./console exp:upgrade:scripts --only=6.0.15-review-hold --apply
```

1. legt die Zustandsgruppe `review_hold` und ihre Zustände an (der erste, `not_held`, geht an jedes Objekt);
2. fügt `StateGroup_review_hold = not_held` jeder Richtlinie `content/edit` und `content/publish` hinzu, die die Gruppe
   noch nicht nennt;
3. legt die Rolle **Review hold** an.

Für Redakteure ändert sich nichts, bis ein Objekt zurückgehalten wird. Um es rückgängig zu machen, entfernen Sie die
Limitierung aus den Richtlinien und die Zustandsgruppe unter Setup > States.

## Automatische Zurückhaltung

Inhalt, den Besucher einreichen (Kommentare, Bewertungen, Forenbeiträge), kann automatisch zurückgehalten werden, bis ein
Moderator ihn angesehen hat:

```ini
# content.ini (or settings/siteaccess/<public siteaccess>/content.ini.append.php)
[ReviewHoldSettings]
AutoHold=enabled
AutoHoldClassList[]=comment
AutoHoldClassList[]=review
ExemptPolicyList[]=state/assign
ExemptPolicyList[]=moderation/act
```

Die **erste Veröffentlichung** eines Objekts einer gelisteten Klasse, gemacht von einem Benutzer, der keine der
ausgenommenen Richtlinien hat, wird in `review_hold/held` gesetzt, und **ihre Orte werden verborgen**. Das geschieht in
der Veröffentlichungsoperation selbst (dem Schritt `auto-review-hold`, nachdem das Objekt veröffentlicht ist und vor den
Content-Edit-Handlern, dem Commit, dem View-Cache und dem Suchindex), sodass die Seite es nie zeigt, gleich was es
veröffentlicht hat: ein Webformular, die REST-API, ein Cronjob, eine Freigabe. Spätere Versionen des Objekts werden nicht
automatisch zurückgehalten. `content.object.hold.auto` wird mit der Adresse und dem Browser des Einreichenden
aufgezeichnet. Ausgenommen: ein Benutzer, der den Zustand `held` auf das Objekt setzen darf (`state/assign`, z. B. die
Rolle Review hold), `content/hide` seines Orts, `moderation/act` oder jede andere gelistete `module/function`.

Warum verborgen und nicht unlesbar: Die eigene Sichtbarkeit des Kernels (das Verborgen-Flag, `ShowHiddenNodes`) ist das,
was jede Ansicht, jedes Fetch, jedes Menü, jede Sitemap, jeder Feed und jede Suchmaschine für Besucher bereits beachtet,
ohne Änderung an irgendeiner Rolle. Zurückgehaltene Objekte unlesbar zu machen hieße, die Limitierung der Zurückhaltung
jeder `content/read`-Richtlinie der Besucherrollen hinzuzufügen, und würde auch Seiten verbergen, die ein Redakteur
absichtlich zurückhält, während Besucher weiter die veröffentlichte Version sehen sollen. Eine Zurückhaltung allein
verbirgt deshalb nie; die automatische Zurückhaltung verbirgt, was sie zurückhält, und merkt sich, welche Orte sie
verborgen hat.

**Freigeben** (Dashboard > Recent content, *Release the hold*, oder das Page-Panel der Website-Seitenleiste) setzt
`not_held` und macht genau die Orte wieder sichtbar, die die automatische Zurückhaltung verborgen hat, wenn der
freigebende Benutzer sie verbergen darf (`content/hide`); andernfalls sagt das Ergebnis, dass sie verborgen bleiben. Einen
Ort, den ein Redakteur selbst verborgen hat, macht eine Freigabe nie sichtbar. Die aufbewahrten Daten (wer, wann, warum,
welche Orte) liegen in `ezsite_data` als `review_hold_<object id>`, solange die Zurückhaltung dauert.

### Automatische Zurückhaltung und HideNew von expwt

Die Website-Seitenleiste hat eine eigene Vorab-Moderation von Kommentaren, expwt.ini `[Comments] HideNew`: Ein neuer
Kommentar von jemandem, der ihn nicht verbergen darf, wird verborgen (nicht zurückgehalten), und *Approve* in der
Seitenleiste macht ihn sichtbar. Sind beide eingeschaltet, läuft die automatische Zurückhaltung zuerst; HideNew sieht den
Kommentar schon verborgen und lässt ihn, und *Approve* in der Seitenleiste hebt auch die Zurückhaltung auf. Wir empfehlen
die automatische Zurückhaltung: Sie funktioniert für jede Klasse, für jeden Weg des Veröffentlichens, ohne die
Seitenleiste, sie erscheint unter Dashboard > Recent content mit der Adresse des Einreichenden, und ein zurückgehaltener
Kommentar kann von seinem Autor nicht bearbeitet oder erneut veröffentlicht werden, bis ein Moderator ihn freigibt.
Behalten Sie HideNew nur in Installationen, die die Zurückhaltung zur Prüfung noch nicht nutzen.

## Für Entwickler

```php
expReviewHold::isHeld( $object );          // held now?
expReviewHold::refusesPublish( $object );  // would the current user's publication be refused?
expReviewHold::state( expReviewHold::STATE_HELD );   // the eZContentObjectState, null before the upgrade step
```

Setzen oder heben Sie eine Zurückhaltung mit der eigenen Operation des Kernels auf, damit die Richtlinie und das
Protokoll gelten:

```php
eZOperationHandler::execute( 'content', 'updateobjectstate',
    array( 'object_id' => $objectID, 'state_id_list' => array( $stateID ) ) );
```
