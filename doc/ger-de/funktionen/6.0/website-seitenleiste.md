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
| `ActiveExtensions[]=ezwt`, `ActiveAccessExtensions[]=ezwt` | `expwt` an derselben Stelle; ezwt wird nicht mehr geladen |

Eine Datei, die er ändert, bleibt als `<Datei>.before-expwt` erhalten. Die eZODF-Schaltflächen und die eigenen
Toolbar-Templates von ezwt haben keine Entsprechung; sie werden als Hinweise aufgeführt, nicht kopiert. Ein zweiter Lauf
ändert nichts. Die Seitenleiste selbst bleibt aus, bis sie eingeschaltet wird (`expwt.ini [ExpwtSettings]
Enabled=enabled`, je Siteaccess), denn expwt wird bis zu seiner Version 1.0 ausgeschaltet ausgeliefert.

## Jede Aktion prüft zwei Policies

Die Policies von expwt (`expwt/use`, `edit`, `create`, `structure` sowie `layouts`, `templates`, `shop`, `admin` für
spätere Bereiche) entscheiden, was die Seitenleiste anbietet. Jede Aktion prüft zusätzlich die Policy des Kernels für
das, was sie ändert (`content/edit` des Objekts, `content/create` unter dem Elternknoten, `content/hide`,
`content/remove` ...), über dieselben Operationen wie die Administration, Workflows eingeschlossen. „Bestätigen Sie Ihre
Identität" fragt bei den Aktionen der Seitenleiste mit denselben Auslösegruppen wie bei den Ansichten der
Administration, für die sie stehen.
