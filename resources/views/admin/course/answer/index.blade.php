@extends('admin.layouts.master')
@section('title','Edit QuestionAnswer')
@section('maincontent')
​
@component('components.breadcumb',['thirdactive' => 'active'])
​
@slot('heading')
{{ __('Home') }}
@endslot
​
@slot('menu1')
{{ __('Admin') }}
@endslot
​
@slot('menu2')
{{ __('Answers') }}
@endslot
​
@slot('button')
<div class="col-md-4 col-lg-4">
  @can('answer.create')
  <a data-toggle="modal" data-target="#myModalanswer" class="float-right btn btn-primary-rgba"> <i class="feather icon-plus mr-2"></i>Add Answers</a>
  @endcan
</div>
@endslot
​
@endcomponent

<div class="contentbar">
 
  <div class="row">
    <div class="col-lg-12">
     
    <div class="card m-b-30">
      <div class="card-header">
          <h5 class="card-box">All Answers</h5>
        </div>
      <div class="card-body">

      <div class="table-responsive">
        <table id="datatable-buttons" class="table table-striped table-bordered">

          <thead>
          
            <th>#</th>
            <th>{{ __('adminstaticword.Question') }}</th>
            <th>{{ __('adminstaticword.Answer') }}</th>
            <th>{{ __('adminstaticword.Status') }}</th>
            <th>{{ __('adminstaticword.Action') }}</th>
          </tr>
          </thead>
          <tbody>
          <?php $i=0;?>
          @foreach($answers as $ans)
          <tr>
          	<?php $i++;?>
          	<td><?php echo $i;?></td>
            	<td>{{strip_tags($ans->question['question'])}}</td>
            	<td>{{strip_tags($ans->answer)}}</td> 
            <td>
                @if($ans->status==1)
                  {{ __('adminstaticword.Active') }}
                @else
                  {{ __('adminstaticword.Deactive') }}
                @endif	                    
            </td>
            <td>
               <div class="dropdown">
                <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                    class="feather icon-more-vertical-"></i></button>
                <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                  @can('answer.edit')
                  <a class="dropdown-item" href="{{route('courseanswer.edit',$ans->id)}}"><i class="feather icon-edit mr-2"></i>Edit</a>
                  @endcan
                  @can('answer.delete')

                  <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{$ans->id}}">
                    <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
                  </a>
                  @endcan
                </div>
              </div>
            </td>

            <div class="modal fade bd-example-modal-sm" id="delete{{$ans->id}}" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleSmallModalLabel">Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted">Do you really want to delete this Bundle ? This process cannot be
                      undone.</p>
                  </div>
                  <div class="modal-footer">
                    <form method="post" action="{{url('courseanswer/'.$ans->id)}}" data-parsley-validate
                      class="form-horizontal form-label-left">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}

                      <button type="reset" class="btn btn-gray translate-y-3"
                        data-dismiss="modal">{{ __('adminstaticword.No') }}</button>
                      <button type="submit" class="btn btn-danger">{{ __('adminstaticword.Yes') }}</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            
            
          </tr>
          @endforeach
          
          </tbody>
        </table>
      </div>

    </div>
  </div>
  


  <div class="modal fade" id="myModalanswer" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>

        <div class="box box-primary">
          <div class="panel panel-sum">
            <div class="modal-body">
              <form id="demo-form2" method="post" action="{{url('courseanswer/')}}" data-parsley-validate class="form-horizontal form-label-left">
                {{ csrf_field() }}
               
                <input type="hidden" name="instructor_id" class="form-control" value="{{ Auth::User()->id }}"  />
                <input type="hidden" name="ans_user_id" value="{{Auth::user()->id}}" />
           
                <div class="row">
                  <div class="col-md-12">
                    <label  for="exampleInputTit1e">{{ __('adminstaticword.SelectQuestion') }}:<sup class="redstar">*</sup></label>
                    <br>
                    <select  name="question_id" required class="form-control select2">
                      <option value="none" selected disabled hidden> 
                       {{ __('adminstaticword.SelectanOption') }}
                      </option>
                      @foreach($questions as $ques)
                        <option value="{{ $ques->id }}">
                          {{strip_tags($ques->question)}}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  @foreach($questions as $ques)
                  <input type="hidden" name="ques_user_id"  value="{{$ques->user_id}}" />
                  <input type="hidden" name="course_id"  value="{{$ques->course_id}}" />
                  @endforeach
                </div>
                <br>

                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInput">{{ __('adminstaticword.Answer') }}:<sup class="redstar">*</sup></label>
                    <textarea id="detail" name="answer" rows="4" class="form-control" placeholder="Please Enter Your Answer"></textarea>
                  </div>
                </div>
                <br>

                <div class="col-md-12">
                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                    <label class="switch">
                      <input class="slider" type="checkbox" name="status" checked />
                      <span class="knob"></span>
                    </label>
                </div>
                <br>
        
                <div class="box-footer">
                  <button type="submit" value="Add Answer" class="btn btn-md col-md-3 btn-primary">+  {{ __('adminstaticword.Save') }}</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Model close -->  
  </div>  

  </div>
  <!-- /.row -->

  </div>
    <!-- /.col -->
</div>
@endsection