# Asturian (ast-ES): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Asturian (asturianu) in the standard of the Academia de la Llingua Asturiana (Normes ortográfiques), with the
computing terms of the Asturian software localisations (Softastur: GNOME, Mozilla, LibreOffice), in a neutral, polite
register. No other locale falls back to `ast-ES`.

- Write real Asturian, not Spanish: the masculine singular ends in **-u** (ficheru, usuariu, oxetu, nodu,
  conteníu), the plural in **-os/-es** (ficheros, opciones), the feminine plural in **-es** (les páxines, les
  imáxenes); the participle in **-áu/-ada/-aos** (guardáu, desaniciada, esbillaos) and **-íu/-ida** (conteníu,
  traducida).
- The Asturian letters and spellings: **x** where Spanish has j/g (xubir, xestión, imaxe, mensaxe, páxina,
  executar), **ll-** (llingua, llista, llugar, llocal), **ñ**, the sound written **ḷḷ** is not used in this standard
  text. Articles: el, la, lo (neuter), los, les; contractions: del, al, pel, pela, colos, coles, na, nel, nos, nes.
- Spanish words are never used where Asturian has its own: "desaniciar" (delete), "esbillar" (select), "amestar"
  (add), "guetar" (search), "xubir/baxar" (upload/download), "encaboxar" (cancel), "anubrir" (hide).
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **tu** (informal singular), as the Asturian software localisations do: "¿De xuru que
  quies desaniciar esti oxetu?", "Nun tienes permisu pa ...".
- **Buttons, menu items and links**: the infinitive: Guardar, Encaboxar, Desaniciar, Quitar, Editar, Amestar,
  Crear, Guetar, Esbillar, Unviar, Ver, Espublizar, Xubir, Baxar, Aplicar, Restaurar.
- **Titles, column headings and tabs**: nouns or noun phrases: Axustes, Traducciones, Allugamientu principal, Nome.
- **Instructions**: the imperative of tu: "Esbilla un oxetu y calca \"Aceutar\".".
- **Confirmations and questions**: "¿De xuru que quies ...?". The opening marks ¿ and ¡ are written as in Spanish.
- **What the user can or may do**: "Pues ...", "Nun pues ..."; "You do not have permission" is "Nun tienes permisu
  pa ...". The negation is **nun** (never "no" before a verb).
- **Messages about a result**: the participle or the past: "Guardóse'l borrador.", "El ficheru xubióse
  correutamente.".

## 3. Script and typography

- **Latin** alphabet with á é í ó ú ü ñ, as composed characters.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. The elided article (l', d') is never glued to a placeholder: write
  "el ficheru %name", not "l'%name".
- **Apostrophe**: the straight ' of the elisions (d'esti, l'usuariu, guardóse'l).
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source, plus the opening ¿ ¡.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Asturian | Note |
|---|---|---|
| content | conteníu | content object: oxetu de conteníu |
| object | oxetu | plural: oxetos |
| node | nodu | main node: nodu principal; parent node: nodu pá |
| location | allugamientu | main location: allugamientu principal |
| item (list entry) | elementu | |
| tree | árbol | content structure: estructura de conteníu |
| class | clas | content class: clas de conteníu; class group: grupu de clases |
| attribute | atributu | |
| datatype | tipu de datos | |
| version | versión | version history: historial de versiones |
| draft | borrador | store draft: Guardar el borrador |
| publish | espublizar | published: espublizáu |
| send for publishing | unviar pa espublizar | |
| discard | descartar | |
| siteaccess | accesu al sitiu | siteaccess as a setting name stays in code |
| site, website | sitiu, sitiu web | |
| section | seición | an INI section: seición INI |
| role | rol | plural: roles |
| policy | política | |
| limitation | llimitación | |
| permission | permisu | access denied: accesu denegáu |
| module | módulu | |
| function | función | |
| user / user group | usuariu / grupu d'usuarios | |
| anonymous | anónimu | |
| administrator | alministrador | administration interface: interfaz d'alministración |
| username | nome d'usuariu | |
| password | contraseña | |
| log in / log out | aniciar sesión / zarrar sesión | |
| e-mail | corréu electrónicu | |
| cache | caché | clear cache: Llimpiar la caché |
| template | plantía | override template: plantía de sobrescritura |
| override | sobrescritura | to override: sobrescribir |
| fallback | alternativu | fallback language: llingua alternativa |
| design | diseñu | |
| workflow | fluxu de trabayu | |
| trigger | disparador | |
| event | eventu | |
| trash | papelera | empty trash: Vaciar la papelera |
| bookmark | marcador | |
| settings | axustes | configuration: configuración |
| setup | configuración | the Setup tab: Configuración |
| layout | maquetación | Exponential Layouts stays |
| block / zone | bloque / zona | |
| extension | estensión | |
| package | paquete | |
| search | guetar | search index: índiz de gueta |
| log | rexistru | log file: ficheru de rexistru |
| queue | cola | |
| job / task | xera | never Spanish "tarea"; cronjob: xera programada |
| handler | xestor | |
| feed (RSS) | canal | |
| update (button) | anovar | |
| kernel, core | nucleu | |
| server | sirvidor | |
| database | base de datos | |
| folder / directory | carpeta / direutoriu | |
| file | ficheru | |
| image | imaxe | thumbnail: miniatura |
| preview | previsualización | |
| upload / download | xubir / baxar | |
| export / import | esportar / importar | |
| remove | quitar | take out of a list or place |
| delete | desaniciar | destroy |
| edit | editar | |
| save / store | guardar | the verb "Store" of the forms |
| Store (shop tab) | Tienda | the shop, as "Shop" |
| cancel | encaboxar | |
| OK | Aceutar | |
| apply | aplicar | |
| back | atrás | |
| next / previous | siguiente / anterior | |
| enable / disable | activar / desactivar | |
| active / inactive | activu / inactivu | |
| run | executar | |
| start / stop | aniciar / parar | |
| session | sesión | |
| hidden / visible | anubríu / visible | hide / reveal: anubrir / amosar |
| archive | archivu | |
| order (sort) | orde | sort order: orde d'ordenación |
| order (shop) | pidíu | |
| shop | tienda | |
| basket | cestu | |
| product | productu | |
| customer | veceru | |
| discount | descuentu | |
| VAT | IVE | |
| price / currency | preciu / moneda | |
| tag | etiqueta | |
| translation | traducción | |
| language | llingua | main language: llingua principal |
| locale | locale | as a setting; in prose: configuración llocal |
| state | estáu | the object state |
| status | estáu | |
| view (button) | ver | a module view (noun): vista |
| sort | ordenar | |
| filter | filtru | to filter: filtrar |
| select | esbillar | selected: esbilláu |
| copy / move | copiar / mover | |
| add / create / new | amestar / crear / nuevu | |
| restore | restaurar | |
| install / uninstall | instalar / desinstalar | |
| related objects | oxetos rellacionaos | relation: rellación |
| dashboard | panel | |
| identifier / ID | identificador / ID | |
| priority | prioridá | |
| owner | propietariu | |
| modified / created | modificáu / creáu | |
| date / time | data / hora | |
| error / warning | error / avisu | |
| help | ayuda | |
| notification | avisu | |
| account | cuenta | |
| default | predetermináu | |
| link | enllaz | |
| none / all | nengún / too | |
| yes / no | sí / non | |
| name / title / description | nome / títulu / descripción | |
| value / type / list | valor / tipu / llista | |
| page / path | páxina / camín | |
| required / optional | obligatoriu / opcional | |
| options | opciones | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Tienda, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Guardar.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Aniciar sesión).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
