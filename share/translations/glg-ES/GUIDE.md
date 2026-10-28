# Galician (glg-ES): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Galician (galego) in the official norm of the Real Academia Galega (the orthography of 2003), with the computing
terms of the Galician free-software localisations (Proxecto Trasno, Mancomún), in a neutral, polite register. No
other locale falls back to `glg-ES`.

- Use Galician words and forms, never Spanish or Portuguese ones: "ficheiro", "contrasinal", "cartafol", "ligazón",
  "usuario", "eliminar", "gardar", "xestionar", not "archivo", "contraseña", "carpeta", "enlace", "senha". Spanish
  (`esl-ES`) and Portuguese (`por-PT`) may help with the meaning and the terminology; the text itself is translated
  from the English.
- Spelling of the norm: x where Galician writes it (xestión, imaxe, páxina, mensaxe, xerar), -ble and -ción (posible,
  configuración), the contractions (ao, á, do, da, no, na, co, coa, polo, pola, nun, nunha), the article o / a / os
  / as.
- Pronoun position after the verb in main clauses (Pódese cambiar, Engádese), before it after a negation or a
  subordinating word (non se pode, que se pode).
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **vostede** (third person singular), as the Galician localisations do: "Quere eliminar
  este obxecto?", "Non ten permiso para ...".
- **Buttons, menu items and links**: the infinitive: Gardar, Cancelar, Eliminar, Quitar, Editar, Engadir, Crear,
  Buscar, Seleccionar, Enviar, Ver, Publicar, Subir, Descargar, Aplicar, Restaurar.
- **Titles, column headings and tabs**: nouns or noun phrases: Configuración, Traducións, Localización principal, Nome.
- **Instructions**: the imperative of vostede: "Seleccione un obxecto e prema \"Aceptar\".", "Por favor" only where
  the source insists.
- **Confirmations and questions**: "Está seguro de que quere ...?" (no opening ¿ in Galician).
- **What the user can or may do**: "Pode ...", "Non pode ..."; "You do not have permission" is "Non ten permiso
  para ...".
- **Messages about a result**: the past participle: "Borrador gardado.", "O ficheiro subiuse correctamente.".

## 3. Script and typography

- **Latin** alphabet with á é í ó ú, ñ and ü where the norm writes them; no ç, no ll for Galician lh.
- **Capitals**: only the first word of a button, title or sentence and proper names; months and weekdays in lower
  case.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. Product names, code, file names
  and INI settings stay as they are.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source; only the closing question and exclamation marks.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Galician | Note |
|---|---|---|
| content | contido | content object: obxecto de contido |
| object | obxecto | |
| node | nodo | main node: nodo principal; parent node: nodo pai |
| location | localización | main location: localización principal |
| item (list entry) | elemento | |
| tree | árbore | tree menu: menú en árbore; content structure: estrutura de contidos |
| class | clase | content class: clase de contido; class group: grupo de clases |
| attribute | atributo | |
| datatype | tipo de datos | |
| version | versión | version history: historial de versións |
| draft | borrador | store draft: Gardar borrador |
| publish | publicar | published: publicado; publishing: publicación |
| send for publishing | enviar para publicar | |
| discard | descartar | discard draft: Descartar borrador |
| archived / pending / rejected | arquivado / pendente / rexeitado | |
| siteaccess | acceso ao sitio | siteaccess as a setting name stays in code |
| site, website | sitio, sitio web | |
| section | sección | the Exponential section; an INI section: sección INI |
| role | rol | |
| policy | política | |
| limitation | limitación | |
| permission | permiso | access denied: acceso denegado |
| module | módulo | |
| function | función | |
| user / user group | usuario / grupo de usuarios | |
| group | grupo | |
| anonymous | anónimo | anonymous user: usuario anónimo |
| administrator | administrador | administration interface: interface de administración |
| username | nome de usuario | |
| password | contrasinal | |
| log in / log out | acceder / saír | the act: acceso / saída |
| e-mail | correo electrónico | e-mail address: enderezo de correo electrónico |
| cache | caché | clear cache: Baleirar a caché |
| template | modelo | override template: modelo de substitución |
| override | substitución | |
| fallback | alternativo | fallback language: idioma alternativo |
| design | deseño | |
| workflow | fluxo de traballo | |
| trigger | disparador | |
| event | evento | |
| trash | lixo | empty trash: Baleirar o lixo; move to trash: Mover ao lixo |
| bookmark | marcador | |
| settings | axustes | configuration: configuración |
| setup | configuración | the Setup tab |
| layout | deseño de páxina | Exponential Layouts stays |
| block / zone | bloque / zona | |
| extension | extensión | |
| package | paquete | |
| search | busca | to search: buscar; search index: índice de busca |
| log | rexistro | a log file: ficheiro de rexistro; never "log in" |
| queue | cola | job queue: cola de tarefas |
| job / task | tarefa | |
| kernel, core | núcleo | |
| server | servidor | |
| database | base de datos | |
| folder / directory | cartafol / directorio | |
| file | ficheiro | |
| image | imaxe | thumbnail: miniatura |
| preview | vista previa | |
| upload / download | subir / descargar | |
| export / import | exportar / importar | |
| remove | quitar | take out of a list or place |
| delete | eliminar | destroy; deleted: eliminado |
| edit | editar | |
| editor | editor | person or software |
| save / store | gardar | the verb "Store" of the forms |
| Store (shop tab) | Tenda | the shop, as "Shop" |
| cancel | cancelar | |
| OK | Aceptar | |
| apply | aplicar | |
| back | volver | |
| next / previous | seguinte / anterior | |
| enable / disable | activar / desactivar | enabled / disabled: activado / desactivado |
| active / inactive | activo / inactivo | |
| run | executar | |
| start / stop | iniciar / deter | |
| session | sesión | |
| hidden / visible | agochado / visible | hide / reveal: agochar / amosar |
| archive | arquivo | |
| order (sort) | orde | sort order: orde de clasificación |
| order (shop) | pedido | |
| shop | tenda | |
| basket | cesta | |
| product | produto | |
| customer | cliente | |
| discount | desconto | |
| VAT | IVE | |
| price / currency | prezo / moeda | |
| tag | etiqueta | |
| translation | tradución | |
| language | idioma | main language: idioma principal |
| locale | configuración rexional | |
| state | estado | the object state |
| status | situación | |
| view (button) | ver | a module view (noun): vista |
| format | formato | |
| sort | ordenar | |
| filter | filtro | to filter: filtrar |
| select | seleccionar | select all: Seleccionar todo; selected: seleccionado |
| copy / move | copiar / mover | |
| add / create / new | engadir / crear / novo | |
| restore | restaurar | |
| install / uninstall | instalar / desinstalar | |
| related objects | obxectos relacionados | relation: relación |
| dashboard | panel | |
| identifier / ID | identificador / ID | |
| priority | prioridade | |
| owner | propietario | |
| modified / created | modificado / creado | |
| date / time | data / hora | |
| error / warning | erro / aviso | |
| help | axuda | |
| notification | notificación | |
| account | conta | |
| default | predeterminado | |
| link | ligazón | |
| none / all | ningún / todos | |
| yes / no | si / non | |
| name / title / description | nome / título / descrición | |
| value / type / list | valor / tipo / lista | |
| page / path | páxina / ruta | |
| required / optional | obrigatorio / opcional | |
| options | opcións | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Tenda, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  Gardar.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Acceder).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
