<div id="leftmenu">
<div id="leftmenu-design">

<div class="objectinfo">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Newsletter'|i18n( 'design/nvnewsletter' )}</h4>

</div></div></div></div></div></div>

<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-br"><div class="box-bl"><div class="box-content">

<p>
<a href={concat("nvnewsletter/preview/",$object.id,"/",$version.version,"/",$language, '/html/1')|ezurl} target="nvnewsletterversionview">{'Show HTML formatted'|i18n('design/nvnewsletter')}</a><br />
<a href={concat("nvnewsletter/preview/",$object.id,"/",$version.version,"/",$language, '/text/1')|ezurl} target="nvnewsletterversionview">{'Show text formatted'|i18n('design/nvnewsletter')}</a>
</p>

<form method="post" action={concat( 'content/versionview/', $object.id, '/', $version.version, '/', $language, '/', $from_language )|ezurl}>
<div class="block">
    <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/content/view/versionview' )}" title="{'Edit the draft that is being displayed.'|i18n( 'design/admin/content/view/versionview' )}" />
</div>
</form>

</div></div></div></div></div></div>

</div>

<div class="objectinfo">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Send preview newsletter'|i18n( 'design/nvnewsletter' )}</h4>

</div></div></div></div></div></div>

<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-br"><div class="box-bl"><div class="box-content">

    <form name="send_preview" method="post" action={concat('/nvnewsletter/send_preview/', $object.id, '/', $version.version, '/', $language, '/1' )|ezurl}>

    <div class="block float-break">
        <label for="PreviewEmail">{'Email address'|i18n('design/nvnewsletter')}:</label>
        <input type="text" name="PreviewEmail" id="PreviewEmail" />
    </div>
    
    <div class="block float-break">
        <label>{'Format'|i18n('design/nvnewsletter')}:</label>
        <label class="checkbox"><input type="radio" name="PreviewFormat" checked="checked" value="1" /> HTML</label>
        <label class="checkbox"><input type="radio" name="PreviewFormat" value="0" /> Text</label>
    </div>
    
    <div class="block float-break">
        <input type="hidden" value="{concat('content/versionview/', $object.id, '/', $version.version, '/', $language)}" name="RedirectURIAfterPreview" />
        <input class="button" type="submit" value="{'Send preview'|i18n('design/nvnewsletter')}" title="{'Send preview newsletter'|i18n('design/nvnewsletter')}" />
    </div>
    
    </form>

</div></div></div></div></div></div>

</div>

</div>

</div>

<div id="maincontent"><div id="fix">
<div id="maincontent-design">
<!-- Maincontent START -->

{* Content window. *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title"><a href={concat( '/class/view/', $object.contentclass_id )|ezurl} onclick="ezpopmenu_showTopLevel( event, 'ClassMenu', ez_createAArray( new Array( '%classID%', {$object.contentclass_id}) ), '{$object.content_class.name|wash(javascript)}', -1 ); return false;">{$object.content_class.identifier|class_icon( normal, $node.class_name )}</a>&nbsp;{$object.name|wash}&nbsp;[{$object.content_class.name|wash}]</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr">

<div class="context-information">
<p class="modified">&nbsp;</p>
<p class="translation">
{$object_languagecode|locale().intl_language_name} <img src="{$object_languagecode|flag_icon}" alt="{$object_languagecode}" style="vertical-align: middle;" />
</p>
<p class="full-screen">
<a href={concat("nvnewsletter/preview/",$object.id,"/",$version.version,"/",$language,'/html/1')|ezurl} target="_blank"><img src={"images/window_fullscreen.png"|ezdesign} /></a>
</p>
<div class="break"></div>
</div>

{* Content preview in content window. *}
<div class="mainobject-window">

    <iframe src={concat("nvnewsletter/preview/",$object.id,'/',$version.version,'/',$language,'/',ezhttp('PreviewFormat', 'post'), '/1')|ezurl} width="100%" height="800" name="nvnewsletterversionview">
    Your browser does not support iframes. Please see this <a href={concat("content/versionview/",$object.id,"/",$version.version,"/",$language, "/site_access/", $siteaccess)|ezurl}>link</a> instead.
</iframe>

</div>


</div></div>

{* Buttonbar for content window. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<form method="post" action={concat( 'content/versionview/', $object.id, '/', $version.version, '/', $language, '/', $from_language )|ezurl}>
{* version.status 0 is draft
   object.status 2 is archived *}
{section show=or( and( eq( $version.status, 0 ), $is_creator, $object.can_edit ),
                  and( eq( $object.status, 2 ), $object.can_edit ) )}
<input class="button" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/content/view/versionview' )}" title="{'Edit the draft that is being displayed.'|i18n( 'design/admin/content/view/versionview' )}" />
<!--input class="button" type="submit" name="PreviewPublishButton" value="{'Publish'|i18n( 'design/admin/content/view/versionview' )}" title="{'Publish the draft that is being displayed.'|i18n( 'design/admin/content/view/versionview' )}" /-->
{section-else}
<input class="button-disabled" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/content/view/versionview' )}" disabled="disabled" title="{'This version is not a draft and therefore cannot be edited.'|i18n( 'design/admin/content/view/versionview' )}" />
<!--input class="button-disabled" type="submit" name="PreviewPublishButton" value="{'Publish'|i18n( 'design/admin/content/view/versionview' )}" disabled="disabled" title="{'Publish the draft that is being displayed.'|i18n( 'design/admin/content/view/versionview' )}" /-->
{/section}
</form>
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>



<!-- Maincontent END -->
</div>
<div class="break"></div>
</div></div>
