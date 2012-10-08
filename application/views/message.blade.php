@layout('layout')
@section('content')
<!-- ui-dialog -->
<div id="tips" title="提示">
      <p>{{ $message }}</p>
</div>
<script>
$('#tips').dialog({
    autoOpen: true,
    modal: true,
    closeOnEscape: false,
    draggable: false,
    resizable: false,
    beforeclose: function() {
        return false;
    },
    buttons: {
        @if(isset($button))
        "{{$button['name']}}": function() {
            location.href='{{$button['link']}}';
        }
        @else
        "返回": function () {
            history.back();
        }
        @endif
    }
});

@if(isset($button))
    var link = '{{$button['link']}}';
@else
    var link = document.referrer;
@endif
    setTimeout(function(){
        location.href=link;
    }, 3000);

</script>
@endsection
