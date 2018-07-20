<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"D:\project\php_wyt_tp\php/application/index\view\upload_demo\index.html";i:1528883616;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>文件上传Demo</title>
</head>
<body>
<a href="">重置页面</a>
<!-- 本地单文件上传 Start-->
<div>
	<span>本地单文件上传</span>
	<form action="http://wyttp.a.com/opening/upload.upload" method="post" enctype="multipart/form-data">
		<input type="hidden" name="token" value="FFFX123456"/>
		<input type="file" name="file"/>
		<input type="submit" value="上传"/>
	</form>
</div>
<!-- 本地单文件上传 End-->
	
<!-- 本地多文件上传 Start-->
<div style="margin-top:20px;">
	<span>本地多文件上传</span>
	<form action="http://wyttp.a.com/index/upload_demo/upload" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]"/><br/>
		<input type="file" name="file[]"/><br/>
		<input type="file" name="file[]"/>
		<input type="submit" value="上传"/>
	</form>
</div>
<!-- 本地多文件上传 End-->

<!-- 七牛单文件上传 Start -->
<div style="margin-top:20px;">
	<span>七牛云单文件上传</span>
	<form action="<?php echo url('qupload'); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="token" value="FFFX123456"/>
		<input type="file" name="file"/>
		<input type="submit" value="上传"/>
	</form>
</div>
<!-- 七牛单文件上传 End -->

<!-- 七牛多文件上传 Start -->
<div style="margin-top:20px;">
	<span>七牛云多文件上传</span>
	<form action="http://wyttp.a.com/index/upload_demo/qupload" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]"/><br/>
		<input type="file" name="file[]"/><br/>
		<input type="file" name="file[]"/>
		<input type="submit" value="上传"/>
	</form>
</div>
<!-- 七牛多文件上传 End -->

</body>
</html>