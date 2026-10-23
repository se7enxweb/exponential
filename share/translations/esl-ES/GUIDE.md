# Spanish (esl-ES): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Spanish of Spain, neutral and technical, as in current Spanish software.

`esl-ES` is read far beyond Spain. Every Spanish-speaking country without a catalogue of its own falls back to it
(`settings/i18n.ini`, `FallbackLanguages[]`): `esl-AR`, `esl-BO`, `esl-CL`, `esl-CO`, `esl-CR`, `esl-CU`,
`esl-DO`, `esl-EC`, `esl-GQ`, `esl-GT`, `esl-HN`, `esl-NI`, `esl-PA`, `esl-PE`, `esl-PR`, `esl-PY`, `esl-SV`,
`esl-US`, `esl-UY`, `esl-VE`. Mexico has its own catalogue (`esl-MX`, see its guide), but takes `esl-ES` for every
extension that has no Mexican one. Write Spanish that a reader in Buenos Aires or Bogotá understands without effort:
no colloquial words of Spain ("vale", "guay", "coger"), no vosotros.

## 2. Form of address

The user is addressed informally with **tú**, in the singular, everywhere.

- **Buttons, menu items and links**: the infinitive. "Eliminar", "Editar", "Cancelar", "Guardar", "Eliminar la
  selección", "Enviar para su publicación", "Vaciar papelera". Not the imperative.
- **Titles, column headings and tabs**: nouns. "Traducciones", "Ubicación principal", "Clases de contenido".
- **Instructions and requests**: the tú imperative: "Comprueba la conexión y vuelve a intentarlo.", "Elige dónde
  quieres empezar.", "Introduce una dirección de correo electrónico válida." "Por favor," may stand before it where
  the source has "Please", but is not required.
- **Confirmations**: "¿Estás seguro de que quieres ...?": "¿Estás seguro de que quieres eliminar este idioma?".
- **Statements about the user**: tú, tu, tus: "Se está cambiando la dirección de correo electrónico de tu cuenta",
  "Comprueba que tienes la política setup/managecache." Where the system is the subject, an impersonal form with se
  is fine: "No se puede eliminar la última clase ...".
- Not allowed: usted and its verb forms (introduzca, seleccione, haga clic), vosotros, the imperative on buttons.

## 3. Script and typography

- **Questions and exclamations** open with ¿ and ¡: "¿Estás seguro de que quieres ...?". A question that starts
  inside a sentence opens there.
- **No space** before ":", ";", "?" and "!".
- **Quotation marks**: straight double quotes "..." as in the source; Spanish angle quotes « » are also accepted in
  running text. Keep one kind within a message.
- **Accents** on capital letters too: "Índice", "Él". Tú (pronoun) and tu (possessive), sí (yes) and si (if) are
  different words.
- **Tenses**: the pretérito perfecto for what has just happened, as usual in Spain: "No se ha podido abrir el
  flujo.", "La petición no ha llegado al servidor."
- **Numbers**: digits 0-9, decimals with a comma in prose ("1,5 MB").
- **Capitals**: only the first word of a button, a title or a heading starts with a capital: "Eliminar la
  selección", "Clases de contenido". No title case.

## 4. Glossary

Binding for the kernel and every extension. Where the Mexican catalogue uses another word, its guide says so.

| English | Spanish | Note |
|---|---|---|
| content | contenido | |
| content object | objeto de contenido | |
| object | objeto | |
| node | nudo | see section 5 |
| location | ubicación | the place of an object in the tree |
| main location | ubicación principal | |
| site | sitio | |
| class | clase | the content class; a PHP class is una clase PHP |
| content class | clase de contenido | |
| class group | grupo de clases | |
| attribute | atributo | |
| datatype | tipo de datos | |
| identifier | identificador | |
| remote ID | ID remoto | |
| version | versión | |
| draft | borrador | |
| publish | publicar | |
| published | publicado | |
| send for publishing | enviar para su publicación | |
| store draft | almacenar borrador | |
| discard | descartar | discard draft: descartar borrador |
| archived | archivado | an archived version |
| pending | pendiente | |
| rejected | rechazado | |
| siteaccess | acceso al sitio | plural: accesos al sitio |
| section | sección | always the Exponential section |
| role | rol | |
| policy | política | |
| limitation | limitación | |
| module | módulo | |
| function | función | |
| permission | permiso | |
| user | usuario | |
| user group | grupo de usuarios | |
| user account | cuenta de usuario | |
| log in | conectarse | the button: Conectar; iniciar sesión is also correct |
| username | nombre de usuario | |
| log out | desconectarse | the button: Desconectar |
| password | contraseña | |
| e-mail | correo electrónico | |
| cache | caché | feminine: la caché; clear cache: limpiar la caché |
| template | plantilla | |
| override | sobreescritura | the verb: sobreescribir |
| fallback | reserva | fallback language: idioma de reserva |
| workflow | flujo de trabajo | |
| trigger | disparador | |
| event | evento | |
| trash | papelera | empty trash: vaciar papelera |
| bookmarks | favoritos | |
| settings | configuración | the plural configuraciones only in the old menu titles |
| setup | configuración | the Setup tab; the setup wizard: instalación |
| dashboard | escritorio | |
| layout | disposición | Exponential Layouts; layouts: disposiciones |
| block | bloque | a block of a layout |
| zone | zona | |
| design | diseño | |
| extension | extensión | |
| package | paquete | |
| kernel | núcleo | |
| search | buscar | the noun: búsqueda |
| search index | índice de búsqueda | indexing: indexación |
| search engine | motor de búsqueda | |
| log | registro | a log file |
| queue | cola | queued: en cola |
| job | tarea | |
| cronjob | tarea programada | |
| file | archivo | not fichero |
| folder | carpeta | |
| image | imagen | |
| thumbnail | miniatura | |
| media | multimedia | media library: biblioteca multimedia |
| preview | vista previa | |
| upload | subir | uploads: subidas |
| download | descargar | the noun: descarga |
| export | exportar | |
| import | importar | |
| remove | eliminar | quitar where the item only leaves a list or place and remains |
| delete | borrar | destroy |
| edit | editar | |
| editor | editor | the person or the software |
| save | guardar | |
| store | almacenar | the verb "Store" of the forms |
| Store / shop | tienda | |
| cancel | cancelar | |
| OK | de acuerdo | see section 5 |
| back | atrás | |
| next / previous | siguiente / anterior | |
| enable / disable | activar / desactivar | |
| enabled / disabled | activado / desactivado | |
| active / inactive | activo / inactivo | |
| hidden / visible | oculto / visible | hide: ocultar |
| apply | aplicar | |
| reset | reiniciar | |
| clear | limpiar | to empty a cache, a list or a field |
| archive | archivo | an archived version is archivada / archivado |
| order (sort) | orden | sort by: ordenado por |
| order (shop) | pedido | orders: pedidos |
| ascending / descending | ascendente / descendente | |
| basket | cesta | |
| wish list | lista de deseos | |
| product | producto | |
| customer | cliente | |
| discount | descuento | |
| VAT | IVA | |
| tag (keyword) | etiqueta | eztags |
| tag (HTML/XML) | etiqueta | etiqueta HTML |
| translation | traducción | |
| language | idioma | main language: idioma principal |
| locale | configuración regional | `Locale` as a setting name stays |
| state | estado | the object state; object states: estados de objetos |
| status | estado | |
| view (button) | ver | |
| view (noun) | vista | a module view |
| format | formato | |
| select | seleccionar | invert selection: invertir la selección |
| browse | navegar | |
| copy | copiar | |
| move | mover | |
| add | añadir | |
| create | crear | created: creado |
| new | nuevo | |
| restore | restaurar | |
| install / uninstall | instalar / desinstalar | |
| sub items | subelementos | |
| children / parent | hijos / padre | |
| subtree | subárbol | |
| related objects | objetos relacionados | relations: relaciones |
| URL alias | alias de URL | |
| content structure | contenidos | the tab |
| owner | propietario | |
| creator / author | creado por / autor | |
| modified | modificado | |
| priority | prioridad | |
| session | sesión | |
| debug | depuración | |
| default | predeterminado | |
| none | ninguno / ninguna | agrees with the noun |
| yes / no | sí / no | |
| help | ayuda | |

## 5. Decisions after the translation

- **Form of address**: tú, as the existing catalogue has always used it ("¿Estás seguro de que quieres ...?" in
  every confirmation). An earlier instruction that asked for usted was wrong. A few messages of one front-end theme
  still say usted; they follow tú when they are next revised.
- **node** is nudo. The catalogue has used it since its first release, in about four hundred messages; nodo appears
  in a few newer extension messages and is brought in line with nudo.
- **OK** is "De acuerdo", as on every OK button of the catalogue. (The Mexican catalogue uses "Aceptar".)
- **siteaccess** is translated (acceso al sitio). Where the word is part of code, a path or a setting
  (`settings/siteaccess`, `[SiteAccessSettings]`), it stays as it is.
- **remove** and **delete** are kept apart: eliminar takes something out (the button "Remove" is Eliminar), borrar
  destroys (the button "Delete" is Borrar).
