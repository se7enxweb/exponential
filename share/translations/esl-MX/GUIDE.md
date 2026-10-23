# Spanish, Mexico (esl-MX): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Spanish as written in Mexico, neutral and technical, as in current Mexican software. It is the one Latin American
catalogue of Exponential.

**Its relation to `esl-ES`.** `esl-MX` is a complete catalogue of its own for the kernel and for 33 extensions, not
a list of differences. `settings/i18n.ini` gives it `FallbackLanguages[esl-MX]=esl-ES`, and the fallback is looked
up for each translation root on its own: an extension without an `esl-MX` catalogue is shown in the Spanish of
Spain. A term should therefore differ from `esl-ES` only where Mexican usage really differs (section 4 marks those
rows); everything else is the same word, so that a page that mixes both catalogues still reads as one.

**Who else reads it.** No other locale falls back to `esl-MX` today: the other Latin American locales (`esl-AR`,
`esl-CO`, `esl-PE` and the rest) fall back to `esl-ES` directly. Write Mexican Spanish nonetheless in a form
readers across Latin America understand: no slang and no words that only Mexico uses where a general Latin American
word exists.

## 2. Form of address

The user is addressed informally with **tú**, in the singular, as in `esl-ES`. The catalogue uses tú in about nine
of ten messages that address the user.

- **Buttons, menu items and links**: the infinitive. "Eliminar", "Editar", "Cancelar", "Guardar", "Eliminar la
  selección", "Cargar", "Vaciar papelera". Not the imperative.
- **Titles, column headings and tabs**: nouns. "Tablero", "Traducciones", "Ubicación principal".
- **Instructions and requests**: the tú imperative: "Revisa la conexión y vuelve a intentarlo.", "Escribe un nombre
  válido.", "Elige dónde quieres empezar."
- **Confirmations**: "¿Estás seguro de que quieres ...?": "¿Estás seguro de que quieres eliminar este idioma?".
- **Statements about the user**: tú, tu, tus: "Comprueba que tienes la política setup/managecache.", "la dirección
  de correo electrónico de tu cuenta". Where the system is the subject, an impersonal form with se is fine: "No se
  puede eliminar la última clase ...".
- Older messages still say "Favor de elegir ..." and use impersonal forms ("se desea"). Both are Mexican, but new
  texts use the tú imperative, and an older message that is revised takes it too.
- Not allowed: usted and its verb forms (ingrese, seleccione, haga clic), ustedes for one user, vosotros, the
  imperative on buttons.

## 3. Script and typography

- **Questions and exclamations** open with ¿ and ¡.
- **No space** before ":", ";", "?" and "!".
- **Quotation marks**: straight double quotes "..." as in the source.
- **Accents**: on capital letters too. Sí (yes) has an accent; si (if) has none.
- **Tenses**: the pretérito indefinido for what has just happened, as usual in Mexico: "No se pudo abrir el
  flujo.", "La solicitud no llegó al servidor." Not "no se ha podido".
- **Spelling**: "video" without an accent.
- **Numbers**: digits 0-9, decimals with a point in prose ("1.5 MB"), as in Mexico.
- **Capitals**: only the first word of a button, a title or a heading starts with a capital: "Cuentas de usuario",
  not "Cuentas de Usuario". No title case.

## 4. Glossary

Binding for the kernel and every Mexican extension catalogue. A mark in the column "≠ esl-ES" means that the word
differs from Spain on purpose.

| English | Spanish (Mexico) | ≠ esl-ES | Note |
|---|---|---|---|
| content | contenido | | |
| content object | objeto de contenido | | |
| object | objeto | | |
| node | nudo | | as in esl-ES, see section 5 |
| location | ubicación | | the place of an object in the tree |
| main location | ubicación principal | | |
| site | sitio | | |
| class | clase | | the content class; a PHP class is una clase PHP |
| content class | clase de contenido | | |
| class group | grupo de clases | | |
| attribute | atributo | | |
| datatype | tipo de datos | | |
| identifier | identificador | | |
| remote ID | ID remoto | | |
| version | versión | | |
| draft | borrador | | |
| publish | publicar | | |
| published | publicado | | |
| send for publishing | enviar para su publicación | | |
| store draft | almacenar borrador | | |
| discard | descartar | | |
| archived | archivado | | |
| pending | pendiente | | |
| rejected | rechazado | | |
| siteaccess | acceso al sitio | | plural: accesos al sitio |
| section | sección | | always the Exponential section |
| role | rol | | |
| policy | política | | |
| limitation | limitación | | |
| module | módulo | | |
| function | función | | |
| user | usuario | | |
| user group | grupo de usuarios | | |
| user account | cuenta de usuario | | |
| log in | conectarse | | the button: Conectar |
| username | nombre de usuario | | |
| log out | desconectarse | | the button: Desconectar |
| password | contraseña | | |
| e-mail | correo electrónico | | |
| cache | caché | ≠ | masculine: el caché; clear cache: limpiar el caché |
| template | plantilla | | |
| override | sobreescritura | | the verb: sobreescribir |
| fallback | respaldo | ≠ | fallback language: idioma de respaldo |
| workflow | flujo de trabajo | | |
| trigger | disparador | | |
| trash | papelera | | empty trash: vaciar papelera |
| bookmarks | favoritos | | |
| settings | configuración | | |
| setup | configuración | | the Setup tab; the setup wizard: instalación |
| dashboard | tablero | ≠ | |
| layout | disposición | | Exponential Layouts; layouts: disposiciones |
| block | bloque | | |
| extension | extensión | | |
| package | paquete | | |
| kernel | núcleo | | |
| search | buscar | | the noun: búsqueda |
| search index | índice de búsqueda | | |
| search engine | motor de búsqueda | | |
| log | registro | | a log file |
| queue | cola | | queued: en cola |
| job | tarea | | |
| cronjob | tarea programada | | |
| scan | escaneo | ≠ | the verb: escanear |
| file | archivo | | |
| folder | carpeta | | |
| image | imagen | | |
| thumbnail | miniatura | | |
| media | multimedia | | |
| preview | vista previa | | |
| upload | cargar | ≠ | uploads: cargas |
| download | descargar | | the noun: descarga |
| export | exportar | | |
| import | importar | | |
| remove | eliminar | | quitar where the item only leaves a list or place |
| delete | borrar | | destroy |
| edit | editar | | |
| save | guardar | | |
| store | almacenar | | the verb "Store" of the forms |
| Store / shop | tienda | | webshop: tienda en línea |
| cancel | cancelar | | |
| OK | aceptar | ≠ | |
| back | atrás | | |
| next / previous | siguiente / anterior | | |
| enable / disable | activar / desactivar | | |
| enabled / disabled | activado / desactivado | | |
| hidden / visible | oculto / visible | | hide: ocultar |
| apply | aplicar | | |
| reset | reiniciar | | |
| clear | limpiar | | |
| restore | restablecer | ≠ | |
| archive | archivo | | |
| order (sort) | orden | | sort by: ordenado por |
| order (shop) | pedido | | orders: pedidos |
| basket | cesta | | |
| wish list | lista de deseos | | |
| product | producto | | |
| customer | cliente | | |
| discount | descuento | | |
| VAT | IVA | | |
| tag (keyword) | etiqueta | | eztags |
| tag (HTML/XML) | etiqueta | | etiqueta HTML |
| translation | traducción | | |
| language | idioma | | main language: idioma principal |
| locale | configuración regional | | `Locale` as a setting name stays |
| state | estado | | the object state |
| status | estado | | |
| view (button) | ver | | |
| view (noun) | vista | | a module view |
| select | seleccionar | | |
| browse | navegar | | |
| copy / move | copiar / mover | | |
| add / create / new | añadir / crear / nuevo | | |
| request | solicitud | ≠ | not petición |
| check (verb) | revisar | ≠ | "Revisa ..."; comprobar is also understood |
| enter (text) | escribir | ≠ | "Escribe ..."; not introducir |
| click | pulsar / hacer clic | | "Pulsa el botón ...", "Haz clic en ..."; not "dar clic" |
| mobile phone | celular | ≠ | |
| computer | computadora | ≠ | |
| sub items | subelementos | | |
| subtree | subárbol | | |
| URL alias | alias de URL | | |
| content structure | estructura de contenido | | |
| owner | propietario | | |
| modified / created | modificado / creado | | |
| priority | prioridad | | |
| debug | depuración | | |
| default | predeterminado | | |
| yes / no | sí / no | | |

## 5. Decisions after the translation

- **Form of address**: tú, like `esl-ES`, so that a page that shows Mexican kernel texts and Spanish extension texts
  side by side does not change its form of address.
- **node** is nudo, as in `esl-ES`, so that the two catalogues name the content tree alike.
- **Order** has two meanings that the catalogue had merged: every "Order" was pedido, also as a sort order. Sort
  order is orden; only the shop order is pedido.
- **Thumbnail** is miniatura. Diapositiva (a slide) was a mistranslation.
