{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* nvNewsletter - edit receiver field *}
<form method="post" action={concat('/nvnewsletter/edit_receiver_field/', $field.id, '/')|ezurl}>

{if $warning|count}
    <div class="message-warning">
    <h2>{'The validation of your entries failed.'|i18n('design/nvnewsletter' )}</h2>
    <ul>
    {foreach $warning as $warningmessage}
        <li>{$warningmessage|wash}</li>
    {/foreach}
    </ul>
    </div>
{/if}

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Edit receiver field'|i18n( 'design/nvnewsletter' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<input type="hidden" name="ReceiverFieldID" value ="{$field.id}"/>

<div class="context-attributes">

{* Name. *}
<div class="block">
<label for="name">{'Name'|i18n( 'design/nvnewsletter' )}:</label>
<input id="name" class="box" id="fieldName" type="text" name="ReceiverFieldName" value="{$field.field_name|wash}" />
</div>

{* Type. *}
<div class="block">
<label for="type">{'Type'|i18n( 'design/nvnewsletter' )}:</label>
<select id="type" name="ReceiverFieldType">
    <option value="TEXT" {if $field.field_type|eq('TEXT')} selected{/if}>TEXT</option>
    <option value="INT" {if $field.field_type|eq('INT')} selected{/if}>INT</option>
</select>
</div>

{* Required. *}
<div class="block">
<label for="required">{'Required'|i18n( 'design/nvnewsletter' )}:</label>
<input id="required" type="checkbox" name="ReceiverFieldRequired" value="1" {if $field.required} checked="checked"{/if} />
</div>

{* Meta. *}
<div class="block">
<label for="metadata">{'Metadata'|i18n( 'design/nvnewsletter' )}:</label>
<input id="metadata" class="box" id="fieldName" type="text" name="ReceiverFieldMeta" value="{$field.meta|wash}" />
</div>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="StoreButton" value="{'OK'|i18n( 'design/nvnewsletter' )}" />
<input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/nvnewsletter' )}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>
</form>


{literal}
<script language="JavaScript" type="text/javascript">
<!--
    window.onload=function() {
        document.getElementById('fieldName').select();
        document.getElementById('fieldName').focus();
    }
-->
</script>
{/literal}
