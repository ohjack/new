<!-- Sidebar begins -->
<style>
/*左侧二级菜单样式*/
.SecMenu{
position: absolute;
background: url(../images/subArrow_2.png) no-repeat 10px 5px;
font-size: 11px;
width: 100px;
top: 13px;
padding-top: 10px;
margin-left: 100px;
display:none;
}
.SecMenuContent{
width: 100px;
border-bottom: 1px solid #343434;
text-align: left;
border-top: 1px solid #545454;
margin-top:-10px;
margin-left:16px;
background: url(../images/backgrounds/sidebar.jpg);
padding: 0;
padding-left:10px;
}

.SecMenulink{
width: 100px;
height:28px
}
.SecMenuLink a{
width: 100px;
height:28px;
line-height:28px;
padding: 0 !important;
text-decoration: none;
color: #CCC !important;
display: block;

}
</style>
<script type="text/javascript">
$(function(){
	$(".nav li").mouseover(function(){
	   //alert($(this).text());
	    $(this).find(".SecMenu").css('display','block');
	    $(this).mouseout(function(){
	    	$(this).find(".SecMenu").css('display','none');
		    
		    });
		});
	
}); 
</script>
<div id="sidebar">
    <div class="mainNav">
        <!-- Main nav -->
        <ul class="nav">
            <li><a href="{{ URL::base() }}" title="" class="active"><img src="{{ URL::base() }}/images/icons/mainnav/dashboard.png" alt="仪表盘" /><span>仪表盘</span></a>
            </li>
            <li>
                <a href="{{ URL::base() }}" title="" class="active"><img src="{{ URL::base() }}/images/icons/mainnav/tables.png" alt="仪表盘" /><span>设置2</span></a>
                <div class='SecMenu'>
                    <div class='SecMenuContent'>
                        <div class="SecMenuLink"><a href="#">设置用户平台</a></div>
                        <div class="SecMenuLink"><a href="#">设置显示标识</a></div>
                        <div class="SecMenuLink"><a href="#">修改密码</a></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- Sidebar ends -->
