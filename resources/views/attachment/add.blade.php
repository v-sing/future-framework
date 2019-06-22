<form id="add-form" class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" action="">
    @if($site['cdnurl'])
        <div class="form-group">
            <label for="c-third" class="control-label col-xs-12 col-sm-2">{{lang('Upload')}}:</label>
            <div class="col-xs-12 col-sm-8">
                <input type="text" name="row[third]" id="c-third" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label for="c-third" class="control-label col-xs-12 col-sm-2"></label>
            <div class="col-xs-12 col-sm-8">
                <button id="plupload-third" class="btn btn-danger plupload" data-multiple="true"
                        data-input-id="c-third"><i class="fa fa-upload"></i> {{lang("Upload to third")}}
                </button>
            </div>
        </div>
    @endif

    <div class="form-group">
        <label for="c-local" class="control-label col-xs-12 col-sm-2">{{lang('Upload')}}:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="row[local]" id="c-local" class="form-control"/>
        </div>
    </div>

    <div class="form-group">
        <label for="c-local" class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button id="plupload-local" class="btn btn-primary plupload" data-input-id="c-local"
                    data-url="{{url('admin/ajax/upload')}}"><i class="fa fa-upload"></i> {{lang("Upload to local")}}
            </button>
        </div>
    </div>

    <div class="form-group">
        <label for="c-editor" class="control-label col-xs-12 col-sm-2">{{lang('Upload from editor')}}:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea name="row[editor]" id="c-editor" cols="60" rows="5" class="form-control editor"></textarea>
        </div>
    </div>
    <div class="form-group hidden layer-footer">
        <div class="col-xs-2"></div>
        <div class="col-xs-12 col-sm-8">
            <button type="reset" class="btn btn-default btn-embossed">{{lang('Reset')}}</button>
        </div>
    </div>
</form>
