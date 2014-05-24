<div class="container padded">
    <div id="breadcrumbs">
        <div class="breadcrumb-button blue">
            <span class="breadcrumb-label"><i class="icon-home"></i> Home</span>
            <span class="breadcrumb-arrow"><span></span></span>
        </div>
        @if( isset($breadcrumbs) )
        <div class="breadcrumb-button">
              <span class="breadcrumb-label">
                <i class="icon-dashboard"></i> Dashboard
              </span>
            <span class="breadcrumb-arrow"><span></span></span>
        </div>
        @endif
    </div>
</div>