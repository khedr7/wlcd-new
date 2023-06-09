<div class="row">
	@if ($errors->any())
		<div class="alert alert-danger" role="alert">
			@foreach ($errors->all() as $error)
				<p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true" style="color:red;">&times;</span></button></p>
			@endforeach
		</div>
	@endif

	<!-- row started -->
	<div class="col-lg-12">

		<div class="card m-b-30">

			<!-- Card header will display you the heading -->
			<div class="card-header">
				<h5 class="card-box">{{ __('About') }}</h5>
			</div>

			<!-- card body started -->
			<div class="card-body">
				<!-- form start -->
				<form action="{{ action('AboutController@update') }}" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
					{{ method_field('PUT') }}

					<input type="hidden" name="lang" value="ar" id="lang">


					<!-- section 1 start -->
					{{-- <div class="row">
								<div class="col-md-12">
			                        <label class="text-dark" for="one_enable">{{ __('Section One Header') }}</label><br>
									<input type="checkbox" class="custom_toggle" id="customSwitch1" name="one_enable" {{ $data['one_enable']==1 ? 'checked' : '' }} />
                                	<input type="hidden" name="free" value="0" for="status" id="customSwitch1">
			                        <br>

					                <div class="row" style="{{ $data['one_enable']==1 ? '' : 'display:none' }}" id="sec_one">
					                  <div class="col-md-6">
					                    <label class="text-dark" for="one_heading">{{ __('Section One Heading :') }} <span class="text-danger">*</span></label>
					                    <input value="{{ $data['one_heading'] }}" autofocus  name="one_heading" type="text" class="form-control" placeholder="Enter Heading"/>
										<br>

					                    <label class="text-dark" for="one_text">{{ __('Section One Text :') }} <span class="text-danger">*</span></label>
					                    <input value="{{ $data['one_text']}}" autofocus  name="one_text" type="text" class="form-control" placeholder="Enter Heading"/>
					                  </div>
					                  <div class="col-md-6">
									  <!-- ============== -->
										<label class="text-dark" for="link_four">{{ __('Section One link :') }} <span class="text-danger">*</span></label>
					                    <input value="{{ $data['link_four']}}" autofocus  name="link_four" type="text" class="form-control" placeholder="Enter Heading"/>

					                    <br>
										<label class="text-dark">{{ __('Section One BackgroundImage :') }}:<span class="text-danger">*</span></label><br>
												<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="one_image" aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
												</div>
												@if ($image = @file_get_contents('../public/images/about/' . $data['one_image']))
												<img src="{{ url('/images/about/'.$data['one_image']) }}" class="image_size"/>
												@endif
					                  </div>

					              	</div>
			                    </div>
			                </div> --}}
					{{-- <br>
			              	<br> --}}
					<!-- section 1 end -->

					<!-- section 2 start -->
					<div class="row">
						<div class="col-md-12">
							{{-- <label  class="text-dark" for="two_enable">{{ __('Family') }}</label> --}}
							<h5>{{ __('Family section') }}</h5>
							{{-- <br> --}}
							{{-- <input type="checkbox" class="custom_toggle" id="customSwitch2" name="two_enable" {{ $data['two_enable']==1 ? 'checked' : '' }} /> --}}
							<input type="hidden" name="free" value="0" for="status" id="customSwitch2">
							{{-- <br> --}}
							<br>

							<div class="row" style="{{ $data['two_enable'] == 1 ? '' : 'display:none' }}" id="sec_two">


								<div class="col-md-6">
									<label class="text-dark" for="two_heading">{{ __('Family section Heading :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('two_heading', 'ar', false) }}" autofocus name="two_heading" type="text" class="form-control"
										placeholder="Enter Heading" />
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_text">{{ __('Family section Text :') }} <span
											class="text-danger">*</span></label>
									<textarea name="two_text" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('two_text', 'ar', false) }}</textarea>
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark">{{ __('Family section Instructor Image One :') }}:<span
											class="text-danger">*</span></label><br>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile01" name="two_imageone"
												aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
										</div>
									</div>
									@if ($image = @file_get_contents('../public/images/about/' . $data['two_imageone']))
										<img src="{{ url('/images/about/' . $data['two_imageone']) }}" class="image_size" />
									@endif
								</div>

								<div class="col-md-6">

									<label class="text-dark">{{ __('Family section Instructor Image Two :') }}:<span
											class="text-danger">*</span></label><br>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile01" name="two_imagetwo"
												aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
										</div>
									</div>
									@if ($image = @file_get_contents('../public/images/about/' . $data['two_imagetwo']))
										<img src="{{ url('/images/about/' . $data['two_imagetwo']) }}" class="image_size" />
									@endif
									<br><br>
								</div>

								<div class="col-md-6">
									<label class="text-dark">{{ __('Family section Instructor Image Three :') }}<span
											class="text-danger">*</span></label><br>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile01" name="two_imagethree"
												aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
										</div>
									</div>
									@if ($image = @file_get_contents('../public/images/about/' . $data['two_imagethree']))
										<img src="{{ url('/images/about/' . $data['two_imagethree']) }}" class="image_size" />
									@endif

									<br><br>
								</div>

								<div class="col-md-6">
									<label class="text-dark">{{ __('Family Section Instructor Image Four :') }}<span
											class="text-danger">*</span></label><br>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile01" name="two_imagefour"
												aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
										</div>
									</div>
									@if ($image = @file_get_contents('../public/images/about/' . $data['two_imagefour']))
										<img src="{{ url('/images/about/' . $data['two_imagefour']) }}" class="image_size" />
									@endif

									<br>
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_txtone">{{ __('Family section Instructor text One :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('two_txtone', 'ar', false) }}" name="two_txtone" type="text" class="form-control"
										placeholder="Enter Text" />
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_txttwo">{{ __('Family section Instructor text Two :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('two_txttwo', 'ar', false) }}" name="two_txttwo" type="text" class="form-control"
										placeholder="Enter Text" />
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_txtthree">{{ __('Family section Instructor text Three :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('two_txtthree', 'ar', false) }}" name="two_txtthree" type="text" class="form-control"
										placeholder="Enter Text" />
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_txtfour">{{ __('Family section Instructor text Four :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('two_txtfour', 'ar', false) }}" name="two_txtfour" type="text" class="form-control"
										placeholder="Enter Text" />
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="two_imagetext">{{ __('Family section Image One Detail :') }} <span
											class="text-danger">*</span></label>
									<textarea name="two_imagetext" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('two_imagetext', 'ar', false) }}</textarea>
									<br>
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="text_one">{{ __('Family section Image Two Detail :') }} <span
											class="text-danger">*</span></label>
									<textarea name="text_one" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('text_one', 'ar', false) }}</textarea>
									<br>
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="text_two">{{ __('Family section Image Three Detail :') }} <span
											class="text-danger">*</span></label>
									<textarea name="text_two" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('text_two', 'ar', false) }}</textarea>
									<br>
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="text_three">{{ __('Family section Image Four Detail :') }} <span
											class="text-danger">*</span></label>
									<textarea name="text_three" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('text_three', 'ar', false) }}</textarea>
									<br>
								</div>

							</div>
						</div>
					</div>
					<br>
					<hr>
					<br>
					<!-- section 2 end -->
					<!-- section 3 start -->
					<div class="row">
						<div class="col-md-12">
							{{-- <label class="text-dark" for="three_enable">{{ __('Numbers section') }}</label><br> --}}
							<h5>{{ __('Numbers section') }}</h5>
							{{-- <input type="checkbox" class="custom_toggle" id="customSwitch3" name="three_enable" {{ $data['three_enable']==1 ? 'checked' : '' }}/> --}}
							<input type="hidden" name="free" value="0" for="status" id="customSwitch3">
							<br>

							<div class="row" style="{{ $data['three_enable'] == 1 ? '' : 'display:none' }}" id="sec_three">
								<div class="col-md-6">
									<label class="text-dark" for="three_heading">{{ __('Numbers section Heading :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_heading', 'ar', false) }}" autofocus name="three_heading" type="text"
										class="form-control" placeholder="Enter Heading" />
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="three_text">{{ __('Numbers section Text :') }} <span
											class="text-danger">*</span></label>
									<textarea name="three_text" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('three_text', 'ar', false) }}</textarea>
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_countone">{{ __('Numbers section Counter One :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_countone'] }}" name="three_countone" type="text" class="form-control"
										placeholder="Enter Count" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_counttwo">{{ __('Numbers section Counter Two :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_counttwo'] }}" name="three_counttwo" type="text" class="form-control"
										placeholder="Enter Count" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_countthree">{{ __('Numbers section Counter Three :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_countthree'] }}" name="three_countthree" type="text" class="form-control"
										placeholder="Enter Count" />
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_countfour">{{ __('Numbers section Counter Four :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_countfour'] }}" name="three_countfour" type="text" class="form-control"
										placeholder="Enter Count" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_countfive">{{ __('Numbers section Counter Five :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_countfive'] }}" name="three_countfive" type="text" class="form-control"
										placeholder="Enter Count" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_countsix">{{ __('Numbers section Counter Six :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data['three_countsix'] }}" name="three_countsix" type="text" class="form-control"
										placeholder="Enter Count" />
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txtone">{{ __('Numbers section text One :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txtone', 'ar', false) }}" name="three_txtone" type="text" class="form-control"
										placeholder="Enter Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txttwo">{{ __('Numbers section text Two :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txttwo', 'ar', false) }}" name="three_txttwo" type="text" class="form-control"
										placeholder="Enter Count Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txtthree">{{ __('Numbers section text Three :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txtthree', 'ar', false) }}" name="three_txtthree" type="text" class="form-control"
										placeholder="Enter Count Text" />
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txtfour">{{ __('Numbers section text Four :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txtfour', 'ar', false) }}" name="three_txtfour" type="text" class="form-control"
										placeholder="Enter Count Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txtfive">{{ __('Numbers section text Five :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txtfive', 'ar', false) }}" name="three_txtfive" type="text" class="form-control"
										placeholder="Enter Count Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="three_txtsix">{{ __('Numbers section text Six :') }} <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('three_txtsix', 'ar', false) }}" name="three_txtsix" type="text" class="form-control"
										placeholder="Enter Count Text" />
								</div>

							</div>
						</div>
					</div>
					<br>
					<hr>
					<br>
					<!-- section 3 end -->
					<!-- section 4 start -->
					<div class="row">
						<div class="col-md-12">
							{{-- <label class="text-dark" for="four_enable">{{ __('Section Four') }}</label><br> --}}
							<h5>{{ __('Vision section') }}</h5><br>
							{{-- <input type="checkbox" class="custom_toggle" id="customSwitch4" name="four_enable"
										{{ $data['four_enable'] == 1 ? 'checked' : '' }} /> --}}
							<input type="hidden" name="free" value="0" for="status" id="customSwitch4">
							{{-- <br> --}}

							<div class="row" style="{{ $data['four_enable'] == 1 ? '' : 'display:none' }}" id="sec_four">
								{{-- <div class="col-md-6">
						                    <label class="text-dark" for="four_heading">{{ __('Section Four Heading : ') }}<span class="text-danger">*</span></label>
						                    <input value="{{ $data['four_heading'] }}" autofocus name="four_heading" type="text" class="form-control" placeholder="Enter Heading"/>
						                </div><br> --}}



								<div class="col-md-12">
									<label class="text-dark" for="four_text">{{ __('Vision section Text :') }} <span
											class="text-danger">*</span></label>
									<textarea name="four_text" rows="3" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('four_text', 'ar', false) }}</textarea>
								</div>

								{{-- <div class="col-md-6">
											<label class="text-dark">{{ __('Section Four Image One :') }}<span class="text-danger">*</span></label><br>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="four_imageone"
														aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
											</div>
											@if ($image = @file_get_contents('../public/images/about/' . $data['four_imageone']))
												<img src="{{ url('/images/about/' . $data['four_imageone']) }}" class="image_size" />
											@endif

										</div> --}}

								{{-- <div class="col-md-6">

											<label class="text-dark">{{ __('Section Four Image Two :') }}<span class="text-danger">*</span></label><br>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="four_imagetwo"
														aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
											</div>
											@if ($image = @file_get_contents('../public/images/about/' . $data['four_imagetwo']))
												<img src="{{ url('/images/about/' . $data['four_imagetwo']) }}" class="image_size" />
											@endif

											<br>
											<br>
										</div> --}}

								{{-- <div class="col-md-4">
											<label class="text-dark" for="four_txtone">{{ __('Section Four Image Text One :') }} <span
													class="text-danger">*</span></label>
											<input value="{{ $data['four_txtone'] }}" name="four_txtone" type="text" class="form-control"
												placeholder="Enter Heading" />
										</div> --}}

								{{-- <div class="col-md-4">
											<label class="text-dark" for="four_txttwo">{{ __('Section Four Image Text Two :') }} <span
													class="text-danger">*</span></label>
											<input value="{{ $data['four_txttwo'] }}" name="four_txttwo" type="text" class="form-control"
												placeholder="Enter Heading" />
											<br>
										</div> --}}

								{{-- <div class="col-md-4 display-none">
											<label class="text-dark" for="four_icon">{{ __('Section Four Icon :') }} <span
													class="text-danger">*</span></label>
											<input value="1"name="four_icon" type="text" class="form-control" placeholder="Enter Heading" />
										</div> --}}
							</div>
						</div>
					</div>
					<br>
					<hr>
					<br>
					<!-- section 4 end -->
					<!-- section 5 start -->
					{{-- <div class="row">
								<div class="col-md-12">
									<label class="text-dark" for="five_enable">{{ __('Section Five') }}</label><br>
									<input type="checkbox" class="custom_toggle" id="customSwitch5" name="five_enable"
										{{ $data['five_enable'] == 1 ? 'checked' : '' }} />
									<input type="hidden" name="free" value="0" for="status" id="customSwitch5">
									<br><br>

									<div class="row" style="{{ $data['five_enable'] == 1 ? '' : 'display:none' }}" id="sec_five">
										<div class="col-md-12">
											<label class="text-dark" for="five_heading">{{ __('Section Five Heading :') }} <span
													class="text-danger">*</span></label>
											<input value="{{ $data['five_heading'] }}" autofocus name="five_heading" type="text"
												class="form-control" placeholder="Enter Heading" />
										</div>

										<div class="col-md-12">
											<label class="text-dark" for="five_text">{{ __('Section Five Text :') }} <span
													class="text-danger">*</span></label>
											<textarea name="five_text" rows="5" class="form-control" placeholder="Enter Your Text">{{ $data['five_text'] }}</textarea>
											<br>
										</div>

										<div class="col-md-4">

											<label class="text-dark">{{ __('Section Five Image One :') }}<span
													class="text-danger">*</span></label><br>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="five_imageone"
														aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
											</div>
											@if ($image = @file_get_contents('../public/images/about/' . $data['five_imageone']))
												<img src="{{ url('/images/about/' . $data['five_imageone']) }}" class="image_size" />
											@endif

										</div>
										<div class="col-md-4">
											<label class="text-dark">{{ __('Section Five Image Two :') }}<span
													class="text-danger">*</span></label><br>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="five_imagetwo"
														aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
											</div>
											@if ($image = @file_get_contents('../public/images/about/' . $data['five_imagetwo']))
												<img src="{{ url('/images/about/' . $data['five_imagetwo']) }}" class="image_size" />
											@endif

										</div>
										<div class="col-md-4">

											<label class="text-dark">{{ __('Section Five Image Three :') }}<span
													class="text-danger">*</span></label><br>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
												</div>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="inputGroupFile01" name="five_imagethree"
														aria-describedby="inputGroupFileAddon01">
													<label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
												</div>
											</div>
											@if ($image = @file_get_contents('../public/images/about/' . $data['five_imagethree']))
												<img src="{{ url('/images/about/' . $data['five_imagethree']) }}" class="image_size" />
											@endif

										</div>
									</div>
								</div>
							</div>
							<br>
							<br> --}}
					<!-- section 5 end -->
					<!-- section 6 start -->
					<div class="row">
						<div class="col-md-12">
							{{-- <label class="text-dark" for="six_enable">{{ __('Section Six') }}</label><br> --}}
							<h5>{{ __('Missions and Social media section') }}</h5>
							{{-- <input type="checkbox" class="custom_toggle" id="customSwitch6" name="six_enable"
										{{ $data['six_enable'] == 1 ? 'checked' : '' }} /> --}}
							<input type="hidden" name="free" value="0" for="status" id="customSwitch6">
							<br>

							<div class="row" style="{{ $data['six_enable'] == 1 ? '' : 'display:none' }}" id="sec_six">
								<div class="col-md-12">
									<label class="text-dark" for="six_heading">{{ __('Missions section Heading') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('six_heading', 'ar', false) }}" name="six_heading" type="text" class="form-control"
										placeholder="Enter Heading" />
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_txtone">{{ __('Missions section Text One') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('six_txtone', 'ar', false) }}" name="six_txtone" type="text" class="form-control"
										placeholder="Enter Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_txttwo">{{ __('Missions section Text Two') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('six_txttwo', 'ar', false) }}" name="six_txttwo" type="text" class="form-control"
										placeholder="Enter Text" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_txtthree">{{ __('Missions section Text Three ') }}: <span
											class="text-danger">*</span></label>
									<input value="{{ $data->getTranslation('six_txtthree', 'ar', false) }}" name="six_txtthree" type="text" class="form-control"
										placeholder="Enter Text" />
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_deatilone">{{ __('Missions section Detail one') }} : <span
											class="text-danger">*</span></label>
									<textarea name="six_deatilone" rows="5" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('six_deatilone', 'ar', false) }}</textarea>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_deatiltwo">{{ __('Missions section Detail two') }} : <span
											class="text-danger">*</span></label>
									<textarea name="six_deatiltwo" rows="5" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('six_deatiltwo', 'ar', false) }}</textarea>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="six_deatilthree">{{ __('Missions section Detail three') }} : <span
											class="text-danger">*</span></label>
									<textarea name="six_deatilthree" rows="5" class="form-control" placeholder="Enter Your Text">{{ $data->getTranslation('six_deatilthree', 'ar', false) }}</textarea>
									<br>
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="link_one">{{ __('Missions section Link one') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['link_one'] }}" name="link_one" type="text" class="form-control"
										placeholder="Enter Link" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="link_two">{{ __('ection Six Link two') }}S : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['link_two'] }}" name="link_two" type="text" class="form-control"
										placeholder="Enter Link" />
								</div>

								<div class="col-md-4">
									<label class="text-dark" for="link_three">{{ __('Missions section Link three') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['link_three'] }}" name="link_three" type="text" class="form-control"
										placeholder="Enter Link" />
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="four_btntext">{{ __('facebook link') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['four_btntext'] }}" autofocus name="four_btntext" type="text"
										class="form-control" placeholder="Enter link" />
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="five_btntext">{{ __('Instagram link') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['five_btntext'] }}" autofocus name="five_btntext" type="text"
										class="form-control" placeholder="Enter link" />
									<br>
								</div>

								<div class="col-md-6">
									<label class="text-dark" for="linkedin">{{ __('Linkedin link') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['linkedin'] }}" autofocus name="linkedin" type="text" class="form-control"
										placeholder="Enter link" />
								</div>
								<div class="col-md-6">
									<label class="text-dark" for="twitter">{{ __('Twitter link') }} : <span
											class="text-danger">*</span></label>
									<input value="{{ $data['twitter'] }}" autofocus name="twitter" type="text" class="form-control"
										placeholder="Enter link" />
									<br>
								</div>

							</div>
							<br>
						</div>
					</div>

					<div class="form-group">
						<button type="reset" class="btn btn-danger-rgba mr-1"><i class="fa fa-ban"></i>
							{{ __('Reset') }}</button>
						<button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
							{{ __('Update') }}</button>
					</div>
					<!-- section 6 end -->

				</form>
				</form>
				<!-- form end -->
			</div>
			<!-- card body end -->
		</div><!-- col end -->
	</div>
</div>

<!-- main content section ended -->
<!-- This section will contain javacsript start -->
@section('script')
	<script>
		(function($) {
			"use strict";

			$(function() {

				$('#customSwitch1').change(function() {
					if ($('#customSwitch1').is(':checked')) {
						$('#sec_one').show('fast');
					} else {
						$('#sec_one').hide('fast');
					}

				});

				$('#customSwitch2').change(function() {
					if ($('#customSwitch2').is(':checked')) {
						$('#sec_two').show('fast');
					} else {
						$('#sec_two').hide('fast');
					}

				});

				$('#customSwitch3').change(function() {
					if ($('#customSwitch3').is(':checked')) {
						$('#sec_three').show('fast');
					} else {
						$('#sec_three').hide('fast');
					}

				});

				$('#customSwitch4').change(function() {
					if ($('#customSwitch4').is(':checked')) {
						$('#sec_four').show('fast');
					} else {
						$('#sec_four').hide('fast');
					}

				});

				$('#customSwitch5').change(function() {
					if ($('#customSwitch5').is(':checked')) {
						$('#sec_five').show('fast');
					} else {
						$('#sec_five').hide('fast');
					}

				});

				$('#customSwitch6').change(function() {
					if ($('#customSwitch6').is(':checked')) {
						$('#sec_six').show('fast');
					} else {
						$('#sec_six').hide('fast');
					}

				});

			});
		})(jQuery);
	</script>
	<style>
		.image_size {
			height: 80px;
			width: 200px;
		}
	</style>
@endsection
<!-- This section will contain javacsript end -->
