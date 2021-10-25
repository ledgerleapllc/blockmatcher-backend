<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<style type="text/css">
		html, body {
			margin: 0;
			padding: 0;
			width: 100%;
			height: 100%;
		}
		* {
			box-sizing: border-box;
		}
		body {
			padding: 50px 15px !important;
			font-family: "Arial", sans-serif;
		}
		#content {
			width: 700px;
			max-width: 100%;
			margin: 0 auto;
		}
		#content-body {
			width: 100%;
			padding: 30px;
			background-color: white;
			border: 1px solid rgba(0, 0, 0, 0.2);
		}
		h2 {
			text-align: center;
			font-size: 20px;
			padding: 0 100px;
		}
		p {
			font-size: 14px;
		}
		.spacer {
			background-color: rgb(62, 62, 62);
			width: 150px;
			height: 3px;
			margin: 40px auto;
		}
		#button-wrap {
			text-align: center;
		}
		#button-wrap a {
			display: block;
			width: 200px;
			background: red;
			padding: 15px;
			color: white;
			text-decoration: none;
			margin: 30px auto;
		}
		#content-footer {
			width: 100%;
			text-align: center;
			margin-top: 20px;
			font-size: 14px;
			font-weight: bold;
		}
		#content-footer a {
			color: #333;
		}
	</style>
</head>

<body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: rgb(245, 247, 247);">
	<div id="content">
		<div id="content-body">
			<img src="http://casperbackend.kyckangaroo.com/images/email-logo.png" width="300px" style="margin-left: 150px;">
			<h2>{{ $headline }}</h2>
			<div class="spacer"></div>
			<p>{!! $content !!}</p>
			@if (isset($button) && $button)
				<div id="button-wrap">
					{!! $button !!}
				</div>
			@endif
			<p style="margin-top: 50px;">Thanks for being a part of the program,</p>
			<p>- Compliance team</p>
		</div> <!-- Content Inner End !-->
		<div id="content-footer">
			<a href="">Unsubscribe</a>
			<span>&nbsp;|&nbsp;</span>
			<a href="">Contact Us</a>
			<span>&nbsp;|&nbsp;</span>
			<a href="">Privacy Policy</a>
		</div> <!-- Content Footer End !-->
	</div>
</body>

</html>