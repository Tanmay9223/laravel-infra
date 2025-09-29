<!doctype html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="format-detection" content="date=no" />
<meta name="format-detection" content="address=no" />
<meta name="format-detection" content="telephone=no" />
<meta name="x-apple-disable-message-reformatting" />
<title>{{ config("constant.DOMAIN_NAME") }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<style type="text/css">
	.bottom, td, th {
		font-family: inter;
		font-style: normal;
		font-weight: 200;
		font-size: 12px;
		color: #000;
		text-align: justify;
	}
	body {
		font-family: inter;
		background-color: #fff;
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
	ol {
		padding-left: 12px;
	}
	ol li {
		padding: 10px 0px;
		line-height: 16px;
	}
	a {
		color: #2F6AFF
	}

		.bgstyle{background-image: url("#");
	  background-position: right; /* Center the image */
	  background-repeat: no-repeat; /* Do not repeat the image */
	  background-size: auto;
		border-radius: 0px 12px 0px 0px;


		}
		.mq{max-width: 600px;}


	</style>
</head>
<body>
	<div class="body">
		<div style="margin: auto;  text-align: center;" class="mq">
			<div style="margin-top: 24px; background: #fff; border-radius: 6px; box-shadow: 0 0.8333em 1.433em 0 #c5c5c5ad !important; border: solid 10px #143f6b;">
			  <table  border="0" cellspacing="0" cellpadding="16"  width="100%">
				<tbody>
				  <tr>
					<td style="text-align: left">
                        <img src="{{env('APP_URL')}}/default_images/logo/dark_logo.png" width="115"  border="0" alt="">
					</td>
					<td style="text-align: -webkit-right !important;" class="bgstyle">
						<table border="0" cellspacing="3" cellpadding="0">
						<tbody>
						  <tr>
							<td colspan="2" style="color: #143F6B; font-weight: 500; text-align: center"> Download APP</td>
						  </tr>
						  <tr>
							<td style="text-align: right">
								<a href="https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5&hl=en&referrer=ref_id%3D5189948685934612469%26server%3Dmlm-Demo%252cmlm-Live&pli=1" target="_blank">
									<img src="{{env('APP_URL')}}/default_images/emails/al.png">
								</a>
							</td>
							<td>
								<a href="https://apps.apple.com/us/app/metatrader-5/id413251709" target="_blank">
									<img src="{{env('APP_URL')}}/default_images/emails/pl.png">
								</a>
							</td>
						  </tr>
						</tbody>
					  </table></td>
				  </tr>
				  <tr>
					<td colspan="2" style="text-align: left; padding: 32px; font-weight: 500; color: #64666B">Hi {{ $greeting }}, <br>
					  <br>
					  {!! $body !!}

					  @if(!empty($headingtable) && (is_array($headingtable) || is_object($headingtable)))
                        <br>
						  <tr>
							<td class="text pb20 stcl" style="font-family:Arial,sans-serif; font-size:14px; line-height:26px; text-align:left; padding-bottom:20px;">

							<table  cellspacing="1" cellpadding="5" border="1" style="text-align: center; width: 100%; border: solid 1px #ebebeb47; border-collapse: collapse;">
							  <tbody>
								<tr style="font-weight: bold">
								  @foreach ($headingtable as $heading)
									<td>{{$heading}}</td>
								  @endforeach
								</tr>
								@if(!empty($bodytable) && (is_array($bodytable) || is_object($bodytable)))
								  @foreach ($bodytable as $body_table)
									@php $body_table = array_values($body_table); @endphp
									<tr>
									  @foreach ($body_table as $val)
										<td>{{ $val }}</td>
									  @endforeach
									</tr>
								  @endforeach
								@endif
							  </tbody>
							</table>
							</td>
						  </tr>
						@endif
					  <br>

					  Need support? Write to us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a><br>
					  <br>
					  Happy Investing
					  <div style="color: #1E1E1E; font-weight: 700; padding-top: 8px;">TEAM {{ config('mail.from.name') }}</div>
					  <br>
					  <br></td>
				  </tr>
				</tbody>
			  </table>
			  <div style="background: #FFEAD2; height: 9px !important;"></div>
			  <div style="background: #E38B29; height: 9px !important; border-radius: 0px 0px 0px 0px"></div>
			</div>
		  </div>
		  <div style="margin: auto; max-width: 600px; text-align: left; margin-top: 34px;">
			<table border="0" width="600" cellspacing="0" cellpadding="0" >
			  <tbody>
				<tr style="color: #fff !important;">
				<td>Customer Service <br> Got queries? Write to us on the App. </td>
				  <td style="text-align: right">
					<a href="https://www.facebook.com/mlmgloballtd" target="_blank"><img src="{{env('APP_URL')}}/default_images/emails/fb.png"></a>
					<a href="https://www.instagram.com/mlm/" target="_blank"><img src="{{env('APP_URL')}}/default_images/emails/insta.png" style="padding-left: 3px;"></a>
					<a href="https://x.com/mlm" target="_blank"><img src="{{env('APP_URL')}}/default_images/emails/x.png" style="padding-left: 3px;"></a>
					<a href="https://www.linkedin.com/company/mlm/?viewAsMember=true" target="_blank"><img src="{{env('APP_URL')}}/default_images/emails/in.png" style="padding-left: 3px;"></a>
				  </td>
				</tr>
				<tr>
				  <td colspan="2"><hr></td>
				</tr>
				<tr>
				  <td colspan="2" style="padding-top: 16px"> Note: This is an auto-generated email. Please do not reply. You are receiving this message because your investment account with email id <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> is registered to get updates from {{ config('constant.DOMAIN_NAME') }}.<br>
				  <br>Investment in currency market are subject to market risks, read all the related documents carefully before investing. Visit <a href="{{ config('constant.DOMAIN_URL') }}" target="_blank">www.{{ config('constant.SITE_URL') }}</a> for complete disclaimers. </td>
				</tr>
			  </tbody>
			</table>
		  </div>
	</div>
</body>
</html>

