# Occitan (oci-FR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Occitan (occitan) in its **Languedocien** (lengadocian) form and the **classical spelling** (grafia classica) of the
Conselh de la Lenga Occitana, with the computing terms of the Occitan software localisations (Mozilla, LibreOffice,
GNOME), in a neutral, polite register. No other locale falls back to `oci-FR`.

- Write real Occitan, not French or Catalan: "fichièr", "senhal", "dossièr", "ligam", "utilizaire", "suprimir",
  "enregistrar", "apondre", "levar".
- Classical spelling: -a for the feminine (la pagina, una lista), -ar/-ir/-re for infinitives, the accents of the
  norm (ò è é á í ó ú, the grave on open vowels: òrdre, dossièr, nuclèu), the digraphs **lh** and **nh** (senhal,
  fuelh, modificacion), **-cion** (traduccion, configuracion).
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **vos** (polite), as the Occitan software localisations do: "Volètz vertadièrament
  suprimir aqueste objècte?", "Avètz pas la permission de ...".
- **Buttons, menu items and links**: the infinitive: Enregistrar, Anullar, Suprimir, Levar, Modificar, Apondre,
  Crear, Recercar, Seleccionar, Mandar, Veire, Publicar, Telecargar, Aplicar, Restablir.
- **Titles, column headings and tabs**: nouns: Paramètres, Traduccions, Emplaçament principal, Nom.
- **Instructions**: the imperative of vos: "Seleccionatz un objècte e clicatz \"D'acòrdi\".".
- **Confirmations and questions**: "Volètz vertadièrament ...?".
- **What the user can or may do**: "Podètz ...", "Podètz pas ..."; the negation is the postverbal **pas** (no
  "ne").
- **Messages about a result**: the participle: "Lo borrolhon es estat enregistrat.", "Fichièr mandat.".

## 3. Script and typography

- **Latin** alphabet with à è é í ò ó ú ç ï ü, as composed characters; the middle dot (·) only where the classical
  norm writes it.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. The elided article (l', d') is never glued to a placeholder: write
  "lo fichièr %name", not "l'%name".
- **Apostrophe**: the straight ' of the elisions (l'utilizaire, d'objèctes).
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source (no space before : ? ! is added).
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Occitan | Note |
|---|---|---|
| content | contengut | content object: objècte de contengut |
| object | objècte | plural: objèctes |
| node | nos | plural: noses; main node: nos principal; parent node: nos parent |
| location | emplaçament | main location: emplaçament principal |
| item (list entry) | element | |
| tree | arborescéncia | content structure: estructura del contengut |
| class | classa | content class: classa de contengut; class group: grop de classas |
| attribute | atribut | |
| datatype | tipe de donadas | |
| version | version | version history: istoric de las versions |
| draft | borrolhon | store draft: Enregistrar lo borrolhon |
| publish | publicar | published: publicat |
| send for publishing | mandar per publicacion | |
| discard | abandonar | |
| siteaccess | accès al site | siteaccess as a setting name stays in code |
| site, website | site, site web | |
| section | seccion | an INI section: seccion INI |
| role | ròtle | |
| policy | politica | |
| limitation | limitacion | |
| permission | permission | access denied: accès refusat |
| module | modul | |
| function | foncion | |
| user / user group | utilizaire / grop d'utilizaires | |
| anonymous | anonim | |
| administrator | administrator | administration interface: interfàcia d'administracion |
| username | nom d'utilizaire | |
| password | senhal | |
| log in / log out | se connectar / se desconnectar | |
| e-mail | corrièl | e-mail address: adreça electronica |
| cache | escondedor | clear cache: Voidar l'escondedor |
| template | modèl | override template: modèl de remplaçament |
| override | remplaçament | to override: remplaçar |
| fallback | de recors | fallback language: lenga de recors |
| design | aparéncia | |
| workflow | flux de trabalh | |
| trigger | desenclavador | |
| event | eveniment | |
| trash | escobilhièr | empty trash: Voidar l'escobilhièr |
| bookmark | marcapagina | |
| settings | paramètres | configuration: configuracion |
| setup | configuracion | the Setup tab: Configuracion |
| layout | disposicion | Exponential Layouts stays |
| block / zone | blòc / zòna | |
| extension | extension | |
| package | paquet | |
| search | recercar | search index: indèx de recèrca |
| log | jornal | log file: fichièr jornal |
| queue | fila d'espèra | |
| job / task | prètzfach | cronjob: prètzfach cron |
| tab | onglet | |
| handler | gestionari | |
| OAuth/API client | client | software; shop customer is also client |
| kernel, core | nuclèu | |
| server | servidor | |
| database | basa de donadas | |
| folder / directory | dossièr / repertòri | |
| file | fichièr | |
| image | imatge | thumbnail: miniatura |
| preview | apercebut | |
| upload / download | mandar / telecargar | |
| export / import | exportar / importar | |
| remove | levar | take out of a list or place |
| delete | suprimir | destroy |
| edit | modificar | |
| editor | editor | person or software |
| save / store | enregistrar | the verb "Store" of the forms |
| Store (shop tab) | Botiga | the shop, as "Shop" |
| cancel | anullar | |
| OK | D'acòrdi | |
| apply | aplicar | |
| back | tornar | |
| next / previous | seguent / precedent | |
| enable / disable | activar / desactivar | |
| active / inactive | actiu / inactiu | |
| run | executar | |
| start / stop | aviar / arrestar | |
| session | session | |
| hidden / visible | amagat / visible | hide / reveal: amagar / mostrar |
| archive | archiu | |
| order (sort) | òrdre | sort order: òrdre de triada |
| order (shop) | comanda | |
| shop | botiga | |
| basket | panièr | |
| product | produch | |
| customer | client | |
| discount | remesa | |
| VAT | TVA | |
| price / currency | prètz / moneda | |
| tag | etiqueta | |
| translation | traduccion | |
| language | lenga | main language: lenga principala |
| locale | locale | as a setting; in prose: paramètres regionals |
| state | estat | the object state |
| status | estatut | |
| view (button) | veire | a module view (noun): vista |
| sort | triar | |
| filter | filtre | to filter: filtrar |
| select | seleccionar | selected: seleccionat |
| copy / move | copiar / desplaçar | |
| add / create / new | apondre / crear / novèl | |
| restore | restablir | |
| install / uninstall | installar / desinstallar | |
| related objects | objèctes ligats | relation: relacion |
| dashboard | tablèu de bòrd | |
| identifier / ID | identificant / ID | |
| priority | prioritat | |
| owner | proprietari | |
| modified / created | modificat / creat | |
| date / time | data / ora | |
| error / warning | error / avertiment | |
| help | ajuda | |
| notification | notificacion | |
| account | compte | |
| default | per defaut | |
| link | ligam | |
| none / all | cap / tot | |
| yes / no | òc / non | |
| name / title / description | nom / títol / descripcion | |
| value / type / list | valor / tipe / lista | |
| page / path | pagina / camin | |
| required / optional | obligatòri / opcional | |
| options | opcions | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Botiga, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Enregistrar.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Se connectar).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
