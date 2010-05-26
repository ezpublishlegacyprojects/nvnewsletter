{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* nvNewsletter - confirm remove receiver field *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Confirm receiver field removal'|i18n( 'design/nvnewsletter' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="message-confirmation">

{section show=$delete_result|count|eq(1)}
    <h2>{'Are you sure you want to remove the receiver field?'|i18n( 'design/nvnewsletter' )}</h2>
{section-else}
    <h2>{'Are you sure you want to remove the receiver fields?'|i18n( 'design/nvnewsletter' )}</h2>
{/section}

<p>{'The following receiver fields will be removed'|i18n( 'design/nvnewsletter' )}:</p>

<ul>
{foreach $delete_result as $item}
    <li>{$item.field_name|wash}</li>
{/foreach}
</ul>

<p><b>{'Warning'|i18n( 'design/nvnewsletter' )}:</b></p>
<p>{'Do not proceed unless you are sure.'|i18n( 'design/nvnewsletter' )}</p>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">

<form action={$module.functions.list_receiver_fields.uri|ezurl} method="post" name="ReceiverFieldRemove">
    <input class="button" type="submit" name="ConfirmRemoveReceiverFieldButton" value="{'OK'|i18n( 'design/nvnewsletter' )}" />
    <input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/nvnewsletter' )}" />
</form>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</div>