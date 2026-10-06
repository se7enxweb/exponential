{* Shown by class/copy/<id> when it is opened by a link instead of a form: a copy is made only by a POST.
   Copy posts ConfirmCopyButton to class/copy/<id>; the form token protects it. Variables: class, copy_redirect. *}
<form method="post" action={concat( 'class/copy/', $class.id )|ezurl}>

<h1>{'Copy the <%class_name> class?'|i18n( 'design/admin/class/copy',, hash( '%class_name', $class.name ) )|wash}</h1>

<p>{'A copy is a new class with the same attributes and settings, in the same class groups, named "Copy of" the class with the identifier copy_of_ and the original identifier. Its objects are not copied; the original class is not changed.'|i18n( 'design/admin/class/copy' )}</p>

<div class="buttonblock">
    <input class="defaultbutton" type="submit" name="ConfirmCopyButton" value="{'Copy'|i18n( 'design/admin/class/copy' )}" />
    <a href={concat( '/class/view/', $class.id )|ezurl}>{'Cancel'|i18n( 'design/admin/class/copy' )}</a>
</div>

</form>
