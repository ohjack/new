function  check()
{
 	if($("input[name='username']").val()=="")
	{
		alert('用户名不能为空');
		return false;
	}
	if($("input[name='password']").val()!=$("input[name='confirm_password']").val())
	{
		alert('两次密码输入不一致');
		return false;
	}
	if($("input[name='email']").val()=="")
	{
		alert('邮箱不能为空');
		return false;
	}
	
}
