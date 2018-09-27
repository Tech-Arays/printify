<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield("title") | @lang('labels.admin') | Monetize Social</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(e){ 
	$(".size-check").click(function(){
		$(this).closest('label').toggleClass("check");
	});
	$('.color-check').on('click',function(){
		$(this).closest('label').toggleClass('active');
	});
});

</script>
{!! Rapyd::styles() !!}