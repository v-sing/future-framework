{!!


 Form::action(function ($form)use ($ruledata){


        $form->radio()->field('ismenu',1)->label(lang('Ismenu'))->data(['1'=>lang('Yes'), '0'=>lang('No')])->render();
        $form->select()->field('pid')->label(lang('Parent'))->rule(['required'])->data($ruledata)->render();
        $form->text()->field('name')->label(lang('Name'))->rule(['required'])->render();
        $form->text()->field('title')->label(lang('Title'))->rule(['required'])->render();
        $html='    <div class="form-group">
        <label for="icon" class="control-label col-xs-12 col-sm-2">'.lang('Icon').'</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group input-groupp-md">
                <input type="text" class="form-control" id="icon" name="row[icon]" value="fa fa-circle-o" />
                <a href="javascript:;" class="btn-search-icon input-group-addon">'.lang('Search icon').'</a>
            </div>
        </div>
    </div>';
        $form->text()->field('weigh')->label(lang('Weigh'))->rule(['required'])->html($html)->render();
        $form->textarea()->field('condition')->label(lang('Condition'))->render();
        $form->radio()->field('status','normal')->label(lang('Status'))->data( ['normal'=>lang('Normal'), 'hidden'=>lang('Hidden')])->render();
        $form->textarea()->field('remark')->label(lang('Remark'))->render();
        $form->button()->submit(lang('Submit'))->reset(lang('Reset'))->render();

 })->render();


 !!}

@include('admin::rule/tpl')