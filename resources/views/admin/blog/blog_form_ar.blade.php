<div>
	@if ($errors->any())
		<div class="alert alert-danger" role="alert">
			@foreach ($errors->all() as $error)
				<p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true" style="color:red;">&times;</span></button></p>
			@endforeach
		</div>
	@endif
	<div class="row">
		<div class="col-lg-12">
			<div class="card m-b-30">
				<div class="card-header">
					<h5 class="card-title">{{ __('Add Testimonial') }}</h5>
				</div>
				<div class="card-body">
<form action="{{action('BlogController@store')}}" class="form" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input  type="hidden" class="form-control" name="user_id" value="{{ Auth::User()->id }}" >
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <!-- card body start -->
                                    <div class="card-body">
                                        <!-- row start -->
                                          <div class="row">
                                              
                                              <div class="col-md-12">
                                                  <!-- row start -->
                                                  <div class="row">
                                                    
                                                     <!-- Heading -->
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.Heading') }} : <span class="text-danger">*</span></label>
                                                            <input type="text" value="{{ old('heading') }}" autofocus="" class="form-control @error('heading') is-invalid @enderror" placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Heading') }}" name="heading" required="">
                                                            @error('heading')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                      <!-- Slug -->
                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.Slug') }} : <span class="text-danger">*</span></label>
                                                            <input type="text" pattern="[/^\S*$/]+"  value="{{ old('slug') }}" autofocus="" class="form-control @error('slug') is-invalid @enderror" placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Slug') }}" name="slug" required="">
                                                            @error('slug')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                     <!-- ButtonText -->
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.ButtonText') }} : <span class="text-danger">*</span></label>
                                                            <input type="text" value="{{ old('text') }}" autofocus="" class="form-control @error('title') is-invalid @enderror" placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.ButtonText') }}" name="text" required="">
                                                            @error('text')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div> --}}

                                                   

                                                    <!-- Date -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.Date') }} : <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control" name="date" id="inputDate" placeholder="{{ __('adminstaticword.Select') }} {{ __('adminstaticword.Date') }}" required>
                                                            
                                                            @error('date')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Description -->
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.Detail') }}: <span class="text-danger">*</span></label>
                                                            <textarea id="detail" name="detail" class="@error('detail') is-invalid @enderror" placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Detail') }}" required="">{{ old('detail') }}</textarea>
                                                            @error('detail')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                    
                                                    <!-- image -->

                                                    @if(Auth::user()->role == 'admin')
                                                    <div class="col-md-6">
                                                        <label class="text-dark">{{ __('adminstaticword.Image') }}:<span class="text-danger">*</span></label><br>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" readonly id="image" name="image">
                                                            <div class="input-group-append">
                                                              <span data-input="image" class="midia-toggle btn-primary  input-group-text" id="basic-addon2">{{ __('Browse') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(Auth::user()->role == 'instructor')
                                                    <div class="col-md-6">
                                                    <label class="text-dark">{{ __('adminstaticword.Image') }}:<span class="text-danger">*</span></label><br>
                                                    <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="image" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" required>
                                                        <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                    </div>
                                                    </div>
                                                    @endif

                                                    <!-- Approved -->
                                                    @if(Auth::user()->role == 'admin')
                                                    <div class="form-group col-md-3">
                                                        <label class="text-dark" for="exampleInputDetails">{{ __('adminstaticword.Approved') }} : <sup class="redstar text-danger">*</sup></label><br>
                                                        <input type="checkbox" class="custom_toggle" name="approved" checked />
                                                        <input type="hidden"  name="free" value="0" for="status" id="status">
                                                    </div>

                                                    <!-- status -->
                                                    <div class="form-group col-md-3">
                                                    <label class="text-dark" for="exampleInputDetails">{{ __('adminstaticword.Status') }} :</label><br>
                                                    <input type="checkbox" class="custom_toggle" name="status" checked />
                                                    <input type="hidden"  name="free" value="0" for="status" id="status">
                                                    </div>
                                                    @endif
                                                                      
                                                    <!-- create and close button -->
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <button type="reset" class="btn btn-danger-rgba mr-1"><i class="fa fa-ban"></i> {{ __("Reset")}}</button>
                                                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                                            {{ __("Create")}}</button>
                                                        </div>
                                                    </div>

                                                  </div><!-- row end -->
                                              </div><!-- col end -->
                                          </div><!-- row end -->

                                    </div>
                                    <!-- card body end -->
                                </div>
                            </div>
                        </div>
                  </form>				</div>
			</div>
		</div>
	</div>
</div>

<script>
	(function($) {
		"use strict";
		tinymce.init({
			selector: 'textarea'
		});
	})(jQuery);
</script>
