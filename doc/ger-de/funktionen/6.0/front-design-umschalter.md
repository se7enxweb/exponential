# Designs eines öffentlichen Siteaccess umschalten

Ein öffentlicher Siteaccess kann seinen Besuchern mehr als ein Design anbieten und sie mit einem Klick zwischen ihnen
wechseln lassen, zum Beispiel zwischen einem klassischen und einem modernen Design derselben Inhalte. Jeder Besucher
kann umschalten: Es braucht kein Konto, keine Sitzung und keine Richtlinie. Die Wahl wird in einem Cookie gehalten und
gilt für jede Seite des Siteaccess.

Das ist etwas anderes als der [UI-Umschalter](ui-umschalter.md), mit dem angemeldete Benutzer der Administration ihre
Oberfläche wählen. Auf einem Administrations-Siteaccess schaltet der Design-Umschalter nie.

## Einschalten

Der Umschalter ist aus, bis der Siteaccess Designs nennt, zu denen umgeschaltet werden kann. In
`settings/siteaccess/<siteaccess>/site.ini.append.php` des Siteaccess:

```ini
[DesignSettings]
SiteDesign=doc
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base
SwitchableDesigns[]
SwitchableDesigns[]=doc2025
SwitchableDesignLabel[doc]=Modern
SwitchableDesignLabel[doc2025]=Classic
```

| Einstellung | Bedeutung |
|---|---|
| `SwitchableDesigns[]` | Die Designs, zu denen ein Besucher wechseln darf, neben dem eigenen `SiteDesign` des Siteaccess, das immer der Standard ist. Leer: Der Umschalter ist aus |
| `SwitchableDesignLabel[<design>]` | Der gezeigte Name eines Designs, übersetzt im Kontext `kernel/uiswitch`; ohne Eintrag der Verzeichnisname des Designs |
| `SwitchableDesignChain[<design>]` | Die Rückfallkette eines Designs, Designs durch `;` getrennt, als `AdditionalSiteDesignList[]`, solange dieses Design gewählt ist. Ohne Eintrag bleibt die Liste des Siteaccess |
| `DesignSwitchCookie` | Das Cookie, das die Wahl hält, standardmäßig `exp_design` |

Ein Design wird nur angeboten, wenn sein Verzeichnis existiert, in `design/` oder im Verzeichnis `design/` einer
aktiven Design-Extension. Ein Siteaccess, dessen Design-Liste ein Administrations-Design (`admin` …) enthält, schaltet
nie.

## Wie ein Besucher umschaltet

Jede Adresse des Siteaccess mit dem View-Parameter `/(design)/<name>` zeigt die Seite in diesem Design und speichert
die Wahl im Cookie, zum Beispiel `/manual/installing/(design)/doc2025`. Wer das eigene Design des Siteaccess wählt,
löscht das Cookie wieder. Ein Name, der nicht angeboten wird, oder ein Design, das nicht installiert ist, wird
ignoriert; ein Cookie mit einem Design, das nicht mehr angeboten wird, wird gelöscht.

Der Umschalter wirkt ganz am Anfang der Anfrage, bevor Design, Übersetzungen und Templates eingerichtet werden, sodass
jedes Template der Seite aus dem gewählten Design kommt.

## In einem Template

`fetch( 'uiswitch', 'front_designs' )` liefert den Umschalter; leer, wo der Siteaccess nicht umschaltet.

```smarty
{def $switch = fetch( 'uiswitch', 'front_designs' )}
{if $switch}
<nav class="design-switch" aria-label="{'Design'|i18n( 'kernel/uiswitch' )}">
    {foreach $switch.designs as $design}
        {if $design.current}
            <span aria-current="true">{$design.label|wash}</span>
        {else}
            <a href={concat( $node.url_alias, '/(', $switch.parameter, ')/', $design.name )|ezurl}>{$design.label|wash}</a>
        {/if}
    {/foreach}
</nav>
{/if}
{undef $switch}
```

| Schlüssel | Wert |
|---|---|
| `current` | Das Design dieser Seite |
| `default` | Das eigene `SiteDesign` des Siteaccess |
| `parameter` | Der Name des View-Parameters, `design` |
| `designs` | Jedes Design mit `name`, `label`, `current` und `default` |

## Caches

- Ein gewähltes Design fügt `fd-<design>` dem Cache-Varianten-Schlüssel der Anfrage hinzu, demselben Schlüssel, den
  der UI-Umschalter verwendet. Er trennt den Design-Basis-Cache, die kompilierten Templates, `{cache-block}` und den
  Inhalts-View-Cache, sodass eine Seite in einem Design nie im anderen ausgeliefert wird.
- Jede Antwort eines umschaltenden Siteaccess sendet `Vary: Cookie`, damit ein Cache vor der Site die Designs
  auseinanderhält.
- Eine Seite in einem gewählten Design (nicht dem Standard) wird als `Cache-Control: private` gesendet. Seiten im
  Standard-Design behalten, was `[HTTPHeaderSettings]` ihnen gibt.
- Ein dauerhafter Velocity-Worker setzt die Design-Einstellungen zu Beginn der nächsten Anfrage zurück.

## Verwandt

- [UI-Umschalter](ui-umschalter.md): die Oberflächen-Wahl angemeldeter Benutzer der Administration.
- [Exponential 6.0 konfigurieren](../../CONFIGURING.md): die Ebenen der Einstellungen und die Siteaccesses.
