<?php
/**
 * Module dashboard
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('module', $Module);

$newsletterDraft = nvNewsletter::fetchByOffset( array( 'status' => nvNewsletter::STATUS_DRAFT ), 
                                                array( 'offset'=>0, 'limit'=>5 ) );

$tpl->setVariable('newsletter_draft_array', $newsletterDraft);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/dashboard.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter dashboard' ) ) );
?>
