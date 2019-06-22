{!!


 Form::action(function ($form)use ($row,$ruledata){


        $form->radio()->field('ismenu',$row['ismenu'])->label(lang('Ismenu'))->data(['1'=>lang('Yes'), '0'=>lang('No')])->render();
        $form->select()->field('pid',$row['pid'])->label(lang('Parent'))->rule(['required'])->data($ruledata)->render();
        $form->text()->field('name',$row['name'])->label(lang('Name'))->rule(['required'])->render();
        $form->text()->field('title',$row['title'])->label(lang('Title'))->rule(['required'])->render();
        $html='    <div class="form-group">
        <label for="icon" class="control-label col-xs-12 col-sm-2">'.lang('Icon').'</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group input-groupp-md">
                <input type="text" class="form-control" id="icon" name="row[icon]" value="'.$row['icon'].'" />
                <a href="javascript:;" class="btn-search-icon input-group-addon">'.lang('Search icon').'</a>
            </div>
        </div>
    </div>';
        $form->text()->field('weigh',$row['weigh'])->label(lang('Weigh'))->rule(['required'])->html($html)->render();
        $form->textarea()->field('condition',$row['condition'])->label(lang('Condition'))->render();
        $form->radio()->field('status',$row['status'])->label(lang('Status'))->data( ['normal'=>lang('Normal'), 'hidden'=>lang('Hidden')])->render();
        $form->textarea()->field('remark',$row['remark'])->label(lang('Remark'))->render();
        $form->button()->submit(lang('Submit'))->reset(lang('Reset'))->render();

 })->render();


 !!}

@include('admin::rule/tpl')