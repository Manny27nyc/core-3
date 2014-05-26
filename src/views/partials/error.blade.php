@if( isset($_status) )
<div class="alert alert-{{ $_status['type'] }}">
    <button class="close" data-dismiss="alert" type="button">×</button>
    {{ $_status['message'] }}
</div>
@else
    @if($errors)
        @foreach ($errors->all() as $message )
        <div class="alert alert-error">
            <button class="close" data-dismiss="alert" type="button">×</button>
            {{ $message }}
        </div>
        @endforeach
    @endif
@endif