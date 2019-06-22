{!!


 Form::action(function ($form)use ($groupdata,$html){

          $form->select()->field('pid')->label(lang('Parent'))->data($groupdata)->rule(['required'])->render();
          $form->text()->field('name')->label(lang('Name'))->rule(['required'])->render();
          $form->hidden()->field('rules')->label(lang('Rule'))->render();
          $form->radio()->field('status','hidden')->label(lang('Status'))->data(['normal'=>lang('Normal'), 'hidden'=>lang('Hidden')])->html($html)->render();
          $form->button()->submit(lang('Submit'))->reset(lang('Reset'))->render();

 })->render();


 !!}