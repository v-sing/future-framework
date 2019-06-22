{!!


 Form::action(function ($form)use ($row,$groupdata,$html){

          $form->select()->field('pid',$row['pid'],['data-id'=>$row['id'], 'data-pid'=>$row['pid']])->label(lang('Parent'))->data($groupdata)->rule(['required'])->render();
          $form->text()->field('name',$row['name'])->label(lang('Name'))->rule(['required'])->render();
          $form->hidden()->field('rules',$row['rules'])->label(lang('Rule'))->rule(['required'])->render();
          $form->radio()->field('status',$row['status'])->label(lang('Status'))->data(['normal'=>lang('Normal'), 'hidden'=>lang('Hidden')])->html($html)->render();
          $form->button()->submit(lang('Submit'))->reset(lang('Reset'))->render();

 })->render();


 !!}