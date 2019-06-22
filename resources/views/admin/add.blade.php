{!!

     Form::action(function ($form)use ($groupdata){
         $form->selects()->field('group[]')->label(lang('Group'))->data($groupdata)->render();
         $form->text()->field('username')->label(lang('Username'))->rule(['required','username'])->render();
         $form->email()->field('email')->label(lang('Email'))->rule(['required','email'])->render();
         $form->text()->field('nickname')->label(lang('Nickname'))->rule(['required'])->render();
         $form->password()->field('password')->label(lang('Password'))->rule(['required'])->render();
         $form->radio()->field('status','normal')->label(lang('Status'))->data( ['normal'=>lang('Normal'), 'hidden'=>lang('Hidden')])->render();
         $form->button()->submit(lang('Submit'))->reset(lang('Reset'))->render();
     })->option(['id'=>'add-form'])->render();

 !!}