        <ul class="quickStats">
            <!--li>
                <a href="" class="blueImg"><img src="{{ URL::base() }}/images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="{{ URL::base() }}/images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li-->
            <li>
                <a href="" class="greenImg"><img src="{{ URL::base() }}/images/icons/quickstats/money.png" alt="" /></a>
                <div class="floatR"><strong class="blue">{{ DB::table('orders')->where('user_id', '=', Sentry::user()->get('id') )->count() }}</strong><span>orders</span></div>
            </li>
        </ul>
        <div class="clear"></div>