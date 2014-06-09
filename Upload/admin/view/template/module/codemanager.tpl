<?php echo $header;?>
<div id="content">
<a class="NormalScreen" id="normalscreen" onClick="normalscreen()"><span class="glyphicon glyphicon-fullscreen"></span></a>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if ($error_warning) { ?><div class="alert alert-danger" > <i class="icon-exclamation-sign"></i>&nbsp;<?php echo $error_warning; ?></div><?php } ?>
    <?php if (!empty($this->session->data['success'])) { ?>
        <div class="alert alert-success autoSlideUp"> <i class="fa fa-info"></i>&nbsp;<?php echo $this->session->data['success']; ?> </div>
        <script> $('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
    <?php $this->session->data['success'] = null; } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<span style="vertical-align:middle;font-weight:bold;"><?php echo $heading_title; ?></span></h3>
            <div class="storeSwitcherWidget">
            	<div class="form-group">
                	<?php if ($buttons) { ?>
                	<button type="submit" id="showModal" class="btn btn-info btn-sm save-changes" data-toggle="modal" data-target="#myModal"><i class="fa fa-key"></i>&nbsp;&nbsp;<?php echo $save_changes?></button>
                    <button type="submit" id="showUsers" class="btn btn-default btn-sm"><i class="fa fa-eye"></i>&nbsp;&nbsp;View all users with access</button>
                    <?php } ?>
            	</div>
            </div>
        </div>
        <div class="panel-body" style="padding: 0px;">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
				<?php require_once(DIR_APPLICATION.'view/template/module/'.$moduleNameSmall.'/tab_editor.php'); ?>
            </form>
        </div> 
    </div>
</div>
<script>

function exitOfFullScreen(el) {
	var requestMethod = el.cancelFullScreen||el.webkitCancelFullScreen||el.mozCancelFullScreen||el.exitFullscreen;
	if (requestMethod) { // cancel full screen.
		requestMethod.call(el);
	} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
		var wscript = new ActiveXObject("WScript.Shell");
		if (wscript !== null) {
			wscript.SendKeys("{F11}");
		}
	}
}

function requestFullScreen(el) {
	// Supports most browsers and their versions.
	var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;

	if (requestMethod) { // Native full screen.
		requestMethod.call(el);
	} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
		var wscript = new ActiveXObject("WScript.Shell");
		if (wscript !== null) {
			wscript.SendKeys("{F11}");
		}
	}
	return false
}

function toggleFull() {
	var elem = document.body; // Make the body go full screen.
	var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) ||  (document.mozFullScreen || document.webkitIsFullScreen);

	if (isInFullScreen) {
		$('#iFrame').addClass("FullScreen");
		$('#normalscreen').show();
	} else {
		$('#iFrame').removeClass("FullScreen");
		$('#normalscreen').hide();
	}
	return false;
}

function fullscreen() {
	requestFullScreen(document.body);
}
function normalscreen() {
	exitOfFullScreen(document);
}

document.addEventListener("fullscreenchange", toggleFull, false);
document.addEventListener("webkitfullscreenchange", toggleFull, false);
document.addEventListener("mozfullscreenchange", toggleFull, false);
</script>

<?php echo $footer; ?>