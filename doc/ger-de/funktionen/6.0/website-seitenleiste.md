# Die Website-Seitenleiste (expwt) anstelle der Website-Toolbar (ezwt)

expwt ist die Verwaltungs-Seitenleiste von Exponential auf der Website: ein schmaler Reiter am Rand der Seiten der
Website, der sich zu einem Bereich öffnet, um die Seite zu bearbeiten, Inhalte anzulegen, sie im Baum zu verschieben
und das eigene Konto zu verwalten, ohne in die Administration zu wechseln. Sie ersetzt ezwt, die Website-Toolbar. expwt
ist eine eigene Erweiterung (Paket `se7enxweb/expwt`); diese Seite beschreibt, was der Kernel dafür bereitstellt und wie
eine Installation von ezwt zu expwt wechselt. Die Anleitung der Erweiterung selbst (Einstellungen, Policies, Bereiche,
Datenschichten) ist `extension/expwt/doc/ger-de/expwt.md`.

## Was der Kernel bereitstellt

- **Den Pagelayout-Hook** ([Pagelayout-Hooks](seitenlayout-hooks.md)): expwt meldet `BodyEnd[]=expwt/hook.tpl` an und
  erreicht so jedes Design, dessen Pagelayout die Hook-Zeile hat; ein Pagelayout ohne sie bekommt die Seitenleiste vom
  Antwortfilter von expwt.
- **Mutationen von Schema-Erweiterungen** (`expGraphQLMutationExtension`, [die GraphQL-API](graphql.md#schema-erweiterungen)):
  die GraphQL-Datenschicht von expwt ergänzt `expwtWrite` neben den Mutationen des Kernels.
- **Den Wechsel von ezwt**: den Konsolenbefehl `exp:wt:migrate-from-ezwt` und den Upgrade-Schritt `6.0.15-expwt-enable`.
- **Die Zeitsteuerung** (`expContentSchedule`): veröffentlichen am und zurückziehen am über den Wait-until-date-Workflow
  und den Hide-Cronjob, siehe [Zeitsteuerung](#zeitsteuerung-ein-verfahren-für-die-administration-und-die-seitenleiste).

## Von ezwt zu expwt wechseln

Mit expwt im Erweiterungsverzeichnis:

```bash
./console exp:wt:migrate-from-ezwt            # was sich ändern würde; nichts wird geschrieben
./console exp:wt:migrate-from-ezwt --apply    # die Änderungen machen
./console exp:upgrade --apply                 # oder im Rahmen eines Upgrades (Schritt 6.0.15-expwt-enable)
```

Was er tut:

| ezwt | wird |
|---|---|
| eine Policy `websitetoolbar/use` oder `websitetoolbar/*` | `expwt/use`, `expwt/edit`, `expwt/create` und `expwt/structure` in derselben Rolle, mit denselben Einschränkungen |
| die Rolle Editor | `expwt/use`, `expwt/edit`, `expwt/create`, `expwt/structure`, wo sie ihr fehlen |
| die Rolle Administrator | `expwt/*`, außer sie hat `*/*` |
| `websitetoolbar.ini [WebsiteToolbarSettings] HiddenContentClasses[]` (settings/override und jeder Siteaccess) | `expwt.ini [CreateSettings] HiddenClassList[]` daneben |
| `ActiveExtensions[]=ezwt`, `ActiveAccessExtensions[]=ezwt` | `expwt` an derselben Stelle; ezwt wird nicht mehr geladen. Aktiviert die Datei expwt schon (vor oder nach ezwt), wird die ezwt-Zeile nur entfernt: „remove ezwt (expwt already active)“ |

Eine Rolle mit mehreren Toolbar-Policies bekommt eine expwt-Policy je Funktion, nicht eine je Toolbar-Policy:
Einschränkungen, die sich nur in den Werten einer einzigen Einschränkung unterscheiden, werden zusammengeführt (zwei
`Class`-Listen werden eine mit den Klassen beider), denn das erlaubt genau, was die Toolbar-Policies zusammen erlaubten;
Einschränkungen, die sich so nicht zusammenführen lassen (zwei Klassen in zwei verschiedenen Sektionen oder eine Policy
mit `Node` und `Subtree`), ergeben je eine expwt-Policy. Eine Policy, die die Rolle schon hat, gleich oder weiter, wird
nicht noch einmal vergeben. Jede Zeile nennt die Werte der Einschränkungen und die Quelle:

```
would remove ezwt (expwt already active) from ./settings/override/site.ini.append.php
would grant expwt/use to Editor (3) limited to Class 1, 2, 5, 11, ... (from 2 websitetoolbar/use policies)
```

Eine Datei, die er ändert, bleibt als `<Datei>.before-expwt` erhalten. Die eZODF-Schaltflächen und die eigenen
Toolbar-Templates von ezwt haben keine Entsprechung; sie werden als Hinweise aufgeführt, nicht kopiert. Ein zweiter Lauf
ändert nichts. Die Seitenleiste selbst bleibt aus, bis sie eingeschaltet wird (`expwt.ini [ExpwtSettings]
Enabled=enabled`, je Siteaccess), denn expwt wird bis zu seiner Version 1.0 ausgeschaltet ausgeliefert.

## Zeitsteuerung: ein Verfahren für die Administration und die Seitenleiste

Später veröffentlichen und eine Seite zu einem Zeitpunkt zurückziehen nutzen das eigene Verfahren des Kernels, dasselbe
wie die Administration (`expContentSchedule`, Entscheidung F21 des expwt-Plans). Nichts davon gehört der Seitenleiste:

| | wie es arbeitet | was die Klasse braucht |
|---|---|---|
| veröffentlichen am | das Datumsfeld des Entwurfs trägt den Zeitpunkt; das Veröffentlichen startet den Workflow an `content/publish` (before), dessen Ereignis „Wait until date“ (`ezwaituntildate`) den Entwurf bis dahin zurückhält; der Workflow-Cronjob (`workflow.php`, Teil frequent) veröffentlicht ihn dann. Jedes andere Ereignis dieses Workflows, etwa eine Freigabe, gilt wie bei jedem Veröffentlichen | ein Datumsfeld (`ezdatetime`, oder `ezdate` für einen Tag), das das Ereignis aufführt, und das Ereignis direkt im Workflow am Trigger (eines in einem Multiplexer zählt nicht: es läuft vielleicht nicht für jedes Objekt, und ein Datum, auf das nicht gewartet wird, würde sofort veröffentlichen) |
| zurückziehen am | das Datumsfeld aus `content.ini [HideSettings] HideDateAttributeList[<Klasse>]`; der Hide-Cronjob (`hide.php`) verbirgt die Seite, sobald der Zeitpunkt da ist | dieses Feld (`ezdatetime` oder `ezdate`) und die Seite unterhalb eines Knotens aus `[HideSettings] RootNodeList[]` |

So wird es für eine Klasse eingeschaltet, zum Beispiel `article`:

1. Der Klasse ein Datumsfeld geben, z. B. `publish_date` und `hide_date` (Datum und Uhrzeit).
2. Einrichtung, Workflows: ein Workflow mit dem Ereignis „Wait until date“, das `article/publish_date` aufführt;
   Einrichtung, Trigger: ihn an `content` `publish` `before` anschließen. Ein Freigabe-Ereignis darf im selben Workflow
   stehen.
3. `settings/override/content.ini.append.php`:
   ```ini
   [HideSettings]
   RootNodeList[]=2
   HideDateAttributeList[article]=hide_date
   ```
4. Die Cronjobs: `workflow.php` (Teil frequent) und `hide.php` (Standardteil) laufen in einer üblichen Crontab bereits.

Die Website-Seitenleiste schreibt genau diese Felder: „Veröffentlichen am“ und „Zurückziehen am“ bietet sie für eine
Klasse an, die sie hat; bei einer Klasse ohne sie zeigt sie statt des Feldes den Grund. In der Administration werden die
Felder wie jedes andere bearbeitet.

### Die Zeitsteuerung früherer expwt-Versionen

Bisher führte expwt einen eigenen Zeitplan (Site-Data-Zeilen `expwt_publish_<Objekt>_<Version>` und
`expwt_unpublish_<Knoten>`) und einen eigenen Cronjob `expwt_scheduled`. `exp:wt:migrate-from-ezwt` (und der
Upgrade-Schritt `6.0.15-expwt-enable`) überführt jeden Eintrag in das Verfahren des Kernels: Die Zeitpunkte kommen in die
Datumsfelder des Entwurfs, und der Entwurf wird als der Benutzer veröffentlicht, der ihn eingeplant hat, sodass das
Wait-until-date-Ereignis ihn zurückhält; ein Zurückziehen-Datum kommt in das Feld der veröffentlichten Version. Der
Eintrag wird entfernt, sobald er überführt ist. Ein Eintrag, dessen Entwurf inzwischen veröffentlicht oder gelöscht
wurde, wird entfernt; einer, dessen Klasse das Datumsfeld fehlt, bleibt, wird als Hinweis aufgeführt und bei einem
späteren Lauf überführt, sobald die Klasse es hat.

## Jede Aktion prüft zwei Policies

Die Policies von expwt (`expwt/use`, `edit`, `create`, `structure` sowie `layouts`, `templates`, `shop`, `admin` für
spätere Bereiche) entscheiden, was die Seitenleiste anbietet. Jede Aktion prüft zusätzlich die Policy des Kernels für
das, was sie ändert (`content/edit` des Objekts, `content/create` unter dem Elternknoten, `content/hide`,
`content/remove` ...), über dieselben Operationen wie die Administration, Workflows eingeschlossen. „Bestätigen Sie Ihre
Identität" fragt bei den Aktionen der Seitenleiste mit denselben Auslösegruppen wie bei den Ansichten der
Administration, für die sie stehen.
