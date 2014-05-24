<div class="padded">
    @foreach ($errors->all() as $message )
    <div class="alert alert-error">
        <button class="close" data-dismiss="alert" type="button">×</button>
        {{ $message }}
    </div>
    @endforeach
</div>