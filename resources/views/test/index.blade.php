{!!
 Form::action(function ($form){
    $form->password()->field('password')->label('password')->option(['class'=>['xxx'],'d'=>"xxx"])->render();

})->option(['id'=>'update-form'])->render()
 !!}

{{--$form->text()->field('text')->label('text')->render();--}}
{{--$form->email()->field('email')->label('email')->render();--}}
{{--$form->url()->field('url')->label('url')->render();--}}
{{--$form->color()->field('color')->label('color')->render();--}}
{{--$form->textarea()->field('textarea1','dddddc')->label('textarea1')->render();--}}
{{--$form->textarea()->field('textarea','dddddx')->label('textarea')->tip('xxxxx')->render();--}}
{{--$form->date()->field('date')->label('date')->render();--}}
{{--$form->datetime()->field('datetime')->label('datetime')->render();--}}
{{--$form->editor()->field('editor')->label('editor')->render();--}}
{{--$form->number()->field('number')->label('number')->render();--}}
{{--$form->select()->field('select','xxxx')->data(['0'=>'xxx','1'=>'xxxx'])->label('select')->tip('test')->render();--}}
{{--$form->selects()->field('selects','xx,x')->data(['x'=>'xxx','xx'=>'xxxx'])->label('selects')->tip('test')->render();--}}
{{--$form->switching()->field('switching',0)->label('switching')->render();--}}
{{--$form->radio()->field('radio',0)->label('radio')->data(['0'=>'否','1'=>'是'])->render();--}}
{{--$form->checkbox()->field('checkbox','0,1,2,3,4,5,6')->label('checkbox')->data(['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8'])->render();--}}
{{--$form->image()->field('image','/uploads/20190402/dbed392bdaca053048674ab5b6f90fee.jpg')->label('image')->render();--}}
{{--$form->images()->field('images','/uploads/20190402/dbed392bdaca053048674ab5b6f90fee.jpg,/uploads/20190402/e9d3bc38eb8e05a5c4b39bd2628ab039.JPG')->label('images')->render();--}}
{{--$form->file()->field('file','/uploads/20190402/dbed392bdaca053048674ab5b6f90fee.jpg')->label('file')->render();--}}
{{--$form->files()->field('files','/uploads/20190402/dbed392bdaca053048674ab5b6f90fee.jpg')->label('files')->render();--}}
{{--$form->button()->submit(lang('Submit'))->reset(lang('Reset'))->addButton('button')->render();--}}