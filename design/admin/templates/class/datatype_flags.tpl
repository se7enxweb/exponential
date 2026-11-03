{* The flags of a datatype as tags (class/datatype/list and class/datatype/view): what eZDataType says of the
   datatype (isTranslatable(), isIndexable(), isInformationCollector(), sortKeyType(), isRelationType(),
   isSimpleStringInsertionSupported(), is*FileInsertionSupported(), serialize_supported).
   Parameter: flags (expDataTypeInventory::flagsOf()). *}
<span class="a4-class-datatypes-flags">
{if $flags.translatable}<span class="a4-tag" title="{'Each translation has its own value.'|i18n( 'design/admin/class/datatype' )|wash}">{'Translatable'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.indexable}<span class="a4-tag" title="{'The search indexes its values.'|i18n( 'design/admin/class/datatype' )|wash}">{'Searchable'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.information_collector}<span class="a4-tag" title="{'It can collect information from visitors (forms).'|i18n( 'design/admin/class/datatype' )|wash}">{'Collects information'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.sortable|ne( '' )}<span class="a4-tag" title="{'Lists can be sorted by it: sort key %type.'|i18n( 'design/admin/class/datatype',, hash( '%type', $flags.sortable ) )|wash}">{'Sortable'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.relation}<span class="a4-tag is-info" title="{'Its values point to other objects.'|i18n( 'design/admin/class/datatype' )|wash}">{'Relations'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.string_insertion}<span class="a4-tag is-mono" title="{'It can be filled from a plain string (import, REST).'|i18n( 'design/admin/class/datatype' )|wash}">{'From text'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.file_insertion}<span class="a4-tag is-mono" title="{'It can be filled from a file.'|i18n( 'design/admin/class/datatype' )|wash}">{'From file'|i18n( 'design/admin/class/datatype' )}</span>{/if}
{if $flags.serialize}<span class="a4-tag is-mono" title="{'Its class attribute settings go into packages.'|i18n( 'design/admin/class/datatype' )|wash}">{'Packages'|i18n( 'design/admin/class/datatype' )}</span>{/if}
</span>
