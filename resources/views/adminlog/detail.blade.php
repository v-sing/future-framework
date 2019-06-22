<table class="table table-striped">
    <thead>
        <tr>
            <th>{{lang(('Title'))}}</th>
            <th>{{lang('Content')}}</th>
        </tr>
    </thead>
    <tbody>
    @foreach($row as $k =>$v)
        <tr>
            <td>{{lang(str_replace('_',' ',ucfirst($k)))}}</td>

            <td>{{$k=='created_at'?date('Y-m-d H:i:s',$v):$v}}</td>
        </tr>
        @endforeach

    </tbody>
</table>
<div class="hide layer-footer">
    <label class="control-label col-xs-12 col-sm-2"></label>
    <div class="col-xs-12 col-sm-8">
        <button type="reset" class="btn btn-primary btn-embossed btn-close" onclick="Layer.closeAll();">{:__('Close')}</button>
    </div>
</div>