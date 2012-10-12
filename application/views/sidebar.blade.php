<!-- Sidebar begins -->
<style>
/*左侧二级菜单样式*/
.SecMenu{
position: absolute;
background: url(../images/subArrow_2.png) no-repeat 10px 5px;
font-size: 11px;
width: 170px;
top: 13px;

margin-left: 96px;
margin-top:-5px;
display:none;
}
.SecMenuContent{
width: 170px;
border-bottom: 1px solid #343434;
text-align: left;
border-top: 1px solid #545454;
margin-top:-10px;
margin-left:16px;
background: url(../images/backgrounds/sidebar.jpg);
padding: 0;
-webkit-border-top-right-radius: 2px; -webkit-border-top-left-radius: 2px; -moz-border-radius-topright: 2px; -moz-border-radius-topleft: 2px; border-top: none; padding-top: 1px; 
-webkit-border-bottom-right-radius: 2px; -webkit-border-bottom-left-radius: 2px; -moz-border-radius-bottomright: 2px; -moz-border-radius-bottomleft: 2px; 
}

.SecMenuLink{
width: 170px;
height:28px;
border-bottom: 1px solid #343434 !important;
border-top: 1px solid #545454 !important;

}
.SecMenuLink a{
width: 170px;
height:28px;
line-height:28px;
padding: 0 !important;
padding-left:24px !important;
background: url(../images/elements/control/subnav_arrow.png) no-repeat 12px 10px !important;
text-decoration: none;

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
            <li>
                <a href="{{ URL::base() }}" title="" class="active"><img src="{{ URL::base() }}/images/icons/mainnav/dashboard.png" alt="仪表盘" /><span>仪表盘</span></a>
            </li>
            <li>
                <a href="{{ URL::base() }}" title="" class="active"><img src="{{ URL::base() }}/images/icons/mainnav/dashboard.png" alt="仪表盘" /><span>库存</span></a>
            </li>
            <li>
                <a href="{{ URL::base() }}" title="" class="active"><img src="{{ URL::base() }}/images/icons/mainnav/tables.png" alt="仪表盘" /><span>设置</span></a>
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
